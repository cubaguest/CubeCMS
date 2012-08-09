<?php
/*
 * Třída modelu detailem článku
*/
class Lecturers_Model extends Model_ORM {
   const DB_TABLE = 'lecturers';

   /**
    * Názvy sloupců v databázi
    */
   const COLUMN_ID = 'id_lecturer';
   const COLUMN_NAME = 'name';
   const COLUMN_SURNAME = 'surname';
   const COLUMN_DEGREE = 'degree';
   const COLUMN_DEGREE_AFTER = 'degree_after';
   const COLUMN_TEXT = 'text';
   const COLUMN_TEXT_CLEAR = 'text_clear';
   const COLUMN_IMAGE = 'image';
   const COLUMN_DELETED = 'deleted';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_lect');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(20)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true, 'fulltextRel' => VVE_SEARCH_ARTICLE_REL_MULTIPLIER));
      $this->addColumn(self::COLUMN_SURNAME, array('datatype' => 'varchar(20)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true, 'fulltextRel' => VVE_SEARCH_ARTICLE_REL_MULTIPLIER));
      $this->addColumn(self::COLUMN_DEGREE, array('datatype' => 'varchar(10)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_DEGREE_AFTER, array('datatype' => 'varchar(10)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TEXT, array('datatype' => 'text', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TEXT_CLEAR, array('datatype' => 'text', 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true));
      $this->addColumn(self::COLUMN_IMAGE, array('datatype' => 'varchar(45)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_DELETED, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => false));
      $this->setPk(self::COLUMN_ID);
//       $this->addRelatioOneToMany(self::COLUMN_ID, 'Articles_Model_PrivateUsers', course::COLUMN_A_H_U_ID_ARTICLE);
   }

   /**
    * Metoda uloží novinku do db
    *
    * @param array -- pole s nadpisem článku
    * @param array -- pole s textem článku
    * @param boolean -- id uživatele
    */
   public function saveLecturer($name, $surname, $degree, $degreeAfter, $text, $image, $id = null) {

      $dbc = Db_PDO::getInstance();
      if($id !== null) {
         $dbst = $dbc->prepare("UPDATE ".Db_PDO::table(self::DB_TABLE)
                 ." SET ".self::COLUMN_NAME."= :name, ".self::COLUMN_SURNAME."= :surname, "
                 .self::COLUMN_DEGREE."= :degree, ".self::COLUMN_DEGREE_AFTER."= :degreeAfter, "
                 .self::COLUMN_TEXT."= :text, "
                 .self::COLUMN_TEXT_CLEAR."= :textclear, ".self::COLUMN_IMAGE."= :image"
                 ." WHERE ".self::COLUMN_ID." = :id");

         $dbst->bindParam(':id', $id, PDO::PARAM_INT);
      } else {
         $dbst = $dbc->prepare("INSERT INTO ".Db_PDO::table(self::DB_TABLE)." "
                 ."(".self::COLUMN_NAME.",". self::COLUMN_SURNAME.","
                 .self::COLUMN_DEGREE.",". self::COLUMN_DEGREE_AFTER.",". self::COLUMN_IMAGE.","
                 .self::COLUMN_TEXT.",". self::COLUMN_TEXT_CLEAR.")"
                 ." VALUES (:name, :surname, :degree, :degreeAfter, :image, :text, :textclear)");
      }
      $dbst->bindValue(':name', $name, PDO::PARAM_STR);
      $dbst->bindValue(':surname', $surname, PDO::PARAM_STR);
      $dbst->bindValue(':degree', $degree, PDO::PARAM_STR|PDO::PARAM_NULL);
      $dbst->bindValue(':degreeAfter', $degreeAfter, PDO::PARAM_STR|PDO::PARAM_NULL);
      $dbst->bindValue(':text', $text, PDO::PARAM_STR);
      $dbst->bindValue(':textclear', vve_strip_tags($text), PDO::PARAM_STR);
      $dbst->bindValue(':image', $image, PDO::PARAM_STR|PDO::PARAM_NULL);

      $dbst->execute();
      if($id === null) $id = $dbc->lastInsertId();

      return $id;
   }

   /**
    * Metoda vrací počet skupin
    *
    * @return integer -- počet
    */
   public function getCount() {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->query("SELECT COUNT(".self::COLUMN_ID.")"." FROM ".Db_PDO::table(self::DB_TABLE)." WHERE (".self::COLUMN_DELETED." = 0)");
      $count = $dbst->fetch();
      return $count[0];
   }

   /**
    * Metoda vrací pole se všemi skupinami
    * @return array -- pole skupin
    */
   public function getList($fromRow = 0, $rowsCount = 100) {
      $dbc = Db_PDO::getInstance();

      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)
              ." WHERE ".self::COLUMN_DELETED." = 0"
              ." ORDER BY ".self::COLUMN_SURNAME." ASC"
              ." LIMIT :fromRow, :rowCount");

      $dbst->setFetchMode(PDO::FETCH_OBJ);
      $dbst->bindValue(':fromRow', (int)$fromRow, PDO::PARAM_INT);
      $dbst->bindValue(':rowCount', (int)$rowsCount, PDO::PARAM_INT);
      $dbst->execute();

      return $dbst->fetchAll();
   }

   /**
    * Metoda vrací skupinu podle zadaného klíče
    *
    * @param string -- url klíč skupiny
    * @return PDOStatement -- pole s skupinou
    */
   public function getLecturer($id) {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)
              ." WHERE (".self::COLUMN_ID." = :idl)"
              ." LIMIT 0, 1");

      $dbst->bindParam(':idl', $id, PDO::PARAM_INT);
      $dbst->execute();

      $dbst->setFetchMode(PDO::FETCH_OBJ);
      return $dbst->fetch();
   }

   /**
    * Metoda smaže zadanou kapelu
    * @param integer $id
    * @return bool
    */
   public function deleteLecturer($id) {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare("UPDATE ".Db_PDO::table(self::DB_TABLE)
                 ." SET ".self::COLUMN_DELETED."= 1"
                 ." WHERE ".self::COLUMN_ID." = :id");
//      $dbst = $dbc->prepare("DELETE FROM ".Db_PDO::table(self::DB_TABLE)
//              ." WHERE (".self::COLUMN_ID ." = :id)");
      $dbst->bindParam(':id', $id, PDO::PARAM_INT);
      return $dbst->execute();
   }

   /**
    * Metoda provede náhodný výběr klipu
    * @return <type>
    */
   public function getRandomCLip() {
      $retClip = null;

      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)
              ." WHERE (ISNULL(".self::COLUMN_CLIPS.") = 0)"
              ." ORDER BY RAND() LIMIT 0, 1");
      $dbst->execute();
      $fetch = $dbst->fetchObject();
      if($fetch != false){
         $clipsArr = explode(self::CLIPS_SEPARATOR, (string)$fetch->{self::COLUMN_CLIPS});
         $fetch->{self::COLUMN_CLIPS} = $clipsArr[rand(0, count($clipsArr)-1)];
      }

      return $fetch;
   }

   /**
    * Metoda provede náhodný výběr skupiny
    * @return Object
    */
   public function getRandomBand() {
      $retBand = null;
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)
              ." ORDER BY RAND() LIMIT 0, 1");
      $dbst->execute();
      return $dbst->fetchObject();
   }

   /**
    * Metoda nastaví změnu článku
    * @param int $id -- id článku
    */
   public function setLastChange($idArt) {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare("UPDATE ".Db_PDO::table(self::DB_TABLE)
              ." SET `".self::COLUMN_EDIT_TIME."` = NOW()"
              ." WHERE (".self::COLUMN_ID." = :idart)");
      $dbst->bindParam(':idart', $idArt, PDO::PARAM_INT);
      return $dbst->execute();
   }
}

?>