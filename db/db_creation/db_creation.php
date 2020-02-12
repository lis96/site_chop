<?php

	$tables = json_decode(file_get_contents(__DIR__ . '/db_tables.json'), true);
	$tablesVals = json_decode(file_get_contents(__DIR__ . '/db_default_values.json'), true);
	$database = json_decode(file_get_contents(__DIR__ . '/../../config.json'), true)['database'];

	$productsTableName = 'products';
	$categoriesTableName = 'products_categories';
	$optionsTableName = 'options';



	if (substr(phpversion(),0,1) == 7) {

		$connect = new mysqli($database['host'] . ':' . $database['port'], $database['login'], $database['password']);

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

		mysql_connect($database['host'] . ':' . $database['port'], $database['login'], $database['password']);

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

	if (!mysqlQuery('USE ' . $database['dbName'])) {

		mysqlQuery('CREATE DATABASE ' . $database['dbName']);
		mysqlQuery('USE ' . $database['dbName']);

	}

	function reCreateTable($name, $desc) {
		mysqlQuery('DROP TABLE ' . $name);

		mysqlQuery('CREATE TABLE ' . $name . '(' . $desc . ')');
		if (mysqlError() != '') {
			throw new Exception(mysqlError());
		}
	}

	(function($optionsTableName)use($tables, $tablesVals){
		$table = $tables[$optionsTableName];

		//Create the table
		$description = $table[0];
		for ($i = 1;$i < count($table);$i++) {
			$description .= ', ' . $table[$i];
		}
		reCreateTable($optionsTableName, $description);

		//fill the table
		foreach($tablesVals[$optionsTableName] as $vals) {
			mysqlQuery('INSERT INTO ' . $optionsTableName . '(' . $vals['keys'] . ') VALUES(' . $vals['values'] . ')');

			if (mysqlError() != '') {
				throw new Exception(mysqlError());
			}
		}
	})($optionsTableName);

	(function($categoriesTableName)use($tables, $tablesVals){
		$table = $tables[$categoriesTableName];

		//Create the table
		$description = $table[0];
		for ($i = 1;$i < count($table);$i++) {
			$description .= ', ' . $table[$i];
		}
		$description .= ', options INT(' . ceil(count($tablesVals['options']) / 3) * 2 . ') UNSIGNED NOT NULL DEFAULT 0';
		reCreateTable($categoriesTableName, $description);

		//fill the table
		foreach($tablesVals[$categoriesTableName] as $vals) {
			mysqlQuery('INSERT INTO ' . $categoriesTableName . '(' . $vals['keys'] . ') VALUES(' . $vals['values'] . ')');

			if (mysqlError() != '') {
				throw new Exception(mysqlError());
			}
		}
	})($categoriesTableName);

	(function($productsTableName)use($tables, $tablesVals){
		$table = $tables[$productsTableName];
		$description = $table[0];
		for ($i = 1;$i < count($table);$i++) {
			$description .= ', ' . $table[$i];
		}

		foreach ($tablesVals['options'] as $vals) {
			$default = $vals['default'];
			$keys = explode(', ', $vals['keys']);
			$vals = explode(', ', $vals['values']);
			$key = NULL;
			$type = NULL;
			for ($i = 0;$i < count($keys);$i++) {
				if ($keys[$i] == 'option_key') {
					$key = substr($vals[$i], 1, -1);
				}
				if ($keys[$i] == 'type_of_values') {
					$type = substr($vals[$i], 1, -1);
				}
			}
			if ($key == NULL || $type == NULL) {
				throw new Exception("Wrong option detected");
			} else {
				switch ($type) {
					case 'NUMBER':
						$description .= ', ' . $key . ' INT(10) DEFAULT ' . $default;
						break;
					case 'DECIMAL':
						$description .= ', ' . $key . ' DECIMAL(15, 5) DEFAULT ' . $default;
						break;
					case 'STRING':
						$description .= ', ' . $key . ' VARCHAR(20) DEFAULT ' . $default;
						break;
					case 'TEXT':
						$description .= ', ' . $key . ' TEXT DEFAULT ' . $default;
						break;
					default:
						throw new Exception("Wrong option detected");
				}
			}
		}

		reCreateTable($productsTableName, $description);

		foreach ($tablesVals['products'] as $product) {
			$keys = 'product_name,categories';
			$vals = $product['values'];
			foreach ($product['options'] as $key => $option) {
				$keys .= ',' . $key;
				$vals .= ', ' . json_encode($option);
			}
			mysqlQuery('INSERT INTO ' . $productsTableName . '(' . $keys . ') VALUES (' . $vals . ')');
			if (mysqlError() != '') {
				throw new Exception(mysqlError());
			}
		}
	})($productsTableName);
