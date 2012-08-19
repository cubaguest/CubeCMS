<?php

class MailsAddressBook_Model_Groups extends Model_ORM {
   const DB_TABLE = 'mails_addressbook_groups';

   const COLUMN_ID = 'id_addressbook_group';
   const COLUMN_NAME = 'addressbook_group_name';
   const COLUMN_NOTE = 'addressbook_group_note';

   const GROUP_ID_DEFAULT = 1;
   const GROUP_ID_ALL = 0; // NEPOUŽÍVAT

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_m_adrgrps');
   
      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
   
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(50)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_NOTE, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR));
   
      $this->setPk(self::COLUMN_ID);
   
      //      $this->addForeignKey(self::COLUMN_ID_GROUP, 'Mail_Model_Groups');
   }
   
   public function saveGroup($name, $note = null, $id = null) {
      $dbc = Db_PDO::getInstance();
      if($id != null){
         $dbst = $dbc->prepare("UPDATE ".Db_PDO::table(self::DB_TABLE)
                 ." SET ".self::COLUMN_NAME." = :name ,".self::COLUMN_NOTE." = :note"
                 ." WHERE ".self::COLUMN_ID." = :idg");
         $dbst->bindParam(':idg', $id, PDO::PARAM_INT);
      } else {
         $dbst = $dbc->prepare("INSERT INTO ".Db_PDO::table(self::DB_TABLE)." "
                 ."(".self::COLUMN_NAME.",". self::COLUMN_NOTE.")"
                 ." VALUES (:name, :note)");

      }
      $dbst->bindValue(':name', $name, PDO::PARAM_STR);
      $dbst->bindValue(':note', $note, PDO::PARAM_STR|PDO::PARAM_NULL);
      $dbst->execute();
      if($id == null) $id = $dbc->lastInsertId ();
      return $id;
   }

   /**
    * Metoda vrací pole objektů s uloženými maily
    * @return array
    */
   public function getGroups($fromRow = 0, $rows = 100, $orderBy = self::COLUMN_NAME, $order = 'ASC') {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)
              ." ORDER BY `".strtoupper($orderBy)."` ".strtoupper($order) // TOHLE je sice prasárna, ale nevím jak předat parametr bez uvozovek
              ." LIMIT :fromRow, :rows"
              );
      $dbst->bindValue(':fromRow', (int)$fromRow, PDO::PARAM_INT);
      $dbst->bindValue(':rows', (int)$rows, PDO::PARAM_INT);
      $dbst->setFetchMode(PDO::FETCH_OBJ);
      $dbst->execute();
      return $dbst->fetchAll();
   }

   /**
    * Metoda vrací objekt s uloženou skupinou
    * @return Object
    */
   public function getGrp($id) {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare('SELECT * FROM '.Db_PDO::table(self::DB_TABLE)
              .' WHERE '.self::COLUMN_ID." = :idg");
      $dbst->setFetchMode(PDO::FETCH_OBJ);
      $dbst->execute(array(':idg' => $id));
      return $dbst->fetch();
   }

   /**
    * metoda vymaže skupinu z db
    * @param int/string $id -- id mailu nebo mail
    */
   public function deleteGroup($id) {
      $dbc = Db_PDO::getInstance();

      $modelM = new Mails_Model_Addressbook();
      $modelM->deleteMailByGrp($id);
      unset ($modelM);

      $dbst = $dbc->prepare('DELETE FROM '.Db_PDO::table(self::DB_TABLE)
                 ." WHERE ".self::COLUMN_ID." = :idgrp");
      $dbst->bindValue(':idgrp', $id, PDO::PARAM_INT);

      return $dbst->execute();
   }

   /**
    * Metoda vrací počet článků
    *
    * @return integer -- počet článků
    */
   public function getCount() {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->query("SELECT COUNT(*) AS mailcount FROM ".Db_PDO::table(self::DB_TABLE));
      $count = $dbst->fetch();
      return $count->mailcount;
   }
}
?>
