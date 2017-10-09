<?php
/*
 * Třída modelu detailem článku
 */
class Courses_Model_Registrations extends Model_ORM {
   const DB_TABLE = 'courses_registrations';

/**
 * Názvy sloupců v databázi
 */
   const COLUMN_ID = 'id_registration';
   const COLUMN_ID_COURSE = 'id_course';
   const COLUMN_NAME = 'name';
   const COLUMN_SURNAME = 'surname';
   const COLUMN_DEGREE = 'degree';
   const COLUMN_GRADE = 'grade';
   const COLUMN_PRACTICE_LENGHT = 'practice_lenght';
   const COLUMN_PHONE = 'phone';
   const COLUMN_MAIL = 'email';
   const COLUMN_NOTE = 'note';
   const COLUMN_PAY_TYPE = 'pay_type';
   const COLUMN_ORG_NAME = 'org_name';
   const COLUMN_ORG_ADDR = 'org_address';
   const COLUMN_ORG_ICO = 'org_ico';
   const COLUMN_ORG_PHONE = 'org_phone';
   const COLUMN_PRIVATE_ADDR = 'private_address';
   const COLUMN_TIME_ADD = 'time_add';
   const COLUMN_IP = 'ip_address';
   const COLUMN_CANCELED = 'canceled';

   protected function _initTable()
   {
      $this->setTableName(self::DB_TABLE, 't_course_regs');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'int', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_COURSE, array('datatype' => 'int', 'nn' => true, 'index' => true));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(50)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_SURNAME, array('datatype' => 'varchar(50)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_DEGREE, array('datatype' => 'varchar(15)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_GRADE, array('datatype' => 'varchar(200)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_PRACTICE_LENGHT, array('datatype' => 'varchar(10)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_PHONE, array('datatype' => 'varchar(16)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_MAIL, array('datatype' => 'varchar(50)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_NOTE, array('datatype' => 'varchar(200)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_PAY_TYPE, array('datatype' => 'varchar(15)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_ORG_NAME, array('datatype' => 'varchar(30)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_ORG_ADDR, array('datatype' => 'varchar(300)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_ORG_ICO, array('datatype' => 'varchar(15)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_ORG_PHONE, array('datatype' => 'varchar(15)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_PRIVATE_ADDR, array('datatype' => 'varchar(300)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TIME_ADD, array('datatype' => 'timestamp', 'pdoparam' => PDO::PARAM_STR, 'default' => 'CURRENT_TIMESTAMP'));
      $this->addColumn(self::COLUMN_IP, array('datatype' => 'varchar(15)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_CANCELED, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => false));
      

      $this->setPk(self::COLUMN_ID);
      $this->addForeignKey(self::COLUMN_ID_COURSE, Courses_Model::class);
   }
   
   
   /**
    * Vrací počet registrací na daný kurz
    * @param int $idc
    */
   public function getCountRegistrations($idc) {
      $dbc = Db_PDO::getInstance();
      // kontrola jestli místo již neexistuje
      $dbst = $dbc->prepare("SELECT COUNT(*) AS count FROM ".Db_PDO::table(self::DB_TABLE)
              ." WHERE ".self::COLUMN_ID_COURSE." = :idc AND ".self::COLUMN_CANCELED." = 0");
      $dbst->execute(array(':idc' => $idc));
      $counter = $dbst->fetchObject();
      return $counter->count;
   }

   public function saveRegistration($idCourse, $name, $surname, $degree, $grade, $practiceLength,
           $phone, $mail, $note, $payType, $orgName, $orgAddr, $orgICO, $orgPhone, $privateAdsr) {

      $dbc = Db_PDO::getInstance();

      $dbst = $dbc->prepare("INSERT INTO ".Db_PDO::table(self::DB_TABLE)." "
                 ."(".self::COLUMN_ID_COURSE.",".self::COLUMN_NAME.",". self::COLUMN_SURNAME.","
                 .self::COLUMN_DEGREE.",".self::COLUMN_GRADE.","
                 .self::COLUMN_PRACTICE_LENGHT.",". self::COLUMN_PHONE.","
                 .self::COLUMN_MAIL.",". self::COLUMN_NOTE.","
                 .self::COLUMN_PAY_TYPE.",". self::COLUMN_ORG_NAME.","
                 .self::COLUMN_ORG_ADDR.",".self::COLUMN_ORG_ICO.","
                 .self::COLUMN_ORG_PHONE.",". self::COLUMN_PRIVATE_ADDR.","
                 .self::COLUMN_IP.")"
                 ." VALUES (:idc, :name, :surname, :degree, :grade, :pracLen,"
                 ." :phone, :mail, :note, :payT, :orgName, :orgAddr, :orgICO, :orgPhone,"
                 ." :privAddr, :ipAddr"
                 .")");

      $dbst->bindValue(':idc', $idCourse, PDO::PARAM_INT);
      $dbst->bindValue(':name', $name, PDO::PARAM_STR);
      $dbst->bindValue(':surname', $surname, PDO::PARAM_STR);
      $dbst->bindValue(':degree', $degree, PDO::PARAM_STR);
      $dbst->bindValue(':grade', $grade, PDO::PARAM_STR);
      $dbst->bindValue(':pracLen', $practiceLength, PDO::PARAM_STR);
      $dbst->bindValue(':phone', $phone, PDO::PARAM_STR);
      $dbst->bindValue(':mail', $mail, PDO::PARAM_STR|PDO::PARAM_NULL);
      $dbst->bindValue(':note', $note, PDO::PARAM_STR|PDO::PARAM_NULL);
      $dbst->bindValue(':payT', $payType, PDO::PARAM_STR);
      $dbst->bindValue(':orgName', $orgName, PDO::PARAM_STR|PDO::PARAM_NULL);
      $dbst->bindValue(':orgAddr', $orgAddr, PDO::PARAM_STR|PDO::PARAM_NULL);
      $dbst->bindValue(':orgICO', $orgICO, PDO::PARAM_STR|PDO::PARAM_NULL);
      $dbst->bindValue(':orgPhone', $orgPhone, PDO::PARAM_STR|PDO::PARAM_NULL);
      $dbst->bindValue(':privAddr', $privateAdsr, PDO::PARAM_STR|PDO::PARAM_NULL);
      $dbst->bindValue(':ipAddr', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);

      return $dbst->execute();
   }

   public function getRegistrations($idC) {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare('SELECT * FROM '.Db_PDO::table(self::DB_TABLE)
              .' WHERE '.self::COLUMN_ID_COURSE." = :idc AND ".self::COLUMN_CANCELED." = 0");
      $dbst->execute(array(':idc' => $idC));
      return $dbst->fetchAll(PDO::FETCH_OBJ);
   }

   public function getRegistration($id) {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare('SELECT * FROM '.Db_PDO::table(self::DB_TABLE)
              .' WHERE '.self::COLUMN_ID." = :idr");
      $dbst->execute(array(':idr' => $id));
      return $dbst->fetch(PDO::FETCH_OBJ);
   }

   public function cancelRegistration($id) {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare("UPDATE ".Db_PDO::table(self::DB_TABLE)
                 ." SET ".self::COLUMN_CANCELED." = 1 WHERE ".self::COLUMN_ID." = :idr");
      return $dbst->execute(array(':idr' => $id));
   }
}
