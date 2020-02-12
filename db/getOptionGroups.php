<?php
	require_once(__DIR__ . '/db_authorization.php');

	$pre = (isset($_GET['pre']) ? $_GET['pre'] : 0);
	$lim = (isset($_GET['lim']) ? $_GET['post'] : 30);

	$res = mysqlQuery('SELECT * FROM options LIMIT ' . $lim . ' OFFSET ' . $pre);

	function echoObj($obj, $strs) {
		echo '{';
		$first = true;
		foreach ($strs as $str) {
			if (!$first) {
				echo ',';
			} else {
				$first = false;
			}
			echo '"' . $str . '":"' . $obj[$str] . '"';
		}
		echo '}';
	}

	$first = true;
	echo '[';
	while ($group = mysqlFetchArray($res)) {
		if (!$first) {
			echo ',';
		} else {
			$first = false;
		}
		echoObj($group, ['option_key', 'option_name', 'type_of_values', 'unit', 'hidden', 'ordered']);
	}
	echo ']';