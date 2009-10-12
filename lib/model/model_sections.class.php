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

class Model_Sections extends Model_PDO {
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

   /**
    * Metoda vrací seznam sekcí
    * @return PDOStatement -- pole se sekcema
    */
   public function getSections() {
      $dbc = new Db_PDO();
      $dbst = $dbc->query("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)."
             ORDER BY ".self::COLUMN_SEC_PRIORITY." ASC", PDO::FETCH_CLASS, 'Model_LangContainer');
      return $dbst->fetchAll();
   }
}

?>