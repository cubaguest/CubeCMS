<?php

/**
 * Třída pro obsluhu INPUT prvku typu TEXT
 * Třída implementující objekt pro obsluhu INPUT prvkuu typu TEXT. Umožňuje kontrolu
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
class Form_Element_DateTime extends Form_Element_Text {

   /**
    *
    * @var string
    */
   protected $elemFormat = 'Y-m-d';

   protected $showDate = true;
   
   protected $showTime = true;
   
   protected $minDate = null;
   
   protected $maxDate = null;

   public function __construct($name, $label = null, $prefix = null)
   {
      parent::__construct($name, $label, $prefix);
      $this->setFormat();
      $this->addValidation(new Form_Validator_Date());
      $this->addFilter(new Form_Filter_DateTimeObj());
   }

   public function setShowDate($show = true)
   {
      $this->showDate = $show;
      return $this;
   }
   
   public function setShowTime($show = true)
   {
      $this->showTime = $show;
      return $this;
   }
   
   public function setMinDate(DateTime $date = null)
   {
      $this->minDate = $date;
      if($date != null){
         $this->addValidation(new Form_Validator_DateMin($date));
      } else {
         $this->removeValidation(Form_Validator_DateMin::class);
      }
      return $this;
   }
   
   public function setMaxDate(DateTime $date = null)
   {
      $this->maxDate = $date;
      if($date != null){
         $this->addValidation(new Form_Validator_DateMax($date));
      } else {
         $this->removeValidation(Form_Validator_DateMax::class);
      }
      return $this;
   }
   
   
   protected function setFormat($format = null)
   {
      if($format == null){
         $formatter = new IntlDateFormatter(Locales::getLangLocale(), IntlDateFormatter::SHORT, IntlDateFormatter::SHORT);
         $this->elemFormat = $formatter->getPattern();
//         var_dump($formatter->getPattern());die;
      } else {
         $this->elemFormat = $format;
      }
   }

   public function control($renderKey = null)
   {
      $values = $this->getUnfilteredValues();
      $format = null;
      if($this->showDate && $this->showTime){
         $format = '%x %X';
      } else if($this->showDate && !$this->showTime){
         $format = '%x';
      } else if(!$this->showDate && $this->showTime) {
         $format = '%X';
      }
      if($values instanceof DateTime){
         $values = Utils_DateTime::fdate($format, $values);
      }
      $this->unfilteredValues = $values;
      return parent::control($renderKey);
   }

   public function scripts($renderKey = null)
   {
      $jsPicker = new JsPlugin_BootstrapDatepicker();
      $jsPicker->setCfgParam('includecss', true);
      Template::addJsPlugin($jsPicker);
      $scripts = parent::scripts($renderKey);
      
      $opts = JsPlugin_BootstrapDatepicker::getBaseJSOptions();
      $opts['format'] = false;
      if($this->showTime){
         $opts['format'] = 'LT';
      }
      if($this->showDate){
         $opts['format'] = 'L';
      }
      if($this->showDate && $this->showTime){
         $opts['format'] = 'L LT';
      }
      
      if($this->minDate != null){
         if(!$this->minDate instanceof DateTime){
            $this->minDate = new DateTime($this->minDate);
         }
         $opts['minDate'] = $this->minDate->format('Y-m-d H:i:s');
      }
      
      if($this->maxDate != null){
         if(!$this->maxDate instanceof DateTime){
            $this->maxDate = new DateTime($this->maxDate);
         }
         $opts['maxDate'] = $this->maxDate->format('Y-m-d H:i:s');
      }
      
      $scripts .= ' $(\'input[name="'.$this->getName(true).'"]\').datetimepicker('. json_encode($opts).');';
      return $scripts;
   }
}
