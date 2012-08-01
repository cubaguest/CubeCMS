<?php
/*
 * Třída modelu odpovědí ve fóru
 * 
*/
class Forum_Model_Messages extends Model_ORM {
   /**
    * Tabulka s detaily
    */
   const DB_TABLE = 'forum_messages';

   /**
    * Názvy sloupců v db
    * @var string
    */
   const COLUMN_ID = 'id_message';
   const COLUMN_ID_TOPIC = 'id_topic';
   const COLUMN_ID_USER = 'message_id_user';
   const COLUMN_ID_PARENT_MESSAGE = 'id_parent_message';
   const COLUMN_EMAIL = 'message_email';
   const COLUMN_CREATED_BY = 'message_created_by';
   const COLUMN_CREATED_BY_MODERATOR = 'message_created_by_moderator';
   const COLUMN_WWW = 'message_www';
   const COLUMN_NAME = 'message_name';
   const COLUMN_TEXT = 'message_text';
   const COLUMN_TEXT_CLEAR = 'message_text_clear';
   const COLUMN_IP = 'message_ip_address';
   const COLUMN_CENSORED = 'message_censored';
   const COLUMN_DATE_ADD = 'message_date_add';
   const COLUMN_ORDER = 'message_order';
   const COLUMN_DEPTH = 'message_depth';
   const COLUMN_SEND_NOTIFY = 'message_reaction_send_notify';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_f_messages');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_TOPIC, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_INT, 'index' => true));
      $this->addColumn(self::COLUMN_ID_USER, array('datatype' => 'smallint', 'default' => 0, 'pdoparam' => PDO::PARAM_INT, 'index' => true));
      $this->addColumn(self::COLUMN_ID_PARENT_MESSAGE, array('datatype' => 'int', 'default' => 0, 'pdoparam' => PDO::PARAM_INT /*,'index' => true*/));
      
      $this->addColumn(self::COLUMN_CREATED_BY, array('datatype' => 'varchar(200)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_CREATED_BY_MODERATOR, array('datatype' => 'tinyint', 'pdoparam' => PDO::PARAM_BOOL, 'default' => 0));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(200)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true));
      $this->addColumn(self::COLUMN_WWW, array('datatype' => 'varchar(400)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_EMAIL, array('datatype' => 'varchar(200)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TEXT, array('datatype' => 'text', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TEXT_CLEAR, array('datatype' => 'text', 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true));
      $this->addColumn(self::COLUMN_IP, array('datatype' => 'varchar(15)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_DATE_ADD, array('datatype' => 'timestamp', 'pdoparam' => PDO::PARAM_STR, 'default' => 'CURRENT_TIMESTAMP'));
      $this->addColumn(self::COLUMN_CENSORED, array('datatype' => 'tinyint', 'pdoparam' => PDO::PARAM_BOOL, 'default' => 0));
      $this->addColumn(self::COLUMN_ORDER, array('datatype' => 'int', 'default' => 0, 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_DEPTH, array('datatype' => 'int', 'default' => 0, 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_SEND_NOTIFY, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => 0));

      $this->setPk(self::COLUMN_ID);

      $this->addForeignKey(self::COLUMN_ID_TOPIC, 'Forum_Model_Topics', Forum_Model_Topics::COLUMN_ID);
      $this->addForeignKey(self::COLUMN_ID_USER, 'Model_Users', Model_Users::COLUMN_ID);
      $this->addRelatioOneToMany(self::COLUMN_ID, 'Forum_Model_Attachments', Forum_Model_Attachments::COLUMN_ID_MESSAGE);
   }

   public function  save(Model_ORM_Record $record) {
      /**
       * @copyright Jakub Vrána
       * @see http://php.vrana.cz/diskuse-s-reakcemi.php
       */
      $this->lock();
      // posun pouze pokud je přidán nový
      if($record->isNew() && $record->{self::COLUMN_ORDER} == 0 && $record->{self::COLUMN_DEPTH} == 0 ){
         // není rodič
         if((int)$record->{self::COLUMN_ID_PARENT_MESSAGE} == 0){
            $order = $this->columns(array('max' => 'MAX('.self::COLUMN_ORDER.')'))
               ->where(self::COLUMN_ID_TOPIC.' = :idt', array('idt' => $record->{self::COLUMN_ID_TOPIC}))
               ->record();
            $record->{self::COLUMN_ORDER} = 1;
            if($order->max != null){
               $record->{self::COLUMN_ORDER} = $order->max+1;
            }
         }
         // je rodič
         else {
            // zjištění pořadí a hloubky příspěvku, na který se reaguje
            $parent = $this->where(self::COLUMN_ID.' = :id', array('id' => $record->{self::COLUMN_ID_PARENT_MESSAGE}))->record();
            
            // zjištění pořadí příspěvku, na jehož místo se bude vkládat - první následující s menší nebo stejnou hloubkou jako rodič
            $replacedComment = $this->columns(array('min' => 'MIN('.self::COLUMN_ORDER.')-1', self::COLUMN_ID, self::COLUMN_DEPTH, self::COLUMN_ORDER))
               ->where(self::COLUMN_ID_TOPIC.' = :idt AND '.self::COLUMN_ORDER.' > :rorder AND '.self::COLUMN_DEPTH.' <= :rdepth',
                  array('idt' => $record->{self::COLUMN_ID_TOPIC}, 
                        'rorder' => $parent->{self::COLUMN_ORDER}, 
                        'rdepth' => $parent->{self::COLUMN_DEPTH}))
               ->record();
                        
           if($replacedComment->min > 0){// bude se vkládat doprostřed tabulky, posunout následující záznamy
              $this->where(self::COLUMN_ORDER." > :curord AND ".self::COLUMN_ID_TOPIC." = :idt", 
                 array( 'curord' => $replacedComment->min, 'idt' => $record->{self::COLUMN_ID_TOPIC}))
                 ->update(array(
                     self::COLUMN_ORDER => array('stmt' => self::COLUMN_ORDER." + 1")
                 ));
              $record->{self::COLUMN_ORDER} =  $replacedComment->min+1; // řadíme za rodiče
           } else { // bude se vkládat na konec tabulky
               $order = $this->columns(array('max' => 'MAX('.self::COLUMN_ORDER.')'))
                  ->where(self::COLUMN_ID_TOPIC.' = :idt', array('idt' => $record->{self::COLUMN_ID_TOPIC}))
                  ->record();
               $record->{self::COLUMN_ORDER} = $order->max+1;
           }
           $record->{self::COLUMN_DEPTH} =  $parent->{self::COLUMN_DEPTH}+1;
         }
      }
      // uložíme komentář
      $ret = parent::save($record);
      $this->unLock();
      return $ret;
   }
   
   public function delete($pk = null)
   {
      // smazání potomků pokud exitují
//      $this->lock();
      $ret = true;
      if($pk != null){
         $rec = $pk;
         if(($pk instanceof Model_ORM_Record) == false){
            $rec = $this->where()->record($pk);
         }
         if($rec){
            //$do = mysql_result(mysql_query("SELECT MIN(poradi) FROM diskuse WHERE poradi > $row[poradi] AND hloubka <= $row[hloubka]"), 0);
            $recsForDelete = $this
               ->columns(array('minord' => 'MIN('.self::COLUMN_ORDER.')'))
               ->where(self::COLUMN_ORDER." > :rorder AND ".self::COLUMN_DEPTH." <= :rdepth", 
               array('rorder' => $rec->{self::COLUMN_ORDER}, 'rdepth' => $rec->{self::COLUMN_DEPTH}))
               ->record();
            
            // mysql_query("DELETE FROM diskuse WHERE poradi >= $row[poradi]" . ($do ? " AND poradi < $do" : ""));
               
               
            if($recsForDelete){ // má potomky
               $this->where(self::COLUMN_ORDER." >= :rord  AND ".self::COLUMN_ORDER." < :toord", 
                  array('rord' => $rec->{self::COLUMN_ORDER}, 'toord' => $recsForDelete->minord ) )
                  ->delete();
                  
               $this->where(self::COLUMN_ORDER." >= :minord", array('minord' => $recsForDelete->minord))
                  ->update(array(
                     self::COLUMN_ORDER => array('stmt' => self::COLUMN_ORDER." - ".($recsForDelete->minord - $rec->{self::COLUMN_ORDER}) )
                  ));
                  
            } else {
               $this->where(self::COLUMN_ORDER." >= :rord", array('rord' => $rec->{self::COLUMN_ORDER}))->delete();
            }   
         }
         
         // Řešení Jakub Vrány 
         // zjištění pořadí a hloubky příspěvku, který se maže
//         $row = mysql_fetch_assoc(mysql_query("SELECT poradi, hloubka FROM diskuse WHERE id = '$_POST[del]'"));
//         if ($row) {
//            // zjištění pořadí příspěvku, do kterého se bude mazat - první následující s menší nebo stejnou hloubkou jako rodič
//            $do = mysql_result(mysql_query("SELECT MIN(poradi) FROM diskuse WHERE poradi > $row[poradi] AND hloubka <= $row[hloubka]"), 0);
//            mysql_query("DELETE FROM diskuse WHERE poradi >= $row[poradi]" . ($do ? " AND poradi < $do" : ""));
//            if ($do) { // maže se zprostředku tabulky, posunout následující záznamy
//               mysql_query("UPDATE diskuse SET poradi = poradi - " . ($do - $row["poradi"]) . " WHERE poradi >= $do");
//            }
//         }
      
      } else {
         $ret = parent::delete($pk);
      }
      
//      $this->unLock();
      return $ret;
   }
   
}

?>