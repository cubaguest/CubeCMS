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

   protected $tableName = null;

   private $tableStructure = array();

   private $tableRelations = array();

   private $pKey = null;

   private $foreignKeys = array();

   private $defaultColumnParams = array(
      'datatype' => 'VARCHAR(45)', // typ sloupce
      'pdoparam' => PDO::PARAM_STR, // typ sloupce (PDO::PARAM_)
      'nn' => false, // non null
      'default' => null, // default value
      'alias' => null, // alias sloupce
      'ai' => false,
      'uq' => false,
      'pk' => false,
      'lang' => false,
      'comment' => null,
      'lenght' => null,
      'readonly' => false,
      'value' => false,
      'valueLoaded' => false,
      'name' => null
   );

   public function  __construct() {
      parent::__construct();
      $this->_initTable();
   }

   protected function _initTable() {
      $this->setTableName(self::DB_TABLE);
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
      $this->tableStructure[$name] = array_merge($this->defaultColumnParams, $params);
   }

   /**
    * Metoda přidá napojení na jinou tabulku přes klíč
    * @param string $column - název sloupce
    * @param string $modelName - název modelu připojené tabulky
    * @param string $externColumn - název sloupce připojené tabulky
    */
   protected function addForeignKey($column, $modelName, $externColumn, $alias = null) {
      $this->foreignKeys[$column] = array('modelName' => $modelName, 'modelColumn' => $externColumn, 'columnAlias' => $alias);
   }


   /**
    * nasatví primary key
    * @param string $collname -- název sloupce
    */
   protected function setPk($collname) {
      $this->pKey = &$this->tableStructure[$collname];
      $this->pKey['pk'] = true;
   }

   /**
    * Metoda nastavuje název tabulky
    * @param string $tablename -- název tabulky bez prefixu
    */
   protected function  setTableName($tablename) {
      $this->tableName = Db_PDO::table($tablename);
   }

   /**
    * Metoda vrací název tabulky i s prefixem
    * @return string
    */
   protected function getTableName() {
      return $this->tableName;
   }

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
   public function record($pk = null, $value = null, $order = null) {
      $record = false;
      if(is_array($pk)) {
         // pole se sloupc => hodnota
         $record = $this->getRow($pk, $order);
      } else if($pk !== null AND $value !== null){
         // sloupec, hodnota
            $record = $this->getRow(array($pk => $value), $order);
      } else if($pk !== null) {
         // jenom pk
         $record = $this->getRow(array($this->pKey['name'] => $pk), $order);
      } else {
         // čistý record
         $record = new Model_ORM_Record($this->tableStructure);
      }
      return $record;
   }

   protected function getRow($array = array(), $orders = null , $cond = 'AND', $limitStr = ' LIMIT 0,1') {
      $dbc = new Db_PDO();
      // názvy sloupců
      $colums = array();
      foreach ($this->tableStructure as $columnName => $params) {
         if($params['alias'] == null){
            array_push($colums, '`'.$columnName.'`');
         } else {
            array_push($colums, '`'.$columnName.'` AS '.$params['alias']);
         }
      }

      $sql = 'SELECT '.  implode(',', $colums).' FROM '.$this->getTableName();
      unset ($colums);

      $sqlOrder = null;
      if($orders != null){
         $ords = array();
         foreach ($orders as $col => $val) {
            if(strtoupper($val) != 'ASC' AND strtoupper($val) != 'DESC') throw new UnexpectedValueException (sprintf (_('Nepodporovaný typ řazení "%s"'), $val));
            array_push($conds, '`'.$col.'` '.$val);
         }
         $sqlOrder = ' ORDER BY '.implode(',', $ords);
      }

      if(!empty ($array)){
         $conds = array();
         foreach ($array as $colname => $value) {
            array_push($conds, '`'.$colname.'` = :'.$colname);
         }
         $sql .= ' WHERE '.  implode($cond, $conds);
         $dbst = $dbc->prepare($sql.$sqlOrder.$limitStr);
         foreach ($array as $colname => $value) {
            $dbst->bindValue(':'.$colname, $value, $this->tableStructure[$colname]['pdoparam']);
         }
      } else {
         $dbst = $dbc->prepare($sql.$sqlOrder.$limitStr);
      }
      $dbst->execute();
      $dbst->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Model_ORM_Record', array($this->tableStructure, true));
      return $dbst->fetch();
   }

   /**
    * Metoda vrací záznamy z db
    * @param int $fromRow -- od záznamu
    * @param int $rows -- počet záznamů
    * @param string/array $conds -- podmínky
    * @param array $orders -- řazení
    * @return <type>
    */
   public function records($fromRow = 0, $rows = null, $conds = null, $orders = null) {
      $dbc = new Db_PDO();
      // názvy sloupců
      $colums = array();
      foreach ($this->tableStructure as $columnName => $params) {
         if($params['alias'] == null){
            array_push($colums, '`'.$columnName.'`');
         } else {
            array_push($colums, '`'.$columnName.'` AS '.$params['alias']);
         }
      }

      $sql = 'SELECT '.  implode(',', $colums).' FROM '.$this->getTableName();
      unset ($colums);

      $orderSql = null;
      if($orders != null){
         $ords = array();
         foreach ($orders as $col => $val) {
            if(strtoupper($val) != 'ASC' AND strtoupper($val) != 'DESC') throw new UnexpectedValueException (sprintf (_('Nepodporovaný typ řazení "%s"'), $val));
            array_push($ords, '`'.$col.'` '.strtoupper($val));
         }
         $orderSql = ' ORDER BY '.implode(',', $ords);
      }

      if(is_array($conds)){
         // create sql
         $cds = array();
         foreach ($conds as $col => $val) {
            array_push($cds, '`'.$col.'` = :'.$col);
         }
         $sql .= $sql.' WHERE '.implode(' AND ', $conds).$orderSql;
         
         if($rows != null){
            $sql .= ' LIMIT :fromRow, :rows';
         }

         $dbst = $dbc->prepare($sql);
         // bind values
         foreach ($conds as $col => $val) {
            $dbst->bindValue(':'.$col, $val, $this->tableStructure[$col]['pdoparam']);
         }
      } else {
         $sql .= $orderSql;
         if($rows != null){
            $sql .= ' LIMIT :fromRow, :rows';
         }
         $dbst = $dbc->prepare($sql);
      }
      if($rows != null){
         $dbst->bindValue(':fromRow', $fromRow, PDO::PARAM_INT);
         $dbst->bindValue(':rows', $rows, PDO::PARAM_INT);
      }
      $dbst->execute();
      $dbst->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Model_ORM_Record', array($this->tableStructure, true));
      $r = $dbst->fetchAll();


      return$r;
   }


   public function save(Model_ORM_Record $record) {
      $dbc = new Db_PDO();
      $returnPk = $record->getPK();
      if($record->getPK() != null){
         // UPDATE

      } else {
         // INSERT
         $sql = "INSERT INTO ". $this->getTableName();
         $colsStr = array(); $bindParamStr = array();
         // create query
         foreach ($record->getColumns() as $colname => $params) {
            if($params['value'] == $params['valueLoaded']) continue;
            if($params['lang'] === true){

            } else {
               array_push($colsStr, '`'.$colname.'`');
               array_push($bindParamStr, ':'.$colname);
            }
         }
         $dbst = $dbc->prepare($sql.' ('.  implode(',', $colsStr).') VALUES ('.  implode(',', $bindParamStr).')');
         // bind values
         foreach ($record->getColumns() as $colname => $params) {
            if($params['value'] == $params['valueLoaded']) continue;
            $value = $params['value'];
            if($params['lang'] == true){

            } else {
               // date clumns
               if($value instanceof DateTime){
                  if($params['datatype'] == 'date'){
                     $value = $value->format('Y-m-d');
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
    * @return bool
    */
   public function delete($pk) {
      $dbc = new Db_PDO();
      if($pk instanceof Model_ORM_Record){
         // asi vatáhnout pk (bude muset být uloženo v rekordu)
         $dbst = $dbc->prepare('DELETE FROM '.$this->getTableName()
            .' WHERE `'.$this->pKey['name'].'` = :pk');
         $dbst->bindValue(':pk', $pk->getPK(), $this->pKey['pdoparam']);
      } else {
         $dbst = $dbc->prepare('DELETE FROM '.$this->getTableName()
            .' WHERE `'.$this->pKey['name'].'` = :pk');
         $dbst->bindParam(':pk', $pk, $this->tableStructure[$this->pKey]['pdoparam']);
      }
      return $dbst->execute();
   }

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
}
?>