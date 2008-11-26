<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty mb upper modifier plugin
 *
 * Type:     modifier<br>
 * Name:     mb_upper<br>
 * Purpose:  convert string to uppercase from locale settings
 * @link http://smarty.php.net/manual/en/language.modifier.upper.php
 *          upper (Smarty online manual)
 * @author   Monte Ohrt <monte at ohrt dot com> Jakub Matas <jakubmatas at gmail dot com>
 * @param string
 * @return string
 * @since 04.23.2008
 */
function smarty_modifier_mb_upper($string)
{
    return mb_strtoupper($string, "utf-8");
}

?>
