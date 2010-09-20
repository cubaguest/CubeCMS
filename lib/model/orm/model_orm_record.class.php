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

class Model_ORM_Record {
   protected $columns = array();

   protected $pKeyValue = null;

   protected $fromDb = false;

   public function  __construct($columns, $fromDb = false) {
      $this->columns = $columns;
      $this->fromDb = $fromDb;
   }

   /**
    * Magic pro nastavení slupce
    * @param <type> $name
    * @param <type> $value
    */
   public function  __set($collName, $value) {
      if($this->columns[$collName]['lang'] == true){
         if (preg_match("/^(.*)_([a-z]{2})$/i", $name, $matches)) {
//            if(!isset ($this->values[$matches[1]])
//             OR !($this->values[$matches[1]] instanceof Model_LangContainer_LangColumn)) {
//               $this->values[$matches[1]] = new Model_LangContainer_LangColumn();
//            }
//            $this->values[$matches[1]]->addValue($matches[2], $value);
         }
      } else {
         // tady kontroly sloupců
         if($this->fromDb == true){
            $this->columns[$collName]['valueLoaded'] = $value;
         }
         $this->columns[$collName]['value'] = $value;
      }
      if($this->columns[$collName]['pk'] == true){// primary key
         $this->pKeyValue = $value;
      }
   }

   /**
    * Magic pro vybrání hodnoty slupce
    * @param <type> $name
    * @param <type> $value
    */
   public function  __get($collName) {
      // tady kontroly sloupců
      return $this->columns[$collName]['value'];
   }

   public function getPK() {
      return $this->pKeyValue;
   }

   public function getColumns() {
      return $this->columns;
   }

   /**
    * Metoda vrací jestli se jedná o nový záznam
    * @return bool
    */
   public function isNew() {
      if($this->pKeyValue == null){
         return true;
      }
      return false;
   }
}
?>