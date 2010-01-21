<?php
class GuestbookPanel extends Panel {
	
	/**
	 * Názva sloupců v db
	 * @var string
	 */
	const COLUM_SPONSOR_ID = 'id_sponsor';
	const COLUM_SPONSOR_ID_ITEM = 'id_item';
	const COLUM_SPONSOR_NAME_LANG_PREFIX = 'name_';
	const COLUM_SPONSOR_LABEL_LANG_PREFIX = 'label_';
	const COLUM_SPONSOR_URL = 'url';
	const COLUM_SPONSOR_LOGO_IMAGE = 'logo_image';
	const COLUM_SPONSOR_URLKEY = 'urlkey';
	const COLUM_SPONSOR_DELETED = 'deleted';
	
	const COLUM_SPONSOR_NAME = 'name';	
	
	/**
	 * Počet novinek v panelu
	 * @var integer
	 */
	const NUMBER_OF_SPONSORS = 3;

	/**
	 * Pole ponzorů
	 * @var array
	 */
	private $sponsorsArray = array();
	
	/**
	 * Link do novinek
	 * @var string
	 */
	private $sponsorsLink = null;
	
	/**
	 * Cesta k obrázkům
	 * @var string
	 */
	private $dirToImages = null;
	
	
	public function panelController() {
//		$sqlSelect = $this->getDb()->select()->from($this->getModule()->getDbTable(), array(self::COLUM_SPONSOR_NAME => "IFNULL(".self::COLUM_SPONSOR_NAME_LANG_PREFIX.Locale::getLang().", ".self::COLUM_SPONSOR_NAME_LANG_PREFIX.Locale::getDefaultLang().")",
//											 self::COLUM_SPONSOR_URLKEY, self::COLUM_SPONSOR_LOGO_IMAGE, self::COLUM_SPONSOR_URL))
//											 ->where(self::COLUM_SPONSOR_ID_ITEM." = ".$this->getModule()->getId())
//											 ->where(self::COLUM_SPONSOR_DELETED." = ".(int)false)
//											 ->order('RAND()')
//											 ->limit(0,self::NUMBER_OF_SPONSORS);
//
//
//		$this->sponsorsArray = $this->getDb()->fetchAssoc($sqlSelect);
//
//		$this->sponsorsLink = $this->getLink();
//		$this->dirToImages = $this->getModule()->getDir()->getDataDir();
		
	}
	
	public function panelView() {
		$this->template()->addTpl("panel.tpl");
		
		$this->template()->addVar("SPONSORS_ARRAY", $this->sponsorsArray);
		$this->template()->addVar("SPONSORS_LINK", $this->sponsorsLink);
		$this->template()->addVar("SPONSORS_LINK_NAME", _("Další sponzoři"));
		$this->template()->addVar('DIR_TO_IMAGES', $this->dirToImages);
		
	}
	
	
}
?>