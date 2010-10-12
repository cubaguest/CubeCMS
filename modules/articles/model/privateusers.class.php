<?php
/*
 * Třída modelu detailem článku
 */
class Articles_Model_PrivateUsers extends Model_ORM {
   const DB_TABLE = 'articles_has_private_users';

   const COLUMN_A_H_U_ID_ARTICLE = 'id_article';
   const COLUMN_A_H_U_ID_USER = 'id_user';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_art_h_pusers');

      $this->addColumn(self::COLUMN_A_H_U_ID_ARTICLE, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_A_H_U_ID_USER, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));

      $this->addForeignKey('t_art', self::COLUMN_A_H_U_ID_ARTICLE, 'Articles_Model_Detail');
      $this->addForeignKey('t_usr', self::COLUMN_A_H_U_ID_USER, 'Model_Users');
   }
}

?>