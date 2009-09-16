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

class Model_Sections extends Model_Db {
   /**
    * Tabulka s detaily
    */
    const DB_TABLE = 'sections';

	 /**
	  * Názvy sloupců v db tabulce
	  * @var string
	  */
	const COLUMN_SEC_LABEL 	= 'slabel';
	const COLUMN_SEC_ALT 	= 'salt';
	const COLUMN_SEC_ID		= 'id_section';
	const COLUMN_SEC_PRIORITY		= 'priority';
}

?>