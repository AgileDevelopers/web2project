<?php
if (!defined('W2P_BASE_DIR')) {
	die('You should not access this file directly.');
}

if (empty($s) || strlen(trim($s)) == 0) {
	$a = 'index';
	$AppUI->setMsg('Please enter a search value');
}
?>