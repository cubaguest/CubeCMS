<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty replace array modifier plugin
 *
 * Type:     modifier<br>
 * Name:     replace with array<br>
 * Purpose:  advanced search/replace
 * @author   Jakub Matas <jakubmatas at gmail dot com>
 * @param string
 * @param string
 * @param string
 * @return string
 * {$articleTitle|replace:'Garden':'Vineyard'}
 * {$articleTitle|replace:'Garden':'Vineyard'}
 */
function smarty_modifier_replacearray($string, $search, $replace)
{
	foreach ($search as $key => $value) {
		$replace2 = str_replace("REPLACE", $value, $replace);
		$search[$key] = $replace2;
	}

	$string = strtr($string, $search);
	return $string;
}

/* vim: set expandtab: */

?>
