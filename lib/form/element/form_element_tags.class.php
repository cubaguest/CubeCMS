<?php
/**
 * Třída pro obsluhu prvku typu TEXTAREA
 * Třída implementující objekt pro obsluhu prvku typu TEXTAREA. Umožňuje kontrolu
 * správnosti zadání,kódování/dekódování obsahu prvku, jazykovou obsluhu a jeho
 * vykreslení i s popisem v šabloně. Při špatném zadání se stará o zvýraznění
 * chyby.
 *
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: $ VVE 6.0 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract      Třída pro obsluhu formulářového prvku typu Input-Text
 */
class Form_Element_Tags extends Form_Element_TextArea {
   const ITEM_DELIMITER = ',';

   protected $autocompleteUrl = false;


   protected function init() {
      $this->htmlElement = new Html_Element('textarea');
      $this->setSubLabel($this->tr('Položky potvrzujete stisknutím klávesy enter.'));
   }
   
   public function setItemsUrl($url)
   {
      $this->autocompleteUrl = $url;
   }
   
   public function setValues($values, $key = null)
   {
      if(!is_array($values)){
         $values = explode(',', $values);
      }
      parent::setValues($values, $key);
   }

      public function populate()
   {
      parent::populate();
      // tagy jsou odděleny čárkou, tak se rozdělí do pole
      if($this->getUnfilteredValues() != null){
         $values = explode(',', $this->getUnfilteredValues());
      }
      $this->unfilteredValues = $values;
      
   }
   
   /**
    * Metoda vrací prvek (html element podle typu elementu - input, textarea, ...)
    * @return string
    */
   public function control($renderKey = null) {
      $rKey = $renderKey != null ? $renderKey : $this->renderedId;
      if(!$this->isValid AND $this->isPopulated) {
         $this->html()->addClass($this->cssClasses['error']);
         if(!self::$elementFocused){ $this->html()->setAttrib('autofocus','autofocus'); self::$elementFocused = true;}
      }

      $values = $this->getUnfilteredValues();
      if(is_array($values)){
         $values = implode(',', $values);
      }
      $this->html()->clearContent(); // vymazání obsahu elementu jinak se duplikuje
      
      $this->html()->addClass($this->getName()."_class");
      $this->html()->setAttrib('name', $this->getName());
      $this->html()->setAttrib('id', $this->getName().'_'.$rKey);
      $this->html()->setAttrib('rows', 1);
//      $this->html()->setAttrib('style', 'width: 400px;');
      $this->html()->setContent($values);
//      $this->renderedId++;
      
      $cnt = $this->html();
         
         
      if($renderKey == null){
         $this->renderedId++;
      }
      return $cnt;
   }
   
   public function scripts($renderKey = null)
   {
      $rKey = $renderKey != null ? $renderKey : $this->renderedId-1;
      Template::addJsPlugin(new JsPlugin_TexText());
      
      $script = '$(\'#'.$this->getName().'_'.$rKey.'\').textext({'
          . 'plugins: "ajax autocomplete suggestion prompt tags arrow",'
          .' ajax : { url : "'.$this->autocompleteUrl.'", dataType : "json", cacheResults : true }, '
          .' prompt: "'.$this->tr('Vložte štítek...').'", '
          .' ext: { core: { onSetFormData : function(e, data){ this.hiddenInput().val(data.join(",")); } } }'
          . '});';
      return $script;
   }
}
