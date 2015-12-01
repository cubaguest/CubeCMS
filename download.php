<?php
/**
 * Soubor pro stazeni zadaneho souboru
 */

$url= urldecode($_GET["url"]);
if(isset($_GET['file'])){
   $file= urldecode($_GET["file"]);
} else {
   $file = basename($url);
}

header("Content-Description: File Transfer");
header("Content-Type: application/force-download");
header("Content-Disposition: attachment; filename=\"$file\"");

readfile ($url."/".$file);