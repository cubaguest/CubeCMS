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
//   const COLUMN_ID_CAT = 'id_category';
   const COLUMN_DATE_ADD = 'date_add';
   const COLUMN_GROUP = 'group';
   const COLUMN_BLOCKED = 'blocked';

   /**
    * Metoda uloží mail do db
    */
   public function saveMail($mail, $id = null, $blocked = false) {
      $dbc = Db_PDO::getInstance();

      if($id !== null) {
         $dbst = $dbc->prepare("UPDATE ".Db_PDO::table(self::DB_TABLE)
          ." SET ".self::COLUMN_MAIL." = :mail,".self::COLUMN_BLOCKED." = :blocked"
                 ." WHERE ".self::COLUMN_ID.' = :id');
         $dbst->bindValue(':id', $id, PDO::PARAM_INT);
         $dbst->bindValue(':mail', $mail, PDO::PARAM_STR);
         $dbst->bindValue(':blocked', (int)$blocked, PDO::PARAM_BOOL);
      } else {
         $dbst = $dbc->prepare("INSERT INTO ".Db_PDO::table(self::DB_TABLE)
             ." (`".self::COLUMN_MAIL."`, `".self::COLUMN_IP."`, `".self::COLUMN_BLOCKED."`)"
             ." VALUES (:mail, :ipaddr, :blocked)");
         $dbst->bindValue(':mail', $mail, PDO::PARAM_STR);
         $dbst->bindValue(':ipaddr', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
         $dbst->bindValue(':blocked', (int)$blocked, PDO::PARAM_BOOL);
      }
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
   public function getMails($fromRow = null, $numRows = 100) {
      $dbc = Db_PDO::getInstance();
      if($fromRow === null){
         $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE));
      } else {
         $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)
         ." LIMIT :fromrow, :numrows");
         $dbst->bindParam(':fromrow', $fromRow, PDO::PARAM_INT);
         $dbst->bindParam(':numrows', $numRows, PDO::PARAM_INT);
      }
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
   public function isSavedMails($mail) {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare("SELECT COUNT(".self::COLUMN_ID.") AS count FROM ".Db_PDO::table(self::DB_TABLE)
         ." WHERE (".self::COLUMN_MAIL." = :mail)");
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
      $dbc = Db_PDO::getInstance();
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
   public function deleteMail($email) {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare("DELETE FROM ".Db_PDO::table(self::DB_TABLE)
          ." WHERE (".self::COLUMN_MAIL ." = :mail)");
      return $dbst->execute(array(':mail' => $email));
   }

   /**
    * Metoda změní blokaci zadaného mailu (podle názvu nebo id)
    * @param string/int $email -- email nebo id
    */
   public function changeMailStatus($email) {
      $dbc = Db_PDO::getInstance();
      if(is_numeric($email)){
         $dbst = $dbc->prepare("UPDATE ".Db_PDO::table(self::DB_TABLE)
            ." SET ".self::COLUMN_BLOCKED." = ".self::COLUMN_BLOCKED."+1"
            ." WHERE (".self::COLUMN_ID." = :idmail");
         $dbst->bindValue(':idmail', (int)$email, PDO::PARAM_INT);
      } else {
      }
      return $dbst->execute();
   }

   public function getMailsByIds($ids) {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)
         ." WHERE (".self::COLUMN_ID." IN (".$this->generateSQLIN($ids)."))");
      $dbst->setFetchMode(PDO::FETCH_OBJ);
      $dbst->execute();
      return $dbst->fetchAll();
   }

   /**
    * Metoda vrací počet článků
    *
    * @return integer -- počet článků
    */
   public function getCount() {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->query("SELECT COUNT(*) FROM ".Db_PDO::table(self::DB_TABLE));
      $count = $dbst->fetch();
      return $count[0];
   }
}

?>