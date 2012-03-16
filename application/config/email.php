<?php

$config['mail_activation_subject'] = 'Successfully activated';
$config['protocol'] = 'smtp';

#$config['smtp_port'] = 465;#when using ssl
#$config['smtp_host'] = 'ssl://smtp.googlemail.com';#ssl no tailing slash!

$config['smtp_host'] = 'localhost';
$config['smtp_user'] = '';
$config['smtp_pass'] = '';
$config['mailpath'] = '/usr/sbin/sendmail';
$config['charset'] = 'iso-8859-1';
$config['wordwrap'] = TRUE;

