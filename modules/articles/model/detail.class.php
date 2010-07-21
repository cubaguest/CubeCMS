<?php
/*
 * Třída modelu detailem článku
 */
class Articles_Model_Detail extends Model_PDO {
   const DB_TABLE = 'articles';
   const DB_TABLE_ART_HAS_PRIVATE_USERS = 'articles_has_private_users';

/**
 * Názvy sloupců v databázi
 */
   const COLUMN_NAME = 'name';
   const COLUMN_TEXT = 'text';
   const COLUMN_ANNOTATION = 'annotation';
   const COLUMN_TEXT_CLEAR = 'text_clear';
   const COLUMN_TEXT_PRIVATE = 'text_private';
   const COLUMN_KEYWORDS = 'keywords';
   const COLUMN_DESCRIPTION = 'description';
   const COLUMN_URLKEY = 'urlkey';
   const COLUMN_ADD_TIME = 'add_time';
   const COLUMN_EDIT_TIME = 'edit_time';
   const COLUMN_ID_USER = 'id_user';
   const COLUMN_ID_USER_LAST_EDIT = 'is_user_last_edit';
   const COLUMN_ID_CATEGORY = 'id_cat';
   const COLUMN_ID = 'id_article';
   const COLUMN_SHOWED = 'viewed';
   const COLUMN_PUBLIC = 'public';

   const COLUMN_A_H_U_ID_ARTICLE = 'id_article';
   const COLUMN_A_H_U_ID_USER = 'id_user';

   /**
    * Metoda uloží novinku do db
    *
    * @param array -- pole s nadpisem článku
    * @param array -- pole s textem článku
    * @param boolean -- id uživatele
    */
   public function saveArticle($name, $text, $annotation, $urlKey, $keywords, $description, $idCat = 0,
           $idUser = 0, $public = true, $id = null, $textPrivate = null,
           $idPrivateUsers = array(), $image = null) {
      // generování unikátního klíče
      $urlKey = $this->generateUrlKeys($urlKey, self::DB_TABLE, $name,
              self::COLUMN_URLKEY, self::COLUMN_ID,$id);

      // globalní prvky
      $this->setIUValues(array(self::COLUMN_NAME => $name,self::COLUMN_TEXT => $text,
         self::COLUMN_TEXT_PRIVATE => $textPrivate, self::COLUMN_KEYWORDS => $keywords,
         self::COLUMN_DESCRIPTION => $description, self::COLUMN_ANNOTATION => $annotation,
         self::COLUMN_URLKEY => $urlKey, self::COLUMN_PUBLIC => $public,
         self::COLUMN_TEXT_CLEAR => vve_strip_tags($text)));

      $dbc = new Db_PDO();

      if($id !== null) {
         $this->setIUValues(array(self::COLUMN_ID_USER_LAST_EDIT => $idUser));

         $dbst = $dbc->prepare("UPDATE ".Db_PDO::table(self::DB_TABLE)
          ." SET ".$this->getUpdateValues()
          ." WHERE ".self::COLUMN_ID." = :id");
         $dbst->bindParam(':id', $id, PDO::PARAM_INT);
         $dbst->execute();
      } else {
         if($idCat == 0){
            throw new InvalidArgumentException($this->_('Při ukládání nového článku musí být zadáno id'), 1);
         }
         // unikátní klíč
         $this->setIUValues(array(self::COLUMN_ID_CATEGORY => $idCat,
               self::COLUMN_ID_USER => $idUser,
               self::COLUMN_ADD_TIME => date("Y-m-d H:i:s")));

         $dbc->exec("INSERT INTO ".Db_PDO::table(self::DB_TABLE)
             ." ".$this->getInsertLabels()." VALUES ".$this->getInsertValues());

         $id = $dbc->lastInsertId();
      }

      // smažeme předchozí spojení článek <> privátní uživatel
      $this->deleteArticlePrivateUsersConnections($id);

      if(!empty ($idPrivateUsers)){
         foreach ($idPrivateUsers as $idU) {
            $this->saveArticlePrivateUsersConnect($id, $idU);
         }
     }
     return $id;
   }

   public function saveArticlePrivateUsersConnect($idArticle, $idUser) {
      // smažeme předchozí spojení
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("INSERT INTO ".Db_PDO::table(self::DB_TABLE_ART_HAS_PRIVATE_USERS)." "
                 ."(".self::COLUMN_A_H_U_ID_ARTICLE.",". self::COLUMN_A_H_U_ID_USER.")"
                 ." VALUES (:idArticle, :idUser)");
      $dbst->execute(array(":idArticle" => $idArticle, ':idUser' => $idUser));
   }

   public function deleteArticlePrivateUsersConnections($idArticle) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("DELETE FROM ".Db_PDO::table(self::DB_TABLE_ART_HAS_PRIVATE_USERS)
          ." WHERE (".self::COLUMN_A_H_U_ID_ARTICLE ." = :idArticle)");
      $dbst->bindParam(':idArticle', $idArticle, PDO::PARAM_INT);
      return $dbst->execute();
   }

   /**
    * Metoda vrací kurzu podle zadaného klíče
    *
    * @param string -- url klíč kurzu
    * @return PDOStatement -- pole s kurzem
    */
   public function getArticlePrivateUsers($ida) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE_ART_HAS_PRIVATE_USERS)
         ." WHERE (".self::COLUMN_A_H_U_ID_ARTICLE." = :ida)");
      $dbst->execute(array(':ida' => (int)$ida));
      $dbst->setFetchMode(PDO::FETCH_OBJ);
      return $dbst->fetchAll();
   }

   public function isPrivateUser($idUser, $idArticle) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT COUNT(*) FROM ".Db_PDO::table(self::DB_TABLE_ART_HAS_PRIVATE_USERS)
                 ." WHERE (".self::COLUMN_A_H_U_ID_ARTICLE ." = :idUser)"
              ." AND (".self::COLUMN_A_H_U_ID_USER ." = :idArticle)");
      $dbst->execute(array(':idUser' => $idUser, ':idArticle' => $idArticle));
      $count = $dbst->fetch();
      if($count[0] != 0){
         return true;
      }
      return false;
   }

   /**
    * Metoda přičte přečtení článku
    * @return string $urlkey -- url klíč článku
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
         ." JOIN ".Model_Users::getUsersTable()." AS user ON article.".self::COLUMN_ID_USER
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
    * Metoda vymaže články podle zadaného id kategorie
    * @param int $id -- id kategorie
    */
   public function deleteArticleByCat($id) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("DELETE FROM ".Db_PDO::table(Articles_Model_Detail::DB_TABLE)
          ." WHERE (".Articles_Model_Detail::COLUMN_ID_CATEGORY ." = :idcat )");
      $dbst->bindValue(':idcat', (int)$id, PDO::PARAM_INT);
      $dbst->execute();
      return $dbst;
   }

   /**
    * Metoda nastaví změnu článku
    * @param int $id -- id článku
    */
   public function setLastChange($idArt) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("UPDATE ".Db_PDO::table(self::DB_TABLE)
          ." SET `".self::COLUMN_EDIT_TIME."` = NOW()"
          ." WHERE (".self::COLUMN_ID." = :idart)");
      $dbst->bindParam(':idart', $idArt, PDO::PARAM_INT);
      return $dbst->execute();
   }

   /**
    * Metoda vyhledává články -- je tu kvůli zbytečnému nenačítání modelu List
    * @param integer $idCat
    * @param string $string
    * @param bool $publicOnly
    * @return PDOStatement
    */
   public function search($idCat, $string, $publicOnly = true){
      $dbc = new Db_PDO();
      $clabel = Articles_Model_Detail::COLUMN_NAME.'_'.Locale::getLang();
      $ctext = Articles_Model_Detail::COLUMN_TEXT_CLEAR.'_'.Locale::getLang();

      $wherePub = null;
      if($publicOnly){
         $wherePub = ' AND '.Articles_Model_Detail::COLUMN_PUBLIC.' = 1';
      }

      $dbst = $dbc->prepare('SELECT *, ('.round(VVE_SEARCH_ARTICLE_REL_MULTIPLIER).' * MATCH('.$clabel.') AGAINST (:sstring)'
              .' + MATCH('.$ctext.') AGAINST (:sstring)) as '.Search::COLUMN_RELEVATION
              .' FROM '.Db_PDO::table(Articles_Model_Detail::DB_TABLE)
              .' WHERE MATCH('.$clabel.', '.$ctext.') AGAINST (:sstring IN BOOLEAN MODE)'
              .' AND `'.Articles_Model_Detail::COLUMN_ID_CATEGORY.'` = :idCat'
              .$wherePub // Public articles
              .' ORDER BY '.round(VVE_SEARCH_ARTICLE_REL_MULTIPLIER)
              .' * MATCH('.$clabel.') AGAINST (:sstring) + MATCH('.$ctext.') AGAINST (:sstring) DESC');

      $dbst->bindValue(':idCat', $idCat, PDO::PARAM_INT);
      $dbst->bindValue(':sstring', $string, PDO::PARAM_STR);
      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
      $dbst->execute();
      return $dbst;
   }
}

?>