<?php
require_once './lib/db/delete.class.php';
/**
 * Třída pro odstraňování záznamů v MySQL DB.
 * Třída obsahuje implementaci metody delete z db.interfacu a všech metod
 * z rozhraní Db_Delete
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.2 $Revision$
 * @author			$Author$ $Date$
 *						$LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída pro mazání záznamů
 * @see           http://dev.mysql.com/doc/refman/5.1/en/delete.html
 * @package       mysqli
 */

class Mysqli_Db_Delete extends Mysqli_Db_Query implements Db_Delete {
   /**
    * Konstanta určující obsah SQL dotazu
    * Musí mít správné pořadí, jak se má SQL dotaz řadit!!!
    *
    */
   protected static $_sqlPartsInit = array(
      parent::INDEX_TABLE			=> array(),
      parent::SQL_WHERE				=> array(),
      parent::ORDER_ORDER_KEY		=> array(),
      parent::SQL_LIMIT				=> array());

   /**
    * Pole s zadávanými částmi SQL dotazu
    * @var array
    */
   protected $_sqlQueryParts = array();

   /**
    * Inicializace
    */
   protected function init() {
      $this->_sqlQueryParts = self::$_sqlPartsInit;
   }

   /**
    * Metoda nastavuje z které tabulky se bude mazat
    * klauzule FROM
    *
    * @param string -- tabulka pro použití
    * @param boolean -- (option) jestli se májí tabulky zamknout
    *
    * @return Db_Delete -- objekt Db_Delete
    */
   public function table($table, $lockTable = false){
      return parent::setTable($table, null, $lockTable);
   }

   /**
    * Metody vatváří podmínku WHERE. Pokud je třeba použít více podmínek, je
    * zadáváno pole v tomot poředí např:
    * where(array($cola,$valuea,'='),'AND',array(array($colb,$valueb,'='),'OR',array($colc,$valuec,'=')))
    * WHERE ($cola = $valuea) AND (($colb = $valueb) OR ($colc = $valuec))
    * @param string -- sloupcec
    * @param char/int -- operátor porovnávání konstatna Db::OPERATOR_XXX
    * @param int -- operátor porovnávání Db::COND_OPERATOR_XXX
    *
    * @return Db_Delete -- objekt Db_Delete
    */
   public function where($column, $value = null, $term = '=', $operator = Db::COND_OPERATOR_AND) {
      if(is_array($column)){
         return parent::where(func_get_args());
      } else {
         return parent::where($column, $value, $term, $operator);
      }
   }

   /**
    * Metoda přiřadí řazení sloupcu v SQL dotazu
    *
    * @param string -- sloupec, podle kterého se má řadit
    * @param string -- (option) jak se má sloupec řadit (ASC, DESC) (default: ASC)
    *
    * @return Db_Delete -- objekt Db_Delete
    */
   public function order($colum, $order = Db::ORDER_ASC) {
      return parent::order($rowCount, $offset);
   }

   /**
    * Metoda přidá do SQL dotazu klauzuli LIMIT
    * @param integer $startRow -- záčátek
    * @param integer $offset -- počet záznamů
    *
    * @return Db_Delete -- objekt Db_Delete
    */
   public function limit($startRow, $offset) {
      return parent::limit($startRow, $offset);
   }

   /**
     * Metoda převede objekt na řetězec
     * @return string -- objekt jako řetězec
     */
   public function __toString() {
      $sql = parent::SQL_DELETE;
      // tabulka
      $sql .= parent::SQL_SEPARATOR.parent::SQL_FROM.parent::SQL_SEPARATOR.$this->_createTable(false);
      // where
      $sql .= $this->_createWhere();
      // order
      $sql .= $this->_createOrder();
      // limit
      $sql .= $this->_createLimit();
      return $sql;
   }
}
?>