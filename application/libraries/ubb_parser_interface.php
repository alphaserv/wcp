<?php
require_once(dirname(__FILE__).'/ubb_parser.php'); #include parser
class a
{
	function __destruct()
	{
		$content = <<<CONT
			[h][b]HEAD[/b][/h]
			[p]
				this is [u]underlinded[/u] text
				remember to use [i]italics
				
				[url=http://www.google.nl/ title="</a><script>allert('HACK');</script>] : [/url]
				
				
				
			[/p]
			correctly[/i]
CONT
	;	
		$parser = new Parser;
		$parser->setLexer(new SplitLexer)
				->addRule(new TagRule('b', 'inline', array('block', 'inline', 'listitem'), new TagTemplate('<strong class="ubb b">{$_content/trim}</strong>')))
				->addRule(new TagRule('i', 'inline', array('block', 'inline', 'listitem'), new TagTemplate('<span class="ubb i">{$_content/trim}</span>')))
				->addRule(new TagRule('u', 'inline', array('block', 'inline', 'listitem'), new TagTemplate('<span class="ubb u">{$_content/trim}</span>')))

				->addRule(new TagRule('noparse', 'inline', array('block', 'inline', 'listitem'), new TagTemplate('{$_content}'), null, TagRule::LITERAL))
				
				->addRule(new TagRule('p', 'block', array('block'), new TagTemplate('<p>{$_content/trim}</p>')))
				->addRule(new TagRule('h', 'block', array('block'), new TagTemplate('<h4 class="ubb h">{$_content/trim}</h4>')))
				
				->addRule(new TagRule('url', 'inline', array('block', 'inline', 'listitem'), new TagTemplate('<a class="ubb a" href="{$url}" title="{$title}">{$_content/trim}</a>')));
		
		echo str_Replace('<br />', '', $parser->parse($content)); #why the fck is this needed =(
	}
}
$a = new a;
