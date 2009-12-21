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


    public function  __construct($name, $params) {
       $this->name = $name;
       $this->params = $params;
    }

    /**
     * Metoda vrací název modulu
     * @return string
     */
    public function getName() {
       return $this->name;
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
    */
   public function getDataDir($webAddres = false) {
      if($webAddres){
         return Url_Request::getBaseWebDir().VVE_DATA_DIR.URL_SEPARATOR.$this->getName().URL_SEPARATOR;
      } else {
         $dir = new Filesystem_Dir(AppCore::getAppWebDir().VVE_DATA_DIR.DIRECTORY_SEPARATOR.$this->getName().DIRECTORY_SEPARATOR);
         $dir->checkDir();
         return (string)$dir;
      }
   }
 }
?>
