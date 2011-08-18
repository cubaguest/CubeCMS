<?php

/**
 * Třída Form_Data
 * 
 * Datové úložiště pro formulářová data
 *
 * @copyright  	Copyright (c) 2008-2011 Jakub Matas
 * @version    	$Id:  $ VVE 7.3 $Revision: $
 * @author        $Author: $ $Date: $
 * @author        cuba
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída 
 */
class Form_Data extends ArrayObject {
   
   public function __construct($array = array())
   {
      parent::__construct($array);
      parent::setFlags(parent::ARRAY_AS_PROPS);
   }

   public function mapData($data)
   {
      // formulář
      if($data instanceof Form){
         $data->getData($this);
      }
   }
   
   
   /* metody pro render */
   
   /**
    * Metoda vyrenderuje data jako tabulku
    * @param Html_Element $table
    * @param Html_Element $row
    * @param Html_Element $headerCell
    * @param Html_Element $dataCell
    * @return Html_Element 
    */
   public function getHtmlTable($skipEmpty = false, Html_Element $table = null, Html_Element $row = null, Html_Element $headerCell = null, Html_Element $dataCell = null)
   {
      if($table == null){ $table = new Html_Element('table'); }
      if($row == null){ $row = new Html_Element('tr'); }
      if($dataCell == null){ $dataCell = new Html_Element('td');}
      if($headerCell == null){ $headerCell = new Html_Element('th');}
      
      $tr = new Translator();
      
      foreach ($this as $item) {
         $hc = clone $headerCell;
         if($item instanceof Form_Data_Header){
            $hc->setAttrib('colspan', 2)->addClass('header')->setContent($item->getName());
            $row->setContent($hc);
            $table->addContent($row);
         } else if($item instanceof Form_Data_Item) {
            $hc->setContent($item->getName());
            
            $dataValue = $item->getValue();
            if(is_bool($dataValue)){
               $dataValue = $dataValue == true ? $tr->tr('Ano') : $tr->tr('Ne');
            } else {
            }
            $dataCell->setContent($dataValue);
               $row->setContent($hc.$dataCell);
            if($dataValue != null || $skipEmpty == false){
               $table->addContent($row);
            }
         }
      }
      return $table;
   }
   
   public function serialize()
   {
      return serialize((array)$this);
   }
}
?>
