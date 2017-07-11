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
class Menu_Admin_ItemRedirect extends Menu_Admin_Item {
   
   protected $url = null;


   /**
    * 
    * @param int $id -- id (vyšší než id kategorie)
    * @param array $name -- název
    * @param string $urlkey -- url klíč
    * @param string $module -- název modulu
    * @param string $icon -- ikona
    */
   public function __construct($id, $name, $url, $icon = null)
   {
      $this->id = $id;
      if($id >= self::$lastId){
         self::$lastId = $id+1;
      }
      $this->name = $name;
      $this->url = (string)$url;
      $this->icon = $icon;
      $this->datadir = null;
   }
   
   
   public function getUrlKey()
   {
      return 'admin/'.$this->urlkey;
   }
   
   public function getUrl()
   {
      if(strpos($this->url, 'http') === false){
         return 'sss'.Url_Link::getMainWebDir().$this->url;
      } else {
         return $this->url;
      }
   }
}
