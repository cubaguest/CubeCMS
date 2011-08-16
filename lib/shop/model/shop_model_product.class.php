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
class Shop_Model_Product extends Model_ORM
{
   const DB_TABLE = 'shop_products_general';

   /**
    * Názvy sloupců v databázi
    */
   const COLUMN_ID = 'id_product';
   const COLUMN_ID_CATEGORY = 'id_category';
   const COLUMN_ID_TAX = 'id_tax';
   
   const COLUMN_CODE = 'code';
   const COLUMN_NAME = 'name';
   const COLUMN_URLKEY = 'urlkey';
   const COLUMN_TEXT_SHORT = 'text_short';
   const COLUMN_TEXT = 'text';
   const COLUMN_TEXT_CLEAR = 'text_clear';
   const COLUMN_KEYWORDS = 'keywords';
   
   const COLUMN_PRICE = 'price';
   const COLUMN_UNIT = 'unit';
   const COLUMN_UNIT_SIZE = 'unit_size';
   const COLUMN_QUANTITY = 'quantity';
   const COLUMN_WEIGHT = 'weight';
   const COLUMN_DISCOUNT = 'discount';
   const COLUMN_DELETED = 'deleted';
   const COLUMN_SHOWED = 'showed';
   const COLUMN_ACTIVE = 'active';
   const COLUMN_IMAGE = 'image';
   const COLUMN_DATE_ADD = 'date_add';
   
   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_sh_pr_gen');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_CATEGORY, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_ID_TAX, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
      
      $this->addColumn(self::COLUMN_CODE, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(200)', 'nn' => true, 'lang' => true, 
         'pdoparam' => PDO::PARAM_STR, 'fulltext' => true, 'fulltextRel' => VVE_SEARCH_ARTICLE_REL_MULTIPLIER));
      $this->addColumn(self::COLUMN_URLKEY, array('datatype' => 'varchar(200)', 'nn' => true, 'lang' => true, 
         'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TEXT_SHORT, array('datatype' => 'text', 'lang' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TEXT, array('datatype' => 'text', 'lang' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TEXT_CLEAR, array('datatype' => 'text', 'lang' => true, 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true));
      $this->addColumn(self::COLUMN_KEYWORDS, array('datatype' => 'varchar(200)', 'lang' => true, 'pdoparam' => PDO::PARAM_STR));
      
      $this->addColumn(self::COLUMN_PRICE, array('datatype' => 'int', 'pdoparam' => PDO::PARAM_STR, 'default' => 0));
      $this->addColumn(self::COLUMN_UNIT, array('datatype' => 'varchar(20)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_UNIT_SIZE, array('datatype' => 'int', 'pdoparam' => PDO::PARAM_INT, 'default' => 0));
      $this->addColumn(self::COLUMN_QUANTITY, array('datatype' => 'int', 'pdoparam' => PDO::PARAM_INT, 'default' => -1));
      $this->addColumn(self::COLUMN_WEIGHT, array('datatype' => 'int', 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_DISCOUNT, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => false));
      $this->addColumn(self::COLUMN_DELETED, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => false));
      $this->addColumn(self::COLUMN_SHOWED, array('datatype' => 'int', 'pdoparam' => PDO::PARAM_INT, 'default' => 0));
      $this->addColumn(self::COLUMN_ACTIVE, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => true));
      $this->addColumn(self::COLUMN_IMAGE, array('datatype' => 'varchar(50)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_DATE_ADD, array('datatype' => 'timestamp', 'pdoparam' => PDO::PARAM_STR, 'default' => 'CURRENT_TIMESTAMP'));

      $this->setPk(self::COLUMN_ID);
      
      $this->addForeignKey(self::COLUMN_ID_CATEGORY, 'Model_Categories', Model_Category::COLUMN_CAT_ID);
      $this->addForeignKey(self::COLUMN_ID_TAX, "Shop_Model_Tax", Shop_Model_Tax::COLUMN_ID);
      
//      $this->addRelatioOneToMany(self::COLUMN_ID, 'Articles_Model_PrivateUsers', Articles_Model_PrivateUsers::COLUMN_A_H_U_ID_ARTICLE);
   }
}
?>
