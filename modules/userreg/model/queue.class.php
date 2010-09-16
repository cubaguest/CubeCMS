<?php

/*
 * Třída modelu s listem Novinek
 */
class UserReg_Model_Queue extends Model_PDO {
   const DB_TABLE = "userreg_queue";
   /**
    * Názvy sloupců v databázi
    * @var string
    */
   const COLUMN_ID = 'id_request';
   const COLUMN_ID_CAT = 'id_category';
   const COLUMN_HASH = 'hash';
   const COLUMN_USERNAME = 'username';
   const COLUMN_PASS = 'pass';
   const COLUMN_SURNAME = 'surname';
   const COLUMN_NAME = 'name';
   const COLUMN_MAIL = 'mail';
   const COLUMN_PHONE_NUMBER = 'phone';
   const COLUMN_TIME_ADD = 'timeadd';
   const COLUMN_IP = 'ipaddress';

   public function save($idc, $username, $pass, $hash, $mail, $name, $surname, $phone, $id = null) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("INSERT INTO " . Db_PDO::table(self::DB_TABLE) . " "
            . "(" . self::COLUMN_ID_CAT . "," . self::COLUMN_USERNAME . "," . self::COLUMN_PASS .
            "," . self::COLUMN_MAIL . "," . self::COLUMN_NAME . "," . self::COLUMN_HASH .
            "," . self::COLUMN_SURNAME . "," . self::COLUMN_PHONE_NUMBER . "," . self::COLUMN_IP . ")"
            . " VALUES (:idc, :username, :pass, :mail, :name, :hash, :surname, :phone, :ip)");

      $dbst->bindValue(':idc', $idc, PDO::PARAM_INT);
      $dbst->bindValue(':username', $username, PDO::PARAM_STR);
      $dbst->bindValue(':pass', $pass, PDO::PARAM_STR);
      $dbst->bindValue(':mail', $mail, PDO::PARAM_STR);
      $dbst->bindValue(':name', $name, PDO::PARAM_STR);
      $dbst->bindValue(':hash', $hash, PDO::PARAM_STR);
      $dbst->bindValue(':surname', $surname, PDO::PARAM_STR);
      $dbst->bindValue(':phone', $phone, PDO::PARAM_STR | PDO::PARAM_NULL);
      $dbst->bindValue(':ip', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR | PDO::PARAM_NULL);

      $dbst->execute();
      return $dbc->lastInsertId();
   }

   public function getRegistration($hash) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT * FROM " . Db_PDO::table(self::DB_TABLE)
            . " WHERE (" . self::COLUMN_HASH . " = :hash)");
      $dbst->bindValue(':hash', $hash, PDO::PARAM_STR);
      $dbst->execute();

      return $dbst->fetch(PDO::FETCH_OBJ);
   }

   public function remove($hash) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("DELETE FROM " . Db_PDO::table(self::DB_TABLE)
            . " WHERE (" . self::COLUMN_HASH . " = :hash)");
      $dbst->bindParam(':hash', $hash, PDO::PARAM_STR);
      return $dbst->execute();
   }

   public function clearExpired($idc, $hours) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("DELETE FROM " . Db_PDO::table(self::DB_TABLE)
            . " WHERE TIMESTAMPDIFF(HOUR,".self::COLUMN_TIME_ADD.",NOW()) > :inter"
            ." AND ".self::COLUMN_ID_CAT." = :idc");
      $dbst->bindValue(':inter', $hours, PDO::PARAM_STR);
      $dbst->bindParam(':idc', $idc, PDO::PARAM_INT);
      return $dbst->execute();
   }

   public function getUser($username) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)
             ." WHERE (".self::COLUMN_USERNAME." = :username)");

      $dbst->bindValue(':username', $username, PDO::PARAM_STR);
      $dbst->execute();

      $dbst->setFetchMode(PDO::FETCH_OBJ);
      return $dbst->fetch();
   }
}
?>