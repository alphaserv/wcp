<?php

$client_id = '02a3aa14b1f050673afa';

if(isset($_GET['redirect']))
	header('Location: https://github.com/login/oauth/authorize?client_id='.$client_id.'&redirect_uri='.rawurlencode($_SERVER['PHP_SELF']));
