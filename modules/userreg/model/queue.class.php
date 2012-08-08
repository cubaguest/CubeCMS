<?php

/*
 * Třída modelu s listem Novinek
 */
class UserReg_Model_Queue extends Model_ORM {
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

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_userreg_queue');
       
      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_CAT, array('datatype' => 'smallint', 'nn' => true, 'index' => true));
      $this->addColumn(self::COLUMN_HASH, array('datatype' => 'varchar(50)', 'pdoparam' => PDO::PARAM_STR, 'uq' => true));
      $this->addColumn(self::COLUMN_USERNAME, array('datatype' => 'varchar(50)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_PASS, array('datatype' => 'varchar(50)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(50)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_SURNAME, array('datatype' => 'varchar(50)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_MAIL, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_PHONE_NUMBER, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_IP, array('datatype' => 'varchar(15)', 'pdoparam' => PDO::PARAM_STR));

      $this->addColumn(self::COLUMN_TIME_ADD, array('datatype' => 'timestamp', 'pdoparam' => PDO::PARAM_STR, 'default' => 'CURRENT_TIMESTAMP'));
   
      $this->setPk(self::COLUMN_ID);
      $this->addForeignKey(self::COLUMN_ID_CAT, 'Model_Category', Model_Category::COLUMN_ID);
   }
   
   public function getRegistration($hash) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT * FROM " . Db_PDO::table(self::DB_TABLE)
            . " WHERE (" . self::COLUMN_HASH . " = :hash)");
      $dbst->bindValue(':hash', $hash, PDO::PARAM_STR);
      $dbst->execute();

      return $dbst->fetch(PDO::FETCH_OBJ);
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