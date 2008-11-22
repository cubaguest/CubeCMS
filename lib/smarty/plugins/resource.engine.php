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
    $path = tplEngineFilePath($tpl_name, &$smarty);

    if($path != null){
        $file = fopen($path.$tpl_name, 'r');
        $fileContent = fread($file, filesize($path.$tpl_name));
        fclose($file);
    }

    if($fileContent != null){
        //		$smarty->_plugins['resource']['engine']['tpl_file_path'] = $path;
        $tpl_source = $fileContent;
        return true;
    } else {
        return false;
    }
}

function smarty_resource_engine_timestamp($tpl_name, &$tpl_timestamp, &$smarty)
{
    if(tplEngineFilePath($tpl_name, $smarty) != null){
        $tpl_timestamp = filectime(tplEngineFilePath($tpl_name, $smarty).$tpl_name);
        return true;
    } else {
        return false;
    }
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

/**
 * Pomocné funkce pro zjišťování souboru
 * @param string -- název sablony
 * @param array -- ukazatel na pole smarty
 * @return string -- cesta k souboru
 */
function tplEngineFilePath($file_name, &$smarty) {
    $path = null;

    if(!isset ($smarty->_plugins['resource']['engine']['tpl_file_path'][$file_name])){
        if(file_exists($smarty->template_face_dir.DIRECTORY_SEPARATOR.$smarty->template_dir.DIRECTORY_SEPARATOR.$tpl_name)){
            $path = $smarty->template_face_dir.$smarty->template_dir.DIRECTORY_SEPARATOR;
        } else if(file_exists($smarty->template_default_face_dir.DIRECTORY_SEPARATOR.$smarty->template_dir.DIRECTORY_SEPARATOR.$tpl_name)){
            $path = $smarty->template_default_face_dir.$smarty->template_dir.DIRECTORY_SEPARATOR;
        } else if(file_exists($smarty->template_dir.DIRECTORY_SEPARATOR.$tpl_name)){
            $path = $smarty->template_dir.DIRECTORY_SEPARATOR;
        }
        $smarty->_plugins['resource']['engine']['tpl_file_path'][$file_name] = $path;
    } else {
        $path = $smarty->_plugins['resource']['engine']['tpl_file_path'][$file_name];
    }
    return $path;
}

?>