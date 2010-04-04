<?php
/*
 * Třída modelu detailem článku
 */
class NewsLetter_Model_Mails extends Model_PDO {
   const DB_TABLE = 'newsletter_mails';

/**
 * Názvy sloupců v databázi
 */
   const COLUMN_ID = 'id_mail';
   const COLUMN_MAIL = 'mail';
   const COLUMN_IP = 'ip_address';
   const COLUMN_ID_CAT = 'id_category';
   const COLUMN_DATE_ADD = 'date_add';
   const COLUMN_GROUP = 'group';

   /**
    * Metoda uloží mail do db
    */
   public function saveMail($mail, $ip, $idCat, $id = null) {
      $dbc = new Db_PDO();

      if($id !== null) {
         $dbst = $dbc->prepare("UPDATE ".Db_PDO::table(self::DB_TABLE)
          ." SET ".self::COLUMN_MAIL.' = :mail WHERE '.self::COLUMN_ID.' = :id');
         $dbst->bindParam(':id', $id, PDO::PARAM_INT);
         $dbst->bindParam(':mail', $mail, PDO::PARAM_STR);
      } else {
         if($idCat == 0){
            throw new InvalidArgumentException($this->_('Při ukládání nového e-mailu musí být zadáno id kategorie'), 1);
         }
         $dbst = $dbc->prepare("INSERT INTO ".Db_PDO::table(self::DB_TABLE)
             ." (`".self::COLUMN_ID_CAT."`, `".self::COLUMN_MAIL."`, `".self::COLUMN_IP."`)"
             ." VALUES (:idcat, :mail, :ipaddr)");
         $dbst->bindValue(':idcat', $idCat, PDO::PARAM_INT);
         $dbst->bindValue(':mail', $mail, PDO::PARAM_STR);
         $dbst->bindValue(':ipaddr', $ip, PDO::PARAM_STR);
      }
      return $dbst->execute();
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
    * @param int -- id kategorie s maily
    * @param int -- (0) od kterého řádku se má začít výpis
    * @param int -- (100) kolik řádků se má vypsat
    * @return PDOStatement -- statement s maily
    */
   public function getMails($idcat, $fromRow = null, $numRows = 100) {
      $dbc = new Db_PDO();
      if($fromRow === null){
         $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)
         ." WHERE (".self::COLUMN_ID_CAT." = :idcat)");
      } else {
         $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)
         ." WHERE (".self::COLUMN_ID_CAT." = :idcat LIMIT :fromrow, :numrows)");
         $dbst->bindParam(':fromrow', $fromRow, PDO::PARAM_INT);
         $dbst->bindParam(':numrows', $numRows, PDO::PARAM_INT);
      }
      $dbst->bindParam(':idcat', $idcat, PDO::PARAM_INT);
      $dbst->setFetchMode(PDO::FETCH_OBJ);
      $dbst->execute();
      return $dbst->fetchAll();
   }

   /**
    * Metoda vrací článek podle zadaného ID
    *
    * @param int -- id článku
    * @return PDOStatement -- pole s článkem
    */
   public function isSavedMails($idCat, $mail) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT COUNT(".self::COLUMN_ID.") AS count FROM ".Db_PDO::table(self::DB_TABLE)
         ." WHERE (".self::COLUMN_ID_CAT." = :idcat AND ".self::COLUMN_MAIL." = :mail)");
      $dbst->bindParam(':idcat', $idCat, PDO::PARAM_INT);
      $dbst->bindParam(':mail', $mail, PDO::PARAM_STR);
      $dbst->execute();
      $res = $dbst->fetch();
      if($res['count'] == 0){
         return false;
      }
      return true;
   }

   /**
    * Metoda smaže zadané maily (podle id)
    * @param array $ids -- pole s id mailů
    */
   public function deleteMails($ids) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("DELETE FROM ".Db_PDO::table(self::DB_TABLE)
          ." WHERE (".self::COLUMN_ID ." = :id)");
      foreach ($ids as $id) {
         $dbst->execute(array(':id' => $id));
      }
      return true;
   }

   /**
    * Metoda smaže zadaný mail (podle názvu)
    * @param integer $idc -- id kategorie
    * @param string $email -- email
    */
   public function deleteMail($idc, $email) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("DELETE FROM ".Db_PDO::table(self::DB_TABLE)
          ." WHERE (".self::COLUMN_MAIL ." = :mail AND ".self::COLUMN_ID_CAT ." = :idcat)");
      return $dbst->execute(array(':idcat' => $idc, ':mail' => $email));
   }

   public function getMailsByIds($ids) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)
         ." WHERE (".self::COLUMN_ID." IN (".$this->generateSQLIN($ids)."))");
      $dbst->setFetchMode(PDO::FETCH_OBJ);
      $dbst->execute();
      return $dbst->fetchAll();
   }
}

?>