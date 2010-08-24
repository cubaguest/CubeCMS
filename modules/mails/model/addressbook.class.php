<?php

class Mails_Model_Addressbook extends Model_PDO {
   const DB_TABLE = 'mails_addressbook';

   const COLUMN_ID = 'id_mail';
   const COLUMN_ID_GRP = 'id_group';
   const COLUMN_NAME = 'name';
   const COLUMN_SURNAME = 'surname';
   const COLUMN_MAIL = 'mail';
   const COLUMN_NOTE = 'note';

   const DEFAULT_GROUP_ID = 2;

   public function saveMail($mail, $idGrp = Mails_Model_Groups::GROUP_ID_DEFAULT,
      $name = null, $surname = null, $note = null, $id = null) {
      $dbc = new Db_PDO();
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
    * Metoda vrací pole objektů s uloženými maily
    * @return array
    */
   public function getMails($idGrp = Mails_Model_Groups::GROUP_ID_DEFAULT, $fromRow = 0, $rows = 100, $orderBy = self::COLUMN_SURNAME, $order = 'ASC') {
      $dbc = new Db_PDO();
      if($idGrp == Mails_Model_Groups::GROUP_ID_ALL){
         $dbst = $dbc->prepare("SELECT tm.* FROM ".Db_PDO::table(self::DB_TABLE)." AS tm"
              ." ORDER BY `".strtoupper($orderBy)."` ".strtoupper($order).", ".self::COLUMN_MAIL." ASC" // TOHLE je sice prasárna, ale nevím jak předat parametr bez uvozovek
              ." LIMIT :fromRow, :rows");
      } else {
         $dbst = $dbc->prepare("SELECT tm.* FROM ".Db_PDO::table(self::DB_TABLE)." AS tm"
              ." WHERE ".self::COLUMN_ID_GRP." = :idGrp"
              ." ORDER BY `".strtoupper($orderBy)."` ".strtoupper($order).", ".self::COLUMN_MAIL." ASC" // TOHLE je sice prasárna, ale nevím jak předat parametr bez uvozovek
              ." LIMIT :fromRow, :rows");
         $dbst->bindValue(':idGrp', (int)$idGrp, PDO::PARAM_INT);
      }
      $dbst->bindValue(':fromRow', (int)$fromRow, PDO::PARAM_INT);
      $dbst->bindValue(':rows', (int)$rows, PDO::PARAM_INT);
      $dbst->setFetchMode(PDO::FETCH_OBJ);
      $dbst->execute();
      return $dbst->fetchAll();
   }

   /**
    * Metoda vrací objekt s uloženým mailem
    * @return Object
    */
   public function getMail($id) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare('SELECT * FROM '.Db_PDO::table(self::DB_TABLE)
              .' WHERE '.self::COLUMN_ID." = :idm");
      $dbst->setFetchMode(PDO::FETCH_OBJ);
      $dbst->execute(array(':idm' => $id));
      return $dbst->fetch();
   }

   /**
    * metoda vymaže mail z db
    * @param int/string $id -- id mailu nebo mail
    */
   public function deleteMail($id) {
      $dbc = new Db_PDO();
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
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare('DELETE FROM '.Db_PDO::table(self::DB_TABLE)
              ." WHERE ".self::COLUMN_ID." = :idgrp");
      $dbst->bindValue(':idgrp', $idGrp, PDO::PARAM_INT);
      return $dbst->execute();
   }

   /**
    * Metoda vrací počet článků
    *
    * @return integer -- počet článků
    */
   public function getCount($idGrp = Mails_Model_Groups::GROUP_ID_ALL) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT COUNT(*) FROM ".Db_PDO::table(self::DB_TABLE)
         ." WHERE ".self::COLUMN_ID_GRP." = :idGrp");
      $dbst->execute(array(':idGrp' => $idGrp));
      $count = $dbst->fetch();
      return $count[0];
   }
}
?>
