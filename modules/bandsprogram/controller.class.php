<?php
class BandsProgram_Controller extends Controller {
   const CONFIG_FILE = "program.xml";
   const BANDS_MODULE_NAME = 'bands';

   public static $itemTypes = array('day', 'stage', 'band', 'other', 'note', 'space');

   /**
    * Kontroler pro zobrazení programu
    */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();

      $prgFile = new Filesystem_File_Text(self::CONFIG_FILE, $this->category()->getModule()->getDataDir());

      // načtení aktuálního programu
      if($prgFile->exist()){
         $currentProgram = new SimpleXMLElement($prgFile->getContent());
         $this->view()->currentProgram = $currentProgram;

         // urlklíč kategirie
         $modelC = new Model_Category();
         $c = $modelC->getCategoryListByModule(self::BANDS_MODULE_NAME)->fetch();
         $blink = $this->link();
         $blink->clear()->category((string)$c->{Model_Category::COLUMN_URLKEY});

         // pole s odkzay
         $model = new Bands_Model();
         $b = $model->getList();
         $bands = array();
         foreach ($b as $band) {
            $bands[(int)$band->{Bands_Model::COLUMN_ID}] = array(
               'name' => (string)$band->{Bands_Model::COLUMN_NAME},
               'link' => (string)$blink->route('detail', array('urlkey' => $band->{Bands_Model::COLUMN_URLKEY}))
            );
         }

         $this->view()->bands = $bands;
      }
   }

   public function exportProgramController(){
      $this->mainController();
   }

   /**
    * controller pro úpravu programu
    */
   public function editController() {
      $this->checkWritebleRights();

      $prgFile = new Filesystem_File_Text(self::CONFIG_FILE, $this->category()->getModule()->getDataDir());

      // načtení aktuálního programu
      $this->view()->currentProgram = array();
      if($prgFile->exist()){
         $currentProgram = new SimpleXMLElement($prgFile->getContent());
         $this->view()->currentProgram = $currentProgram;
      }

      $form = $this->createForm();

      if($form->isValid()) {

         $times = $form->time->getValues();
         $lenghts = $form->lenght->getValues();
         $texts = $form->text->getValues();
         $textslong = $form->textlong->getValues();
         $bandids = $form->bandid->getValues();
         $types = $form->type->getValues();
         // výstupní pole
         $outputArray = array();
         $curDayKey = $curStageKey = -1;

         // projdeme všechny prvky
         foreach ($types as $key => $type) {
            // reference na cnt
            // podle typu vnitřek
            switch ($type) {
               case "band":
                  array_push($outputArray[$curDayKey]['stages'][$curStageKey]['items'], array(
                     'type' => 'band',
                     'time' => $times[$key],
                     'lenght' => $lenghts[$key],
                     'bandid' => $bandids[$key]
                     ));
                  break;
               case "day":
                  $curDayKey++;
                  // definujeme nové pole se dny
                  $outputArray[$curDayKey] = array('time' => $times[$key],
                     'totime' => $lenghts[$key], 'text' => $texts[$key],
                     'stages' => array());
                  break;
               case "stage":
                  $curStageKey++;
                  $outputArray[$curDayKey]['stages'][$curStageKey] = array(
                     'name' => $texts[$key],
                     'items' => array()
                  );
                  break;
               case "other":
                  array_push($outputArray[$curDayKey]['stages'][$curStageKey]['items'], array(
                     'type' => 'other',
                     'time' => $times[$key],
                     'lenght' => $lenghts[$key],
                     'text' => $texts[$key]
                     ));
                  break;
               default:
                  break;
            }
         }
         // protože se předává i poslední
//         echo "<br><br>";
//         var_dump($outputArray[0]['cnt']);flush();
//         var_dump($outputArray[1]['cnt']);flush();
//         var_dump($outputArray);flush();

         // vytvoříme objekt pro uložení (zde xml)
         $xml = new XMLWriter();
         $xml->openMemory();
         $xml->startDocument('1.0', 'UTF-8');// hlavička
         $xml->setIndent(4);
         $xml->startElement('program'); // obal
         $xml->writeAttribute('xmlns','http://www.vveframework.eu/v6/program');
         $xml->writeAttribute('xml:lang', Locale::getLang());
         // days
         foreach ($outputArray as $day) {
            $xml->startElement('day');
            $xml->writeAttribute('text', $day['text']);
            $xml->writeAttribute('time', $day['time']);
            $xml->writeAttribute('totime', $day['totime']);
            // stages
            foreach ($day['stages'] as $stage) {
               $xml->startElement('stage');
               $xml->writeAttribute('name', $stage['name']);
               // items
               foreach ($stage['items'] as $item) {
                  $xml->startElement('item');
                  $xml->writeAttribute('type', $item['type']);
                  if(isset ($item['time'])) $xml->writeElement('time', $item['time']);
                  if(isset ($item['text'])) $xml->writeElement('text', $item['text']);
                  if(isset ($item['lenght'])) $xml->writeElement('lenght', $item['lenght']);
                  if(isset ($item['bandid'])) $xml->writeElement('bandid', $item['bandid']);
                  $xml->endElement();
               }
               $xml->endElement();
            }
            $xml->endElement();
         }
         $xml->endElement();// konec xml
//         var_export(htmlspecialchars($xml->flush()));
         $prgFile->setContent($xml->flush());

         $this->infoMsg()->addMessage($this->_('Program byl uložen'));
         $this->link()->reload();
         $this->link()->route()->reload();
      }

      $this->view()->form = $form;
      $this->view()->linkBack = (string)$this->link()->route();
   }

   public function programItemController() {
      $this->checkReadableRights();
      $this->view()->type = $this->getRequestParam('type', 'band');
      $this->view()->form = $this->createForm(); // form

      // výchozí hodnoty
      $this->view()->form->lenght->setValues('00:00');
      if($this->view()->type == "band" OR $this->view()->type == "other") {
         $this->view()->form->time->setValues('00:00');
      } else if($this->view()->type == "stage" OR $this->view()->type == "day") {
         $this->view()->form->time->setValues('14:00');
      }

      $this->view()->itemIndex = $this->getRequestParam('index', 0);
   }

   /**
    * Metoda vytvoří formulář pro itemy
    *
    * @return Form
    */
   private function createForm() {
      $form = new Form("program_");

      $elemTime = new Form_Element_Text('time');
      $elemTime->setDimensional();
      $form->addElement($elemTime);

      $elemLenght = new Form_Element_Text('lenght');
      $elemLenght->setDimensional();
      $form->addElement($elemLenght);

      $elemBandId = new Form_Element_Select('bandid');
      // doplníme skupiny
      $model = new Bands_Model();
      $l = $model->getList();
      $bands = array();
      foreach ($l as $band) {
         $bands[(string)$band->{Bands_Model::COLUMN_NAME}] = $band->{Bands_Model::COLUMN_ID};
      }
      $elemBandId->setOptions($bands);
      $elemBandId->setDimensional();
      $form->addElement($elemBandId);

      $elemType = new Form_Element_Hidden('type');
      $elemType->setDimensional();
      $form->addElement($elemType);

      $elemText = new Form_Element_Text('text');
      $elemText->setDimensional();
      $form->addElement($elemText);

      $elemTextLong = new Form_Element_TextArea('textlong');
      $elemTextLong->setDimensional();
      $form->addElement($elemTextLong);

      $elemSave = new Form_Element_Submit('save', $this->_('Uložit'));
      $form->addElement($elemSave);

      return $form;
   }
}
?>