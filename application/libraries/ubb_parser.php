<?php

/**
 * De almachtige {@link http://en.wikipedia.org/wiki/BBCode BBCode} parser
 *
 * Het is een vrij complex proces, dat parsen. Het is hier dan ook opgedeeld
 * in verschillende stappen:
 * <ul>
 *     <li>Lexing</li>
 *     <li>Parsing</li>
 *     <li>Rendering</li>
 * </ul>
 * Met andere woorden, het is opgedeeld in drie logische stappen die ieder een
 * proces behandelen.
 *
 * Het lexen gebeurt op basis van de {@link Lexer}-interface, deze geef je mee
 * aan de {@link Parser}. De Lexer deelt de input op in {@link Token}s, waar
 * de parser vervolgens doorheen gaat en een geneste lijst van {@link Node}s maakt.
 * Deze Nodes kunnen hierna door {@link Node::__toString()} direct ge-output worden.
 *
 * De snelheid valt reuze mee, wat het trager is dan simpele regex-parser, maakt het
 * geheel goed door vele malen flexibeler en betrouwbaarder te zijn. Het is niet mogelijk
 * om ongeldige uitvoer te krijgen, dat is de pracht ervan. ;)
 *
 * Het gebruik is heel simpel, we maken hier de parser aan met een lexer en geven het een b-tag:
 * <code><?php
 *$parser = new Parser;
 *$parser->setLexer(new SplitLexer)
 *       ->addRule(new TagRule('b', 'inline', array('block', 'inline', 'listitem'), new TagTemplate('<strong>{$_content/nl2br}</strong>')));
 *echo $parser->parse($input); // $input komt _ergens_ vandaan</code>
 *
 * Klaar alweer, we hebben direct output. :-)
 *
 * Geluk met de parser!
 *
 * @author Richard van Velzen
 * @package Bbcode
 * @copyright 2009 Richard van Velzen
 * @version 0.1
 */
/**
 * Een generieke lexerinterface
 *
 * @author Richard van Velzen
 * @package Bbcode
 * @subpackage Lexer
 */
interface Lexer {
    /**
     * Verwerk de gegeven input intern naar een array
     *
     * @param string $input
     * @param array $availableTags de toegestane tags binnen de input
     * @return boolean
     */
    public function lex($input, array $availableTags);
    /**
     * Haal het volgende token op en verhoog de pointer
     *
     * @return Token
     */
    public function getToken();
    /**
     * Bekijk het volgende token, maar zonder de pointer op te hogen
     *
     * <b>Let op:</b> als je deze meerdere keren achter elkaar gebruikt, verspringt
     * de pointer wel steeds!
     *
     * @return Token
     */
    public function peekToken();
}
/**
 * Een lexer gebaseerd op preg_split()
 *
 * @author Richard van Velzen
 * @package Bbcode
 * @subpackage Lexer
 */
class SplitLexer implements Lexer {
    /**
     * Lexemes
     *
     * @var array
     */
    private $_parts = array();
    /**
     * Pointer voor de $_parts array
     *
     * @var integer
     */
    private $_pointer = 0;
    /**
     * Laatste token (bij {@link SplitLexer::peekToken()})
     *
     * @var Token
     */
    private $_lastToken = null;
    /**
     * Lex de gegeven input
     *
     * @param string $input
     * @param array $availableTokens Toegestane tagnames
     */
    public function lex($input, array $availableTokens) {
        $input = str_replace(array("\r\n", "\n\r", "\r"), "\n", $input);
        $tokenList = implode('|', array_map('preg_quote', $availableTokens));
        $this->_parts = preg_split(
              '{(\[/?(?:' . $tokenList . ')'
            . '(?:\h*=\h*(?:"[^"]*"|\'[^\']*\'|[^][\s]+))?'
            . '(?:\h+[\w]+(?:\h*=\h*(?:"[^"\n]*"|\'[^\'\n]*\'|[^][\s]+))?)*\]'
            . '|(?<=]|^)\s+'
            . '|\s+(?=\[|$))}',
            $input,
            null,
            PREG_SPLIT_DELIM_CAPTURE
        );
        $this->_pointer = -1;
        $this->_partsCount = count($this->_parts);
    }
    /**
     * Haal het volgende token op
     *
     * @return Token
     */
    public function getToken() {
        if($this->_lastToken !== null) {
            $return = $this->_lastToken;
            $this->_lastToken = null;
            return $return;
        }
        while(++$this->_pointer < $this->_partsCount) {
            $part = $this->_parts[$this->_pointer];
            if($part == '') {
                continue;
            }
            if($this->_pointer & 1) {
                if(ctype_space($part)) {
                    return new WhitespaceToken($part);
                } else {
                    return $this->_analyzeTag($part);
                }
            } else {
                return new TextToken($part);
            }
        }
        return false;
    }
    /**
     * Bekijk het volgende token
     *
     * @return Token
     */
    public function peekToken() {
        $this->_lastToken = null;
        return $this->_lastToken = $this->getToken();
    }
    /**
     * Analyseer een tag en geef het correcte lexeme terug
     *
     * @param string $tag
     * @return TagToken
     */
    private function _analyzeTag($tag) {
        $originalTag = $tag;
        $tag = substr($tag, 1, -1);
        if($tag[0] == '/') {
            return new EndTagToken($originalTag, strtok(substr($tag, 1), " \t=]"));
        }
        $arguments = $matches = array();
        if(preg_match_all('{([^\s=]+)\h*=\h*(?|"([^"]*)"|\'([^\']*)\'|([^][=\s]+))}', $tag, $matches, PREG_SET_ORDER)) {
            foreach($matches as $match) {
                $arguments[$match[1]] = $match[2];
            }
        }
        return new BeginTagToken($originalTag, strtok($tag, " \t=]"), $arguments);
    }
}
/**
 * Implementatie voor een lijst van {@link Node}s.
 *
 * @author Richard van Velzen
 * @package Bbcode
 * @subpackage Node
 */
class NodeList extends ArrayObject {}
/**
 * Abstracte implementatie voor een node.
 *
 * @author Richard van Velzen
 * @package Bbcode
 * @subpackage Node
 */
abstract class Node {
    /**
     * Parent van deze node
     *
     * @var PointNode|void
     */
    private $_parent = null;
    /**
     * Constructor, doet hier niks
     *
     */
    public function __construct() {}
    /**
     * Stel de parentnode in voor deze node
     *
     * @param PointNode $parent
     * @return Node
     */
    public function setParent(PointNode $parent) {
        $this->_parent = $parent;
        return $this;
    }
    /**
     * Haal de parent van deze node op
     *
     * @return PointNode
     */
    public function getParent() {
        return $this->_parent;
    }
    /**
     * Haal een stringrepresentatie op van deze node
     *
     * @return string
     */
    abstract public function __toString();
}
/**
 * Abstracte implementatie voor een verbindingsnode (root of een tag)
 *
 * @author Richard van Velzen
 * @package Bbcode
 * @subpackage Node
 */
abstract class PointNode extends Node {
    /**
     * Bevat de childnodes van deze pointnode
     *
     * @var NodeList
     */
    protected $_childNodes = null;
    /**
     * Bevat de regel voor deze node
     *
     * @var TagRule
     */
    protected $_rule = null;
    /**
     * Stel de rule van de node in
     *
     * @param TagRule $rule
     */
    public function __construct(TagRule $rule) {
        $this->_childNodes = new NodeList();
        $this->_rule = $rule;
        parent::__construct();
    }
    /**
     * Voeg een nieuwe childnode toe
     *
     * @param Node $node
     * @return PointNode
     */
    public function addChildNode(Node $node) {
        $this->_childNodes->append($node);
        return $this;
    }
    /**
     * Verwijder de laatste childnode als dit whitespace is
     *
     * <b>Let op:</b> dit is meer een hacky hacky methode, maar anders wordt het
     * erg lastig goed te doen. :-(
     *
     * @return void
     */
    public function removeFinalWhitespace() {
        $offset = $this->_childNodes->count() - 1;
        if($this->_childNodes->offsetExists($offset) && $this->_childNodes->offsetGet($offset) instanceof WhitespaceNode) {
            $this->_childNodes->offsetUnset($offset);
        }
    }
    /**
     * Haal de inhoud op
     *
     * @return string
     */
    public function getContents() {
        $output = '';
        foreach($this->_childNodes as $node) {
            $output .= $node->__toString();
        }
        return $output;
    }
    /**
     * Haal de TagRule op die bij deze node hoort
     *
     * @return TagRule
     */
    public function getRule() {
        return $this->_rule;
    }
    /**
     * Verkrijg de tekstuele output van deze node
     *
     * @return string
     */
    public function __toString() {
        return $this->getContents();
    }
}
/**
 * Tekstnode
 *
 * Representatie van een gedeelte met alleen tekst
 *
 * @author Richard van Velzen
 * @package Bbcode
 * @subpackage Node
 */
class TextNode extends Node {
    /**
     * De inhoud van deze node
     *
     * @var string
     */
    protected $_contents = '';
    /**
     * Stel de contents van deze node in
     *
     * @param string $contents
     */
    public function __construct($contents) {
        $this->_contents = $contents;
        parent::__construct();
    }
    /**
     * Haal de content van deze node op
     *
     * @return string
     */
    public function getContents() {
        $output = htmlspecialchars($this->_contents);
        // automatisch linken naar urls
        $output = preg_replace_callback(
            '{(?<=\b)((?:https?|ftp)://|www\.)[\w.]+[;#&/~=\w+()?.,:%-]*[;#&/~=\w+(-]}i',
            array($this, '_linkReplacement'),
            $output
        );
        return $output;
    }
    /**
     * Haal de stringrepresenatie van deze node op
     *
     * @return string
     */
    public function __toString() {
        return $this->getContents();
    }
    /**
     * Link replacement voor {@link TextNode::getContents()}
     *
     * @param array $match Input vanuit preg_match_callback
     * @return string
     */
    protected function _linkReplacement(array $match) {
        $link = $match[0];
        if($match[1] == 'www.') {
            $link = 'http://' . $match[0];
        }
        return '<a href="' . $link . '">' . $match[0] . '</a>';
    }
}
/**
 * Letterlijke tekstnode, voor binnen bijvoorbeeld codeblokken
 *
 * @package Bbcode
 * @subpackage Node
 */
class LiteralTextNode extends TextNode {
    /**
     * Haal de contents van deze node op
     *
     * @return string
     */
    public function getContents() {
        return $this->_contents;
    }
}
/**
 * Node die whitespace representeert
 *
 * @package Bbcode
 * @subpackage Node
 */
class WhitespaceNode extends TextNode {
    /**
     * Haal de uitgevoerde whitespace op
     *
     * @return string
     */
    public function getContents() {
        return str_replace("\n", '<br />', parent::getContents());
    }
}
/**
 * Node die een tag representeert
 *
 * @package Bbcode
 * @subpackage Node
 */
class TagNode extends PointNode {
    /**
     * De naam de van de node
     *
     * @var string
     */
    private $_tagName = '';
    /**
     * De argumenten die bij deze node horen
     *
     * @var array
     */
    private $_arguments = array();
    /**
     * Stel de rule, tagnaam en argumenten in
     *
     * @param TagRule $rule
     * @param string $tagName
     * @param array $arguments
     */
    public function __construct(TagRule $rule, $tagName, array $arguments) {
        $this->_tagName = $tagName;
        $this->_arguments = $arguments;
        parent::__construct($rule);
    }
    /**
     * Haal de tagnaam op
     *
     * @return string
     */
    public function getTagName() {
        return $this->_tagName;
    }
    /**
     * Haal de doorgevoerde inhoud van de node op
     *
     * @return string
     */
    public function getContents() {
        $output = parent::getContents();
        return $this->_rule->processOutput($this->_tagName, $output, $this->_arguments);
    }
}
/**
 * Simpele node die de root representeert
 *
 * @package Bbcode
 * @subpackage Node
 */
class RootNode extends PointNode {
    /**
     * Doe niks bijzonders, alleen een nieuwe rootnode instellen
     *
     */
    public function __construct() {
        parent::__construct(new RootRule());
    }
}
/**
 * Simpele parser gebaseerd op bijbehorende klassen
 *
 * <code><?php
 *$parser = new Parser;
 *$parser->setLexer(new SplitLexer)
 *       ->addRule(
 *             // doe je ding hier
 *         );</code>
 * @package Bbcode
 * @subpackage Parser
 *
 * @todo Optimalisatie! Vooral het cachen van tagnamen zou al schelen :-)
 */
class Parser {
    /**
     * De gebruikte {@link Lexer}
     *
     * @var Lexer
     */
    private $_lexer = null;
    /**
     * De node waarin we nu bewerkingen doen
     *
     * @var PointNode
     */
    private $_currentNode = null;
    /**
     * De rootnode van deze tekst
     *
     * @var RootNode
     */
    private $_rootNode = null;
    /**
     * Een lijst van rules die we tot onze beschikking hebben
     *
     * @var array Array van {@link TagRule}s.
     */
    private $_tagRules = array();
    /**
     * Lijst van geopende tags
     *
     * Let op: dit is meer optimalisatie, uiteindelijk zou alles prima
     * via {@link Node::getParent()} te doen zijn, maar dit scheelt
     * ontzettend veel tijd
     *
     * @var array Array van namen
     */
    private $_openedTags = array();
    /**
     * Voeg een rule toe, waar mee kan worden geparsed
     *
     * @param TagRule $rule
     * @return Parser
     */
    public function addRule(TagRule $rule) {
        $this->_tagRules[$rule->getName()] = $rule;
        return $this;
    }
    /**
     * Stel de lexer in die we gaan gebruiken
     *
     * @param Lexer $lexer
     * @return Parser
     */
    public function setLexer(Lexer $lexer) {
        $this->_lexer = $lexer;
        return $this;
    }
    /**
     * De kern van de parser.
     *
     * Men neme een stuk tekst en insert het, en krijgt een rootNode met
     * compleet geparsede tree terug die door middel van de __toString methode
     * direct geoutput kan worden.
     *
     * @param string $text
     * @return RootNode
     */
    public function parse($text) {
        $this->_rootNode = new RootNode();
        $this->_currentNode = $this->_rootNode;
        $this->_lexer->lex($text, array_keys($this->_tagRules));
        while(false !== $token = $this->_lexer->getToken()) {
            if($token instanceof BeginTagToken) {
                $tagRule = $this->_tagRules[$token->getTagName()];
                $oldNode = $this->_currentNode;
                while(! $tagRule->isPermissableIn($this->_currentNode)) {
                    if($this->_currentNode instanceof RootNode) {
                        // we zijn bovenaan, tag is echt NIET te matchen
                        $this->_currentNode->addChildNode(new TextNode($token->getContent()));
                        $this->_rootNode = $oldNode;
                        continue 2;
                    }
                    $this->_currentNode = $this->_currentNode->getParent();
                }
                if($tagRule->getTrimWhitespace() & TagRule::TRIM_BEFORE) {
                    $oldNode->removeFinalWhitespace();
                }
                $newNode = new TagNode($tagRule, $token->getTagName(), $token->getArguments());
                $newNode->setParent($this->_currentNode);
                $this->_currentNode->addChildNode($newNode);
                if($tagRule->getParseType() == TagRule::PARSE) {
                    $this->_currentNode = $newNode;
                    $this->_setTagOpen($token->getTagName());
                } else {
                    $nodeContent = '';
                    $finished = false;
                    while(false !== $newToken = $this->_lexer->getToken()) {
                        if($newToken instanceof EndTagToken && $newToken->getTagName() == $token->getTagName()) {
                            $finished = true;
                            $newNode->addChildNode(new LiteralTextNode($nodeContent));
                            if($this->_tagRules[$token->getTagName()]->getTrimWhitespace() & TagRule::TRIM_AFTER) {
                                // probeer whitespace weg te halen bij volgende input
                                if(false !== ($peek = $this->_lexer->peekToken())) {
                                    if($peek instanceof WhitespaceToken) {
                                        $this->_lexer->peekToken();
                                    }
                                }
                            }
                            break;
                        } else {
                            $nodeContent .= $newToken->getContent();
                        }
                    }
                    if(!$finished) {
                        $newNode->addChildNode(new LiteralTextNode($nodeContent));
                    }
                    unset($finished);
                }
            } elseif($token instanceof EndTagToken && $this->_currentNode instanceof TagNode) {
                if(isset($this->_openedTags[$token->getTagName()])) {
                    if($this->_tagRules[$token->getTagName()]->getTrimWhitespace() & TagRule::TRIM_AFTER) {
                        // probeer whitespace weg te halen bij volgende input
                        if(false !== ($peek = $this->_lexer->peekToken())) {
                            if($peek instanceof WhitespaceToken) {
                                $this->_lexer->peekToken();
                            }
                        }
                    }
                    // check of de eindtag dezelfde naam heeft als de huidige opentag
                    while($this->_currentNode instanceof TagNode && $token->getTagName() != $this->_currentNode->getTagName()) {
                        $this->_setTagOpen($token->getTagName(), false);
                        $this->_currentNode = $this->_currentNode->getParent();
                    }
                    if(! $this->_currentNode instanceof RootNode) {
                        $this->_currentNode = $this->_currentNode->getParent();
                    }
                } else {
                    $this->_currentNode->addChildNode(new TextNode($token->getContent()));
                }
            } elseif($token instanceof WhitespaceToken) {
                // simpele whitespace
                $this->_currentNode->addChildNode(new WhitespaceNode($token->getContent()));
            } else {
                // alleen nog tekst
                $this->_currentNode->addChildNode(new TextNode($token->getContent()));
            }
        }
        return $this->_rootNode;
    }
    /**
     * Stel in of een tag geopend of gesloten is
     *
     * @param string $tagName
     * @param boolean $add true bij nieuw, false bij gesloten
     */
    protected function _setTagOpen($tagName, $add = true) {
        if($add) {
            if(! isset($this->_openedTags[$tagName])) {
                $this->_openedTags[$tagName] = 1;
            } else {
                ++$this->_openedTags[$tagName];
            }
        } else {
            --$this->_openedTags[$tagName];
            if(! $this->_openedTags[$tagName]) {
                unset($this->_openedTags[$tagName]);
            }
        }
    }
}
/**
 * Een generieke regel qua wat er moet gebeuren
 *
 * @author Richard van Velzen
 * @package Bbcode
 * @subpackage TagRule
 */
class TagRule {
    /**
     * Geen whitespace trimmen
     */
    const TRIM_NONE = 0;
    /**
     * Whitespace voor de tag trimmen
     */
    const TRIM_BEFORE = 1;
    /**
     * Whitespace na de tag trimmen
     */
    const TRIM_AFTER = 2;
    /**
     * Aan beide kanten trimmen
     */
    const TRIM_BOTH = 3;
    /**
     * Binnen deze tag verder parsen
     */
    const PARSE = 3;
    /**
     * Niet parsen binnen deze tag
     */
    const LITERAL = 2;
    /**
     * Naam van de tag
     *
     * @var string
     */
    private $_name = '';
    /**
     * In welke state zitten we binnen deze tag
     *
     * @var string
     */
    private $_state = '';
    /**
     * De states bninen welke deze tag is toegestaan
     *
     * @var array
     */
    private $_permissableIn = array();
    /**
     * Bijbehorende processor
     *
     * @var TagTemplate|callback
     */
    private $_processor = null;
    /**
     * Manier waarop deze tag wordt geparsed
     *
     * @var integer Een van {@link TagRule::PARSE} en {@link TagRule::LITERAL}
     */
    private $_parseType = self::PARSE;
    /**
     * Bepaalt of en zo ja, aan welke kanten van de tag whitespace wordt getrimmed
     *
     * @var integer
     */
    private $_trimWhitespace = self::TRIM_NONE;
    /**
     * Stel de waarden voor deze rule in
     *
     * @param string $name De naam van de rule
     * @param string $state De state waarin we in deze tag zitten
     * @param array $permissableIn States waarin deze rule zich mag bevinden
     * @param TagTemplate|callback $processor Ofwel een callback ofwel een TagTemplate
     * @param integer $trimWhitespace Op welke wijze moeten we whitespace rondom trimmen?
     * @param integer $parseType Is dit een letterlijke of parsende rule?
     */
    public function __construct($name, $state, array $permissableIn, $processor, $trimWhitespace = self::TRIM_NONE, $parseType = self::PARSE) {
        if(!$processor instanceof TagTemplate && !is_callable($processor)) {
            throw new InvalidArgumentException('Geen geldige processor voor tag "' . $name . '"');
        }
        if($trimWhitespace < 0 || $trimWhitespace > 3) {
            throw new InvalidArgumentException('TrimWhitespace is niet geldig voor "' . $name . '"');
        }
        if($parseType != self::PARSE && $parseType != self::LITERAL) {
            throw new InvalidArgumentException('Parsetype is niet geldig voor "' . $name . '"');
        }
        $this->_name = $name;
        $this->_state = $state;
        $this->_permissableIn = $permissableIn;
        $this->_processor = $processor;
        $this->_trimWhitespace = $trimWhitespace;
        $this->_parseType = $parseType;
    }
    /**
     * Process de output van deze rule
     *
     * @param string $tagName
     * @param string $content
     * @param array $arguments
     * @return string
     */
    public function processOutput($tagName, $content, array $arguments) {
        if($this->_processor instanceof TagTemplate) {
            return $this->_processor->render($tagName, $content, $arguments);
        }
        return call_user_func($this->_processor, $tagName, $content, $arguments);
    }
    /**
     * Haal de tagnaam op
     *
     * @return string
     */
    public function getName() {
        return $this->_name;
    }
    /**
     * Haal de state op
     *
     * @return string
     */
    public function getState() {
        return $this->_state;
    }
    /**
     * Check of deze state is toegestaan binnen een node
     *
     * @param PointNode $node
     * @return boolean
     */
    public function isPermissableIn(PointNode $node) {
        return in_array($node->getRule()->getState(), $this->_permissableIn);
    }
    /**
     * Kijk of er whitespace moet worden weggehaald voor of na
     *
     * @return integer
     */
    public function getTrimWhitespace() {
        return $this->_trimWhitespace;
    }
    /**
     * Hoe moet deze tag worden geparsed
     *
     * @return integer Een van {@link TagRule::PARSE} en
     *                 {@link TagRule::LITERAL}
     */
    public function getParseType() {
        return $this->_parseType;
    }
}
/**
 * Specifieke rule voor de root-node
 *
 * @author Richard van Velzen
 * @package Bbcode
 * @subpackage TagRule
 */
class RootRule extends TagRule {
    /**
     * Deze roept alleen de nodige dingen aan, verder hoef je zelf niks in te stellen
     *
     */
    public function __construct() {
        parent::__construct('__root', 'block', array(), new TagTemplate('{$_content}'));
    }
}
/**
 * Een template die kan worden gebruikt bij een tag
 *
 * @author Richard van Velzen
 * @package Bbcode
 * @subpackage TagRule
 */
class TagTemplate {
    /**
     * De originele template
     *
     * @var string
     */
    private $_template = '';
    /**
     * Argumenten bij de aanroep
     *
     * @var array
     */
    private $_arguments = array();
    /**
     * Voer de template in
     *
     * @param string $template
     */
    public function __construct($template) {
        $this->_template = $template;
    }
    /**
     * Render de template
     *
     * Voer de templaterendering uit met behulp van de argumenten
     *
     * @param string $tagName
     * @param string $content
     * @param array $arguments
     * @return string
     */
    public function render($tagName, $content, array $arguments) {
        $this->_arguments = $arguments;
        $this->_arguments['_tag'] = $tagName;
        $this->_arguments['_content'] = $content;
        $return = preg_replace_callback(
            '~{\$([A-Za-z_][A-Za-z\d_]*)(?:/([a-z\d/]+))?}~',
            array($this, '_variableReplace'),
            $this->_template
        );
        // geheugen vrijmaken, blij blij blij :-)
        $this->_arguments = null;
        return $return;
    }
    /**
     * Callback voor het vervangen van template variabelen
     *
     * @param array $match
     * @return string
     */
    private function _variableReplace(array $match) {
        if(! isset($this->_arguments[$match[1]])) {
            return '';
        }
        $return = $this->_arguments[$match[1]];
        if(isset($match[2])) {
            $return = $this->_applyModifiers($return, explode('/', $match[2]));
        }
        return $return;
    }
    /**
     * Voer verschillende modifiers uit over de tekst
     *
     * @param string $text
     * @param array $modifiers
     * @return string
     */
    private function _applyModifiers($text, array $modifiers) {
        $usedModifiers = array();
        foreach($modifiers as $modifier) {
            // even checken, niet dubbel uitvoeren
            if(isset($usedModifiers[$modifier])) {
                continue;
            }
            $usedModifiers[$modifier] = 0;
            switch(strtolower(trim($modifier))) {
                case 'trim':
                    $text = preg_replace('{^(?:<br />|\s+)+|(?:<br />|\s+)+$}', '', $text);
                    break;
                case 'nl2br':
                    $text = str_replace("\n", '<br />', $text);
                    break;
                case 'html':
                    $text = htmlspecialchars($text);
                    break;
                default:
                    // oei, foutje zeker?
                    throw new RuntimeException('Onbekende modifier "' . $modifier . '"');
            }
        }
        return $text;
    }
}
/**
 * Abstracte implementatie van een lexer token
 *
 * @author Richard van Velzen
 * @package Bbcode
 * @subpackage Token
 */
abstract class Token {
    /**
     * De letterlijke tekstuele inhoud van dit token
     *
     * @var string
     */
    private $_content = '';
    /**
     * Stel de content in
     *
     * @param string $content
     */
    public function __construct($content) {
        $this->_content = $content;
    }
    /**
     * Haal de letterlijke inhoud van dit token op
     *
     * @return string
     */
    public function getContent() {
        return $this->_content;
    }
    /**
     * String-implementatie
     *
     * @return string
     */
    public function __toString() {
        return $this->getContent();
    }
}
/**
 * Implementatie van een tag
 *
 * @author Richard van Velzen
 * @package Bbcode
 * @subpackage Token
 */
abstract class TagToken extends Token {
    /**
     * De naam van de tag
     */
    private $_tagName = '';
    /**
     * Stel de content en tagnaam in
     *
     * @param string $content
     * @param string $tagName
     */
    public function __construct($content, $tagName) {
        $this->_tagName = $tagName;
        parent::__construct($content);
    }
    /**
     * Haal de tagnaam van dit token op
     *
     * @return string
     */
    public function getTagName() {
        return $this->_tagName;
    }
}
/**
 * Implementatie van een begintag
 *
 * @author Richard van Velzen
 * @package Bbcode
 * @subpackage Token
 */
class BeginTagToken extends TagToken {
    /**
     * Gegeven argumenten
     *
     * @var array
     */
    private $_arguments = array();
    /**
     * Stel content, tagnaam en argumenten in
     *
     * @param string $content
     * @param string $tagName
     * @param array $arguments
     */
    public function __construct($content, $tagName, $arguments) {
        $this->_arguments = $arguments;
        parent::__construct($content, $tagName);
    }
    /**
     * Haal de argumenten op
     *
     * @return array
     */
    public function getArguments() {
        return $this->_arguments;
    }
}
/**
 * Een eindtag
 *
 * @author Richard van Velzen
 * @package Bbcode
 * @subpackage Token
 */
class EndTagToken extends TagToken {}
/**
 * Een plaintext token
 *
 * @author Richard van Velzen
 * @package Bbcode
 * @subpackage Token
 */
class TextToken extends Token {}
/**
 * Een whitespacetoken
 *
 * @author Richard van Velzen
 * @package Bbcode
 * @subpackage Token
 */
class WhitespaceToken extends Token {}
