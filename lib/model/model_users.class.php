<?php
/**
 * Třída s modelem pro práci s uživateli
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: model_module.class.php 625 2009-06-13 16:01:09Z jakub $ VVE 5.1.0 $Revision: 625 $
 * @author			$Author: jakub $ $Date: 2009-06-13 16:01:09 +0000 (So, 13 čen 2009) $
 *						$LastChangedBy: jakub $ $LastChangedDate: 2009-06-13 16:01:09 +0000 (So, 13 čen 2009) $
 * @abstract 		Třída s modelem pro práci s uživateli
 */

class Model_Users extends Model_ORM {
/**
 * Název tabulky s uživateli
 */
   const DB_TABLE = 'users';

   /**
    * Název tabulky se skupinami
    */
   const DB_TABLE_GROUPS = 'groups';

   /**
    * Názvy sloupců v db tabulce
    * @var string
    */
   const COLUMN_ID         = 'id_user';
   const COLUMN_USERNAME   = 'username';
   const COLUMN_PASSWORD   = 'password';
   const COLUMN_PASSWORD_RESTORE   = 'password_restore';
   const COLUMN_ID_GROUP   = 'id_group';
   const COLUMN_NAME       = 'name';
   const COLUMN_SURNAME    = 'surname';
   const COLUMN_MAIL       = 'mail';
   const COLUMN_NOTE       = 'note';
   const COLUMN_BLOCKED    = 'blocked';
   const COLUMN_FOTO_FILE  = 'foto_file'; // dyť není v db?
   const COLUMN_DELETED    = 'deleted';



   const COLUMN_GROUP_ID    = 'id_group';
   const COLUMN_GROUP_NAME    = 'gname';
   const COLUMN_GROUP_LABEL    = 'label';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_us');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_GROUP, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_USERNAME, array('datatype' => 'varchar(50)', 'nn' => true, 'uq' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_PASSWORD, array('datatype' => 'varchar(50)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_PASSWORD_RESTORE, array('datatype' => 'varchar(50)', 'pdoparam' => PDO::PARAM_STR, 'default' => null));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(50)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_SURNAME, array('datatype' => 'varchar(50)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_BLOCKED, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => false));
      $this->addColumn(self::COLUMN_DELETED, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => false));
      $this->addColumn(self::COLUMN_FOTO_FILE, array('datatype' => 'varchar(50)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_NOTE, array('datatype' => 'varchar(500)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_MAIL, array('datatype' => 'varchar(500)', 'pdoparam' => PDO::PARAM_STR));

      $this->setPk(self::COLUMN_ID);
      $this->addForeignKey('tgroups', self::COLUMN_ID_GROUP, 'Model_Groups');
   }

   /**
    * Metoda načte uživatele podle uživatelského jména
    * @param string $username -- uživatelské jméno
    */
   public function getUser($username, $blockedUsers = false) {
      $where = null;
      if($blockedUsers === false){
         $where = ' AND user.'.self::COLUMN_BLOCKED.' = :blocked';
      }
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT *, grp.name AS gname FROM ".self::getUsersTable()." AS user
             JOIN ".self::getGroupsTable()." AS grp ON user.".self::COLUMN_ID_GROUP
          ." = grp.".self::COLUMN_ID_GROUP."
             WHERE (user.".self::COLUMN_USERNAME." = :username".$where.")");


      $dbst->bindValue(':username', $username, PDO::PARAM_STR);

      if($blockedUsers === false){
         $dbst->bindValue(':blocked', $blockedUsers, PDO::PARAM_BOOL);
      }

      $dbst->execute();

      $dbst->setFetchMode(PDO::FETCH_OBJ);
      return $dbst->fetch();
   }

   /**
    * Metoda načte uživatele podle id uživatele
    * @param int $id -- id uživatele
    * @return Object
    */
   public function getUserById($id) {
      $dbc = new Db_PDO();
      $dbst = $dbc->query("SELECT user.*, grp.".self::COLUMN_ID_GROUP.", grp.name AS ".self::COLUMN_GROUP_NAME
          ." FROM ".self::getUsersTable()." AS user"
          ." JOIN ".self::getGroupsTable()." AS grp ON user.".self::COLUMN_ID_GROUP." = grp.".self::COLUMN_ID_GROUP
          ." WHERE (user.".self::COLUMN_ID." = ".$dbc->quote((int)$id).")");
      $dbst->execute();

      $dbst->setFetchMode(PDO::FETCH_OBJ);
      return $dbst->fetch();
   }

   /**
    * Metoda načte skupinu podle id
    * @param int $id -- id skupiny
    */
   public function getGroupById($id) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT *, name AS gname FROM ".self::getGroupsTable()
             ." WHERE (".self::COLUMN_GROUP_ID." = :idgrp)");
      $dbst->bindValue(':idgrp', $id, PDO::PARAM_INT);
      $dbst->execute();

      $dbst->setFetchMode(PDO::FETCH_OBJ);
      return $dbst->fetch();
   }

   /**
    * Metoda vrací seznam skupin v systému
    * @return PDOStatement
    */
   public function getGroups() {
      $dbc = new Db_PDO();
      return $dbc->query("SELECT *, name AS gname FROM ".self::getGroupsTable());
   }

   /**
    * Metoda vrací seznam uživatelů v systému
    * @return PDOStatement
    */
   public function getUsersList() {
      $dbc = new Db_PDO();
      $dbst = $dbc->query("SELECT users.*, grps.name AS gname FROM ".self::getUsersTable()." AS users"
          ." JOIN ".self::getGroupsTable()." AS grps ON users.".self::COLUMN_ID_GROUP." = grps.".self::COLUMN_ID_GROUP
//          ." WHERE users.".self::COLUMN_BLOCKED." = 0"
          ." ORDER BY users.".self::COLUMN_ID);
      $dbst->execute();
      return $dbst;
   }

   public function getCount($idgrp = null){
      $dbc = new Db_PDO();
      $sql = "SELECT COUNT(*) FROM ".$this->getUsersTable();
      if($idgrp !== null) $sql .= " WHERE ".self::COLUMN_ID_GROUP." = :idGrp";
      $dbst = $dbc->prepare($sql);
      $dbst->execute(array(':idGrp' => $idgrp));
      $count = $dbst->fetch();
      return $count[0];
   }

   public function getUsers($fromRow = 0 , $rows = 1000, $orderCol = self::COLUMN_ID, $order = 'ASC', $idgrp = null) {
      $this->isValidColumn($orderCol, array(self::COLUMN_ID, self::COLUMN_NAME, self::COLUMN_SURNAME, self::COLUMN_USERNAME,
         self::COLUMN_MAIL, self::COLUMN_NOTE, self::COLUMN_BLOCKED));
      $this->isValidOrder($order);

      $dbc = new Db_PDO();

      $sql = "SELECT tu.* FROM ".Db_PDO::table(self::DB_TABLE)." AS tu";
      if($idgrp !== null) $sql .=" WHERE ".self::COLUMN_ID_GROUP." = :idGrp"; // GRP
      $sql .=" ORDER BY `".strtoupper($orderCol)."` ".strtoupper($order).", ".self::COLUMN_ID." ASC"
      ." LIMIT :fromRow, :rows";

      $dbst = $dbc->prepare($sql);

      if($idgrp !== null) $dbst->bindValue(':idGrp', (int)$idgrp, PDO::PARAM_INT); // GRP

      $dbst->bindValue(':fromRow', (int)$fromRow, PDO::PARAM_INT);
      $dbst->bindValue(':rows', (int)$rows, PDO::PARAM_INT);
      $dbst->setFetchMode(PDO::FETCH_OBJ);
      $dbst->execute();
      return $dbst->fetchAll();
   }

   /**
    * Metoda vrací seznam uživatelů v systému s e-maily
    * @return PDOStatement
    */
   public function getUsersWithMails() {
      $dbc = new Db_PDO();
      $dbst = $dbc->query("SELECT users.*, grps.name AS gname FROM ".self::getUsersTable()." AS users"
          ." JOIN ".self::getGroupsTable()." AS grps ON users.".self::COLUMN_ID_GROUP." = grps.".self::COLUMN_ID_GROUP
          ." WHERE ISNULL(users.".self::COLUMN_MAIL.") = 0 AND users.".self::COLUMN_MAIL." != ''"
          ." ORDER BY users.".self::COLUMN_ID);
      $dbst->execute();
      return $dbst;
   }

   public function saveUser($username,$name,$surname, $password,$group, $email,$note,$blocked = false, $id = null) {
      $dbc = new Db_PDO();
      if($id === null) {
      // nový uživatel
         $password = Auth::cryptPassword($password);
         $dbst = $dbc->prepare("INSERT INTO ".self::getUsersTable()
             ." (`".self::COLUMN_USERNAME."`, `".self::COLUMN_NAME."`, `".self::COLUMN_SURNAME."`,
        `".self::COLUMN_PASSWORD."`,`".self::COLUMN_ID_GROUP."`,`".self::COLUMN_MAIL."`,
         `".self::COLUMN_NOTE."`,`".self::COLUMN_BLOCKED."`)"
             ." VALUES (:username, :name, :surname, :pass, :idgrp, :mail, :note, :blocked)");
         $dbst->bindValue(':pass', $password);
      } else {
      // existující uživatel
         $passSql = null;
         if($password != null){
            $passSql = "`".self::COLUMN_PASSWORD."` = ".$dbc->quote(Auth::cryptPassword($password)).",";
         }

         $dbst = $dbc->prepare("UPDATE ".self::getUsersTable(). " SET"
                ." `".self::COLUMN_USERNAME."` = :username, `".self::COLUMN_NAME."` = :name,"
                ." `".self::COLUMN_SURNAME."` = :surname, ".$passSql
                ." `".self::COLUMN_ID_GROUP."` = :idgrp, `".self::COLUMN_MAIL."` = :mail,"
                ." `".self::COLUMN_NOTE."` = :note, `".self::COLUMN_BLOCKED."` = :blocked"
                ." WHERE (".self::COLUMN_ID." = :iduser)");
         $dbst->bindValue(':iduser', $id);
      }

      $dbst->bindValue(':username', $username);
      $dbst->bindValue(':name', $name);
      $dbst->bindValue(':surname', $surname);
      $dbst->bindValue(':idgrp', (int)$group, PDO::PARAM_INT);
      $dbst->bindValue(':mail', $email);
      $dbst->bindValue(':note', $note);
      $dbst->bindValue(':blocked', $blocked, PDO::PARAM_BOOL);
      $dbst->execute();

      if($id == null) $id = $dbc->lastInsertId();
      return (int)$id;
   }

   /**
    * Metoda pro vypnutí uživatele
    * @param int $id -- id uživatele
    */
   public function disableUser($id) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("UPDATE ".self::getUsersTable(). " SET"
                ." `".self::COLUMN_BLOCKED."` = 1"
                ." WHERE (".self::COLUMN_ID." = :iduser)");
      $dbst->bindValue(':iduser', (int)$id, PDO::PARAM_INT);
      $dbst->execute();
      return $dbst;
   }

   /**
    * Metoda pro zapnutí uživatele
    * @param int $id -- id uživatele
    */
   public function enableUser($id) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("UPDATE ".self::getUsersTable(). " SET"
                ." `".self::COLUMN_BLOCKED."` = 0"
                ." WHERE (".self::COLUMN_ID." = :iduser)");
      $dbst->bindValue(':iduser', (int)$id, PDO::PARAM_INT);
      $dbst->execute();
      return $dbst;
   }

   /**
    * Metoda smaže uživatele z db
    * @param int $id -- id uživatele
    */
   public function deleteUser($id) {
      $dbc = new Db_PDO();
      return $dbc->query("DELETE FROM ".self::getUsersTable()
          . " WHERE ".self::COLUMN_ID." = ".$dbc->quote((int)$id));
   }

   public function saveGroup($name,$label, $id = null) {
      $dbc = new Db_PDO();
      if($id === null) {
      // nová skupina
         $dbst = $dbc->prepare("INSERT INTO ".self::getGroupsTable()
             ." (`name`, `".self::COLUMN_GROUP_LABEL."`)"
             ." VALUES (:name, :label)");
      } else {
      // existující skupina
         $dbst = $dbc->prepare("UPDATE ".self::getGroupsTable(). " SET"
                ." `name` = :name, `".self::COLUMN_GROUP_LABEL."` = :label"
                ." WHERE (".self::COLUMN_GROUP_ID." = :idgrp)");
          $dbst->bindValue(':idgrp', (int)$id, PDO::PARAM_INT);
      }

      $dbst->bindValue(':name', $name);
      $dbst->bindValue(':label', $label);

      $dbst->execute();
      return $dbc->lastInsertId();
   }

   /**
    * Metoda smaže skupinu z db
    * @param int $id -- id skupiny
    */
   public function deleteGroup($id) {
      $dbc = new Db_PDO();
      return $dbc->query("DELETE FROM ".self::getGroupsTable()
          . " WHERE ".self::COLUMN_GROUP_ID." = ".$dbc->quote((int)$id));
   }

   /**
    * Metoda změní heslo pro zadaného uživatele
    * @param int $iduser -- id uživatele
    * @param string $newPass -- nové heslo
    * @return PDOStatement
    */
   public function changeUserPassword($iduser, $newPass) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("UPDATE ".self::getUsersTable(). " SET"
                ." `".self::COLUMN_PASSWORD."` = :password"
                ." WHERE (".self::COLUMN_ID." = :iduser)");
      $dbst->execute(array(':iduser' => $iduser, ':password' => Auth::cryptPassword($newPass)));
      return $dbst;
   }

   /**
    * Metoda vrací název tabulky s uživateli (včetně prefixu)
    * @return string -- název tabulky
    */
   public static function getUsersTable() {
      if(VVE_USE_GLOBAL_ACCOUNTS === true) {
         return VVE_GLOBAL_TABLES_PREFIX.self::DB_TABLE;
      } else {
         return Db_PDO::table(self::DB_TABLE);
      }

   }

   /**
    * Metoda vrací název tabulky se skupinami (včetně prefixu)
    * @return string -- název tabulky
    */
   public static function getGroupsTable() {
      if(VVE_USE_GLOBAL_ACCOUNTS === true) {
         return VVE_GLOBAL_TABLES_PREFIX.self::DB_TABLE_GROUPS;
      } else {
         return Db_PDO::table(self::DB_TABLE_GROUPS);
      }
   }
}
?>