<?php

	$database = json_decode(file_get_contents(__DIR__ . '/../config.json'), true)['database'];

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