<?php
class ProjectsSimple_Controller extends Projects_Controller {
   const BASE_SECTION_ID = 1;

   /**
    * Kontroler pro zobrazení novinek
    */
   public function mainController() 
   {
      //        Kontrola práv
      $this->checkReadableRights();
      $modelProjects = new Projects_Model_Projects();
      
      // načteme sekci, pokud žádná sekce není, není ani projekt
      
//      $projects = $modelSec->join(Projects_Model_Sections::COLUMN_ID, 'Projects_Model_Projects', 
//         Projects_Model_Projects::COLUMN_ID_SECTION)
//         ->where(Projects_Model_Sections::COLUMN_ID_CATEGORY.' = :idc', array('idc' => $this->category()->getId()))
//            // ordery atd
//         ->order(array( Projects_Model_Projects::COLUMN_WEIGHT => Model_ORM::ORDER_DESC,
//                        Projects_Model_Projects::COLUMN_NAME => Model_ORM::ORDER_ASC ))
//         ->records();
      
      $projects = $modelProjects->joinFK(Projects_Model_Projects::COLUMN_ID_SECTION, array(Projects_Model_Sections::COLUMN_ID_CATEGORY))
         ->where(Projects_Model_Sections::COLUMN_ID_CATEGORY.' = :ids', array( 'ids' => $this->category()->getId() ))
         ->order(array( Projects_Model_Projects::COLUMN_WEIGHT => Model_ORM::ORDER_DESC,
                        Projects_Model_Projects::COLUMN_NAME => Model_ORM::ORDER_ASC ))
         ->records();
      
      $this->view()->projects = $projects;
      $this->view()->dataDir = $this->module()->getDataDir(true);
      
      // pokud není žádná sekce, vytvoříme základní sekci
      if($this->category()->getRights()->isWritable() && ( $projects == false || empty ($projects) )){
         $modelSec = new Projects_Model_Sections();
         $sec = $modelSec->where(Projects_Model_Sections::COLUMN_ID_CATEGORY.' = :ids', array('ids' => $this->category()->getId()))->record();

         if ($sec == false || $sec->isNew()) {
            $baseSec = $modelSec->newRecord();
            $baseSec->{Projects_Model_Sections::COLUMN_ID_CATEGORY} = $this->category()->getId();
            $baseSec->{Projects_Model_Sections::COLUMN_NAME} = $this->tr('Základní');
            $baseSec->{Projects_Model_Sections::COLUMN_URLKEY} = 'base';
         
            $modelSec->save($baseSec);
            $this->infoMsg()->addMessage($this->tr('Základní sekce byla vytvořena'));
            $this->link()->reload();
         }
      }
   }

   /**
    * Metoda pro přípravu spuštění registrovaného modulu
    * @param Controller $ctrl -- kontroler modulu
    * @param string $module -- název modulu
    * @param string $action -- akce
    * @return type 
    */
   protected function callRegisteredModule(Controller $ctrl, $module, $action)
   {
      $model = new Projects_Model_Projects();
      $pr = $model->where(Projects_Model_Projects::COLUMN_URLKEY, $this->getRequest('prkey'))->record();
      if($pr == false) return false;
      // base setup variables
      $ctrl->idItem = $pr->{Projects_Model_Projects::COLUMN_ID};
      $ctrl->subDir = $pr->{Projects_Model_Projects::COLUMN_URLKEY}.DIRECTORY_SEPARATOR;
      $ctrl->linkBack = $this->link()->route('project');
      
      $ctrl->view()->name = $pr->{Projects_Model_Projects::COLUMN_NAME};
   }

   /**
    * Smazání článků při odstranění kategorie
    * @param Category $category
    */
   public static function clearOnRemove(Category $category) 
   {
   }
}
?>
