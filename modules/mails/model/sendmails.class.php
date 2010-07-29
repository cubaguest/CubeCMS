<?php

class Mails_Model_SendMails extends Model_PDO {
   const RECORD_SEPARATOR = ', ';

   const DB_TABLE = 'mails_sends';

   const COLUMN_ID = 'id_mail';
   const COLUMN_ID_USER = 'id_user';
   const COLUMN_SUBJECT = 'subject';
   const COLUMN_CONTENT = 'content';
   const COLUMN_RECIPIENTS = 'recipients';
   const COLUMN_DATE = 'date';
   const COLUMN_ATTACHMENTS = 'attachments';

   public function saveMail($subject, $content, $recipients = array(), $idUsqer = 1, $attachments = array() ) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("INSERT INTO ".Db_PDO::table(self::DB_TABLE)." "
                 ."(".self::COLUMN_SUBJECT.",". self::COLUMN_CONTENT.","
              .self::COLUMN_RECIPIENTS.",".self::COLUMN_ID_USER.",".self::COLUMN_ATTACHMENTS.")"
                 ." VALUES (:subject, :content, :recipients, :idUser, :attachmetns)");
      $dbst->bindValue(':subject', $subject, PDO::PARAM_STR|PDO::PARAM_NULL);
      $dbst->bindValue(':content', $content, PDO::PARAM_STR|PDO::PARAM_NULL);
      $dbst->bindValue(':recipients', implode(self::RECORD_SEPARATOR, $recipients), PDO::PARAM_STR|PDO::PARAM_NULL);
      $dbst->bindValue(':idUser', Auth::getUserId(), PDO::PARAM_INT);
      $dbst->bindValue(':attachmetns', implode(self::RECORD_SEPARATOR, $attachments), PDO::PARAM_STR|PDO::PARAM_NULL);
      $dbst->execute();
   }

   /**
    * Metoda vrací pole objektů s uloženými maily
    * @return <type>
    */
   public function getMails($fromRow = 0, $rows = 100) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare('SELECT * FROM '.Db_PDO::table(self::DB_TABLE)
              ." ORDER BY ".self::COLUMN_DATE." DESC"
              ." LIMIT :fromRow, :rows");
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
    * Metoda vrací počet mailů
    * @return integer -- počet mailů
    */
   public function getCount() {
      $dbc = new Db_PDO();
      $dbst = $dbc->query("SELECT COUNT(*) FROM ".Db_PDO::table(self::DB_TABLE));
      $count = $dbst->fetch();
      return $count[0];
   }
}
?>
