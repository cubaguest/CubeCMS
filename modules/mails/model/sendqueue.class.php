<?php

class Mails_Model_SendQueue extends Model_ORM {
   const DB_TABLE = 'mails_send_queue';

   const COLUMN_ID = 'id_mail';
   const COLUMN_ID_USER = 'id_user';
   const COLUMN_MAIL = 'mail';
   const COLUMN_NAME = 'name';
   const COLUMN_UNDELIVERABLE = 'undeliverable';
   const COLUMN_DATA = 'mail_data';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_m_queue');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_USER, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_INT, 'index' => true));
      
      $this->addColumn(self::COLUMN_MAIL, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_UNDELIVERABLE, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => false));
      $this->addColumn(self::COLUMN_DATA, array('datatype' => 'blob', 'pdoparam' => PDO::PARAM_STR));
      
      $this->setPk(self::COLUMN_ID);
      
      $this->addForeignKey(self::COLUMN_ID_USER, 'Model_Users');
//      $this->addForeignKey(self::COLUMN_ID_GROUP, 'Mail_Model_Groups');
   }
   
   public function addMails($mailsArr) {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare("INSERT INTO " . Db_PDO::table(self::DB_TABLE) . " "
            . "(" . self::COLUMN_MAIL . "," . self::COLUMN_NAME . ") VALUES (:mail, :name)");

      foreach ($mailsArr as $key => $val) {
         $n = null;
         if(is_int($key)){
            $m = $val;
         } else {
            $m = $key;
            $n = $val;
         }
         $dbst->bindValue(':mail', $m, PDO::PARAM_STR);
         $dbst->bindValue(':name', $n, PDO::PARAM_STR|PDO::PARAM_NULL);
         $dbst->execute();
      }
   }

   /**
    * Metoda vrací pole objektů s uloženými maily
    * @return <type>
    */
   public function getMails($fromRow = 0, $rows = 10000) {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare('SELECT * FROM ' . Db_PDO::table(self::DB_TABLE)
            . " LIMIT :fromRow, :rows");
      $dbst->bindParam(':fromRow', $fromRow, PDO::PARAM_INT);
      $dbst->bindParam(':rows', $rows, PDO::PARAM_INT);
      $dbst->execute();
      $dbst->setFetchMode(PDO::FETCH_OBJ);
      return $dbst->fetchAll();
   }

   /**
    * Metoda vrací objekt s uloženým mailem
    * @return Object
    */
   public function getMail($id) {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare('SELECT * FROM ' . Db_PDO::table(self::DB_TABLE)
            . ' WHERE ' . self::COLUMN_ID . " = :idm");
      $dbst->setFetchMode(PDO::FETCH_OBJ);
      $dbst->execute(array(':idm' => $id));
      return $dbst->fetch();
   }

   /**
    * metoda vymaže mail z db
    * @param int/string $id -- id mailu nebo mail
    */
   public function deleteMail($id) {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare('DELETE FROM ' . Db_PDO::table(self::DB_TABLE). " WHERE " . self::COLUMN_ID . " = :id");
      $dbst->bindValue(':id', $id, PDO::PARAM_INT);
      return $dbst->execute();
   }

   /**
    * Vymaže všechny maily z fronty
    */
   public function truncateModel()
   {
      $dbc = Db_PDO::getInstance();
      return $dbc->exec('TRUNCATE TABLE ' . Db_PDO::table(self::DB_TABLE));
   }

   public function setUndeliverable($idm)
   {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare("UPDATE ".Db_PDO::table(self::DB_TABLE)
              ." SET ".self::COLUMN_UNDELIVERABLE." = 1"
              ." WHERE ".self::COLUMN_ID." = :idm");
      $dbst->bindParam(':idm', $idm, PDO::PARAM_INT);
      return $dbst->execute();
   }

   public function getUndeliverable()
   {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare('SELECT * FROM ' . Db_PDO::table(self::DB_TABLE)
         ." WHERE ".self::COLUMN_UNDELIVERABLE." = 1");
      $dbst->execute();
      $dbst->setFetchMode(PDO::FETCH_OBJ);
      return $dbst->fetchAll();
   }

   /**
    * Metoda vrací počet mailů
    * @return integer -- počet mailů
    */
   public function getCount() {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->query("SELECT COUNT(*) FROM " . Db_PDO::table(self::DB_TABLE));
      $count = $dbst->fetch();
      return $count[0];
   }

}
?>
