<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of menu_admin_item
 *
 * @author cuba
 */
class Menu_Admin_Item {
   
   public static $lastId = 32789;

   protected $name;
   
   protected $urlkey = "module";
   
   protected $id;
   
   protected $icon = null;
   
   protected $module = "text";
   
   protected $type = "main";
   
   protected $params = array();
   
   protected $datadir = 'admin';

   /**
    * 
    * @param int $id -- id (vyšší než id kategorie)
    * @param array $name -- název
    * @param string $urlkey -- url klíč
    * @param string $module -- název modulu
    * @param string $icon -- ikona
    */
   public function __construct($id, $name, $urlkey, $module, $icon = null)
   {
      
      $this->id = $id;
      if($id >= self::$lastId){
         self::$lastId = $id+1;
      }
      $this->name = $name;
      $this->urlkey = $urlkey;
      $this->module = $module;
      $this->icon = $icon;
   }
   
   public static function getLastID()
   {
      return self::$lastId;
   }


   public function setIcon($icon)
   {
      $this->icon = $icon;
      return $this;
   }
   
   public function setParams($params)
   {
      $this->params = serialize($params);
      return $this;
   }
   
   public function getName()
   {
      if(isset($this->name[Locales::getUserLang()])){
         return $this->name[Locales::getUserLang()];
      }
      if(isset($this->name[Locales::getLang()])){
         return $this->name[Locales::getLang()];
      }
      if(isset($this->name[Locales::getDefaultLang()])){
         return $this->name[Locales::getDefaultLang()];
      }
      return $this->name['cs'];
   }
   
   public function getId()
   {
      return $this->id;
   }
   
   public function getUrlKey()
   {
      return 'admin/'.$this->urlkey;
   }
   
   public function getUrl()
   {
      return Url_Link::getMainWebDir().Locales::getUserLang().'/'.$this->getUrlKey()."/";
   }
   
   public function getIcon()
   {
      return $this->icon;
   }
   
   public function getType()
   {
      return $this->type;
   }
   
   public function getModule()
   {
      return $this->module;
   }
   
   public function getParams()
   {
      return $this->params;
   }
   
   public function getDataDir()
   {
      return $this->datadir;
   }
}
