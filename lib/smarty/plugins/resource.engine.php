<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     resource.engine.php
 * Type:     resource
 * Name:     db
 * Purpose:  Fetches templates from a engine faces templates
 * Dependency: 	VVE engine version 3.1
 * @subpackage VVE engine version 3.1
 * @author Jakub Matas <jakubmatas at gmail dot com> -- for VVE (Veprove vypecky engine)
 * @version  1.0
 * -------------------------------------------------------------
 */
function smarty_resource_engine_source($tpl_name, &$tpl_source, &$smarty)
{
	$fileContent = null;

	if(file_exists($smarty->template_face_dir.DIRECTORY_SEPARATOR.$smarty->template_dir.DIRECTORY_SEPARATOR.$tpl_name)){
		$file = fopen($smarty->template_face_dir.DIRECTORY_SEPARATOR.$smarty->template_dir.DIRECTORY_SEPARATOR.$tpl_name, 'r');
		$fileContent = fread($file, filesize($smarty->template_face_dir.DIRECTORY_SEPARATOR.$smarty->template_dir.DIRECTORY_SEPARATOR.$tpl_name));
		fclose($file);
	} else if(file_exists($smarty->template_default_face_dir.DIRECTORY_SEPARATOR.$smarty->template_dir.DIRECTORY_SEPARATOR.$tpl_name)){
		$file = fopen($smarty->template_default_face_dir.DIRECTORY_SEPARATOR.$smarty->template_dir.DIRECTORY_SEPARATOR.$tpl_name, 'r');
		$fileContent = fread($file, filesize($smarty->template_default_face_dir.DIRECTORY_SEPARATOR.$smarty->template_dir.DIRECTORY_SEPARATOR.$tpl_name));
		fclose($file);
	} else if(file_exists($smarty->template_dir.DIRECTORY_SEPARATOR.$tpl_name)){
		$file = fopen($smarty->template_dir.DIRECTORY_SEPARATOR.$tpl_name, 'r');
		$fileContent = fread($file, filesize($smarty->template_dir.DIRECTORY_SEPARATOR.$tpl_name));
		fclose($file);
	}
	
	if($fileContent != null){
		$tpl_source = $fileContent;
		return true;
	} else {
		return false;
	}
}

function smarty_resource_engine_timestamp($tpl_name, &$tpl_timestamp, &$smarty)
{
	$tpl_timestamp = time();
	return true;
}

function smarty_resource_engine_secure($tpl_name, &$smarty)
{
	// assume all templates are secure
	return true;
}

function smarty_resource_engine_trusted($tpl_name, &$smarty)
{
	// not used for templates
}



?>