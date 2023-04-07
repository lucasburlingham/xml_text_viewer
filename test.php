<?php

$splitFiles = array();
$filename = 'file';

$range = range('a', 'z');
foreach ($range as $letter) {
	foreach ($range as $secondletter) {
		array_push($splitFiles, $filename . $letter . $secondletter);
		echo $filename . $letter . $secondletter . "\n";
	}
}