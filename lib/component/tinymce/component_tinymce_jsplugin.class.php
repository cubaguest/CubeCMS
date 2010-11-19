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
      $this->addFile(new JsPlugin_JsFile("tiny_mce.js"));
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
}
?>