<?php

/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 */
class Services_Controller extends Controller {
   const BACKUP_NONE = 0;
   const BACKUP_DB = 1;
   const BACKUP_DATA = 2;
   const BACKUP_ALL = 4;

   protected function init() 
   {
      $this->checkControllRights();
   } 
   /**
    * Kontroler pro zobrazení textu
    */
   public function mainController() {
      $this->diskController();
   }
   public function diskController() {
      $this->checkControllRights();
      $formClearCache = new Form('cache_clear_');
      $elemSub = new Form_Element_Submit('clear', $this->tr('Vyčistit'));
      $formClearCache->addElement($elemSub);
      if ($formClearCache->isValid()) {

         $dir = new DirectoryIterator(AppCore::getAppCacheDir());
         foreach ($dir as $fileinfo) {
            if ($fileinfo->isDot() OR $fileinfo->getFilename() == '.svn' | '.git')
               continue;
            @unlink(AppCore::getAppCacheDir() . $fileinfo->getFilename());
         }
         $this->infoMsg()->addMessage($this->tr('Cache byla vyčištěna'));
         $this->link()->reload();
      }

      $this->view()->cacheSize = $this->dirSize(AppCore::getAppCacheDir());
      if ($this->view()->cacheSize / (1024 * 1024) > 10) {
         $this->view()->isBigSize = true;
      }
      $this->view()->formClearCache = $formClearCache;
      $this->view()->dataSize = $this->dirSize(AppCore::getAppDataDir());
   }

   public function databaseController() {
      $this->checkControllRights();
      /**
       * Optimalizace db tabulek
       */
      $formOptimiseDbTables = new Form('db_tables_optimise_');
      $elemSub = new Form_Element_Submit('optimise', $this->tr('Optimalizovat tabulky'));
      $formOptimiseDbTables->addElement($elemSub);
      if ($formOptimiseDbTables->isValid()) {
         $modelTables = new Model_Tables();
         $modelTables->where(Model_Tables::COL_TABLE_SCHEMA.' = :dbname AND '
            .'('.Model_Tables::COL_TABLE_NAME.' LIKE :prefix OR '.Model_Tables::COL_TABLE_NAME.' LIKE :prefixGlobal )',
            array('dbname' => VVE_DB_NAME,'prefix' => VVE_DB_PREFIX.'%','prefixGlobal' => 'global%'));

         $dbc = Db_PDO::getInstance();
         $prevBuff = $dbc->getAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY);
         $dbc->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
         foreach ($modelTables->records() as $table) {
            // opravovat jsou pouze MyISAM tabulky
            if($table->{Model_Tables::COL_ENGINE} == 'MyISAM'){
               $dbst = $dbc->prepare('OPTIMIZE TABLE '.$table->{Model_Tables::COL_TABLE_NAME});
               $dbst->execute();
               $dbst->closeCursor();
            }
         }
         $dbc->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, $prevBuff);

         $this->infoMsg()->addMessage($this->tr('Tabulky v db byly optimalizovány'));
         $this->link()->reload();
      }
      $this->view()->formOptimizeDbTables = $formOptimiseDbTables;

      /**
       * Oprava db tabulek
       */
      $formRepairDbTables = new Form('db_tables_repair_');
      $elemSub = new Form_Element_Submit('repair', $this->tr('Opravit tabulky'));
      $formRepairDbTables->addElement($elemSub);
      if ($formRepairDbTables->isValid()) {
         $modelTables = new Model_Tables();
         $modelTables->where(Model_Tables::COL_TABLE_SCHEMA.' = :dbname AND '
            .'('.Model_Tables::COL_TABLE_NAME.' LIKE :prefix OR '.Model_Tables::COL_TABLE_NAME.' LIKE :prefixGlobal )',
            array('dbname' => VVE_DB_NAME,'prefix' => VVE_DB_PREFIX.'%','prefixGlobal' => 'global%'));

         $dbc = Db_PDO::getInstance();
         $prevBuff = $dbc->getAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY);
         $dbc->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
         foreach ($modelTables->records() as $table) {
            // opravovat jsou pouze MyISAM tabulky
            if($table->{Model_Tables::COL_ENGINE} == 'MyISAM'){
               $dbst = $dbc->prepare('REPAIR TABLE '.$table->{Model_Tables::COL_TABLE_NAME});
               $dbst->execute();
               $dbst->closeCursor();
            }
         }
         $dbc->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, $prevBuff);

         $this->infoMsg()->addMessage($this->tr('Tabulky v db byly opraveny'));
         $this->link()->reload();
      }
      $this->view()->formRepairDbTables = $formRepairDbTables;
   }

   public function tablesListController() {
      $this->checkControllRights();
      // objekt komponenty JGrid
      $jqGrid = new Component_JqGrid();
      $jqGrid->request()->setDefaultOrderField(Model_Tables::COL_TABLE_NAME);
      $modelTables = new Model_Tables();
      $modelTables->order(array($jqGrid->request()->orderField => $jqGrid->request()->order));

      $baseWhere = Model_Tables::COL_TABLE_SCHEMA.' = :dbname AND '
         .'('.Model_Tables::COL_TABLE_NAME.' LIKE :prefix OR '.Model_Tables::COL_TABLE_NAME.' LIKE :prefixGlobal )';
      $baseWhereVals = array(
         'dbname' => VVE_DB_NAME,
         'prefix' => VVE_DB_PREFIX.'%',
         'prefixGlobal' => 'global%'
         );
      // search
      if ($jqGrid->request()->isSearch()) {
         switch ($jqGrid->request()->searchType()) {
            case Component_JqGrid_Request::SEARCH_EQUAL:
               $modelTables->where($jqGrid->request()->searchField().' = :str',array('str' => $jqGrid->request()->searchString()));
               break;
            case Component_JqGrid_Request::SEARCH_NOT_EQUAL:
               $modelTables->where($jqGrid->request()->searchField().' != :str', array('str' => $jqGrid->request()->searchString()));
               break;
            case Component_JqGrid_Request::SEARCH_NOT_CONTAIN:
               $modelTables->where($jqGrid->request()->searchField().' NOT LIKE :str', array('str' => '%'.$jqGrid->request()->searchString().'%'));
               break;
            case Component_JqGrid_Request::SEARCH_CONTAIN:
            default:
               $modelTables->where($jqGrid->request()->searchField().' LIKE :str', array('str' => '%'.$jqGrid->request()->searchString().'%'));
               break;
         }
         $jqGrid->respond()->setRecords($modelTables->count());
         $users = $modelTables->limit(($jqGrid->request()->page - 1) * $jqGrid->respond()->getRecordsOnPage(), $jqGrid->request()->rows)
            ->records();
      }

      $modelTables->where($baseWhere, $baseWhereVals);

      $jqGrid->respond()->setRecords($modelTables->count());
      $tables = $modelTables->limit(($jqGrid->request()->page - 1) * $jqGrid->respond()->getRecordsOnPage(), $jqGrid->request()->rows)->records();
      // out
      $dbc = Db_PDO::getInstance();
      foreach ($tables as $id => $table) {
         // kontrola tebulky
         $data = $dbc->query('CHECK TABLE '.$table->{Model_Tables::COL_TABLE_NAME}.' FAST QUICK')->fetchObject();
         $msg = $data->Msg_type;
         if(isset ($data->Msg_text)) $msg = $data->Msg_text;

         array_push($jqGrid->respond()->rows, array('id' => $id,
             'cell' => array(
                 $table->{Model_Tables::COL_TABLE_NAME},
                 $table->{Model_Tables::COL_TABLE_ROWS},
                 $table->{Model_Tables::COL_DATA_LENGTH},
                 $table->{Model_Tables::COL_TABLE_COLLATION},
                 $table->{Model_Tables::COL_ENGINE},
                 $msg
                 )));
         unset ($data);
      }
      $this->view()->respond = $jqGrid->respond();
   }

   public function backupController() 
   {
      $form = new Form('backup_db_');
      $eType = new Form_Element_Select('type', $this->tr('Zálohovat'));
      $eType->setOptions(array(
          $this->tr('Vše') => self::BACKUP_ALL,
          $this->tr('Databázi') => self::BACKUP_DB,
          $this->tr('Data') => self::BACKUP_DATA,
      ));
      $form->addElement($eType);
      
      $eSubmit = new Form_Element_Submit('backup', $this->tr('Provést zálohu'));
      $form->addElement($eSubmit);
      
      if($form->isValid()){
         $file = self::createBackup($form->type->getValues());
         $this->infoMsg()->addMessage( sprintf( $this->tr('Záloha DB byla uložena do souboru %s.'),$file ) );
         $this->link()->reload();
      }
      $this->view()->form = $form;
      
      $formClean = new Form('backup_clean_');
      $eOld = new Form_Element_Select('oldest', $this->tr('Vymazat'));
      $eOld->setOptions(array(
            $this->tr('Starší než měsíc') => 'month',
            $this->tr('Starší než rok') => 'year',
            $this->tr('vše') => 'all',
            ));
      $formClean->addElement($eOld);
      
      $eS = new Form_Element_Submit('clean', $this->tr('Vymazat'));
      $formClean->addElement($eS);
      
      if ($formClean->isValid()) {
         $type = $formClean->oldest->getValues();
         switch ($type) {
            case 'all':
               $compareTime = time();
               break;
            case 'year':
               $compareTime = time()-60*60*24*365;
               break;
            case 'month':
            default:
               $compareTime = time()-60*60*24*31;
               break;
         }
         
         foreach (glob(self::getBackupPath()."*.{sql,zip}", GLOB_BRACE) as $file) {
            if( filemtime($file) <= $compareTime ){
               unlink($file);
            }
         }
         $this->infoMsg()->addMessage($this->tr('Zálohy byly smazány'));
         $this->link()->reload();
      }
      
      $this->view()->formClean = $formClean;
      
      // load all backup files
      $files = glob(self::getBackupPath()."*.{sql,zip}", GLOB_BRACE);
      usort($files, create_function('$a,$b', 'return filemtime($b) - filemtime($a);'));
      $this->view()->backupFiles = $files;
   }
   
   /**
    * Metoda spočítá velikost adresáře
    * @param string $dir -- adresář
    * @return string
    */
   private function dirSize($dir) {
      $size = 0;
      foreach (new DirectoryIterator($dir) as $fileInfo) {
         if ($fileInfo->isDot() OR $fileInfo->getFilename() == '.svn' | '.git'){
            continue;
         } else if($fileInfo->isDir()){
            $size += $this->dirSize($fileInfo->getPathname());
         } else {
            $size += $fileInfo->getSize();
         }
      }
      return $size;
   }

   protected static function createBackup($type = self::BACKUP_ALL)
   {
      $backupPath = self::getBackupPath();
      
      $dbc = Db_PDO::getInstance();
      $filePrefix = $backupPath.'db-backup-'.vve_date("%Y-%M-%D_%H-%i-%s").(CUBE_CMS_SUB_SITE_DIR == null ? '' : '_'.CUBE_CMS_SUB_SITE_DIR);
      $fileDb = $filePrefix.'.sql';
      $fileData = $filePrefix.'.zip';

      if(is_file($fileDb)){
         unlink($fileDb);
      }

      if($type == self::BACKUP_DB || $type == self::BACKUP_ALL){
         //get all of the tables
         $tables = array();
         $stmt = $dbc->prepare('SHOW TABLES');
         $stmt->execute();
         $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
         //cycle through table
         foreach($tables as $table) {
            file_put_contents($fileDb, '-- TABLE: '.$table."\n", FILE_APPEND);
            file_put_contents($fileDb, 'DROP TABLE IF EXISTS '.$table.';', FILE_APPEND);

            $stmt = $dbc->prepare('SHOW CREATE TABLE '.$table);
            $stmt->execute();
            $createSQL = $stmt->fetch(PDO::FETCH_NUM);
            file_put_contents($fileDb, "\n\n".$createSQL[1].";\n\n", FILE_APPEND);

            $stmt = $dbc->prepare('SELECT * FROM '.$table);
            $stmt->execute();
            $c = $stmt->fetch(PDO::FETCH_NUM);

            // empty tables ckip
            if(!$c){ continue; }

            $num_fields = count($c);

            $stmt = $dbc->prepare('SELECT * FROM '.$table);
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
               file_put_contents($fileDb, 'INSERT INTO '.$table.' VALUES(', FILE_APPEND);
               $return = null;
               for($j=0; $j<$num_fields; $j++) {
                  $row[$j] = addslashes($row[$j]);
                  $row[$j] = str_replace("\n","\\n",$row[$j]);

                  $return .= '"'.$row[$j].'"' ;
                  if ($j < ($num_fields-1)) {
                     $return .= ',';
                  }
               }
               $return .= ");\n";
               file_put_contents($fileDb, $return, FILE_APPEND);
            }
            file_put_contents($fileDb, "\n\n\n", FILE_APPEND);
         }
      }

      // backup data dir to zip
      if ( ($type == self::BACKUP_DATA || $type == self::BACKUP_ALL) && extension_loaded('zip')) {
         $zip = new ZipArchive();

         if ($zip->open($fileData, ZIPARCHIVE::CREATE) && is_dir(AppCore::getAppDataDir())) {

            $source = str_replace('\\', '/', realpath(AppCore::getAppDataDir()));

            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator( $source ), RecursiveIteratorIterator::SELF_FIRST);

            foreach ($files as $file) {
               $file = str_replace('\\', '/', $file);
               // Ignore "." and ".." folders
               if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) )
                  continue;

               $file = realpath($file);

               if (is_dir($file) === true)
               {
                  $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
               }
               else if (is_file($file) === true)
               {
                  $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
               }
            }

            $zip->close();
         }
      }

      return $fileDb;
   }
   
   public function fileActionController( ) 
   {
      $act = $this->getRequestParam('a');
      $file = $this->getRequestParam('file');
      
      switch ($act) {
         case 'dw':
            Template_Output::factory('txt');
            Template_Output::setDownload($file);
            Template_Output::addHeader('Content-Description: File Transfer');
            Template_Output::addHeader('Pragma: public');
            Template_Output::addHeader('Expires: 0');
            Template_Output::sendHeaders();
            ob_clean();
            flush();
            readfile(self::getBackupPath().$file);
            exit;
         break;
         default:
            ;
         break;
      }
   }
   
   protected static function getBackupPath() 
   {
      if(!is_dir(AppCore::getAppLibDir().'backup' )){
         // mkdir by ftp account
         // mkdir(AppCore::getAppWebDir().'backup');
         new CoreErrors(new CoreException( 'Neexistuje adresář pro zálohy. Vytvořte adresář "backup" v kořenu stránek.') ) ;
      }
      return AppCore::getAppLibDir().'backup'.DIRECTORY_SEPARATOR;
   }
   
   public function dbadminController() 
   {
      ;
   } 
   
   /* Autorun metody */
   public static function AutoRunWeekly()
   {
      // backup only main site
      if(VVE_SUB_SITE_DOMAIN == null){
         self::createBackup(self::BACKUP_DB);
         Log::msg('Provedena záloha db');
      }
   }
}
?>