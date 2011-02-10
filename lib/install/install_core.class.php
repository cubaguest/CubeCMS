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
   const CORE_UPGRADE_SQL_DIR = 'sql';
   const CORE_UPGRADE_PHP_DIR = 'php';

   const FILE_SQL_UPGRADE = 'upgrade_{from}_to_{to}.sql';
   const FILE_PHP_UPGRADE = 'upgrade_{from}_to_{to}.php';

   const FILE_SQL_PATCH = 'patch_{to}.sql';
   const FILE_PHP_PATCH = 'patch_{to}.php';

   protected $tablesPrefix = '{PREFIX}';

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
   public function upgrade()
   {
      // kontrola downgrade
      if(VVE_VERSION > AppCore::ENGINE_VERSION ){
         echo sprintf(_('Downgrade verze %s na verzi %s nelze provádět'),VVE_VERSION, AppCore::ENGINE_VERSION).'<br />';
         return;
      }
      $modelCfg = new Model_Config();
      $record = $modelCfg->columns(array(Model_Config::COLUMN_KEY, Model_Config::COLUMN_VALUE))->where(Model_Config::COLUMN_KEY, 'VERSION')->record();

      $currentVer = (int)VVE_VERSION;
      try {
         while ($currentVer != AppCore::ENGINE_VERSION) {
            /* php update */
            $phpFileName = preg_replace(array('/{from}/', '/{to}/'), array($currentVer, $currentVer + 1), self::FILE_PHP_UPGRADE);


            if (file_exists($this->getInstallDir() . self::CORE_UPGRADE_DIR . DIRECTORY_SEPARATOR . $phpFileName)) {
               include $this->getInstallDir() . self::CORE_UPGRADE_DIR . DIRECTORY_SEPARATOR . $phpFileName;
            }

            /* sql update */
            $sqlFileName = preg_replace(array('/{from}/', '/{to}/'), array($currentVer, $currentVer + 1), self::FILE_SQL_UPGRADE);

            $file = new Filesystem_File_Text($sqlFileName, $this->getInstallDir() . self::CORE_UPGRADE_DIR . DIRECTORY_SEPARATOR, false);
            if ($file->exist()) {
               $this->runSQLCommand($this->replaceDBPrefix($file->getContent()));
            }
            $record->{Model_Config::COLUMN_VALUE} = $currentVer + 1;
            $modelCfg->save($record);

            $currentVer++; // loop na další verzi
         }
      } catch (Exception $exc) {
         echo "<br/><strong>Chyba při upgradu!!!</strong><br />";
         echo '<pre>'.$exc->getTraceAsString().'</pre>';
         echo '<pre>'.htmlspecialchars($this->replaceDBPrefix($file->getContent())).'</pre>';

         die;
      }
      $this->installComplete(sprintf(_('Jádro bylo aktualizováno na verzi %s release %s'), AppCore::ENGINE_VERSION, 0));
   }

   /**
    * Metoda provede update releasu
    * @return <type>
    */
   public function update()
   {
      // kontrola downgrade
      if(VVE_RELEASE > AppCore::ENGINE_RELEASE ){
         echo sprintf(_('Downgrade revize %s na revizi %s nelze provádět'),VVE_RELEASE, AppCore::ENGINE_RELEASE);
         return;
      } else if(VVE_RELEASE == AppCore::ENGINE_RELEASE ){
         return;
      }
      $modelCfg = new Model_Config();
      $modelCfg->where(Model_Config::COLUMN_KEY, 'RELEASE')->columns(array(Model_Config::COLUMN_KEY, Model_Config::COLUMN_VALUE));
      $record = $modelCfg->record();
      $versionDir = (string)AppCore::ENGINE_VERSION;

      $currentRelease = VVE_RELEASE;
      while ($currentRelease != AppCore::ENGINE_RELEASE) {
         /* php update */
         $phpFileName = preg_replace('/{to}/', $currentRelease+1, self::FILE_PHP_PATCH);
         if(file_exists($this->getInstallDir().self::CORE_UPGRADE_DIR.DIRECTORY_SEPARATOR.$versionDir.DIRECTORY_SEPARATOR.$phpFileName)){
            include $this->getInstallDir().self::CORE_UPGRADE_DIR.DIRECTORY_SEPARATOR.$versionDir.DIRECTORY_SEPARATOR.$phpFileName;
         }

         /* sql update */
         $sqlFileName = preg_replace('/{to}/', $currentRelease+1, self::FILE_SQL_PATCH);
         $file = new Filesystem_File_Text($sqlFileName, $this->getInstallDir().self::CORE_UPGRADE_DIR.DIRECTORY_SEPARATOR.$versionDir.DIRECTORY_SEPARATOR, false);
         if ($file->exist()) {
            $this->runSQLCommand($this->replaceDBPrefix($file->getContent()));
         }
         $record->{Model_Config::COLUMN_VALUE} = $currentRelease + 1;
         $modelCfg->save($record);
         $currentRelease++;
      }
      $this->installComplete(sprintf(_('Jádro bylo aktualizováno na verzi %s release %s'), AppCore::ENGINE_VERSION, AppCore::ENGINE_RELEASE));
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
      if($record == false) $record = $modelCfg->newRecord();
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

   }

   /**
    * Metoda pro instalaci SQL patchů
    * @param string $SQL -- SQL patch
    */
   protected function runSQLCommand($SQL)
   {
      $model = new Model_DbSupport();
      $model->runSQL($SQL);
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

   protected function replaceDBPrefix($cnt)
   {
      return str_replace($this->tablesPrefix, VVE_DB_PREFIX, $cnt);
   }

   public static function addUpgradeMessages()
   {
      if(isset ($_COOKIE['upgrade'])){
         AppCore::getInfoMessages()->addMessage($_COOKIE['upgrade'], false);
         setcookie('upgrade', '', time() - 3600);
      }
   }

}
?>
