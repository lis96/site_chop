<?php
	require_once(__DIR__ . '/db_authorization.php');
	require_once(__DIR__ . '/../php/writeObj.php');

	$pre = (isset($_GET['pre']) ? $_GET['pre'] : 0);
	$lim = (isset($_GET['lim']) ? $_GET['post'] : 30);

	$res = mysqlQuery('SELECT * FROM products_categories LIMIT ' . $lim . ' OFFSET ' . $pre);

	$first = true;
	echo '[';
	while ($group = mysqlFetchArray($res)) {
		if (!$first) {
			echo ',';
		} else {
			$first = false;
		}
		echoObj($group, ['category_key', 'category_name', 'subset', 'hidden']);
	}
	echo ']';