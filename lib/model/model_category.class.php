<?php
/**
 * Třída Modelu pro práci s kategoriemi.
 * Třída, která umožňuje pracovet s modelem kategorií
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: categorymodel.class.php 533 2009-03-29 00:11:57Z jakub $ VVE3.9.2 $Revision: 533 $
 * @author			$Author: jakub $ $Date: 2009-03-29 01:11:57 +0100 (Sun, 29 Mar 2009) $
 *						$LastChangedBy: jakub $ $LastChangedDate: 2009-03-29 01:11:57 +0100 (Sun, 29 Mar 2009) $
 * @abstract 		Třída pro vytvoření modelu pro práci s kategoriemi
 */

class Model_Category extends Model_Db {

	 /**
	  * Názvy sloupců v db tabulce
	  * @var string
	  */
	const COLUMN_CAT_LABEL 	= 'clabel';
	const COLUMN_SEC_LABEL 	= 'slabel';
	const COLUMN_CAT_ID		= 'id_category';
	const COLUMN_SEC_ID		= 'id_section';
	const COLUMN_CAT_URLKEY	= 'urlkey';
	const COLUMN_CAT_LPANEL	= 'left_panel';
	const COLUMN_CAT_RPANEL	= 'right_panel';
	const COLUMN_CAT_PARAMS	= 'cparams';
	const COLUMN_CAT_SHOW_IN_MENU	= 'show_in_menu';
	const COLUMN_CAT_PROTECTED	= 'protected';
	const COLUMN_CAT_PRIORITY	= 'priority';

   const COLUMN_CAT_LABEL_ORIG = 'label';
   const COLUMN_SEC_LABEL_ORIG = 'label';
   const COLUMN_CAT_ALT_ORIG = 'alt';

   const COLUMN_CAT_ACTIVE = 'active';

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
    * Metoda načte kategori, pokud je zadáno Id je načtena určitá, pokud ne je
    * načtena kategorie s nejvyšší prioritou
    * @param integer $idCat -- (option) id kategorie
    */
   public function getCategory($idCat = null) {
      $this->getTables();
      $userNameGroup = AppCore::getAuth()->getGroupName();

      $sqlSelect = $this->getDb()->select()->table($this->catTable, 'cat')
         ->colums(array("clabel" => "IFNULL(cat.label_".Locale::getLang()
            .", cat.label_".Locale::getDefaultLang().")", "id_category", self::COLUMN_CAT_LPANEL,
            self::COLUMN_CAT_RPANEL, self::COLUMN_SEC_ID, self::COLUMN_CAT_PARAMS))
      ->join(array('item' => $this->itemsTable),
         array('cat' => self::COLUMN_CAT_ID, 'item'=>self::COLUMN_CAT_ID), Db::JOIN_INNER)
      ->join(array('sec'=>$this->secTable),
         array('cat' => self::COLUMN_SEC_ID, 'sec'=>self::COLUMN_SEC_ID), Db::JOIN_INNER,
         array("slabel" => 'IFNULL(sec.'.self::COLUMN_SEC_LABEL_ORIG.'_'.Locale::getLang()
            .', sec.'.self::COLUMN_SEC_LABEL_ORIG.'_'.Locale::getDefaultLang().")"))
      ->where("item.".Rights::RIGHTS_GROUPS_TABLE_PREFIX.$userNameGroup, 'r__', Db::OPERATOR_LIKE)
         ->where("cat.".self::COLUMN_CAT_ACTIVE, 1)
         ->order("sec.priority", Db::ORDER_DESC)
         ->order("cat.priority", Db::ORDER_DESC)
         ->order("clabel")
         ->limit(0,1);

      if($idCat != null){
         $sqlSelect->where("cat.".self::COLUMN_CAT_ID, (int)$idCat);
      }

      return $this->getDb()->fetchObject($sqlSelect);
   }

   /**
    * Metoda načte názvy tabulek
    */
   private function getTables() {
      $this->catTable = AppCore::sysConfig()->getOptionValue("category_table", "db_tables");
		$this->secTable = AppCore::sysConfig()->getOptionValue("section_table", "db_tables");
		$this->itemsTable = AppCore::sysConfig()->getOptionValue("items_table", "db_tables");
   }
}
?>