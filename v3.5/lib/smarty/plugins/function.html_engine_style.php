<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {html_engine_style} function plugin
 *
 * Type:     function<br>
 * Name:     html_engine_style<br>
 * Date:     Feb 24, 2003<br>
 * Purpose:  format HTML tags for the engine style<br>
 * Input:<br>
 *         - file = file (and path) of image (required)
 *         - rel = rel name (stylesheet)
 *         - type = type of file (text/css)
 *
 * Examples: {html_engine_style file="style.css"}
 * Output:   <link rel="stylesheet" type="text/css" href="./stylesheet/style.css" />
 * @author Jakub Matas <jakubmatas at gmail dot com> -- for VVE (Veprove vypecky engine)
 * @subpackage VVE engine version 3.1
 * @version  1.0
 * @param array
 * @param Smarty
 * @return string
 * @uses smarty_function_escape_special_chars()
 */
function smarty_function_html_engine_style($params, &$smarty)
{
    require_once $smarty->_get_plugin_filepath('shared','escape_special_chars');
    
    $rel = '';
    $file = $href = '';
    $type = '';
    foreach($params as $_key => $_val) {
        switch($_key) {
            case 'file':
            case 'rel':
            case 'type':
                $$_key = $_val;
                break;

            default:
                if(!is_array($_val)) {
                    $extra .= ' '.$_key.'="'.smarty_function_escape_special_chars($_val).'"';
                } else {
                    $smarty->trigger_error("html_engine_style: extra attribute '$_key' cannot be an array", E_USER_NOTICE);
                }
                break;
        }
    }
    
    if (empty($file)) {
        $smarty->trigger_error("html_engine_style: missing 'file' parameter", E_USER_NOTICE);
        return;
    }
    
    if(!isset($params['rel'])) {
        $rel = 'stylesheet';
    }

    if(!isset($params['type'])) {
        $type = 'text/css';
    }

//		zvolení vzhledu
//		vybraný vzhled
	if(file_exists($smarty->template_face_dir.$smarty->template_engine_stylesheets_dir.DIRECTORY_SEPARATOR.$file)){
		$path_prefix = $smarty->template_face_dir_rel.$smarty->template_engine_stylesheets_dir.URL_SEPARATOR;
	} 
//		Výchozí vzhled
	else if(file_exists($smarty->template_default_face_dir.$smarty->template_engine_stylesheets_dir.DIRECTORY_SEPARATOR.$file)){
		$path_prefix = $smarty->template_default_face_dir_rel.$smarty->template_engine_stylesheets_dir.URL_SEPARATOR;
	} 
//		Vzhled v engine
	else {
		$path_prefix = $smarty->template_engine_stylesheets_dir.URL_SEPARATOR;
	};
    
    return '<link rel="'.$rel.'" type="'.$type.'" href="'.$path_prefix.$file.'"  '.$extra.' />';
}

/* vim: set expandtab: */

?>
