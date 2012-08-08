<?php
/**
 * Třída obsluhuje db statement pro db konektor
 *
 * @copyright  	Copyright (c) 2008-2012 Jakub Matas
 * @version    	$Id: $ Cube CMS 7.14 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro vytvoření db statementu pro db konektor
 */
class Db_PDO_Statement extends PDOStatement {
   public $dbh;
   protected function __construct($dbh) {
      $this->dbh = $dbh;
   }
   
   public function execute($input_parameters = null)
   {
      Db_PDO::addQueryCount();
      return parent::execute($input_parameters);
   }
}
?>