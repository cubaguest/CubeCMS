<?php
/**
 * Třída pro obsluhu souborů.
 * Třída poskytuje základní metody pro práci se soubory,
 * zjišťování mime typu, ukládání do filesystému, kopírování, mazání.
 *
 * @copyright  	Copyright (c) 2009 Jakub Matas
 * @version    	$Id: file.class.php 636 2009-07-07 20:17:18Z jakub $ VVE3.9.4 $Revision: 636 $
 * @author        $Author: jakub $ $Date: 2009-07-07 22:17:18 +0200 (Út, 07 čec 2009) $
 *                $LastChangedBy: jakub $ $LastChangedDate: 2009-07-07 22:17:18 +0200 (Út, 07 čec 2009) $
 * @abstract 		Třída pro obsluhu souborů
 */

class File_Text extends File {
	private static $textMimeTypes = array(
       'txt' => 'text/plain',
       'htm' => 'text/html',
       'html' => 'text/html',
       'php' => 'text/html',
       'css' => 'text/css',
       'js' => 'application/javascript',
       'json' => 'application/json',
       'xml' => 'application/xml'
	);
   
   
	public function isTextFile() {
		if(in_array($this->getMimeType(), self::$textMimeTypes)){
		   return true;
		}
      return false;
	}
	
	/**
	 * Metoda načte obsah souboru
	 * @return string -- obsah souboru
	 */
	public function getData() {
		if($this->exist()){
			return file_get_contents((string)$this);
		}
		return null;
	}

   /**
    * Metoda uloží obsah do souboru
    * @param string $cnt -- obsah
    * @param int $flags -- flag viz. funkce file_put_contents
    */
   public function setData($cnt, $flags = 0){
      $this->getPath()->check();
      file_put_contents((string)$this, $cnt, FILE, $flags);
      return $this;
   }
   
   public function isUTF8()
   {
      return preg_match('//u', $this->getData());
   }
}