<?php
/**
 * Třída Modelu pro práci s panely modulů.
 * Třída, která umožňuje pracovet s modely panelů
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.2 $Revision$
 * @author			$Author$ $Date$
 *						$LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída pro vytvoření modelu pro práci s panely
 */

class Model_Panel extends Model_PDO {
/**
 * Tabulka s detaily
 */
   const DB_TABLE = 'panels';

   /**
    * Konstanty s názzvy sloupců
    */
   const COLUMN_ID = 'id_panel';
   const COLUMN_ID_CAT = 'id_cat';
   const COLUMN_ID_SHOW_CAT = 'id_show_cat';
   const COLUMN_ORDER = 'porder';
   const COLUMN_POSITION    = 'position';

   /**
    * Metoda uloží panel
    * @param int $idCat -- id kategorie ke které panel patří
    * @param string $pPosition -- pozice panelu (název boxu)
    * @param int $order -- pořadí panelu
    * @param int $idPanel -- id panelu
    * @param int $idShowCat -- id kategorie ve které má být panel zobrazen (požije se pouze
    * u kategorií s individuáním nasatvením panelů)
    * @return bool -- jestli byl záznam uložen nebo id posledního vloženého záznamu
    */
   public function savePanel($idCat, $pPosition, $order = 0, $idPanel = null, $idShowCat = null) {
      $this->setIUValues(array(self::COLUMN_ID_CAT => $idCat, self::COLUMN_POSITION => $pPosition,
          self::COLUMN_ORDER => $order));
      if($idShowCat !== null) {
         $this->setIUValues(array(self::COLUMN_ID_SHOW_CAT => $idShowCat));
      }

      $dbc = new Db_PDO();
      // ukládá se nový
      if($idPanel === null) {
         $dbc->exec("INSERT INTO ".Db_PDO::table(self::DB_TABLE)
             ." ".$this->getInsertLabels()." VALUES ".$this->getInsertValues());
         return $dbc->lastInsertId();
      } else {
         $dbst = $dbc->prepare("UPDATE ".Db_PDO::table(self::DB_TABLE)
          ." SET ".$this->getUpdateValues()
          ." WHERE ".self::COLUMN_ID." = :id");

         return $dbst->execute(array(':id' => $idPanel));
         return true;
      }
   }

   /**
    * Metoda načte všechny kategorie
    * @param bool $allCategories -- jestli mají být vráceny všechny kategorie
    * @param bool $withRights -- jestli mají být načtený pouze kategorie na které ma uživatel práva
    * @return PDOStatement -- objekt s daty
    */
   public function getPanelsList($idCat = 0, $withRights = true) {
      $dbc = new Db_PDO();

      if($withRights){
      $dbst = $dbc->query("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)." AS panel
         JOIN ".Db_PDO::table(Model_Category::DB_TABLE)." AS cat ON cat.".Model_Category::COLUMN_CAT_ID." = panel.".self::COLUMN_ID_CAT
          ." WHERE (cat.".Model_Category::COLUMN_GROUP_PREFIX.AppCore::getAuth()->getGroupName()." LIKE 'r__')"
          ." AND (panel.".self::COLUMN_ID_SHOW_CAT." = ".$idCat.")"
          ." ORDER BY panel.".self::COLUMN_POSITION." ASC, panel.".self::COLUMN_ORDER." ASC");
      } else {
         $dbst = $dbc->query("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)." AS panel
         JOIN ".Db_PDO::table(Model_Category::DB_TABLE)." AS cat ON cat.".Model_Category::COLUMN_CAT_ID." = panel.".self::COLUMN_ID_CAT
          ." WHERE (panel.".self::COLUMN_ID_SHOW_CAT." = ".$idCat.")"
          ." ORDER BY panel.".self::COLUMN_POSITION." ASC, panel.".self::COLUMN_ORDER." ASC");
      }

      $dbst->execute();
      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
      return $dbst;
   }

   public function deletePanel($id) {
      $dbc = new Db_PDO();
      $st = $dbc->prepare("DELETE FROM ".Db_PDO::table(self::DB_TABLE)
          . " WHERE ".self::COLUMN_ID." = :id");
      return $st->execute(array(':id' => $id));
   }
}

?>