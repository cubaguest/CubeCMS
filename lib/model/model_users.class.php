<?php
/**
 * Třída s modelem pro práci s uživateli
 *
 * @copyright     Copyright (c) 2008-2009 Jakub Matas
 * @version       $Id: model_module.class.php 625 2009-06-13 16:01:09Z jakub $ VVE 5.1.0 $Revision: 625 $
 * @author        $Author: jakub $ $Date: 2009-06-13 16:01:09 +0000 (So, 13 čen 2009) $
 *                $LastChangedBy: jakub $ $LastChangedDate: 2009-06-13 16:01:09 +0000 (So, 13 čen 2009) $
 * @abstract      Třída s modelem pro práci s uživateli
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
   const COLUMN_CREATED    = 'created';
   const COLUMN_LAST_LOGIN = 'last_login';
   const COLUMN_EXTERNAL_AUTH_ID    = 'external_auth_id';
   const COLUMN_AUTHENTICATOR  = 'authenticator';
   const COLUMN_ADDRESS  = 'user_address';
   const COLUMN_PHONE  = 'user_phone';
   const COLUMN_INFO_IS_PRIVATE  = 'user_info_is_private';

   const COLUMN_GROUP_ID    = 'id_group';
   const COLUMN_GROUP_NAME    = 'gname';
   const COLUMN_GROUP_LABEL    = 'label';

   protected function  _initTable() {
      if(VVE_USE_GLOBAL_ACCOUNTS === true) {
         $this->setTableName(VVE_GLOBAL_TABLES_PREFIX.self::DB_TABLE, 't_us', false);
      } else {
         $this->setTableName(self::DB_TABLE, 't_us');
      }

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'int', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_GROUP, array('datatype' => 'int', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
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
      $this->addColumn(self::COLUMN_CREATED, array('datatype' => 'datetime', 'pdoparam' => PDO::PARAM_STR, 'default' => 'CURRENT_TIMESTAMP'));
      $this->addColumn(self::COLUMN_LAST_LOGIN, array('datatype' => 'datetime', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_EXTERNAL_AUTH_ID, array('datatype' => 'varchar(200)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_AUTHENTICATOR, array('datatype' => 'varchar(20)', 'pdoparam' => PDO::PARAM_STR, 'default' => 'internal'));
      $this->addColumn(self::COLUMN_ADDRESS, array('datatype' => 'varchar(500)', 'pdoparam' => PDO::PARAM_STR, 'default' => null));
      $this->addColumn(self::COLUMN_PHONE, array('datatype' => 'varchar(15)', 'pdoparam' => PDO::PARAM_STR, 'default' => null));
      $this->addColumn(self::COLUMN_INFO_IS_PRIVATE, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => false));

      $this->setPk(self::COLUMN_ID);
      $this->addForeignKey(self::COLUMN_ID_GROUP, 'Model_Groups');
   }
   
   /**
    * Metoday nastaví výběr na uživatele připojené k aktuálnímu webu
    * @return Model_Groups
    */
   public function usersForThisWeb($withAdmins = false)
   {
      $model = $this->joinFK(Model_Users::COLUMN_ID_GROUP)
         ->join(Model_Groups::COLUMN_ID, array('sitegrps' => 'Model_SitesGroups'), Model_SitesGroups::COLUMN_ID_GROUP, array())
         ->join(array('sitegrps' => Model_SitesGroups::COLUMN_ID_SITE), 'Model_Sites', Model_Sites::COLUMN_ID, array(Model_Sites::COLUMN_DOMAIN));
      if($withAdmins){
         $model->where('( ISNULL(`'.Model_Sites::COLUMN_DOMAIN.'`) OR  `'.Model_Sites::COLUMN_DOMAIN.'` = :domain)', 
            array('domain' => VVE_SUB_SITE_DOMAIN == null ? 'www' : VVE_SUB_SITE_DOMAIN));
      } else {
         $model->where(Model_Groups::COLUMN_IS_ADMIN.' = 0 AND ( ISNULL(`'.Model_Sites::COLUMN_DOMAIN.'`) OR  `'.Model_Sites::COLUMN_DOMAIN.'` = :domain)', 
            array('domain' => VVE_SUB_SITE_DOMAIN == null ? 'www' : VVE_SUB_SITE_DOMAIN));
      }
      return $model;
   }

   /**
    * Funkc vrací uživatele s emailem v poli, ID je klíč a hodnota obsahuje jméno a email
    * @param bool $onlyThisWebsite -- pouze z tohoto webu
    * @return array
    */
   public static function getUsersWithMails($onlyThisWebsite = true)
   {
      $model = new self();
      if($onlyThisWebsite){
         $model->usersForThisWeb(true);
      }
      $users = $model->records(PDO::FETCH_OBJ);
      $usersIds = array();
      foreach ($users as $user) {
         if($user->{self::COLUMN_MAIL} != null){
            $usersIds[$user->{self::COLUMN_ID}] = $user->{self::COLUMN_NAME} ." ".$user->{self::COLUMN_SURNAME}
               .' ('.$user->{self::COLUMN_USERNAME}.') - '.$user->{self::COLUMN_MAIL};
         }
      }
      return $usersIds;
   }
   
   /**
    * Vrací uživatele se skupinou
    * @param int $idUser
    * @return Model_ORM_Record
    */
   public static function getUsersWithGroup($idUser)
   {
      $model = new self();
      return $model->joinFK(self::COLUMN_GROUP_ID)->where(self::COLUMN_ID." = :idu", array('idu' => $idUser))->record();
   }
   
   public static function getUsersByUsernameAndAuth($username, $auth) 
   {
      $model = new Model_Users();
      return $model
             ->where(Model_Users::COLUMN_USERNAME.' = :username AND '.Model_Users::COLUMN_EXTERNAL_AUTH_ID." = :ident",
               array('username' => $username, 'ident' => $auth))
             ->joinFK(Model_Users::COLUMN_ID_GROUP, array('group_name' => Model_Groups::COLUMN_NAME, '*'))
             ->record();
   }
   
   public static function getUsersByUsername($username) 
   {
      $model = new Model_Users();
      return $model
             ->where(Model_Users::COLUMN_USERNAME.' = :username', array('username' => $username))
             ->joinFK(Model_Users::COLUMN_ID_GROUP, array('group_name' => Model_Groups::COLUMN_NAME, '*'))
             ->record();
   }
   
   public static function getUsersByMail($mail) 
   {
      $model = new Model_Users();
      return $model
             ->where(Model_Users::COLUMN_MAIL.' = :username AND '.Model_Users::COLUMN_MAIL.' IS NOT NULL', array('username' => $mail))
             ->joinFK(Model_Users::COLUMN_ID_GROUP, array('group_name' => Model_Groups::COLUMN_NAME, '*'))
             ->record();
   }
   
   public static function getUserByID($id) 
   {
      $model = new Model_Users();
      return $model
             ->where(Model_Users::COLUMN_ID.' = :id', array('id' => $id))
             ->joinFK(Model_Users::COLUMN_ID_GROUP, array('group_name' => Model_Groups::COLUMN_NAME, '*'))
             ->record();
   }


   /* DEPRECATED */

   /**
    * Metoda načte uživatele podle uživatelského jména
    * @param string $username -- uživatelské jméno
    * @deprecated
    */
   public function getUser($username, $blockedUsers = false) {
      $mgrp = new Model_Groups();
      $where = null;
      if($blockedUsers === false){
         $where = ' AND user.'.self::COLUMN_BLOCKED.' = :blocked';
      }
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare("SELECT *, grp.name AS gname FROM ".$this->getTableName()." AS user
             JOIN ".$mgrp->getTableName()." AS grp ON user.".self::COLUMN_ID_GROUP
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
    * Metoda vrací seznam uživatelů v systému
    * @return PDOStatement
    * @deprecated
    */
   public function getUsersList() {
      $mgrp = new Model_Groups();
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->query("SELECT users.*, grps.name AS gname FROM ".$this->getTableName()." AS users"
          ." JOIN ".$mgrp->getTableName()." AS grps ON users.".self::COLUMN_ID_GROUP." = grps.".self::COLUMN_ID_GROUP
//          ." WHERE users.".self::COLUMN_BLOCKED." = 0"
          ." ORDER BY users.".self::COLUMN_ID);
      $dbst->execute();
      return $dbst;
   }

   /**
    * @param null $idgrp
    * @return mixed
    * @deprecated
    */
   public function getCount($idgrp = null){
      $dbc = Db_PDO::getInstance();
      $sql = "SELECT COUNT(*) FROM ".$this->getTableName();
      if($idgrp !== null) $sql .= " WHERE ".self::COLUMN_ID_GROUP." = :idGrp";
      $dbst = $dbc->prepare($sql);
      $dbst->execute(array(':idGrp' => $idgrp));
      $count = $dbst->fetch();
      return $count[0];
   }

   /**
    * @param int $fromRow
    * @param int $rows
    * @param string $orderCol
    * @param string $order
    * @param null $idgrp
    * @return array
    * @deprecated
    */
   public function getUsers($fromRow = 0 , $rows = 1000, $orderCol = self::COLUMN_ID, $order = 'ASC', $idgrp = null) {
      $this->isValidColumn($orderCol, array(self::COLUMN_ID, self::COLUMN_NAME, self::COLUMN_SURNAME, self::COLUMN_USERNAME,
         self::COLUMN_MAIL, self::COLUMN_NOTE, self::COLUMN_BLOCKED));
      $this->isValidOrder($order);

      $dbc = Db_PDO::getInstance();

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
    * @param $username
    * @param $name
    * @param $surname
    * @param $password
    * @param $group
    * @param $email
    * @param $note
    * @param bool $blocked
    * @param null $id
    * @return int
    * @deprecated
    */
   public function saveUser($username,$name,$surname, $password,$group, $email,$note,$blocked = false, $id = null) {
      $dbc = Db_PDO::getInstance();
      if($id === null) {
      // nový uživatel
         $password = Auth::cryptPassword($password);
         $dbst = $dbc->prepare("INSERT INTO ".$this->getTableName()
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

         $dbst = $dbc->prepare("UPDATE ".$this->getTableName(). " SET"
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
    * @deprecated
    */
   public function disableUser($id) {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare("UPDATE ".$this->getTableName(). " SET"
                ." `".self::COLUMN_BLOCKED."` = 1"
                ." WHERE (".self::COLUMN_ID." = :iduser)");
      $dbst->bindValue(':iduser', (int)$id, PDO::PARAM_INT);
      $dbst->execute();
      return $dbst;
   }

   /**
    * Metoda pro zapnutí uživatele
    * @param int $id -- id uživatele
    * @deprecated
    */
   public function enableUser($id) {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare("UPDATE ".$this->getTableName(). " SET"
                ." `".self::COLUMN_BLOCKED."` = 0"
                ." WHERE (".self::COLUMN_ID." = :iduser)");
      $dbst->bindValue(':iduser', (int)$id, PDO::PARAM_INT);
      $dbst->execute();
      return $dbst;
   }

   /**
    * Metoda smaže uživatele z db
    * @param int $id -- id uživatele
    * @deprecated
    */
   public function deleteUser($id) {
      $dbc = Db_PDO::getInstance();
      return $dbc->query("DELETE FROM ".$this->getTableName()
          . " WHERE ".self::COLUMN_ID." = ".$dbc->quote((int)$id));
   }

   /**
    * Metoda změní heslo pro zadaného uživatele
    * @param int $iduser -- id uživatele
    * @param string $newPass -- nové heslo
    * @return PDOStatement
    * @deprecated
    */
   public function changeUserPassword($iduser, $newPass) {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare("UPDATE ".$this->getTableName(). " SET"
                ." `".self::COLUMN_PASSWORD."` = :password"
                ." WHERE (".self::COLUMN_ID." = :iduser)");
      $dbst->execute(array(':iduser' => $iduser, ':password' => Auth::cryptPassword($newPass)));
      return $dbst;
   }
   
   /**
    * Metoda vrací pole s uživatelskými emaily
    * @return array
    */
   public static function getUsersMails($usersIds = array())
   {
      if(empty($usersIds)){
         return array();
      }
      $returnArray = array();
      $model = new Model_Users();
      $users = $model->where(Model_Users::COLUMN_ID.' IN ('.Model_ORM::getWhereINPlaceholders($usersIds).')', Model_ORM::getWhereINValues($usersIds))->records();
      foreach ($users as $user) {
         $returnArray[$user->{Model_Users::COLUMN_NAME}.' '.$user->{Model_Users::COLUMN_SURNAME}] = $user->{Model_Users::COLUMN_MAIL};
      }
      return $returnArray;
   }
}
