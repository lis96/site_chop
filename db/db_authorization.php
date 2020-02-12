<?php

	$database = json_decode(file_get_contents(__DIR__ . '/../config.json'), true)['database'];

	if (substr(phpversion(),0,1) == 7) {

		$connect = new mysqli($database['host'] . ':' . $database['port'], $database['login'], $database['password'], $database['dbName']);

		function mysqlQuery($query) {
			global $connect;
			return $connect->query($query);
		}

		function mysqlFetchArray($result) {
			return $result->fetch_array();
		}

		function mysqlError() {
			global $connect;
			return $connect->error;
		}

		function mysqlClose() {
			global $connect;
			return $connect->close();
		}

	} else {

		mysql_connect($database['host'] . ':' . $database['port'], $database['login'], $database['password'], $database['dbName']);

		function mysqlQuery($query) {
			return mysql_query($query);
		}

		function mysqlFetchArray($result) {
			return mysql_fetch_array($result);
		}

		function mysqlError() {
			return mysql_error();
		}

		function mysqlClose() {
			return mysql_close();
		}

	}