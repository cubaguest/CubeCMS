<?php
/*
 * Třída modelu detailem článku
 */
class Articles_Model_Tags extends Model_ORM {
   const DB_TABLE = 'articles_tags';

   const COLUMN_ID = 'id_article_tag';
   const COLUMN_NAME = 'article_tag_name';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_art_tags');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'int', 'nn' => true, 'ai' => true, 'pk' => true, 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(30)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      
      $this->addRelatioOneToMany(self::COLUMN_ID, 'Articles_Model_TagsConnection', Articles_Model_TagsConnection::COLUMN_ID_TAG);
   }
}

?>