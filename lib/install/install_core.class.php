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
   const CORE_UPGRADE_DIR = 'upgradedata';
   const CORE_UPGRADE_SQL_DIR = 'sql';
   const CORE_UPGRADE_PHP_DIR = 'php';

   const FILE_SQL_UPGRADE = 'upgrade_{from}_{to}.sql';
   const FILE_PHP_UPGRADE = 'upgrade_{from}_{to}.php';

   protected $tablesPrefix = '{PREFIX}';
   protected $version = array('major' => 6, 'minor' => 0, 'build' => 0); // začíná se od verze 6.0.0

   public function __construct() {
   }

   /**
    * metoda pro instalaci modulu
    */
   public function install() {

   }

   /**
    * Metoda pro upgrade jádra
    */
   public function upgrade() {
      // kontrola downgrade
      if((float)VVE_VERSION > (float)AppCore::ENGINE_VERSION ){
         new CoreErrors(new CoreException(sprintf(_('Downgrade verze %s na verzi %s nelze provádět'),number_format((float)VVE_VERSION,1,'.',''), number_format((float)AppCore::ENGINE_VERSION,1,'.',''))));
         return;
      }
      $currentVer = (float)VVE_VERSION;
      while ($currentVer < (float)AppCore::ENGINE_VERSION) {
         /* php update */
         $phpFileName = preg_replace(array('/{from}/', '/{to}/'),
                 array(number_format($currentVer, 1, '.', ''), number_format($currentVer+0.1, 1, '.', '')),
                         self::FILE_PHP_UPGRADE);
         if(file_exists($this->getInstallDir().self::CORE_UPGRADE_DIR.DIRECTORY_SEPARATOR
            .self::CORE_UPGRADE_PHP_DIR.DIRECTORY_SEPARATOR.$phpFileName)){
            include $this->getInstallDir().self::CORE_UPGRADE_DIR.DIRECTORY_SEPARATOR
               .self::CORE_UPGRADE_PHP_DIR.DIRECTORY_SEPARATOR.$phpFileName;
         }
         /* sql update */
         $sqlFileName = preg_replace(array('/{from}/', '/{to}/'),
                 array(number_format($currentVer, 1, '.', ''), number_format($currentVer+0.1, 1, '.', '')),
                         self::FILE_SQL_UPGRADE);
         $file = new Filesystem_File_Text($sqlFileName, $this->getInstallDir()
            .self::CORE_UPGRADE_DIR.DIRECTORY_SEPARATOR.self::CORE_UPGRADE_SQL_DIR.DIRECTORY_SEPARATOR, false);
         if ($file->exist()) {
            $this->runSQLCommand($this->replaceDBPrefix($file->getContent()));
         }
         $currentVer = $currentVer + 0.1;
      }
      $modelCfg = new Model_Config();
      $modelCfg->saveCfg('VERSION', AppCore::ENGINE_VERSION);
      AppCore::getInfoMessages()->addMessage(_('Jádro bylo aktualizováno na verzi').' '.number_format((float)AppCore::ENGINE_VERSION,1,'.',''));
      // reload nové verze
      $link = new Url_Link(true);
      $link->clear(true)->reload();
   }

   /**
    * metoda vrátí instalovanou verzi jádra
    */
   public function getInstaledVersion() {

   }

   /**
    * Metoda pro instalaci SQL patchů
    * @param string $SQL -- SQL patch
    */
   protected function runSQLCommand($SQL) {
      $model = new Model_DbSupport();
      $model->runSQL($SQL);
   }

   protected function getSQLFileContent($file = 'install.sql') {
      if (file_exists($this->getInstallDir() . $file)) {
         return file_get_contents($this->getInstallDir() . $file);
      } else {
         return null;
      }
   }

   /**
    * Metoda nastaví název datového adresáře
    * @return string
    */
   public function getInstallDir() {
      return AppCore::getAppLibDir() . AppCore::ENGINE_LIB_DIR . DIRECTORY_SEPARATOR
      . self::CORE_INSTALL_DIR . DIRECTORY_SEPARATOR;
   }

   protected function replaceDBPrefix($cnt) {
      return str_replace($this->moduleTablesPrefix, VVE_DB_PREFIX, $cnt);
   }

}
?>
