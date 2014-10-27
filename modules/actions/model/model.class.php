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
   const COLUMN_NAME = 'action_name';
   const COLUMN_SUBANME = 'action_subname';
   const COLUMN_AUTHOR = 'action_author';
   const COLUMN_NOTE = 'action_note';
   const COLUMN_TEXT = 'action_text';
   const COLUMN_TEXT_CLEAR = 'action_text_clear';
   const COLUMN_URLKEY = 'action_urlkey';
   const COLUMN_PUBLIC = 'action_public';
   const COLUMN_DATE_START = 'action_start_date';
   const COLUMN_DATE_STOP = 'action_stop_date';
   const COLUMN_TIME = 'action_time';
   const COLUMN_IMAGE = 'action_image';
   const COLUMN_CHANGED = 'action_changed';
   const COLUMN_ADDED = 'action_time_add';
   const COLUMN_PLACE = 'action_place';
   const COLUMN_PRICE = 'action_price';
   const COLUMN_PREPRICE = 'action_preprice';
   const COLUMN_FORM = 'action_id_form';
   const COLUMN_FORM_SHOW_TO = 'action_form_show_to_date';

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
      
      $this->addForeignKey(self::COLUMN_ID_CAT, 'Model_Category', Model_Category::COLUMN_CAT_ID);
      $this->addForeignKey(self::COLUMN_ID_USER, 'Model_Users', Model_Users::COLUMN_ID);
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
      // AND ( (".Locales::getLang().")".self::COLUMN_URLKEY." IS NOT NULL)
      return $this->where(self::COLUMN_ID_CAT." = :idc AND (".self::COLUMN_PUBLIC." = 1) 
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
            OR (".self::COLUMN_DATE_START." >= CURDATE() AND ".self::COLUMN_TIME." IS NULL )
         )", 
         array("idc" => $idc));
   }
   
   public static function getActions($idc, DateTime $from, DateTime $to, $restrictUser = true)
   {
      if(!is_array($idc)){
         $idc = array($idc);
      }
      $m = new self();

      $m->joinFK(Actions_Model::COLUMN_ID_CAT, array('curlkey' => Model_Category::COLUMN_URLKEY));

      $m->where( ( !empty($idc) ? self::COLUMN_ID_CAT." IN(".$m->getWhereINPlaceholders($idc).") AND " : null )
         ." (".self::COLUMN_PUBLIC." = 1) "
         . " AND ( (".Locales::getLang().")".self::COLUMN_URLKEY." IS NOT NULL)"
         ." AND ( "
               ." (".self::COLUMN_DATE_START." >= :dateFrom AND ".self::COLUMN_DATE_START." <= :dateTo )" // kace začínají v rozsahu
               ." OR (".self::COLUMN_DATE_START." < :dateFrom AND ".self::COLUMN_DATE_STOP." IS NOT NULL AND ".self::COLUMN_DATE_STOP." > :dateTo )" // akce které právě probíhají
               ." OR (".self::COLUMN_DATE_STOP." >= :dateFrom AND ".self::COLUMN_DATE_STOP." <= :dateTo )" // akce které končí v rozsahu
               ." )"
         ,  array_merge(array(
              'dateFrom' => $from->format(DATE_ISO8601),
              'dateTo' => $to->format(DATE_ISO8601),
            ), !empty($idc) ? $m->getWhereINValues($idc) : array() )
         );
      
      
      return $m
          ->order(array(self::COLUMN_DATE_START => Model_ORM::ORDER_ASC, self::COLUMN_TIME => Model_ORM::ORDER_ASC))
          ->records();
   }
   
   public static function getActionsByLimit($idc, DateTime $from, $limit = 10, $restrictUser = true, $fromRow = 0)
   {
      if(!is_array($idc)){
         $idc = array($idc);
      }
      $m = new self();
      $m->joinFK(Actions_Model::COLUMN_ID_CAT, array('curlkey' => Model_Category::COLUMN_URLKEY));
      $m->where( ( !empty($idc) ? self::COLUMN_ID_CAT." IN(".$m->getWhereINPlaceholders($idc).") AND " : null )
         ." (".self::COLUMN_PUBLIC." = 1) "
         . " AND ( (".Locales::getLang().")".self::COLUMN_URLKEY." IS NOT NULL)"
         ." AND ( "
               ." (".self::COLUMN_DATE_START." >= :dateFrom)" // kace začínají v rozsahu
               ." OR (".self::COLUMN_DATE_START." < :dateFrom AND ".self::COLUMN_DATE_STOP." IS NOT NULL AND ".self::COLUMN_DATE_STOP." > :dateFrom )" // akce které právě probíhají
               ." )"
         ,  array_merge(array(
              'dateFrom' => $from->format(DATE_ISO8601),
            ), !empty($idc) ? $m->getWhereINValues($idc) : array() )
         );
      
      
      return $m
          ->order(array(self::COLUMN_DATE_START => Model_ORM::ORDER_ASC, self::COLUMN_TIME => Model_ORM::ORDER_ASC))
          ->limit($fromRow, $limit)
          ->records();
   }
}
