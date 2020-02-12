<?php
	require_once(__DIR__ . '/db_authorization.php');

	function getCategoryOptionKeys($categoryName) {
		$category = mysqlFetchArray(mysqlQuery('SELECT id,options FROM products_categories WHERE category_key="' . $categoryName . '"'));

		$optionsKeys = [];
		if ($category != NULL) {
			$options = mysqlQuery('SELECT id,option_key FROM options');
			$binaryInd = 1;
			while ($option = mysqlFetchArray($options)) {
				if ($binaryInd & $category['options']) {
					array_push($optionsKeys, $option['option_key']);
				}
				$binaryInd = $binaryInd << 1;
			}
		}

		return [ 'keys' => $optionsKeys, 'id' => $category['id'] ];
	}