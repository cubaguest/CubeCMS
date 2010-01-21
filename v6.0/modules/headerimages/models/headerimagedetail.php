<?php
/*
 * Třída modelu s detailem textu
 * 
 */
class HeaderimageDetailModel extends FileModel {
	/**
	 * předpona souboru
	 * @var string
	 */
	const FILE_PREFIX = 'item';

	/**
	 * Pole s povolenými příponami platných souborů hlavičky
	 *
	 * @var array
	 */
	private $imagesExtension = 'jpg';
	
	
	/**
	 * Metoda kontroluje jestli byl obrázek přiřazen
	 * @return boolean -- true pokud obrázek existuje
	 */
	public function isImage() {
		if(file_exists($this->getImageName())){
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Metoda vrací název obrázku
	 *
	 * @param boolean -- true pokud se má vrátit i z cestou (option - true)
	 * @return string -- cesta k obrázku
	 */
	public function getImageName($withDir = true) {
		if($withDir){
			return $this->getModule()->getDir()->getDataDir().self::FILE_PREFIX.$this->getModule()->getId().'.'.$this->imagesExtension;
		} else {
			return self::FILE_PREFIX.$this->getModule()->getId().'.'.$this->imagesExtension;
		}
	}
}

?>