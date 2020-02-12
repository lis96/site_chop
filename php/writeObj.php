<?php
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