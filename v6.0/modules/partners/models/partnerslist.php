<?php
/*
 * Třída modelu s listem Sponzorů
 * 
 * note:
 * první je vždy název modulu a potom název modelu
 * první písmena dle standardu jsou velká 
 */
class PartnersListModel extends DbModel {
   /**
    * Názvy sloupců v DB
    */
   const COLUMN_ID_PARTNER = 'id_partner';
   const COLUMN_ID_ITEM = 'id_item';
   const COLUMN_NAME = 'name';
   const COLUMN_LABEL = 'label';
   const COLUMN_LABEL_LANG_PREFIX = 'label_';
   const COLUMN_URL = 'url';
   const COLUMN_LOGO_FILE = 'logo_file';
   const COLUMN_LOGO_TYPE = 'logo_type';
   const COLUMN_LOGO_WIDTH = 'logo_width';
   const COLUMN_LOGO_HEIGHT = 'logo_height';

   /**
    * Metoda vrací seznam partnerů
    * @return array
    */
   public function getPartners() {
//      $sqlSelect = $this->getDb()->select()->from($this->getModule()->getDbTable(),
//         array(self::COLUMN_LABEL => "IFNULL(".self::COLUMN_LABEL_LANG_PREFIX.Locale::getLang()
//            .", ".self::COLUMN_LABEL_LANG_PREFIX.Locale::getDefaultLang().")",
//			self::COLUMN_ID_PARTNER, self::COLUMN_NAME, self::COLUMN_LOGO_FILE, self::COLUMN_LOGO_TYPE,
//         self::COLUMN_LOGO_WIDTH, self::COLUMN_LOGO_HEIGHT, self::COLUMN_URL, PartnerDetailModel::COLUMN_PRIORITY))
//				->where(self::COLUMN_ID_ITEM." = ".$this->getModule()->getId())
//            ->order(PartnerDetailModel::COLUMN_PRIORITY,'desc')
//				->order(self::COLUMN_NAME, 'asc');
//
//		$returArray = $this->getDb()->fetchAssoc($sqlSelect);
//
//		return $returArray;
   }

   /**
    * Metoda vrací zadaný počet náhodných partnerů
    * @param int $num -- (option) počet partnerů
    */
   public function getRandomPartners($num = 1) {
//      $sqlSelect = $this->getDb()->select()->from($this->getModule()->getDbTable(),
//         array(self::COLUMN_LABEL => "IFNULL(".self::COLUMN_LABEL_LANG_PREFIX.Locale::getLang()
//            .", ".self::COLUMN_LABEL_LANG_PREFIX.Locale::getDefaultLang().")",
//			self::COLUMN_ID_PARTNER, self::COLUMN_NAME, self::COLUMN_LOGO_FILE, self::COLUMN_LOGO_TYPE,
//         self::COLUMN_LOGO_WIDTH, self::COLUMN_LOGO_HEIGHT, self::COLUMN_URL))
//				->where(self::COLUMN_ID_ITEM." = ".$this->getModule()->getId())
//				->order('rand()')
//            ->limit(0, $num);
//
//		$returArray = $this->getDb()->fetchAssoc($sqlSelect);
//
//		return $returArray;
   }
}

?>