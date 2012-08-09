<?php
/*
 * Třída modelu detailem článku
 */
class Search_Model_Api extends Model_PDO {
   const DB_TABLE = 'search_apis';

/**
 * Názvy sloupců v databázi
 */
   const COLUMN_ID_CATEGORY = 'id_category';
   const COLUMN_ID = 'id_api';
   const COLUMN_URL = 'url';
   const COLUMN_API = 'api';
   const COLUMN_NAME = 'name';

   /**
    * Metoda uloží novinku do db
    *
    * @param array -- pole s nadpisem článku
    * @param array -- pole s textem článku
    * @param boolean -- id uživatele
    */
   public function saveApi($url, $api, $name, $idCat = 0, $idApi = null) {
      // globalní prvky
      $dbc = Db_PDO::getInstance();

      if($id !== null) {
//         $this->setIUValues(array(self::COLUMN_ID_USER_LAST_EDIT => $idUser));
//
//         $dbst = $dbc->prepare("UPDATE ".Db_PDO::table(self::DB_TABLE)
//          ." SET ".$this->getUpdateValues()
//          ." WHERE ".self::COLUMN_ID." = :id");
//         $dbst->bindParam(':id', $id, PDO::PARAM_INT);
//         return $dbst->execute();
      } else {
         if($idCat == 0){
            throw new InvalidArgumentException($this->tr('Při ukládání nového api musí být zadáno id kategorie'), 1);
         }
         $dbst = $dbc->prepare("INSERT INTO ".Db_PDO::table(self::DB_TABLE)
             ." (`".self::COLUMN_ID_CATEGORY."`,`".self::COLUMN_API."`, `".self::COLUMN_URL."`, `".self::COLUMN_NAME."`)"
             ." VALUES (:idCat, :apiname, :apiurl, :name)");
         $dbst->bindValue(':idCat', $idCat, PDO::PARAM_INT);
         $dbst->bindValue(':apiname', $api, PDO::PARAM_STR);
         $dbst->bindValue(':apiurl', $url, PDO::PARAM_STR);
         $dbst->bindValue(':name', $name, PDO::PARAM_STR);
         $dbst->execute();
         return $dbc->lastInsertId();
      }
   }

   /**
    * Metoda vrací všechna uložená api pro kategorii
    *
    * @param int $idCat -- id category
    * @return array of PDOStatement -- pole s api
    */
   public function getApis($idCat) {
      $dbc = Db_PDO::getInstance();
         $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)
         ." WHERE (`".self::COLUMN_ID_CATEGORY."` = :idCat)");
      $dbst->bindParam(':idCat', $idCat, PDO::PARAM_INT);
      
      $dbst->setFetchMode(PDO::FETCH_OBJ);
      $dbst->execute();

      return $dbst->fetchAll();
   }

   /**
    * Metoda vrací počet apis
    *
    * @return integer -- počet článků
    */
   public function getCountApis($idCat) {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->query("SELECT COUNT(*) FROM ".Db_PDO::table(self::DB_TABLE)
             ." WHERE (".self::COLUMN_ID_CATEGORY ." = '".$idCat."')");
      $count = $dbst->fetch();
      return $count[0];
   }

   /**
    * Metoda vrací všechna uložená api pro kategorii
    *
    * @param int $idCat -- id category
    * @return PDOStatement -- pole s api
    */
   public function getApi($id) {
      $dbc = Db_PDO::getInstance();
         $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)
         ." WHERE (`".self::COLUMN_ID."` = :idApi)");
      $dbst->bindParam(':idApi', $id, PDO::PARAM_INT);
      $dbst->setFetchMode(PDO::FETCH_OBJ);
      $dbst->execute();

      return $dbst->fetchAll();
   }

   /**
    * Metoda smaže zadané API
    * @param integer $idApi
    * @return bool
    */
   public function deleteApi($idApi) {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare("DELETE FROM ".Db_PDO::table(self::DB_TABLE)
          ." WHERE (".self::COLUMN_ID ." = :id)");
      $dbst->bindParam(':id', $idApi, PDO::PARAM_INT);
      return $dbst->execute();
   }
}

?>