<?php

/**
 * Třída ORM pro práci s databází a záznamy v ní.
 * třída poskytuje základní prvky pro práci s prvky v databázi. načítání, ukládání mazání dat.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: $ VVE 6.4.0 $Revision: $
 * @author			$Author: $ $Date: $
 * 						$LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro práci s modelem v databázi (ORM)
 */
abstract class Model_ORM extends Model implements ArrayAccess {
   const DB_TABLE = null;

   const ORDER_ASC = 'ASC';
   const ORDER_DESC = 'DESC';

   const JOIN_LEFT = 1;
   const JOIN_RIGHT = 2;
   const JOIN_OUTER = 3;
   const JOIN_CROSS = 4;
   const JOIN_INNER = 5;
   const JOIN = 6;

   const LOCK_WRITE = 'WRITE';
   const LOCK_READ = 'READ';

   /**
    * FETCH konstanty
    */
   const FETCH_LANG_CLASS = 16384;
   const FETCH_PKEY_AS_ARR_KEY = 32768;

   protected $tableName = null;
   protected $tableShortName = null;
   protected $dbName = null;
   protected $dbEngine = 'MyISAM';
   public $dbPrefix = null;
   private $tableStructure = array();
   private $pKey = null;
   private $foreignKeys = array();
   private $getAllLangs = true;
   private $assignLang = false;
   private static $defaultColumnParams = array(
      // parametry v db
      'datatype' => 'VARCHAR(45)', // typ sloupce
      'nn' => false, // non null
      'default' => null, // default value
      'aliasFor' => null, // alias sloupce
      'ai' => false,
      'uq' => false,
      'pk' => false,
      'index' => false,
      'comment' => null,
      'characterset' => 'utf8',
      'collate' => 'utf8_general_ci',
      'collateautodetect' => true,
      'lenght' => null,
      'readonly' => false,
      'name' => null,
      'fulltext' => false, // FULLTEXT na sloupci
      // ostatní parametry
      'extern' => false, // externí sloupec
      'changed' => 0, // INTERNAL -- check if column is changed: 0 - is new cell; -1 - cell is loaded from db; 1 - cell is updated in app;
      'pdoparam' => PDO::PARAM_STR, // typ sloupce (PDO::PARAM_)
      'lang' => false,
      'value' => false,
      'valueLoaded' => false,
      'fulltextRel' => 1, // relevance sloupce FULLTEXTu

      'validation' => false, // validační funkce
      'maxlen' => false, // maximální délka řetězce
   );
   protected $limit = array('from' => null, 'rows' => null);
   protected $orders = array();
   protected $groupby = array();
   protected $where = null;
   protected $whereBindValues = array();
   protected $having = array();
   protected $havingBindValues = array();
   protected $joins = array();
   private $joinString = null;
   protected $relations = array();
   protected $selectedColumns = array();
   /**
    * aktuální dotaz
    * @var string/PDOStatement
    */
   protected $currentSql = null;
   protected $bindValues = array();
   protected $fetchingDbst = false;

   /**
    * Název třídy s objektem záznamu
    * @var string
    */
   protected $rowClass = 'Model_ORM_Record';

   protected static $tablesLocked = array();

   protected $params = array();

   /**
    *
    * @param array $params - nepovinné parametry modelu, např datový adresář a podobně
    */
   public function __construct($params = array())
   {
      $this->dbPrefix = defined('CUBE_CMS_DB_PREFIX') ? CUBE_CMS_DB_PREFIX : VVE_DB_PREFIX;
      $this->dbName = defined('CUBE_CMS_DB_NAME') ? CUBE_CMS_DB_NAME : VVE_DB_NAME;
      parent::__construct();
      $this->selectedColumns = array();
      $this->orders = array();
      $this->joins = array();
      $this->joinString = null;
      $this->where = null;
      $this->whereBindValues = array();
      $this->limit(null, null);
      $this->groupby = array();
      $this->having = array();
      $this->havingBindValues = array();
      $this->bindValues = array();
      $this->fetchingDbst = false;

      $this->params = $params;

      $this->_initTable();
      if($this->rowClass == 'Model_ORM_Record' && class_exists(get_class($this).'_Record')){
         $this->rowClass = get_class($this).'_Record';
      }
   }

   /**
    * Metoda vytvoří nový objekt
    * @return static
    */
   public static function getInstance()
   {
      $ref = new ReflectionClass(get_called_class());
      return $ref->newInstanceArgs(func_get_args());
   }

   /**
    * Metody pro nasatvení modelu
    */
   protected function _initTable()
   {
      if ($this->tableName == null) {
         $this->tableName = self::DB_TABLE;
      }
      $this->setTableName($this->tableName);
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
   protected function addColumn($name, $params = array())
   {
      if (!isset($params['pdoparam'])) {
         // detekce typu sloupce
         $typeLen = 0;
         $typePdo = PDO::PARAM_STR;
         $paramType = $params['datatype'];
         if($pos = strpos($paramType,'(')){
            $paramType = substr($paramType, 0, $pos);
         }
         switch (strtolower($paramType)) {
            case 'int':
            case 'smallint':
            case 'bigint':
            case 'tinyint':
               $params['pdoparam'] = PDO::PARAM_INT;
               break;
            case 'float':
            case 'decimal':
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
               throw new UnexpectedValueException(sprintf($this->tr('Nepodporovaný datový typ %s sloupce "%s"'), $paramType, $name));
               break;
         }
      }
      $params['name'] = $name;
      $defParams = self::$defaultColumnParams;
      unset($defParams['default']); // unset becouse default is no need't when is not set
      $this->tableStructure[$name] = array_merge($defParams, $params);
   }

   /**
    * Metoda přidá napojení na jinou tabulku přes klíč
    * @param string $tbName - název tabulky (pro identifikaci klíče)
    * @param string $column - název sloupce
    * @param string $modelName - název modelu připojené tabulky
    * @param string $externColumn - název sloupce připojené tabulky
    */
   protected function addForeignKey($column, $modelName, $externColumn = null, $columnsPrefix = null)
   {
      // pokud má sloupce alias
      $columnReal = $column;
      if($this->tableStructure[$column]['aliasFor'] != null) $columnReal = $this->tableStructure[$column]['aliasFor'];
      $this->foreignKeys[$column] = array('column' => $columnReal, 'modelName' => $modelName, 'modelColumn' => $externColumn, 'prefix' => $columnsPrefix);
   }

   /**
    * Kontroluje jestli je daný klíč cizí
    * @param string $colName
    * @return bool
    */
   public function isForeignKey($colName)
   {
      return isset($this->foreignKeys[$colName]);
   }

   /**
    * Vrací nasatvení cizího klíče pokud existuje
    * @param string $colName
    * @return array
    */
   public function getForeignKey($colName)
   {
      return isset($this->foreignKeys[$colName]) ? $this->foreignKeys[$colName] : false;
   }

   /**
    * Přidá relaci 1:N na jiný model
    * @param string $column -- název sloupce v tomto modelu
    * @param string $modelName -- název připojeného modelu
    * @param string $modelColumn -- název sloupce v připojeném modelu
    */
   protected function addRelatioOneToMany($column, $modelName, $modelColumn)
   {
      array_push($this->relations, array('type' => 'oneToMany', 'column' => $column, 'model' => $modelName, 'modelColumn' => $modelColumn));
   }

   /**
    * Přidá relaci N:N na jiný model
    * @param string $column -- název sloupce v tomto modelu
    * @param string $modelName -- název připojeného modelu
    * @param string $modelColumn -- název sloupce v připojeném modelu
    * @todo NEFUNGUJE !!!
    */
   protected function addRelatioManyToMany($column, $modelName, $modelColumn)
   {
      array_push($this->relations, array('type' => 'manyToMany', 'column' => $column, 'model' => $modelName, 'modelColumn' => $modelColumn));
   }

   /**
    * Přidá relaci 1:1 na jiný model
    * @param string $column -- název sloupce v tomto modelu
    * @param string $modelName -- název připojeného modelu
    * @param string $modelColumn -- název sloupce v připojeném modelu
    * @todo NEFUNGUJE !!!
    */
   protected function addRelatioOneToOne($column, $modelName, $modelColumn)
   {
      array_push($this->relations, array('type' => 'oneToOne', 'column' => $column, 'model' => $modelName, 'modelColumn' => $modelColumn));
   }

   /**
    * nasatví primary key
    * @param string $collname -- název sloupce
    */
   protected function setPk($collname)
   {
      $this->pKey = $collname;
      $this->tableStructure[$collname]['pk'] = true;
   }

   /**
    * Metoda vrací název primárního klíče
    * @return string
    */
   public function getPkName()
   {
      return $this->pKey;
   }

   /**
    * Metoda nastavuje název tabulky
    * @param string $tablename -- název tabulky bez prefixu
    */
   protected function setTableName($tablename, $short = null, $addPrefix = true)
   {
      if ($addPrefix == true) {
         $this->tableName = Db_PDO::table($tablename);
      } else {
         $this->tableName = $tablename;
      }
      if ($short == null) {
         $short = 't_';
         $parts = explode('_', $tablename);
         foreach ($parts as $part) {
            $short .= $part[0];
         }
      }
      $this->tableShortName = $short;
   }

   /**
    * Metoda nastaví název databáze
    * @param string $dbName
    */
   protected function setDbName($dbName)
   {
      $this->dbName = $dbName;
   }

   /**
    * Metoda vrací název databáze
    * @return string
    */
   protected function getDbName()
   {
      return $this->dbName;
   }

   /**
    * Metoda nastaví engine tabulky (MyISAM, InnoDB)
    * @param string $dbEngine
    */
   public function setDbEngine($dbEngine)
   {
      $this->dbEngine = $dbEngine;
   }

   /**
    * Metoda vrací název databáze (MyISAM, InnoDB)
    * @return string
    */
   public function getDbEngine()
   {
      return $this->dbEngine;
   }

   /*
    * Metody pro vrácení informací o modelu
    */

   /**
    * Metoda vrací název tabulky i s prefixem
    * @return string
    */
   public function getTableName()
   {
      return $this->tableName;
   }

   /**
    * Metoda vrací zkrácený název tabulky i s prefixem
    * @return string
    */
   public function getTableShortName()
   {
      return $this->tableShortName;
   }

   /**
    * Metoda vrací pole s joiny
    * @return array
    */
   public function getJoins()
   {
      return $this->joins;
   }

   /**
    * Metoda vrací všechny sloupce v tabulce i s nastavením
    * @return array
    */
   public function getColumns()
   {
      return $this->tableStructure;
   }

   /**
    * Metoda vrací jestli se vybírají všechny jazyky aplikace nebo jenom aktuální
    * @return bool
    */
   public function isSelectAllLangs()
   {
      return $this->getAllLangs;
   }

   /**
    * Metoda nasatvuje, které jazyky se mají vybírat, jestli všechny, nebo jen aktuální jazyk
    * @param bool $all -- true pro všechny
    * @return this
    */
   public function setSelectAllLangs($all = true)
   {
      $this->getAllLangs = $all;
      return $this;
   }

   /*
    * Metody pro práci s dotazem
    */

   /**
    * Metoda vrací záznam
    * @param string/int/array $pk -- (option) primární klíč
    * @param string/int $value -- (option) hodnota
    * @return Model_ORM_Record|false
    *
    * Možné zadání:<br />
    * record() -- nový zýznam
    * record(1) -- záznam s pk = 1
    * record(column, value); -- zázhnam podle zadaného sloupce
    */
   public function record($pk = null, $value = null, $fetchParams = self::FETCH_LANG_CLASS)
   {
      if ($pk == null AND $this->where == null AND empty($this->orders) AND empty($this->selectedColumns)) { // pokud nejsou žádné podmínky je vytvořen nový
         Log::msg('Using record for new row is deprecated!! Caller: '.  json_encode(debug_backtrace()));
         return new $this->rowClass($this->tableStructure, false, $this);
      }

      $obj = clone $this;
      $obj = $obj->limit( $this->limit['from'] == null ? 0 : $this->limit['from'], 1);
      if ($pk != null AND $value == null) { // je zadána hodnota pk
         $obj = $obj->where($pk);
      } else if ($pk != null AND $value != null) { // je zadán sloupce a jeho jeho hodnota
         $obj = $obj->where($pk, $value);
      }
      return $this->getRow($obj, $fetchParams);
   }

   /**
    * Nový objekt záznamu
    * @return Model_ORM_Record
    */
   public function newRecord()
   {
      return new $this->rowClass($this->tableStructure, false, $this);
   }

   /**
    *
    * @param Model_ORM $obj
    * @param type $fetchParams
    * @return Model_ORM_Record|false
    */
   protected function getRow(Model_ORM $obj = null, $fetchParams = self::FETCH_LANG_CLASS)
   {
      if ($obj == null)
         $obj = $this;
      $sql = null;
      if($this->currentSql == null){
         $sql = 'SELECT ' . $obj->createSQLSelectColumns() . ' FROM `' . $this->getDbName() . '`.`' . $obj->getTableName() . '` AS ' . $this->getTableShortName();
         $obj->createSQLJoins($sql);
         $obj->createSQLWhere($sql, $this->getTableShortName());
         $obj->createSQLGroupBy($sql); // group by
         $obj->createSQLHaving($sql);
         $obj->createSQLOrder($sql);
         $obj->createSQLLimi($sql);
         $dbst = $this->getDb()->prepare($sql);
         $obj->bindSQLWhere($dbst);
         $obj->bindSQLHaving($dbst);
         $obj->bindSQLLimit($dbst);
         $obj->bindValues($dbst);
      } else if($this->currentSql instanceof PDOStatement) {
         $dbst = $this->currentSql;
         $this->currentSql = null;
      } else {
         $dbst = $this->getDb()->prepare($this->currentSql);
         $dbst = $this->bindValues($dbst);
         $this->currentSql = null;
      }
      $r = false;
      try {
         $r = false;
//         $timer = Debug_Timer::getInstance()->timerStart('SQL_record');
         if ($fetchParams == self::FETCH_LANG_CLASS OR $fetchParams == self::FETCH_PKEY_AS_ARR_KEY) {
            $dbst->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $this->rowClass, array($obj->tableStructure, true, $this));
            $dbst->execute();
            $r = $dbst->fetch(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE);
            $r instanceof Model_ORM_Record ? $r->load() : false;
         } else {
            $dbst->setFetchMode($fetchParams);
            $dbst->execute();
            $r = $dbst->fetch($fetchParams);
         }
//         $timer->timerStop('SQL_record', $sql != null ? $sql : $this->currentSql);
      } catch (PDOException $exc) {
         $this->unLock();
         CoreErrors::addException($exc);
         if (AppCore::getUserErrors() instanceof Messages AND VVE_DEBUG_LEVEL > 0) {
            AppCore::getUserErrors()->addMessage('ERROR SQL: ' . $sql);
         }
      }
      return $r;
   }

   protected $stmt = false;
   protected $stmtSQL = false;
   /**
    * Funkce pro na čtení dat pomocí jednoho záznamu
    * @param type $fetchParams
    * @return boolean
    * @internal není stabilní API!!!
    */
   public function fetchRow($fetchParams = self::FETCH_LANG_CLASS)
   {
      $r = false;
      if(!$this->stmt){
         $sql = null;
         if($this->currentSql == null){
            $sql = 'SELECT ' . $this->createSQLSelectColumns() . ' FROM `'
                . $this->getDbName() . '`.`' . $this->getTableName() . '` AS ' . $this->getTableShortName();
            $this->createSQLJoins($sql);
            $this->createSQLWhere($sql, $this->getTableShortName());
            $this->createSQLGroupBy($sql); // group by
            $this->createSQLHaving($sql);
            $this->createSQLOrder($sql);
            $this->createSQLLimi($sql);
            $this->stmt = $this->getDb()->prepare($sql);
            $this->bindSQLWhere($this->stmt);
            $this->bindSQLHaving($this->stmt);
            $this->bindSQLLimit($this->stmt);
            $this->bindValues($this->stmt);
         } else if($this->currentSql instanceof PDOStatement) {
            $this->stmt = $this->currentSql;
            $this->currentSql = null;
         } else {
            $this->stmt = $this->getDb()->prepare($this->currentSql);
            $this->stmt = $this->bindValues($this->stmt);
            $this->currentSql = null;
         }
         try {
            $r = false;
            if ($fetchParams == self::FETCH_LANG_CLASS OR $fetchParams == self::FETCH_PKEY_AS_ARR_KEY) {
               $this->stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $this->rowClass, array($this->tableStructure, true, $this));
               $this->stmt->execute();
            } else {
               $this->stmt->setFetchMode($fetchParams);
               $this->stmt->execute();
            }
         } catch (PDOException $exc) {
            $this->unLock();
            CoreErrors::addException($exc);
            if (AppCore::getUserErrors() instanceof Messages AND VVE_DEBUG_LEVEL > 0) {
               AppCore::getUserErrors()->addMessage('ERROR SQL: ' . $sql);
            }
         }
      }
      if ($fetchParams == self::FETCH_LANG_CLASS OR $fetchParams == self::FETCH_PKEY_AS_ARR_KEY) {
         $rec = $this->stmt->fetch(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE);
         $rec instanceof Model_ORM_Record ? $rec->load() : false;
         return $rec;
      } else {
         return $this->stmt->fetch($fetchParams);
      }
      return false;
   }

   /**
    * Metoda vrací záznamy z db
    * @param int -- typ výstupu PDO::FETCH_XXX nebo Model_ORM::FETCH_XXX
    * @return Model_ORM_Record[]|false
    */
   public function records($fetchParams = self::FETCH_LANG_CLASS)
   {
      $sql = null;
      if($this->currentSql == null){
         $sql = 'SELECT ' . $this->createSQLSelectColumns() . ' FROM `' . $this->getDbName() . '`.`' . $this->getTableName() . '` AS ' . $this->getTableShortName();
         $this->createSQLJoins($sql);
         $this->createSQLWhere($sql, $this->getTableShortName()); // where
         $this->createSQLGroupBy($sql); // group by
         $this->createSQLHaving($sql); // having
         $this->createSQLOrder($sql); // order
         $this->createSQLLimi($sql); // limit
         $dbst = $this->getDb()->prepare($sql);
         $this->bindSQLWhere($dbst); // where values
         $this->bindSQLHaving($dbst); // having values
         $this->bindSQLLimit($dbst); // limit values
         $this->bindValues($dbst); // limit values
      } else if($this->currentSql instanceof PDOStatement) {
         $dbst = $this->currentSql;
         $this->currentSql = null;
      } else {
         $dbst = $this->getDb()->prepare($this->currentSql);
         $dbst = $this->bindValues($dbst);
         $this->currentSql = null;
      }
      $r = false;
      try {
//         $timer = Debug_Timer::getInstance()->timerStart('SQL_records');
         if ($fetchParams == self::FETCH_LANG_CLASS OR $fetchParams == self::FETCH_PKEY_AS_ARR_KEY) {
            $dbst->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $this->rowClass, array($this->tableStructure, true, $this));
            $dbst->execute();
            $r = $dbst->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $this->rowClass, array($this->tableStructure, true, $this));
            foreach ($r as &$rec) {
               $rec instanceof Model_ORM_Record ? $rec->load() : '';
            }
         } else {
            $dbst->setFetchMode($fetchParams);
            $dbst->execute();
            $r = $dbst->fetchAll($fetchParams);
         }
//         $timer->timerStop('SQL_records', $sql != null ? $sql : $this->currentSql);
         if ($fetchParams == self::FETCH_PKEY_AS_ARR_KEY AND $r != false) {
            $newR = array();
            foreach ($r as $record) {
               $newR[$record->getPK()] = $record;
            }
            $r = $newR;
         }
      } catch (PDOException $exc) {
         $this->unLock();
         CoreErrors::addException($exc);
         if (AppCore::getUserErrors() instanceof Messages AND VVE_DEBUG_LEVEL > 0) {
            AppCore::getUserErrors()->addMessage('ERROR SQL: ' . $sql);
         }
      }

      return $r;
   }

   /**
    * Provede načtení přepřipraveného dotazu na db. V parametru můžou být změny proměnných pro where klauzuli
    * @param array $whereBarams -- parametry where v assoc. poli
    * @param type $fetchParams
    * @return Model_ORM_Record[]|false
    */
   public function fetchRecords($whereBarams = array(), $fetchParams = self::FETCH_LANG_CLASS)
   {
      if(!$this->fetchingDbst){
         $sql = 'SELECT ' . $this->createSQLSelectColumns() . ' FROM `' . $this->getDbName() . '`.`' . $this->getTableName() . '` AS ' . $this->getTableShortName();
         $this->createSQLJoins($sql);
         $this->createSQLWhere($sql, $this->getTableShortName()); // where
         $this->createSQLGroupBy($sql); // group by
         $this->createSQLHaving($sql); // having
         $this->createSQLOrder($sql); // order
         $this->createSQLLimi($sql); // limit
         $this->fetchingDbst = $this->getDb()->prepare($sql);
      }
      $this->whereBindValues = array_merge($this->whereBindValues, $whereBarams);

      $this->bindSQLWhere($this->fetchingDbst); // where values
      $this->bindSQLHaving($this->fetchingDbst); // having values
      $this->bindSQLLimit($this->fetchingDbst); // limit values
      $this->bindValues($this->fetchingDbst); // limit values
      $r = false;
      try {
//         $timer = Debug_Timer::getInstance()->timerStart('SQL_records');
         if ($fetchParams == self::FETCH_LANG_CLASS OR $fetchParams == self::FETCH_PKEY_AS_ARR_KEY) {
            $this->fetchingDbst->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $this->rowClass, array($this->tableStructure, true, $this));
            $this->fetchingDbst->execute();
            $r = $this->fetchingDbst->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $this->rowClass, array($this->tableStructure, true, $this));
         } else {
            $this->fetchingDbst->setFetchMode($fetchParams);
            $this->fetchingDbst->execute();
            $r = $this->fetchingDbst->fetchAll($fetchParams);
         }
//         $timer->timerStop('SQL_records', $sql != null ? $sql : $this->currentSql);
         if ($fetchParams == self::FETCH_PKEY_AS_ARR_KEY AND $r != false) {
            $newR = array();
            foreach ($r as $record) {
               $newR[$record->getPK()] = $record;
            }
            $r = $newR;
         }
      } catch (PDOException $exc) {
         $this->unLock();
         CoreErrors::addException($exc);
         if (AppCore::getUserErrors() instanceof Messages AND VVE_DEBUG_LEVEL > 0) {
            AppCore::getUserErrors()->addMessage('ERROR SQL: ' . $sql);
         }
      }

      return $r;
   }

   /**
    * Metoda vrací počet záznamů z db podle daného nastavení modelu
    * @return int
    * @todo -- dořešit při "GROUP BY"
    */
   public function count()
   {
      $sql = null;
      if($this->currentSql == null){
         $pk = '*';
         if($this->pKey != null){
            $pk = '`'.$this->getTableShortName().'`.`'.$this->pKey.'`';
         }
         if(!empty ($this->groupby)){
            $col = $pk;
            if(count($this->groupby) == 1){
               $col = '`'.$this->getTableShortName().'`.`'.reset($this->groupby).'`';
            }
            $sql = 'SELECT COUNT(distinct '.$col.') AS cnt FROM `' . $this->getDbName() . '`.`' . $this->getTableName() . '` AS ' . $this->getTableShortName();
         } else {
            $sql = 'SELECT COUNT('.$pk.') AS cnt FROM `' . $this->getDbName() . '`.`' . $this->getTableName() . '` AS ' . $this->getTableShortName();
         }
         $this->createSQLJoins($sql);
         $this->createSQLWhere($sql, $this->getTableShortName());
//         $this->createSQLGroupBy($sql);
         $dbst = $this->getDb()->prepare($sql);
         $this->bindSQLWhere($dbst);
      } else {
         /* tady asi musí byt replace sloupců za count */
         /* preg_replace('/SELECT * FROM/', 'COUNT(*)', $this->currentSql); */
         $dbst = $this->bindValues($this->dbconnector->prepare($this->currentSql));/* TODO */
      }

      try {
         $dbst->execute();
      } catch (PDOException $exc) {
         $this->unLock();
         CoreErrors::addException($exc);
         if (AppCore::getUserErrors() instanceof Messages AND VVE_DEBUG_LEVEL > 0) {
            AppCore::getUserErrors()->addMessage('ERROR SQL: ' . $sql);
         }
      }

      $r = $dbst->fetchObject();
      if ($r == false)
         return 0;
      return (int) $r->cnt;
   }

   /**
    * Vytvoření zámku na modelu (tabulce)
    * @param string $type -- typ zámku (READ/WRITE) Model_ORM::LOCK_XXX
    */
   public function lock($mode = self::LOCK_READ)
   {
      self::lockModels(array($this, $mode));
   }

   /**
    * Vytvoření zámku na modelu (tabulce)
    * @param array $model -- pole s modely nebo tabulkami  array($model, 'WRITE', 'alias'), array($model, 'READ', 'alias'),
    */
   public static function lockModels($models)
   {
      $models = func_get_args();

      foreach ($models as $model){
         if(!is_array($model)){
            $model = array($model);
         }

         $model[1] = isset($model[1]) ? $model[1] : self::LOCK_READ; // mode
         $model[1] = isset($model['mode']) ? $model['mode'] : $model[1]; // mode
         $model[2] = isset($model[2]) ? $model[2] : null; // alias
         $model[2] = isset($model['alias']) ? $model['alias'] : $model[2]; // alias

         if($model[0] instanceof Model_ORM){ // pokud je Model_ORM model
            // full table name
            $baseArr = array(
               'table' => $model[0]->getTableName(),
               'mode' => $model[1],
               'alias' => null );
            self::$tablesLocked[$baseArr['table'].'_'.$baseArr['alias']] = $baseArr;

            // table alias
            $baseArr = array(
               'table' => $model[0]->getTableName(),
               'mode' => $model[1],
               'alias' => $model[0]->getTableShortName() );
            self::$tablesLocked[$baseArr['table'].'_'.$baseArr['alias']] = $baseArr;

            // assign join tables
            foreach($model[0]->getJoins() as $join) {
               $joinModel = new $join['model2']();

               // full table name
               $baseArr = array(
                  'table' => $joinModel->getTableName(),
                  'mode' => $model[1],
                  'alias' => null );
               self::$tablesLocked[$baseArr['table'].'_'.$baseArr['alias']] = $baseArr;

               // table alias
               $baseArr = array(
                  'table' => $joinModel->getTableName(),
                  'mode' => $model[1],
                  'alias' => $joinModel->getTableShortName() );
               self::$tablesLocked[$baseArr['table'].'_'.$baseArr['alias']] = $baseArr;

               // table alias
               $baseArr = array(
                  'table' => $join['table2Alias'],
                  'mode' => $model[1],
                  'alias' => null );
               self::$tablesLocked[$baseArr['table'].'_'.$baseArr['alias']] = $baseArr;
            }
         } else if(is_string($model[0])){ // pokud je zadán název tabulky
            $baseArr = array(
               'table' => $model[0],
               'mode' => $model[1],
               'alias' => $model[2] );

            self::$tablesLocked[$baseArr['table'].'_'.$baseArr['alias']] = $baseArr;
         }
      }

      // struktura self::$tablesLocked
      /*
       * array(
       *    array('table' => 'table_name', 'mode' => 'read' )
       *    array( array('table' => 'table_name', 'mode' => 'write' 'alias' => 't_alias' )
       * )
       */
      if(!empty(self::$tablesLocked)){
         // create SQL
         $parts = array();
         foreach (self::$tablesLocked as $table){
            if(!isset($table['alias']) || $table['alias'] == null){
               $parts[] = $table['table'].' '.$table['mode'];
            } else {
               $parts[] = $table['table'].' AS '.$table['alias'].' '.$table['mode'];
            }
         }
         // merge with locked tables
         $db = Db_PDO::getInstance();
         $db->exec('LOCK TABLES '.implode(', ', $parts).';');
      }
   }

   /**
    * Zrušení zámku
    */
   public function unLock()
   {
       self::unLockTables();
   }

   /**
    * Zrušení zámku
    */
   public static function unLockTables()
   {
      if(!empty(self::$tablesLocked)){
         self::$tablesLocked = array();
         $db = Db_PDO::getInstance();
         $db->exec('UNLOCK TABLES;');
      }
   }


   /**
    * Metoda pro dotaz na model
    * @param mixed $stmt (PDOStatement nebo string)
    * @return type PDOStatement
    *
    * Pokud je předán řetezec, je řetězec {THIS} nahrazen názvem tabulky
    */
   public function query($stmt)
   {
      if($stmt instanceof PDOStatement == false) {
         if(is_string($stmt)){
            $stmt = str_replace('{THIS}', '`'.$this->getTableName().'`', $stmt);
         }
         $stmt = $this->getDb()->prepare($stmt);
      }
      return $stmt;
   }

   /**
    * Metoda pro dotaz na model
    * @param string $stmt dotaz
    * @return this
    *
    * Pokud je předán řetezec, je řetězec {THIS} nahrazen názvem tabulky
    */
   public function setQuery($query)
   {
      if(is_string($query)) {
         $this->currentSql = str_replace('{THIS}', '`'.$this->getTableName().'`', $query);
      } else if($query instanceof PDOStatement){
         $this->currentSql = $query;
      }
      return $this;
   }

   /**
    * Metoda vrací db konektor k databázi
    * @return Db_PDO
    */
   public function getDb()
   {
      return Db_PDO::getInstance();
   }

   /**
    * Metoda přidá parametry do dotazu
    * @param type $values
    * @param type $merge
    * @return this
    */
   public function setBindValues($values, $merge = false)
   {
      if($merge){
         $this->bindValues = array_merge($values, $this->bindValues);
      } else {
         $this->bindValues = $values;
      }
      return $this;
   }

   /**
    * Varcí SQL dotaz pro výběr
    * @return string
    */
   public function getSQLQuery($assignVars = true)
   {
      if($this->currentSql == null){
         $sql = 'SELECT ' . $this->createSQLSelectColumns()."\n" . ' FROM `' . $this->getDbName() . '`.`' . $this->getTableName() . '` AS ' . $this->getTableShortName()."\n";
      } else {
         $sql = $this->currentSql;
      }
      $this->createSQLJoins($sql);
      $this->createSQLWhere($sql, $this->getTableShortName()); // where
      $this->createSQLGroupBy($sql); // group by
      $this->createSQLHaving($sql); // group by
      $this->createSQLOrder($sql); // order
      $this->createSQLLimi($sql); // limit
      if($assignVars){
         $replacements = array();
         foreach (array_merge($this->bindValues, $this->whereBindValues, $this->havingBindValues, $this->limit) as $key => $value) {
            $k = $key;
            if(strpos($key,':') === false){
               $k = ':'.$key;
            }
            if($value instanceof DateTime){
               $value = $value->format(DATE_ISO8601);
            }
            $replacements[$k] = is_int($value) ? $value : '\''.$value.'\'';
         }
         $sql = str_replace(array_keys($replacements), $replacements, $sql);
      }
      return array(
         'sql' => $sql,
         'values' => array_merge($this->bindValues, $this->whereBindValues, $this->havingBindValues, $this->limit)
      );
   }

   /**
    * Uloží záznam do databáze
    * @param Model_ORM_Record $record
    * @return int|string -- primární klíč nového záznamu
    */
   public function save(Model_ORM_Record $record)
   {
      $returnPk = $record->getPK();

      // validace

      if (!$record->isNew()) {
         $this->beforeSave($record, 'U');
         // @TODO - použít metodu update !!!!
         // UPDATE
         $sql = 'UPDATE ' . $this->getTableName();

         $colsStr = array();
         // create query
         foreach ($record->getColumns() as $colname => $params) {
            if ($params['extern'] == true OR $params['changed'] < 1)
               continue;
            if (!isset($params['lang']))
               $params['lang'] = false;
            if ($params['lang'] === true) {
               foreach (Locales::getAppLangs() as $lang) {
                  if ($params['aliasFor'] === null) {
                     array_push($colsStr, '`' . $colname . '_' . $lang . '` = :' . $colname . '_' . $lang . '');
                  } else {
                     array_push($colsStr, '`' . $params['aliasFor'] . '_' . $lang . '` = :' . $colname . '_' . $lang . '');
                  }
               }
            } else {
               if ($params['aliasFor'] === null) {
                  array_push($colsStr, '`' . $colname . '` = :' . $colname . '');
               } else {
                  array_push($colsStr, '`' . $params['aliasFor'] . '` = :' . $colname . '');
               }
            }
         }
         if (empty($colsStr)){
            return $returnPk; // žádné změny se neukládájí
         }
         /** @todo tady asi přidělat where, protože pak by šli dělat updaty na různách sloupcích */
         $sqlStr = $sql . ' SET ' . implode(',', $colsStr) . ' WHERE `' . $this->pKey . '` = :pkey';
         $dbst = $this->getDb()->prepare($sqlStr);
         Log::msg($sqlStr, 'UPDATE', null, 'sql');

         $dbst->bindValue(':pkey', $record->getPK(), $this->tableStructure[$this->pKey]['pdoparam']); // bind pk
         // bind values
         foreach ($record->getColumns() as $colname => $params) {
            if ($params['extern'] == true OR $params['changed'] < 1)
               continue;
            if (!isset($params['lang']))
               $params['lang'] = false;
            $value = $params['value'];
            if ($params['lang'] == true) {
               foreach (Locales::getAppLangs() as $lang) {
                  $dbst->bindValue(':' . $colname . '_' . $lang, $value[$lang], $params['pdoparam']);
               }
            } else {
               // date clumns
               if ($value instanceof DateTime) {
                  $value = $this->createDateTimeStr($value, $params['datatype']);
               }
               if(is_array($value) || is_object($value) ){
                  $value = serialize($value);
               }
               $dbst->bindValue(':' . $colname, $value, $params['pdoparam']);
            }
         }
//         $timer = Debug_Timer::getInstance()->timerStart('SQL_update');
         try {
            $dbst->execute();
         } catch (PDOException $exc) {
            $this->unLock();
            CoreErrors::addException($exc);
            if (AppCore::getUserErrors() instanceof Messages AND VVE_DEBUG_LEVEL > 0) {
               AppCore::getUserErrors()->addMessage('ERROR SQL: ' . $sqlStr);
            }
         }
//         $timer->timerStop('SQL_update', $sqlStr);
         $returnPk = $record->getPK();
      } else {
         // INSERT
         $this->beforeSave($record, 'I');
         $sql = "INSERT INTO " . $this->getTableName();
         $colsStr = array();
         $bindParamStr = array();
         // create query
         foreach ($record->getColumns() as $colname => $params) {
            if (!isset($params['lang']))
               $params['lang'] = false;
            if ($params['extern'] == true OR $params['changed'] != 1)
               continue;
            if ($params['lang'] === true) {
               foreach (Locales::getAppLangs() as $lang) {
                  if ($params['aliasFor'] === null) {
                     array_push($colsStr, '`' . $colname . '_' . $lang . '`');
                  } else {
                     array_push($colsStr, '`' . $params['aliasFor'] . '_' . $lang . '`');
                  }
                  array_push($bindParamStr, ':' . $colname . '_' . $lang);
               }
            } else {
               if ($params['aliasFor'] === null) {
                  array_push($colsStr, '`' . $colname . '`');
               } else {
                  array_push($colsStr, '`' . $params['aliasFor'] . '`');
               }
               array_push($bindParamStr, ':' . $colname);
            }
         }
         $sqlStr = $sql . ' (' . implode(',', $colsStr) . ') VALUES (' . implode(',', $bindParamStr) . ')';
         $dbst = $this->getDb()->prepare($sqlStr);
         Log::msg($sqlStr, 'INSERT', null, 'sql');
         // bind values
         foreach ($record->getColumns() as $colname => $params) {
            if (!isset($params['lang']))
               $params['lang'] = false;
            if ($params['extern'] == true OR $params['changed'] != 1)
               continue;
            if (isset($params['default']) AND $params['value'] === false AND $params['changed'] != 1) {
//               switch ((string) $params['default']) {
//                  case 'CURRENT_TIMESTAMP':
//                     $params['value'] = new DateTime();
//                     break;
//                  default:
                     $params['value'] = $params['default'];
//                     break;
//               }
            }
            $value = $params['value'];
            if ($params['lang'] == true) {
               foreach (Locales::getAppLangs() as $lang) {
                  if ($params['nn'] AND isset($params['default']) AND $params['value'][$lang] == null ) {
                     if(is_bool($params['default']) || is_int($params['default'])){
                        // bool vždy na int jinak false je prázdné, ne 0
                        $value[$lang] = (int)$params['default'];
                     } else {
                        $value[$lang] = $params['default'];
                     }
                  }
                  $dbst->bindValue(':' . $colname . '_' . $lang, $value[$lang], $params['pdoparam']);
               }
            } else {
               // date clumns
               if ($value instanceof DateTime) {
                  $value = $this->createDateTimeStr($value, $params['datatype']);
               }
               if(is_array($value) || is_object($value) ){
                  $value = serialize($value);
               }
               $dbst->bindValue(':' . $colname, $value, $params['pdoparam']);
            }
         }
//         $timer = Debug_Timer::getInstance()->timerStart('SQL_insert');
         try {
            $dbst->execute();
         } catch (PDOException $exc) {
            $this->unLock();
            CoreErrors::addException($exc);
            if (AppCore::getUserErrors() instanceof Messages AND VVE_DEBUG_LEVEL > 0) {
               AppCore::getUserErrors()->addMessage('ERROR SQL: ' . $sqlStr);
            }
         }
//         $timer->timerStop('SQL_insert', $sqlStr);
         $returnPk = $this->getDb()->lastInsertId();
         $record->_setPK($returnPk);
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
   public function delete($pk = null)
   {
      $dbc = Db_PDO::getInstance();
      $sql = 'DELETE '.$this->getTableShortName().'.* FROM ' . $this->getTableName().' AS '.$this->getTableShortName();
      $pkValue = false;
      if ($pk instanceof Model_ORM_Record) {
         $this->where($this->pKey, $pk->getPK());
         // asi vytáhnout pk (bude muset být uloženo v rekordu)
         $pkValue = $pk->getPK();
      } else if ($pk != null) {
         $this->where($this->pKey, $pk);
         $pkValue = $pk;
      } else if (isset($this->whereBindValues['col'])) {
         $pkValue = $this->whereBindValues['col'];
      }
      if ($pk == null AND $this->where == null) {
         return 0;
      } // pokud není podmínka nemažeme, na smazání kompletní tabulky bude flush()
      $this->createSQLJoins($sql);
      $this->createSQLWhere($sql);
//      $this->createSQLGroupBy($sql); // group by
//      $this->createSQLHaving($sql); // having
      $dbst = $dbc->prepare($sql);
//      Debug::log($sql);
      Log::msg($sql, 'DELETE', null, 'sql');
      $this->bindSQLWhere($dbst);
//      $this->bindSQLHaving($dbst); // having values
      try {
         if($pkValue){
            $this->beforeDelete($pkValue);
         }
         $dbst->execute();
      } catch (PDOException $exc) {
         $this->unLock();
         CoreErrors::addException($exc);
         if (AppCore::getUserErrors() instanceof Messages AND VVE_DEBUG_LEVEL > 0) {
            AppCore::getUserErrors()->addMessage('ERROR SQL: ' . $sql);
         }
      }
      if ($pkValue) { // if $pkey is not defined
         $this->deleteRelations($pkValue); // vymazat přidružení
      }
      return $dbst->rowCount();
   }

   /**
    * Metoda vymaže všechny záznamy z tabulky
    * @return bool
    */
   public function truncate()
   {
      $dbc = Db_PDO::getInstance();
//      $this->lock(self::LOCK_READ);
      $sql = 'TRUNCATE ' . $this->getTableName();
      $dbst = $dbc->prepare($sql);
      Log::msg($sql, 'TRUNCATE', null, 'sql');
      try {
         $ret = $dbst->execute();
      } catch (PDOException $exc) {
//         $this->unLock();
         CoreErrors::addException($exc);
         if (AppCore::getUserErrors() instanceof Messages AND VVE_DEBUG_LEVEL > 0) {
            AppCore::getUserErrors()->addMessage('ERROR SQL: ' . $sql);
         }
      }
      return $ret;
   }

   /**
    * Metoda provede update záznamů
    * @param array $updateValues -- pole s hodnotami které se mají upravit
    * @todo Jazykové sloupce
    *
    * array
    *    'ColumnName' => value
    *
    * array
    *    'ColumnName' => array
    *       'stmt' => string 'DATEADD(`ColumnName`, :dates)'
    *       'values' => array
    *          'dates' => string '2012-03-03'
    */
   public function update($updateValues)
   {
//      Debug::log($updateValues);

      $sql = 'UPDATE ' . $this->getTableName(). " SET ";
      $colsStr = array();
      $bindValues = array();
      foreach ($updateValues as $columnName => $value) {
         // UPDATE

         if( !isset($this->tableStructure[$columnName]) ){
            // není sloupec z tabubly -- přeskočit
            continue;
         }

         $params = $this->tableStructure[$columnName];

         if($this->tableStructure[$columnName]['lang'] == true){
            // is multilang column
            foreach (Locales::getAppLangs() as $lang) {
               if ($params['aliasFor'] === null) {
                  array_push($colsStr, '`' . $columnName . '_' . $lang . '` = :' . $columnName . '_' . $lang . '');
               } else {
                  array_push($colsStr, '`' . $params['aliasFor'] . '_' . $lang . '` = :' . $columnName . '_' . $lang . '');
               }
               if(is_array($value)){
                  $bindValues[$columnName . '_' . $lang] = $value[$lang];
               } else {
                  $bindValues[$columnName . '_' . $lang] = $value;
               }
            }

         } else {
            // is normal column

            if(is_array($value)){
               // je předán dotaz

               if ($params['aliasFor'] === null) {
                  array_push($colsStr, '`' . $columnName . '` = ' . $value['stmt'] . '');
               } else {
                  array_push($colsStr, '`' . $params['aliasFor'] . '` = ' . $value['stmt'] . '');
               }
               if(isset($value['values'])){
                  $bindValues = array_merge($bindValues, $value['values']);
               }

            } else {
               // je jenom hodnota
               if ($params['aliasFor'] === null) {
                  array_push($colsStr, '`' . $columnName . '` = :' . $columnName . '');
               } else {
                  array_push($colsStr, '`' . $params['aliasFor'] . '` = :' . $columnName . '');
               }
               $bindValues[$columnName] = $value;
            }
         }
      }

      // create base cols
      $sql .= implode(', ', $colsStr);

      $this->createSQLWhere($sql);
      $this->createSQLLimi($sql);

      $dbst = $this->getDb()->prepare($sql);

      foreach ($bindValues as $key => $value) {
         $varType = gettype($value);
         // detect value
         if ($varType == 'boolean') {
            $pdoParam = PDO::PARAM_BOOL;
         } else if ($varType == 'integer') {
            $pdoParam = PDO::PARAM_INT;
         } else if ($varType == 'NULL') {
            $pdoParam = PDO::PARAM_NULL;
         } else if ($varType == 'array') {
            $pdoParam = PDO::PARAM_STMT;
            $val = join(',', $value);
         } else if ($varType == 'double' || $varType == 'string') {
            $pdoParam = PDO::PARAM_STR;
         } else if ($varType == 'object') {
            $pdoParam = PDO::PARAM_STR;
            $className = get_class($value);
            if ($value instanceof DateTime) {
               $value = $value->format(DATE_ISO8601);
            } else if (method_exists($value, '__toString')) {
               $value = (string) $value;
            } else {
               throw new UnexpectedValueException(sprintf($this->tr('Nepovolený objekt "%s" v předanám paramteru'), $className));
            }
         } else {
            throw new UnexpectedValueException(sprintf($this->tr('Nepovolená hodnota "%s" v předanám paramteru'), $varType));
         }

         $dbst->bindValue(strpos($key, ':') === false ? ':'.$key : $key, $value, $pdoParam);
      }

      $this->bindSQLWhere($dbst);
      $this->bindSQLLimit($dbst);

      try {
         return $dbst->execute();
      } catch (PDOException $exc) {
         $this->unLock();
         CoreErrors::addException($exc);
         if (AppCore::getUserErrors() instanceof Messages AND VVE_DEBUG_LEVEL > 0) {
            AppCore::getUserErrors()->addMessage('ERROR SQL: ' . $sql);
         }
      }
   }

   /**
    * Metoda vrací záznamy z fulltext hledání v modelu
    * @param string $string -- řetězec, který se má hledat
    * @return Model_ORM_Record[]|false -- pole s výsledky
    * @todo implementovat načítání pouze zvolených sloupců
    */
   public function search($string, $allLangs = false, $fetchParams = self::FETCH_LANG_CLASS)
   {
      $this->setSelectAllLangs($allLangs);
      $cols = $this->tableStructure;
      // vybrání sloupců a vytvoření podřetězců
      $selectParts = array();
      $whereParts = array();
      foreach ($cols as $name => $params) {
         if($params['fulltext'] == false) continue;
         // jak se bude sloupce jmenovat (aliasy)
         if($params['aliasFor'] != null ){
            $name = $params['aliasFor'];
         }
         // pokud je jazykový přidají se přípony pro vybraný jazyk
         if($params['lang'] == true){
            $name .= '_'.Locales::getLang();
         }
         // select - není nutný, protože řazení je výchozí podle relevance
         array_push($selectParts, '('.  round($params['fulltextRel']).' * MATCH (`'.$this->getTableShortName().'`. `'.$name.'`) AGAINST (:searchString) )');
         // where
         array_push($whereParts, '`'.$this->getTableShortName().'`.`'.$name.'`');
      }
      // nesmí být prázdné vyhledávání
      if(empty ($selectParts) OR empty ($whereParts)){
         throw new OutOfRangeException($this->tr('Model nepodporuje vyhledávání pomocí fultextu'));
      }

      $sql = 'SELECT ' . $this->createSQLSelectColumns().', '.implode(' + ', $selectParts).' AS '.Search::COLUMN_RELEVATION
         .' FROM `' . $this->getDbName() . '`.`' . $this->getTableName() . '` AS ' . $this->getTableShortName();

      $this->order(array(Search::COLUMN_RELEVATION => self::ORDER_DESC));

      $this->createSQLJoins($sql);

      $this->whereBindValues['searchString'] = $string;
      $sqlWhere = ' MATCH ('. implode(',', $whereParts).') AGAINST (:searchString IN BOOLEAN MODE)';
      if($this->where != null){
         $sqlWhere .=  'AND ' . $this->where;
      }
      $this->where = $sqlWhere;

      $this->createSQLWhere($sql, $this->getTableShortName()); // where
      $this->createSQLGroupBy($sql); // group by
      $this->createSQLOrder($sql); // order
      $this->createSQLLimi($sql); // limit
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare($sql);
      $this->bindSQLWhere($dbst); // where values
      $this->bindSQLLimit($dbst); // limit values

      $r = false;
      try {
         if ($fetchParams == self::FETCH_LANG_CLASS OR $fetchParams == self::FETCH_PKEY_AS_ARR_KEY) {
            $dbst->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $this->rowClass, array($this->tableStructure, true, $this));
            $dbst->execute();
            $r = $dbst->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $this->rowClass, array($this->tableStructure, true, $this));
         } else {
            $dbst->setFetchMode($fetchParams);
            $dbst->execute();
            $r = $dbst->fetchAll($fetchParams);
         }

         if ($fetchParams == self::FETCH_PKEY_AS_ARR_KEY AND $r != false) {
            $newR = array();
            foreach ($r as $record) {
               $newR[$record->getPK()] = $record;
            }
            $r = $newR;
         }
      } catch (PDOException $exc) {
         $this->unLock();
         CoreErrors::addException($exc);
         if (AppCore::getUserErrors() instanceof Messages AND VVE_DEBUG_LEVEL > 0) {
            AppCore::getUserErrors()->addMessage('ERROR SQL: ' . $sql);
         }
      }

      return $r;
   }

   /*
    * Metody pro úrpoavu a vytváření modelu
    */

   public function createTable()
   {
      $db = new Model_ORM_Db($this);
      return $db->create();
   }

   /*
    * Metody pro úpravu parametrů modelu
    */

   /**
    * Metoda přidává limit
    * @param int $fromRow -- od řádku
    * @param int $rows -- řádků
    * @return this
    */
   public function limit($fromRow, $rows)
   {
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
    * @return this
    */
   public function order($arrayOrders, $merge = false)
   {
      if(!is_array($arrayOrders)){
         $arrayOrders = array($arrayOrders => Model_ORM::ORDER_ASC);
      }
      if($merge){
         $this->orders = array_merge($this->orders, $arrayOrders);
      } else {
         $this->orders = $arrayOrders;
      }
      return $this;
   }

   /**
    * Metoda přidá podmínky do dotazu
    * @param array $condArray -- pole s podmínkami
    * @param array $bindValues -- pole s předanými hodnotami - reference
    * @return this
    */
   public function where($cond = null, $bindValues = null, $append = false)
   {
      // vymaz
      if ($cond === null) {
         $this->where = null;
         $this->whereBindValues = array();
      }
      // pokud je jenom hodnota bere se primary key, nesmí obsahovat rovnítko a mezeru
      else if ($bindValues === null && strpos($cond, '=') === false && strpos($cond, ' ') === false) {
         $bindValues = array('col' => $cond);
         $cond = $this->pKey . ' = :col';
      }
      // pokud je jenom podmínka
      else if ($bindValues === null) {
         $bindValues = array();
      }
      // pokud je sloupec a hodnota
      else if ($bindValues !== null AND !is_array($bindValues)) {
         $cond = $cond . ' = :colval';
         $bindValues = array('colval' => $bindValues);
      }

      // přidání dvojtečky do všech klíčů (ryhlejší zpracování vy výstupu)
      foreach ($bindValues as $key => $value) {
         if(strpos($key, ':') === false){
            $bindValues[':'.$key] = $value;
            unset($bindValues[$key]);
         }
      }

      if ($append == false) {
         $this->where = $cond;
         $this->whereBindValues = $bindValues;
      } else {
         $this->where = $this->where . ' ' . $cond;
         $this->whereBindValues = array_merge($this->whereBindValues, $bindValues);
      }
      return $this;
   }

   /**
    * Podmínka s AND
    * @param string $cond - podmínka
    * @param array $values - hodnoty
    * @return Model_ORM
    */
   public function whereAnd($cond, $values)
   {
      return $this->where(' AND '.$cond, $values, true);
   }

   /**
    * Podmínka s OR
    * @param string $cond - podmínka
    * @param array $values - hodnoty
    * @return Model_ORM
    */
   public function whereOr($cond, $values)
   {
      return $this->where(' OR '.$cond, $values, true);
   }

   /**
    * Metoda přidá podmínku HAVING do dotazu
    * @param null $cond
    * @param null $bindValues
    * @return this
    */
   public function having($cond = null, $bindValues = null)
   {
      if($cond == null){
         $this->having = null;
         $this->havingBindValues = array();
      } else {
         $this->having = $cond;
         $this->havingBindValues = $bindValues;
      }
      return $this;
   }

//   public function join($tbName, $columns = null, $joinType = self::JOIN_LEFT) {
//      'column' => $column, 'modelName' => $modelName, 'modelColumn' => $externColumn, 'columns' => null
//      $joinParams = array('column' => $column, 'modelName' => $modelName, 'modelColumn' => $externColumn, 'columns' => null);
//      array_push($this->joins, array('tablename' => $tbName, 'type' => $joinType, 'columns' => $columns));
//      return $this;
//   }

   /**
    * Metoda připojí model definovaný pomocí ForeignKey pomocí JOIN
    * @param string/array $colname -- Název sloupce 1, popřípadě aliasem 1 => sloupce 1
    * @param array $columns -- (optional) pole se sloupci
    * @param int $joinType -- (optional) typ joinu (Model_ORM konstanta)
    * @param string $with -- (optional) další parametry co se připojí k joinu
    * @param array $withValues -- (optional) hodnoty přidaných parametrů připojených k joinu
    * @return this
    */
   public function joinFK($colname, $columns = null, $joinType = self::JOIN_LEFT, $with = null, $withValues = array())
   {
      $newIndex = count($this->joins);
      /* FK
       * 'column' => string 'id_cat' (length=6)
       * 'modelName' => string 'Model_Category' (length=14)
       * 'modelColumn' => string 'id_category' (length=11)
       */

      // pokud je vložen pole je alias 1 => column 1
      $tAlias = $this->getTableShortName();
      if (is_array($colname)) {
         $t2Alias = key($colname);
         $colname = current($colname);
         $fk = $this->foreignKeys[$colname];
      } else {
         // pokud je jenom sloupec
         $fk = $this->foreignKeys[$colname];
         $t2Alias = $fk['modelName'] . '_' . ($newIndex + 1);
      }

      if ($fk['modelColumn'] == null) $fk['modelColumn'] = $fk['column'];

      $joinParams = array(
         'column1' => $fk['column'], 'table1Alias' => $tAlias, // tento model
         'model2' => $fk['modelName'], 'column2' => $fk['modelColumn'], 'table2Alias' => $t2Alias, // připojený model
         'columns' => $columns, 'type' => $joinType, 'with' => $with, 'prefix' => $fk['prefix']);
      $this->joins[$newIndex] = $joinParams;
      return $this;
   }

   /**
    * Metoda připojí model pomocí JOIN
    * @param string/array $model1Column -- Název sloupce 1, popřípadě aliasem 1 => sloupce 1
    * @param array $model2Name -- Název modelu 2 nebo pole s aliasem 2 a modelem 2
    * @param string $model2Column -- Název sloupce z modelu 2
    * @param array $columns -- (optional) pole se sloupci
    * @param int $joinType -- (optional) typ joinu (Model_ORM konstanta)
    * @param string $with -- (optional) další parametry co se připojí k joinu
    * @param array $withValues -- (optional) hodnoty přidaných parametrů připojených k joinu (název > hodnota)
    * @return this
    */
   public function join($model1Column, $model2Name, $model2Column = null, $columns = null, $joinType = self::JOIN_LEFT, $with = null, $withValues = array(), $columnsPrefix = null)
   {
      $newIndex = count($this->joins);
      $model1Alias = $this->getTableShortName();
      // púokud je předán model se sloupcem
      if (is_array($model1Column)) {
         $model1Alias = key($model1Column);
         $model1Column = current($model1Column);
      }

      if (is_array($model2Name)) {
         $model2Alias = key($model2Name);
         $model2Name = current($model2Name);
      } else {
         $model2Alias = $model2Name . '_' . $newIndex;
      }
      if ($model2Column == null)
         $model2Column = $model1Column;

      $joinParams = array(
         'column1' => $model1Column, 'table1Alias' => $model1Alias,
         'model2' => $model2Name, 'column2' => $model2Column, 'table2Alias' => $model2Alias,
         'columns' => $columns, 'type' => $joinType, 'with' => $with, 'withValues' => $withValues, 'prefix' => $columnsPrefix);
      array_push($this->joins, $joinParams);
      return $this;
   }

   /**
    * Metoda přidá parametr GROUP BY
    * @param array $cols -- sloupce, podle kterých se má seskupovat, key je možní použít jako alias tabulky
    * @param bool $merge -- (option) sloupčí s předešlými sloupci
    * @return this
    */
   public function groupBy($cols, $merge = false)
   {
      if(!is_array($cols)) $cols = array($cols);
      if(!$merge){
         $this->groupby = $cols;
      } else {
         $this->groupby = array_merge($this->groupby, $cols);
      }
      return $this;
   }

   /**
    * Metoda nastaví, které sloupce se budou vybírat
    * @param array $columnsArr -- pole se sloupci
    * @return this -- sám sebe
    * @example:
    * array(
    *    'alias' => 'colum',
    *    'alias' => 'statement',
    *    'alias' => array('stmt' => 'statement', 'values' => array() ),
    * )
    */
   public function columns($columnsArr, $bindValues = array(), $mergeValues = false)
   {
      $this->selectedColumns = $columnsArr;
      $this->setBindValues($bindValues, $mergeValues);
      return $this;
   }

   /**
    * Metoda provede výmaz hodnot s přidružených tabulek
    */
   protected function deleteRelations($pkVal)
   {
      if (!empty($this->relations)) {
         foreach ($this->relations as $rel) {
            $model = new $rel['model']();
            if ($model instanceof Model_ORM) {
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
   protected function createSQLSelectColumns()
   {
      $columns = array();
      if (empty($this->selectedColumns)) { // není vybrán žádný sloupec, tedy všechny
         foreach ($this->tableStructure as $columnName => $params) {
            array_push($columns, $this->createSelectColumnString(
               $this->getTableShortName(), $columnName, null, $params['lang'], $params['aliasFor']));
         }
      } else { // sloupce jsou vybrány
         if($this->pKey != null && !in_array($this->pKey, $this->selectedColumns)){
            // pokud není pk přidáme jej
            array_push($columns, $this->createSelectColumnString(
               $this->getTableShortName(), $this->pKey, null, false, $this->tableStructure[$this->pKey]['aliasFor']));
         }
         foreach ($this->selectedColumns as $alias => $columnName) {
            if (isset($this->tableStructure[$columnName])) {
               array_push($columns, $this->createSelectColumnString(
                  $this->getTableShortName(), $columnName, $alias, $this->tableStructure[$columnName]['lang'], $this->tableStructure[$columnName]['aliasFor']));
               if($columnName == $this->pKey) $pkAdded = true;
            } else { // není colum z této tabulky nebo se jedná o funkci
               if($columnName == '*'){
                  foreach ($this->tableStructure as $columnName => $params) {
                     array_push($columns, $this->createSelectColumnString(
                        $this->getTableShortName(), $columnName, null, $params['lang'], $params['aliasFor']));
                  }
               } else if (is_int($alias)) {
                  array_push($columns, '' . str_replace('{THIS}', $this->getTableName(), $columnName) . '');
               } else {
                  array_push($columns, '' . str_replace('{THIS}', $this->getTableName(), $columnName) . ' AS `'.$alias.'`');
               }
            }
         }
      }
      $columns = $this->createSQLSelectJoinColumns($columns);
      $tableCols = implode(', ', $columns);
      return $tableCols;
   }

   /**
    * Metoda vytváří sloupce pro join. Kvůli optimalizace je zde vytvořena i klauzule JOIN
    * @param <type> $columns
    * @return array
    */
   protected function createSQLSelectJoinColumns($columns)
   {
      $this->joinString = null; //reset
      if (!empty($this->joins)) {
         foreach ($this->joins as $join) {
            /*
             * $joinParams = array(
             * 'model1' => $model1Name, 'column1' => $model1Column, 'table1Alias' => $model1Alias,
             * 'model2' => $model2Name, 'column2' => $model2Column, 'table2Alias' => $model2Alias,
             * 'columns' => $columns, 'type' => $joinType, 'with' => $with, 'prefix' => string);
             */
            if (!class_exists($join['model2']))
               throw new Exception($this->tr('Neplatný parametr s modelem'));
            $model2 = new $join['model2']();
            $modelCols = $model2->getColumns();
            // vytváření SQL pro joiny je tu kvůli kešování
            $part = null;
            switch ($join['type']) {
               case self::JOIN_CROSS:
                  $part .= ' CROSS ';
                  break;
               case self::JOIN_INNER:
                  $part .= ' INNER ';
                  break;
               case self::JOIN_RIGHT:
                  $part .= ' RIGHT ';
                  break;
               case self::JOIN:
                  $part .= ' ';
                  break;
               case self::JOIN_LEFT:
               default:
                  $part .= ' LEFT ';
                  break;
            }
            $part .= 'JOIN ' . $model2->getTableName() . ' AS ' . $join['table2Alias'];
            $part .= ' ON (`' . $join['table2Alias'] . '`.`' . $join['column2'] . '` = `' . $join['table1Alias'] . '`.`' . $join['column1'] . '` ';
            if($join['with'] != null){ // další parametry v joinu
               $add = $join['with'];
               if(!empty($join['withValues'])){
                  $pdodriver = Db_PDO::getInstance();
                  foreach($join['withValues'] as $key => $value) {
//                      if(is_int($value)){
                     $add = str_replace(':'.$key, $pdodriver->quote($value), $add);
//                      } else {
//                         str_replace(':'.$key, PDO::quote($value));
//                      }
                  }
               }
               $part .= $add;
            }

            $part .= ')';
            $this->joinString .= $part."\n"; // uložení do joinstring
            // samotné vytvoření sloupců

            if ($join['columns'] === null) { // všechny sloupce z modelu
               foreach ($modelCols as $name => $params) {
                  if (isset($this->tableStructure[$name]) OR ($params['pk'] == true && $name == $this->pKey))// všechny sloupce z tabulky kromě pk
                     continue;
                  array_push($columns, $this->createSelectColumnString($join['table2Alias'], $name, $join['prefix'].$name, $params['lang'], $params['aliasFor']));
               }
            } else if(is_array($join['columns']) AND !empty ($join['columns'])) {
               foreach ($join['columns'] as $alias => $coll) {
                  if($coll == "*"){
                     array_push($columns, '`'.$join['table2Alias'].'`.*');
                  } else {
                     array_push($columns, $this->createSelectColumnString($join['table2Alias'], $coll, $join['prefix'].$alias,
                        isset($modelCols[$coll]) ? $modelCols[$coll]['lang'] : false, isset($modelCols[$coll]) ? $modelCols[$coll]['aliasFor'] : null));
                  }
               }
            }
            unset($model2);
         }
      }
      return $columns;
   }

   /**
    * Metoda vytváří řetězec pro sloupce
    *
    * <code>
    * 0 => string 'Model_Groups_1' (length=14)
    * 1 => string 'gname' (length=5)
    * 2 => null
    * 3 => boolean false
    * 4 => string 'name' (length=4)
    * </code>
    * @param <type> $tbPrefix
    * @param <type> $columnName
    * @param <type> $alias
    * @param <type> $isLang
    * @param <type> $columnOrigName
    * @return string
    */
   protected function createSelectColumnString($tbPrefix, $columnName, $alias = null, $isLang = false, $columnOrigName = null)
   {
      $alias = is_numeric($alias) ? null : $alias; // numbers as alias not allowed
      if ($isLang == false) {// není jazyk
         if ($columnOrigName != null){
            $alias = $alias == null ? $columnName : $alias;
            $columnName = $columnOrigName;
         }
         if(preg_match('/[a-z ]+\(/i', $columnName) == 0){ // kontrola jestli není funkce
            $colString = '`' . $tbPrefix . '`.`' . $columnName . '`';
         } else {
            $colString = $columnName;
         }

         if ($alias != null) {
            $colString .= ' AS `' . $alias.'`';
         }
      } else if ($this->getAllLangs == true || VVE_DEFAULT_LANG_SUBSTITUTION) { // více jazyčné sloupce
         $cols = array();
         foreach (Locales::getAppLangs() as $lang) {
            array_push($cols, '`' . $tbPrefix . '`.`' . $columnName . '_' . $lang . '` '.($alias != null ? 'AS '.$alias.'_'.$lang : null));
         }
         $colString = implode(',', $cols);
      } else { // pouze aktuální jazykový sloupec
         $colString = '`' . $tbPrefix . '`.`' . ($columnOrigName == null ? $columnName : $columnOrigName) . '_' . Locales::getLang()
            . '` AS ' . ($alias == null ? $columnName : $alias);
      }
      return $colString;
   }

   protected function createSQLGroupBy(&$sql)
   {
      if (!empty ($this->groupby)) {
         $cols = array();
         foreach ($this->groupby as $tbAlias => $column) {
            if(is_int($tbAlias)){
               if(isset($this->tableStructure[$column])){ // sloupce z tohoto modelu
                  array_push($cols, '`'.$this->getTableShortName().'`.`'.$column.'`');
               } else {
                  array_push($cols, '`'.$column.'`');
               }
            } else {
               array_push($cols, '`'.$tbAlias.'`.`'.$column.'`');
            }
         }
         $sql .= ' GROUP BY '.  implode(',', $cols)."\n";
      }
   }

   /**
    * Metoda vytvoří část sql s LIMIT
    * @param string $sql
    */
   protected function createSQLLimi(&$sql)
   {
      if ($this->limit['from'] !== null) {
         $sql .= ' LIMIT :cube_cms_fromrow, :cube_cms_rows'."\n";
      }
   }

   /**
    * Metoda vytvoří část sql s ORDER
    * @param string $sql
    */
   protected function createSQLOrder(&$sql)
   {
      if (!empty($this->orders)) {
         $ords = array();
         foreach ($this->orders as $col => $order) {
            // pokud obsahuje tečku jedná se o zápis s prefixem tabulky a ten se vkládá přímo
            if (is_int($col)) {// pokud je jenom sloupce je ASC
               $col = $order;
               $order = 'ASC';
            }
            // parsování
            /* Array
              (
              [0] => FUNC(table.column+5)
              [1] => FUNC
              [2] => table
              [3] => column
              [4] => +5 // aditional
              ) */
            $matches = array();
            $ordStr = null;

            if (preg_match('/^(?:([a-zA-Z ]+)?\()?(?:([a-zA-Z0-9_-]+)\.)?([a-zA-Z0-9_]+)([ 0-9\/*+-]+)?\)?$/i', $col, $matches)) {
               // tb prefix
               if ($matches[2] == null) {
                  if(isset ($this->tableStructure[$matches[3]])){ // pokud je sloupec této tabulky
                     $ordStr .= '`' . $this->getTableShortName() . '`.';
                  }
               } else {
                  $ordStr .= '`' . $matches[2] . '`.';
               }
               // coll
               if (isset($this->tableStructure[$matches[3]])) {
                  if ($this->tableStructure[$matches[3]]['aliasFor'] != null) {
                     $column = $this->tableStructure[$matches[3]]['aliasFor'];
                  } else {
                     $column = $matches[3];
                     if ($this->tableStructure[$matches[3]]['lang'] == true) {
                        $column .= '_' . Locales::getLang();
                     }
                  }
               } else {
                  $columnAssigned = false;
                  if(!empty($this->joins)){
                     // jsou připojeny další modely. Projdou se a pokud model daný sloupce obsahuje, nastavíme jej
                     foreach ($this->joins as $join) {
                        $mName = $join['model2'];
                        $coll = $matches[3];
                        $model = new $mName();
                        // model má tento sloupec
                        if(isset($model[$coll])){
                           // alias sloupce
                           if($model[$coll]['aliasFor'] != null){
                              $column = $model[$coll]['aliasFor'];
                           } else {
                              $column = $matches[3];
                           }

                           // jazykový sloupce a jazyk není zadán
                           if ($model[$coll]['lang'] == true) {
                              $column .= '_' . Locales::getLang();
                           }
                           $columnAssigned = true;
                        }
                     }
                  }
                  if(!$columnAssigned){
                     $column = $matches[3];
                  }
               }
               $ordStr .= '`' . $column . '`';
               // dopočty
               if (isset($matches[4]) AND $matches[4] != null) {
                  $ordStr .= $matches[4];
               }
               // funkce
               if ($matches[1] != null) {
                  $ordStr = strtoupper($matches[1]) . '(' . $ordStr . ')';
               }
               // order
               $ordStr .= ' ' . strtoupper($order);
            } else {
               $ordStr .= ' ' . strtoupper($col).' ' . strtoupper($order);
            }
            array_push($ords, $ordStr);
         }
         $sql .= ' ORDER BY ' . implode(',', $ords)."\n";
      }
   }

   /**
    * Metoda vytvoří část sql s WHERE
    * @param <type> $sql
    */
   protected function createSQLWhere(&$sql, $tbShorName = null)
   {
      if ($this->where != null) {
         $retWhere = ' WHERE (';
         $parts = explode(' ', $this->where);
         foreach ($parts as $coll) {
            /**
             * @todo optimalizace na zbytečné znaky
             */
            // přetypování (použití např. u jazyků či změnu hodnoty)
            $retype = null;
            if (strpos($coll, '(') == 0) { // musí být na začátku před řetězcem
               $retype = array();
               preg_match('/^\(([a-z]+)\)(.*)$/i', $coll, $retype);
               if (isset($retype[1])) {
                  $coll = $retype[2];
                  $retype = $retype[1];
               }
            }
            if (isset($this->tableStructure[$coll])) {
               if ($this->tableStructure[$coll]['lang'] == true AND $retype == null) {
                  $coll = $coll . '_' . Locales::getLang();
               } else if ($this->tableStructure[$coll]['lang'] == true AND Locales::isLang($retype)) {
                  $coll = $coll . '_' . $retype;
               }
               // pokud je prefix tabulky
               if ($tbShorName != null) {
                  $retWhere .= '`' . $tbShorName . '`.`' . $coll . '` ';
               } else {
                  $retWhere .= '`' . $coll . '` ';
               }
            } else if (strpos($coll, '.') !== false) {
               // doplnění uvozovek u cizích sloupců
               $retWhere .= preg_replace('/([a-z_-]+)\.([a-z_-]+)/i', '`\1`.`\2`', $coll);
            } else if (preg_match ('/\:[a-z0-9]+/i', $coll, $paramm) != false) {
               // pokud je parametr pole a má pole hodnot
               $paramName = $paramm[0];
               if(isset($this->whereBindValues[$paramName]) && is_array($this->whereBindValues[$paramName])){
                  $whereReplaceStr = array();
                  $values = array_values($this->whereBindValues[$paramName]);
                  unset($this->whereBindValues[$paramName]);
                  $prefix = substr(uniqid(), -8);
                  foreach ($values as $key => $value) {
                     if($value instanceof Model_ORM_Record){
                        $value = $value->getPK();
                     }
                     $keyF = ':'.$prefix.'_arr_'.$key;
                     $this->whereBindValues[$keyF] = $value;
                     $whereReplaceStr[] = $keyF;
                  }
                  $coll = '('.implode(',',$whereReplaceStr).')';
               }
               $retWhere .= $coll . ' ';
            } else {
               $retWhere .= $coll . ' ';
            }
         }
         $retWhere .= ')';
         $sql .= $retWhere."\n";
      }
   }

   protected function createSQLHaving(&$sql)
   {
       if($this->having != null){
          $retHaving = ' HAVING '.$this->having;
          $sql .= $retHaving."\n";
       }
   }

   protected function createSQLJoins(&$sql)
   {
      if ($this->joinString == null)
         $this->createSQLSelectJoinColumns(array()); // provede vytvoření sloupců
      $sql .= $this->joinString; // je vytvořen v přípravě sloupců
   }

   /**
    * Aktualizace sloupce s jazykovými mutacemi
    * @param string $lang -- kód jazyka
    */
   public function updateLangColumns($lang)
   {
      if($this->tableExist()){
         foreach ($this->tableStructure as $colName => $colParams) {
            if($colParams['lang'] && !$this->columnExist($colName.'_'.$lang)){
               $this->createColumn($colName.'_'.$lang, $colParams, $lang);
            }
         }
      }
   }

   public function columnExist($name)
   {
      $sql = 'SELECT COUNT(*) AS count FROM INFORMATION_SCHEMA.COLUMNS
        WHERE table_name = :tablename AND table_schema = DATABASE() AND column_name = :colname';

      $db = $this->getDb()->prepare($sql);
      $db->bindValue(':colname', $name);
      $db->bindValue(':tablename', $this->getTableName());
      $db->setFetchMode(PDO::FETCH_OBJ);
      $db->execute();
      $result = $db->fetch();
      return (isset($result->count) && $result->count > 0 ) ? true : false;
   }

   public function tableExist()
   {
      $sql = 'SELECT COUNT(*) AS count FROM INFORMATION_SCHEMA.TABLES
        WHERE table_name = :tablename AND table_schema = DATABASE()';

      $db = $this->getDb()->prepare($sql);
      $db->bindValue(':tablename', $this->getTableName());
      $db->setFetchMode(PDO::FETCH_OBJ);
      $db->execute();
      $result = $db->fetch();
      return (isset($result->count) && $result->count > 0 ) ? true : false;
   }

   public function createColumn($name, $params = array(), $lang = null)
   {
      $params += self::$defaultColumnParams;
      $datatype = strtoupper($params['datatype']);

      $sql = 'ALTER TABLE '.$this->getTableName();
      // column
      $sql .= ' ADD '.$name.' '.$datatype;
      // kódování
      if(strpos($datatype, 'CHAR') !== false || strpos($datatype, 'TEXT') !== false){
         $collate = $params['collate'];
         // korekce podle jazyka
         if($params['lang'] && $params['collateautodetect']){
            $collate = Locales::getCollation($lang, 'mysql');
         }

         $sql .= ' CHARACTER SET \'utf8\' COLLATE \''.$collate.'\'';
      }
      // výchozí parametry a nulová hodnota
      $sql .= ' '.($params['nn'] ? 'NOT NULL' : 'NULL');
      if($params['default'] !== null){
         $def = $params['default'];
         if(is_bool($def)){
            $def = (int)$def;
         }
         $sql .= ' DEFAULT '.$def;
      }

      // fulltext
      if($params['fulltext']){
         $sql .= ', ADD FULLTEXT INDEX `'.$name.'` (`'.$name.'` ASC)';
      }

      $sql .= ';';
      $this->getDb()->exec($sql);
   }

   /* vložení hodnot so sql částí */

   /**
    * Metoda doplní hodnoty do limit clause
    * @param PDOStatement $stmt
    */
   protected function bindSQLLimit(PDOStatement &$stmt)
   {
      if ($this->limit['from'] !== null) {
         $stmt->bindValue(':cube_cms_fromrow', (int) $this->limit['from'], PDO::PARAM_INT);
         $stmt->bindValue(':cube_cms_rows', (int) $this->limit['rows'], PDO::PARAM_INT);
      }
   }

   /**
    * Metoda doplní hodnoty do where clause
    * @param PDOStatement $stmt
    * @todo použít bindValues nebo bind params
    */
   protected function bindSQLWhere(PDOStatement &$stmt)
   {
      if ($this->where != null AND !empty($this->whereBindValues)) {
         // bind values
         foreach ($this->whereBindValues as $name => $val) {
            $mustBeBindValue = false;
            $varType = gettype($val);
            if($varType == 'boolean'){
               $pdoParam = PDO::PARAM_BOOL;
            } else if($varType == 'integer'){
               $pdoParam = PDO::PARAM_INT;
            } else if($varType == 'NULL'){
               $pdoParam = PDO::PARAM_NULL;
            } else if($varType == 'array'){
               $pdoParam = PDO::PARAM_STMT;
               $val = join(',', $val);
               $mustBeBindValue = true;
            } else if($varType == 'double' || $varType == 'string'){
               $pdoParam = PDO::PARAM_STR;
            } else if($varType == 'object'){
               $pdoParam = PDO::PARAM_STR;
               $mustBeBindValue = true;
               $className = get_class($val);
               if($val instanceof DateTime){
                  $val = $val->format(DATE_ISO8601);
               } else if( method_exists($val , '__toString') ) {
                  $val = (string)$val;
               } else {
                  throw new UnexpectedValueException(sprintf($this->tr('Nepovolený objekt "%s" v předanám paramteru'), $className));
               }
            } else {
               throw new UnexpectedValueException(sprintf($this->tr('Nepovolená hodnota "%s" v předanám paramteru'), $varType));
            }
            $nameParam = $name[0] != ":" ? ':'.$name : $name;
            // bindParam use references http://php.net/manual/en/pdostatement.bindvalue.php#80285
            if (!$mustBeBindValue && $stmt->bindParam($nameParam, $this->whereBindValues[$name], $pdoParam) !== false) {
            } else {
               $stmt->bindValue($nameParam, $val, $pdoParam);
            }
         }
      }
   }


   protected function bindSQLHaving(PDOStatement &$stmt)
   {
      if ($this->having != null AND !empty($this->havingBindValues)) {
         // bind values
         foreach ($this->havingBindValues as $name => $val) {
            $mustBeBindValue = false;
            $varType = gettype($val);
            if($varType == 'boolean'){
               $pdoParam = PDO::PARAM_BOOL;
            } else if($varType == 'integer'){
               $pdoParam = PDO::PARAM_INT;
            } else if($varType == 'NULL'){
               $pdoParam = PDO::PARAM_NULL;
            } else if($varType == 'array'){
               $pdoParam = PDO::PARAM_STMT;
               $val = join(',', $val);
               $mustBeBindValue = true;
            } else if($varType == 'double' || $varType == 'string'){
               $pdoParam = PDO::PARAM_STR;
            } else if($varType == 'object'){
               $pdoParam = PDO::PARAM_STR;
               $mustBeBindValue = true;
               $className = get_class($val);
               if($val instanceof DateTime){
                  $val = $val->format(DATE_ISO8601);
               } else if( method_exists($val , '__toString') ) {
                  $val = (string)$val;
               } else {
                  throw new UnexpectedValueException(sprintf($this->tr('Nepovolený objekt "%s" v předanám paramteru'), $className));
               }
            } else {
               throw new UnexpectedValueException(sprintf($this->tr('Nepovolená hodnota "%s" v předanám paramteru'), $varType));
            }
            $nameParam = $name[0] != ":" ? ':'.$name : $name;
            if (!$mustBeBindValue && $stmt->bindParam($nameParam, $val, $pdoParam) !== false) { // nefunguje při některých where, ale proč??
            } else {
               $stmt->bindValue($nameParam, $val, $pdoParam);
            }
         }
      }
   }

   /**
    * Metoda vloží hodnoty do dotazu
    * @param PDOStatement $stmt
    * @param array $values
    * @return PDOStatement
    */
   protected function bindValues(PDOStatement &$stmt, $values = false)
   {
      if($values == false){
         $values = $this->bindValues;
      }
      foreach ($values as $key => $value) {
         switch (gettype($value)) {
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
            default:
               $pdoParam = PDO::PARAM_STR;
               break;
               break;
         }
         $nameParam = $key[0] != ":" ? ':'.$key : $key;
         $stmt->bindValue($nameParam, $value, $pdoParam);
      }
      return $stmt;
   }

   /**
    * Metoda vytvoří řetězec pro ščasový objekt k uložení do db
    * @param DateTime $dateTimeObj -- časový objekt
    * @param $datatype -- datový typ výstupu (date, time, datetime)
    * @return string -- vytvořený řetězec
    */
   protected function createDateTimeStr(DateTime $dateTimeObj, $datatype)
   {
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

   private function clearModel()
   {
      $this->orders = array();
      $this->joins = array();
      $this->where = null;
      $this->whereBindValues = array();
      $this->limit(null, null);
   }

   public function reset()
   {
      $this->__construct();
   }

   public static function resetModel(Model_ORM &$model)
   {
      $class = get_class($model);
      $model = new $class();
   }

   /**
    * Metoda vrací PDO::PRAMA_ datovy typ
    * @param $val mixed -- proměnná
    * @return int|bool
    */
   private function getPdoParamType($val)
   {
      $varType = gettype($val);
      if ($varType == 'boolean') {
         return PDO::PARAM_BOOL;
      } else if ($varType == 'integer') {
         return PDO::PARAM_INT;
      } else if ($varType == 'NULL') {
         return PDO::PARAM_NULL;
//      } else if ($varType == 'array') {
//         return PDO::PARAM_STMT;
      } else if ($varType == 'double' || $varType == 'string') {
         return PDO::PARAM_STR;
      }
      return false;
   }

   /* callback */

   /**
    * Metoda, která se provede před uložením záznamu do db
    * @param Model_ORM_Record $record
    * @param string $type -- typ uložení (U,I)
    */
   protected function beforeSave(Model_ORM_Record $record, $type = 'U')
   {}

   /**
    * Metoda, která se provede před smazáním záznamu z db
    * @param int $pk -- primary key záznamu
    */
   protected function beforeDelete($pk)
   {}

   /* ARRAY ACCESS */
   public function offsetSet($offset, $value) {
      if (is_null($offset)) {
         $this->tableStructure[] = $value;
      } else {
         $this->tableStructure[$offset] = $value;
      }
   }
   public function offsetExists($offset) {
      return isset($this->tableStructure[$offset]);
   }
   public function offsetUnset($offset) {
      unset($this->tableStructure[$offset]);
   }
   public function offsetGet($offset) {
      return isset($this->tableStructure[$offset]) ? $this->tableStructure[$offset] : null;
   }

   /* STATIC */
   /**
    * Metodav rací pole s výchozími parametry sloupce
    * @return array
    */
   public static function getDefaultColumnParams()
   {
      return self::$defaultColumnParams;
   }

   /**
    * Meoda vrací jazykový sloupec
    * @param $column -- název sloupce
    * @param string $lang -- název jazyka
    * @return string
    */
   public static function getLangColumn($column, $lang = null)
   {
      return $lang == null ? $column.'_'.Locales::getLang() : $column.'_'.$lang;
   }

   /**
    * metoda vrací daný objekt
    * @param int/string $id -- klíč položky
    * @return Model_ORM_Record|false
    * @since 8.0.1
    * @depends PHP 5.3
    */
   public static function getRecord($id) {
      $m = new static();
      return $m->record($id);
   }

   /**
    * metoda vrací nový záznam
    * @return Model_ORM_Record
    * @since 8.0.1
    * @depends PHP 5.3
    */
   public static function getNewRecord() {
      $m = new static();
      return $m->newRecord();
   }

   /**
    * Metoda vrací všechny záznamy z db
    * @return Model_ORM_Record[]
    * @since 8.0.2
    * @depends PHP 5.3
    */
   public static function getAllRecords()
   {
      $m = new static();
      return $m->records();
   }

   /**
    * Metoda vytvoří placeholdery pro klauzuli WHERE IN
    * @return string - řetězec s placeholdery
    */
   public static function getWhereINPlaceholders($array)
   {
      array_walk($array, create_function('&$val,$key', '$val = ":key_".$key;'));
      return implode(',', $array);
   }

   /**
    * Metoda vytvoří pole s hodnotami pro klauzuli WHERE IN
    * @return array pole s elementy pro pro vložení
    */
   public static function getWhereINValues($array)
   {
      $retArray = array();
      foreach ($array as $key => $value){
         $retArray[':key_'.$key] = $value;
      }
      return $retArray;
   }
}
