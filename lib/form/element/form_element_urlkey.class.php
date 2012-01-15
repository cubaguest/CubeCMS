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
      
      $imgBad = new Html_Element('img');
      $imgBad->setAttrib('src', "images/icons/delete.png")
         ->addClass("image-input-bad")
         ->addClass("image-urlkey-bad");
            
      $imgGood = new Html_Element('img');
      $imgGood->setAttrib('src', "images/icons/accept.png")
         ->addClass("image-input-good")
         ->addClass("image-urlkey-good");
      
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
            $cnt .= $container->setContent($this->html().$imgBad.$imgGood);
         }
         
         return $cnt;
      } else {
         $this->html()->setAttrib('name', $this->getName());
         $this->html()->setAttrib('id', $this->getName().'_'.$this->renderedId);
         $this->html()->setAttrib('lang', Locales::getDefaultLang());
         $this->html()->setAttrib('value', htmlspecialchars((string)$values));
         $this->renderedId++;
         
         return $this->html().$imgBad.$imgGood;
      }
      
      //return parent::controll();
   }
   
   public function setCheckingUrl($url)
   {
      $this->checkUrl = $url;
      return $this;
   }
   
   public function scripts() {
      if($this->checkUrl == null) {return null;}
   
      $script = '
      
      $(".image-urlkey-bad").hide();
      funtion changeStatusImage(isOk, $container ){
         if(isEmpty){
            $(".image-urlkey-bad", $container).hide();
            $(".image-urlkey-good", $container).show();
         } else {
            $(".image-urlkey-good", $container).hide();
            $(".image-urlkey-bad", $container).show();
         }
      }
      
      $(".'.$this->getName()."_class".'").change(function(){
         var $container = $(this).parent();
         $(this).val( str2url($(this).val()) );
         vveIsEmptykUrlKey("'.$this->checkUrl.'", 
            $(this).val(), $(this).attr("lang"), 
            function(isEmpty){
               changeStatusImage(isEmpty, $container );
         });
      });
      ';
   
      return $script;
   }
   
}
?>
