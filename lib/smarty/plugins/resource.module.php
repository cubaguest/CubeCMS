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
	$modulePath = $smarty->_tpl_vars['MODULE_TPL_FILE'];
	$matches = array();
//	Regulérní výraz pro vytáhnutí názvu modulu
	$reg = "/".$smarty->template_modules_dir."\/(.*)\/".$smarty->template_dir."/";
	preg_match($reg, $modulePath, $matches);
	$moduleName = $matches[1];
	
	$fileContent = null;
	$path = null;
//	Cesta k šabloně modulu	
	$modTplDir = DIRECTORY_SEPARATOR.$smarty->template_modules_dir.DIRECTORY_SEPARATOR.$moduleName.DIRECTORY_SEPARATOR.$smarty->template_dir.DIRECTORY_SEPARATOR;
	
	if(file_exists($smarty->template_face_dir.$modTplDir.$tpl_name)){
		$path = $smarty->template_face_dir.$modTplDir;
		$file = fopen($smarty->template_face_dir.$modTplDir.$tpl_name, 'r');
		$fileContent = fread($file, filesize($smarty->template_face_dir.$modTplDir.$tpl_name));
		fclose($file);
	} else if(file_exists($smarty->template_default_face_dir.$modTplDir.$tpl_name)){
		$path = $smarty->template_default_face_dir.$modTplDir;
		$file = fopen($smarty->template_default_face_dir.$modTplDir.$tpl_name, 'r');
		$fileContent = fread($file, filesize($smarty->template_default_face_dir.$modTplDir.$tpl_name));
		fclose($file);
	} else if(file_exists('..'.$modTplDir.$tpl_name)){
		$path = '..'.$modTplDir.DIRECTORY_SEPARATOR;
		$file = fopen('..'.$modTplDir.$tpl_name, 'r');
		$fileContent = fread($file, filesize('..'.$modTplDir.$tpl_name));
		fclose($file);
	}
	
	if($fileContent != null){
		$smarty->_plugins['resource']['module']['tpl_file_path'] = $path;
		$tpl_source = $fileContent;
		return true;
	} else {
		return false;
	}
}

function smarty_resource_module_timestamp($tpl_name, &$tpl_timestamp, &$smarty)
{
	$path = $smarty->_plugins['resource']['module']['tpl_file_path'];
	
	if($path != null){
		$tpl_timestamp = filectime($path.$tpl_name);
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



?>