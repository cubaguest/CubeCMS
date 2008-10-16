<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {html_engine_image} function plugin
 *
 * Type:     function<br>
 * Name:     html_engine_image<br>
 * Date:     Feb 24, 2003<br>
 * Purpose:  format HTML tags for the engine image<br>
 * Input:<br>
 *         - file = file (and path) of image (required)
 *         - height = image height (optional, default actual height)
 *         - width = image width (optional, default actual width)
 *         - basedir = base directory for absolute paths, default
 *                     is environment variable DOCUMENT_ROOT
 *         - path_prefix = prefix for path output (optional, default empty)
 *
 * Examples: {html_engine_image file="/images/masthead.gif"}
 * Output:   <img src="/images/masthead.gif" width=400 height=23>
 * @link http://smarty.php.net/manual/en/language.function.html.image.php {html_image}
 *      (Smarty online manual)
 * @author   Monte Ohrt <monte at ohrt dot com>
 * @author credits to Duda <duda@big.hu> - wrote first image function
 *           in repository, helped with lots of functionality
 * @author Jakub Matas <jakubmatas at gmail dot com> -- uprava pro VVE
 * @version  1.0
 * @param array
 * @param Smarty
 * @return string
 * @uses smarty_function_escape_special_chars()
 */
function smarty_function_html_engine_image($params, &$smarty)
{
    require_once $smarty->_get_plugin_filepath('shared','escape_special_chars');
    
    $alt = '';
    $file = '';
    $height = '';
    $width = '';
    $extra = '';
    $prefix = '';
    $suffix = '';
    $path_prefix = '';
    $server_vars = ($smarty->request_use_auto_globals) ? $_SERVER : $GLOBALS['HTTP_SERVER_VARS'];
//    $basedir = isset($server_vars['DOCUMENT_ROOT']) ? $server_vars['DOCUMENT_ROOT'] : '';
    foreach($params as $_key => $_val) {
        switch($_key) {
            case 'file':
            case 'height':
            case 'width':
            case 'dpi':
            case 'path_prefix':
            case 'basedir':
                $$_key = $_val;
                break;

            case 'alt':
                if(!is_array($_val)) {
                    $$_key = smarty_function_escape_special_chars($_val);
                } else {
                    $smarty->trigger_error("html_image: extra attribute '$_key' cannot be an array", E_USER_NOTICE);
                }
                break;

            case 'link':
            case 'href':
                $prefix = '<a href="' . $_val . '">';
                $suffix = '</a>';
                break;

            default:
                if(!is_array($_val)) {
                    $extra .= ' '.$_key.'="'.smarty_function_escape_special_chars($_val).'"';
                } else {
                    $smarty->trigger_error("html_image: extra attribute '$_key' cannot be an array", E_USER_NOTICE);
                }
                break;
        }
    }

    if (empty($file)) {
        $smarty->trigger_error("html_image: missing 'file' parameter", E_USER_NOTICE);
        return;
    }

    if (substr($file,0,1) == '/') {
    	$_image_path = $basedir . $file;
    } else {
    	$_image_path = $file;
    }

    $fileNotExist = false;
	if(file_exists($smarty->template_face_dir.$smarty->template_engine_images_dir.DIRECTORY_SEPARATOR.$_image_path)){
		$_image_path = $smarty->template_face_dir.$smarty->template_engine_images_dir.DIRECTORY_SEPARATOR.$_image_path;
		$path_prefix = $smarty->template_face_dir_rel.$smarty->template_engine_images_dir.URL_SEPARATOR;
	} 
//		Výchozí vzhled
	else if(file_exists($smarty->template_default_face_dir.$smarty->template_engine_images_dir.DIRECTORY_SEPARATOR.$_image_path)){
		$_image_path = $smarty->template_default_face_dir.$smarty->template_engine_images_dir.DIRECTORY_SEPARATOR.$_image_path;
		$path_prefix = $smarty->template_default_face_dir_rel.$smarty->template_engine_stylesheets_dir.URL_SEPARATOR;
	} 
//		Vzhled v engine
	else  if(file_exists($smarty->template_dir.DIRECTORY_SEPARATOR.$_image_path)){
		$path_prefix = '.'.URL_SEPARATOR.$smarty->template_engine_images_dir.URL_SEPARATOR;
		$_image_path = '.'.URL_SEPARATOR.$smarty->template_engine_images_dir.URL_SEPARATOR.$_image_path;
	} else {
		$fileNotExist = true;    
	}
    
//		zvolení vzhledu
//		vybraný vzhled
//	if(file_exists(AppCore::getTepmlateFaceDir().AppCore::TEMPLATES_IMAGES_DIR.DIRECTORY_SEPARATOR.$_image_path)){
//		$_image_path = AppCore::getTepmlateFaceDir().AppCore::TEMPLATES_IMAGES_DIR.DIRECTORY_SEPARATOR.$_image_path;
//		$path_prefix = AppCore::getTepmlateFaceDir(false).AppCore::TEMPLATES_IMAGES_DIR.URL_SEPARATOR;
//	} 
////		Výchozí vzhled
//	else if(file_exists(AppCore::getTepmlateDefaultFaceDir().AppCore::TEMPLATES_IMAGES_DIR.DIRECTORY_SEPARATOR.$_image_path)){
//		$_image_path = AppCore::getTepmlateDefaultFaceDir().AppCore::TEMPLATES_IMAGES_DIR.DIRECTORY_SEPARATOR.$_image_path;
//		$path_prefix = AppCore::getTepmlateDefaultFaceDir(false).AppCore::TEMPLATES_IMAGES_DIR.URL_SEPARATOR;
//	} 
////		Vzhled v engine
//	else {
//		$_image_path = '.'.DIRECTORY_SEPARATOR.AppCore::TEMPLATES_IMAGES_DIR.DIRECTORY_SEPARATOR.$_image_path;
//		$path_prefix = '.'.URL_SEPARATOR.AppCore::TEMPLATES_IMAGES_DIR.URL_SEPARATOR;
//	};
	
//    echo "TADY ---- ".$path_prefix.$file;
    
    if(!isset($params['width']) || !isset($params['height'])) {
        if(!$_image_data = @getimagesize($_image_path)) {
            if(!file_exists($_image_path)) {
                $smarty->trigger_error("html_image: unable to find '$_image_path'", E_USER_NOTICE);
                return;
            } else if(!is_readable($_image_path)) {
                $smarty->trigger_error("html_image: unable to read '$_image_path'", E_USER_NOTICE);
                return;
            } else {
                $smarty->trigger_error("html_image: '$_image_path' is not a valid image file", E_USER_NOTICE);
                return;
            }
        }
        if ($smarty->security &&
            ($_params = array('resource_type' => 'file', 'resource_name' => $_image_path)) &&
            (require_once(SMARTY_CORE_DIR . 'core.is_secure.php')) &&
            (!smarty_core_is_secure($_params, $smarty)) ) {
            $smarty->trigger_error("html_image: (secure) '$_image_path' not in secure directory", E_USER_NOTICE);
        }        
        
        if(!isset($params['width'])) {
            $width = $_image_data[0];
        }
        if(!isset($params['height'])) {
            $height = $_image_data[1];
        }

    }

    if(isset($params['dpi'])) {
        if(strstr($server_vars['HTTP_USER_AGENT'], 'Mac')) {
            $dpi_default = 72;
        } else {
            $dpi_default = 96;
        }
        $_resize = $dpi_default/$params['dpi'];
        $width = round($width * $_resize);
        $height = round($height * $_resize);
    }

    if(!$fileNotExist){
        return $prefix . '<img src="'.$path_prefix.$file.'" alt="'.$alt.'" width="'.$width.'" height="'.$height.'"'.$extra.' />' . $suffix;
	} else {
		return false;
	}
}

/* vim: set expandtab: */

?>
