<?php
/*
 * Model s událostmi
*/
class Actions_Model extends Model_ORM {
   /**
    * Tabulka s detaily
    */
   const DB_TABLE = 'actions';

   /**
    * Názvy sloupců v databázi
    * @var string
    */
   const COLUMN_ID = 'id_action';
   const COLUMN_ID_CAT = 'id_category';
   const COLUMN_ID_USER = 'id_user';
   const COLUMN_NAME = 'name';
   const COLUMN_SUBANME = 'subname';
   const COLUMN_AUTHOR = 'author';
   const COLUMN_NOTE = 'note';
   const COLUMN_TEXT = 'text';
   const COLUMN_TEXT_CLEAR = 'text_clear';
   const COLUMN_URLKEY = 'urlkey';
   const COLUMN_PUBLIC = 'public';
   const COLUMN_DATE_START = 'start_date';
   const COLUMN_DATE_STOP = 'stop_date';
   const COLUMN_TIME = 'time';
   const COLUMN_IMAGE = 'image';
   const COLUMN_CHANGED = 'changed';
   const COLUMN_ADDED = 'time_add';
   const COLUMN_PLACE = 'place';
   const COLUMN_PRICE = 'price';
   const COLUMN_PREPRICE = 'preprice';
   const COLUMN_FORM = 'id_form';
   const COLUMN_FORM_SHOW_TO = 'form_show_to_date';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_act');
      $this->setPk(self::COLUMN_ID);

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_CAT, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_ID_USER, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
      
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(200)', 'nn' => true, 'lang' => true, 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true, 'fulltextRel' => VVE_SEARCH_ARTICLE_REL_MULTIPLIER));
      $this->addColumn(self::COLUMN_SUBANME, array('datatype' => 'varchar(200)', 'lang' => true, 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true, 'fulltextRel' => VVE_SEARCH_ARTICLE_REL_MULTIPLIER));
      
      $this->addColumn(self::COLUMN_AUTHOR, array('datatype' => 'varchar(200)', 'pdoparam' => PDO::PARAM_STR ));
      $this->addColumn(self::COLUMN_NOTE, array('datatype' => 'varchar(500)', 'lang' => true, 'pdoparam' => PDO::PARAM_STR));
      
      $this->addColumn(self::COLUMN_TEXT, array('datatype' => 'text', 'lang' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TEXT_CLEAR, array('datatype' => 'text', 'lang' => true, 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true));
      
      $this->addColumn(self::COLUMN_URLKEY, array('datatype' => 'varchar(200)', 'nn' => true, 'lang' => true, 'pdoparam' => PDO::PARAM_STR));
//      $this->addColumn(self::COLUMN_KEYWORDS, array('datatype' => 'varchar(200)', 'lang' => true, 'pdoparam' => PDO::PARAM_STR));
//      $this->addColumn(self::COLUMN_DESCRIPTION, array('datatype' => 'varchar(700)', 'lang' => true, 'pdoparam' => PDO::PARAM_STR));

      $this->addColumn(self::COLUMN_PUBLIC, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => true));
      
      $this->addColumn(self::COLUMN_DATE_START, array('datatype' => 'datetime', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_DATE_STOP, array('datatype' => 'datetime', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TIME, array('datatype' => 'time', 'pdoparam' => PDO::PARAM_STR));
      
      $this->addColumn(self::COLUMN_IMAGE, array('datatype' => 'varchar(200)', 'pdoparam' => PDO::PARAM_STR ));
      
      $this->addColumn(self::COLUMN_CHANGED, array('datatype' => 'timestamp', 'pdoparam' => PDO::PARAM_STR, 'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
      $this->addColumn(self::COLUMN_ADDED, array('datatype' => 'datetime', 'pdoparam' => PDO::PARAM_STR));
      
      $this->addColumn(self::COLUMN_PLACE, array('datatype' => 'varchar(200)', 'pdoparam' => PDO::PARAM_STR ));
      $this->addColumn(self::COLUMN_PRICE, array('datatype' => 'int', 'pdoparam' => PDO::PARAM_INT, 'default' => 0));
      $this->addColumn(self::COLUMN_PREPRICE, array('datatype' => 'int', 'pdoparam' => PDO::PARAM_INT, 'default' => 0));
      
      $this->addColumn(self::COLUMN_FORM, array('datatype' => 'int', 'pdoparam' => PDO::PARAM_INT, 'default' => 0));
      $this->addColumn(self::COLUMN_FORM_SHOW_TO, array('datatype' => 'datetime', 'pdoparam' => PDO::PARAM_STR, 'default' => null));
   }
   
   public function setPastOnly($idc)
   {
      return $this->where(self::COLUMN_ID_CAT." = :idc AND (".self::COLUMN_PUBLIC." = 1) 
         AND ( (".Locales::getLang().")".self::COLUMN_URLKEY." IS NOT NULL)
         AND 
         (
            (".self::COLUMN_DATE_START." <= CURDATE() AND ".self::COLUMN_TIME." IS NOT NULL )
            OR (".self::COLUMN_DATE_START." < CURDATE() AND ".self::COLUMN_TIME." IS NULL )
         )", 
         array("idc" => $idc));
   }
   
   public function featuredOnly($idc)
   {
      return $this->where(self::COLUMN_ID_CAT." = :idc AND (".self::COLUMN_PUBLIC." = 1) 
         AND ( (".Locales::getLang().")".self::COLUMN_URLKEY." IS NOT NULL)
         AND 
         (
            (".self::COLUMN_DATE_START." >= CURDATE() AND ".self::COLUMN_TIME." IS NOT NULL )
            OR (".self::COLUMN_DATE_START." > CURDATE() AND ".self::COLUMN_TIME." IS NULL )
         )", 
         array("idc" => $idc));
   }
   public function actualOnly($idc)
   {
      return $this->where(self::COLUMN_ID_CAT." = :idc AND (".self::COLUMN_PUBLIC." = 1) 
         AND ( (".Locales::getLang().")".self::COLUMN_URLKEY." IS NOT NULL)
         AND 
         (
            (".self::COLUMN_DATE_START." >= CURDATE() AND ".self::COLUMN_TIME." IS NOT NULL )
            OR (".self::COLUMN_DATE_START." < CURDATE() AND ".self::COLUMN_DATE_STOP." IS NOT NULL AND ".self::COLUMN_DATE_STOP." >= CURDATE() )
            OR (".self::COLUMN_DATE_START." > CURDATE() AND ".self::COLUMN_TIME." IS NULL )
         )", 
         array("idc" => $idc));
   }
}

?>