<?php
/**
 * Třída Modelu pro práci s kategoriemi.
 * Třída, která umožňuje pracovet s modelem kategorií
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: model_category.class.php 648 2009-09-16 16:20:04Z jakub $ VVE3.9.2 $Revision: 648 $
 * @author			$Author: jakub $ $Date: 2009-09-16 18:20:04 +0200 (Wed, 16 Sep 2009) $
 *						$LastChangedBy: jakub $ $LastChangedDate: 2009-09-16 18:20:04 +0200 (Wed, 16 Sep 2009) $
 * @abstract 		Třída pro vytvoření modelu pro práci s kategoriemi
 */

class Component_Model_UserFiles extends Model_PDO {
/**
 * Tabulka s detaily
 */
   const DB_TABLE = 'userfiles';

   /**
    * Názvy sloupců v db
    * @var string
    */
   const COLUM_ID				= 'id_file';
   const COLUM_ID_USER		= 'id_user';
   const COLUM_ID_ITEM		= 'id_item';
   const COLUM_ID_ARTICLE	= 'id_article';
   const COLUM_FILE			= 'file';
   const COLUM_SIZE			= 'size';
   const COLUM_TIME			= 'time';
   const COLUM_TYPE			= 'type';
   const COLUM_WIDTH			= 'width';
   const COLUM_HEIGHT		= 'height';

//   const COLUM_LINK_TO_SHOW	= 'link_show';
//   const COLUM_LINK_TO_DOWNLOAD= 'link_download';
//   const COLUM_LINK_TO_SMALL	= 'link_small';

   /**
    * Metoda načte kategori, pokud je zadán klíč je načtena určitá, pokud ne je
    * načtena kategorie s nejvyšší prioritou
    * @param string $catKey -- (option) klíč kategorie
    */
   public function getCategory($catKey = null) {
      $userNameGroup = AppCore::getAuth()->getGroupName();

      $dbc = new Db_PDO();
      if($catKey != null) {
         $dbst = $dbc->query("SELECT cat.*, sec.*, module.* FROM ".Db_PDO::table(self::DB_TABLE)." AS cat
             INNER JOIN ".Db_PDO::table(Model_Sections::DB_TABLE)." AS sec ON cat.".self::COLUMN_SEC_ID
             ." = sec.".Model_Sections::COLUMN_SEC_ID."
             INNER JOIN ".Db_PDO::table(Model_Module::DB_TABLE_MODULES)." AS module ON cat.".self::COLUMN_MODULE_ID
             ." = module.".Model_Module::COLUMN_ID_MODULE."
             WHERE (cat.".self::COLUMN_CAT_GROUP_PREFIX.$userNameGroup." LIKE 'r__')
             AND (cat.".self::COLUMN_ACTIVE." = 1) AND (cat.".self::COLUMN_URLKEY.'_'.Locale::getLang()
            ." = ".$dbc->quote($catKey).") LIMIT 0, 1");
      } else {
         $dbst = $dbc->query("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)." AS cat
             INNER JOIN ".Db_PDO::table(Model_Sections::DB_TABLE)." AS sec ON cat.".self::COLUMN_SEC_ID
             ." = sec.".Model_Sections::COLUMN_SEC_ID."
             INNER JOIN ".Db_PDO::table(Model_Module::DB_TABLE_MODULES)." AS module ON cat.".self::COLUMN_MODULE_ID
             ." = module.".Model_Module::COLUMN_ID_MODULE."
             WHERE (cat.".self::COLUMN_CAT_GROUP_PREFIX.$userNameGroup." LIKE 'r__')
             AND (cat.".self::COLUMN_ACTIVE." = 1)
             ORDER BY cat.".self::COLUMN_PRIORITY." DESC LIMIT 0, 1");
      }
      $dbst->execute();

      $cl = new Model_LangContainer();
      $dbst->setFetchMode(PDO::FETCH_INTO, $cl);
      return $dbst->fetch();
   }
}
?>