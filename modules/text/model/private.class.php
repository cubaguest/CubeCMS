<?php
/*
 * Třída modelu s detailem textu
 * 
*/
class Text_Model_Private extends Model_PDO {
   /**
    * Tabulka s detaily
    */
   const DB_TABLE_TEXT_HAS_PRIVATE_USERS = 'texts_has_private_users';
   const DB_TABLE_TEXT_HAS_PRIVATE_GROUPS = 'texts_has_private_groups';

   const COLUMN_T_H_U_ID_TEXT = 'id_text';
   const COLUMN_T_H_U_ID_USER = 'id_user';

   const COLUMN_T_H_G_ID_TEXT = 'id_text';
   const COLUMN_T_H_G_ID_GROUP = 'id_group';

   public function saveUsersConnect($idText, $idUsers) {
      // smažeme předchozí spojení
      $this->deleteUsersConnections($idText);

      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare("INSERT INTO ".Db_PDO::table(self::DB_TABLE_TEXT_HAS_PRIVATE_USERS)." "
                 ."(".self::COLUMN_T_H_U_ID_TEXT.",". self::COLUMN_T_H_U_ID_USER.")"
                 ." VALUES (:idText, :idUser)");

      $dbst->bindValue(':idText', $idText, PDO::PARAM_INT);
      $dbst->bindParam(':idUser', $idU, PDO::PARAM_INT);
      foreach ($idUsers as $idU) { $dbst->execute(); }
   }

   private function deleteUsersConnections($idText) {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare("DELETE FROM ".Db_PDO::table(self::DB_TABLE_TEXT_HAS_PRIVATE_USERS)
          ." WHERE (".self::COLUMN_T_H_U_ID_TEXT ." = :idText)");
      $dbst->bindParam(':idText', $idText, PDO::PARAM_INT);
      return $dbst->execute();
   }

   public function saveGroupsConnect($idText, $idGroups) {
      // smažeme předchozí spojení
      $this->deleteGroupsConnections($idText);

      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare("INSERT INTO ".Db_PDO::table(self::DB_TABLE_TEXT_HAS_PRIVATE_GROUPS)." "
                 ."(".self::COLUMN_T_H_G_ID_TEXT.",". self::COLUMN_T_H_G_ID_GROUP.")"
                 ." VALUES (:idText, :idGroup)");

      $dbst->bindValue(':idText', $idText, PDO::PARAM_INT);
      $dbst->bindParam(':idGroup', $idG, PDO::PARAM_INT);
      foreach ($idGroups as $idG) { $dbst->execute(); }
   }

   private function deleteGroupsConnections($idText) {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare("DELETE FROM ".Db_PDO::table(self::DB_TABLE_TEXT_HAS_PRIVATE_GROUPS)
          ." WHERE (".self::COLUMN_T_H_G_ID_TEXT ." = :idText)");
      $dbst->bindParam(':idText', $idText, PDO::PARAM_INT);
      return $dbst->execute();
   }

   public function getGroupsConnect($idText) {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare("SELECT ".self::COLUMN_T_H_G_ID_GROUP." FROM ".Db_PDO::table(self::DB_TABLE_TEXT_HAS_PRIVATE_GROUPS)
         ." WHERE ".self::COLUMN_T_H_G_ID_TEXT." = :idt");
      $dbst->bindValue(':idt', $idText, PDO::PARAM_INT);
      $dbst->execute();
      return $dbst->fetchAll(PDO::FETCH_OBJ);
   }

   public function getUsersConnect($idText) {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare("SELECT ".self::COLUMN_T_H_U_ID_USER." FROM ".Db_PDO::table(self::DB_TABLE_TEXT_HAS_PRIVATE_USERS)
         ." WHERE ".self::COLUMN_T_H_U_ID_TEXT." = :idt");
      $dbst->bindValue(':idt', $idText, PDO::PARAM_INT);
      $dbst->execute();
      return $dbst->fetchAll(PDO::FETCH_OBJ);
   }

   public function haveGroup($idt, $idg) {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE_TEXT_HAS_PRIVATE_GROUPS)
              ." WHERE (".self::COLUMN_T_H_G_ID_TEXT." = :idt AND ".self::COLUMN_T_H_G_ID_GROUP." = :idg)");
      $dbst->execute(array(':idt'=> (int)$idt,':idg'=> (int)$idg));
      if($dbst->fetchObject() === false) return false;
      return true;
   }

   public function haveUser($idt, $idu) {
       $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE_TEXT_HAS_PRIVATE_USERS)
              ." WHERE (".self::COLUMN_T_H_U_ID_TEXT." = :idt AND ".self::COLUMN_T_H_U_ID_USER." = :idu)");
      $dbst->execute(array(':idt'=> (int)$idt,':idu'=> (int)$idu));
      if($dbst->fetchObject() === false) return false;
      return true;
   }
}

?>