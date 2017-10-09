<?php
/*
 * Třída modelu detailem článku
 */
class Courses_Model_Lecturers extends Model_ORM {
   const DB_TABLE = 'courses_lecturers';

   const COLUMN_L_H_C_ID_COURSE = 'id_course';
   const COLUMN_L_H_C_ID_LECTURER = 'id_lecturer';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_crs_lecturers');

      $this->addColumn(self::COLUMN_L_H_C_ID_COURSE, array('datatype' => 'int', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_L_H_C_ID_LECTURER, array('datatype' => 'int', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));

      $this->addForeignKey(self::COLUMN_L_H_C_ID_COURSE, Courses_Model_Courses::class, Courses_Model_Courses::COLUMN_ID);
      $this->addForeignKey(self::COLUMN_L_H_C_ID_LECTURER, People_Model::class, People_Model::COLUMN_ID);
   }
}