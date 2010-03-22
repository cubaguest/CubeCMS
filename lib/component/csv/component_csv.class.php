<?php
/**
 * Třída pro generování csv exportů (např tabulek)
 * Třída slouží pro generování csv dat
 *
 * @copyright  	Copyright (c) 2010 Jakub Matas
 * @version    	$Id: $ VVE 6.0.1 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro generování csv exportu
 */

class Component_CSV extends Component {
   /**
    * Pole s prvky
    * @var array
    */
   private $data = array();

   /**
    * Pole s popisky sloupců
    * @var array
    */
   private $labels = array();

   /**
    * Pole s konfiguračními hodnotami
    * @var array
    */
   protected $config = array('separator' => ';');

   /**
    * Metoda přidá řádek do  tabulky
    * @param array $row -- pole s buňkami
    */
   public function addRow($row) {
      if(!is_array($row)) $row = array($row);
      array_push($this->data, $row);
   }

   /**
    * Metoda nasatví názvy sloupců
    * @param array $labels -- pole s popisky
    */
   public function setCellLabels($labels) {
      $this->labels = $labels;
   }

   /**
    * Metoda nasatví data (dvourozměrné pole)
    * @param array $data -- pole s  daty
    */
   public function setData($data) {
      $this->data = $data;
   }

   /**
    * Vygeneruje a odešle výstup a ukončí script
    */
   public function flush() {
      $out = new Template_Output();
      $out->sendHeaders();
      echo($this->createstring());
      flush();
      exit();
   }

   /**
    * Metoda vytvoří cvs řetězec
    * @return XMLWriter
    */
   private function createstring() {
      $return = null;
      foreach ($this->data as $row) {
         $cellStr = null;
         foreach ($row as $cell) {
            // pokud obsahuje oddělovač dá se do uvozovek
            if(strpos($cell, $needle) !== false){
               $cellStr .= '"'.$cell.'"'.$this->getConfig('separator');
            } else {
               $cellStr .= $cell.$this->getConfig('separator');
            }
         }
         $return .= substr($cellStr,0,strlen($cellStr)-1)."\n";
      };

      return $return;
   }

   /**
    * Metoda pro výpis komponenty
    */
   public function mainView() {}
}
?>
