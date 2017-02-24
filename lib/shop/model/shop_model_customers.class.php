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
 */
class Shop_Model_Customers extends Model_ORM
{
   const DB_TABLE = 'shop_customers';

   /**
    * Názvy sloupců v databázi
    */
   const COLUMN_ID = 'id_customer';
   const COLUMN_ID_USER = 'id_user';
   const COLUMN_ID_GROUP = 'id_customer_group';
//   const COLUMN_SURNAME = 'customer_surname';
   const COLUMN_PHONE = 'customer_phone';
   const COLUMN_COMPANY = 'customer_company';
   const COLUMN_STREET = 'customer_street';
   const COLUMN_CITY = 'customer_city';
   const COLUMN_PSC = 'customer_psc';
   const COLUMN_IC = 'customer_ic';
   const COLUMN_DIC = 'customer_dic';
   const COLUMN_ID_COUNTRY = 'id_country';
   const COLUMN_DELIVERY_NAME = 'customer_delivery_name';
   const COLUMN_DELIVERY_STREET = 'customer_delivery_street';
   const COLUMN_DELIVERY_CITY = 'customer_delivery_city';
   const COLUMN_DELIVERY_PSC = 'customer_delivery_psc';
   const COLUMN_ID_DELIVERY_COUNTRY = 'id_delivery_country';
   const COLUMN_NEWSLETTER = 'newsletter';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_s_cust');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'int', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_USER, array('datatype' => 'int', 'nn' => true, 'index' => true));
      $this->addColumn(self::COLUMN_ID_GROUP, array('datatype' => 'int', 'nn' => true, 'index' => true));
      $this->addColumn(self::COLUMN_ID_COUNTRY, array('datatype' => 'int', 'index' => true, 'default' => 0));
      $this->addColumn(self::COLUMN_ID_DELIVERY_COUNTRY, array('datatype' => 'int', 'index' => true, 'default' => 0));

      $this->addColumn(self::COLUMN_PHONE, array('datatype' => 'varchar(15)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_COMPANY, array('datatype' => 'varchar(70)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_STREET, array('datatype' => 'varchar(70)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_CITY, array('datatype' => 'varchar(50)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_PSC, array('datatype' => 'varchar(6)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_IC, array('datatype' => 'varchar(20)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_DIC, array('datatype' => 'varchar(20)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_DELIVERY_NAME, array('datatype' => 'varchar(70)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_DELIVERY_STREET, array('datatype' => 'varchar(70)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_DELIVERY_CITY, array('datatype' => 'varchar(50)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_DELIVERY_PSC, array('datatype' => 'varchar(6)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_NEWSLETTER, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => false));

      $this->setPk(self::COLUMN_ID);

      $this->addForeignKey(self::COLUMN_ID_USER, 'Model_Users', Model_Users::COLUMN_ID);
      $this->addForeignKey(self::COLUMN_ID_GROUP, 'Shop_Model_CustomersGroups', Shop_Model_CustomersGroups::COLUMN_ID);

      // relationa na orders je vypnuta, protože jinak by docházelo k mazání objednávek,

   }

   public static function getCustomer($idUser)
   {
      $m = new self();
      return $m
         ->joinFK(self::COLUMN_ID_USER)
         ->joinFK(self::COLUMN_ID_GROUP)
         ->where(self::COLUMN_ID_USER." = :idu", array('idu' => $idUser))
         ->record();
   }

   public static function getCustomerByEmail($email)
   {
      $m = new self();
      return $m
         ->joinFK(self::COLUMN_ID_USER)
         ->joinFK(self::COLUMN_ID_GROUP)
         ->where(Model_Users::COLUMN_MAIL." = :m", array('m' => $email))
         ->record();
   }
}
