<?php

$mail_config['mail_activation_subject'] = 'Successfully activated';
$mail_config['protocol'] = 'smtp';

#$mail_config['smtp_port'] = 465;#when using ssl
#$mail_config['smtp_host'] = 'ssl://smtp.googlemail.com';#ssl no tailing slash!

$mail_config['smtp_host'] = 'localhost';
$mail_config['smtp_user'] = '';
$mail_config['smtp_pass'] = '';
$mail_config['mailpath'] = '/usr/sbin/sendmail';
$mail_config['charset'] = 'iso-8859-1';
$mail_config['wordwrap'] = TRUE;

$GLOBALS['mail_config'] = $mail_config;
$config = $mail_config;

