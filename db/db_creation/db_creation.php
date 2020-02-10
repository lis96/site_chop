<?php

	$tables = json_decode(file_get_contents(__DIR__ . '/db_tables.json'), true);
	$database = json_decode(file_get_contents(__DIR__ . '/../../config.json'), true)['database'];

	if (substr(phpversion(),0,1) == 7) {

		$connect = new mysqli($database['host'] . ':' . $database['port'], $database['login'], $database['password']);

		function mysqlQuery($query) {
			global $connect;

			return $connect->query($query);
		}

		function mysqlFetchArray($result) {
			return $result->fetch_array();
		}
	
	} else {

		mysql_connect($database['host'] . ':' . $database['port'], $database['login'], $database['password']);

		function mysqlQuery($query) {
			return mysql_query($query);
		}

		function mysqlFetchArray($result) {
			return mysql_fetch_array($result);
		}

	} 

	if (!mysqlQuery('USE ' . $database['dbName'])) {

		mysqlQuery('CREATE DATABASE ' . $database['dbName']);
		mysqlQuery('USE ' . $database['dbName']);

	}

	function reCreateTable($name, $desc) {
		mysqlQuery('DROP TABLE ' . $name);
		mysqlQuery('CREATE TABLE ' . $name . '(' . $desc . ')');
	}

	foreach ($tables as $table) {
		$description = $table['fields'][0];

		for ($i = 1;$i < count($table['fields']);$i++) {
			$description .= ',' . $table['fields'][$i];
		}

		reCreateTable($table['name'], $description);
	}




