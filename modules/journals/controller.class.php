<?php

class Journals_Controller extends Controller {
   const DATA_DIR = 'journals';

   protected function init()
   {
      $this->module()->setDataDir(self::DATA_DIR);
   }

   /**
    * Kontroler pro zobrazení novinek
    */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();
      
      /* SQL query for get all with conact labels
      SELECT `t_jounals`.`id_journal`, `t_jounals`.`number`, `t_jounals`.`year`, `t_jounals`.`text`, `t_jounals`.`file`,
      GROUP_CONCAT(t_jounals_l.label) AS labels
      FROM `roznovskyprosto`.`rp_journals` AS t_jounals  
      LEFT JOIN rp_journals_labels AS t_jounals_l ON  `t_jounals`.`id_journal` = t_jounals_l.`id_journal`
      GROUP BY `t_jounals`.`id_journal`
      ORDER BY `t_jounals`.`year` DESC,`t_jounals`.`number` DESC
      */
      $this->view()->yearCurrent = intval($this->getRequest('year', 0));
      
      if($this->view()->yearCurrent == 0){
         // select only last year
         $model = new Journals_Model();
         $maxRec = $model
            ->columns(array('maxyear' => 'MAX('.Journals_Model::COLUMN_YEAR.')'))
            ->record(null, null, PDO::FETCH_OBJ);
         $this->view()->yearCurrent = (int)$maxRec->maxyear;
      }
      // načtení záznamů
      $journals = $this->getJournals($this->view()->yearCurrent);
      
      // načtení ročníků
      $model = new Journals_Model();
      $yearsRecs = $model
         ->columns(array(Journals_Model::COLUMN_YEAR, 'count' => 'COUNT('.Journals_Model::COLUMN_ID.')'))
         ->groupBy(array(Journals_Model::COLUMN_YEAR))
         ->order(array(Journals_Model::COLUMN_YEAR => Model_ORM::ORDER_DESC))
         ->records();
      
      foreach ($yearsRecs as $year) {
         $years[$year->{Journals_Model::COLUMN_YEAR}] = $year->count;
      }
      
      /* Assign to template */
      $this->view()->years = $years;
      $this->view()->journals = $journals;
      $this->view()->dir = $this->module()->getDataDir(true);
   }

   protected function getJournals($year = 0)
   {
      $model = new Journals_Model();
      $journals =  $model
         ->join(Journals_Model::COLUMN_ID, array("t_jounals_l" => "Journals_Model_Labels"), Journals_Model_Labels::COLUMN_ID_JOURNAL, 
            array('labels' => "GROUP_CONCAT(t_jounals_l.".Journals_Model_Labels::COLUMN_LABEL." SEPARATOR ' / ')") )
         ->order(array(Journals_Model::COLUMN_YEAR => Journals_Model::ORDER_DESC,Journals_Model::COLUMN_NUMBER => Journals_Model::ORDER_DESC,))
         ->groupBy(array(Journals_Model::COLUMN_ID))
         ->where(Journals_Model::COLUMN_YEAR." = :y", array('y' => $year))
         ->records();
      
      return $journals;
   }


   public function showLastController()
   {
      $this->checkReadableRights();
      
      $model = new Journals_Model();
      
      $journal = $model->order(array(
         Journals_Model::COLUMN_YEAR => Journals_Model::ORDER_DESC,
         Journals_Model::COLUMN_NUMBER => Journals_Model::ORDER_DESC,
         ))->record();

      if($journal == false){
         return false;
      }
      
      $this->checkDeleteJournal($journal);
      
      $this->view()->journal = $journal;
      $this->view()->dir = $this->module()->getDataDir(true);
      $this->view()->linkBack = $this->link()->route();
   }
   
   public function showController()
   {
      $this->checkReadableRights();
      
      $model = new Journals_Model();
      
      $journal = $model->where(
         Journals_Model::COLUMN_YEAR .' = :y AND '.Journals_Model::COLUMN_NUMBER.' = :n',
         array('y' => $this->getRequest('year', 1),'n' => $this->getRequest('number', 1)))->record();

      if($journal == false){
         return false;
      }
      
      $this->checkDeleteJournal($journal);
      
      // načtení labelů
      $modelLabels = new Journals_Model_Labels();
      $labels = $modelLabels->where(Journals_Model_Labels::COLUMN_ID_JOURNAL.' = :idj AND '.Journals_Model_Labels::COLUMN_PAGE." IS NOT NULL",
         array('idj' => $journal->{Journals_Model::COLUMN_ID} ))->records();
      
      // update viewed
      if(!$this->category()->getRights()->isWritable()){
         $journal->{Journals_Model::COLUMN_VIEWED} = $journal->{Journals_Model::COLUMN_VIEWED}+1;
         $model->save($journal);
      }   
      
      $this->view()->journal = $journal;
      $this->view()->labels = $labels;
      
      $this->view()->dir = $this->module()->getDataDir(true);
      $this->view()->linkBack = $this->link()->route('showYear');
   }
   
   private function checkDeleteJournal(Model_ORM_Record $journal)
   {
      if($this->category()->getRights()->isWritable()){
         $formDel = new Form('journal_del_');
//         $eId = new Form_Element_Hidden('id');
//         $eId->setValues($id);
//         $formDel->addElement($eId);
         
         $eSend = new Form_Element_Submit('delete', $this->tr('Smazat deník'));
         $formDel->addElement($eSend);
         
         if($formDel->isValid()){
            $model = new Journals_Model();
            $model->delete($journal->{Journals_Model::COLUMN_ID});
            $this->deleteSupportFiles($journal->{Journals_Model::COLUMN_FILE});
            
            $this->infoMsg()->addMessage($this->tr('Deník byl smazán'));
            $this->link()->route()->reload();
         }
         $this->view()->formDelete = $formDel;
      }
   }

   /**
    * Kontroler pro přidání novinky
    */
   public function addController() {
      $this->checkWritebleRights();
      $form = $this->createForm();

      if($form->isSend() && $form->save->getValues() == false){
         $this->link()->route()->reload();
      }
      
      if ($form->isValid()) {
         
         $fileName = $form->number->getValues().'-'.$form->year->getValues().'.pdf';
         $file = $form->file->createFileObject('Filesystem_File');
//         $file = new Filesystem_File();
         $file->move($this->module()->getDataDir(), $fileName);

         $this->createSupportFiles($fileName);
         
         // dump textu z pdf
         $fulltext = null;
         $txtFile = $this->module()->getDataDir().$fileName.'.txt';
         if(is_file($txtFile)){
            $fulltext = file_get_contents($txtFile);
         }
         
         $model = new Journals_Model();
         
         $journal = $model->newRecord();
         $journal->{Journals_Model::COLUMN_NUMBER} = $form->number->getValues();
         $journal->{Journals_Model::COLUMN_YEAR} = $form->year->getValues();
         $journal->{Journals_Model::COLUMN_FILE} = $fileName;
         $journal->{Journals_Model::COLUMN_TEXT} = preg_replace('/\s{3,}/','',$fulltext);
         
         $id = $model->save($journal);
         
         $this->saveLabels($form->label->getValues(), $form->label_page->getValues(), $id);
         
         $this->infoMsg()->addMessage($this->_('Deníky byl uložen'));
         $this->link()->route()->reload();
      }
      if($form->isSend()){
         $this->view()->labels = $form->label->getValues();
         $this->view()->labels_pages = $form->label_page->getValues();
      } else {
         $this->view()->labels = array(null);
         $this->view()->labels_pages = array(null);
      }
      $this->view()->form = $form;
   }
   
   private function saveLabels($labels, $pages, $id)
   {
      $modelLabels = new Journals_Model_Labels();
      // remove old labels
      $modelLabels->where(Journals_Model_Labels::COLUMN_ID_JOURNAL.' = :idj', array('idj' => $id))->delete();
      // save nev labels
      foreach ($labels as $key => $label) {
         if($label != null && $pages[$key] != null){
            $record = $modelLabels->newRecord();
            $record->{Journals_Model_Labels::COLUMN_ID_JOURNAL} = $id;
            $record->{Journals_Model_Labels::COLUMN_LABEL} = $label;
            $record->{Journals_Model_Labels::COLUMN_PAGE} = $pages[$key];
            $modelLabels->save($record);
         }
      }
   }

   private function createSupportFiles($filename)
   {
      $logFile = AppCore::getAppWebDir().'logs'.DIRECTORY_SEPARATOR.'journal.log';
      $dir = $this->module()->getDataDir();
      // create swf file
      $out = array();
      if(exec('pdf2swf '.escapeshellarg($dir.$filename).' -o '.escapeshellarg($dir.$filename.'.swf').' -f -T 9 -t -s storeallcharacters', $out) === false){
         throw new BadFunctionCallException($this->tr('Systém nemá instalován konvertor pdf > swf. Je instalováno swftools?'));
      }
      file_put_contents($logFile, "Createng swf\n"
         . "cmd: " .'pdf2swf '.escapeshellarg($dir.$filename).' -o '.escapeshellarg($dir.$filename.'.swf').' -f -T 9 -t -s storeallcharacters'."\n" 
         . implode("\n", $out)."\n");
      $out = array();
      // create txt file
      if(exec('pdftotext '.escapeshellarg($dir.$filename).' '.escapeshellarg($dir.$filename.'.txt'), $out) === false){
         throw new BadFunctionCallException($this->tr('Systém nemá instalován konvertor pdf > text. Je instalován poppler?'));
      }
      file_put_contents($logFile, "Createng txt\n"
         . "cmd: " . 'pdftotext '.escapeshellarg($dir.$filename).' '.escapeshellarg($dir.$filename.'.txt') ."\n" .implode("\n", $out)."\n", FILE_APPEND);
      $out = array();
      // create preview
      if(exec('convert "'.$dir.$filename.'[0]" -thumbnail 168x116^ -gravity north -extent 168x116 -quality 80 '.escapeshellarg($dir.$filename.'.jpg'), $out) === false){
         throw new BadFunctionCallException($this->tr('Systém nemá instalován konvertor pdf > jpeg. Je instalován ImageMagic?'));
      }
      file_put_contents($logFile, "Createng txt\n"
         . "cmd: " . 'convert "'.$dir.$filename.'[0]" -thumbnail 168x116^ -gravity north -extent 168x116 -quality 80 '.escapeshellarg($dir.$filename.'.jpg')
         . implode("\n", $out)."\n", FILE_APPEND);
   }

   private function deleteSupportFiles($filename)
   {
      // delete pdf file
      if(@unlink($this->module()->getDataDir().$filename) == false){
         $this->errMsg()->addMessage(sprintf($this->tr('Nepodařilo se smazat "%s" soubor deníku. Zůstává uložen.'),$filename), true);
      }
      // delete swf file
      if(@unlink($this->module()->getDataDir().$filename.'.swf') == false){
         $this->errMsg()->addMessage(sprintf($this->tr('Nepodařilo se smazat "%s" soubor deníku. Zůstává uložen.'),$filename.'.swf'), true);
      }
      // delete txt file
      if(@unlink($this->module()->getDataDir().$filename.'.txt') == false){
         $this->errMsg()->addMessage(sprintf($this->tr('Nepodařilo se smazat "%s" soubor deníku. Zůstává uložen.'),$filename.'.txt'), true);
      }
      // delete preview
      if(@unlink($this->module()->getDataDir().$filename.'.jpg') == false){
         $this->errMsg()->addMessage(sprintf($this->tr('Nepodařilo se smazat "%s" soubor deníku. Zůstává uložen.'),$filename.'.jpg'), true);
      }
   }

   /**
    * controller pro úpravu novinky
    */
   public function editController() {
      $this->checkWritebleRights();
      $model = new Journals_Model();
      $form = $this->createForm();
      
      $journal = $model->where(Journals_Model::COLUMN_NUMBER.' = :n AND '.Journals_Model::COLUMN_YEAR.' = :y',
         array('y' => $this->getRequest('year', 0), 'n' => $this->getRequest('number', 0) ))->record();
      
      if($journal == false){
         return false;
      }
      
      $form->number->setValues($journal->{Journals_Model::COLUMN_NUMBER});
      $form->year->setValues($journal->{Journals_Model::COLUMN_YEAR});
      $form->text->setValues($journal->{Journals_Model::COLUMN_TEXT});
      
      $form->file->removeValidation('Form_Validator_NotEmpty');
      
      if($form->isSend() && $form->save->getValues() == false){
         $this->link()->route('show')->reload();
      }
      
      if ($form->isValid()) {
         
         if($form->file->getValues() != null){
            $fileName = $form->number->getValues().'-'.$form->year->getValues().'.pdf';
            $file = $form->file->createFileObject('Filesystem_File');
   //         $file = new Filesystem_File();
            $file->move($this->module()->getDataDir(), $fileName);

            $this->createSupportFiles($fileName);
         
            // dump textu z pdf
            $fulltext = null;
            $txtFile = $this->module()->getDataDir().$fileName.'.txt';
            if(is_file($txtFile)){
               $fulltext = file_get_contents($txtFile);
            }
            $journal->{Journals_Model::COLUMN_TEXT} = preg_replace('/\s{3,}/','',$fulltext);
            $journal->{Journals_Model::COLUMN_FILE} = $fileName;
         }
         
         $journal->{Journals_Model::COLUMN_NUMBER} = $form->number->getValues();
         $journal->{Journals_Model::COLUMN_YEAR} = $form->year->getValues();
         
         $id = $model->save($journal);
         
         $this->saveLabels($form->label->getValues(), $form->label_page->getValues(), $id);
         
         $this->infoMsg()->addMessage($this->_('Deníky byl uložen'));
         $this->link()->route('show')->reload();
      }
      $labels = array(null);
      $labels_pages = array(null);
      
      if($form->isSend()){
         $labels = $form->label->getValues();
         $labels_pages = $form->label_page->getValues();
      } else {
         $modelLabels = new Journals_Model_Labels();
         $l = $modelLabels->where(Journals_Model_Labels::COLUMN_ID_JOURNAL.' = :idj', array('idj' => $journal->{Journals_Model::COLUMN_ID}))->records();
         if($l != false){
            $labels = array();
            $labels_pages = array();
            foreach ($l as $label) {
               array_push($labels, $label->{Journals_Model_Labels::COLUMN_LABEL});
               array_push($labels_pages, $label->{Journals_Model_Labels::COLUMN_PAGE});
            }
         }
      }
      $this->view()->labels = $labels;
      $this->view()->labels_pages = $labels_pages;
      $this->view()->form = $form;
   }

   /**
    * Metoda  vytvoří element formuláře
    * @return Form
    */
   protected function createForm() {
      $form = new Form('journal_');

      $fGrpBasic = $form->addGroup('basic', $this->_('Základní informace'));
      $fGrpMeta = $form->addGroup('metadata', $this->_('Metadata'));

      $eNumber = new Form_Element_Text('number', $this->_('Číslo'));
      $eNumber->addValidation(New Form_Validator_NotEmpty());
      $eNumber->addValidation(New Form_Validator_IsNumber(null, Form_Validator_IsNumber::TYPE_INT));
      $form->addElement($eNumber, $fGrpBasic);
      
      $eYear = new Form_Element_Text('year', $this->_('Ročník'));
      $eYear->addValidation(New Form_Validator_NotEmpty());
      $eYear->addValidation(New Form_Validator_IsNumber(null, Form_Validator_IsNumber::TYPE_INT));
      $form->addElement($eYear, $fGrpBasic);

      $eFile = new Form_Element_File('file', $this->_('Soubor'));
      $eFile->addValidation(New Form_Validator_NotEmpty());
      $eFile->setUploadDir(AppCore::getAppCacheDir());
      $eFile->addValidation(new Form_Validator_FileExtension('pdf'));
      $form->addElement($eFile, $fGrpBasic);

      $eLabel = new Form_Element_Text('label', $this->_('Nadpis'));
      $eLabel->setDimensional();
      $eLabel->setSubLabel($this->tr('Význačný nadpis ve formátu: strana:nadpis. Př.: 26:Tenhle je nadpis na stránce 26'));
//      $eLabel->addValidation(New Form_Validator_Regexp());
      $form->addElement($eLabel, $fGrpMeta);
      
      $eLabelPage = new Form_Element_Text('label_page', $this->_('Strana'));
      $eLabelPage->setDimensional();
//      $eLabel->addValidation(New Form_Validator_Regexp());
      $form->addElement($eLabelPage, $fGrpMeta);
      
      $eFullText = new Form_Element_TextArea('text', $this->_('Textový obsah'));
      $eFullText->setSubLabel($this->tr('Textový obsah pdf souboru, určený pro fulltext vyhledávání'));
//      $eFullText->addValidation(New Form_Validator_NotEmpty());
      $form->addElement($eFullText, $fGrpMeta);

      $iSubmit = new Form_Element_SaveCancel('save');
      $form->addElement($iSubmit);

      return $form;
   }

   /**
    * Smazání článků při odstranění kategorie
    * @param Category $category
    */
   public static function clearOnRemove(Category $category) {

   }

   /**
    * Metoda pro nastavení modulu
    */
   public static function settingsController(&$settings, Form &$form) {
      $eOnPage = new Form_Element_Text('numOnPage', 'Počet lektorů na stránku');
      $eOnPage->addValidation(new Form_Validator_IsNumber());
      $eOnPage->setSubLabel(sprintf('Výchozí: %s lektorů na stránku', self::DEFAULT_RECORDS_ON_PAGE));
      $form->addElement($eOnPage, 'view');


      $form->addGroup('images', 'Nasatvení obrázků');

      $elemImgW = new Form_Element_Text('imgw', 'Šířka portrétu');
      $elemImgW->setSubLabel('Výchozí: ' . self::DEFAULT_IMAGE_WIDTH . ' px');
      $elemImgW->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemImgW, 'images');

      $elemImgH = new Form_Element_Text('imgh', 'Výška portrétu');
      $elemImgH->setSubLabel('Výchozí: ' . self::DEFAULT_IMAGE_HEIGHT . ' px');
      $elemImgH->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemImgH, 'images');

      $elemCropImage = new Form_Element_Checkbox('cropimg', 'Ořezávat portréty');
      $form->addElement($elemCropImage, 'images');

      if (isset($settings['imgw'])) {
         $form->imgw->setValues($settings['imgw']);
      }
      if (isset($settings['imgh'])) {
         $form->imgh->setValues($settings['imgh']);
      }
      if (isset($settings['cropimg'])) {
         $form->imgh->setValues($settings['cropimg']);
      }

      if (isset($settings['recordsonpage'])) {
         $form->numOnPage->setValues($settings['recordsonpage']);
      }
      // znovu protože mohl být už jednou validován bez těchto hodnot
      if ($form->isValid()) {
         $settings['imgw'] = $form->imgw->getValues();
         $settings['imgh'] = $form->imgh->getValues();
         $settings['cropimg'] = $form->cropimg->getValues();
         $settings['recordsonpage'] = $form->numOnPage->getValues();
      }
   }

}
?>