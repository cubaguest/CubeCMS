<?php
/*
 * Třída modelu s detailem textu
 * 
*/
class Text_Model extends Model_PDO {
   /**
    * Tabulka s detaily
    */
   const DB_TABLE = 'texts';

   /**
    * Názvy sloupců v db
    * @var string
    */
   const COLUMN_ID = 'id_text';
   const COLUMN_ID_CATEGORY = 'id_item';
   const COLUMN_SUBKEY = 'subkey';
   const COLUMN_CHANGED_TIME = 'changed';
   const COLUMN_TEXT = 'text';
   const COLUMN_TEXT_CLEAR = 'text_clear';
   const COLUMN_LABEL = 'label';

   const DEFAULT_SUBKEY = 'nokey';

   /**
    * Metoda provede načtení textu z db podle kategorie a subklíče
    *
    * @return string -- načtený text
    */
   public function getText($idCat, $subkey = self::DEFAULT_SUBKEY) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)." AS text
             WHERE (text.".self::COLUMN_ID_CATEGORY." = :idc AND text.".self::COLUMN_SUBKEY." = :subkey)");
      $dbst->bindValue(':subkey', $subkey, PDO::PARAM_STR);
      $dbst->bindValue(':idc', $idCat, PDO::PARAM_INT);
      $dbst->execute();

      $cl = new Model_LangContainer();
      $dbst->setFetchMode(PDO::FETCH_INTO, $cl);
      return $dbst->fetch();
   }

   /**
    * Metoda provede načtení textu z db podle id
    *
    * @return string -- načtený text
    */
   public function getTextId($id, $subkey = self::DEFAULT_SUBKEY) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)." AS text
             WHERE (text.".self::COLUMN_ID." = :id AND text.".self::COLUMN_SUBKEY." = :subkey)");
      $dbst->bindValue(':subkey', $subkey, PDO::PARAM_STR);
      $dbst->bindValue(':id', $id, PDO::PARAM_INT);
      $dbst->execute();

      $cl = new Model_LangContainer();
      $dbst->setFetchMode(PDO::FETCH_INTO, $cl);
      return $dbst->fetch();
   }

   /**
    * Metoda ukloží zadaný text do db
    * @param <type> $texts
    * @param <type> $label
    * @param <type> $idCat
    * @param <type> $subKey
    * @param <type> $id
    * @return <type>
    */
   public function saveText($texts, $label, $idCat, $subKey = self::DEFAULT_SUBKEY, $id = null) {
      // zjištění jestli existuje záznam
      // globalní prvky
      $dbc = new Db_PDO();
      $this->setIUValues(array(self::COLUMN_TEXT => $texts,
          self::COLUMN_TEXT_CLEAR => vve_strip_tags($texts)));
      if ($label !== null) {
          $this->setIUValues(array(self::COLUMN_LABEL => $label));
      }
      
      if ($id === null) {
         $dbst = $dbc->prepare("SELECT * FROM " . Db_PDO::table(self::DB_TABLE)
                 . " WHERE (" . self::COLUMN_ID_CATEGORY . " = :idCat AND "
                 . self::COLUMN_SUBKEY . " = :subkey)");
         $dbst->bindValue(':subkey', $subKey, PDO::PARAM_STR);
         $dbst->bindValue(':idCat', $idCat, PDO::PARAM_INT);
         $dbst->execute();
         $data = $dbst->fetchObject();

//         var_dump($data);flush();exit();

         $count = $dbst->rowCount();

         

         if ($count != 0) {
            // je už uloženo
            $dbst = $dbc->prepare("UPDATE " . Db_PDO::table(self::DB_TABLE)
                            . " SET " . $this->getUpdateValues()
                            . " WHERE (" . self::COLUMN_ID_CATEGORY . " = :idCat AND " . self::COLUMN_SUBKEY . " = :subkey)");
            $dbst->execute(array(':idCat' => $idCat, ':subkey' => $subKey));
            $id = $data->{self::COLUMN_ID};
         } else {
            // není uloženo
            $this->setIUValues(array(self::COLUMN_ID_CATEGORY => $idCat, self::COLUMN_SUBKEY => $subKey));
            $dbc->query("INSERT INTO " . Db_PDO::table(self::DB_TABLE)
                    . " " . $this->getInsertLabels() . " VALUES " . $this->getInsertValues());
            $id = $dbc->lastInsertId();
         }
      } else {
         // je už uloženo
         $dbst = $dbc->prepare("UPDATE " . Db_PDO::table(self::DB_TABLE)
                         . " SET " . $this->getUpdateValues()
                         . " WHERE (" . self::COLUMN_ID . " = :id AND " . self::COLUMN_SUBKEY . " = :subkey)");
         $dbst->execute(array(':id' => (int)$id, ':subkey' => $subKey));
         $id = $dbc->lastInsertId();
      }

      return $id;
   }

   public function getLastChange($idCat, $subKey = self::DEFAULT_SUBKEY) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT ".self::COLUMN_CHANGED_TIME." AS tm FROM ".Db_PDO::table(self::DB_TABLE)
              ." WHERE (".self::COLUMN_ID_CATEGORY." = :idcategory AND ".self::COLUMN_SUBKEY." = :subkey)");
//      $dbst->bindParam(':idcategory', $idCat);
      $dbst->execute(array(':idcategory' => $idCat, ':subkey' => $subKey));
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
      $clabel = self::COLUMN_LABEL.'_'.Locales::getLang();
      $ctext = self::COLUMN_TEXT_CLEAR.'_'.Locales::getLang();

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