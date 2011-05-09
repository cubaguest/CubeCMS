<?php
/**
 * Třída pro práci s orm modelem nad databází (instalace, updaty, kontrola atd)
 *
 * @copyright  	Copyright (c) 2008-2010 Jakub Matas
 * @version    	$Id: dbmodel.class.php 615 2009-06-09 13:05:12Z jakub $ VVE3.9.2 $Revision: 615 $
 * @author			$Author: jakub $ $Date: 2009-06-09 15:05:12 +0200 (Út, 09 čen 2009) $
 *						$LastChangedBy: jakub $ $LastChangedDate: 2009-06-09 15:05:12 +0200 (Út, 09 čen 2009) $
 * @abstract 		Abstraktní třída pro vytvoření modelu pro práci s databází
 */

class Model_ORM_Db {

   private $defaultCollate = 'utf8_general_ci';
   private $defaultCharset = 'utf8';

   private $model = null;



   public function  __construct(Model_ORM $model) {
      $this->model = $model;
   }

   public function create() {
      $pdo = new Db_PDO();

      $columns = $this->model->getColumns();

      $colsArr = $pKeys = $uqKeys = $fullTextKeys = array();
      $haveAI = false;
      foreach ($columns as $colName => $params) {
         if($params['lang'] == true){ // vícejazyčný má více sloupců
            foreach (Locales::getAppLangs() as $lang) {
               switch ($lang) {
                  case 'cs':
                     $params['collate'] = 'utf8_czech_ci';
                     break;
                  case 'sk':
                     $params['collate'] = 'utf8_slovak_ci';
                     break;
                  default:
                     $params['collate'] = 'utf8_general_ci';
                     break;
               }
               $colsArr[] = $this->createColStr($colName.'_'.$lang, $params);
               // fulltext keys
               if($params['fulltext'] == true) {
                  $fullTextKeys[] = 'FULLTEXT KEY `'.$colName.'_'.$lang.'` (`'.$colName.'_'.$lang.'`)';
               }
               // unique keys
               if($params['uq'] == true) {
                  $uqKeys[] = 'UNIQUE KEY `'.$colName.'_'.$lang.'` (`'.$colName.'_'.$lang.'`)';
               }
            }
         } else {
            $colsArr[] = $this->createColStr($colName, $params);
            // fulltext keys
            if($params['fulltext'] == true) {
               $fullTextKeys[] = 'FULLTEXT KEY `'.$colName.'` (`'.$colName.'`)';
            }
            // unique keys
            if($params['uq'] == true) {
               $uqKeys[] = 'UNIQUE KEY `'.$colName.'` (`'.$colName.'`)';
            }
         }
         // ai
         if($params['ai'] == true) {
            $haveAI = true;
         }
         // primary keys
         if($params['pk'] == true) {
            $pKeys[] = 'PRIMARY KEY (`'.$colName.'`)';
         }
      }

      $sql = 'CREATE TABLE IF NOT EXISTS `'.  $this->model->getTableName().'`'
         .'('."\n". implode(",\n", array_merge($colsArr, $pKeys, $uqKeys, $fullTextKeys)) ."\n".')'."\n"
         .'ENGINE = '.  $this->model->getDbEngine()."\n"
         .'DEFAULT CHARACTER SET = '.  $this->defaultCharset."\n";

      if($haveAI){
         $sql .='AUTO_INCREMENT = 1'."\n";
      }
      $sql .= 'COLLATE = '.$this->defaultCollate.';';
      Debug::log($sql);
//      $pdo->query($sql);
   }

   /**
    *
    * @param <type> $colName
    * @param <type> $colParams
    * @return string
    *
    * column_definition:
    *  data_type [NOT NULL | NULL] [DEFAULT default_value]
    *  [AUTO_INCREMENT] [UNIQUE [KEY] | [PRIMARY] KEY]
    *  [COMMENT 'string']
    *  [COLUMN_FORMAT {FIXED|DYNAMIC|DEFAULT}]
    *  [STORAGE {DISK|MEMORY|DEFAULT}]
    *  [reference_definition]
    *
    */
   private function createColStr($colName, $colParams) {
      $c = '`'.$colName.'` '.  strtoupper($colParams['datatype']);

      if($colParams['pdoparam'] == PDO::PARAM_STR){// je text přidáme collate či charset
         if($colParams['characterset'] != $this->defaultCharset){
            $c .= ' CHARACTER SET '.$colParams['characterset'];
         }
         if($colParams['collate'] != $this->defaultCollate){
            $c .= ' COLLATE '.$colParams['collate'];
         }
      }

      // is null
      if($colParams['nn'] == true){
         $c .= ' NOT NULL';
      } else {
         $c .= ' NULL';
      }

      $info = $this->parseDataType($colParams['datatype']);

      if(isset ($colParams['default'])){ // default param is set
         // default type
         if(in_array($colParams['default'], array('CURRENT_TIMESTAMP'))){
            $c .= ' DEFAULT '.(string)$colParams['default'];
         } else if($colParams['pdoparam'] == PDO::PARAM_BOOL OR $colParams['pdoparam'] == PDO::PARAM_INT){
            $c .= ' DEFAULT '.(int)$colParams['default'];
         } else if($colParams['default'] == null OR strtolower ($colParams['default']) == 'null') {
            $c .= ' DEFAULT NULL';
         } else {
            $c .= ' DEFAULT \''.(string)$colParams['default'].'\'';
         }
      }

      // autoincrement
      if($colParams['ai'] == true){
         $c .= ' AUTO_INCREMENT';
      }

      return $c;
   }

   private function parseDataType($dataType) {
      $matches = array();
      preg_match('/^(?P<datatype>[a-z]+)(?:\((?P<size>[0-9]+)\))?[ ]+(?P<unsigned>unsigned)$/i', $dataType, $matches);
      return $matches;
   }

   public function checkModel() {
      $dbc = new Db_PDO();
      $columnsInModel = $this->model->getColumns();

      // načteme sloupce a převedeme na assoc pole s klíčema jako název sloupce
      $dbst = $dbc->query('SHOW FULL COLUMNS FROM '.$this->model->getTableName());
      $columnsInDb = array();
      foreach ($dbst->fetchAll(PDO::FETCH_ASSOC) as $col) {
         $columnsInDb[$col['Field']] = $col;
      }
      
      // načteme indexy a převedeme na assoc pole s klíčema jako název sloupce
      $dbst = $dbc->query('SHOW INDEXES FROM '.$this->model->getTableName());
      $columnsIndexes = array();
      foreach ($dbst->fetchAll(PDO::FETCH_ASSOC) as $col) {
         $columnsIndexes[$col['Column_name']] = $col;
      }
      
      // SQL pro úpravu
      $alterTableArray = null;

      // projdeme model a přidáme neexistující sloupce
      foreach ($columnsInModel as $column) {
         $colName = $column['name'];
         if($column['aliasFor'] != null){
            $colName = $column['aliasFor'];
         }
         
         // kontrola existence sloupce v db
         if($column['lang'] == true){
            foreach (Locales::getAppLangs() as $lang) {
               if(isset ($columnsInDb[$colName.'_'.$lang])){ // pokud sloupec je v db, provede se kontrola definice sloupce
                  $colInDb = $columnsInDb[$colName.'_'.$lang];
                  // kontrola datového typu
                  if(strtolower($column['datatype']) != $colInDb['Type']){
                     $alterTableArray[] = sprintf(' Change col "%s" type: %s > %s ,', $colInDb['Field'] , $colInDb['Type'], strtolower($column['datatype']));
                  }
                  
                  
               } else { // sloupce v db není, bude vytvořen
                  $alterTableArray[] = ' Add col: '.$colName.'_'.$lang.', ';
               }
               // odstraní sloupce z pole sloupcu z db
               unset ($columnsInDb[$colName.'_'.$lang]);
            }
         } else {
            if(isset ($columnsInDb[$colName])){ // pokud sloupec je v db, provede se kontrola definice sloupce
               
            } else { // sloupce v db není, bude vytvořen
               $alterTableArray[] = ' Add col: '.$colName.', ';
            }
            // odstraní sloupce z pole sloupcu z db
            unset ($columnsInDb[$colName]);
         }
      }
      
      // odstranění přebytečných sloupců
      foreach ($columnsInDb as $col) {
         $alterTableArray[] = ' Delete col: '.$col['Field'].', ';
      }

      Debug::table($alterTableArray);
      
      Debug::table($columnsInDb);
      Debug::table($columnsIndexes);
      Debug::table($columnsInModel);
   }

}
?>