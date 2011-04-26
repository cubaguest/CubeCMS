<?php
/**
 * Třída JsPluginu komponenty TinyMce.
 * Třída slouží pro vytvoření JsPluginu TinyMce, což je wysiwing textový edtor,
 * který se navazuje na textarea v šabloně. Třída umožňuje jednoduché nastavení
 * parametrů editor (volba vzhledu, jazyka, atd).
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: jsplugin_tinymce.class.php 1662 2010-10-19 06:00:16Z jakub $ VVE3.9.4 $Revision: 1662 $
 * @author        $Author: jakub $ $Date: 2010-10-19 08:00:16 +0200 (Út, 19 říj 2010) $
 *                $LastChangedBy: jakub $ $LastChangedDate: 2010-10-19 08:00:16 +0200 (Út, 19 říj 2010) $
 * @abstract 		Třída JsPluginu komponenty TinyMce
 */

class Component_TinyMCE_JsPlugin extends JsPlugin {

   private $settingObj = null;

   private $settingsFile = null;

   protected function initJsPlugin() {
      $this->setJsFilesDir('tinymce');
      $this->setSettingFile(new JsPlugin_JsFile("settings.js", true));
   }

   protected function setFiles() {
//       if(VVE_DEBUG_LEVEL == 0){
         $this->addFile(new JsPlugin_JsFile("tiny_mce.js"));
//       } else {
//          $this->addFile(new JsPlugin_JsFile("tiny_mce_src.js"));
//       }
      $this->getSettingFile()->setParams($this->getSettings()->getParamsForUrl());
      $this->addFile($this->getSettingFile());
   }

   /**
    * Metoda vrací objekt se souborem s nastavením editoru
    * @return JsPlugin_JsFile
    */
   public function getSettingFile() {
      return $this->settingsFile;
   }

   /**
    * Metoda nasatví objekt se souborem s nastavením editoru
    * @return JsPlugin_JsFile
    */
   public function setSettingFile(JsPlugin_JsFile $file) {
      $this->settingsFile = $file;
   }

   /**
    * Metoda nastaví která konfigurace se přidá
    * @param Component_TinyMCE_Settings $settings
    */
   public function setSettings(Component_TinyMCE_Settings $settings) {
      $this->settingObj = $settings;
   }

   /**
    * Metoda vrací aktuální nastavení editoru
    * @return Component_TinyMCE_Settings a jeho potomky
    */
   public function getSettings() {
      if(($this->settingObj instanceof Component_TinyMCE_Settings) != true){
         $this->settingObj = new Component_TinyMCE_Settings_Simple();
      }
      return $this->settingObj;
   }

   public function settingsView(){
      if(!isset ($_GET['set'])){
         throw new UnexpectedValueException(_('Nedefinovaný typ nastavení pro TinyMCE'));
      }
      $className = 'Component_TinyMCE_Settings_'.ucfirst($_GET['set']);
      $setObj = new $className();
      echo $setObj;
   }

   /**
    * Metoda pro vrácení seznamu systémových šablon
    */
   public function tplsSystemView(){
      $list = new Component_TinyMCE_TPLList_System();
      echo $list;
   }

   /**
    * Metoda pro vrácení seznamu systémových mailových šablon
    */
   public function tplsSystemMailView(){
      $list = new Component_TinyMCE_TPLList_SystemMail();
      echo $list;
   }
   
   /**
    * Metoda pro vrácení seznamu externích odkazů
    */
   public function listView()
   {
      $type = isset($_GET['type']) ? (int)$_GET['type'] : Component_TinyMCE::LINK_LIST_ALL;
      $listType = isset($_GET['listtype']) ? $_GET['listtype'] : Component_TinyMCE_List::LIST_TYPE_LINK;
      
      $items = array();
      
      if(($type-Component_TinyMCE::LINK_LIST_MEDIA) >= 0) {
         $type = $type-Component_TinyMCE::LINK_LIST_MEDIA;
         $images = new Component_TinyMCE_List_Medias();
         $items = array_merge($items, $images->getItems());
      }
      
      if(($type-Component_TinyMCE::LINK_LIST_IMAGES) >= 0) {
         $type = $type-Component_TinyMCE::LINK_LIST_IMAGES;

         $images = new Component_TinyMCE_List_Images();
         $items = array_merge($items, $images->getItems());
      }
      
      if(($type-Component_TinyMCE::LINK_LIST_FILES) >= 0) {
         $type = $type-Component_TinyMCE::LINK_LIST_FILES;
         
         $files = new Component_TinyMCE_List_Files();
         $items = array_merge($items, $files->getItems());
      }
      
      if(($type-Component_TinyMCE::LINK_LIST_CATEGORIES) >= 0) {
         $type = $type-Component_TinyMCE::LINK_LIST_CATEGORIES;
         
         $cats = new Component_TinyMCE_List_Categories();
         $items = array_merge($items, $cats->getItems());
      }
      
//      echo $type;
//      $list = new Component_TinyMCE_LinkList_Files();
      echo Component_TinyMCE_List::tinyMceString($items, $listType);
   }
}
?>