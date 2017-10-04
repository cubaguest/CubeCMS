<?php

/**
 * Třída pro instalaci a upgrade jádra
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: $ VVE 6.1.0 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro instalaci jádra
 */
class Install_Core {
   const CORE_INSTALL_DIR = 'install';
   const CORE_UPGRADE_DIR = 'upgrade';
   const CORE_UPGRADE_RELEASES_DIR = 'releases';
   const CORE_UPGRADE_SQL_DIR = 'sql';
   const CORE_UPGRADE_PHP_DIR = 'php';

   const FILE_SQL_UPGRADE = 'upgrade_{from}_to_{to}.sql';
   const FILE_PHP_UPGRADE = 'upgrade_{from}_to_{to}.php';
   const FILE_PHP_PREPARE_UPGRADE = 'upgrade_prepare_{from}_to_{to}.php';

   const FILE_SQL_PATCH = 'patch_{to}.sql';
   const FILE_PHP_PATCH = 'patch_{to}.php';
   const FILE_PHP_PREPARE_UPDATE = 'prepare_{to}.php';

   const SQL_MAIN_SITE_UPDATE = 'UPDATE_MAIN_SITE';
   const SQL_SUB_SITE_UPDATE = 'UPDATE_SUB_SITE';
   const SQL_SITE_END_UPDATE = 'END_UPDATE';
   const SQL_SHOP_UPDATE = 'UPDATE_SHOP';

   const SQL_TABLE_PREFIX_REPLACEMENT = '{PREFIX}';

   public function __construct()
   {
   }

   /**
    * metoda pro instalaci modulu
    */
   public function install()
   {

   }

   /**
    * Metoda provede upgrade verze
    */
//   public function upgrade()
//   {
//      // kontrola downgrade
//      //      if(VVE_VERSION > AppCore::ENGINE_VERSION ){
//      //         echo sprintf('Downgrade verze %s na verzi %s nelze provádět',VVE_VERSION, AppCore::ENGINE_VERSION).'<br />';
//      //         return;
//      //      }
//      $modelCfg = new Model_Config();
//      $record = $modelCfg->columns(array(Model_Config::COLUMN_KEY, Model_Config::COLUMN_VALUE))->where(Model_Config::COLUMN_KEY, 'VERSION')->record();
//
//      $currentVer = VVE_VERSION;
//      // oprava aktuální verze na
//
//
//
//      try {
//         while ($currentVer != AppCore::ENGINE_VERSION) {
//            /* php prepare update */
//            $phpFileName = preg_replace(array('/{from}/', '/{to}/'), array($currentVer, $currentVer + 1), self::FILE_PHP_PREPARE_UPGRADE);
//
//            if (is_file($this->getInstallDir() . self::CORE_UPGRADE_DIR . DIRECTORY_SEPARATOR . $phpFileName)) {
//               include $this->getInstallDir() . self::CORE_UPGRADE_DIR . DIRECTORY_SEPARATOR . $phpFileName;
//            }
//
//            /* sql update */
//            $sqlFile = $this->getInstallDir() . $this->getInstallDir() . self::CORE_UPGRADE_DIR . DIRECTORY_SEPARATOR
//               . preg_replace(array('/{from}/', '/{to}/'), array($currentVer, $currentVer + 1), self::FILE_SQL_UPGRADE);
//
//            if(is_file($sqlFile)){
//               $handle = @fopen($sqlFile, "r");
//               if ($handle) {
//                  $update = 'all';
//                  $sql = null;
//                  $m = str_repeat('-', 2);
//
//                  while (($buffer = fgets($handle)) !== false) {
//                     if(strlen($buffer) <= 30 && strpos($buffer, self::SQL_MAIN_SITE_UPDATE)){
//                        $update = 'main';
//                        $sql .= $m.' UPDATING '. $update .' ' .$m ."\n";
//                     } else if(strlen($buffer) <= 30 && strpos($buffer, self::SQL_SUB_SITE_UPDATE)){
//                        $update = 'sub';
//                        $sql .= $m . ' UPDATING '. $update .' ' . $m ."\n";
//                     } else if(strlen($buffer) <= 30 && strpos($buffer, self::SQL_SHOP_UPDATE)){ // checking shop enabled
//                        $update = 'shop';
//                        $sql .= $m . ' UPDATING '. $update .' ' . $m ."\n";
//                     } else if(strlen($buffer) <= 30 && strpos($buffer, self::SQL_SITE_END_UPDATE)){
//                        $sql .= $m.' ENDING '. $update .' '. $m ."\n";
//                        $update = 'all';
//                     } else {
//                        if($buffer != null && ($update == 'all' || (VVE_SUB_SITE_DIR == null && $update == 'main' ) || (VVE_SUB_SITE_DIR != null && $update == 'sub' ) )){
//                           $sql .= $buffer;
//                        } else {
//                           $sql .= $m.' SKIPING '. $update .' '. $m ."\n";
////                            $sql .= '-- '.$buffer;
//                        }
//                     }
//                  }
////                   echo nl2br("-- SQL Update :\n ".$sql);
//                  $this->runSQLCommand($this->replaceDBPrefix($sql));
//
//                  if (!feof($handle)) {
//                     echo "Error: unexpected fgets() fail\n";
//                  }
//
//                  fclose($handle);
//               }
//            }
//            // post upgrade
//            $phpFileName = preg_replace(array('/{from}/', '/{to}/'), array($currentVer, $currentVer + 1), self::FILE_PHP_UPGRADE);
//
//            if (is_file($this->getInstallDir() . self::CORE_UPGRADE_DIR . DIRECTORY_SEPARATOR . $phpFileName)) {
//               include $this->getInstallDir() . self::CORE_UPGRADE_DIR . DIRECTORY_SEPARATOR . $phpFileName;
//            }
//
//            $record->{Model_Config::COLUMN_VALUE} = $currentVer + 1;
//            $modelCfg->save($record);
//
//            $currentVer++; // loop na další verzi
//         }
//      } catch (Exception $exc) {
//         echo 'ERROR: Chyba při upgradu<br />';
//         echo $exc->getMessage().'<br />';
//         echo 'SQL: '.nl2br($sql).'<br />';
//         echo "DEBUG: <br/ >";
//         echo $exc->getTraceAsString();
//         die ();
//      }
//      $this->installComplete(sprintf(_('Jádro bylo aktualizováno na verzi %s release %s'), AppCore::ENGINE_VERSION, 0));
//   }

   /**
    * Metoda provede upgrade jádra
    * @return <type>
    */
   public function upgrade()
   {
      // kontrola downgrade
      if(version_compare(AppCore::ENGINE_VERSION, CUBE_CMS_VERSION) != 1){
         return;
      }

      $modelCfg = new Model_Config();
      $record = $modelCfg->columns(array(Model_Config::COLUMN_KEY, Model_Config::COLUMN_VALUE))->where(Model_Config::COLUMN_KEY, 'VERSION')->record();

      if($record == false){ // release není v databázi
         define('CUBE_CMS_VERSION', 6);
         // aktualizovat version na 6.1.0 ???
      }

      $currentVer = CUBE_CMS_VERSION;
      // oprava aktuální verze na plnou hodnotu
      if(strpos((string)CUBE_CMS_VERSION, '.') === false){
         $currentVer = CUBE_CMS_VERSION.'.'.CUBE_CMS_RELEASE.'.0';
      }

      $upgradeVersions = array();
      if(is_dir($this->getReleasesDir())){
         foreach (glob($this->getReleasesDir()."*.txt") as $filename) {
            $version = str_replace(".txt", "", basename($filename));
            if(version_compare($version, $currentVer) == 1){
               $upgradeVersions[] = $version;
            }
         }
      }
      natcasesort($upgradeVersions);

      if(!empty($upgradeVersions)){
         $phpDir = $this->getUpgradeDir() . self::CORE_UPGRADE_PHP_DIR . DIRECTORY_SEPARATOR;
         $sqlDir = $this->getUpgradeDir() . self::CORE_UPGRADE_SQL_DIR . DIRECTORY_SEPARATOR;

         $isMainSite = true;

         /**
          * Tohle by hctělo předělat nějak jinak a detekovat to přímo z tabulky se sites
          */
         if(
            defined('CUBE_CMS_SUB_SITE_DOMAIN') && CUBE_CMS_SUB_SITE_DOMAIN != null
            ||
            // OLD !!!
            ( defined('CUBE_CMS_SUB_SITE_DIR') && CUBE_CMS_SUB_SITE_DIR != null )
            ||
            ( defined('CUBE_CMS_USE_SUBDOMAIN_HTACCESS_WORKAROUND') && CUBE_CMS_USE_SUBDOMAIN_HTACCESS_WORKAROUND != null )
         ){
            $isMainSite = false;
         }
         
         $recVer = Model_Config::getInstance()->where(Model_Config::COLUMN_KEY.' = :key', array('key' => 'VERSION'))->record();

         foreach($upgradeVersions as $version){
            /* php prepare update */
            $phpFile = $version.'.php';
            $prePhpFile = $version.'_pre.php';
            $sqlFile = $version.'.sql';
            $sqlFileMainSite = $version.'_main.sql';
            $sqlFileSubSite = $version.'_sub.sql';
            $sqlFileShop = $version.'_shop.sql';

            try {
               // pre update
               if (is_file($phpDir.$prePhpFile)) {
                  include $phpDir.$prePhpFile;
               }
//               var_dump($sqlDir.$sqlFile);
               // update sql
               if (is_file($sqlDir.$sqlFile)) {
                  $this->processSqlUpgradeFile($sqlDir.$sqlFile);
               }

               // update main site sql
               if ($isMainSite && is_file($sqlDir.$sqlFileMainSite)) {
                  $this->processSqlUpgradeFile($sqlDir.$sqlFileMainSite);
               }

               // update sub site sql
               if (!$isMainSite && is_file($sqlDir.$sqlFileSubSite)) {
                  $this->processSqlUpgradeFile($sqlDir.$sqlFileSubSite);
               }

               // update shop sql
               if (defined('CUBE_CMS_SHOP') && CUBE_CMS_SHOP == true && is_file($sqlDir.$sqlFileShop)) {
                  $this->processSqlUpgradeFile($sqlDir.$sqlFileShop);
               }

               // post update
               if (is_file($phpDir.$phpFile)) {
                  include $phpDir.$phpFile;
               }
               
               $recVer->{Model_Config::COLUMN_VALUE} = $version;
               $recVer->save();
            } catch (Exception $exc) {
               var_dump($exc);die;
               echo 'ERROR: Chyba při aktualizaci<br />';
               echo $exc->getMessage().'<br />';
               echo "DEBUG: <br/ >";
               echo $exc->getTraceAsString();
               die ();
            }
         }
      }

      Install_Module::updateAllModules();
      // update subdomains
      $this->updateSubdomains();

      $this->installComplete(sprintf('Jádro a moduly bylo aktualizováno na verzi %s', AppCore::ENGINE_VERSION));
   }

   protected function processSqlUpgradeFile($file)
   {
      $handle = @fopen($file, "r");
      if ($handle) {
         $update = 'all';
         $sql = null;
         $m = str_repeat('-', 2);

         while (($buffer = fgets($handle)) !== false) {
            if(strlen($buffer) <= 30 && strpos($buffer, self::SQL_MAIN_SITE_UPDATE)){
               $update = 'main';
               $sql .= $m.' UPDATING '. $update ."\n";
            } else if(strlen($buffer) <= 30 && strpos($buffer, self::SQL_SUB_SITE_UPDATE)){
               $update = 'sub';
               $sql .= $m . ' UPDATING '. $update."\n";
            } else if(strlen($buffer) <= 30 && strpos($buffer, self::SQL_SHOP_UPDATE)){ // musí být definová CUBE_CMS_SHOP
               $update = 'shop';
               $sql .= $m . ' UPDATING SHOP '. $update."\n";
            } else if(strlen($buffer) <= 30 && strpos($buffer, self::SQL_SITE_END_UPDATE)){
               $sql .= $m.' ENDING '. $update."\n";
               $update = 'all';
            } else if($buffer != null) {
               if($update == 'all' ||
                  (defined('CUBE_CMS_SUB_SITE_DIR') && ((!defined('CUBE_CMS_USE_SUBDOMAIN_HTACCESS_WORKAROUND') && CUBE_CMS_SUB_SITE_DIR == null && $update == 'main' )
                     || (!defined('CUBE_CMS_USE_SUBDOMAIN_HTACCESS_WORKAROUND') && CUBE_CMS_SUB_SITE_DIR != null && $update == 'sub' ) ) )
                  // for old subdomain htaccess
                  || (defined('CUBE_CMS_USE_SUBDOMAIN_HTACCESS_WORKAROUND') && ((CUBE_CMS_USE_SUBDOMAIN_HTACCESS_WORKAROUND == null && $update == 'main' )
                     || (CUBE_CMS_USE_SUBDOMAIN_HTACCESS_WORKAROUND != null && $update == 'sub' )) )
               ){
                  $sql .= $buffer;
               } else if($update == 'shop' && defined('CUBE_CMS_SHOP') && CUBE_CMS_SHOP == true){
                  $sql .= $buffer;
               } else {
                  $sql .= $m.' SKIPING '. $update  ."\n";
               }

            }
         }
//          echo nl2br("-- SQL Update :\n ".$sql);
         $sql = $this->replaceDBPrefix($sql);
//          echo nl2br("-- SQL Update :\n ".$sql).'<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />';
//          echo $sql;
//         print_r(nl2br($sql));die;
         $this->runSQLCommand($sql);

         if (!feof($handle)) {
            echo "Error: unexpected fgets() fail\n";
         }

         fclose($handle);
      }
   }

   private function updateSubdomains()
   {
      // do not update main site
      if(strpos($_SERVER['SERVER_NAME'], 'www') !== false){
         $modelDomains = new Model_Sites();
         $subDomains = $modelDomains->where(Model_Sites::COLUMN_IS_MAIN." = 0", array())->records();
         if(!empty($subDomains)){
            foreach($subDomains as $domain){
               // build link
//               $site = str_replace('www', $domain->{Model_Sites::COLUMN_DOMAIN}, 'http://'.$_SERVER['SERVER_NAME'])."/sitemap.html?sessionid=".session_id();
               $site = str_replace('www', $domain->{Model_Sites::COLUMN_DOMAIN}, 'http://'.$_SERVER['SERVER_NAME']);
//               var_dump($site);
               $ch = curl_init($site);
               curl_setopt($ch, CURLOPT_AUTOREFERER, true);
               curl_setopt($ch, CURLOPT_HEADER, true);
               curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
               curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
               curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml; charset=UTF-8"));
               curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
               curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
               $res = curl_exec($ch);
//               var_dump($res);
               if($res === false)
               {
                  Log::msg('Nelze updatovat subdoménu: '.$domain->{Model_Sites::COLUMN_DOMAIN}." CURL_ERRNO: ".curl_errno($ch)." CURL_ERROR: ".curl_error($ch));
               }
               curl_close($ch);
            }
         }
      }
   }

   /**
    * Metoda provede upgrade na verzi, kterou lze aktualizovat
    */
   public function upgradeToMain()
   {
      $modelCfg = new Model_Config();
      $record = $modelCfg
         ->columns(array(Model_Config::COLUMN_KEY, Model_Config::COLUMN_VALUE))
         ->where(Model_Config::COLUMN_KEY, 'VERSION')
         ->record();
      if($record == false) {
         $record = $modelCfg->newRecord();
         $record->{Model_Config::COLUMN_KEY} = 'VERSION';
      }
      $record->{Model_Config::COLUMN_VALUE} = 6;
      $record->{Model_Config::COLUMN_TYPE} = Model_Config::TYPE_STRING;
      $record->{Model_Config::COLUMN_LABEL} = 'Verze jádra';
      $record->{Model_Config::COLUMN_PROTECTED} = true;
      $modelCfg->save($record);
      echo ('Jádro bylo násilně aktualizováno na novou verzi. Kontaktuje webmastera, protože nemusí pracovat správně!');
      header('Location: http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
      die();
   }


   /**
    * metoda vrátí instalovanou verzi jádra
    */
   public function getInstaledVersion()
   {
      return AppCore::ENGINE_VERSION;
   }

   /**
    * Metoda pro instalaci SQL patchů
    * @param string $SQL -- SQL patch
    */
   protected function runSQLCommand($SQL)
   {
      $model = new Model_DbSupport();
      $ret = $model->execSQL($SQL);
      
      if($ret === false){
         throw new PDOException('Undefined SQL error: '. "\n" . 'SQL: '.$SQL);
      }
   }

   protected function getSQLFileContent($file = 'install.sql')
   {
      if (file_exists($this->getInstallDir() . $file)) {
         return file_get_contents($this->getInstallDir() . $file);
      } else {
         return null;
      }
   }

   private function installComplete($msg)
   {
      setcookie('upgrade', $msg, time() + 3600);
      header('Location: http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
      die ();
   }


   /**
    * Metoda nastaví název datového adresáře
    * @return string
    */
   public function getInstallDir()
   {
      return AppCore::getAppLibDir() . AppCore::ENGINE_LIB_DIR . DIRECTORY_SEPARATOR
         . self::CORE_INSTALL_DIR . DIRECTORY_SEPARATOR;
   }

   /**
    * Metoda vrací cestu k upgrade adresáři
    * @return string
    */
   public function getUpgradeDir()
   {
      return $this->getInstallDir() . self::CORE_UPGRADE_DIR . DIRECTORY_SEPARATOR;
   }

   /**
    * Metoda nastaví název adresáře s releasy
    * @return string
    */
   public function getReleasesDir()
   {
      return $this->getUpgradeDir() . self::CORE_UPGRADE_RELEASES_DIR . DIRECTORY_SEPARATOR;
   }

   protected function replaceDBPrefix($cnt)
   {
      return str_replace(self::SQL_TABLE_PREFIX_REPLACEMENT, defined('CUBE_CMS_DB_PREFIX') ? CUBE_CMS_DB_PREFIX : VVE_DB_PREFIX, $cnt);
   }

   public static function addUpgradeMessages()
   {
      if(isset ($_COOKIE['upgrade'])){
         AppCore::getInfoMessages()->addMessage($_COOKIE['upgrade'], false);
         setcookie('upgrade', '', time() - 3600);
      }
   }
   
    /**
    * Metoda pro aktualizaci jazyků
    * @param array $langs pole s jazyky
    */
   public static function updateInstalledLangs($langs = null)
   {
      $modelCat = new Model_Category();
      $modelPanels = new Model_Panel();
      $modelCfgGroups = new Model_ConfigGroups();
      // load langs from db
      if($langs == null){
         $langs = explode(';', Model_Config::getValue('APP_LANGS', array()));
      }
      
      foreach ($langs as $lang) {
         $modelCat->updateLangColumns($lang);
         $modelPanels->updateLangColumns($lang);
         $modelCfgGroups->updateLangColumns($lang);
      }
      
      if(defined('CUBE_CMS_SHOP') && CUBE_CMS_SHOP){
         $dir = AppCore::getAppLibDir().AppCore::ENGINE_LIB_DIR.DIRECTORY_SEPARATOR.'shop'.DIRECTORY_SEPARATOR.'model'.DIRECTORY_SEPARATOR;
         $files = glob($dir.'*.php');
         if(!empty($files)){
            foreach ($files as $file) {
               $file = str_replace(arraY('.class.php', 'shop_model_'), array('', ''), basename($file));
               $modelClass = 'Shop_Model_'.ucfirst($file);
               if(class_exists($modelClass)){
                  $model = new $modelClass();
                  if($model instanceof Model_ORM){
                     foreach ($langs as $lang) {
                        $model->updateLangColumns($lang);
                     }
                  }
               }
            }  
         }
      }
      
      $modelModules = new Model_Module();
      $modules = $modelModules->records();
      foreach ($modules as $module) {
         $className = ucfirst($module->{Model_Module::COLUMN_NAME}).'_Module';
         if(class_exists($className)){
            $mod = new $className();
//            var_dump('Aktualizace modulu '.$className.' '.$mod->getName());
            foreach ($langs as $lang) {
               $mod->installLang($lang);
            }
         }
      }
      Log::msg('Proběhla aktualizace jazykových sloupů');
      AppCore::getInfoMessages()->addMessage('Aktualizace jazyků proběhla úspěšně');
   }
}
