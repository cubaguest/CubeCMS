<?php
/**
 * Soubor pro stazeni zadaneho souboru
 */
$url= urldecode($_GET["url"]);
if(isset($_GET['file'])){
   $file= urldecode($_GET["file"]);
} else {
   $file = basename($url);
   $url = str_replace('/'.$file, '', $url);
}

if(strpos($url, 'lib') !== false 
    || strpos($url, 'config') !== false 
    || strpos($url, 'modules')  !== false 
    || strpos($file, '.php') !== false
    || strpos($file, '.phtml') !== false
    || strpos($file, '.htaccess') !== false
    ){
   header('HTTP/1.0 403 Forbidden');
   echo 'Denied';die;
}

header("Content-Description: File Transfer");
header("Content-Type: application/force-download");
header("Content-Disposition: attachment; filename=\"$file\"");

readfile ($url."/".$file);