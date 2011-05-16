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

   const SQL_MAIN_SITE_UPDATE = 'UPDATE_MAIN_SITE';
   const SQL_SUB_SITE_UPDATE = 'UPDATE_SUB_SITE';
   const SQL_SITE_END_UPDATE = 'END_UPDATE';

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


            if (is_file($this->getInstallDir() . self::CORE_UPGRADE_DIR . DIRECTORY_SEPARATOR . $phpFileName)) {
               include $this->getInstallDir() . self::CORE_UPGRADE_DIR . DIRECTORY_SEPARATOR . $phpFileName;
            }

            /* sql update */
            $sqlFile = $this->getInstallDir() . $this->getInstallDir() . self::CORE_UPGRADE_DIR . DIRECTORY_SEPARATOR
               . preg_replace(array('/{from}/', '/{to}/'), array($currentVer, $currentVer + 1), self::FILE_SQL_UPGRADE);

            if(is_file($sqlFile)){
               $handle = @fopen($sqlFile, "r");
               if ($handle) {
                  $update = 'all';
                  $sql = null;
                  $m = str_repeat('-', 2);

                  while (($buffer = fgets($handle)) !== false) {
                     if(strlen($buffer) <= 30 && strpos($buffer, self::SQL_MAIN_SITE_UPDATE)){
                        $update = 'main';
                        $sql .= $m.' UPDATING '. $update .' ' .$m ."\n";
                     } else if(strlen($buffer) <= 30 && strpos($buffer, self::SQL_SUB_SITE_UPDATE)){
                        $update = 'sub';
                        $sql .= $m . ' UPDATING '. $update .' ' . $m ."\n";
                     } else if(strlen($buffer) <= 30 && strpos($buffer, self::SQL_SITE_END_UPDATE)){
                        $sql .= $m.' ENDING '. $update .' '. $m ."\n";
                        $update = 'all';
                     } else {
                        if($buffer != null && ($update == 'all' || (VVE_SUB_SITE_DIR == null && $update == 'main' ) || (VVE_SUB_SITE_DIR != null && $update == 'sub' ) )){
                           $sql .= $buffer;
                        } else {
                           $sql .= $m.' SKIPING '. $update .' '. $m ."\n";
//                            $sql .= '-- '.$buffer;
                        }
                     }
                  }
//                   echo nl2br("-- SQL Update :\n ".$sql);
                  $this->runSQLCommand($this->replaceDBPrefix($sql));

                  if (!feof($handle)) {
                     echo "Error: unexpected fgets() fail\n";
                  }

                  fclose($handle);
               }
            }


            $record->{Model_Config::COLUMN_VALUE} = $currentVer + 1;
            $modelCfg->save($record);

            $currentVer++; // loop na další verzi
         }
      } catch (Exception $exc) {
         echo 'ERROR: Chyba při upgradu<br />';
         echo $exc->getMessage().'<br />';
         echo 'SQL: '.nl2br($sql).'<br />';
         echo "DEBUG: <br/ >";
         echo $exc->getTraceAsString();
         die ();
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
      $record = $modelCfg->columns(array(Model_Config::COLUMN_KEY, Model_Config::COLUMN_VALUE))->where(Model_Config::COLUMN_KEY, 'RELEASE')->record();
      if($record == false){ // release není v databázi
         $record = $modelCfg->newRecord();
         $record->{Model_Config::COLUMN_KEY} = 'RELEASE';
         $record->{Model_Config::COLUMN_VALUE} = 0;
         $record->{Model_Config::COLUMN_PROTECTED} = true;
         $record->{Model_Config::COLUMN_LABEL} = 'verze release';
         $record->{Model_Config::COLUMN_TYPE} = 'number';
         $modelCfg->save($record);
         $record = $modelCfg->columns(array(Model_Config::COLUMN_KEY, Model_Config::COLUMN_VALUE))->where(Model_Config::COLUMN_KEY, 'RELEASE')->record();
      }

      $versionDir = (string)AppCore::ENGINE_VERSION;
      $currentRelease = VVE_RELEASE;
      while ($currentRelease != AppCore::ENGINE_RELEASE) {
         try {
            /* php update */
            $phpFileName = preg_replace('/{to}/', $currentRelease + 1, self::FILE_PHP_PATCH);
            if (is_file($this->getInstallDir() . self::CORE_UPGRADE_DIR . DIRECTORY_SEPARATOR . $versionDir . DIRECTORY_SEPARATOR . $phpFileName)) {
               include $this->getInstallDir() . self::CORE_UPGRADE_DIR . DIRECTORY_SEPARATOR . $versionDir . DIRECTORY_SEPARATOR . $phpFileName;
            }

            /* sql update */
            $sqlFile = $this->getInstallDir() . self::CORE_UPGRADE_DIR . DIRECTORY_SEPARATOR . $versionDir . DIRECTORY_SEPARATOR
               . preg_replace('/{to}/', $currentRelease + 1, self::FILE_SQL_PATCH);

            if(is_file($sqlFile)){
               $handle = @fopen($sqlFile, "r");
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
                     } else if(strlen($buffer) <= 30 && strpos($buffer, self::SQL_SITE_END_UPDATE)){
                        $sql .= $m.' ENDING '. $update."\n";
                        $update = 'all';
                     } else if($buffer != null) {
                        if($update == 'all' ||
                        (defined('VVE_SUB_SITE_DIR') && ((!defined('VVE_USE_SUBDOMAIN_HTACCESS_WORKAROUND') && VVE_SUB_SITE_DIR == null && $update == 'main' )
                                                     || (!defined('VVE_USE_SUBDOMAIN_HTACCESS_WORKAROUND') && VVE_SUB_SITE_DIR != null && $update == 'sub' ) ) )
                        // for old subdomain htaccess
                        || (defined('VVE_USE_SUBDOMAIN_HTACCESS_WORKAROUND') && ((VVE_USE_SUBDOMAIN_HTACCESS_WORKAROUND == null && $update == 'main' )
                                                                          || (VVE_USE_SUBDOMAIN_HTACCESS_WORKAROUND != null && $update == 'sub' )) )
                                                                          ){
                           $sql .= $buffer;
                        } else {
                           $sql .= $m.' SKIPING '. $update  ."\n";
//                            $sql .= '-- '.$buffer;
                        }
                     }
                  }
//                   echo nl2br("-- SQL Update :\n ".$sql);die();
                  $sql = $this->replaceDBPrefix($sql);
//                   echo $sql;
                  $this->runSQLCommand($sql);

                  if (!feof($handle)) {
                     echo "Error: unexpected fgets() fail\n";
                  }

                  fclose($handle);
               }
            }

            $record->{Model_Config::COLUMN_VALUE} = $currentRelease + 1;
            $modelCfg->save($record);
         } catch (Exception $exc) {
            echo 'ERROR: Chyba při aktualizaci<br />';
            echo $exc->getMessage().'<br />';
            echo 'SQL: '.nl2br($sql).'<br />';
            echo "DEBUG: <br/ >";
            echo $exc->getTraceAsString();
            die ();
         }
         $currentRelease++;
      }
//       die();
      $this->installComplete(sprintf('Jádro bylo aktualizováno na verzi %s release %s', AppCore::ENGINE_VERSION, AppCore::ENGINE_RELEASE));
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
      $stmt = $model->runSQL($SQL);
      if(!$stmt){
         var_dump($stmt->errorInfo());
         throw new PDOException('Undefined SQL error.');
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
