<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     resource.module.php
 * Type:     resource
 * Name:     db
 * Purpose:  Fetches templates from a module faces templates
 * Dependency: 	VVE engine version 3.1
 * @subpackage VVE engine version 3.1
 * @author Jakub Matas <jakubmatas at gmail dot com> -- for VVE (Veprove vypecky engine)
 * @version  1.0
 * -------------------------------------------------------------
 */
function smarty_resource_module_source($tpl_name, &$tpl_source, &$smarty)
{
	$fileContent = null;
	$path = null;

    $path = tplModuleFilePath($tpl_name, $smarty);
    if($path != null){
        $file = fopen($path.$tpl_name, 'r');
        $fileContent = fread($file, filesize($path.$tpl_name));
        fclose($file);
    }
	
	if($fileContent != null){
		$tpl_source = $fileContent;
		return true;
	} else {
		return false;
	}
}

function smarty_resource_module_timestamp($tpl_name, &$tpl_timestamp, &$smarty)
{
    if(tplModuleFilePath($tpl_name, $smarty) != null){
        $tpl_timestamp = filectime(tplModuleFilePath($tpl_name, &$smarty).$tpl_name);
        return true;
    } else {
        return false;
    }
}

function smarty_resource_module_secure($tpl_name, &$smarty)
{
	// assume all templates are secure
	return true;
}

function smarty_resource_module_trusted($tpl_name, &$smarty)
{
	// not used for templates
}

/**
 * Pomocné funkce pro zjišťování souboru
 * @param string -- název sablony
 * @param array -- ukazatel na pole smarty
 * @return string -- cesta k souboru
 */
function tplModuleFilePath($tpl_name, &$smarty) {
    $path = null;

    if(!isset ($smarty->_plugins['resource']['module']['tpl_file_path'][$tpl_name])){

        $modulePath = $smarty->_tpl_vars['MODULE_TPL_FILE'];
        $matches = array();

        $reg = "/".$smarty->template_modules_dir."\/(.*)\/".$smarty->template_dir."/";

        preg_match($reg, $modulePath, $matches);

        $moduleName = $matches[1];

        $fileContent = null;
        $path = null;

        $modTplDir = $smarty->template_modules_dir.DIRECTORY_SEPARATOR.$moduleName.DIRECTORY_SEPARATOR.$smarty->template_dir.DIRECTORY_SEPARATOR;
        if(file_exists($smarty->template_face_dir.$modTplDir.$tpl_name)){
            $path = $smarty->template_face_dir.$modTplDir;
        } else if(file_exists($smarty->template_default_face_dir.$modTplDir.$tpl_name)){
            $path = $smarty->template_default_face_dir.$modTplDir;
         } else if(file_exists('.'.DIRECTORY_SEPARATOR.$modTplDir.$tpl_name)){
            $path = '.'.DIRECTORY_SEPARATOR.$modTplDir.DIRECTORY_SEPARATOR;
        }
        $smarty->_plugins['resource']['module']['tpl_file_path'][$tpl_name] = $path;
    } else {
        $path = $smarty->_plugins['resource']['module']['tpl_file_path'][$tpl_name];
    }
    return $path;
}

?>