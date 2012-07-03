<?php
/*
 * Třída modelu detailem článku
 */
class Articles_Model_TagsConnection extends Model_ORM {
   const DB_TABLE = 'articles_tags_has_articles';

   const COLUMN_ID_ARTICLE = 'articles_id_article';
   const COLUMN_ID_TAG = 'articles_tags_id_article_tag';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_art_tags_conn');

      $this->addColumn(self::COLUMN_ID_ARTICLE, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_ID_TAG, array('datatype' => 'int', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));

      $this->addForeignKey(self::COLUMN_ID_ARTICLE, 'Articles_Model', Articles_Model::COLUMN_ID);
      $this->addForeignKey(self::COLUMN_ID_TAG, 'Articles_Model_Tags', Articles_Model_Tags::COLUMN_ID);
   }
}

?>