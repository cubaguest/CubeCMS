<?php
/**
 * Třída Modelu pro práci se sekcemi
 * Třída, která umožňuje pracovet s modelem kategorií
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: sectionsmodel.class.php 533 2009-03-29 00:11:57Z jakub $ VVE3.9.2 $Revision: 533 $
 * @author			$Author: jakub $ $Date: 2009-03-29 00:11:57 +0000 (Ne, 29 bře 2009) $
 *						$LastChangedBy: jakub $ $LastChangedDate: 2009-03-29 00:11:57 +0000 (Ne, 29 bře 2009) $
 * @abstract 		Třída pro vytvoření modelu pro práci s kategoriemi
 */

class Model_Sections extends Model_Db {

	 /**
	  * Názvy sloupců v db tabulce
	  * @var string
	  */
	const COLUMN_SEC_LABEL 	= 'slabel';
	const COLUMN_SEC_ALT 	= 'salt';
	const COLUMN_SEC_ID		= 'id_section';
	const COLUMN_SEC_PRIORITY		= 'priority';
   const COLUMN_SEC_LABEL_ORIG = 'label';
   const COLUMN_SEC_ALT_ORIG = 'alt';

   /**
    * Proměná s názvem tabulky s kategoriemi
    * @var string
    */
   private $catTable = null;

   /**
    * Proměná s názvem tabulky se sekcemi
    * @var string
    */
   private $secTable = null;

   /**
    * Proměná s názvem tabulky s itemi
    * @var string
    */
   private $itemsTable = null;

   /**
    * Metoda načte tabulky
    */
   private function getTables() {
      $this->catTable = AppCore::sysConfig()->getOptionValue("category_table", "db_tables");
		$this->secTable = AppCore::sysConfig()->getOptionValue("section_table", "db_tables");
		$this->itemsTable = AppCore::sysConfig()->getOptionValue("items_table", "db_tables");
   }
}

?>