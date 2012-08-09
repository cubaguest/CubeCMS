<?php
/*
 * Třída modelu s listem Novinek
*/
class TitlePage_Model_Items extends Model_PDO {
   const DB_TABLE = 'titlepage_items';


   const COLUMN_ID = 'id-item';
   const COLUMN_ORDER = 'order';
   const COLUMN_COLUMNS = 'columns';
   const COLUMN_TYPE = 'type';
   const COLUMN_DATA = 'data';
   const COLUMN_NAME = 'name';
   const COLUMN_IMAGE = 'image';
   const COLUMN_ID_CATEGORY = 'id-category';
   const COLUMN_ID_EXTERN = 'id-external';

   private $usersTable = null;

   public function __construct()
   {
      $modelUsers = new Model_Users();
      $this->usersTable = $modelUsers->getTableName();
      parent::__construct();
   }

      /**
    * Metoda uloží prvek do db
    *
    * @param string -- typ prvku (konstanta kontroleru)
    * @param mixed -- data
    * @param string -- (option) název prvku (konstanta kontroleru)
    * @param int -- (option) id prvku
    * @param int -- (option) id kategorie
    * @param int -- (option) id externího prvku (např id článku)
    */
   public function saveItem($type, $data, $idCat, $name = null, $image = null, $columns = 1,
           $id = null, $idExt = 0) {
      $dbc = Db_PDO::getInstance();

      if($id !== null) {
         $dbst = $dbc->prepare("UPDATE ".Db_PDO::table(self::DB_TABLE)
            ." SET `".self::COLUMN_NAME."` = :name,"." `".self::COLUMN_DATA."` = :data,"
            ." `".self::COLUMN_IMAGE."` = :image, `".self::COLUMN_COLUMNS."` = :columns,"
            ." `".self::COLUMN_ID_CATEGORY."` = :idcat, `".self::COLUMN_ID_EXTERN."` = :idext"
            ." WHERE `".self::COLUMN_ID."` = :id");
         $dbst->bindValue(':id', (int)$id, PDO::PARAM_INT);
      } else {
         // počet - řadí se na konec
         $dbst = $dbc->query("SELECT `".self::COLUMN_ORDER."` FROM ".Db_PDO::table(self::DB_TABLE)
            ." ORDER BY `".self::COLUMN_ORDER."` DESC");
         $count = $dbst->fetch();
         $count = (int)$count[0];

         $dbst = $dbc->prepare("INSERT INTO ".Db_PDO::table(self::DB_TABLE)
             ." (".self::COLUMN_DATA.", ".self::COLUMN_TYPE.", "
             ." `".self::COLUMN_NAME."`, `".self::COLUMN_COLUMNS."`, "
             ." `".self::COLUMN_ID_CATEGORY."`, `".self::COLUMN_ID_EXTERN."`, "
             ." `".self::COLUMN_ORDER."`, `".self::COLUMN_IMAGE."`)"
             ." VALUES (:data, :type, :name, :columns, :idcat, :idext, :order, :image)");
         $dbst->bindValue(':order', (int)$count+1, PDO::PARAM_INT);
         $dbst->bindValue(':type', $type, PDO::PARAM_STR);
      }

      $dbst->bindValue(':data', $data, PDO::PARAM_STR);
      $dbst->bindValue(':name', $name, PDO::PARAM_STR|PDO::PARAM_NULL);
      $dbst->bindValue(':image', $image, PDO::PARAM_STR|PDO::PARAM_NULL);
      $dbst->bindValue(':columns', (int)$columns, PDO::PARAM_INT);
      $dbst->bindValue(':idcat', (int)$idCat, PDO::PARAM_INT);
      $dbst->bindValue(':idext', (int)$idExt, PDO::PARAM_INT);

      $dbst->execute();

      if($id === null){
         $id = $dbc->lastInsertId();
      }
      return $id;
   }

   public function getItems() {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare("SELECT tbi.* FROM ".Db_PDO::table(self::DB_TABLE)." AS tbi"
              ." JOIN ".Db_PDO::table(Model_Category::DB_TABLE)." AS tbc"
              ." ON tbc.`".Model_Category::COLUMN_CAT_ID."` = tbi.`".self::COLUMN_ID_CATEGORY."`"
              ." JOIN ".Model_Rights::getRightsTable()." AS tbr "
              ."ON tbr.`".Model_Rights::COLUMN_ID_CATEGORY."` = tbc.`".Model_Category::COLUMN_CAT_ID."`"
              ." WHERE (tbr.`".Model_Rights::COLUMN_ID_GROUP."` = :idgrp"
              ." AND tbr.`".Model_Rights::COLUMN_RIGHT."` LIKE 'r__')"
              ." ORDER BY `".self::COLUMN_ORDER."` ASC"
              );
//      echo "SELECT tbi.* FROM ".Db_PDO::table(self::DB_TABLE)." AS tbi"
//              ." JOIN ".Db_PDO::table(Model_Category::DB_TABLE)." AS tbc"
//              ." ON tbc.`".Model_Category::COLUMN_CAT_ID."` = tbi.`".self::COLUMN_ID_CATEGORY."`"
//              ." JOIN ".Model_Rights::getRightsTable()." AS tbr "
//              ."ON tbr.`".Model_Rights::COLUMN_ID_CATEGORY."` = tbc.`".Model_Category::COLUMN_CAT_ID."`"
//              ." WHERE (tbr.`".Model_Rights::COLUMN_ID_GROUP."` = ".Auth::getGroupId()
//              ." AND tbr.`".Model_Rights::COLUMN_RIGHT."` LIKE 'r__')"
//              ." ORDER BY `".self::COLUMN_ORDER."` ASC";
              
//      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
      $dbst->bindValue(":idgrp", Auth::getGroupId(), PDO::PARAM_INT);
      $dbst->setFetchMode(PDO::FETCH_OBJ);
//      var_dump($dbst);flush();
//      print_r($dbst);flush();
      $dbst->execute();

      return $dbst->fetchAll();
   }

   public function setPositions($positions) {
      $dbc = Db_PDO::getInstance();

      $dbst = $dbc->prepare("UPDATE ".Db_PDO::table(self::DB_TABLE)
          ." SET `".self::COLUMN_ORDER."` = :pos"
          ." WHERE `".self::COLUMN_ID."` = :id");
      foreach ($positions as $pos => $id) {
         $dbst->bindValue(':pos', $pos+1, PDO::PARAM_INT);
         $dbst->bindParam(':id', $id, PDO::PARAM_INT);
         $dbst->execute();
      }

      return $dbst;
   }


   public function deleteItem($id) {
      $dbc = Db_PDO::getInstance();

      $dbst = $dbc->prepare("DELETE FROM ".Db_PDO::table(self::DB_TABLE)
          ." WHERE `".self::COLUMN_ID."` = :id");
      $dbst->bindParam(':id', $id, PDO::PARAM_INT);
      $dbst->execute();

      return $dbst;
   }

   public function getItem($id) {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)
         ." WHERE (`".self::COLUMN_ID."` = :id)");
      $dbst->execute(array(':id' => (int)$id));
      $dbst->setFetchMode(PDO::FETCH_OBJ);
      return $dbst->fetch();
   }

   /* Konec items? */


   /**
    * Metoda vrací počet článků
    *
    * @return integer -- počet článků
    */
   public function getCountArticles($idCat, $onlyPublic = true) {
      $dbc = Db_PDO::getInstance();
      if($onlyPublic) {
         $dbst = $dbc->query("SELECT COUNT(".Articles_Model_Detail::COLUMN_ID.")"
                 ." FROM ".Db_PDO::table(Articles_Model_Detail::DB_TABLE)
                 ." WHERE (".Articles_Model_Detail::COLUMN_ID_CATEGORY ." = '".$idCat."')"
                 ." AND (".Articles_Model_Detail::COLUMN_CONCEPT." = 0)");
      } else {
         $dbst = $dbc->query("SELECT COUNT(*) FROM ".Db_PDO::table(Articles_Model_Detail::DB_TABLE)
                 ." WHERE (".Articles_Model_Detail::COLUMN_ID_CATEGORY ." = '".$idCat."')");
      }
      $count = $dbst->fetch();
      return $count[0];
   }

   /**
    * Metoda vrací pole se všemi články
    * @return array -- pole článků
    */
   public function getList($idCat, $fromRow = 0, $rowsCount = 100, $onlyPublic = true) {
      $dbc = Db_PDO::getInstance();
      if($onlyPublic) {
         $wherePub = " AND (article.".Articles_Model_Detail::COLUMN_CONCEPT." = 0)";
      } else {
         $wherePub = null;
      }
      $dbst = $dbc->prepare("SELECT article.*, user.".Model_Users::COLUMN_USERNAME
              ." FROM ".Db_PDO::table(Articles_Model_Detail::DB_TABLE)." AS article"
              ." JOIN ".$this->usersTable." AS user ON article.".Articles_Model_Detail::COLUMN_ID_USER
              ." = user.".Model_Users::COLUMN_ID
              ." WHERE (article.".Articles_Model_Detail::COLUMN_ID_CATEGORY ." = :idcat)"
              . $wherePub // public
              ." ORDER BY ".Articles_Model_Detail::COLUMN_ADD_TIME." DESC"
              ." LIMIT :fromRow, :rowCount ");
      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
      $dbst->bindValue(':idcat', (int)$idCat, PDO::PARAM_INT);
      $dbst->bindValue(':fromRow', (int)$fromRow, PDO::PARAM_INT);
      $dbst->bindValue(':rowCount', (int)$rowsCount, PDO::PARAM_INT);
      $dbst->execute();

      return $dbst;
   }
   /**
    * Metoda vrací pole se všemi články
    * @return array -- pole článků
    */
   public function getListAll($idCat, $onlyPublic = true) {
      $dbc = Db_PDO::getInstance();
      if($onlyPublic) {
         $wherePub = " AND (article.".Articles_Model_Detail::COLUMN_CONCEPT." = 0)";
      } else {
         $wherePub = null;
      }
      $dbst = $dbc->prepare("SELECT article.*, user.".Model_Users::COLUMN_USERNAME
              ." FROM ".Db_PDO::table(Articles_Model_Detail::DB_TABLE)." AS article"
              ." JOIN ".$this->usersTable." AS user ON article.".Articles_Model_Detail::COLUMN_ID_USER
              ." = user.".Model_Users::COLUMN_ID
              ." WHERE (article.".Articles_Model_Detail::COLUMN_ID_CATEGORY ." = :idcat)"
              . $wherePub // public
              ." ORDER BY ".Articles_Model_Detail::COLUMN_ADD_TIME." DESC");
      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
      $dbst->bindValue(':idcat', (int)$idCat, PDO::PARAM_INT);
      $dbst->execute();

      return $dbst;
   }

   /**
    * Metoda vrací pole se články, seřazenými podle počtu zobrazení
    * @return array -- pole článků
    */
   public function getListTop($idCat, $fromRow = 0, $rowsCount = 100, $onlyPublic = true) {
      $dbc = Db_PDO::getInstance();
      if($onlyPublic) {
         $whereP = " AND (article.".Articles_Model_Detail::COLUMN_CONCEPT." = 0)";
      } else {
         $whereP = null;
      }
      
      $dbst = $dbc->prepare("SELECT article.*, user.".Model_Users::COLUMN_USERNAME
              ." FROM ".Db_PDO::table(Articles_Model_Detail::DB_TABLE)." AS article"
              ." JOIN ".$this->usersTable." AS user ON article.".Articles_Model_Detail::COLUMN_ID_USER
              ." = user.".Model_Users::COLUMN_ID
              ." WHERE (article.".Articles_Model_Detail::COLUMN_ID_CATEGORY ." = :idcat)"
              . $whereP
              ." ORDER BY ".Articles_Model_Detail::COLUMN_SHOWED." DESC"
              ." LIMIT :fromRow, :rowCount ");
      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
      $dbst->bindValue(':idcat', (int)$idCat, PDO::PARAM_INT);
      $dbst->bindValue(':fromRow', (int)$fromRow, PDO::PARAM_INT);
      $dbst->bindValue(':rowCount', (int)$rowsCount, PDO::PARAM_INT);
      $dbst->execute();
      return $dbst;
   }


   /**
    * Metoda vrací poslední změnu článků v dané kategorii
    * @param int $id -- id kategorie
    * @return int -- timestamp
    */
   public function getLastChange($id, $onlyPublic = true) {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare("SELECT ".Articles_Model_Detail::COLUMN_EDIT_TIME." AS et FROM ".Db_PDO::table(Articles_Model_Detail::DB_TABLE)." AS article"
              ." WHERE (".Articles_Model_Detail::COLUMN_ID_CATEGORY." = :id) AND (".Articles_Model_Detail::COLUMN_CONCEPT." = :onlyPublic)"
              ." ORDER BY ".Articles_Model_Detail::COLUMN_EDIT_TIME." DESC"
              ." LIMIT 0, 1");
      $dbst->bindParam(':id', $id, PDO::PARAM_INT);
      $dbst->bindValue(':onlyPublic', (int)!$onlyPublic, PDO::PARAM_INT);
      $dbst->execute();

      $fetch = $dbst->fetchObject();
      if($fetch != false) {
         return $fetch->et;
      }
      return false;
   }

   /**
    * Metoda vrací pole se všemi články
    * @return array -- pole článků
    */
   public function getListByCats($idCats, $num = 10, $onlyPublic = true) {
      $dbc = Db_PDO::getInstance();
      if($onlyPublic) {
         $wherePub = " AND (article.".Articles_Model_Detail::COLUMN_CONCEPT." = 0)";
      } else {
         $wherePub = null;
      }
      $dbst = $dbc->prepare("SELECT article.*, user.".Model_Users::COLUMN_USERNAME.", cats.".Model_Category::COLUMN_URLKEY.'_'.Locales::getLang()." AS curlkey"
              ." FROM ".Db_PDO::table(Articles_Model_Detail::DB_TABLE)." AS article"
              ." JOIN ".$this->usersTable." AS user ON article.".Articles_Model_Detail::COLUMN_ID_USER
              ." = user.".Model_Users::COLUMN_ID
              ." JOIN ".Db_PDO::table(Model_Category::DB_TABLE)." AS cats ON article.".Articles_Model_Detail::COLUMN_ID_CATEGORY
              ." = cats.".Model_Category::COLUMN_CAT_ID
              ." WHERE (article.".Articles_Model_Detail::COLUMN_ID_CATEGORY ." IN (".$this->generateSQLIN($idCats)."))"
              . $wherePub // public
              ." ORDER BY article.".Articles_Model_Detail::COLUMN_ADD_TIME." DESC"
              ." LIMIT 0, :rowCount ");
      
      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
      $dbst->bindValue(':rowCount', (int)$num, PDO::PARAM_INT);
      $dbst->execute();

      return $dbst->fetchAll();
   }
}

?>