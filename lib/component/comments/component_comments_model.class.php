<?php
/**
 * Třída Modelu pro načítání a ukládání komentářů
 *
 * @copyright  	Copyright (c) 2008-2010 Jakub Matas
 * @version    	$Id: $ VVE 6.1.0 $Revision: $
 * @author      $Author: $ $Date:  $
 *              $LastChangedBy:$ $LastChangedDate:  $
 * @abstract    Třída pro vytvoření modelu pro práci s komentáři
 */

class Component_Comments_Model extends Model_ORM {
   /**
    * Tabulka s detaily
    */
   const DB_TABLE = 'comments';

   /**
    * Názvy sloupců v db
    * @var string
    */
   const COL_ID			= 'id_comment';
   const COL_ID_ART		= 'id_article';
   const COL_ID_CAT		= 'id_category';
   const COL_ID_PARENT	= 'id_parent';
   const COL_NICK       = 'nick';
   const COL_COMMENT		= 'comment';
   const COL_PUBLIC		= 'public';
   const COL_CENSORED	= 'censored';
   const COL_ORDER		= 'corder';
   const COL_LEVEL		= 'level';
   const COL_TIME_ADD	= 'time_add';
   const COL_IP_ADDRESS	= 'ip_address';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_comments');

      $this->addColumn(self::COL_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COL_ID_ART, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COL_ID_PARENT, array('datatype' => 'smallint', 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COL_ID_CAT, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COL_NICK, array('datatype' => 'varchar(200)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COL_COMMENT, array('datatype' => 'text', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COL_PUBLIC, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => false));
      $this->addColumn(self::COL_CENSORED, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => false));
      $this->addColumn(self::COL_ORDER, array('datatype' => 'smallint', 'pdoparam' => PDO::PARAM_INT, 'default' => 1));
      $this->addColumn(self::COL_LEVEL, array('datatype' => 'smallint', 'pdoparam' => PDO::PARAM_INT, 'default' => 0));

      $this->addColumn(self::COL_TIME_ADD, array('datatype' => 'timestamp', 'pdoparam' => PDO::PARAM_STR, 'default' => 'CURRENT_TIMESTAMP'));
      $this->addColumn(self::COL_IP_ADDRESS, array('datatype' => 'varchar(50)', 'pdoparam' => PDO::PARAM_STR, 'default' => $_SERVER['REMOTE_ADDR']));

      $this->setPk(self::COL_ID);
   }

   public function  save(Model_ORM_Record $record) {
//      $dbc->query("LOCK TABLES {$this->getTableName()} WRITE");
//      $dbc->beginTransaction();
      $this->lock();
      // posun pouze pokud je přidán nový
      if($record->isNew()){
         // není rodič
         if((int)$record->{self::COL_ID_PARENT} == 0){
            $record->{self::COL_ID_PARENT} = 0;
            $order = $this->columns(array('max' => 'MAX('.self::COL_ORDER.')'))
               ->where(self::COL_ID_CAT.' = :idc AND '.self::COL_ID_ART.' = :ida', array('idc' => $record->{self::COL_ID_CAT}, 'ida' => $record->{self::COL_ID_ART}))
               ->record();
            $record->{self::COL_ORDER} = 1;
            if($order->max != null){
               $record->{self::COL_ORDER} = $order->max+1;
            }
         }
         // je rodič
         else {
            $dbc = Db_PDO::getInstance();
            $tbl = $dbc->table(self::DB_TABLE);
            // zjištění pořadí a hloubky příspěvku, na který se reaguje
            $parent = $this->where(self::COL_ID.' = :id', array('id' => $record->{self::COL_ID_PARENT}))->record();
            // zjištění pořadí příspěvku, na jehož místo se bude vkládat - první následující s menší nebo stejnou hloubkou jako rodič
            $replacedComment = $this->columns(array('min' => 'MIN('.self::COL_ORDER.')-1', self::COL_ID, self::COL_LEVEL, self::COL_ORDER))
               ->where(self::COL_ID_CAT.' = :idc AND '.self::COL_ID_ART.' = :ida AND '.self::COL_ORDER.' > :rorder AND '.self::COL_LEVEL.' <= :rlevel',
                  array('idc' => $record->{self::COL_ID_CAT}, 'ida' => $record->{self::COL_ID_ART},
                     'rorder' => $parent->{self::COL_ORDER}, 'rlevel' => $parent->{self::COL_LEVEL}))->record();
           if($replacedComment->min > 0){// bude se vkládat doprostřed tabulky, posunout následující záznamy
              $stmt = $dbc->prepare("UPDATE $tbl SET ".self::COL_ORDER." = ".self::COL_ORDER."+1 "
                       ."WHERE ".self::COL_ORDER." > :curord AND ".self::COL_ID_CAT.' = :idc AND '.self::COL_ID_ART.' = :ida');
              $stmt->bindValue('curord', $replacedComment->min, PDO::PARAM_INT);
              $stmt->bindValue('idc', $record->{self::COL_ID_CAT}, PDO::PARAM_INT);
              $stmt->bindValue('ida', $record->{self::COL_ID_ART}, PDO::PARAM_INT);
              $stmt->execute();
              $record->{self::COL_ORDER} =  $replacedComment->min+1; // řadíme za rodiče
           } else { // bude se vkládat na konec tabulky
               $order = $this->columns(array('max' => 'MAX('.self::COL_ORDER.')'))
                  ->where(self::COL_ID_CAT.' = :idc AND '.self::COL_ID_ART.' = :ida', array('idc' => $record->{self::COL_ID_CAT}, 'ida' => $record->{self::COL_ID_ART}))
                  ->record();
               $record->{self::COL_ORDER} = $order->max+1;
           }
           $record->{self::COL_LEVEL} =  $parent->{self::COL_LEVEL}+1;
         }
      }
      // uložíme komentář
      $ret = parent::save($record);
      $this->unLock();
      return $ret;
   }

   public function changePublic($idComment) {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare("UPDATE ".$dbc->table(self::DB_TABLE)." SET ".self::COL_PUBLIC." = IF(".self::COL_PUBLIC."=1,0,1) WHERE ".self::COL_ID." = :idc");
      $dbst->execute(array(':idc' => $idComment));

   }

   public function changeCensored($idComment) {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare("UPDATE ".$dbc->table(self::DB_TABLE)." SET ".self::COL_CENSORED." = IF(".self::COL_CENSORED."=1,0,1) WHERE ".self::COL_ID." = :idc");
      $dbst->execute(array(':idc' => $idComment));

   }
}
?>