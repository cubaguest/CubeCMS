<?php
/**
 * Třída Modelu pro načítání sdílení
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: model_category.class.php 648 2009-09-16 16:20:04Z jakub $ VVE3.9.2 $Revision: 648 $
 * @author			$Author: jakub $ $Date: 2009-09-16 18:20:04 +0200 (Wed, 16 Sep 2009) $
 *						$LastChangedBy: jakub $ $LastChangedDate: 2009-09-16 18:20:04 +0200 (Wed, 16 Sep 2009) $
 * @abstract 		Třída pro vytvoření modelu pro práci s kategoriemi
 */

class Component_Share_Model extends Model_PDO {
/**
 * Tabulka s detaily
 */
   const DB_TABLE = 'shares';

   /**
    * Názvy sloupců v db
    * @var string
    */
   const COLUM_ID				= 'id_share';
   const COLUM_LINK			= 'link';
   const COLUM_ICON			= 'icon';
   const COLUM_NAME			= 'name';
   const COLUM_TYPE			= 'type';
   const COLUM_WIDTH			= 'width';
   const COLUM_HEIGHT		= 'height';

   /**
    * Metoda načte všechny sdílení
    * @return PDOStatement
    */
   public function getShares() {
      $dbc = new Db_PDO();
      $dbst = $dbc->query("SELECT * FROM ".$this->getSharesTable());
      $dbst->execute();

      $dbst->setFetchMode(PDO::FETCH_OBJ);
      return $dbst;
   }

   private function getSharesTable(){
      if(defined('VVE_SHARES_TABLE') AND VVE_SHARES_TABLE != null){
         return VVE_SHARES_TABLE;
      } else {
         return Db_PDO::table(self::DB_TABLE);
      }
   }
}
?>