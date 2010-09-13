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
   const CFG_CELL_SEPARATOR = 'cell_separator';
   const CFG_ROW_SEPARATOR = 'row_separator';
   const CFG_FLUSH_FILE = 'file';
   const QUOTE = '"';

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
   protected $config = array(self::CFG_CELL_SEPARATOR => ',',
                             self::CFG_ROW_SEPARATOR => "\r\n",
                             self::CFG_FLUSH_FILE => 'file.csv'
      );

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
      $out->factory('csv');
      $out->setDownload($this->getConfig(self::CFG_FLUSH_FILE));
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
      if(!empty ($this->labels)){
         $labels = $this->labels;
         array_walk($labels, array(&$this, 'applyQuotes'));
         $return .= implode($this->getConfig(self::CFG_CELL_SEPARATOR), $labels).$this->getConfig(self::CFG_ROW_SEPARATOR);
      }
      $data = $this->data;
      array_walk_recursive($data, array(&$this, 'applyQuotes'));
      foreach ($data as $row) {
         $return .= implode($this->getConfig(self::CFG_CELL_SEPARATOR), $row).$this->getConfig(self::CFG_ROW_SEPARATOR);
      };

      return $return;
   }

   private function applyQuotes(&$item, $key) {
      // zdvojení uvozovek
      if(strpos($item, self::QUOTE)){
         $item = str_replace(self::QUOTE, self::QUOTE.self::QUOTE, $item);
      }

      if(ctype_digit($item)){
         $item = $item;
      } else if(!empty ($item)){
         $item = self::QUOTE.$item.self::QUOTE;
      } else {
         $item = null;
      }
   }


   /**
    * Metoda pro výpis komponenty
    */
   public function mainView() {}
}
?>
