<?php
/*
 * Třída modelu detailem článku
 */
class Articles_Model_Detail extends Model_PDO {
   const DB_TABLE = 'articles';

/**
 * Názvy sloupců v databázi
 */
   const COLUMN_NAME = 'name';
   const COLUMN_TEXT = 'text';
   const COLUMN_URLKEY = 'urlkey';
   const COLUMN_ADD_TIME = 'add_time';
   const COLUMN_EDIT_TIME = 'edit_time';
   const COLUMN_ID_USER = 'id_user';
   const COLUMN_ID_USER_LAST_EDIT = 'is_user_last_edit';
   const COLUMN_ID_CATEGORY = 'id_cat';
   const COLUMN_ID = 'id_article';
   const COLUMN_SHOWED = 'viewed';

   /**
    * Metoda uloží novinku do db
    *
    * @param array -- pole s nadpisem článku
    * @param array -- pole s textem článku
    * @param boolean -- id uživatele
    */
   public function saveArticle($name, $text, $urlKey, $idCat = 0, $idUser = 0, $id = null) {
      // globalní prvky
      $this->setIUValues(array(self::COLUMN_NAME => $name,self::COLUMN_TEXT => $text,
             self::COLUMN_URLKEY => $urlKey, self::COLUMN_EDIT_TIME => time()));

      $dbc = new Db_PDO();

      if($id !== null) {
         $this->setIUValues(array(self::COLUMN_ID_USER_LAST_EDIT => $idUser));

         $dbst = $dbc->prepare("UPDATE ".Db_PDO::table(self::DB_TABLE)
          ." SET ".$this->getUpdateValues()
          ." WHERE ".self::COLUMN_ID." = :id");
         $dbst->bindParam(':id', $id, PDO::PARAM_INT);
         return $dbst->execute();
      } else {
         if($idCat == 0){
            throw new InvalidArgumentException($this->_('Při ukládání nového článku musí být zadáno id'), 1);
         }
         // unikátní klíč
//         $dbc = new Db_PDO();
//         // načtu všechny existující url klíče
//         $dbst = $dbc->query("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)."
//             WHERE (".self::COLUMN_ID_CATEGORY." = '".$idCat."')");
//
//         while($row = $dbst->fetch()){
//            $cats[$row->{Model_Category::COLUMN_CAT_ID}] = $row;
//         }

         $this->setIUValues(array(self::COLUMN_ID_CATEGORY => $idCat,
               self::COLUMN_ID_USER => $idUser,
               self::COLUMN_ADD_TIME => time()));

         $dbc->exec("INSERT INTO ".Db_PDO::table(self::DB_TABLE)
             ." ".$this->getInsertLabels()." VALUES ".$this->getInsertValues());

         return $dbc->lastInsertId();
      }
   }

   /**
    * Metoda vrací id posledního vloženého článku
    * @return integer -- id článku
    */
   public function addShowCount($urlKey) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("UPDATE ".Db_PDO::table(self::DB_TABLE)
          ." SET ".self::COLUMN_SHOWED." = ".self::COLUMN_SHOWED."+1"
          ." WHERE (".self::COLUMN_URLKEY."_".Locale::getLang()." = :urlkey"
          ." OR ".self::COLUMN_URLKEY."_".Locale::getDefaultLang()." = :urlkey2)");
      $dbst->bindParam(':urlkey', $urlKey, PDO::PARAM_STR);
      $dbst->bindParam(':urlkey2', $urlKey, PDO::PARAM_STR);
      return $dbst->execute();
   }

   /**
    * Metoda vrací článek podle zadaného klíče
    *
    * @param string -- url klíč článku
    * @return PDOStatement -- pole s článkem
    */
   public function getArticle($urlKey) {
      $dbc = new Db_PDO();
         $dbst = $dbc->prepare("SELECT article.*, user.".Model_Users::COLUMN_USERNAME
         ." FROM ".Db_PDO::table(self::DB_TABLE)." AS article"
         ." JOIN ".Db_PDO::table(Model_Users::DB_TABLE)." AS user ON article.".self::COLUMN_ID_USER
         ." = user.".Model_Users::COLUMN_ID
         ." WHERE (article.".self::COLUMN_URLKEY."_".Locale::getLang()." = :urlkey"
         ." OR article.".self::COLUMN_URLKEY."_".Locale::getDefaultLang()." = :urlkey2)".
          " LIMIT 0, 1");
       $dbst->bindParam(':urlkey', $urlKey, PDO::PARAM_STR);
       $dbst->bindParam(':urlkey2', $urlKey, PDO::PARAM_STR);
      $dbst->execute();

      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
      return $dbst->fetch();
   }

   /**
    * Metoda vrací článek podle zadaného ID
    *
    * @param int -- id článku
    * @return PDOStatement -- pole s článkem
    */
   public function getArticleById($id) {
      $dbc = new Db_PDO();
         $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)." AS article"
         ." WHERE (".self::COLUMN_ID." = :id)".
          " LIMIT 0, 1");
      $dbst->bindParam(':id', $id, PDO::PARAM_INT);
      $dbst->execute();

      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
      return $dbst->fetch();
   }

   /**
    * Metoda smaže zadaný článek
    * @param integer $idArticle
    * @return bool
    */
   public function deleteArticle($idArticle) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("DELETE FROM ".Db_PDO::table(Articles_Model_Detail::DB_TABLE)
          ." WHERE (".Articles_Model_Detail::COLUMN_ID ." = :id)");
      $dbst->bindParam(':id', $idArticle, PDO::PARAM_INT);
      return $dbst->execute();
   }

   /**
    * Metoda vrací poslední změnu článků v dané kategorii
    * @param int $id -- id kategorie
    * @return int -- timestamp
    */
   public function getLastChange($id) {
      $dbc = new Db_PDO();
         $dbst = $dbc->prepare("SELECT ".self::COLUMN_EDIT_TIME." AS et FROM ".Db_PDO::table(self::DB_TABLE)." AS article"
         ." WHERE (".self::COLUMN_ID_CATEGORY." = :id)".
          " LIMIT 0, 1");
      $dbst->bindParam(':id', $id, PDO::PARAM_INT);
      $dbst->execute();
      
      $fetch = $dbst->fetchObject();
      if($fetch != false){
         return $fetch->et;
      }
      return false;
   }
}

?>