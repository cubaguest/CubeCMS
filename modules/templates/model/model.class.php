<?php
/*
 * Třída modelu detailem šablon
*/
class Templates_Model extends Model_PDO {
   const TEMPLATE_TYPE_TEXT = 'text';
   const TEMPLATE_TYPE_MAIL = 'mail';

   const DB_TABLE = 'templates';

   /**
    * Názvy sloupců v databázi
    */
   const COLUMN_ID         = 'id_template';
   const COLUMN_NAME       = 'name';
   const COLUMN_DESC       = 'description';
   const COLUMN_CONTENT    = 'content';
   const COLUMN_ADD_TIME   = 'time_add';
   const COLUMN_TYPE       = 'type';

   /**
    * Pole s typy šablon
    * @var <array>
    */
   public static $tplTypes = array(Templates_Model::TEMPLATE_TYPE_TEXT, Templates_Model::TEMPLATE_TYPE_MAIL);

   /**
    * Metoda uloží šablonu do db
    *
    * @param string -- nadpis šablony
    * @param string -- obsahe šablony
    * @param string -- type šablony (text,mail, ...) (option def Templates_Model::TEMPLATE_TYPE_TEXT)
    * @param int -- id šablony
    */
   public function saveTemplate($name, $desc, $content, $type = self::TEMPLATE_TYPE_TEXT, $id = null) {
      $dbc = new Db_PDO();

      if($id !== null) {
         $dbst = $dbc->prepare("UPDATE ".Db_PDO::table(self::DB_TABLE)
          ." SET ".self::COLUMN_CONTENT." = :content,".self::COLUMN_DESC.' = :descrip,'
                 .self::COLUMN_NAME.' = :name, '.self::COLUMN_TYPE." = :type"
          ." WHERE ".self::COLUMN_ID." = :id");
         $dbst->bindValue(':name',$name, PDO::PARAM_STR);
         $dbst->bindValue(':descrip',$desc, PDO::PARAM_STR);
         $dbst->bindValue(':content',$content, PDO::PARAM_STR);
         $dbst->bindValue(':type',$type, PDO::PARAM_STR);
         $dbst->bindParam(':id', $id, PDO::PARAM_INT);
         return $dbst->execute();
      } else {
         $dbst = $dbc->prepare("INSERT INTO ".Db_PDO::table(self::DB_TABLE)
                 ." (".self::COLUMN_NAME.", ".self::COLUMN_DESC.", ".self::COLUMN_CONTENT.", ".self::COLUMN_TYPE.") "
                 ."VALUES (:name, :descrip, :content, :type)");
         $dbst->bindValue(':name',$name, PDO::PARAM_STR);
         $dbst->bindValue(':descrip',$desc, PDO::PARAM_STR);
         $dbst->bindValue(':content',$content, PDO::PARAM_STR);
         $dbst->bindValue(':type',$type, PDO::PARAM_STR);
         return $dbst->execute();
      }
   }

   /**
    * Metoda vrací šablonu podle zadaného id
    *
    * @param int -- id šablony
    * @return Object -- pole s šablonou
    */
   public function getTemplate($id) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)
              ." WHERE ".self::COLUMN_ID." = :idt");

      $dbst->bindValue(':idt', (int)$id, PDO::PARAM_INT);
      $dbst->execute();

      return $dbst->fetchObject();
   }

   public function getTemplates($type = null) {
      $dbc = new Db_PDO();
      if($type == null) {
         $dbst = $dbc->prepare(
//                 'IF EXISTS (SHOW TABLES LIKE \''.$dbc->table(self::DB_TABLE).'\') THEN'.
                 ' SELECT * FROM '.$dbc->table(self::DB_TABLE));
      } else {
         $dbst = $dbc->prepare('SELECT * FROM '.$dbc->table(self::DB_TABLE)
                 .' WHERE '.self::COLUMN_TYPE.' = :type');
         $dbst->bindValue(':type', $type, PDO::PARAM_STR);

      }
      $dbst->execute();
      $dbst->setFetchMode(PDO::FETCH_OBJ);
      return $dbst->fetchAll();
   }

   /**
    * Metoda smaže zadanou šablonu
    * @param integer $id
    * @return bool
    */
   public function deleteTemplate($id) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("DELETE FROM ".Db_PDO::table(self::DB_TABLE)
          ." WHERE (".self::COLUMN_ID ." = :id)");
      $dbst->bindParam(':id', $id, PDO::PARAM_INT);
      return $dbst->execute();
   }
}

?>