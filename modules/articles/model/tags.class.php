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
      $this->setPk(self::COLUMN_ID);
      
      $this->addColumn(self::COLUMN_ID, array('datatype' => 'int', 'nn' => true, 'ai' => true, 'pk' => true, 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(30)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      
      $this->addRelatioOneToMany(self::COLUMN_ID, 'Articles_Model_TagsConnection', Articles_Model_TagsConnection::COLUMN_ID_TAG);
   }
   
   /**
    * 
    * @param type $idCategories
    * @return stdClass[]
    */
   public static function getTagsByCategory($idCategories = array(), $forceLoad = false)
   {
      if(!is_array($idCategories)){
         $idCategories[] = $idCategories;
      }
      
      $cache = new Cache();
      $cacheKey = implode(';', $idCategories).'_arttags';
      if( ($tags = $cache->get($cacheKey)) != false && $forceLoad == false){
         return $tags;
      }
      
      $model = new self();
      $tags = $model
          ->columns(array('tag' => self::COLUMN_NAME, 'count' => 'COUNT('.self::COLUMN_ID.')'))
          ->join(self::COLUMN_ID, array( 't_tg' => 'Articles_Model_TagsConnection'), Articles_Model_TagsConnection::COLUMN_ID_TAG, false)
          ->join(array('t_tg' => Articles_Model_TagsConnection::COLUMN_ID_ARTICLE), 
              array( 't_a' => 'Articles_Model'), Articles_Model::COLUMN_ID, false)
          ->join(array('t_a' => Articles_Model::COLUMN_ID_CATEGORY), 
              array( 't_c' => 'Model_Category'), Model_Category::COLUMN_ID, false)
          // omezení na kategorie
          ->where(Model_Category::COLUMN_CAT_ID.' IN ('.$model->getWhereINPlaceholders($idCategories).') AND '.Articles_Model::COLUMN_CONCEPT.' = 0 ', 
              array_merge($model->getWhereINValues($idCategories)))
          ->groupBy(self::COLUMN_ID)
          ->order(array('`count`' => Model_ORM::ORDER_DESC))
          ->records(PDO::FETCH_OBJ);
      
      $cache->set($cacheKey, $tags);
      
      return $tags;
      
   }
   
}
