<?
//PHP convenience functions: quick.php

function send($variable, $varname, $global = 1) {
	if ($global) {
		echo("<script> window." . $varname . " = " . json_encode($variable) . "</script>");
	} else {
		echo("<script> var " . $varname . " = " . json_encode($variable) . "</script>");
	}
}

?>