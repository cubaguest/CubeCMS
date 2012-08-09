<?php

/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 */
class Services_Controller extends Controller {

   /**
    * Kontroler pro zobrazení textu
    */
   public function mainController() {
      $this->diskController();
   }
   public function diskController() {
      $this->checkControllRights();
      $formClearCache = new Form('cache_clear_');
      $elemSub = new Form_Element_Submit('clear', $this->_('Vyčistit'));
      $formClearCache->addElement($elemSub);
      if ($formClearCache->isValid()) {

         $dir = new DirectoryIterator(AppCore::getAppCacheDir());
         foreach ($dir as $fileinfo) {
            if ($fileinfo->isDot() OR $fileinfo->getFilename() == '.svn' | '.git')
               continue;
            @unlink(AppCore::getAppCacheDir() . $fileinfo->getFilename());
         }
         $this->infoMsg()->addMessage($this->_('Cache byla vyčištěna'));
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
      $elemSub = new Form_Element_Submit('optimise', $this->_('Optimalizovat tabulky'));
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

         $this->infoMsg()->addMessage($this->_('Tabulky v db byly optimalizovány'));
         $this->link()->reload();
      }
      $this->view()->formOptimizeDbTables = $formOptimiseDbTables;

      /**
       * Oprava db tabulek
       */
      $formRepairDbTables = new Form('db_tables_repair_');
      $elemSub = new Form_Element_Submit('repair', $this->_('Opravit tabulky'));
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

         $this->infoMsg()->addMessage($this->_('Tabulky v db byly opraveny'));
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

}
?>