<?php
	require_once(__DIR__ . '/db_authorization.php');
	require_once(__DIR__ . '/functionGetCategoryOptions.php');
	require_once(__DIR__ . '/../php/writeObj.php');

	if (!isset($_GET['category'])) $_GET['category'] = "everything";

	$categoryOptionsKeys;
	$categoryID;
	(function()use(&$categoryOptionsKeys, &$categoryID){
		$tmp = getCategoryOptionKeys($_GET['category']);

		$categoryOptionsKeys = $tmp['keys'];
		$categoryID = $tmp['id'];
	})();

	$keys = '';
	array_map(function($el)use(&$keys){ $keys .= ',' . $el; }, $categoryOptionsKeys);

	$products = mysqlQuery('SELECT id,product_name,categories' . $keys . ' FROM products WHERE NOT categories & ' . $categoryID . ' = 0');

	$first = true;
	echo '[';
	while ($product = mysqlFetchArray($products)) {
		if (!$first) {
			echo ',';
		} else {
			$first = false;
		}
		echoObj($product, array_merge(['id','product_name','categories'], $categoryOptionsKeys));
	}
	echo ']';
