<?php

/*
 * Třída modelu detailem článku
 */

class Courses_Model extends Model_ORM {

   const DB_TABLE = 'courses';
   const DB_TABLE_LECTURERS_HAS_COURSES = 'courses_has_lecturers';
   const DB_TABLE_COURSES_HAS_USERS = 'courses_has_users';

   /**
    * Názvy sloupců v databázi
    */
   const COLUMN_ID = 'id_course';
   const COLUMN_TYPE = 'type';
   const COLUMN_ID_USER = 'id_user';
   const COLUMN_URLKEY = 'url_key';
   const COLUMN_NAME = 'name';
   const COLUMN_TEXT_SHORT = 'text_short';
   const COLUMN_TEXT_PRIVATE = 'text_private';
   const COLUMN_TEXT = 'text';
   const COLUMN_TEXT_CLEAR = 'text_clear';
   const COLUMN_DATE_START = 'date_start';
   const COLUMN_DATE_STOP = 'date_stop';
   const COLUMN_TIME_ADD = 'time_add';
   const COLUMN_TIME_EDIT = 'time_edit';
   const COLUMN_PRICE = 'price';
   const COLUMN_PLACE = 'place';
   const COLUMN_HOURS_LEN = 'hours_lenght';
   const COLUMN_SEATS = 'seats';
   const COLUMN_SEATS_BLOCKED = 'seats_blocked';
   const COLUMN_IS_NEW = 'is_new';
   const COLUMN_ALLOW_REG = 'allow_registration';
   const COLUMN_DELETED = 'deleted';
   const COLUMN_IMAGE = 'image';
   const COLUMN_DESCRIPTION = 'description';
   const COLUMN_KEYWORDS = 'keywords';
   const COLUMN_FEED = 'rss_feed';
   const COLUMN_IN_LIST = 'show_in_list';
   const COLUMN_AKREDIT_MPSV = 'akreditace_mpsv';
   const COLUMN_AKREDIT_MSMT = 'akreditace_msmt';
   const COLUMN_TAGRT_GROUPS = 'target_groups';
   const COLUMN_TIME_START = 'time_start';
   const COLUMN_L_H_C_ID_COURSE = 'id_course';
   const COLUMN_L_H_C_ID_LECTURER = 'id_lecturer';
   const COLUMN_C_H_U_ID_COURSE = 'id_course';
   const COLUMN_C_H_U_ID_USER = 'id_user';

   protected function _initTable()
   {
      $this->setTableName(self::DB_TABLE, 't_course');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'int', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_TYPE, array('datatype' => 'varchar(15)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR, 'default' => 'kurz'));
      $this->addColumn(self::COLUMN_ID_USER, array('datatype' => 'int', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_URLKEY, array('datatype' => 'varchar(200)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(200)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true, 'fulltextRel' => VVE_SEARCH_ARTICLE_REL_MULTIPLIER));
      $this->addColumn(self::COLUMN_TEXT_SHORT, array('datatype' => 'varchar(700)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TEXT_PRIVATE, array('datatype' => 'text', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TEXT, array('datatype' => 'text', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TEXT_CLEAR, array('datatype' => 'text', 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true));
      $this->addColumn(self::COLUMN_DATE_START, array('datatype' => 'date', 'pdoparam' => PDO::PARAM_STR, 'nn' => true));
      $this->addColumn(self::COLUMN_DATE_STOP, array('datatype' => 'date', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TIME_ADD, array('datatype' => 'timestamp', 'pdoparam' => PDO::PARAM_STR, 'default' => 'CURRENT_TIMESTAMP'));
      $this->addColumn(self::COLUMN_TIME_EDIT, array('datatype' => 'datetime', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_PRICE, array('datatype' => 'int(6)', 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_HOURS_LEN, array('datatype' => 'smallint(6)', 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_SEATS, array('datatype' => 'smallint(6)', 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_SEATS_BLOCKED, array('datatype' => 'smallint(6)', 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_PLACE, array('datatype' => 'varchar(600)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_IMAGE, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_IS_NEW, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => false));
      $this->addColumn(self::COLUMN_DELETED, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => false));
      $this->addColumn(self::COLUMN_ALLOW_REG, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => true));
      $this->addColumn(self::COLUMN_FEED, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => true));
      $this->addColumn(self::COLUMN_IN_LIST, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => true));
      $this->addColumn(self::COLUMN_AKREDIT_MPSV, array('datatype' => 'varchar(50)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_AKREDIT_MSMT, array('datatype' => 'varchar(50)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TAGRT_GROUPS, array('datatype' => 'varchar(500)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TIME_START, array('datatype' => 'time', 'pdoparam' => PDO::PARAM_STR));

      $this->addColumn(self::COLUMN_KEYWORDS, array('datatype' => 'varchar(200)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_DESCRIPTION, array('datatype' => 'varchar(700)', 'pdoparam' => PDO::PARAM_STR));

      $this->setPk(self::COLUMN_ID);

//       $this->addForeignKey(self::COLUMN_ID_CATEGORY, 'Model_Categories', Model_Category::COLUMN_CAT_ID);
      $this->addForeignKey(self::COLUMN_ID_USER, 'Model_Users');

      $this->addRelatioOneToMany(self::COLUMN_ID, Courses_Model_Registrations::class, Courses_Model_Registrations::COLUMN_ID_COURSE);
      $this->addRelatioOneToMany(self::COLUMN_ID, Courses_Model_Lecturers::class, Courses_Model_Lecturers::COLUMN_L_H_C_ID_COURSE);
   }

   protected function beforeSave(\Model_ORM_Record $record, $type = 'U')
   {
      parent::beforeSave($record, $type);
      $record->{self::COLUMN_TEXT_CLEAR} = strip_tags($record->{self::COLUMN_TEXT});
      
      $record->{self::COLUMN_URLKEY} = $this->createUniqueUrlKey($record->{self::COLUMN_URLKEY}, $record->{self::COLUMN_NAME}, $record->getPK());
   }
   
   protected function createUniqueUrlKey($key, $name, $id = 0)
   {
      if($key == null){
         $key = Utils_Url::toUrlKey($name);
      }
      
      $model = new self();
      $step = 1;
      $key = Utils_Url::toUrlKey($key);
      $origPart = $key;
      
      $where = self::COLUMN_URLKEY.' = :ukey AND '.self::COLUMN_ID.' != :id';
      $keys = array('ukey' => $key, 'id' => (int)$id);// when is nul bad sql query is created
      
      while ($model->where($where, $keys)->count() != 0 ) {
         $keys['ukey'] = $origPart.'-'.$step;
         $step++;
      }
      return $keys['ukey'];
   }
   
   /**
    * Metoda uloží novinku do db
    *
    * @param array -- pole s nadpisem článku
    * @param array -- pole s textem článku
    * @param boolean -- id uživatele
    */
   public function saveCourse($name, $textShort, $text, $textPrivate, $urlKey, $desc, $keywords, DateTime $dateStart, $dateStop, $price, $hours, $place, $seats, $seatsBlocked, $isNew, $inList, $allowReg, $type, $image, $idLecturers, $idUsers, $isFeed, $akredMpsv, $akredMsmt, $targetGroups, $timeStart, $id = null)
   {
      // generování unikátního klíče
      $urlKey = $this->generateUrlKeys($urlKey, self::DB_TABLE, $name, self::COLUMN_URLKEY, self::COLUMN_ID, $id);

      $dbc = Db_PDO::getInstance();
      if ($id !== null) {
         $dbst = $dbc->prepare("UPDATE " . Db_PDO::table(self::DB_TABLE)
                 . " SET " . self::COLUMN_NAME . "= :name, " . self::COLUMN_TEXT_SHORT . "= :textShort, "
                 . self::COLUMN_TEXT . "= :text, " . self::COLUMN_TEXT_CLEAR . "= :textClear, "
                 . self::COLUMN_URLKEY . "= :urlkey, " . self::COLUMN_DATE_START . "= :dateStart, "
                 . self::COLUMN_DESCRIPTION . "= :metaDesc, " . self::COLUMN_KEYWORDS . "= :keywords, "
                 . self::COLUMN_DATE_STOP . "= :dateStop, " . self::COLUMN_PRICE . "= :price, "
                 . self::COLUMN_PLACE . "= :place, " . self::COLUMN_HOURS_LEN . "= :hoursLen, "
                 . self::COLUMN_SEATS . "= :seats, " . self::COLUMN_SEATS_BLOCKED . "= :seatsBlocked, "
                 . self::COLUMN_IS_NEW . "= :isNew, " . self::COLUMN_IN_LIST . "= :isShowed, " . self::COLUMN_ALLOW_REG . "= :allowReg, "
                 . self::COLUMN_TYPE . "= :type, " . self::COLUMN_FEED . "= :isFeed, " . self::COLUMN_TEXT_PRIVATE . "= :textPrivate, "
                 . self::COLUMN_IMAGE . "= :image, " . self::COLUMN_ID_USER . "= :idUser,"
                 . self::COLUMN_AKREDIT_MPSV . "= :akredmpsv, " . self::COLUMN_AKREDIT_MSMT . "= :akredmsmt,"
                 . self::COLUMN_TAGRT_GROUPS . "= :targetgroups, " . self::COLUMN_TIME_START . "= :timestart"
                 . " WHERE " . self::COLUMN_ID . " = :idc");
         $dbst->bindParam(':idc', $id, PDO::PARAM_INT);
      } else {
         $dbst = $dbc->prepare("INSERT INTO " . Db_PDO::table(self::DB_TABLE) . " "
                 . "(" . self::COLUMN_NAME . "," . self::COLUMN_TEXT_SHORT . "," . self::COLUMN_TEXT . ","
                 . self::COLUMN_TEXT_PRIVATE . "," . self::COLUMN_TEXT_CLEAR . "," . self::COLUMN_URLKEY . ","
                 . self::COLUMN_DESCRIPTION . "," . self::COLUMN_KEYWORDS . ","
                 . self::COLUMN_DATE_START . "," . self::COLUMN_DATE_STOP . ","
                 . self::COLUMN_PRICE . "," . self::COLUMN_PLACE . "," . self::COLUMN_HOURS_LEN . ","
                 . self::COLUMN_SEATS . "," . self::COLUMN_SEATS_BLOCKED . ","
                 . self::COLUMN_IS_NEW . "," . self::COLUMN_IN_LIST . "," . self::COLUMN_ALLOW_REG . "," . self::COLUMN_TYPE . "," . self::COLUMN_IMAGE . ","
                 . self::COLUMN_ID_USER . ", " . self::COLUMN_FEED . ","
                 . self::COLUMN_AKREDIT_MPSV . "," . self::COLUMN_AKREDIT_MSMT . "," . self::COLUMN_TAGRT_GROUPS . "," . self::COLUMN_TIME_START . ")"
                 . " VALUES (:name, :textShort, :text, :textPrivate, :textClear, :urlkey, :metaDesc, :keywords,"
                 . " :dateStart, :dateStop, :price, :place, :hoursLen, :seats,"
                 . " :seatsBlocked, :isNew, :isShowed, :allowReg, :type, :image, :idUser, :isFeed,"
                 . " :akredmpsv, :akredmsmt, :targetgroups, :timestart)");
      }
      $dbst->bindValue(':name', $name, PDO::PARAM_STR);
      $dbst->bindValue(':textShort', $textShort, PDO::PARAM_STR);
      $dbst->bindValue(':text', $text, PDO::PARAM_STR);
      $dbst->bindValue(':textPrivate', $textPrivate, PDO::PARAM_STR);
      $dbst->bindValue(':textClear', vve_strip_tags($text . ' ' . $textShort), PDO::PARAM_STR);
      $dbst->bindValue(':urlkey', $urlKey, PDO::PARAM_STR);
      $dbst->bindValue(':metaDesc', $desc, PDO::PARAM_STR);
      $dbst->bindValue(':keywords', $keywords, PDO::PARAM_STR);
      $dbst->bindValue(':dateStart', $dateStart->format("Y-m-d"), PDO::PARAM_STR);
      if ($dateStop != null and $dateStop instanceof DateTime) {
         $dbst->bindValue(':dateStop', $dateStop->format("Y-m-d"), PDO::PARAM_STR);
      } else {
         $dbst->bindValue(':dateStop', null, PDO::PARAM_NULL);
      }
      $dbst->bindValue(':price', (int) $price, PDO::PARAM_INT);
      $dbst->bindValue(':hoursLen', (int) $hours, PDO::PARAM_INT);
      $dbst->bindValue(':place', $place, PDO::PARAM_STR);
      $dbst->bindValue(':seats', (int) $seats, PDO::PARAM_INT);
      $dbst->bindValue(':seatsBlocked', (int) $seatsBlocked, PDO::PARAM_INT);
      $dbst->bindValue(':isNew', (bool) $isNew, PDO::PARAM_BOOL);
      $dbst->bindValue(':isShowed', (bool) $inList, PDO::PARAM_BOOL);
      $dbst->bindValue(':allowReg', (bool) $allowReg, PDO::PARAM_BOOL);
      $dbst->bindValue(':type', $type, PDO::PARAM_STR);
      $dbst->bindValue(':image', $image, PDO::PARAM_STR | PDO::PARAM_NULL);
      $dbst->bindValue(':idUser', Auth::getUserId(), PDO::PARAM_INT);
      $dbst->bindValue(':isFeed', (bool) $isFeed, PDO::PARAM_INT);

      $dbst->bindValue(':akredmpsv', $akredMpsv, PDO::PARAM_STR | PDO::PARAM_NULL);
      $dbst->bindValue(':akredmsmt', $akredMsmt, PDO::PARAM_STR | PDO::PARAM_NULL);
      $dbst->bindValue(':targetgroups', $targetGroups, PDO::PARAM_STR | PDO::PARAM_NULL);
      $dbst->bindValue(':timestart', $timeStart, PDO::PARAM_STR | PDO::PARAM_NULL);

      $dbst->execute();

      if ($id == null) {
         $id = $dbc->lastInsertId();
      }

      // smažeme předchozí spojení lektor <> kurz
      $this->deleteCourseLecturersConnections($id);
      if ($idLecturers) {
         foreach ($idLecturers as $idL) {
            $this->saveCourseLecturerConnect($id, $idL);
         }
      }
      // smažeme předchozí spojení kurz <> privátní uživatelé
      $this->deleteCourseUsersConnections($id);
      if ($idUsers) {
         foreach ($idUsers as $idU) {
            $this->saveCourseUserConnect($id, (int) $idU);
         }
      }

      // uložení místa
      $modlePlaces = new Courses_Model_Places();
      $modlePlaces->savePlace($place);

      return $id;
   }

   /**
    * Metoda vrací kurzu podle zadaného klíče
    *
    * @param string -- url klíč kurzu
    * @return PDOStatement -- pole s kurzem
    */
   public function getCourse($urlKey)
   {
      $m = new self();
      return  $m->where(self::COLUMN_URLKEY." = :ukey", array('ukey' => $urlKey))
                  ->record();
   }

   /**
    * Metoda vrací kurzy
    *
    * @return PDOStatement -- pole s kurzy
    */
   public function getCourses($onlyVisible = true, $fromRow = 0, $numRows = 500, $type = null)
   {
      $conds = array(self::COLUMN_DELETED . " = 0 ");
      $bindValues = array();
      if ($onlyVisible == true) {
         $conds[] = self::COLUMN_IN_LIST . " = 1";
      }
      if ($type != null) {
         $conds[] = self::COLUMN_TYPE . " = :type";
         $bindValues['type'] = $type;
      }
      $m = new self();
      return $m->where(implode(' AND ', $conds), $bindValues)
                      ->limit($fromRow, $numRows)
                      ->order(array(self::COLUMN_DATE_START))
                      ->records();
   }

   /**
    * Metoda vrací kurzy pro rss feed
    *
    * @return PDOStatement -- pole s kurzy
    */
   public function getCoursesForFeed($numRows = 20)
   {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare("SELECT tc.*, tu." . Model_Users::COLUMN_USERNAME . " FROM " . Db_PDO::table(self::DB_TABLE) . " AS tc"
              . " JOIN " . Db_PDO::table(Model_Users::DB_TABLE) . " AS tu ON tu." . Model_Users::COLUMN_ID . " = tc." . self::COLUMN_ID_USER
              . " WHERE tc." . self::COLUMN_DELETED . " = 0 AND tc." . self::COLUMN_FEED . " = 1 AND " . self::COLUMN_IN_LIST . " = 1"
              . " ORDER BY tc." . self::COLUMN_DATE_START . " ASC"
              . " LIMIT 0, :numRows");
      $dbst->bindValue(':numRows', (int) $numRows, PDO::PARAM_INT);
      $dbst->execute();
      $dbst->setFetchMode(PDO::FETCH_OBJ);
      return $dbst->fetchAll();
   }

   /**
    * Metoda vrací kurzy od zadaného data
    *
    * @return PDOStatement -- pole s kurzy
    */
   public static function getCoursesFromDate(DateTime $date, $onlyVisible = true, $fromRow = 0, $numRows = 500, $type = null)
   {
      $model = new self();

      $whereStrings = array(
          self::COLUMN_DELETED . " = 0 AND " . self::COLUMN_DATE_START . " >= :dates"
      );
      $whereValues = array('dates' => $date->format(DATE_ISO8601));
      if ($onlyVisible == true) {
         $whereStrings[] = self::COLUMN_IN_LIST . " = 1";
      }
      if ($type != null) {
         $whereStrings[] = self::COLUMN_TYPE . ' = :type';
         $whereValues['type'] = $type;
      }

      return $model
                      ->where(implode(' AND ', $whereStrings), $whereValues)
                      ->limit($fromRow, $numRows)
                      ->records()
      ;
   }

   /**
    * Metoda vrací kurz podle zadaného ID
    *
    * @param int -- id kurzu
    * @return PDOStatement -- pole s kurzem
    */
   public function getCourseById($id)
   {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare("SELECT * FROM " . Db_PDO::table(self::DB_TABLE)
              . " WHERE (" . self::COLUMN_ID . " = :id)"
              . " LIMIT 0, 1");
      $dbst->execute(array(':id' => (int) $id));
      $dbst->setFetchMode(PDO::FETCH_OBJ);
      return $dbst->fetch();
   }

   /**
    * Metoda vrací poslední změnu kurzů
    * @return DateTime -- timestamp
    */
   public function getLastChange()
   {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare("SELECT " . self::COLUMN_TIME_EDIT . " AS et"
              . " FROM " . Db_PDO::table(self::DB_TABLE) . " AS tbcourse"
              . " ORDER BY " . self::COLUMN_TIME_EDIT . " DESC"
              . " LIMIT 0, 1");
      $dbst->execute();

      $fetch = $dbst->fetchObject();
      if ($fetch != false) {
         return new DateTime((string) $fetch->et);
      }
      return false;
   }

}

class Courses_Model_Record extends Model_ORM_Record {

   private $lecturers = false;


   public function getLecturers()
   {
      if($this->lecturers === false){
         $m = new Courses_Model_Lecturers();
         $this->lecturers = $m->joinFK(Courses_Model_Lecturers::COLUMN_L_H_C_ID_LECTURER)
                 ->order(array(People_Model::COLUMN_SURNAME))
                 ->where(Courses_Model_Lecturers::COLUMN_L_H_C_ID_COURSE.' = :idc', array('idc' => $this->getPK()))
                 ->records();
      
      }
      return $this->lecturers;
   }
   
   public function assignLecturers($lecturersIds = array())
   {
      $m = new Courses_Model_Lecturers();
      $m->where(Courses_Model_Lecturers::COLUMN_L_H_C_ID_COURSE.' = :idc', array('idc' => $this->getPK()))->delete();
      
      if(!empty($lecturersIds)){
         foreach ($lecturersIds as $id) {
            $newConn = Courses_Model_Lecturers::getNewRecord();
            $newConn->{Courses_Model_Lecturers::COLUMN_L_H_C_ID_COURSE} = $this->getPK();
            $newConn->{Courses_Model_Lecturers::COLUMN_L_H_C_ID_LECTURER} = $id;
            $newConn->save();
         }
      }
      $this->lecturers = false;
   }
   
   
   public function getUsers()
   {
      return array();
   }
   
   public function assignPrivateUsers($usersIds = array())
   {
      $m = new Courses_Model_PrivateUsers();
      $m->where(Courses_Model_PrivateUsers::COLUMN_C_H_U_ID_COURSE.' = :idc', array('idc' => $this->getPK()))->delete();
      
      if(!empty($usersIds)){
         foreach ($usersIds as $id) {
            $newConn = Courses_Model_PrivateUsers::getNewRecord();
            $newConn->{Courses_Model_PrivateUsers::COLUMN_C_H_U_ID_COURSE} = $this->getPK();
            $newConn->{Courses_Model_PrivateUsers::COLUMN_C_H_U_ID_USER} = $id;
            $newConn->save();
         }
      }
   }
   
   public function isPrivateUser($idUser)
   {
      $m = new Courses_Model_PrivateUsers();
      
      return (bool)$m->where(Courses_Model_PrivateUsers::COLUMN_C_H_U_ID_USER.' = :idu AND '.Courses_Model_PrivateUsers::COLUMN_C_H_U_ID_COURSE.' = :idc', 
              array('idu' => $idUser, 'idc' => $this->getPK()))->count();
   }
}
