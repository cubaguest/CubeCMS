<?php

class MailsAddressBook_Model_Addressbook extends Model_ORM {
   const DB_TABLE = 'mails_addressbook';

   const COLUMN_ID = 'id_addressbook_mail';
   const COLUMN_ID_GRP = 'id_addressbook_group';
   const COLUMN_NAME = 'addressbook_name';
   const COLUMN_SURNAME = 'addressbook_surname';
   const COLUMN_MAIL = 'addressbook_mail';
   const COLUMN_NOTE = 'addressbook_note';

   const DEFAULT_GROUP_ID = 2;

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_m_adr');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_GRP, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_INT, 'index' => true, 'default' => self::DEFAULT_GROUP_ID));
      
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(50)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_SURNAME, array('datatype' => 'varchar(50)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_MAIL, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_NOTE, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR));
      
      $this->setPk(self::COLUMN_ID);
      
      $this->addForeignKey(self::COLUMN_ID_GRP, 'MailsAddressBook_Model_Groups', MailsAddressBook_Model_Groups::COLUMN_ID);
   }
   
   public function saveMail($mail, $idGrp = Mails_Model_Groups::GROUP_ID_DEFAULT,
      $name = null, $surname = null, $note = null, $id = null) {
      $dbc = Db_PDO::getInstance();
      if($id != null){
         $dbst = $dbc->prepare("UPDATE ".Db_PDO::table(self::DB_TABLE)
                 ." SET ".self::COLUMN_NAME." = :name ,". self::COLUMN_SURNAME." = :surname,"
                 .self::COLUMN_MAIL." = :mail, ".self::COLUMN_NOTE." = :note,".self::COLUMN_ID_GRP." = :idGrp"
                 ." WHERE ".self::COLUMN_ID." = :idm");
         $dbst->bindParam(':idm', $id, PDO::PARAM_INT);
      } else {
         $dbst = $dbc->prepare("INSERT INTO ".Db_PDO::table(self::DB_TABLE)." "
                 ."(".self::COLUMN_NAME.",". self::COLUMN_SURNAME.","
                 .self::COLUMN_MAIL.", ".self::COLUMN_NOTE.", ".self::COLUMN_ID_GRP.")"
                 ." VALUES (:name, :surname, :mail, :note, :idGrp)");
      }
      $dbst->bindValue(':name', $name, PDO::PARAM_STR|PDO::PARAM_NULL);
      $dbst->bindValue(':surname', $surname, PDO::PARAM_STR|PDO::PARAM_NULL);
      $dbst->bindValue(':mail', $mail, PDO::PARAM_STR);
      $dbst->bindValue(':note', $note, PDO::PARAM_STR);
      $dbst->bindValue(':idGrp', $idGrp, PDO::PARAM_INT);
      $dbst->execute();
      if($id == null) $id = $dbc->lastInsertId ();
      return $id;
   }

   /**
    * Metoda vrací objekt s maily podle hledaného řetězce
    * @return Object
    */
   public function searchMail($search) {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare('SELECT * FROM '.Db_PDO::table(self::DB_TABLE)
              .' WHERE '.self::COLUMN_NAME." LIKE :str"
              .' OR '.self::COLUMN_SURNAME." LIKE :str"
              .' OR '.self::COLUMN_MAIL." LIKE :str"
              );
      $dbst->setFetchMode(PDO::FETCH_OBJ);
      $dbst->execute(array(':str' => '%'.$search.'%'));
      return $dbst->fetchAll();
   }

   /**
    * metoda vymaže mail z db
    * @param int/string $id -- id mailu nebo mail
    */
   public function deleteMail($id) {
      $dbc = Db_PDO::getInstance();
      if(is_integer($id)){
         $dbst = $dbc->prepare('DELETE FROM '.Db_PDO::table(self::DB_TABLE)
                 ." WHERE ".self::COLUMN_ID." = :mail");
      } else {
         $dbst = $dbc->prepare('DELETE FROM '.Db_PDO::table(self::DB_TABLE)
                 ." WHERE ".self::COLUMN_MAIL." = :mail");
      }
      $dbst->bindValue(':mail', $id, PDO::PARAM_INT|PDO::PARAM_STR);
      return $dbst->execute();
   }

   /**
    * metoda vymaže maily z db podle skupiny
    * @param int $id -- id skupiny
    */
   public function deleteMailByGrp($idGrp) {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare('DELETE FROM '.Db_PDO::table(self::DB_TABLE)
              ." WHERE ".self::COLUMN_ID." = :idgrp");
      $dbst->bindValue(':idgrp', $idGrp, PDO::PARAM_INT);
      return $dbst->execute();
   }
}
?>