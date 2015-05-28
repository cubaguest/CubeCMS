<?php

/*
 * Třída modelu s listem Novinek
 */

class Contact_Model_Questions extends Model_PDO {
   const DB_TABLE = "contact_questions";
   /**
    * Názvy sloupců v databázi
    * @var string
    */
   const COLUMN_ID = 'id_question';
   const COLUMN_NAME = 'name';
   const COLUMN_MAIL = 'mail';
   const COLUMN_SUBJECT = 'subject';
   const COLUMN_PHONE = 'phone';
   const COLUMN_TEXT = 'text';
   const COLUMN_TIME_ADD = 'time_add';

   public function saveQuestion($name, $mail, $subject, $text, $phone) {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare("INSERT INTO " . Db_PDO::table(self::DB_TABLE) . " "
                      . "(" . self::COLUMN_NAME . "," . self::COLUMN_MAIL. ","
                      . self::COLUMN_SUBJECT . "," . self::COLUMN_TEXT. "," . self::COLUMN_PHONE. ")"
                      . " VALUES (:name, :mail, :subject, :text, :phone)");
      $dbst->bindValue(':name', $name, PDO::PARAM_STR);
      $dbst->bindValue(':mail', $mail, PDO::PARAM_STR);
      $dbst->bindValue(':subject', $subject, PDO::PARAM_STR | PDO::PARAM_NULL);
      $dbst->bindValue(':text', $text, PDO::PARAM_STR);
      $dbst->bindValue(':phone', $phone, PDO::PARAM_STR);
      $dbst->execute();

      return $dbc->lastInsertId();
   }

}
?>