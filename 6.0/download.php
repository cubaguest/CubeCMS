<?php
/**
 * Soubor pro stazeni zadaneho souboru
 */

$url= urldecode($_GET["url"]);
$file= urldecode($_GET["file"]);

header("Content-Description: File Transfer");
header("Content-Type: application/force-download");
header("Content-Disposition: attachment; filename=\"$file\"");

readfile ($url."/".$file);
?>