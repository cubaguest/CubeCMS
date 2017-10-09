<?php
/*
 * Třída modelu detailem článku
 */
class Courses_Model_Files extends Model_ORM {
   const DB_TABLE = 'courses_files';

   const COLUMN_ID = 'id_course_file';
   const COLUMN_ID_COURSE = 'id_course';
   const COLUMN_FILENAME = 'filename';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_crs_files');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'int', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_COURSE, array('datatype' => 'int', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_FILENAME, array('datatype' => 'varchar(200)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));

      $this->addForeignKey(self::COLUMN_ID_COURSE, Courses_Model_Courses::class, Courses_Model_Courses::COLUMN_ID);
   }
}

class Courses_Model_Files_Records extends Model_ORM_Record {
   
}