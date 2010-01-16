<?php
/*
 * Třída modelu s detailem textu
 * 
*/
class Text_Model_Detail extends Model_PDO {
   /**
    * Tabulka s detaily
    */
   const DB_TABLE = 'texts';

   /**
    * Názvy sloupců v db
    * @var string
    */
   const COLUMN_ID_CATEGORY = 'id_item';
   const COLUMN_SUBKEY = 'subkey';
   const COLUMN_CHANGED_TIME = 'changed';
   const COLUMN_TEXT = 'text';
   const COLUMN_TEXT_CLEAR = 'text_clear';
   const COLUMN_LABEL = 'label';
   const COLUMN_TEXT_PANEL = 'text_panel';

   /**
    * Metoda provede načtení textu z db
    *
    * @return string -- načtený text
    */
   public function getText($idCat) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)." AS text
             WHERE (text.".self::COLUMN_ID_CATEGORY." = :idCat)");
      $dbst->bindParam(':idCat', $idCat, PDO::PARAM_INT);
      $dbst->execute();

      $cl = new Model_LangContainer();
      $dbst->setFetchMode(PDO::FETCH_INTO, $cl);
      return $dbst->fetch();
   }

   public function saveText($texts, $label, $panelText, $idCat, $subKey = 'NULL') {
      // zjištění jestli existuje záznam
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)
              ." WHERE (".self::COLUMN_ID_CATEGORY." = :idCat AND ".self::COLUMN_SUBKEY." = :subkey)");
      //      if($subKey === null) {
      $dbst->bindValue(':subkey', $subKey, PDO::PARAM_STR);
      //      } else {
      //         $dbst->bindValue(':subkey', $subKey);
      //      }
      $dbst->bindValue(':idCat', $idCat, PDO::PARAM_INT);
      $dbst->execute();
      $count = $dbst->rowCount();
      //      $count = $dbst->fetch();
      //      $count = $count[0];
      //          exit;
      // globalní prvky
      $dbc = new Db_PDO();
      $this->setIUValues(array(self::COLUMN_TEXT => $texts, self::COLUMN_SUBKEY => $subKey,
         self::COLUMN_TEXT_CLEAR => vve_strip_tags($texts)));

      if($label !== null) {
         $this->setIUValues(array(self::COLUMN_LABEL => $label));
      }
      if($panelText !== null) {
         $this->setIUValues(array(self::COLUMN_TEXT_PANEL => $panelText));
      }

      if($count != 0) {
         // je už uloženo
         $dbst = $dbc->prepare("UPDATE ".Db_PDO::table(self::DB_TABLE)
                 ." SET ".$this->getUpdateValues()." WHERE ".self::COLUMN_ID_CATEGORY." = ".(int)$idCat);
         //             ." WHERE ".self::COLUMN_ID_CATEGORY." = :category");
         //$dbst->bindParam(1, $idCat, PDO::PARAM_INT);
         $dbst->execute();
      } else {
         // není uloženo
         $this->setIUValues(array(self::COLUMN_ID_CATEGORY => $idCat));
         $dbc->query("INSERT INTO ".Db_PDO::table(self::DB_TABLE)
                 ." ".$this->getInsertLabels()." VALUES ".$this->getInsertValues());
         return $dbc->lastInsertId();
      }
   }

   public function getLastChange($idCat) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT ".self::COLUMN_CHANGED_TIME." AS tm FROM ".Db_PDO::table(self::DB_TABLE)
              ." WHERE ".self::COLUMN_ID_CATEGORY." = :idcategory");
      $dbst->bindParam(':idcategory', $idCat);
      $dbst->execute();
      $fetch = $dbst->fetchObject();
      if($fetch == false) {
         return false;
      } else {
         return $fetch->tm;
      }
   }

   /**
    * Metoda provede hledání textu
    * @param integer $idCat -- id kategorie
    * @param string $string -- hledaný řetězec
    * @return PDOStatement
    */
   public function search($idCat, $string) {
      $dbc = new Db_PDO();
      $clabel = self::COLUMN_LABEL.'_'.Locale::getLang();
      $ctext = self::COLUMN_TEXT_CLEAR.'_'.Locale::getLang();

      $dbst = $dbc->prepare('SELECT *, ('.round(VVE_SEARCH_ARTICLE_REL_MULTIPLIER/2).' * MATCH(`'.$clabel.'`) AGAINST (:sstring)'
              .' + MATCH(`'.$ctext.'`) AGAINST (:sstring)) as '.Search::COLUMN_RELEVATION
              .' FROM '.Db_PDO::table(self::DB_TABLE)
              .' WHERE MATCH(`'.$clabel.'`, `'.$ctext.'`) AGAINST (:sstring IN BOOLEAN MODE)'
              .' AND `'.self::COLUMN_ID_CATEGORY.'` = :idCat'
              .' ORDER BY '.round(VVE_SEARCH_ARTICLE_REL_MULTIPLIER/2)
              .' * MATCH(`'.$clabel.'`) AGAINST (:sstring) + MATCH(`'.$ctext.'`) AGAINST (:sstring) DESC');

      $dbst->bindValue(':idCat', $idCat, PDO::PARAM_INT);
      $dbst->bindValue(':sstring', $string, PDO::PARAM_STR);
      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
      $dbst->execute();

      return $dbst->fetch();
   }
}

?>