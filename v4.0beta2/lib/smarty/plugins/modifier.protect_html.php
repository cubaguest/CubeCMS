<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     modifier.protect_html.php
 * Type:     modifier
 * Name:     protect_html
 * Purpose:  Remove HTML tags from string
 * -------------------------------------------------------------
 */

function smarty_modifier_protect_html ($string)
{
   $output = preg_replace ("'<style[^>]*>.*</style>'siU",'', $string);
   $output = preg_replace ("'<script[^>]*>.*</script>'siU",'', $output);
   
   return strip_tags ($output);
 }
?> 
