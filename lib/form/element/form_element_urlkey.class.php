<?php
/**
 * Třída pro obsluhu INPUT prvku typu TEXT
 * Třída implementující objekt pro obsluhu INPUT prvku typu TEXT pro generování 
 * url klíčů.
 *
 *
 * @copyright  	Copyright (c) 2012 Jakub Matas
 * @version    	$Id: $ VVE 7.7 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract      Třída pro obsluhu formulářového prvku typu Input-Text pro generování url klíčů
 */
class Form_Element_UrlKey extends Form_Element_Text {

   protected $checkUrl = null;
   
   protected $nameElement = null;
   
   protected $checkParams = array();

   protected $autoUpdate = true;

   /**
    *
    * @var Form_Element
    */
   protected $createFromElement = null;

   public function __construct($name, $label = null, $prefix = null){
      parent::__construct($name, $label, $prefix);
      // je třeba rovnou přidat filtr
      $this->addFilter(new Form_Filter_UrlKey());
   }

/**
 * Metoda vrací prvek (html element podle typu elementu - input, textarea, ...)
 * @return string
 */
   public function controll() {
      $this->html()->setAttrib('type', 'text');
      $this->createValidationLabels();
      $this->html()->clearContent();
      if(!$this->isValid AND $this->isPopulated) {
         $this->html()->addClass(self::$cssClasses['error']);
         if(!self::$elementFocused){ $this->html()->setAttrib('autofocus','autofocus'); self::$elementFocused = true;}
      }
      $values = $this->getUnfilteredValues();
      $this->html()->addClass($this->getName()."_class");
      
      $updateButton = null;
      if(!$this->autoUpdate && $this->checkUrl != null){
         $updateButton = new Html_Element('a', $this->tr('Aktualizovat'));
         $updateButton->addClass('button_update_urlkey_for_'.$this->getName())->setAttrib('href', 'javascript:void(0)');
      }
      
      if($this->isMultiLang()) {
         $cnt = null;
         $first = true;
         foreach ($this->getLangs() as $langKey => $langLabel) {
            $container = new Html_Element('p');
            $this->html()->setAttrib('name', $this->getName().'['.$langKey.']');
            $this->html()->setAttrib('id', $this->getName().'_'.$this->renderedId.'_'.$langKey);
            $this->html()->setAttrib('value', htmlspecialchars($values[$langKey]));
            $container->setAttrib('id', $this->getName().'_container_'.$langKey);
            $this->html()->setAttrib('lang', $langKey);
            $container->addClass(self::$cssClasses['elemContainer']);
            $container->setAttrib('lang', $langKey);
            $cnt .= $container->setContent($this->html().$updateButton);
         }
         
         return $cnt;
      } else {
         $this->html()->setAttrib('name', $this->getName());
         $this->html()->setAttrib('id', $this->getName().'_'.$this->renderedId);
         $this->html()->setAttrib('lang', Locales::getDefaultLang());
         $this->html()->setAttrib('value', htmlspecialchars((string)$values));
         $this->renderedId++;
         
         return $this->html().$updateButton;
      }
   }
   
   public function setCheckingUrl($url)
   {
      $this->checkUrl = $url;
      return $this;
   }
   
   /**
    * Metoda přidá paramter s exclude parametrem
    * @param string $name
    * @param mixed $value
    * @return \Form_Element_UrlKey 
    */
   public function setCheckParam($name, $value)
   {
      $this->checkParams[$name] = $value;
      return $this;
   }

   /**
    * Metoda nestaví jak se má provádět kontrola urlklíče (pouze pokud je url adresa pro kontrolu)
    * @param bool $update -- true pro zapnutí
    * @return \Form_Element_UrlKey 
    */
   public function setAutoUpdate($update)
   {
      $this->autoUpdate = $update;
      return $this;
   }
   
   
   public function setUpdateFromElement(Form_Element $element)
   {
      $this->createFromElement = $element;
      return $this;
   }

   public function scripts() {
      if($this->checkUrl == null) {return null;}
      
      $script = '$(document).ready(function(){';
      if($this->autoUpdate){
         if($this->createFromElement != null){
         
         }
         $script .= '
            $(".'.$this->getName()."_class".'").change(function(){
               var $elem = $(this); $elem.val( str2url($elem.val()) );
               vveCheckUrlKey("'.$this->checkUrl.'", $elem, function(urlkey){ $elem.val(urlkey)},'.json_encode($this->checkParams).' );
            });
            ';   
      } else {
         $script .= '
            $(".button_update_urlkey_for_'.$this->getName().'").click(function(){
               var $elem = $(this).prev("input"); ';
         
         if($this->createFromElement != null){
            $script .= '
               var $updateFrom = $elem.parents("form").find(\'input.'.$this->createFromElement->getName().'_class[lang="\'+$elem.attr("lang")+\'"]\');
               if($updateFrom.length == 0){ // not lang attribute ??
                  $updateFrom = $elem.parents("form").find(\'input.'.$this->createFromElement->getName().'_class\');
               }
               $elem.val( str2url($updateFrom.val() ) ); 
            ';
         }

         $script .= '   
               vveCheckUrlKey("'.$this->checkUrl.'", $elem, function(urlkey){ $elem.val(urlkey)},'.json_encode($this->checkParams).' );
            });
            ';   
      }
   
      return $script.'});';
   }
   
}
?>
