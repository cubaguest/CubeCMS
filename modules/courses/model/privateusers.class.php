<?php
/*
 * Třída modelu detailem článku
 */
class Courses_Model_PrivateUsers extends Model_ORM {
   const DB_TABLE = 'courses_has_users';

   const COLUMN_C_H_U_ID_COURSE = 'id_course';
   const COLUMN_C_H_U_ID_USER = 'id_user';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_course_h_pusers');

      $this->addColumn(self::COLUMN_C_H_U_ID_COURSE, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_C_H_U_ID_USER, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));

      $this->addForeignKey(self::COLUMN_C_H_U_ID_COURSE, 'Courses_Model_Courses', Courses_Model_Courses::COLUMN_ID);
      $this->addForeignKey(self::COLUMN_C_H_U_ID_USER, 'Model_Users', Model_Users::COLUMN_ID);
   }
}