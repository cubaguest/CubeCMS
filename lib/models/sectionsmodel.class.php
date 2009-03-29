<?php
/**
 * Třída Modelu pro práci se sekcemi
 * Třída, která umožňuje pracovet s modelem kategorií
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.2 $Revision$
 * @author			$Author$ $Date$
 *						$LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída pro vytvoření modelu pro práci s kategoriemi
 */

class SectionsModel extends DbModel {

	 /**
	  * Názvy sloupců v db tabulce
	  * @var string
	  */
	const COLUMN_SEC_LABEL 	= 'slabel';
	const COLUMN_SEC_ID		= 'id_section';
	const COLUMN_SEC_PRIORITY		= 'priority';
   const COLUMN_SEC_LABEL_ORIG = 'label';

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