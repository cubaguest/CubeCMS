<?php
/* base headers */
header("Content-type: text/html; charset=utf-8");
$errMsg = null;
$buttonNextTitle = 'Pokračovat';
$currentLink = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'].'?'.$_SERVER['QUERY_STRING'];
$cmsLink = str_replace('install/index.php', 'ucet/',  'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME']);
// tohle ověřit
define('INST_DIR', str_replace('index.php', '', $_SERVER['SCRIPT_FILENAME']));
define('CMS_DIR', str_replace('install'.DIRECTORY_SEPARATOR, '', INST_DIR));
define('CMS_DATA_DIR_NAME', 'data');
define('CMS_CACHE_DIR_NAME', 'cache');
define('CMS_LOG_DIR_NAME', 'logs');
define('CMS_BACKUP_DIR_NAME', 'backup');

if(isset ($_GET['step'])){
   $step = (int)$_GET['step'];
} else {
   $step = 1;
}


$allowNextStep = true;
include_once INST_DIR.'functions.php';

switch ($step) {
   case 2: // adresáře
      $infoItems = checkDir(CMS_DATA_DIR_NAME);
      $infoItems = array_merge($infoItems, checkDir(CMS_CACHE_DIR_NAME, 'Odkládací adresář'));
      $infoItems = array_merge($infoItems, checkDir(CMS_LOG_DIR_NAME, 'Logovací adresář'));
      $infoItems = array_merge($infoItems, checkDir(CMS_BACKUP_DIR_NAME, 'Zálohovací adresář'));
      break;
   case 3: // databáze
      break;
   case 4: // konec
      $buttonNextTitle = 'Dokončit';
      break;
   case 1: // Vítejte
   default:
      $infoItems = checkPHPInstalation();
      break;
}

// zpracování formu
if (isset($_POST['next'])) {
   switch ($step) {
      case 2: // adresáře
         if(!$allowNextStep){
            $errMsg = 'Nelze pokračovat, protože nejsou splněny všechny podmínky.';
         } else {
            redirectToStep($step+1);
         }
         break;
      case 3: // databáze
         define('VVE_APP_IS_RUN', true);
         include CMS_DIR.'config'.DIRECTORY_SEPARATOR.'config.php';

         if($_POST['db_pass'] != VVE_DB_PASSWD){
            $errMsg = 'Špatně zadané heslo k databázi.';
         } else {
            $sqlStr = file_get_contents(INST_DIR.'sql'.DIRECTORY_SEPARATOR.'install.sql');
            $sqlStr = str_replace('{PREFIX}', VVE_DB_PREFIX, $sqlStr);

            if($sqlStr == null){
               $allowNextStep = false;
               $errMsg = 'Prázdný instalační skript.';
            }
            try {
               $dbc = new PDO('mysql:dbname='.VVE_DB_NAME.';host='.VVE_DB_SERVER, VVE_DB_USER, VVE_DB_PASSWD);
               $dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
               $dbc->exec('SET CHARACTER SET utf8; SET character_set_connection = utf8;');
               $dbc->exec($sqlStr);

               // install eshop
               if(isset ($_POST['shop_install']) && $_POST['shop_install'] == "true"){
                  $sqlStr = file_get_contents(INST_DIR.'sql'.DIRECTORY_SEPARATOR.'install_shop.sql');
                  $sqlStr = str_replace('{PREFIX}', VVE_DB_PREFIX, $sqlStr);
                  if($sqlStr != null){
                     $dbc->exec($sqlStr);
                  }
               }
            } catch (Exception $exc) {
               $allowNextStep = false;
               $errMsg = $exc->getTraceAsString();
            }

            if($allowNextStep){
               redirectToStep($step+1);
            }
         }
         break;
      case 4: // konec
         redirectTo($cmsLink);
         break;
      case 1: // Vítejte
      default:
         if(!$allowNextStep){
            $errMsg = 'Nelze pokračovat, protože nejsou splněny všechny podmínky.';
         } else {
            redirectToStep($step+1);
         }

         break;
   }
}

$tplFile = 'step_'.$step.'.phtml';

// include tpl
include './tpls/main.phtml';
?>
