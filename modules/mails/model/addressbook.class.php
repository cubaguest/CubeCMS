<?php

class Mails_Model_Addressbook extends Model_PDO {
   const DB_TABLE = 'mails_addressbook';

   const COLUMN_ID = 'id_mail';
   const COLUMN_NAME = 'name';
   const COLUMN_SURNAME = 'surname';
   const COLUMN_MAIL = 'mail';

   public function saveMail($mail, $name = null, $surname = null, $id = null) {
      $dbc = new Db_PDO();
      if($id != null){
         $dbst = $dbc->prepare("UPDATE ".Db_PDO::table(self::DB_TABLE)
                 ." SET ".self::COLUMN_NAME." = :name ,". self::COLUMN_SURNAME." = :surname,"
                 .self::COLUMN_MAIL." = :mail"
                 ." WHERE ".self::COLUMN_ID." = :idm");
         $dbst->bindParam(':idm', $id, PDO::PARAM_INT);
      } else {
         $dbst = $dbc->prepare("INSERT INTO ".Db_PDO::table(self::DB_TABLE)." "
                 ."(".self::COLUMN_NAME.",". self::COLUMN_SURNAME.",".self::COLUMN_MAIL.")"
                 ." VALUES (:name, :surname, :mail)");

      }
      $dbst->bindValue(':name', $name, PDO::PARAM_STR|PDO::PARAM_NULL);
      $dbst->bindValue(':surname', $surname, PDO::PARAM_STR|PDO::PARAM_NULL);
      $dbst->bindValue(':mail', $mail, PDO::PARAM_STR);

      $dbst->execute();

      if($id == null) $id = $dbc->lastInsertId ();

      return $id;
   }

   /**
    * Metoda vrací pole objektů s uloženými maily
    * @return <type>
    */
   public function getMails() {
      $dbc = new Db_PDO();
      $dbst = $dbc->query('SELECT * FROM '.Db_PDO::table(self::DB_TABLE));
      $dbst->setFetchMode(PDO::FETCH_OBJ);
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
}
?>
