<?php
/**
 * Třída Modelu pro práci s celou databází, jsou zde metody pro výběr tabulek atd
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: $ VVE6.0.0 $Revision: $
 * @author			$Author: $ $Date: $
 *						$LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro práci s celou databází
 */

class Model_Tables extends Model_ORM {
   const COL_TABLE_NAME = 'TABLE_NAME';
   const COL_TABLE_SCHEMA = 'TABLE_SCHEMA';
   const COL_TABLE_ROWS = 'TABLE_ROWS';
   const COL_TABLE_COLLATION = 'TABLE_COLLATION';
   const COL_TABLE_COMMENT = 'TABLE_COMMENT';
   const COL_DATA_LENGTH = 'DATA_LENGTH';
   const COL_ENGINE = 'ENGINE';


//  public 'TABLE_CATALOG' => null
//  public 'TABLE_SCHEMA' => string 'dev' (length=3)
//  public 'TABLE_NAME' => string 'PORTROSS_categories' (length=19)
//  public 'TABLE_TYPE' => string 'BASE TABLE' (length=10)
//  public 'ENGINE' => string 'MyISAM' (length=6)
//  public 'VERSION' => string '10' (length=2)
//  public 'ROW_FORMAT' => string 'Dynamic' (length=7)
//  public 'TABLE_ROWS' => string '16' (length=2)
//  public 'AVG_ROW_LENGTH' => string '145' (length=3)
//  public 'DATA_LENGTH' => string '2320' (length=4)
//  public 'MAX_DATA_LENGTH' => string '281474976710655' (length=15)
//  public 'INDEX_LENGTH' => string '13312' (length=5)
//  public 'DATA_FREE' => string '0' (length=1)
//  public 'AUTO_INCREMENT' => string '112' (length=3)
//  public 'CREATE_TIME' => string '2010-04-20 13:27:37' (length=19)
//  public 'UPDATE_TIME' => string '2010-04-20 13:27:37' (length=19)
//  public 'CHECK_TIME' => null
//  public 'TABLE_COLLATION' => string 'utf8_general_ci' (length=15)
//  public 'CHECKSUM' => null
//  public 'CREATE_OPTIONS' => string '' (length=0)
//  public 'TABLE_COMMENT' => string '' (length=0)


   const DB_NAME = 'information_schema';
   const DB_TABLE = 'tables';
   
   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_inf_sch', false);
      $this->setDbName(self::DB_NAME);
      
      $this->addColumn(self::COL_TABLE_NAME, array('datatype' => 'varchar(50)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COL_TABLE_SCHEMA, array('datatype' => 'varchar(50)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COL_TABLE_ROWS, array('datatype' => 'int', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COL_TABLE_COLLATION, array('datatype' => 'varchar(50)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COL_TABLE_COMMENT, array('datatype' => 'varchar(500)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COL_DATA_LENGTH, array('datatype' => 'int', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COL_ENGINE, array('datatype' => 'varchar(500)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
   }
}

?>