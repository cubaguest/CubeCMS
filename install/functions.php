<?php
if(!defined('PHP_VERSION_ID')){
   $version = explode('.',PHP_VERSION);
   define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
   define('PHP_MAJOR_VERSION',   $version[0]);
   define('PHP_MINOR_VERSION',   $version[1]);
   define('PHP_RELEASE_VERSION', $version[2]);
}

// funkce pro kontrolu php
function checkPHPInstalation()
{
   $msgs = array();
   // grp basic
   addMsg ($msgs, 'Základní', 'group');
   // verze php
   $status = 'ok'; $info = null;
   if(PHP_MAJOR_VERSION < 5 OR (PHP_MAJOR_VERSION == 5 AND PHP_MINOR_VERSION < 2 )) {
      $status = 'err';
      $info = 'PHP verze menší jak 5.2 není podporováno, protože obsahuje spoustu chyb a některé funkce nejsou implementovány. Doporučujeme upgrade.';
   } else if(PHP_MAJOR_VERSION == 5 AND (PHP_MINOR_VERSION == 2 AND PHP_RELEASE_VERSION < 6)) {
      $status = 'warn';
      $info = 'Doporučujeme PHP verze 5.2.6 a vyšší. Některé funkce v nižších verzích nemusí pracovat správně.';
   }
   addMsg ($msgs, 'Verze PHP: '.PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION.'.'.PHP_RELEASE_VERSION, $status, $info);
   $status = 'ok'; $info = null;

   // SAFE MODE
   $status = 'ok'; $info = null;
   if(ini_get('safe_mode')){
      $status = 'warn';
      $info = 'PHP <i>Safe Mode</i> by mělo být vypnuto. V opačném případě hrozí snížení výkonu.';
   }
   addMsg ($msgs, 'Vypnuté PHP <i>Safe Mode</i>', $status, $info);

   // magic quotes
   $status = 'ok'; $info = null;
   if(get_magic_quotes_gpc() === 1){
      $status = 'warn';
      $info = 'PHP volba <i>magic_quotes_gpc</i> by mělo být vypnuta. V opačném případě hrozí snížení výkonu.';
   }
   addMsg ($msgs, 'Vypnuté PHP <i>magic_quotes_gpc</i>', $status, $info);

   // max upload size
   $status = 'ok'; $info = null;
   $max_upload = (int)(ini_get('upload_max_filesize'));
   $max_post = (int)(ini_get('post_max_size'));
   $memory_limit = (int)(ini_get('memory_limit'));
   $upload_mb = min($max_upload, $max_post, $memory_limit);
   if($upload_mb < 8){
      $status = 'warn';
      $info = 'Maximální velikost nahraného souboru je příliš malá. Doporučeno je alespoň 8 MB.';
   }
   addMsg ($msgs, 'Maximální velikost nahraného souboru: '.$upload_mb.' MB', $status, $info);
   // SPL
   $status = 'ok'; $info = null;
   if(!class_exists('Exception')) {
      $status = 'err';
      $info = 'Podpora SPL je vyžadována';
   }
   addMsg ($msgs, 'Podpora SPL', $status, $info);

   // DateTime
   $status = 'ok'; $info = null;
   if(!class_exists('DateTime')) {
      $status = 'err';
      $info = 'Podpora třídy DateTime je vyžadována';
   }
   addMsg ($msgs, 'Podpora DateTime', $status, $info);




   // grp libs
   addMsg ($msgs, 'Knihovny', 'group');

   // PDO
   $status = 'ok'; $info = null;
   if(!class_exists('PDO')) {
      $status = 'err';
      $info = 'Podpora přístupu k databázi pomocí PDO je vyžadována';
   }
   addMsg ($msgs, 'Podpora PDO', $status, $info);

   // PDO MYSQL
   if(class_exists('PDO')) {
      $status = 'ok'; $info = null;
      $drivers = pdo_drivers();
      if(!in_array('mysql', $drivers)) {
         $status = 'err';
         $info = 'PDO musí mít podporu MySQL databáze';
      }
      addMsg ($msgs, 'Podpora PDO ovladače pro MySQL databázi', $status, $info);
   }
   // curl
   $status = 'ok'; $info = null;
   if(!function_exists('curl_init')) {
      $status = 'warn';
      $info = 'Rozšíření cURL je doporučeno';
   }
   addMsg ($msgs, 'Podpora cURL', $status, $info);

   // mime
   $status = 'ok'; $info = null;
   if(!function_exists('mime_content_type') AND !class_exists('finfo')) {
      $status = 'err';
      $info = 'Podpora funkce <i>mime_content_type</i> nebo třídy <i>finfo</i> je vyžadována';
   }
   addMsg ($msgs, 'Podpora detekce MIME typu', $status, $info);

   // gettext
   $status = 'ok'; $info = null;
   if(!function_exists('gettext')) {
      $status = 'err';
      $info = 'Podpora funkce <i>gettext</i> je vyžadována (bude odstraněna v příštích verzích).';
   }
   addMsg ($msgs, 'Podpora GETTEXT', $status, $info);
   // ftp
   $status = 'ok'; $info = null;
   if(!function_exists('ftp_connect')) {
      $status = 'warn';
      $info = 'Podpora FTP není instalována.';
   }
   addMsg ($msgs, 'Podpora FTP', $status, $info);

   // JSON
   $status = 'ok'; $info = null;
   if(!function_exists('json_decode')) {
      $status = 'err';
      $info = 'Podpora JSON je vyžadována.';
   }
   addMsg ($msgs, 'Podpora JSON', $status, $info);

   // MB
   $status = 'ok'; $info = null;
   if(!function_exists('mb_get_info')) {
      $status = 'err';
      $info = 'Podpora MB String je vyžadována.';
   }
   addMsg ($msgs, 'Podpora MB String (MultiByte Strings)', $status, $info);

   // SimpleXML
   $status = 'ok'; $info = null;
   if(!class_exists('SimpleXMLElement')) {
      $status = 'err';
      $info = 'Podpora SimpleXML je vyžadována.';
   }
   addMsg ($msgs, 'Podpora SimpleXML', $status, $info);

   // XMLReader
   $status = 'ok'; $info = null;
   if(!class_exists('XMLReader')) {
      $status = 'err';
      $info = 'Podpora XMLReader je vyžadována.';
   }
   addMsg ($msgs, 'Podpora XMLReader', $status, $info);
   // XMLWriter
   $status = 'ok'; $info = null;
   if(!class_exists('XMLWriter')) {
      $status = 'err';
      $info = 'Podpora XMLWriter je vyžadována.';
   }
   addMsg ($msgs, 'Podpora XMLWriter', $status, $info);

   // TOKENIZER
   $status = 'ok'; $info = null;
   if(!function_exists('token_name')) {
      $status = 'err';
      $info = 'Podpora TOKENIZER není vyžadována, ale je použita pro překladač statických textů.';
   }
   addMsg ($msgs, 'Podpora TOKENIZER', $status, $info);

   // TOKENIZER
   $status = 'ok'; $info = null;
   if(!class_exists('ZipArchive')) {
      $status = 'err';
      $info = 'Podpora ZipArchive není vyžadována..';
   }
   addMsg ($msgs, 'Podpora ZipArchive', $status, $info);



   // obrázky
   // grp images
   addMsg ($msgs, 'Obrázky', 'group');
   $gdinfo = gd_info();
   // jpeg
   $status = 'ok'; $info = null;
   if($gdinfo['JPEG Support'] != true) {
      $status = 'warn';
      $info = 'Podpora pro JPEG by měla být zapnuta. Jinak nelze zpracovávat obrázky JPEG.';
   }
   addMsg ($msgs, 'Podpora JPEG', $status, $info);
   // png
   $status = 'ok'; $info = null;
   if($gdinfo['PNG Support'] != true) {
      $status = 'warn';
      $info = 'Podpora pro PNG by měla být zapnuta. Jinak nelze zpracovávat obrázky PNG.';
   }
   addMsg ($msgs, 'Podpora PNG', $status, $info);
   // gif
   $status = 'ok'; $info = null;
   if($gdinfo['GIF Create Support'] != true OR $gdinfo['GIF Read Support'] != true) {
      $status = 'warn';
      $info = 'Podpora pro GIF by měla být zapnuta. Jinak nelze zpracovávat obrázky GIF.';
   }
   addMsg ($msgs, 'Podpora GIF', $status, $info);


   return $msgs;
}

function addMsg(&$msgs, $name, $status = 'ok', $info = null)
{
   global $allowNextStep;
   if($status == 'err'){
      $allowNextStep = false;
   }
   array_push($msgs, array('name' => $name, 'status' => $status, 'info' => $info));
}

function redirectToStep()
{
   global $step;
   redirectTo('http://'.$_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'].'?'.http_build_query(array('step' => $step+1)));
}
function redirectTo($url)
{
   header('Location: '.$url);
}

function checkDir($dir, $dirGroupName = 'Datový adresář')
{
   $msgs = array();

   addMsg ($msgs, $dirGroupName, 'group');
   
   // kontrola existence datového adresáře
   $status = 'ok'; $info = null;
   if(!file_exists(CMS_DIR.$dir) OR !is_dir(CMS_DIR.$dir)) {
      $status = 'err';
      $info = 'Vytvořte adresář <i>'.$dir.'</i> v kořenu webu';
   }
   addMsg ($msgs, 'Adresář vytvořen', $status, $info);

   // kontrola práv adresáře
   $info = 'Nastavte adresáři <i>'.$dir.'</i> v kořenu webu oprávnění na 777';
   $status = 'err';
   if(file_exists(CMS_DIR.$dir) AND is_dir(CMS_DIR.$dir) AND is_writable(CMS_DIR.$dir)) {
      $status = 'ok';
      $info = null;
   }
   addMsg ($msgs, 'Oprávnění adresáře', $status, $info);


   return $msgs;
}


?>
