<?php
/**
 * Abstraktní třída pro Db Model typu PDO.
 * Tříta pro vytvoření modelu, přistupujícího k databázi. Umožňuje základní práce
 * s databází.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: dbmodel.class.php 615 2009-06-09 13:05:12Z jakub $ VVE3.9.2 $Revision: 615 $
 * @author			$Author: jakub $ $Date: 2009-06-09 15:05:12 +0200 (Út, 09 čen 2009) $
 *						$LastChangedBy: jakub $ $LastChangedDate: 2009-06-09 15:05:12 +0200 (Út, 09 čen 2009) $
 * @abstract 		Abstraktní třída pro vytvoření modelu pro práci s databází
 */

class Model_ORM extends Model_PDO {
   const DB_TABLE = null;

   const ORDER_ASC = 'ASC';
   const ORDER_DESC = 'DESC';

   const JOIN_LEFT = 1;
   const JOIN_RIGHT = 2;
   const JOIN_OUTER = 3;
   const JOIN_CROSS = 4;

   protected static $tableName = null;

   protected static $tableShortName = null;

   protected static $dbName =  VVE_DB_NAME;

   protected static $dbEngine =  'MyISAM';

   private $tableStructure = array();

   private $pKey = null;

   private $foreignKeys = array();

   private $getAllLangs = true;

   private static $defaultColumnParams = array(
      'datatype' => 'VARCHAR(45)', // typ sloupce
      'pdoparam' => PDO::PARAM_STR, // typ sloupce (PDO::PARAM_)
      'nn' => false, // non null
      'default' => null, // default value
      'aliasFor' => null, // alias sloupce
      'ai' => false,
      'uq' => false,
      'pk' => false,
      'lang' => false,
      'comment' => null,
      'characterset' => 'utf8',
      'collate' => 'utf8_general_ci',
      'lenght' => null,
      'readonly' => false,
      'value' => false,
      'valueLoaded' => false,
      'name' => null,
      'extern' => false, // externí sloupec
      'changed' => 0, // INTERNAL -- check if column is changed
      'fulltext' => false // FULLTEXT na sloupci
   );

   protected $limit = array('from' => null, 'rows' => null);

   protected $orders = array();

   protected $where = null;
   protected $whereBindValues = array();

   protected $joins = array();
   private $joinString = null;


   protected $relations = array();

   protected $selectedColumns = array();

   public function  __construct() {
      parent::__construct();
      $this->_initTable();
   }

   /**
    * Metody pro nasatvení modelu
    */

   protected function _initTable() {
      if(self::$tableName == null){
         self::$tableName = self::DB_TABLE;
      }
      $this->setTableName(self::$tableName);
   }

   /**
    * Přidání sloupce
    * @param string $name -- název sloupce
    * @param array $params -- parametry sloupce
    *
    * <br/>
    * <p><b>datatype</b> - string - datový typ (varchar(15))</p>
    * <p><b>pdoparam</b> - datový typ (PDO::PARAM_) -- bude se dynamicky detekovat</p>
    * <p><b>nn</b> - bool - jest může být sloupce nulový</p>
    * <p><b>default</b> - string - výchozí hodnota</p>
    * <p><b>alias</b> - string - alias sloupce</p>
    * <p><b>uq</b> - bool - jest může být sloupce unikátní</p>
    * <p><b>pk</b> - bool - jest je sloupce primary key</p>
    * <p><b>lang</b> - bool - jest je jazykový (jsou pak vytvářeny nové jazykové sloupce)</p>
    * <p><b>commne</b> - string - komentář</p>
    * <p><b>readonly</b> - bool - jestli je sloupce pouze pro čtení</p>
    * <p><b>value</b> - mixed - hodnota sloupce (interní)</p>
    */
   protected function addColumn($name, $params = array()) {
      if (!isset($params['pdoparam'])) {
         // detekce typu sloupce
         $typeLen = 0;
         $typePdo = PDO::PARAM_STR;
         switch (strtolower($params['datatype'])) {
            case 'int':
            case 'smallint':
            case 'bigint':
            case 'tinyint':
               $params['pdoparam'] = PDO::PARAM_INT;
               break;
            case 'float':
            case 'double':
            case 'time':
            case 'date':
            case 'datetime':
            case 'text':
            case 'varchar':
            case 'longtext':
            case 'mediumtext':
            case 'char':
            case 'blob':
            case 'longblob':
            case 'mediumblob':
            case 'tinyblob':
            case 'binary':
            case 'varbinary':
               $params['pdoparam'] = PDO::PARAM_STR;
               break;
            case 'bool':
               $params['pdoparam'] = PDO::PARAM_BOOL;
               break;
            case 'null':
               $params['pdoparam'] = PDO::PARAM_NULL;
               break;
            case 'timestamp':
               $params['pdoparam'] = PDO::PARAM_STMT;
               break;
            default:
               throw new UnexpectedValueException(sprintf(_('Nepodporovaný datový typ sloupce "%s"'), $name));
               break;
         }
      }
      $params['name'] = $name;
      $defParams = self::$defaultColumnParams;
      unset ($defParams['default']); // unset becouse default is no need't when is not set
      $this->tableStructure[$name] = array_merge($defParams, $params);
   }

   /**
    * Metodav rací pole s výchozími parametry sloupce
    * @return array
    */
   public static function getDefaultColumnParams() {
      return self::$defaultColumnParams;
   }

   /**
    * Metoda přidá napojení na jinou tabulku přes klíč
    * @param string $tbName - název tabulky (pro identifikaci klíče)
    * @param string $column - název sloupce
    * @param string $modelName - název modelu připojené tabulky
    * @param string $externColumn - název sloupce připojené tabulky
    */
   protected function addForeignKey($tbName, $column, $modelName, $externColumn = null) {
      $this->foreignKeys[$tbName] = array('column' => $column, 'modelName' => $modelName, 'modelColumn' => $externColumn, 'columns' => null);
   }

   /**
    * Přidá relaci 1:N na jiný model
    * @param string $column -- název sloupce v tomto modelu
    * @param string $modelName -- název připojeného modelu
    * @param string $modelColumn -- název sloupce v připojeném modelu
    */
   protected function addRelatioOneToMany($column, $modelName, $modelColumn) {
      array_push($this->relations, array('type' => 'oneToMany','column' => $column, 'model' => $modelName, 'modelColumn' => $modelColumn));
   }

   /**
    * Přidá relaci N:N na jiný model
    * @param string $column -- název sloupce v tomto modelu
    * @param string $modelName -- název připojeného modelu
    * @param string $modelColumn -- název sloupce v připojeném modelu
    * @todo NEFUNGUJE !!!
    */
   protected function addRelatioManyToMany($column, $modelName, $modelColumn) {
      array_push($this->relations, array('type' => 'manyToMany','column' => $column, 'model' => $modelName, 'modelColumn' => $modelColumn));
   }

   /**
    * Přidá relaci 1:1 na jiný model
    * @param string $column -- název sloupce v tomto modelu
    * @param string $modelName -- název připojeného modelu
    * @param string $modelColumn -- název sloupce v připojeném modelu
    * @todo NEFUNGUJE !!!
    */
   protected function addRelatioOneToOne($column, $modelName, $modelColumn) {
      array_push($this->relations, array('type' => 'oneToOne','column' => $column, 'model' => $modelName, 'modelColumn' => $modelColumn));
   }

   /**
    * nasatví primary key
    * @param string $collname -- název sloupce
    */
   protected function setPk($collname) {
      $this->pKey = $collname;
      $this->tableStructure[$collname]['pk'] = true;
   }

   /**
    * Metoda nastavuje název tabulky
    * @param string $tablename -- název tabulky bez prefixu
    */
   protected function setTableName($tablename, $short = null, $addPrefix = true) {
      if($addPrefix == true){
         self::$tableName = Db_PDO::table($tablename);
      } else {
         self::$tableName = $tablename;
      }
      if($short == null){
         $short = 't_';
         $parts = explode('_', $tablename);
         foreach ($parts as $part) {
            $short .= $part[0];
         }
      }
      self::$tableShortName = $short;
   }

   /**
    * Metoda nastaví název databáze
    * @param string $dbName
    */
   protected function setDbName($dbName) {
      self::$dbName = $dbName;
   }

   /**
    * Metoda vrací název databáze
    * @return string
    */
   protected function getDbName() {
      return self::$dbName;
   }

   /**
    * Metoda nastaví engine tabulky (MyISAM, InnoDB)
    * @param string $dbEngine
    */
   public function setDbEngine($dbEngine) {
      self::$dbEngine = $dbEngine;
   }

   /**
    * Metoda vrací název databáze (MyISAM, InnoDB)
    * @return string
    */
   public function getDbEngine() {
      return self::$dbEngine;
   }

   /*
    * Metody pro vrácení informací o modelu
    */

   /**
    * Metoda vrací název tabulky i s prefixem
    * @return string
    */
   public function getTableName() {
      return self::$tableName;
   }

   /**
    * Metoda vrací zkrácený název tabulky i s prefixem
    * @return string
    */
   public function getTableShortName() {
      return self::$tableShortName;
   }

   /**
    * Metoda vrací všechny sloupce v tabulce i s nastavením
    * @return array
    */
   public function getColumns() {
      return $this->tableStructure;
   }

   /**
    * Metoda vrací jestli se vybírají všechny jazyky aplikace nebo jenom aktuální
    * @return bool
    */
   public function isSelectAllLangs() {
      return $this->getAllLangs;
   }

   /**
    * Metoda nasatvuje, které jazyky se mají vybírat, jestli všechny, nebo jen aktuální jazyk
    * @param bool $all -- true pro všechny
    */
   public function setSelectAllLangs($all = true) {
      $this->getAllLangs = $all;
   }

   /*
    * Metody pro práci s dotazem
    */

   /**
    * Metoda vrací záznam
    * @param string/int/array $pk -- (option) primární klíč
    * @param string/int $value -- (option) hodnota
    * @param array $order -- (option) řazení
    * @return Model_ORM_Record
    *
    * Možné zadání:<br />
    * record() -- nový zýznam
    * record(1) -- záznam s pk = 1
    * record(array(column => value, column => value)); -- záznamy podle zadaných parametru
    * record(column, value); -- zázhnam podle zadaného sloupce
    */
   public function record($pk = null, $value = null) {
      if($pk == null AND $this->where == null AND empty ($this->orders)){ // pokud nejsou žádné podmínky je vytvořen nový
         return new Model_ORM_Record($this->tableStructure);
      }

      $obj = clone $this;
      $obj = $obj->limit(0, 1);
      if($pk != null AND $value == null){ // je zadána hodnota pk
         $obj = $obj->where($pk);
      } else if($pk != null AND $value != null) { // je zadán sloupce a jeho jeho hodnota
         $obj = $obj->where($pk, $value);
      }
      return $this->getRow($obj);
   }

   public function newRecord() {
      return new Model_ORM_Record($this->tableStructure);
   }

   protected function getRow(Model_ORM $obj = null) {
      if($obj == null) $obj = $this;

      $dbc = new Db_PDO();
      $sql = 'SELECT '. $obj->createSQLSelectColumns().' FROM `'.$this->getDbName().'`.`'.$obj->getTableName().'` AS '.$this->getTableShortName();
      $obj->createSQLJoins($sql);
      $obj->createSQLWhere($sql, $this->getTableShortName());
      $obj->createSQLOrder($sql);
      $obj->createSQLLimi($sql);
      $dbst = $dbc->prepare($sql);
      $obj->bindSQLWhere($dbst);
      $obj->bindSQLLimit($dbst);

      $dbst->execute();
      $dbst->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Model_ORM_Record', array($obj->tableStructure, true));
      return $dbst->fetch(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE);
   }
   
   /**
    * Metoda vrací záznamy z db
    * @param int $fromRow -- od záznamu
    * @param int $rows -- počet záznamů
    * @param string/array $conds -- podmínky
    * @param array $orders -- řazení
    * @return array of Model_ORM_Record
    */
   public function records() {
      $dbc = new Db_PDO();
      $sql = 'SELECT '. $this->createSQLSelectColumns().' FROM `'.$this->getDbName().'`.`'.$this->getTableName().'` AS '.$this->getTableShortName();

      $this->createSQLJoins($sql);
      $this->createSQLWhere($sql, $this->getTableShortName());// where
      $this->createSQLOrder($sql);// order
      $this->createSQLLimi($sql);// limit
      $dbst = $dbc->prepare($sql);
      $this->bindSQLWhere($dbst);// where values
      $this->bindSQLLimit($dbst);// limit values

      $dbst->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Model_ORM_Record', array($this->tableStructure, true));
      $dbst->execute();
      $r = $dbst->fetchAll(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'Model_ORM_Record', array($this->tableStructure, true));
      return $r;
   }

   public function count() {
      $dbc = new Db_PDO();
      $sql = 'SELECT COUNT(*) AS cnt FROM `'.$this->getDbName().'`.`'.$this->getTableName().'` AS '.$this->getTableShortName();
      $this->createSQLJoins($sql);
      $this->createSQLWhere($sql, $this->getTableShortName());
      $dbst = $dbc->prepare($sql);
      $this->bindSQLWhere($dbst);
      $dbst->execute();
      $r = $dbst->fetchObject();
      if($r == false) return 0;
      return (int)$r->cnt;
   }

   public function save(Model_ORM_Record $record) {
      $dbc = new Db_PDO();
      $returnPk = $record->getPK();
      if(!$record->isNew()){
         // UPDATE
         $sql = 'UPDATE '.$this->getTableName();

         $colsStr = array();
         // create query
         foreach ($record->getColumns() as $colname => $params) {
//            if((is_object($params['value']) AND ($params['value'] instanceof DateTime) == false )
//               OR (is_array($params['value'] AND $params['lang'] == false))
//               OR $params['extern'] == true OR $params['value'] == $params['valueLoaded']) continue;
            if($params['extern'] == true OR $params['changed'] != 1) continue;
            if(!isset ($params['lang'])) $params['lang'] = false;
            if($params['lang'] === true){
               foreach (Locales::getAppLangs() as $lang) {
                  if($params['aliasFor'] === null){
                     array_push($colsStr, '`'.$colname.'_'.$lang.'` = :'.$colname.'_'.$lang.'');
                  } else {
                     array_push($colsStr, '`'.$params['aliasFor'].'_'.$lang.'` = :'.$colname.'_'.$lang.'');
                  }
               }
            } else {
               if($params['aliasFor'] === null){
                  array_push($colsStr, '`'.$colname.'` = :'.$colname.'');
               } else {
                  array_push($colsStr, '`'.$params['aliasFor'].'` = :'.$colname.'');
               }
            }
         }
         if(empty ($colsStr)) return $returnPk; // žádné změny se neukládájí
         $sqlStr = $sql.' SET '.  implode(',', $colsStr).' WHERE `'.$this->pKey.'` = :pkey';
         $dbst = $dbc->prepare($sqlStr);
         Log::msg($sqlStr, 'UPDATE', null, 'sql');
//      var_dump($sql.' SET '.  implode(',', $colsStr) .' WHERE `'.$this->pKey.'` = :pkey');flush();

         $dbst->bindValue(':pkey', $record->getPK(), $this->tableStructure[$this->pKey]['pdoparam']); // bind pk
         // bind values
         foreach ($record->getColumns() as $colname => $params) {
//            if((is_object($params['value']) AND ($params['value'] instanceof DateTime) == false )
//               OR (is_array($params['value'] AND $params['lang'] == false))
//               OR $params['extern'] == true OR $params['value'] == $params['valueLoaded']) continue;
            if($params['extern'] == true OR $params['changed'] != 1) continue;
            if(!isset ($params['lang'])) $params['lang'] = false;
            $value = $params['value'];
//            echo $colname;
//            var_dump($value);flush();
            if($params['lang'] == true){
               foreach (Locales::getAppLangs() as $lang) {
                  $dbst->bindValue(':'.$colname.'_'.$lang, $value[$lang], $params['pdoparam']);
               }
            } else {
               // date clumns
               if($value instanceof DateTime){
                  $value = $this->createDateTimeStr($value, $params['datatype']);
               }
               $dbst->bindValue(':'.$colname, $value, $params['pdoparam']);
            }
         }
         $dbst->execute();
         $returnPk = $record->getPK();
         
      } else {
         // INSERT
         $sql = "INSERT INTO ". $this->getTableName();
         $colsStr = array(); $bindParamStr = array();
         // create query
         foreach ($record->getColumns() as $colname => $params) {
            if(!isset ($params['lang'])) $params['lang'] = false;
//            if($params['extern'] == true OR $params['pk'] == true
//               OR (is_object($params['value']) AND ($params['value'] instanceof DateTime) == false )
//               OR (is_array($params['value'] AND $params['lang'] == false))
////               OR $params['value'] == $params['valueLoaded']
//            ) continue;
            if($params['extern'] == true OR $params['changed'] != 1) continue;
            if($params['lang'] === true){
               foreach (Locales::getAppLangs() as $lang) {
                  if($params['aliasFor'] === null){
                     array_push($colsStr, '`'.$colname.'_'.$lang.'`');
                  } else {
                     array_push($colsStr, '`'.$params['aliasFor'].'_'.$lang.'`');
                  }
                  array_push($bindParamStr, ':'.$colname.'_'.$lang);
               }
            } else {
               if($params['aliasFor'] === null){
                  array_push($colsStr, '`'.$colname.'`');
               } else {
                  array_push($colsStr, '`'.$params['aliasFor'].'`');
               }
               array_push($bindParamStr, ':'.$colname);
            }
         }
         $sqlStr = $sql.' ('.  implode(',', $colsStr).') VALUES ('.  implode(',', $bindParamStr).')';
         $dbst = $dbc->prepare($sqlStr);
         Log::msg($sqlStr, 'INSERT', null, 'sql');
         // bind values
         foreach ($record->getColumns() as $colname => $params) {
            if(!isset ($params['lang'])) $params['lang'] = false;
//            if($params['extern'] == true OR $params['pk'] == true
//               OR (is_object($params['value']) AND ($params['value'] instanceof DateTime) == false )
//               OR (is_array($params['value'] AND $params['lang'] == false))
////               OR $params['value'] == $params['valueLoaded']
//               ) continue;
            if($params['extern'] == true OR $params['changed'] != 1) continue;
            if(isset($params['default']) AND $params['value'] === false AND $params['changed'] != 1){
               switch ((string)$params['default']) {
                  case 'CURRENT_TIMESTAMP':
                     $params['value'] = new DateTime();
                     break;
                  default:
                     $params['value'] = $params['default'];
                     break;
               }
            }
            $value = $params['value'];
            if($params['lang'] == true){
               foreach (Locales::getAppLangs() as $lang) {
                  $dbst->bindValue(':'.$colname.'_'.$lang, $value[$lang], $params['pdoparam']);
               }
            } else {
               // date clumns
               if($value instanceof DateTime){
                  if($value instanceof DateTime){
                     $value = $this->createDateTimeStr($value, $params['datatype']);
                  }
               }
               $dbst->bindValue(':'.$colname, $value, $params['pdoparam']);
            }
         }
         $dbst->execute();
         $returnPk = $dbc->lastInsertId();
      }

      return $returnPk;
   }

   /**
    * Metoda vymaže záznam z db
    * @param mixed $pk<p>
    * pKey - hodnota primárního klíče</p>
    * <p>Model_ORM_Record - objekt záznamu</p>
    * @return int -- počet smazaných záznamů
    */
   public function delete($pk = null) {
      $dbc = new Db_PDO();
      $sql = 'DELETE FROM '.$this->getTableName();
      if($pk instanceof Model_ORM_Record){
         $this->where($this->pKey, $pk->getPK());
         // asi vytáhnout pk (bude muset být uloženo v rekordu)
         $pkValue = $pk->getPK();
      } else if($pk != null) {
         $this->where($this->pKey, $pk);
         $pkValue = $pk;
      } else if(isset ($this->whereBindValues['col'])) {
         $pkValue = $this->whereBindValues['col'];
      }
      if($pk == null AND $this->where == null){return 0;} // pokud není podmínka nemažeme, na smazání kompletní tabulky bude flush()

      $this->createSQLWhere($sql);
      $dbst = $dbc->prepare($sql);
      Log::msg($sql, 'DELETE', null, 'sql');
      $this->bindSQLWhere($dbst);
      $ret = $dbst->execute();
      if(isset ($pkValue)){ // if $pkey is not defined
         $this->deleteRelations($pkValue); // vymazat přidružení
      }
      return $dbst->rowCount();
   }

   /*
    * Metody pro úrpoavu a vytváření modelu
    */

   public function createTable() {
//      $dbc = new Db_PDO();
//
//      $clomunsDef = array();
//      foreach ($this->tableStructure as $column => $params) {
//         //`id_article` smallint(5) unsigned NOT NULL AUTO_INCREMENT
//         $str = '`'.$column.'` '.$params['datatype'];
//
//
//         array_push($clomunsDef, $str);
//      }
//
//      $sql = 'CREATE TABLE IF NOT EXISTS `'.$this->getTableName().'` ()';


   }

   /*
    * Metody pro úpravu parametrů modelu
    */

   /**
    * Metoda přidává limit
    * @param int $fromRow -- od řádku
    * @param int $rows -- řádků
    * @return Model_ORM
    */
   public function limit($fromRow, $rows) {
      $this->limit['from'] = $fromRow;
      $this->limit['rows'] = $rows;
      return $this;
   }

   /**
    * Metoda přidá řazení
    * @param array $arrayOrders -- pole se sloupcy pro řazení
    * <p>Použití: 'sloupce' => 'ASC/DESC' nebo jenom název sloupce<br />
    * Pokud je vloženo se zkratkou tabulky tak nejsou doplněny spec. db uvozovky
    * </p>
    * @return Model_ORM
    */
   public function order($arrayOrders) {
      $this->orders = $arrayOrders;
      return $this;
   }

   /**
    * Metoda přidá podmínky do dotazu
    * @param array $condArray -- pole s podmínkami
    * @param array $bindValues -- pole s předanými hodnotami - reference
    * @return Model_ORM
    */
   public function where($cond = null, $bindValues = null, $append = false) {
      // vymaz
      if($cond === null) {
         $this->where = null;
         $this->whereBindValues = array();
      } else if($bindValues === null){ // pokud je jenom hodnota bere se primary key
         $bindValues = array('col' => $cond);
         $cond = $this->pKey.' = :col';
      } else if($bindValues != null AND !is_array($bindValues)){
         $cond = $cond.' = :col';
         $bindValues = array('col' => $bindValues);
      }
      if($append == false){
         $this->where = $cond;
         $this->whereBindValues = $bindValues;
      } else {
         $this->where = $this->where.' '.$cond;
         $this->whereBindValues = array_merge($this->whereBindValues, $bindValues);
      }
      return $this;
   }

   public function join($tbName, $columns = null, $joinType = self::JOIN_LEFT) {
      $this->joins[$tbName] = array('type' => $joinType, 'columns' => $columns);
      return $this;
   }

   /**
    * Metoda nastaví, které sloupce se budou vybírat
    * @param array $columnsArr -- pole se sloupci
    * @return Model_ORM -- sám sebe
    */
   public function columns($columnsArr) {
      $this->selectedColumns = $columnsArr;
      return $this;
   }

   /**
    * Metoda provede výmaz hodnot s přidružených tabulek
    */
   protected function deleteRelations($pkVal) {
      if(!empty ($this->relations)){
         foreach ($this->relations as $rel) {
            $model = new $rel['model']();
            if($model instanceof Model_ORM){
               // výmaz z relation table
               $model->where($rel['modelColumn'], $pkVal)->delete();
            }
         }
      }
   }


   /* vytváření sql částí */
   /**
    * Metoda vytvoří část sql se sloupcy pro select
    */
   protected function createSQLSelectColumns() {
      $columns = array();
      if(empty ($this->selectedColumns)){ // není vybrán žádný sloupec
         foreach ($this->tableStructure as $columnName => $params) {
            if($params['lang'] == false){// není jazyk
               if($params['aliasFor'] == null){
                  array_push($columns, '`'.$this->getTableShortName().'`.`'.$columnName.'`');
               } else {
                  array_push($columns, '`'.$this->getTableShortName().'`.`'.$params['aliasFor'].'` AS '.$columnName);
               }
            } else if($this->getAllLangs == true) { // více jazyčné sloupce
               foreach (Locales::getAppLangs() as $key => $value) {
                  array_push($columns, '`'.$this->getTableShortName().'`.`'.$columnName.'_'.$value.'`');
               }
            } else { // pouze aktuální jazykový sloupec
               array_push($columns, '`'.$this->getTableShortName().'`.`'.$columnName.'_'.Locales::getLang().'` AS '.$columnName);
            }
         }
      } else { // sloupce jsou vybrány
         foreach ($this->selectedColumns as $alias => $columnName) {
            if(!isset ($this->tableStructure[$columnName])){ // není colum z této tabulky nebo se jedná o funkci
               if(is_int($alias)){
                  array_push($columns, ''.$columnName.'');
               } else {
                  array_push($columns, ''.$columnName.' AS '.$alias);
               }
            } else if($this->tableStructure[$colName]['lang'] == false){// není jazyk
               if($this->tableStructure[$colName]['aliasFor'] == null){
                  array_push($columns, '`'.$this->getTableShortName().'`.`'.$columnName.'`');
               } else {
                  array_push($columns, '`'.$this->getTableShortName().'`.`'.$this->tableStructure[$colName]['aliasFor'].'` AS '.$columnName);
               }
            } else if($this->getAllLangs == true) { // více jazyčné sloupce
               foreach (Locales::getAppLangs() as $key => $value) {
                  array_push($columns, '`'.$this->getTableShortName().'`.`'.$columnName.'_'.$value.'`');
               }
            } else { // pouze aktuální jazykový sloupec
               array_push($columns, '`'.$this->getTableShortName().'`.`'.$columnName.'_'.Locales::getLang().'` AS '.$columnName);
            }
         }
      }
      $columns = $this->createSQLSelectJoinColumns($columns);
      $tableCols = implode(',', $columns);
      return $tableCols;
   }

   protected function createSQLSelectJoinColumns($columns) {
      $this->joinString = null; //reset
      if(!empty ($this->joins) AND !empty ($this->foreignKeys)){
         foreach ($this->joins as $tbName => $join) {
            $model = new $this->foreignKeys[$tbName]['modelName']();
            $tableName = uniqid($model->getTableShortName());
            if(($model instanceof Model_ORM) == false) throw new Exception('unexpectec value of model');
            // vytváření SQL pro joiny je tu kvůli kešování
            $part = null;
            switch ($join['type']) {
               case self::JOIN_CROSS:
                  $part .= ' CROSS ';
                  break;
               case self::JOIN_RIGHT:
                  $part .= ' RIGHT ';
                  break;
               case self::JOIN_LEFT:
               default:
                  $part .= ' LEFT ';
                  break;
            }
            $part .= 'JOIN '.$model->getTableName().' AS '.$tableName;
            if($this->foreignKeys[$tbName]['modelColumn'] == null OR $this->foreignKeys[$tbName]['modelColumn'] == $this->foreignKeys[$tbName]['column']){ // USING
               $part .= ' ON (`'.$tableName.'`.`'.$this->foreignKeys[$tbName]['column'].'` = `'.$this->getTableShortName().'`.`'.$this->foreignKeys[$tbName]['column'].'`)';
//               $part .= ' USING (`'.$this->foreignKeys[$tbName]['column'].'`)';
            } else {
               $part .= ' ON (`'.$tableName.'`.`'.$this->foreignKeys[$tbName]['modelColumn'].'` = `'.$this->getTableShortName().'`.`'.$this->foreignKeys[$tbName]['column'].'`)';
            }
            $this->joinString .= $part; // uložení do joinstring

            // samotné vytvoření sloupců
            $modelCols = $model->getColumns();
            if(!empty($this->joins[$tbName]['columns'])){ // jen vybrané sloupce
               foreach ($this->joins[$tbName]['columns'] as $alias => $coll) {
                  if(!is_int($alias)){ // is alias
                     array_push($columns, '`'.$tableName.'`.`'.$coll.'` AS '.$alias);
                  } else if($modelCols[$coll]['aliasFor'] != null) { // is alias from model
                     array_push($columns, '`'.$tableName.'`.`'.$modelCols[$coll]['aliasFor'].'` AS '.$coll);
                  } else {
                     array_push($columns, '`'.$tableName.'`.`'.$coll.'`');
                  }
               }
            } else { // všechny sloupce z tabulky kromě pk
               foreach ($modelCols as $name => $params) {
                  if($params['pk'] == true) continue;
                  if($params['aliasFor'] == null){
                     array_push($columns, '`'.$tableName.'`.`'.$name.'`');
                  } else {
                     array_push($columns, '`'.$tableName.'`.`'.$params['aliasFor'].'` AS '.$name);
                  }
               }
            }
            unset ($model);
         }
      }
      return $columns;
   }


   /**
    * Metoda vytvoří část sql s LIMIT
    * @param string $sql
    */
   protected function createSQLLimi(&$sql) {
      if($this->limit['from'] !== null){
         $sql .= ' LIMIT :fromRow, :rows';
      }
   }

   /**
    * Metoda vytvoří část sql s ORDER
    * @param string $sql
    */
   protected function createSQLOrder(&$sql) {
      if(!empty ($this->orders)){
         $ords = array();
         foreach ($this->orders as $col => $val) {
            if(!is_int($col) AND strtoupper($val) != 'ASC' AND strtoupper($val) != 'DESC') throw new UnexpectedValueException (sprintf (_('Nepodporovaný typ řazení "%s"'), $val));
            // kontrola sloupce jestli existuje
            // pokud obsahuje tečku jedná se o zápis s prefixem tabulky a ten se vkládá přímo
            if(is_int($col)){// pokud je jenom sloupce je ASC
               $col = $val;
               $val = 'ASC';
            }
            if(strpos($val,'.') === false){ // pokud není předána zkratka s tabulkou
               if(isset ($this->tableStructure[$col])){
                  $colName = $col;
                  if($this->tableStructure[$col]['lang'] == true){
                     $colName = $col.'_'.Locales::getLang();
                  }
                  if($this->tableStructure[$col]['aliasFor'] != null){
                     array_push($ords, '`'.$this->getTableShortName().'`.`'.$this->tableStructure[$col]['aliasFor'].'` '.strtoupper($val));
                  } else {
                     array_push($ords, '`'.$this->getTableShortName().'`.`'.$colName.'` '.strtoupper($val));
                  }
               } else {
                  array_push($ords, '`'.$col.'` '.strtoupper($val));
               }
            } else {
               array_push($ords, preg_replace('/([a-z_-]+)\.([a-z_-]+)/i', '`\1`.`\2`', $col).' '.strtoupper($val));
            }
         }
         $sql .= ' ORDER BY '.implode(',', $ords);
      }
   }

   /**
    * Metoda vytvoří část sql s WHERE
    * @param <type> $sql
    */
   protected function createSQLWhere(&$sql, $tbShorName = null){
      if($this->where != null){
         $retWhere = ' WHERE (';
         $parts = explode(' ', $this->where);
         foreach ($parts as $coll) {
            /**
             * @todo optimalizace na zbytečné znaky
             */

            // přetypování (použití např. u jazyků či změnu hodnoty)
            $retype = null;
            if(strpos($coll, '(') == 0){ // musí být na začátku před řetězcem
               $retype = array();
               preg_match('/^\(([a-z]+)\)(.*)$/i', $coll,$retype);
               if(isset ($retype[1])){
                  $coll = $retype[2];
                  $retype = $retype[1];
               }
            }
            if(isset ($this->tableStructure[$coll])){
               if($this->tableStructure[$coll]['lang'] == true AND $retype == null){
                  $coll = $coll.'_'.Locales::getLang();
               } else if($this->tableStructure[$coll]['lang'] == true AND Locales::isLang($retype)) {
                  $coll = $coll.'_'.$retype;
               }
               // pokud je prefix tabulky
               if($tbShorName != null){
                  $retWhere .= '`'.$tbShorName.'`.`'.$coll.'` ';
               } else {
                  $retWhere .= '`'.$coll.'` ';
               }
            } else if(strpos($coll, '.')) {
            // doplnění uvozovek u cizích sloupců
               $retWhere .= preg_replace('/([a-z_-]+)\.([a-z_-]+)/i', '`\1`.`\2`', $coll);
            } else {
               $retWhere .= $coll.' ';
            }
         }
         $retWhere .= ')';
         $sql .= $retWhere;
      }
   }

   protected function createSQLJoins(&$sql) {
      if($this->joinString == null) $this->createSQLSelectJoinColumns(array());// provede vytvoření sloupců
      $sql .= $this->joinString; // je vytvořen v přípravě sloupců
   }


   /* vložení hodnot so sql částí */
   /**
    * Metoda doplní hodnoty do limit clause
    * @param PDOStatement $stmt
    */
   protected function bindSQLLimit(PDOStatement &$stmt) {
      if($this->limit['from'] !== null){
         $stmt->bindValue(':fromRow', (int)$this->limit['from'], PDO::PARAM_INT);
         $stmt->bindValue(':rows', (int)$this->limit['rows'], PDO::PARAM_INT);
      }
   }

   /**
    * Metoda doplní hodnoty do where clause
    * @param PDOStatement $stmt
    */
   protected function bindSQLWhere(PDOStatement &$stmt) {
      if($this->where != null){
         // bind values
         foreach ($this->whereBindValues as $name => $val) {
            switch (gettype($val)) {
               case 'boolean':
                  $pdoParam = PDO::PARAM_BOOL;
                  break;
               case 'integer':
                  $pdoParam = PDO::PARAM_INT;
                  break;
               case 'NULL':
                  $pdoParam = PDO::PARAM_NULL;
                  break;
               case 'double':
               case 'string':
                  $pdoParam = PDO::PARAM_STR;
                  break;
               default:
                  throw new UnexpectedValueException(sprintf(_('Nepovolená hodnota "%s" v předanám paramteru'),gettype($val)));
                  break;
            }
            if($stmt->bindParam(':'.$name, $this->whereBindValues[$name], $pdoParam) === false){ // nefunguje při některých where, ale proč??
               $stmt->bindValue(':'.$name, $val, $pdoParam);
            }
         }
      }
   }

   /**
    * Metoda vytvoří řetězec pro ščasový objekt k uložení do db
    * @param DateTime $dateTimeObj -- časový objekt
    * @param $datatype -- datový typ výstupu (date, time, datetime)
    * @return string -- vytvořený řetězec
    */
   protected function createDateTimeStr(DateTime $dateTimeObj, $datatype) {
      switch ($datatype) {
         case 'date':
            $value = $dateTimeObj->format('Y-m-d');
            break;
         case 'time':
            $value = $dateTimeObj->format('H:i:s');
            break;
         case 'datetime':
         default:
            $value = $dateTimeObj->format('Y-m-d H:i:s');
            break;
      }
      return $value;
   }

   /* Pomocné metody */

   private function clearModel() {
      $this->orders = array();
      $this->where = null;
      $this->whereBindValues = array();
      $this->limit(null, null);
   }
}
?>