<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 */

class DataTables_Controller extends Controller {
   const PARAM_COLS = 'cols';
   const PARAM_COLS_DEFAULT = 2;
   const PARAM_COLS_DELIMITER = ';';


   /**
 * Kontroler pro zobrazení textu
 */
   public function mainController() {
//       		Kontrola práv
      $this->checkReadableRights();
      
      $this->view()->rows = $this->loadData(Locales::getLang());
      $this->view()->colls = $colls = $this->category()->getParam(self::PARAM_COLS, self::PARAM_COLS_DEFAULT);
   }
   
   public function editController() {
      $this->checkWritebleRights();
      $form = new Form('table_');
      
      $this->view()->colls = $colls = $this->category()->getParam(self::PARAM_COLS, self::PARAM_COLS_DEFAULT);
      
      $eHeaderColl = new Form_Element_Checkbox('header', $this->tr('Je hlavička'));
      $eHeaderColl->setDimensional();
      $form->addElement(clone $eHeaderColl);
      
      for ($i = 1; $i <= $colls; $i++) {
         $eColl = new Form_Element_TextArea('coll_'.$i, $this->tr('Sloupec'));
         $eColl->setDimensional();
         $form->addElement(clone $eColl);
      }
      
      $eClose = new Form_Element_Checkbox('close', $this->tr('Zavřít po uložení'));
      $eClose->setValues(true);
      $form->addElement($eClose);
      
      $eSubmit = new Form_Element_SaveCancel('save');
      $form->addElement($eSubmit);

      if($form->isSend() && $form->save->getValues() == false){
         $this->link()->route()->rmParam()->reload();
      }
      
      if($form->isValid()){
         $rows = count($form->coll_1->getValues())-1; // -1 protože poslední je key
         $headers = $form->header->getValues();
         $vals = array();
         foreach ($form->coll_1->getValues() as $key => $coll1row) {
            if($key === '{KEY}') continue;
            $vals[$key] = array('header' => isset($headers[$key])? $headers[$key] : false);
            for ($c = 1; $c <= $colls; $c++) {
               $collArr= $form->{'coll_'.$c}->getValues();
               $vals[$key][$c-1] = $collArr[$key];
            }
         }
         $vals = array_filter($vals, array($this, 'removeEmptyRows'));
         $this->saveData($vals, $this->getRequestParam('lang', Locales::getLang()));
         $this->infoMsg()->addMessage($this->tr('Data byla uložena'));
         if($form->close->getValues() == true){
            $this->link()->route()->rmParam()->reload();
         } else {
            $this->link()->reload();
         }
      }
      
      $this->view()->rows = $this->loadData($this->getRequestParam('lang', Locales::getLang()));
      $this->view()->form = $form;
   }
   
   /**
    * Metoda odebere všechny prázdné řádky
    * @param array $item -- řádky v tabulkce
    * @return bool/array -- pole pokud je ok, false pro odstranění
    */
   private function removeEmptyRows($item)
   {
      foreach ($item as $key => $cell) {
         if(is_int($key) AND $cell != "") {
            return $item;
         }
      }
      return false;
   }


   private function saveData($array, $lang)
   {
      $dir = new Filesystem_Dir($this->module()->getDataDir());
      $dir->checkDir();
      
      if(empty ($array)){
         if(is_file($dir.'table_'.$lang.'.xml')){
            unlink($dir.'table_'.$lang.'.xml');
         }
         return;
      }
      
      $cols = $this->category()->getParam(self::PARAM_COLS, self::PARAM_COLS_DEFAULT);
      $writer = new XMLWriter();

      $writer->openMemory();
      $writer->startDocument('1.0', 'UTF-8');
      $writer->setIndent(4);

      $writer->startElement('datatable');
      $writer->writeAttribute('version', '1.0');

      foreach ($array as $row) {
         $writer->startElement('row'); // SOF row
         if($row['header'] === true){
            $writer->writeAttribute('header', 1);
         }
         for($i = 0; $i < $cols; $i++) {
            $writer->startElement('cell'); // SOF cell
            $writer->writeRaw($row[$i]);
            $writer->endElement(); // EOF cell
         }
         $writer->endElement(); // EOF row
      }
      
      $writer->endElement();
      
      file_put_contents($dir.'table_'.$lang.'.xml', $writer->outputMemory());
   }
   
   private function loadData($lang)
   {
      $return = array();
    
      $file = $this->module()->getDataDir().'table_'.$lang.'.xml';
      if(is_file($file)){
         $xml = new SimpleXMLElement(file_get_contents($file));
         // rows
         foreach ($xml as $xmlrow) {
            $newRow = array('header' => (bool)$xmlrow['header'] );
            foreach ($xmlrow->cell as $cell) {
               array_push($newRow, $cell);
            }
            array_push($return, $newRow);
         }
      } else {
         $return = array(array_merge(array('header' => false), array_fill(0, $this->category()->getParam(self::PARAM_COLS, self::PARAM_COLS_DEFAULT), null)));
      }
      return $return;
   }

   public function settings(&$settings, Form &$form) {
      $fGrpParams = $form->addGroup('params', $this->tr('Parametry datového zdroje'));
      
      $eCols = new Form_Element_Text('colls', $this->tr('Počet sloupců'));
      $eCols->addValidation(new Form_Validator_IsNumber(null, Form_Validator_IsNumber::TYPE_INT));
      $eCols->setSubLabel($this->tr(array('Výchozí: %s sloupec', 'Výchozí: %s sloupce', 'Výchozí: %s sloupců'), self::PARAM_COLS_DEFAULT ));
      
      if(isset ($settings[self::PARAM_COLS])){
         $eCols->setValues($settings[self::PARAM_COLS]);
      }
      
      $form->addElement($eCols, $fGrpParams);
      
      // znovu protože mohl být už jednou validován bez těchto hodnot
      if($form->isValid()) {
         $settings[self::PARAM_COLS] = $form->colls->getValues();
      }
   }
}

?>