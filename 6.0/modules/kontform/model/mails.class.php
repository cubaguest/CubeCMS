<?php
/*
 * Třída modelu s listem Novinek
 */
class Kontform_Model_Mails extends Model_PDO {
   const DB_TABLE = 'kontform_mails';

/**
 * Názvy sloupců v databázi
 * @var string
 */
   const COLUMN_ID = 'id_mail';
   const COLUMN_EMAIL = 'mail';
   const COLUMN_ID_ITEM = 'id_item';

   /**
    * Načtení všech emailů
    * @return PDOStatement
    */
   public function getListMails() {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)." WHERE (id_item = ".$this->idItem().")");
      $dbst->execute();

      $cl = new Model_LangContainer();
      $dbst->setFetchMode(PDO::FETCH_INTO, $cl);
      return $dbst;
   }

   /**
    * Metoda uloží novinku do db
    * @param string $mail -- emailová adresa
    */
   public function saveNewMail($mail) {
      $this->setIUValues(array(self::COLUMN_EMAIL => $mail,
          self::COLUMN_ID_ITEM => $this->module()->getId()));
      $dbc = new Db_PDO();
      return $dbc->exec("INSERT INTO ".Db_PDO::table(self::DB_TABLE)
          ." {$this->getInsertLabels()}"
          ." VALUES {$this->getInsertValues()}");
   }

   /**
    * Metoda vymaže email
    * @param integer $id -- id emailu
    * @return boolean
    */
   public function deleteMail($id) {
      $dbc = new Db_PDO();
      return $dbc->exec("DELETE FROM ".Db_PDO::table(self::DB_TABLE)
             ." WHERE (".self::COLUMN_ID." = ".$dbc->quote($id, PDO::PARAM_INT).")");
   }
}

?>