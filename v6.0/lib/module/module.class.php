<?php
/**
 * Třída pro obsluhu vlastností mmodulu
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.4 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída pro obsluhu vlastností modulu
 */

 class Module {
    private $name = null;
    private $params = null;
    private $dataDir = null;


    public function  __construct($name, $params) {
       $this->name = $name;
       $this->params = $params;
       $this->dataDir = $name;
    }

    /**
     * Metoda vrací název modulu
     * @return string
     */
    public function getName() {
       return $this->name;
    }

    /**
     * Metoda nastaví název datového adresáře
     * @return string
     */
    public function setDataDir($name) {
       $this->dataDir = $name;
    }

    /**
    * Metoda vrací požadovaný parametr
    * @param string $param -- index parametru
    * @param mixed $defaultParam -- výchozí hodnota
    * @return string -- parametr
    */
   public function getParam($param, $defaultParam = null) {
      if(isset($this->params[$param])){
         return $this->params[$param];
      } else {
         return $defaultParam;
      }
   }

   /**
    * Metoda datový vrací adresář modulu
    * @return string
    * @todo -- ověřit vytváření adresáře
    */
   public function getDataDir($webAddres = false) {
      if($webAddres){
         return Url_Request::getBaseWebDir().VVE_DATA_DIR.URL_SEPARATOR.$this->dataDir.URL_SEPARATOR;
      } else {
         $dir = new Filesystem_Dir(AppCore::getAppWebDir().VVE_DATA_DIR.DIRECTORY_SEPARATOR.$this->dataDir.DIRECTORY_SEPARATOR);
         //$dir->checkDir(); -- TODO
         return (string)$dir;
      }
   }
 }
?>
