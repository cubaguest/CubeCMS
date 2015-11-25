<?php
/*
 * Třída modelu s detailem uživatele
 */
class AdvEventsBase_Model_Events extends Model_ORM {
   const DB_TABLE = 'adv_events';
	/**
	 * Pole s názvy sloupců v tabulce
	 * @var array
	 */
	const COLUMN_ID 	   = 'id_event';
	const COLUMN_ID_PARENT 	   = 'id_parent_event';
	const COLUMN_ID_PLACE		= 'id_place';
	const COLUMN_PLACE_ALT     = 'event_place_alt';
	const COLUMN_ID_CATEGORY	= 'id_ev_category';
	const COLUMN_ID_ORGANIZER	= 'id_ev_organizer';
	const COLUMN_NAME		      = 'event_name';
	const COLUMN_SUBNAME		   = 'event_subname';
	const COLUMN_URLKEY	      = 'event_urlkey';
	const COLUMN_TEXT		      = 'event_text';
	const COLUMN_TEXT_CLEAR		= 'event_text_clear';
	const COLUMN_NOTICE		   = 'event_notice';
	const COLUMN_PEREX		   = 'event_perex';
	const COLUMN_WEBSITE		   = 'event_website';
	const COLUMN_TIME_EDIT	   = 'event_time_edit';
	const COLUMN_ACTIVE	      = 'event_active';
	const COLUMN_RECOMMENDED	= 'event_recommended';
	const COLUMN_IMAGE	      = 'event_image';
	const COLUMN_MAP_URL	      = 'event_map_url';
	const COLUMN_URL_MAP	      = self::COLUMN_MAP_URL;
	const COLUMN_URL_FACEBOOK	= 'event_url_facebook';
	const COLUMN_URL_YOUTUBE	= 'event_url_youtube';
	const COLUMN_KEY           = 'event_sessionkey';
	const COLUMN_APPROVED      = 'event_approved';
	const COLUMN_SHOW_ON_HP    = 'event_show_hp';
	const COLUMN_SOURCE        = 'event_source';
	const COLUMN_SOURCE_ID     = 'event_source_id';
   
//   const COLUMN_CHEERING_ONLY	= 'event_cheering_only';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_event');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'int', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_PARENT, array('datatype' => 'int', 'index' => true, 'nn' => true, 'default' => 0));
      $this->addColumn(self::COLUMN_ID_CATEGORY, array('datatype' => 'int', 'index' => true, 'nn' => true, 'default' => 0));
      $this->addColumn(self::COLUMN_ID_PLACE, array('datatype' => 'int', 'index' => true, 'nn' => true));
      $this->addColumn(self::COLUMN_ID_ORGANIZER, array('datatype' => 'int', 'index' => true, 'nn' => true));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(100)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true, 'fulltextRel' => VVE_SEARCH_ARTICLE_REL_MULTIPLIER));
      $this->addColumn(self::COLUMN_SUBNAME, array('datatype' => 'varchar(100)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true, 'fulltextRel' => VVE_SEARCH_ARTICLE_REL_MULTIPLIER));
      $this->addColumn(self::COLUMN_URLKEY, array('datatype' => 'varchar(100)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR, 'index' => true));
      $this->addColumn(self::COLUMN_TEXT, array('datatype' => 'varchar(500)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TEXT_CLEAR, array('datatype' => 'varchar(500)', 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true));
      $this->addColumn(self::COLUMN_PEREX, array('datatype' => 'varchar(200)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_WEBSITE, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_ACTIVE, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => 1));
      $this->addColumn(self::COLUMN_RECOMMENDED, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => 0));
      $this->addColumn(self::COLUMN_SHOW_ON_HP, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => 0));
      $this->addColumn(self::COLUMN_APPROVED, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => 1));
      $this->addColumn(self::COLUMN_PLACE_ALT, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR));
      
      $this->addColumn(self::COLUMN_URL_FACEBOOK, array('datatype' => 'varchar(200)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_URL_YOUTUBE, array('datatype' => 'varchar(200)', 'pdoparam' => PDO::PARAM_STR));

      $this->addColumn(self::COLUMN_TIME_EDIT, array('datatype' => 'timestamp', 'pdoparam' => PDO::PARAM_STR, 'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
      
      $this->addColumn(self::COLUMN_SOURCE, array('datatype' => 'varchar(50)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_SOURCE_ID, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR));

      $this->addForeignKey(self::COLUMN_ID_PLACE, 'AdvEventsBase_Model_Places', AdvEventsBase_Model_Places::COLUMN_ID);
      $this->addForeignKey(self::COLUMN_ID_CATEGORY, 'AdvEventsBase_Model_Categories', AdvEventsBase_Model_Categories::COLUMN_ID);
      $this->addForeignKey(self::COLUMN_ID_ORGANIZER, 'AdvEventsBase_Model_Organizers', AdvEventsBase_Model_Organizers::COLUMN_ID);

      // relace na časy a obrázky
      $this->addRelatioOneToMany(self::COLUMN_ID, 'AdvEventsBase_Model_EventsImages', AdvEventsBase_Model_EventsImages::COLUMN_ID_EVENT);
      $this->addRelatioOneToMany(self::COLUMN_ID, 'AdvEventsBase_Model_EventsTimes', AdvEventsBase_Model_EventsTimes::COLUMN_ID_EVENT);
      
      
      $this->setPk(self::COLUMN_ID);
      
      // má místo, nepotřebuje mapu
      $this->addColumn(self::COLUMN_MAP_URL, array('datatype' => 'varchar(200)', 'pdoparam' => PDO::PARAM_STR));
      // obrázky jsou v jiné tabulce
      $this->addColumn(self::COLUMN_IMAGE, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR));
   }

   protected static function getModelEventsByDateRange(DateTime $begin, DateTime $end, $params = array())
   {
      $params += array(
          'activeOnly' => true,
          'offset' => 0,
          'limit' => 1000,
          'idPlace' => 0,
          'idLocation' => 0,
          'idOrganizer' => 0,
          'idCategory' => 0,
          'ignoreEvents' =>array(),
          'recommendedOnly' => false,
          'fulltext' => false,
          'debug' => false,
          'allEvents' => false,
          'notApprovedOnly' => false,
      );
      
      $model = new self();
      $model
//          ->columns(array('*',
//              'times' => '    concat(\'[\',GROUP_CONCAT(\'{ "event_date_begin" : "\', ttimes2.event_date_begin,\'"\',
//                IF(ttimes2.event_date_end IS NULL, \', "event_date_end" : null\', concat(\', "event_date_end" : "\',ttimes2.event_date_end,\'"\')) ,
//                IF(ttimes2.event_time_begin IS NULL, \', "event_time_begin" : ""\', concat(\', "event_time_begin" : "\',ttimes2.event_time_begin,\'"\')),
//                IF(ttimes2.event_time_end IS NULL, \', "event_time_end" : ""\', concat(\', "event_time_end" : "\',ttimes2.event_time_end,\'"\')),
//                IF(ttimes2.event_time_text IS NULL, \', "event_time_text" : ""\', concat(\', "event_time_text" : "\',ttimes2.event_time_text,\'"\'))
//				, \'}\'
//               ORDER BY ttimes2.event_date_begin , ttimes2.event_time_begin
//               SEPARATOR \',\' ),\']\')'
//              ))
          
          ->columns(array('*',
              // připojení všech časů dané události
              'times' => '( SELECT 
            GROUP_CONCAT(IFNULL('.AdvEventsBase_Model_EventsTimes::COLUMN_ID.', ""),\';\', 
                IFNULL('.AdvEventsBase_Model_EventsTimes::COLUMN_DATE_BEGIN.', ""),\';\', 
                IFNULL('.AdvEventsBase_Model_EventsTimes::COLUMN_DATE_END.', ""), \';\',
                IFNULL('.AdvEventsBase_Model_EventsTimes::COLUMN_TIME_BEGIN.', ""), \';\',
                IFNULL('.AdvEventsBase_Model_EventsTimes::COLUMN_TIME_END.', ""), \';\',
                IFNULL('.AdvEventsBase_Model_EventsTimes::COLUMN_NOTE.', ""), \';\'
                ORDER BY event_date_begin, event_time_begin SEPARATOR \'|\')
			FROM web_adv_events_times AS t WHERE t.id_event = t_event.id_event  GROUP BY t.id_event )',
              'ordered' => '(ABS(DATEDIFF(ttimes1.'.AdvEventsBase_Model_EventsTimes::COLUMN_DATE_BEGIN.', :ordDate)) '
              . ' -  IF('.AdvEventsBase_Model_Organizers::COLUMN_PRIORITY.' > 0 , '.AdvEventsBase_Model_Organizers::COLUMN_PRIORITY.'/ 1000, 0)'
              . ' - IF('.AdvEventsBase_Model_EventsTimes::COLUMN_TIME_BEGIN.' IS NULL, 0, 0.001) )',
              ), array('ordDate' => $begin->format('Y-m-d')))
          /* 
           * vrací json data, ale né v dotatečné délce. 
           * viz: http://dev.mysql.com/doc/refman/5.5/en/group-by-functions.html#function_group-concat
           */
//          ->columns(array('*',
//              // připojení všech časů dané události
//              'times' => '( SELECT concat(\'[\',
//            GROUP_CONCAT(\'{ "event_date_begin" : "\', IFNULL('.AdvEventsBase_Model_EventsTimes::COLUMN_DATE_BEGIN.', ""),\'",\', 
//                \'"event_date_end":"\', IFNULL('.AdvEventsBase_Model_EventsTimes::COLUMN_DATE_END.', ""), \'",\',
//                \'"event_time_begin":"\', IFNULL('.AdvEventsBase_Model_EventsTimes::COLUMN_TIME_BEGIN.', ""), \'",\',
//                \'"event_time_end":"\', IFNULL('.AdvEventsBase_Model_EventsTimes::COLUMN_TIME_END.', ""), \'",\',
//                \'"event_time_text":"\', IFNULL('.AdvEventsBase_Model_EventsTimes::COLUMN_NOTE.', ""), \'"\',
//                \'}\'
//                ORDER BY event_date_begin , event_time_begin SEPARATOR \',\'),
//            \']\') 
//			FROM web_adv_events_times AS t WHERE t.id_event = t_event.id_event  GROUP BY t.id_event )',
//              'ordered' => '(ABS(DATEDIFF(ttimes1.'.AdvEventsBase_Model_EventsTimes::COLUMN_DATE_BEGIN.', :ordDate)) '
//              . ' -  IF('.AdvEventsBase_Model_Organizers::COLUMN_PRIORITY.' > 0 , '.AdvEventsBase_Model_Organizers::COLUMN_PRIORITY.'/ 1000, 0)'
//              . ' - IF('.AdvEventsBase_Model_EventsTimes::COLUMN_TIME_BEGIN.' IS NULL, 0, 0.001) )',
//              ), array('ordDate' => $begin->format('Y-m-d')))
          // podle této se omezuje výpis
          ->join(self::COLUMN_ID, array( 'ttimes1' => 'AdvEventsBase_Model_EventsTimes'), AdvEventsBase_Model_EventsTimes::COLUMN_ID_EVENT, array()) // není třeba sloupce, jsou v poli times
          ->joinFK(self::COLUMN_ID_PLACE)
          ->joinFK(self::COLUMN_ID_ORGANIZER)
          ->join(array('AdvEventsBase_Model_Places_2' => AdvEventsBase_Model_Places::COLUMN_ID_LOCATION), array('tloc' => 'AdvEventsBase_Model_Locations'), AdvEventsBase_Model_Locations::COLUMN_ID) // není třeba sloupce, jsou v poli times
          ->join(self::COLUMN_ID, array( 'iamges' => 'AdvEventsBase_Model_EventsImages'), AdvEventsBase_Model_EventsImages::COLUMN_ID_EVENT
              , array(
                  'title_image' => AdvEventsBase_Model_EventsImages::COLUMN_FILE,
                  'title_image_name' => AdvEventsBase_Model_EventsImages::COLUMN_NAME,
                  ), Model_ORM::JOIN_LEFT, ' AND '.AdvEventsBase_Model_EventsImages::COLUMN_IS_TITLE.' = 1') // není třeba sloupce, jsou v poli times
          ->groupBy(AdvEventsBase_Model_Events::COLUMN_ID)
          ->order(array( 
              'ordered', 
              'ttimes1.'.AdvEventsBase_Model_EventsTimes::COLUMN_DATE_BEGIN, 
              'ttimes1.'.AdvEventsBase_Model_EventsTimes::COLUMN_TIME_BEGIN,
              AdvEventsBase_Model_Events::COLUMN_NAME
              ));
      
      $whereString = '( (ttimes1.'.AdvEventsBase_Model_EventsTimes::COLUMN_DATE_BEGIN.' >= :dateFrom '
              . ' AND (ttimes1.'.AdvEventsBase_Model_EventsTimes::COLUMN_DATE_BEGIN.' <= :dateTo OR ttimes1.'.AdvEventsBase_Model_EventsTimes::COLUMN_DATE_BEGIN.'  IS NULL)) '
              . ' OR (ttimes1.'.AdvEventsBase_Model_EventsTimes::COLUMN_DATE_BEGIN.'  BETWEEN :dateFrom AND :dateTo)'
              . ' OR (ttimes1.'.AdvEventsBase_Model_EventsTimes::COLUMN_DATE_END.'  BETWEEN :dateFrom AND :dateTo) '
              . ' OR ( ttimes1.'.AdvEventsBase_Model_EventsTimes::COLUMN_DATE_BEGIN.' <= :dateFrom '
                     . ' AND ttimes1.'.AdvEventsBase_Model_EventsTimes::COLUMN_DATE_END.'  IS NOT NULL '
                     . ' AND ttimes1.'.AdvEventsBase_Model_EventsTimes::COLUMN_DATE_END.'  >= :dateTo ) '
          . ')';
      $whereValues = array(
                  'dateFrom' => $begin->format('Y-m-d'),
                  'dateTo' => $end->format('Y-m-d'),
//                  'dateFrom2' => $begin,
//                  'dateTo2' => $end,
                  );
      
      if(!empty($params['ignoreEvents'])){
         $whereString .= ' AND '.self::COLUMN_ID.' NOT IN('.self::getWhereINPlaceholders($params['ignoreEvents']).')';
         $whereValues = array_merge($whereValues, self::getWhereINValues($params['ignoreEvents']));
      }
      
      
      if($params['activeOnly']){
         $whereString .= ' AND '.self::COLUMN_ACTIVE.' = 1';
      }
      if($params['notApprovedOnly']){
         $whereString .= ' AND '.self::COLUMN_APPROVED.' = 0';
      }
      if($params['allEvents'] == false){
         $whereString .= ' AND '.self::COLUMN_APPROVED.' = 1';
      }
      if($params['recommendedOnly']){
         $whereString .= ' AND '.self::COLUMN_RECOMMENDED.' = 1';
      }
      
      if($params['idPlace'] != 0){
         $whereString .= ' AND '.self::COLUMN_ID_PLACE.' = :idPlace';
         $whereValues['idPlace'] = $params['idPlace'];
      }
      if($params['idLocation'] != 0){
         $whereString .= ' AND AdvEventsBase_Model_Places_2.'.AdvEventsBase_Model_Places::COLUMN_ID_LOCATION.' = :idLocation';
         $whereValues['idLocation'] = $params['idLocation'];
      }
      if($params['idOrganizer'] != 0){
         $whereString .= ' AND '.  AdvEventsBase_Model_Organizers::COLUMN_ID.' = :idOrg';
         $whereValues['idOrg'] = $params['idOrganizer'];
      }
      if($params['idCategory'] != 0){
         $whereString .= ' AND '. AdvEventsBase_Model_Events::COLUMN_ID_CATEGORY.' = :idCat';
         $whereValues['idCat'] = $params['idCategory'];
      }
      if($params['fulltext']){
         $whereString .= ' AND ( '. AdvEventsBase_Model_Events::COLUMN_NAME.' LIKE :name '
             . ' OR '. AdvEventsBase_Model_Events::COLUMN_SUBNAME.' LIKE :name '
             . ' OR '. AdvEventsBase_Model_Events::COLUMN_TEXT_CLEAR.' LIKE :name )';
         $whereValues['name'] =  '%'.$params['fulltext'].'%';
      }
      
      
      
      $model->where($whereString, $whereValues);
      if($params['debug']){
         Debug::log($model->getSQLQuery());
      }
      return $model;
   }

   /**
    * 
    * @param type $params
    * @return Model_ORM_Record[]
    */
   public static function getHomePageEvents($params = array())
   {
      $now = new DateTime();
      $end = new DateTime();
      $end->modify('+1 year');
      
      
      $model = self::getModelEventsByDateRange($now, $end, $params);
      $model->where(' AND '.self::COLUMN_SHOW_ON_HP.' = 1', array(), true);
      
      return $model->records();
   }

   /**
    * 
    * @param DateTime $begin
    * @param DateTime $end
    * @param array $params
    * @return Model_ORM_Record[]
    */
   public static function getEventsByDateRange(DateTime $begin, DateTime $end, $params = array())
   {
      $params+=array(
          'offset' => 0, 
          'limit' => 1000
          );
      $model = self::getModelEventsByDateRange($begin, $end, $params);
      $records = $model->limit($params['offset'], $params['limit'])->records();
      return $records;
   }
   
   /**
    * 
    * @param DateTime $begin
    * @param DateTime $end
    * @param type $params
    * @return int
    */
   public static function getCountEventsByDateRange(DateTime $begin, DateTime $end, $params = array())
   {
      $model = self::getModelEventsByDateRange($begin, $end, $params);
      return $model->count();
   }

   
   /**
    * 
    * @param DateTime $begin
    * @param DateTime $end
    * @param type $fulltext
    * @param type $cheeringOnly
    * @param type $sportId
    * @param type $sportsSeparator
    * @return type
    * @deprecated since version number
    */
   public static function getEventsByDateRangeAndFilter(DateTime $begin, DateTime $end,
                                                        $fulltext = null, $cheeringOnly = null, $sportId = false,
                                                        $sportsSeparator = ';')
   {
      $m = new self();

      $whereParts = array(
         '('
            .'('.self::COLUMN_DATE_END.' IS NULL AND '.self::COLUMN_DATE_BEGIN.' BETWEEN :dateBegin AND :dateEnd )'
            .' OR ('.self::COLUMN_DATE_END.' IS NOT NULL AND '.self::COLUMN_DATE_BEGIN.' <= :dateEnd2 AND '.self::COLUMN_DATE_END.' >= :dateBegin2 )'
         .')' ,
         self::COLUMN_ACTIVE." = 1",
      );
      $whereBinds = array(
         'dateBegin' => $begin->format('Y-m-d'),
         'dateEnd' => $end->format('Y-m-d'),
         'dateBegin2' => $begin->format('Y-m-d'),
         'dateEnd2' => $end->format('Y-m-d'),
      );

      if($fulltext != null){
         $whereParts[] = 'MATCH('.self::COLUMN_NAME.', '.self::COLUMN_TEXT.', '.self::COLUMN_PEREX.') AGAINST (:searchStr IN BOOLEAN MODE)';
         $whereBinds['searchStr'] = $fulltext;
      }
      if($cheeringOnly !== null){
         $whereParts[] = self::COLUMN_CHEERING_ONLY.' = '.($cheeringOnly == true ? '1' : '0');
      }

      if($sportId != 0){
         $whereParts[] = SvbBase_Model_EventHasSports::COLUMN_ID_SPORT.' = :idSport';
         $whereBinds['idSport'] = (int)$sportId;
      }

      $m->where( implode(' AND ', $whereParts), $whereBinds)
         ->order(array(self::COLUMN_DATE_BEGIN => self::ORDER_ASC, self::COLUMN_TIME_BEGIN => self::ORDER_ASC))
         ->joinFK(self::COLUMN_ID_PLACE, array(SvbBase_Model_Places::COLUMN_NAME, SvbBase_Model_Places::COLUMN_URL))
         ->join(self::COLUMN_ID, array('t_eve_has_sport' => 'SvbBase_Model_EventHasSports'), SvbBase_Model_EventHasSports::COLUMN_ID_EVENT)
         ->join(array('t_eve_has_sport' => SvbBase_Model_EventHasSports::COLUMN_ID_SPORT),
         array('t_eve_sport' => 'SvbBase_Model_Sports'), SvbBase_Model_Sports::COLUMN_ID,
         array('sports_string_merged' => "GROUP_CONCAT(".SvbBase_Model_Sports::COLUMN_NAME." SEPARATOR '".addslashes($sportsSeparator)."')"))
         ->groupBy(array(self::COLUMN_ID))
      ;
      return $m->records();
   }

   /**
    * 
    * @param DateTime $date
    * @param type $onlyActive
    * @param type $limit
    * @param type $from
    * @param type $sportsSeparator
    * @return Model_ORM_Record[]
    * @deprecated since version number
    */
   public static function getEventsByDate(DateTime $date, $onlyActive = true, $limit = 20, $from = 0, $sportsSeparator = ';')
   {
      $m = new self();
      $m->where(
         '( ( '.self::COLUMN_DATE_END.' IS NULL AND '.self::COLUMN_DATE_BEGIN." = :date1 )"
         .' OR ('.self::COLUMN_DATE_END.' IS NOT NULL AND '.self::COLUMN_DATE_BEGIN.' <= :date2 AND '.self::COLUMN_DATE_END.' >= :date3 )'. ')'
         .( $onlyActive ? ' AND '.self::COLUMN_ACTIVE." = 1" : null ),
         array('date1' => $date->format('Y-m-d'), 'date2' => $date->format('Y-m-d'), 'date3' => $date->format('Y-m-d')))
         ->order(array(self::COLUMN_DATE_BEGIN => self::ORDER_ASC, self::COLUMN_TIME_BEGIN => self::ORDER_ASC))
         ->joinFK(self::COLUMN_ID_PLACE, array(SvbBase_Model_Places::COLUMN_NAME, SvbBase_Model_Places::COLUMN_URL))
         ->join(self::COLUMN_ID, array('t_eve_has_sport' => 'SvbBase_Model_EventHasSports'), SvbBase_Model_EventHasSports::COLUMN_ID_EVENT)
         ->join(array('t_eve_has_sport' => SvbBase_Model_EventHasSports::COLUMN_ID_SPORT),
            array('t_eve_sport' => 'SvbBase_Model_Sports'), SvbBase_Model_Sports::COLUMN_ID,
            array('sports_string_merged' => "GROUP_CONCAT(".SvbBase_Model_Sports::COLUMN_NAME." SEPARATOR '".addslashes($sportsSeparator)."')"))
         ->groupBy(array(self::COLUMN_ID))
         ->limit($from, $limit)
      ;
      return $m->records();
   }
   
   /**
    * 
    * @param DateTime $date
    * @param type $onlyActive
    * @param type $limit
    * @param type $from
    * @param type $sportsSeparator
    * @return Model_ORM_Record[]
    * @deprecated since version number
    */
   public static function getLastEventsByDate(DateTime $date, $onlyActive = true, $limit = 20, $from = 0, $sportsSeparator = ';')
   {
      $m = new self();
      $m->where(
         self::COLUMN_DATE_BEGIN." >= :date1"
         .( $onlyActive ? ' AND '.self::COLUMN_ACTIVE." = 1" : null ),
         array('date1' => $date->format('Y-m-d')))
         ->order(array(self::COLUMN_DATE_BEGIN => self::ORDER_ASC, self::COLUMN_TIME_BEGIN => self::ORDER_ASC))
         ->joinFK(self::COLUMN_ID_PLACE, array(SvbBase_Model_Places::COLUMN_NAME, SvbBase_Model_Places::COLUMN_URL))
         ->join(self::COLUMN_ID, array('t_eve_has_sport' => 'SvbBase_Model_EventHasSports'), SvbBase_Model_EventHasSports::COLUMN_ID_EVENT)
         ->join(array('t_eve_has_sport' => SvbBase_Model_EventHasSports::COLUMN_ID_SPORT),
            array('t_eve_sport' => 'SvbBase_Model_Sports'), SvbBase_Model_Sports::COLUMN_ID,
            array('sports_string_merged' => "GROUP_CONCAT(".SvbBase_Model_Sports::COLUMN_NAME." SEPARATOR '".addslashes($sportsSeparator)."')"))
         ->groupBy(array(self::COLUMN_ID))
         ->limit($from, $limit)
      ;
      return $m->records();
   }

   public static function getEventsByName($name, $onlyActive = true)
   {
      $m = new self();
      $m->where(self::COLUMN_NAME.' LIKE :str '
         .( $onlyActive ? ' AND '.self::COLUMN_ACTIVE." = 1" : null ),
         array('str' => '%'.$name.'%'));
      return $m->records();
   }

   protected function beforeSave(Model_ORM_Record $record, $type = 'U')
   {
      if($record->{self::COLUMN_URLKEY} == null){
         $record->{self::COLUMN_URLKEY} = Utils_Url::toUrlKey($record->{self::COLUMN_NAME});
      }
      // pro fulltext
      $record->{self::COLUMN_TEXT_CLEAR} = Utils_Html::stripTags($record->{self::COLUMN_PEREX}.' '.$record->{self::COLUMN_TEXT});
   }

   public static function getEvent($id)
   {
      $m = new self();
      $m
         ->columns(array('*',
              // připojení všech časů dané události
              'times' => '( SELECT 
            GROUP_CONCAT(IFNULL('.AdvEventsBase_Model_EventsTimes::COLUMN_ID.', ""),\';\', 
                IFNULL('.AdvEventsBase_Model_EventsTimes::COLUMN_DATE_BEGIN.', ""),\';\', 
                IFNULL('.AdvEventsBase_Model_EventsTimes::COLUMN_DATE_END.', ""), \';\',
                IFNULL('.AdvEventsBase_Model_EventsTimes::COLUMN_TIME_BEGIN.', ""), \';\',
                IFNULL('.AdvEventsBase_Model_EventsTimes::COLUMN_TIME_END.', ""), \';\',
                IFNULL('.AdvEventsBase_Model_EventsTimes::COLUMN_NOTE.', ""), \';\'
                ORDER BY event_date_begin, event_time_begin SEPARATOR \'|\')
			FROM web_adv_events_times AS t WHERE t.id_event = t_event.id_event  GROUP BY t.id_event )',
              ))
          /* 
           * vrací json data, ale né v dotatečné délce. 
           * viz: http://dev.mysql.com/doc/refman/5.5/en/group-by-functions.html#function_group-concat
           */
//          ->columns(array('*',
//              // připojení všech časů dané události
//              'times' => '( SELECT concat(\'[\',
//            GROUP_CONCAT(\'{ "event_date_begin" : "\', IFNULL('.AdvEventsBase_Model_EventsTimes::COLUMN_DATE_BEGIN.', ""),\'",\', 
//                \'"event_date_end":"\', IFNULL('.AdvEventsBase_Model_EventsTimes::COLUMN_DATE_END.', ""), \'",\',
//                \'"event_time_begin":"\', IFNULL('.AdvEventsBase_Model_EventsTimes::COLUMN_TIME_BEGIN.', ""), \'",\',
//                \'"event_time_end":"\', IFNULL('.AdvEventsBase_Model_EventsTimes::COLUMN_TIME_END.', ""), \'",\',
//                \'"event_time_text":"\', IFNULL('.AdvEventsBase_Model_EventsTimes::COLUMN_NOTE.', ""), \'"\',
//                \'}\'
//                ORDER BY event_date_begin , event_time_begin SEPARATOR \',\'),
//            \']\') 
//			FROM web_adv_events_times AS t WHERE t.id_event = t_event.id_event  GROUP BY t.id_event )',
//              'ordered' => '(ABS(DATEDIFF(ttimes1.'.AdvEventsBase_Model_EventsTimes::COLUMN_DATE_BEGIN.', :ordDate)) '
//              . ' -  IF('.AdvEventsBase_Model_Organizers::COLUMN_PRIORITY.' > 0 , '.AdvEventsBase_Model_Organizers::COLUMN_PRIORITY.'/ 1000, 0)'
//              . ' - IF('.AdvEventsBase_Model_EventsTimes::COLUMN_TIME_BEGIN.' IS NULL, 0, 0.001) )',
//              ), array('ordDate' => $begin->format('Y-m-d')))
          // podle této se omezuje výpis
          ->join(self::COLUMN_ID, array( 'ttimes1' => 'AdvEventsBase_Model_EventsTimes'), AdvEventsBase_Model_EventsTimes::COLUMN_ID_EVENT, array()) // není třeba sloupce, jsou v poli times
          ->joinFK(self::COLUMN_ID_PLACE)
          ->joinFK(self::COLUMN_ID_ORGANIZER)
          ->join(array('AdvEventsBase_Model_Places_2' => AdvEventsBase_Model_Places::COLUMN_ID_LOCATION), array('tloc' => 'AdvEventsBase_Model_Locations'), AdvEventsBase_Model_Locations::COLUMN_ID) // není třeba sloupce, jsou v poli times
          ->join(self::COLUMN_ID, array( 'iamges' => 'AdvEventsBase_Model_EventsImages'), AdvEventsBase_Model_EventsImages::COLUMN_ID_EVENT
              , array(
                  'title_image' => AdvEventsBase_Model_EventsImages::COLUMN_FILE,
                  'title_image_name' => AdvEventsBase_Model_EventsImages::COLUMN_NAME,
                  ), Model_ORM::JOIN_LEFT, ' AND '.AdvEventsBase_Model_EventsImages::COLUMN_IS_TITLE.' = 1') // není třeba sloupce, jsou v poli times
          ->groupBy(AdvEventsBase_Model_Events::COLUMN_ID)
         ;
      return $m->record($id);
   }
   
   
   public static function getEventyBySourceID($id)
   {
      $m = new self();
      return $m->where(self::COLUMN_SOURCE_ID.' = :sourceid', array('sourceid' => $id))->record();
   }
   
   
   protected function beforeDelete($pk)
   {
      parent::beforeDelete($pk);
      
      $mImg = new AdvEventsBase_Model_EventsImages();
      $mImg->where(AdvEventsBase_Model_EventsImages::COLUMN_ID_EVENT." = :ide", array('ide' => $pk))->delete();
         
      $mTimes = new AdvEventsBase_Model_EventsImages();
      $mTimes->where(AdvEventsBase_Model_EventsImages::COLUMN_ID_EVENT." = :ide", array('ide' => $pk))->delete();
      
      $mUserAdds = new AdvEventsBase_Model_UserAdds();
      $mUserAdds->where(AdvEventsBase_Model_UserAdds::COLUMN_ID_EVENT." = :ide", array('ide' => $pk))->delete();
      
   }
}


class AdvEventsBase_Model_Events_Record extends Model_ORM_Record {
   public function getImages()
   {
      $m = new AdvEventsBase_Model_EventsImages();
      return $m
          ->where(AdvEventsBase_Model_EventsImages::COLUMN_ID_EVENT." = :ide", array('ide' => $this->getPK()))
          ->records();
   }
   
   public function getTimes()
   {
      $m = new AdvEventsBase_Model_EventsTimes();
      return $m
          ->where(AdvEventsBase_Model_EventsTimes::COLUMN_ID_EVENT." = :ide", array('ide' => $this->getPK()))
          ->order(array(AdvEventsBase_Model_EventsTimes::COLUMN_DATE_BEGIN, AdvEventsBase_Model_EventsTimes::COLUMN_TIME_BEGIN))
          ->records();
   }
   
   public function getTimesArray()
   {
//      $times = array();
//      if(isset($this->times)){
//         $ranges = explode('|', $this->times);
//         foreach ($ranges as $r) {
//            $dates = explode(';', $r);
//            $obj = AdvEventsBase_Model_EventsTimes::getNewRecord();
//            $obj->{AdvEventsBase_Model_EventsTimes::COLUMN_ID} = $dates[0];
//            $obj->{AdvEventsBase_Model_EventsTimes::COLUMN_ID_EVENT} = $this->getPK();
//            $obj->{AdvEventsBase_Model_EventsTimes::COLUMN_DATE_BEGIN} = $dates[1];
//            $obj->{AdvEventsBase_Model_EventsTimes::COLUMN_DATE_END} = $dates[2];
//            $obj->{AdvEventsBase_Model_EventsTimes::COLUMN_TIME_BEGIN} = $dates[3];
//            $obj->{AdvEventsBase_Model_EventsTimes::COLUMN_TIME_END} = $dates[4];
//            $obj->{AdvEventsBase_Model_EventsTimes::COLUMN_NOTE} = $dates[5];
//            $obj->setNew(false);
//            $times[] = $obj;
//         }
//      }
//      return $times;
      
      return $this->getTimes();
   }
   
   public function getTitleImage()
   {
      if(isset($this->title_image)){
         return $this->title_image != null ? AdvEventsBase_Controller::getEventImagesUrl($this->getPK()).$this->title_image : null;
      } else {
         // není titulní obrázek, načti z db
      }
   }
   
   public function getHomePageImage()
   {
      if($this->{AdvEventsBase_Model_Events::COLUMN_SHOW_ON_HP} == true){
         return AdvEventsBase_Controller::getEventImagesUrl($this->getPK())
             .AdvEventsBase_Controller::DIR_HOMEPAGE.'/'.AdvEventsBase_Controller::HOMEPAGE_IMAGE;
      } else {
         return null;
      }
   }
   
   public function haveHomePageImage()
   {
      return file_exists(AdvEventsBase_Controller::getEventImagesDir($this->getPK())
          .AdvEventsBase_Controller::DIR_HOMEPAGE.DIRECTORY_SEPARATOR.AdvEventsBase_Controller::HOMEPAGE_IMAGE);
   }
   
   public function getAddUser()
   {
      return AdvEventsBase_Model_UserAdds::getUserByEvent($this->getPK());
   }
   
}