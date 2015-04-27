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
   
   /**
    * kolace sloupců
    * @var array
    */
   private $langsColations = array(
                 'cs' => 'utf8_czech_ci', 
                 'sk' => 'utf8_slovak_ci', 
                 'en' => 'utf8_general_ci', 
                 'de' => 'utf8_general_ci', 
                 'pl' => 'utf8_polish_ci');
   
   /**
    * model
    * @var Model_ORM
    */
   private $model = null;

   public function  __construct(Model_ORM $model) {
      $this->model = $model;
   }

   public function create() {
      $dbc = Db_PDO::getInstance();

     $parts = array();
     $indexes = $fulltexts = array();
     foreach ($this->model->getColumns() as $column => $params) {
        $str = null;
//         `id_article` smallint(5) unsigned NOT NULL AUTO_INCREMENT

        if($params['lang']){
           foreach ($this->langsColations as $lang => $colation) {
              array_push($parts, $this->createColumnString($column."_".$lang, $params, $colation));
              if($params['index'] == true){
                 if(is_bool($params['index'])){
                    $indexes[] = 'KEY `index_'.$column."_".$lang.'` (`'.$column."_".$lang.'`)';
                 } else {
                    // tady sloupce na více indexů
                 }
              }   
              
              if($params['fulltext'] == true){
                 $fulltexts[] = 'FULLTEXT KEY `fulltext_'.$column."_".$lang.'` (`'.$column."_".$lang.'`)';
              }
           }
           
        } else {
           array_push($parts, $this->createColumnString($column, $params));
           
           if($params['pk'] == true){
              $indexes[] = 'PRIMARY KEY (`'.$column.'`)';
           }
           if($params['index'] == true){
              if(is_bool($params['index'])){
                 $indexes[] = 'KEY `index_'.$column.'` (`'.$column.'`)';
              } else if(is_array($params['index'])) {
                 // tady sloupce na více indexů
                 $i = array();
                 foreach ($params['index'] as $indexColumn) {
                    $i[] = '`'.$indexColumn.'`';
                 }
                 $indexes[] = 'KEY `index_'.$column.'` ('.implode(',', $i).')';
              } else {
                 $indexes[] = 'KEY `index_'.$column.'` ('.$params['index'].')';
              }
           }
           // fulltext   
           if($params['fulltext'] == true){
              $fulltexts[] = 'FULLTEXT KEY `fulltext_'.$column.'` (`'.$column.'`)';
           }   
        }
        
     }
     
     $colsStr = implode(",\n", array_merge($parts, $indexes, $fulltexts ) ); 
     
     $sql = 'CREATE TABLE IF NOT EXISTS `'.$this->model->getTableName()."`\n"
     ." (".$colsStr.")\n"
     ." ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
     return $dbc->exec($sql);
   }
   
   /**
    * Vytvoří řetězec pro vytvoření sloupce
    * @param string $name -- název
    * @param string $params -- parametry
    * @param string $colation -- kolace
    */   
   private function createColumnString($name ,$params, $colation = 'utf8_general_ci')
   {
      $pdo = Db_PDO::getInstance();
   
      $str = '`'.$name.'` '.$params['datatype'];
      // colation
      if(preg_match("/(varchar)|(text)/", $params['datatype']) ){
         $str .= ' CHARACTER SET utf8 COLLATE '.$colation;
      }
      //       if(preg_match("/(date)|(text)/", $params['datatype']) ){
      //          $str .= ' CHARACTER SET utf8 COLLATE '.$colation;
      //       }
      // not null
      if($params['nn']){
         $str .= ' NOT NULL';
      }
      // AI
      if($params['ai']){
         $str .= ' AUTO_INCREMENT';
      }
   
      if(!$params['nn'] && isset($params['default']) && $params['default'] === null){
         $str .= ' DEFAULT NULL';
      } else if(isset($params['default']) && $params['default'] !== null &&
            ( $params['datatype'] == 'timestamp' ) ){
         $str .= ' DEFAULT '.$params['default'];
      } else if(isset($params['default']) && $params['default'] !== null){
         $str .= ' DEFAULT '.$pdo->quote($params['default'], $params['pdoparam']);
      }
      return $str;
   }

   private function parseDataType($dataType) {
      $matches = array();
      preg_match('/^(?P<datatype>[a-z]+)(?:\((?P<size>[0-9]+)\))?[ ]+(?P<unsigned>unsigned)$/i', $dataType, $matches);
      return $matches;
   }

   public function removeColumn($name) 
   {
      ;
   }
    
   public function addColumn($name, $params = array()) 
   {
      ;
   }
    
   public function alterColumn($name, $params = array()) 
   {
      ;
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