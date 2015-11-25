<?php

/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */
class AdvEventsAdmImport_Controller extends AdvEventsBase_Controller {

   public function init()
   {
      parent::init();
   }

   public function mainController()
   {
      parent::mainController();

      $this->view()->advEventSources = AdvEventsBase_Model_EventsSources::getAllRecords();


      $formRemove = $this->createRemoveForm();
      if($formRemove->isValid()){
         $model = AdvEventsBase_Model_EventsSources();
         $model->delete($formRemove->id->getValues());

         $this->infoMsg()->addMessage($this->tr('Zdroj byl odstraněn'));
         $this->link()->reload();
      }
      $this->view()->formRemove = $formRemove;
//      $this->processImportPlaces();
//      $this->processImportLocations();
//      $this->processImportOrganizers();
//      $this->processImportCategories();
      $this->processImportEvents();
   }

   public function addSourceController()
   {
      $form = $this->createSourceForm();

      if ($form->isSend() && $form->save->getValues() == false) {
         $this->link()->route()->redirect();
      }
      if ($form->isValid()) {
         $source = AdvEventsBase_Model_EventsSources::getNewRecord();
         $source->{AdvEventsBase_Model_EventsSources::COLUMN_NAME} = $form->name->getValues();
         $source->{AdvEventsBase_Model_EventsSources::COLUMN_CLASS} = $form->class->getValues();
         $source->{AdvEventsBase_Model_EventsSources::COLUMN_PARAMS} = $form->params->getValues();
         $source->{AdvEventsBase_Model_EventsSources::COLUMN_ENABLED} = $form->enabled->getValues();
         $source->save();
         
         $this->infoMsg()->addMessage($this->tr('Zdroj byl uložen'));
         $this->link()->route()->redirect();
      }

      $this->view()->form = $form;
   }
   
   public function editSourceController($id)
   {
      $source = AdvEventsBase_Model_EventsSources::getRecord($id);
      if(!$source){
         throw new InvalidArgumentException($this->tr('Předáno neplatné ID zdroje'));
      }
         
      
      $form = $this->createSourceForm($source);

      if ($form->isSend() && $form->save->getValues() == false) {
         $this->link()->route()->redirect();
      }
      if ($form->isValid()) {
         $source->{AdvEventsBase_Model_EventsSources::COLUMN_NAME} = $form->name->getValues();
         $source->{AdvEventsBase_Model_EventsSources::COLUMN_CLASS} = $form->class->getValues();
         $source->{AdvEventsBase_Model_EventsSources::COLUMN_PARAMS} = $form->params->getValues();
         $source->{AdvEventsBase_Model_EventsSources::COLUMN_ENABLED} = $form->enabled->getValues();
         $source->save();
         
         $this->infoMsg()->addMessage($this->tr('Zdroj byl uložen'));
         $this->link()->route()->redirect();
      }

      $this->view()->form = $form;
   }

   protected function createRemoveForm()
   {
      $formRemove = new Form('advEventSourceDelete');
      $elemId = new Form_Element_Hidden('id');
      $formRemove->addElement($elemId);

      $elemSubmit = new Form_Element_Submit('delete', $this->tr('Smazat'));
      $formRemove->addElement($elemSubmit);
      return $formRemove;
   }
   
   protected function createSourceForm(Model_ORM_Record $source = null)
   {
      $form = new Form('advevent_source_');

      $eName = new Form_Element_Text('name', $this->tr('Název'));
      $eName->addValidation(new Form_Validator_NotEmpty());
      $form->addElement($eName);

      // get Calsses

      $files = glob(realpath(__DIR__ . '/../adveventsbase/imports/') . '/*.class.php');

      $classes = array();
      foreach ($files as $file) {
         if(pathinfo($file, PATHINFO_BASENAME) == 'events.class.php'){
            continue;
         }
         $fp = fopen($file, 'r');
         $class = $buffer = '';
         $i = 0;
         while (!$class) {
            if (feof($fp))
               break;

            $buffer .= fread($fp, 512);
            $tokens = token_get_all($buffer);

            if (strpos($buffer, '{') === false)
               continue;

            for (; $i < count($tokens); $i++) {
               if ($tokens[$i][0] === T_CLASS) {
                  for ($j = $i + 1; $j < count($tokens); $j++) {
                     if ($tokens[$j] === '{') {
                        $class = $tokens[$i + 2][1];
                     }
                  }
               }
            }
         }
         $classes[str_replace('AdveventsBase_Imports_', '', $class)] = str_replace('AdveventsBase_Imports_', '', $class);
      }
      
      $eClass = new Form_Element_Select('class', $this->tr('Obslužná třída'));
      $eClass->setOptions($classes);
      $form->addElement($eClass);

      $eParams = new Form_Element_TextArea('params', $this->tr('Parametry'));
      $eParams->setSubLabel($this->tr('Parametry pro danou třídu. Pokud nevíte, zeptejte se webmastera. Hodnoty, které obsahují dvojtečku dávejte do uvozovek.'));
      $form->addElement($eParams);
      
      $eActive = new Form_Element_Checkbox('enabled', $this->tr('Aktivní'));
      $eActive->setValues(true);
      $form->addElement($eActive);
      
      $eSend = new Form_Element_SaveCancel('save');
      $form->addElement($eSend);

      if($source){
         $form->name->setValues($source->{AdvEventsBase_Model_EventsSources::COLUMN_NAME});
         $form->class->setValues($source->{AdvEventsBase_Model_EventsSources::COLUMN_CLASS});
         $form->params->setValues($source->{AdvEventsBase_Model_EventsSources::COLUMN_PARAMS});
         $form->enabled->setValues($source->{AdvEventsBase_Model_EventsSources::COLUMN_ENABLED});
      }
      
      return $form;
   }

   protected function processImportPlaces()
   {
      $f = new Form('import_places_');
      $f->addElement(new Form_Element_Submit('send', 'Importuj místa'));
      $this->view()->formImportPalces = $f;

      if ($f->isValid()) {
         $this->importPlaces();
         $this->link()->redirect();
      }
   }

   protected function processImportEvents()
   {
      $f = new Form('import_events_');
      $f->addElement(new Form_Element_Submit('send', 'Importuj událostí'));
      $this->view()->formImportEvents = $f;

      if ($f->isValid()) {
         $this->autoImportEvents();
//         $this->link()->redirect();
      }
   }

   protected function processImportOrganizers()
   {
      $f = new Form('import_org_');
      $f->addElement(new Form_Element_Submit('send', 'Importuj organizátory'));
      $this->view()->formImportOrg = $f;

      if ($f->isValid()) {
         $this->importOrganizers();
         $this->link()->redirect();
      }
   }

   protected function processImportCategories()
   {
      $f = new Form('import_cat_');
      $f->addElement(new Form_Element_Submit('send', 'Importuj kategorie'));
      $this->view()->formImportCat = $f;

      if ($f->isValid()) {
         $this->importCategories();
         $this->link()->redirect();
      }
   }

   protected function processImportLocations()
   {
      $f = new Form('import_locations_');
      $f->addElement(new Form_Element_Submit('send', 'Importuj lokace'));
      $this->view()->formImportLocations = $f;

      if ($f->isValid()) {
         $this->importLocations();
         $this->link()->redirect();
      }
   }

   protected function importEvents()
   {
      $pdo = $this->getICPDO();
      $stmt = $pdo->prepare('SELECT * FROM addresses_town '
//             . 'LIMIT 100 OFFSET 0'
      );

      $stmt->execute();
      $items = $stmt->fetchAll();
//         Debug::log($items);
      // clear current locations
//      $m = new AdvEventsBase_Model_Locations();
//      $m->truncate();
//
//
//      foreach ($items as $item) {
//         $rec = $m->newRecord();
//         $rec->{AdvEventsBase_Model_Locations::COLUMN_ID} = $item->id;
//         $rec->{AdvEventsBase_Model_Locations::COLUMN_NAME} = $item->title;
//         $rec->{AdvEventsBase_Model_Locations::COLUMN_ZIP} = $item->zip;
//         $rec->save();
//      }
   }

   protected function importLocations()
   {
      $pdo = $this->getICPDO();
      $stmt = $pdo->prepare('SELECT * FROM addresses_town '
//             . 'LIMIT 100 OFFSET 0'
      );

      $stmt->execute();
      $items = $stmt->fetchAll();
//         Debug::log($items);
      // clear current locations
      $m = new AdvEventsBase_Model_Locations();
      $m->truncate();


      foreach ($items as $item) {
         $rec = $m->newRecord();
         $rec->{AdvEventsBase_Model_Locations::COLUMN_ID} = $item->id;
         $rec->{AdvEventsBase_Model_Locations::COLUMN_NAME} = $item->title;
         $rec->{AdvEventsBase_Model_Locations::COLUMN_ZIP} = $item->zip;
         $rec->save();
      }
   }

   protected function importPlaces()
   {
      $pdo = $this->getICPDO();
      $stmt = $pdo->prepare('SELECT * FROM addresses_address adr'
          . ' LEFT JOIN addresses_street str ON str.id = adr.street_id'
//             . 'LIMIT 100 OFFSET 0'
      );

      $stmt->execute();
      $items = $stmt->fetchAll();
      Debug::log($items);
      // clear current locations
      $m = new AdvEventsBase_Model_Places();
//         $m->truncate();
//         
//         
      foreach ($items as $item) {
         $rec = $m->newRecord();
         $rec->{AdvEventsBase_Model_Places::COLUMN_ID} = $item->id;
         $rec->{AdvEventsBase_Model_Places::COLUMN_NAME} = $item->title;
         $rec->{AdvEventsBase_Model_Places::COLUMN_LAT} = $item->lat;
         $rec->{AdvEventsBase_Model_Places::COLUMN_LNG} = $item->lon;
         $rec->{AdvEventsBase_Model_Places::COLUMN_ID_LOCATION} = $item->town_id;
//            $rec->save();
      }
   }

   protected function importOrganizers()
   {
      $pdo = $this->getICPDO();
      $stmt = $pdo->prepare('SELECT * FROM events_eventorganizer');

      $stmt->execute();
      $items = $stmt->fetchAll();
//         Debug::log($items);
      // clear current locations
      $m = new AdvEventsBase_Model_Organizers();
      $m->truncate();
//         
//         
      foreach ($items as $item) {
         $rec = $m->newRecord();
         $rec->{AdvEventsBase_Model_Organizers::COLUMN_ID} = $item->id;
         $rec->{AdvEventsBase_Model_Organizers::COLUMN_NAME} = $item->title;
         if ($item->street != null) {
            $rec->{AdvEventsBase_Model_Organizers::COLUMN_ADDRESS} = $item->street . "\n" . $item->town . ' ' . $item->zip;
         }
         $rec->{AdvEventsBase_Model_Organizers::COLUMN_CONTACT_PERSON} = str_replace('?', null, $item->firstname . ' ' . $item->lastname);
         $rec->{AdvEventsBase_Model_Organizers::COLUMN_EMAIL} = $item->email;
         $rec->{AdvEventsBase_Model_Organizers::COLUMN_NOTE} = $item->notice;
         $rec->{AdvEventsBase_Model_Organizers::COLUMN_PHONE} = $item->phone;
         $rec->{AdvEventsBase_Model_Organizers::COLUMN_URL} = $item->web;
         $rec->save();

//            Debug::log($rec->toArray());
      }
   }

   protected function importCategories()
   {
      $pdo = $this->getICPDO();
      $stmt = $pdo->prepare('SELECT * FROM events_eventcategory');

      $stmt->execute();
      $items = $stmt->fetchAll();
//         Debug::log($items);
      // clear current locations
      $m = new AdvEventsBase_Model_Categories();
      $m->truncate();
//         
//         
      foreach ($items as $item) {
         $rec = $m->newRecord();
         $rec->{AdvEventsBase_Model_Categories::COLUMN_ID} = $item->id;
         $rec->{AdvEventsBase_Model_Categories::COLUMN_NAME} = ucfirst(strtolower($item->title));
         $rec->{AdvEventsBase_Model_Categories::COLUMN_DESC} = $item->description;
         $rec->save();

//            Debug::log($rec->toArray());
      }
   }

   /**
    * 
    * @return \PDO
    */
   protected function getICPDO()
   {
      $pdo = new PDO('pgsql:dbname=avia_ic;host=adela.cube-studio.cz;user=avia_ic;password=kombajnCHLAP');
      $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
      return $pdo;
   }

   public function settings(&$settings, Form &$form)
   {
      parent::settings($settings, $form);
   }

}
