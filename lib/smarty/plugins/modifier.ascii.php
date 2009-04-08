<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     modifier.protect_html.php
 * Type:     modifier
 * Name:     ascii
 * Purpose:  translate string to ascii charset and remove white spaces
 * -------------------------------------------------------------
 */

function smarty_modifier_ascii ($string, $removeWhiteSpace = false)
{
   $output = iconv("utf-8", "us-ascii//TRANSLIT", $string);
   if($removeWhiteSpace){
      $output = str_ireplace(' ', '', $output);
   }
   return strip_tags ($output);
}
?> 
