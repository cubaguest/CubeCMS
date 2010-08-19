<?php
/*
 * Třída modelu detailem článku
 */
class Courses_Model_Courses extends Model_PDO {
   const DB_TABLE = 'courses';
   const DB_TABLE_LECTURERS_HAS_COURSES = 'lecturers_has_courses';
   const DB_TABLE_COURSES_HAS_USERS = 'courses_has_users';

   /**
    * Názvy sloupců v databázi
    */
   const COLUMN_ID = 'id_course';
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

   const COLUMN_L_H_C_ID_COURSE = 'id_course';
   const COLUMN_L_H_C_ID_LECTURER = 'id_lecturer';

   const COLUMN_C_H_U_ID_COURSE = 'id_course';
   const COLUMN_C_H_U_ID_USER = 'id_user';

   /**
    * Metoda uloží novinku do db
    *
    * @param array -- pole s nadpisem článku
    * @param array -- pole s textem článku
    * @param boolean -- id uživatele
    */
   public function saveCourse($name, $textShort, $text, $textPrivate, $urlKey, $desc, $keywords,
      DateTime $dateStart, $dateStop, $price, $hours, $place, $seats, $seatsBlocked,
           $isNew, $allowReg, $image, $idLecturers, $idUsers, $isFeed, $id = null) {
      // generování unikátního klíče
      $urlKey = $this->generateUrlKeys($urlKey, self::DB_TABLE, $name,
              self::COLUMN_URLKEY, self::COLUMN_ID ,$id);

      $dbc = new Db_PDO();
      if($id !== null) {
         $dbst = $dbc->prepare("UPDATE ".Db_PDO::table(self::DB_TABLE)
                 ." SET ".self::COLUMN_NAME."= :name, ".self::COLUMN_TEXT_SHORT."= :textShort, "
                 .self::COLUMN_TEXT."= :text, ".self::COLUMN_TEXT_CLEAR."= :textClear, "
                 .self::COLUMN_URLKEY."= :urlkey, ".self::COLUMN_DATE_START."= :dateStart, "
                 .self::COLUMN_DESCRIPTION."= :metaDesc, ".self::COLUMN_KEYWORDS."= :keywords, "
                 .self::COLUMN_DATE_STOP."= :dateStop, ".self::COLUMN_PRICE."= :price, "
                 .self::COLUMN_PLACE."= :place, ".self::COLUMN_HOURS_LEN."= :hoursLen, "
                 .self::COLUMN_SEATS."= :seats, ".self::COLUMN_SEATS_BLOCKED."= :seatsBlocked, "
                 .self::COLUMN_IS_NEW."= :isNew, ".self::COLUMN_ALLOW_REG."= :allowReg, "
                 .self::COLUMN_FEED."= :isFeed, ".self::COLUMN_TEXT_PRIVATE."= :textPrivate, "
                 .self::COLUMN_IMAGE."= :image, ".self::COLUMN_ID_USER."= :idUser"
                  ." WHERE ".self::COLUMN_ID." = :idc");
         $dbst->bindParam(':idc', $id, PDO::PARAM_INT);
      } else {
         $dbst = $dbc->prepare("INSERT INTO ".Db_PDO::table(self::DB_TABLE)." "
                 ."(".self::COLUMN_NAME.",". self::COLUMN_TEXT_SHORT.",".self::COLUMN_TEXT.","
                 .self::COLUMN_TEXT_PRIVATE.",".self::COLUMN_TEXT_CLEAR.",". self::COLUMN_URLKEY.","
                 .self::COLUMN_DESCRIPTION.",". self::COLUMN_KEYWORDS.","
                 .self::COLUMN_DATE_START.",". self::COLUMN_DATE_STOP.","
                 .self::COLUMN_PRICE.",". self::COLUMN_PLACE.",". self::COLUMN_HOURS_LEN.","
                 .self::COLUMN_SEATS.",". self::COLUMN_SEATS_BLOCKED.","
                 .self::COLUMN_IS_NEW.",". self::COLUMN_ALLOW_REG.",". self::COLUMN_IMAGE.","
                 .self::COLUMN_ID_USER.", ".self::COLUMN_FEED.")"
                 ." VALUES (:name, :textShort, :text, :textPrivate, :textClear, :urlkey, :metaDesc, :keywords,"
                 ." :dateStart, :dateStop, :price, :place, :hoursLen, :seats,"
                 ." :seatsBlocked, :isNew, :allowReg, :image, :idUser, :isFeed".")");
      }
      $dbst->bindValue(':name', $name, PDO::PARAM_STR);
      $dbst->bindValue(':textShort', $textShort, PDO::PARAM_STR);
      $dbst->bindValue(':text', $text, PDO::PARAM_STR);
      $dbst->bindValue(':textPrivate', $textPrivate, PDO::PARAM_STR);
      $dbst->bindValue(':textClear', vve_strip_tags($text), PDO::PARAM_STR);
      $dbst->bindValue(':urlkey', $urlKey, PDO::PARAM_STR);
      $dbst->bindValue(':metaDesc', $desc, PDO::PARAM_STR);
      $dbst->bindValue(':keywords', $keywords, PDO::PARAM_STR);
      $dbst->bindValue(':dateStart', $dateStart->format("Y-m-d"), PDO::PARAM_STR);
      if($dateStop != null and $dateStop instanceof DateTime){
         $dbst->bindValue(':dateStop', $dateStop->format("Y-m-d"), PDO::PARAM_STR);
      } else {
         $dbst->bindValue(':dateStop', null, PDO::PARAM_NULL);
      }
      $dbst->bindValue(':price', (int)$price, PDO::PARAM_INT);
      $dbst->bindValue(':hoursLen', (int)$hours, PDO::PARAM_INT);
      $dbst->bindValue(':place', $place, PDO::PARAM_STR);
      $dbst->bindValue(':seats', (int)$seats, PDO::PARAM_INT);
      $dbst->bindValue(':seatsBlocked', (int)$seatsBlocked, PDO::PARAM_INT);
      $dbst->bindValue(':isNew', (bool)$isNew, PDO::PARAM_BOOL);
      $dbst->bindValue(':allowReg', (bool)$allowReg, PDO::PARAM_BOOL);
      $dbst->bindValue(':image', $image, PDO::PARAM_STR|PDO::PARAM_NULL);
      $dbst->bindValue(':idUser', Auth::getUserId(), PDO::PARAM_INT);
      $dbst->bindValue(':isFeed', (bool)$isFeed, PDO::PARAM_INT);

      $dbst->execute();

      if($id == null){
         $id = $dbc->lastInsertId();
      }

      // smažeme předchozí spojení lektor <> kurz
      $this->deleteCourseLecturersConnections($id);

      foreach ($idLecturers as $idL) {
         $this->saveCourseLecturerConnect($id, $idL);
      }
      // smažeme předchozí spojení kurz <> privátní uživatelé
      $this->deleteCourseUsersConnections($id);

      foreach ($idUsers as $idU) {
         $this->saveCourseUserConnect($id, (int)$idU);
      }

      // uložení místa
      $modlePlaces = new Courses_Model_Places();
      $modlePlaces->savePlace($place);
      
      return $id;
   }

   public function saveCourseLecturerConnect($idCourse, $idLecturer) {
      // smažeme předchozí spojení
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("INSERT INTO ".Db_PDO::table(self::DB_TABLE_LECTURERS_HAS_COURSES)." "
                 ."(".self::COLUMN_L_H_C_ID_COURSE.",". self::COLUMN_L_H_C_ID_LECTURER.")"
                 ." VALUES (:idCourse, :idLecturer)");
      $dbst->execute(array(":idCourse" => $idCourse, ':idLecturer' => $idLecturer));
   }

   public function deleteCourseLecturersConnections($idCourse) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("DELETE FROM ".Db_PDO::table(self::DB_TABLE_LECTURERS_HAS_COURSES)
          ." WHERE (".self::COLUMN_L_H_C_ID_COURSE ." = :idCourse)");
      $dbst->bindParam(':idCourse', $idCourse, PDO::PARAM_INT);
      return $dbst->execute();
   }

   public function saveCourseUserConnect($idCourse, $idUser) {
      // smažeme předchozí spojení
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("INSERT INTO ".Db_PDO::table(self::DB_TABLE_COURSES_HAS_USERS)." "
                 ."(".self::COLUMN_C_H_U_ID_COURSE.",". self::COLUMN_C_H_U_ID_USER.")"
                 ." VALUES (:idCourse, :idUser)");
      $dbst->execute(array(":idCourse" => $idCourse, ':idUser' => $idUser));
   }

   public function deleteCourseUsersConnections($idCourse) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("DELETE FROM ".Db_PDO::table(self::DB_TABLE_COURSES_HAS_USERS)
          ." WHERE (".self::COLUMN_C_H_U_ID_COURSE ." = :idCourse)");
      $dbst->bindParam(':idCourse', $idCourse, PDO::PARAM_INT);
      return $dbst->execute();
   }

   /**
    * Metoda vrací kurzu podle zadaného klíče
    *
    * @param string -- url klíč kurzu
    * @return PDOStatement -- pole s kurzem
    */
   public function getCourseUsers($idc) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE_COURSES_HAS_USERS)
         ." WHERE (".self::COLUMN_C_H_U_ID_COURSE." = :idc)");
      $dbst->execute(array(':idc' => (int)$idc));
      $dbst->setFetchMode(PDO::FETCH_OBJ);
      return $dbst->fetchAll();
   }

   public function isPrivateUser($idUser, $idCourse) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT COUNT(*) FROM ".Db_PDO::table(self::DB_TABLE_COURSES_HAS_USERS)
                 ." WHERE (".self::COLUMN_C_H_U_ID_USER ." = :idUser)"
              ." AND (".self::COLUMN_C_H_U_ID_COURSE ." = :idCourse)");
      $dbst->execute(array(':idUser' => $idUser, ':idCourse' => $idCourse));
      $count = $dbst->fetch();
      if($count[0] != 0){
         return true;
      }
      return false;
   }

   /**
    * Metoda vrací kurzu podle zadaného klíče
    *
    * @param string -- url klíč kurzu
    * @return PDOStatement -- pole s kurzem
    */
   public function getCourse($urlKey) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)
         ." WHERE (".self::COLUMN_URLKEY." = :urlkey)".
          " LIMIT 0, 1");
      $dbst->execute(array(':urlkey' => $urlKey));
      $dbst->setFetchMode(PDO::FETCH_OBJ);
      return $dbst->fetch();
   }

   /**
    * Metoda vrací kurzy
    *
    * @return PDOStatement -- pole s kurzy
    */
   public function getCourses($fromRow = 0, $numRows = 500) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)
              ." WHERE ".self::COLUMN_DELETED." = 0"
                      ." ORDER BY ".self::COLUMN_DATE_START." ASC"
          ." LIMIT :fromRow, :numRows");
      $dbst->bindValue(':fromRow', $fromRow, PDO::PARAM_INT);
      $dbst->bindValue(':numRows', $numRows, PDO::PARAM_INT);
      $dbst->execute();
      $dbst->setFetchMode(PDO::FETCH_OBJ);
      return $dbst->fetchAll();
   }

   /**
    * Metoda vrací kurzy pro rss feed
    *
    * @return PDOStatement -- pole s kurzy
    */
   public function getCoursesForFeed($numRows = 20) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT tc.*, tu.".Model_Users::COLUMN_USERNAME." FROM ".Db_PDO::table(self::DB_TABLE)." AS tc"
              ." JOIN ".Db_PDO::table(Model_Users::DB_TABLE)." AS tu ON tu.".Model_Users::COLUMN_ID." = tc.".self::COLUMN_ID_USER
              ." WHERE tc.".self::COLUMN_DELETED." = 0 AND tc.".self::COLUMN_FEED." = 1"
              ." ORDER BY tc.".self::COLUMN_DATE_START." ASC"
              ." LIMIT 0, :numRows");
      $dbst->bindValue(':numRows', (int)$numRows, PDO::PARAM_INT);
      $dbst->execute();
      $dbst->setFetchMode(PDO::FETCH_OBJ);
      return $dbst->fetchAll();
   }

   /**
    * Metoda vrací kurzy od zadaného data
    *
    * @return PDOStatement -- pole s kurzy
    */
   public function getCoursesFromDate(DateTime $date, $fromRow = 0, $numRows = 500) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)
              ." WHERE ".self::COLUMN_DELETED." = 0 AND ".self::COLUMN_DATE_START." >= :dates"
              ." ORDER BY ".self::COLUMN_DATE_START." ASC"
          ." LIMIT :fromRow, :numRows");
      $dbst->bindValue(':dates', $date->format('Y-m-d'), PDO::PARAM_STR);
      $dbst->bindValue(':fromRow', (int)$fromRow, PDO::PARAM_INT);
      $dbst->bindValue(':numRows', (int)$numRows, PDO::PARAM_INT);
      $dbst->execute();
      $dbst->setFetchMode(PDO::FETCH_OBJ);
      return $dbst->fetchAll();
   }

   /**
    * Metoda vrací kurz podle zadaného ID
    *
    * @param int -- id kurzu
    * @return PDOStatement -- pole s kurzem
    */
   public function getCourseById($id) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT * FROM " . Db_PDO::table(self::DB_TABLE)
                      . " WHERE (" . self::COLUMN_ID . " = :id)"
                      . " LIMIT 0, 1");
      $dbst->execute(array(':id' => (int) $id));
      $dbst->setFetchMode(PDO::FETCH_OBJ);
      return $dbst->fetch();
   }

   /**
    * Metoda smaže zadaný kurz
    * @param integer $id
    * @return bool
    */
   public function deleteCourse($id) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("UPDATE ".Db_PDO::table(self::DB_TABLE)
                 ." SET ".self::COLUMN_DELETED." = 1 WHERE ".self::COLUMN_ID." = :idc");
      return $dbst->execute(array(':idc' => $id));
   }

   public function getLecturers($idc) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare('SELECT * FROM '.Db_PDO::table(self::DB_TABLE_LECTURERS_HAS_COURSES)." AS l_has_c"
              ." JOIN ".Db_PDO::table(Lecturers_Model::DB_TABLE)." AS tb_l ON tb_l.".Lecturers_Model::COLUMN_ID." = l_has_c.".self::COLUMN_L_H_C_ID_LECTURER
              ." WHERE tb_l.".Lecturers_Model::COLUMN_DELETED." = 0 AND l_has_c.".self::COLUMN_L_H_C_ID_COURSE." = :idc"
              ." ORDER BY ".Lecturers_Model::COLUMN_SURNAME
              );

      $dbst->execute(array(':idc' => $idc));
      $dbst->setFetchMode(PDO::FETCH_OBJ);
      return $dbst->fetchAll();
   }

   /**
    * Metoda vrací poslední změnu kurzů
    * @return DateTime -- timestamp
    */
   public function getLastChange() {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT ".self::COLUMN_TIME_EDIT." AS et"
              ." FROM ".Db_PDO::table(self::DB_TABLE)." AS tbcourse"
              ." ORDER BY ".self::COLUMN_TIME_EDIT." DESC"
              ." LIMIT 0, 1");
      $dbst->execute();

      $fetch = $dbst->fetchObject();
      if($fetch != false) {
         return new DateTime((string)$fetch->et);
      }
      return false;
   }

   /**
    * Metoda vyhledává články -- je tu kvůli zbytečnému nenačítání modelu List
    * @param integer $idCat
    * @param string $string
    * @param bool $publicOnly
    * @return PDOStatement
    */
   public function search($idCat, $string, $publicOnly = true){
      $dbc = new Db_PDO();
      $clabel = Articles_Model_Detail::COLUMN_NAME.'_'.Locales::getLang();
      $ctext = Articles_Model_Detail::COLUMN_TEXT_CLEAR.'_'.Locales::getLang();

      $wherePub = null;
      if($publicOnly){
         $wherePub = ' AND '.Articles_Model_Detail::COLUMN_PUBLIC.' = 1';
      }

      $dbst = $dbc->prepare('SELECT *, ('.round(VVE_SEARCH_ARTICLE_REL_MULTIPLIER).' * MATCH('.$clabel.') AGAINST (:sstring)'
              .' + MATCH('.$ctext.') AGAINST (:sstring)) as '.Search::COLUMN_RELEVATION
              .' FROM '.Db_PDO::table(Articles_Model_Detail::DB_TABLE)
              .' WHERE MATCH('.$clabel.', '.$ctext.') AGAINST (:sstring IN BOOLEAN MODE)'
              .' AND `'.Articles_Model_Detail::COLUMN_ID_CATEGORY.'` = :idCat'
              .$wherePub // Public articles
              .' ORDER BY '.round(VVE_SEARCH_ARTICLE_REL_MULTIPLIER)
              .' * MATCH('.$clabel.') AGAINST (:sstring) + MATCH('.$ctext.') AGAINST (:sstring) DESC');

      $dbst->bindValue(':idCat', $idCat, PDO::PARAM_INT);
      $dbst->bindValue(':sstring', $string, PDO::PARAM_STR);
      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
      $dbst->execute();
      return $dbst;
   }
}

?>