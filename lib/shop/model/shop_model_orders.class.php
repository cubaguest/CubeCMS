<?php

/**
 * Třída modelu produktů
 *
 * @copyright  	Copyright (c) 2008-2011 Jakub Matas
 * @version    	$Id:  $ VVE 7.3 $Revision: $
 * @author        $Author: $ $Date: $
 * @author        cuba
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída 
 * 
 * @sql
 * CREATE  TABLE IF NOT EXISTS `mydb`.`shop_orders` (
  `id_order` INT NOT NULL ,
  `id_customer` INT NOT NULL DEFAULT 0 ,
  `order_shiping_metod` VARCHAR(45) NULL ,
  `order_shipping_price` INT NULL ,
  `order_payment_metod` VARCHAR(45) NULL ,
  `order_payment_price` INT NULL ,
  `order_total` INT NULL ,
  `order_tax` INT NULL ,
  `time_add` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `last_modified` DATETIME NULL ,
  `ip_address` VARCHAR(45) NULL ,
  `order_customer_name` VARCHAR(100) NULL ,
  `order_customer_phone` VARCHAR(15) NULL ,
  `order_customer_email` VARCHAR(100) NULL ,
  `order_customer_street` VARCHAR(100) NULL ,
  `order_customer_city` VARCHAR(100) NULL ,
  `order_customer_post_code` SMALLINT NULL ,
  `order_customer_state` VARCHAR(50) NULL ,
  `order_customer_company` VARCHAR(100) NULL ,
  `order_customer_company_dic` VARCHAR(15) NULL ,
  `order_customer_company_ic` VARCHAR(15) NULL ,
  `order_delivery_name` VARCHAR(100) NULL ,
  `order_delivery_street` VARCHAR(100) NULL ,
  `order_delivery_city` VARCHAR(100) NULL ,
  `order_delivery_post_code` SMALLINT NULL ,
  `order_delivery_state` VARCHAR(50) NULL ,
  `order_note` VARCHAR(500) NULL ,
  PRIMARY KEY (`id_order`) ,
  INDEX `fk_customers` (`id_customer` ASC) ,
  CONSTRAINT `fk_shop_orders_shop_users1`
  FOREIGN KEY (`id_customer` )
  REFERENCES `mydb`.`shop_costomers` (`id_customer` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8
  COLLATE = utf8_general_ci
 */
class Shop_Model_Orders extends Model_ORM {

   const DB_TABLE = 'shop_orders';
   const COLUMN_ID = 'id_order';
   const COLUMN_ID_CUSTOMER = 'id_customer';
   const COLUMN_SHIPPING_METHOD = 'order_shipping_method';
   const COLUMN_SHIPPING_PRICE = 'order_shipping_price';
   const COLUMN_SHIPPING_ID = 'order_shipping_id';
   const COLUMN_PAYMENT_METHOD = 'order_payment_method';
   const COLUMN_PAYMENT_PRICE = 'order_payment_price';
   const COLUMN_PAYMENT_ID = 'order_payment_id';
   const COLUMN_TOTAL = 'order_total';
   const COLUMN_TAX = 'order_tax';
   const COLUMN_TIME_ADD = 'time_add';
   const COLUMN_LAST_MODIFIED = 'last_modified';
   const COLUMN_IP = 'ip_address';
   const COLUMN_IS_NEW = 'order_is_new';
   const COLUMN_CUSTOMER_NAME = 'order_customer_name';
   const COLUMN_CUSTOMER_PHONE = 'order_customer_phone';
   const COLUMN_CUSTOMER_EMAIL = 'order_customer_email';
   const COLUMN_CUSTOMER_STREET = 'order_customer_street';
   const COLUMN_CUSTOMER_CITY = 'order_customer_city';
   const COLUMN_CUSTOMER_POST_CODE = 'order_customer_post_code';
   const COLUMN_CUSTOMER_COUNTRY = 'order_customer_country';
   const COLUMN_CUSTOMER_COMPANY = 'order_customer_company';
   const COLUMN_CUSTOMER_COMPANY_IC = 'order_customer_company_ic';
   const COLUMN_CUSTOMER_COMPANY_DIC = 'order_customer_company_dic';
   const COLUMN_DELIVERY_NAME = 'order_delivery_name';
   const COLUMN_DELIVERY_STREET = 'order_delivery_street';
   const COLUMN_DELIVERY_CITY = 'order_delivery_city';
   const COLUMN_DELIVERY_POST_CODE = 'order_delivery_post_code';
   const COLUMN_DELIVERY_COUNTRY = 'order_delivery_country';
   const COLUMN_PICKUP_DATE = 'order_pickup_date';
   const COLUMN_NOTE = 'order_note';

   protected function _initTable()
   {
      $this->setTableName(self::DB_TABLE, 't_sh_orders');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_CUSTOMER, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_INT, 'default' => 0));

      $this->addColumn(self::COLUMN_SHIPPING_METHOD, array('datatype' => 'varchar(50)', 'pdoparam' => PDO::PARAM_STR, 'default' => null));
      $this->addColumn(self::COLUMN_SHIPPING_PRICE, array('datatype' => 'int', 'pdoparam' => PDO::PARAM_INT, 'default' => 0));
      $this->addColumn(self::COLUMN_SHIPPING_ID, array('datatype' => 'smallint', 'pdoparam' => PDO::PARAM_INT, 'default' => 0));
      $this->addColumn(self::COLUMN_PAYMENT_METHOD, array('datatype' => 'varchar(50)', 'pdoparam' => PDO::PARAM_STR, 'default' => null));
      $this->addColumn(self::COLUMN_PAYMENT_PRICE, array('datatype' => 'int', 'pdoparam' => PDO::PARAM_INT, 'default' => 0));
      $this->addColumn(self::COLUMN_PAYMENT_ID, array('datatype' => 'smallint', 'pdoparam' => PDO::PARAM_INT, 'default' => 0));

      $this->addColumn(self::COLUMN_TOTAL, array('datatype' => 'int', 'pdoparam' => PDO::PARAM_INT, 'default' => 0));
      $this->addColumn(self::COLUMN_TAX, array('datatype' => 'int', 'pdoparam' => PDO::PARAM_INT, 'default' => 0));
      $this->addColumn(self::COLUMN_TIME_ADD, array('datatype' => 'timestamp', 'pdoparam' => PDO::PARAM_STR, 'default' => 'CURRENT_TIMESTAMP'));
      $this->addColumn(self::COLUMN_LAST_MODIFIED, array('datatype' => 'datetime', 'pdoparam' => PDO::PARAM_STR, 'default' => null));
      $this->addColumn(self::COLUMN_IP, array('datatype' => 'varchar(15)', 'pdoparam' => PDO::PARAM_STR));

      $this->addColumn(self::COLUMN_CUSTOMER_NAME, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR, 'default' => null));
      $this->addColumn(self::COLUMN_CUSTOMER_PHONE, array('datatype' => 'varchar(15)', 'pdoparam' => PDO::PARAM_STR, 'default' => null));
      $this->addColumn(self::COLUMN_CUSTOMER_EMAIL, array('datatype' => 'varchar(50)', 'pdoparam' => PDO::PARAM_STR, 'default' => null));
      $this->addColumn(self::COLUMN_CUSTOMER_STREET, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR, 'default' => null));
      $this->addColumn(self::COLUMN_CUSTOMER_CITY, array('datatype' => 'varchar(50)', 'pdoparam' => PDO::PARAM_STR, 'default' => null));
      $this->addColumn(self::COLUMN_CUSTOMER_POST_CODE, array('datatype' => 'int', 'pdoparam' => PDO::PARAM_INT, 'default' => 0));
      $this->addColumn(self::COLUMN_CUSTOMER_COUNTRY, array('datatype' => 'varchar(50)', 'pdoparam' => PDO::PARAM_STR, 'default' => null));
      $this->addColumn(self::COLUMN_CUSTOMER_COMPANY, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR, 'default' => null));
      $this->addColumn(self::COLUMN_CUSTOMER_COMPANY_IC, array('datatype' => 'varchar(20)', 'pdoparam' => PDO::PARAM_STR, 'default' => null));
      $this->addColumn(self::COLUMN_CUSTOMER_COMPANY_DIC, array('datatype' => 'varchar(20)', 'pdoparam' => PDO::PARAM_STR, 'default' => null));

      $this->addColumn(self::COLUMN_DELIVERY_NAME, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR, 'default' => null));
      $this->addColumn(self::COLUMN_DELIVERY_STREET, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR, 'default' => null));
      $this->addColumn(self::COLUMN_DELIVERY_CITY, array('datatype' => 'varchar(50)', 'pdoparam' => PDO::PARAM_STR, 'default' => null));
      $this->addColumn(self::COLUMN_DELIVERY_POST_CODE, array('datatype' => 'int', 'pdoparam' => PDO::PARAM_INT, 'default' => 0));
      $this->addColumn(self::COLUMN_DELIVERY_COUNTRY, array('datatype' => 'varchar(50)', 'pdoparam' => PDO::PARAM_STR, 'default' => null));

      $this->addColumn(self::COLUMN_NOTE, array('datatype' => 'text', 'pdoparam' => PDO::PARAM_STR, 'default' => null));

      $this->addColumn(self::COLUMN_IS_NEW, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => true));

      $this->addColumn(self::COLUMN_PICKUP_DATE, array('datatype' => 'date', 'pdoparam' => PDO::PARAM_STR, 'default' => null));

      $this->setPk(self::COLUMN_ID);

      $this->addForeignKey(self::COLUMN_ID_CUSTOMER, "Shop_Model_Customers", Shop_Model_Customers::COLUMN_ID);
   }
   
   protected function beforeSave(\Model_ORM_Record $record, $type = 'U')
   {
      parent::beforeSave($record, $type);
   }

}

class Shop_Model_Orders_Record extends Model_ORM_Record {

   public function getLastState()
   {
      $mHistory = new Shop_Model_OrdersHistory();
      return $mHistory
                      ->joinFK(Shop_Model_OrdersHistory::COLUMN_ID_STATE)
                      ->where(Shop_Model_Orders::COLUMN_ID, $this->getPK())
                      ->order(array(Shop_Model_OrdersHistory::COLUMN_TIME_ADD => Model_ORM::ORDER_DESC))
                      ->record();
   }

   public function getHistory()
   {
      $mHistory = new Shop_Model_OrdersHistory();

      return $mHistory
                      ->joinFK(Shop_Model_OrdersHistory::COLUMN_ID_STATE)
                      ->where(Shop_Model_Orders::COLUMN_ID, $this->getPK())
                      ->order(array(Shop_Model_OrdersHistory::COLUMN_TIME_ADD => Model_ORM::ORDER_DESC))
                      ->records();
   }

   public function changeState($idNewState, $sendMail = true, $note = null)
   {
      $mHistory = new Shop_Model_OrdersHistory();
      $history = $mHistory->newRecord();

      $history->{Shop_Model_OrdersHistory::COLUMN_ID_ORDER} = $this->getPK();
      $history->{Shop_Model_OrdersHistory::COLUMN_ID_STATE} = $idNewState;
      $history->{Shop_Model_OrdersHistory::COLUMN_NOTE} = $note;
      $history->save();
      $state = Shop_Model_OrdersStates::getRecord($idNewState);

      $tr = new Translator();
      
      if ($sendMail && $state->{Shop_Model_OrdersStates::COLUMN_ID_TEMPLATE} != 0) {
         $lang = Locales::getLang();
         // order lang načís od zákazníka nebo z objednávky
         $tpl = Templates_Model::getTemplate($state->{Shop_Model_OrdersStates::COLUMN_ID_TEMPLATE});
         if (!$tpl) {
            throw new InvalidArgumentException(sprintf($tr->tr('Šablona emailu stavu ID: %s nebyla nalezena.'),$state->getPK() ));
         }
         $tplCnt = $tpl->{Templates_Model::COLUMN_CONTENT};
         $email = new Email(true);
//         $email->setSubject(sprintf($tr->tr('Změna stavu Vaší objednávky č. %s'), $this->getOrderNumber()));
         $email->setSubject(str_replace('{ORDER_NUMBER}', $this->getOrderNumber() , $tpl->{Templates_Model::COLUMN_NAME}));
         
         $tplCnt = Shop_Tools::getMailTplContent($tplCnt, $this, null, $history, $lang);
         
         $email->setContent(Email::getBaseHtmlMail($tplCnt));
         $email->addAddress($this->{Shop_Model_Orders::COLUMN_CUSTOMER_EMAIL}, $this->{Shop_Model_Orders::COLUMN_CUSTOMER_NAME});
         $email->send();
      }
   }

   public function getOrderNumber()
   {
      return Shop_Tools::getFormatOrderNumber($this->getPK());
   }
   
   public function getItems()
   {
      $m = new Shop_Model_OrderItems();
      return $m->where(Shop_Model_OrderItems::COLUMN_ID_ORDER.' = :id', array('id' => $this->getPK()))->records();
   }

}
