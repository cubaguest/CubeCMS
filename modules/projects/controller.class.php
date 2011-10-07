<?php
class Projects_Controller extends Controller {
   const PARAM_THUM_W = 'tw';
   const PARAM_THUM_H = 'th';
   const PARAM_THUM_C = 'tc';
   const PARAM_MED_W = 'mw';
   const PARAM_MED_H = 'mh';
   const PARAM_BIG_W = 'bw';
   const PARAM_BIG_H = 'bh';

   protected function init()
   {
      parent::init();
      // registrace modulu fotogalerie pro obsluhu galerie
      $this->registerModule('photogalery');
   }

   /**
    * Kontroler pro zobrazení novinek
    */
   public function mainController() 
   {
      //        Kontrola práv
      $this->checkReadableRights();
      $modelSec = new Projects_Model_Sections();
      
      if($this->rights()->isControll()){
         $formDelete = new Form('section_del', true);
         $elemId = new Form_Element_Hidden('id');
         $formDelete->addElement($elemId);
         
         $elemDel = new Form_Element_Submit('del', $this->tr('Smazat'));
         $formDelete->addElement($elemDel);
         
         if($formDelete->isValid()){
            $this->deleteSection($formDelete->id->getValues());
            $this->infoMsg()->addMessage($this->tr('Sekce byla smazána i sprojekty'));
            $this->log(sprintf('Smazána sekce id: "%s"', $formDelete->id->getValues()));
            $this->link()->route()->reload();
         }
         $this->view()->formDelete = $formDelete;
      }
      
      $secs = $modelSec->join(Projects_Model_Sections::COLUMN_ID, 'Projects_Model_Projects', Projects_Model_Projects::COLUMN_ID_SECTION,
         null, Model_ORM::JOIN_OUTER)
         ->where(Projects_Model_Sections::COLUMN_ID_CATEGORY.' = :idc', array('idc' => $this->category()->getId()))
            // ordery atd
         ->records();

      $sectionsData = array();
      foreach ($secs as $sec) {
         $sid = $sec->{Projects_Model_Sections::COLUMN_ID};
         if(!isset ($sectionsData[$sid])){
            $sectionsData[$sid] = new Object();
            $sectionsData[$sid]->data = $sec;
            $sectionsData[$sid]->projects = array();
         }
         if($sec->{Projects_Model_Projects::COLUMN_ID} != null){
            array_push($sectionsData[$sid]->projects, $sec);
         }
      }
      
      $this->view()->sections = $sectionsData;
      
   }

   public function projectController() 
   {
      $this->checkReadableRights();

      $model = new Projects_Model_Projects();
      
      $pr = $model
         ->where(Projects_Model_Projects::COLUMN_URLKEY.' = :prkey',array('prkey' => $this->getRequest('prkey')))
         ->record();
      
      if($pr == false){ return false; }
      
      $this->view()->project = $pr;
      $this->view()->dataDir = $this->module()->getDataDir(true).$pr->{Projects_Model_Projects::COLUMN_URLKEY}.'/';
      
      // fotogalerie
      $this->view()->pCtrl = new Photogalery_Controller($this);
      $this->view()->pCtrl->loadText = false;
      $this->view()->pCtrl->idItem = $pr->{Projects_Model_Projects::COLUMN_ID};
      $this->view()->pCtrl->subDir = $pr->{Projects_Model_Projects::COLUMN_URLKEY}.DIRECTORY_SEPARATOR;
      $this->view()->pCtrl->mainController();

      // related projects
      if($pr->{Projects_Model_Projects::COLUMN_RELATED} != null){
         $relPrIds = explode(';',$pr->{Projects_Model_Projects::COLUMN_RELATED});
         
         $sqlIn = $sqlVals = array();
         foreach ($relPrIds as $id) {
            $sqlIn[] = ':id'.$id;
            $sqlVals['id'.$id] = $id;
         }
         
         $relProjects = $model
            ->joinFK(Projects_Model_Projects::COLUMN_ID_SECTION)
            ->where(Projects_Model_Projects::COLUMN_ID.' IN ('.implode(',', $sqlIn).')', $sqlVals )
            ->records();
         if($relProjects != false){
            $this->view()->projectsRelated = $relProjects;
         }
      }
      
      // form for remove
      if($this->rights()->isControll()){
         $formDelete = new Form('project_del', true);
         $elemDel = new Form_Element_Submit('del', $this->tr('Smazat'));
         $formDelete->addElement($elemDel);
         
         if($formDelete->isValid()){
            $this->deleteProject($pr);
            $this->infoMsg()->addMessage($this->tr('Projekt byl smazán'));
            $this->log(sprintf('Smazán projekt "%s"', $pr->{Projects_Model_Projects::COLUMN_NAME}));
            $this->link()->route()->reload();
         }
         $this->view()->formDelete = $formDelete;
      }
      
   }
   
   public function sectionController() 
   {
      $this->checkReadableRights();
      $modelSec = new Projects_Model_Sections();
      
      $sec = $modelSec->where(Projects_Model_Sections::COLUMN_URLKEY.' = :seckey', array('seckey' => $this->getRequest('seckey')))->record();
      
      if($sec == false){ return false; }
      
      // form for remove
      if($this->rights()->isControll()){
         
      }
      
      $this->view()->section = $sec;
   }

   
   public function addSectionController()
   {
      $this->checkControllRights();
      
      $form = $this->createEditSectionForm();
      
      if($form->isSend() && $form->save->getValues() == false){
         $this->infoMsg()->addMessage($this->tr('Změny byly zrušeny'));
         $this->link()->route()->reload();
      }
      
      if($form->isValid()){
         $model = new Projects_Model_Sections();
         
         $rec = $model->newRecord();
         
         $rec->{Projects_Model_Sections::COLUMN_ID_CATEGORY} = $this->category()->getId();
         $rec->{Projects_Model_Sections::COLUMN_NAME} = $form->name->getValues();
         $rec->{Projects_Model_Sections::COLUMN_TEXT} = $form->text->getValues();
         $rec->{Projects_Model_Sections::COLUMN_TEXT_CLEAR} = strip_tags($rec->{Projects_Model_Sections::COLUMN_TEXT});
         if($form->url->getValues() == null){
            $rec->{Projects_Model_Sections::COLUMN_URLKEY} = vve_cr_url_key($rec->{Projects_Model_Sections::COLUMN_NAME});
         } else {
            $rec->{Projects_Model_Sections::COLUMN_URLKEY} = $form->url->getValues();
         }
         
         $model->save($rec);
         
         $this->infoMsg()->addMessage($this->tr('Sekce byla uložena'));
         $this->log('Přidána sekce do projektů');
         $this->link()->route()->reload();
      }
      
      $this->view()->form = $form;
   }
   
   public function editSectionController()
   {
      $this->checkControllRights();
      
      $model = new Projects_Model_Sections();
      $sec = $model->where(Projects_Model_Sections::COLUMN_URLKEY.' = :seckey', array('seckey' => $this->getRequest('seckey')))->record();
      if($sec == false){ return false; }

      $form = $this->createEditSectionForm($sec);

      if($form->isSend() && $form->save->getValues() == false){
         $this->infoMsg()->addMessage($this->tr('Změny byly zrušeny'));
         $this->link()->route()->reload();
      }
      if($form->isValid()){
         $sec->{Projects_Model_Sections::COLUMN_NAME} = $form->name->getValues();
         $sec->{Projects_Model_Sections::COLUMN_TEXT} = $form->text->getValues();
         $sec->{Projects_Model_Sections::COLUMN_TEXT_CLEAR} = strip_tags($sec->{Projects_Model_Sections::COLUMN_TEXT});
         if($form->url->getValues() == null){
            $sec->{Projects_Model_Sections::COLUMN_URLKEY} = vve_cr_url_key($sec->{Projects_Model_Sections::COLUMN_NAME});
         } else {
            $sec->{Projects_Model_Sections::COLUMN_URLKEY} = $form->url->getValues();
         }
         
         $model->save($sec);
         
         $this->infoMsg()->addMessage($this->tr('Sekce byla uložena'));
         $this->log('Upravena sekce projektů');
         $this->link()->route()->reload();
      }
      $this->view()->section = $sec;
      $this->view()->form = $form;
   }

   /**
    * Metoda pro vytvoření formuláře editace sekce
    * @param Model_ORM_Record $sectionRecord -- objekt záznamu sekce
    * @return Form  
    */
   protected function createEditSectionForm($sectionRecord = null)
   {
      $form = new Form('edit_section');
      
      $elemName = new Form_Element_Text('name', $this->tr('Název'));
      $elemName->addValidation(new Form_Validator_NotEmpty());
      $form->addElement($elemName);
      
      $elemText = new Form_Element_TextArea('text', $this->tr('Popis'));
      $form->addElement($elemText);
      
      $elemUrl = new Form_Element_Text('url', $this->tr('URL klíč'));
      $elemUrl->addFilter(new Form_Filter_UrlKey());
      $form->addElement($elemUrl);
      
      $elemSave = new Form_Element_SaveCancel('save');
      $form->addElement($elemSave);
      
      if($sectionRecord != null){
         $form->name->setValues($sectionRecord->{Projects_Model_Sections::COLUMN_NAME});
         $form->text->setValues($sectionRecord->{Projects_Model_Sections::COLUMN_TEXT});
         $form->url->setValues($sectionRecord->{Projects_Model_Sections::COLUMN_URLKEY});
      }
      
      return $form;
   }
   
   
   public function addProjectController()
   {
      $this->checkWritebleRights();
      
      $form = $this->createEditProjectForm();
      
      if($form->isSend() && $form->save->getValues() == false){
         $this->infoMsg()->addMessage($this->tr('Změny byly zrušeny'));
         $this->link()->route()->reload();
      }
      
      if($form->isValid()){
         $model = new Projects_Model_Projects();
         $rec = $model->newRecord();
         
         $rec->{Projects_Model_Projects::COLUMN_ID_SECTION} = $form->section->getValues();
         $rec->{Projects_Model_Projects::COLUMN_ID_USER} = Auth::getUserId();
         $rec->{Projects_Model_Projects::COLUMN_NAME} = $form->name->getValues();
         $rec->{Projects_Model_Projects::COLUMN_TEXT} = $form->text->getValues();
         $rec->{Projects_Model_Projects::COLUMN_TEXT_CLEAR} = strip_tags($rec->{Projects_Model_Projects::COLUMN_TEXT});
         $rec->{Projects_Model_Projects::COLUMN_RELATED} = implode(';', $form->related->getValues());
         
         if($form->url->getValues() == null){
            $rec->{Projects_Model_Projects::COLUMN_URLKEY} = vve_cr_url_key($rec->{Projects_Model_Projects::COLUMN_NAME});
         } else {
            $rec->{Projects_Model_Projects::COLUMN_URLKEY} = $form->url->getValues();
         }
         
         // zpracovánbí obrázku
         if($form->image->getValues() != null){
            $image = new Filesystem_File_Image($form->image);
            
            $dir = $this->module()->getDataDir().$rec->{Projects_Model_Projects::COLUMN_URLKEY}.DIRECTORY_SEPARATOR;
            
            $image->saveAs($dir, $this->category()->getParam(self::PARAM_THUM_W, VVE_IMAGE_THUMB_W), $this->category()->getParam(self::PARAM_THUM_H, VVE_IMAGE_THUMB_H), 
               $this->category()->getParam(self::PARAM_THUM_C, false), 'main_thum.jpeg', IMAGETYPE_JPEG);
            $image->saveAs($dir, $this->category()->getParam(self::PARAM_MED_W, 300), $this->category()->getParam(self::PARAM_MED_H, 300), 
               false, 'main_med.jpeg', IMAGETYPE_JPEG);
            $image->saveAs($dir, $this->category()->getParam(self::PARAM_BIG_W, VVE_DEFAULT_PHOTO_W), $this->category()->getParam(self::PARAM_BIG_H, VVE_DEFAULT_PHOTO_H), 
               false, 'main.jpeg', IMAGETYPE_JPEG);
            $imageName = 'main';
            $rec->{Projects_Model_Projects::COLUMN_IMAGE} = true;
            $image->remove();
            
         }
         
         $model->save($rec);
         
         $this->infoMsg()->addMessage($this->tr('Projekt byl uložen'));
         $this->log('Přidán projekt '. $rec->{Projects_Model_Projects::COLUMN_NAME});
         $this->link()->route('project', array('prkey' => $rec->{Projects_Model_Projects::COLUMN_URLKEY}))->reload();
      }
      
      $this->view()->form = $form;
   }

   public function editProjectController()
   {
      $this->checkWritebleRights();
      
      $model = new Projects_Model_Projects();
      
      $rec = $model->where(Projects_Model_Projects::COLUMN_URLKEY.' = :prkey', array('prkey' => $this->getRequest('prkey')))->record();
      
      if($rec == false OR (!$this->getRights()->isControll() AND $rec->{Projects_Model_Projects::COLUMN_ID_USER} != Auth::getUserId())){
         return false;
      }
      
      $form = $this->createEditProjectForm($rec);
      
      if($form->isSend() && $form->save->getValues() == false){
         $this->infoMsg()->addMessage($this->tr('Změny byly zrušeny'));
         $this->link()->route('project')->reload();
      }
      
      
      if($form->isValid()){
         $rec->{Projects_Model_Projects::COLUMN_ID_SECTION} = $form->section->getValues();
         $rec->{Projects_Model_Projects::COLUMN_NAME} = $form->name->getValues();
         $rec->{Projects_Model_Projects::COLUMN_TEXT} = $form->text->getValues();
         $rec->{Projects_Model_Projects::COLUMN_TEXT_CLEAR} = strip_tags($rec->{Projects_Model_Projects::COLUMN_TEXT});
         $rec->{Projects_Model_Projects::COLUMN_RELATED} = implode(';', $form->related->getValues());
         
         $oldDirName = $rec->{Projects_Model_Projects::COLUMN_URLKEY};
         
         if($form->url->getValues() == null){
            $rec->{Projects_Model_Projects::COLUMN_URLKEY} = vve_cr_url_key($rec->{Projects_Model_Projects::COLUMN_NAME});
         } else {
            $rec->{Projects_Model_Projects::COLUMN_URLKEY} = $form->url->getValues();
         }
         
         // mazání obrázku
         if(isset ($form->delimg) AND $form->delimg->getValues() == true){
            $rec->{Projects_Model_Projects::COLUMN_IMAGE} = false;
         }
         
         // zpracovánbí obrázku
         if($form->image->getValues() != null){
            $image = new Filesystem_File_Image($form->image);
            
            $dir = $this->module()->getDataDir().$rec->{Projects_Model_Projects::COLUMN_URLKEY}.DIRECTORY_SEPARATOR;
            
            $image->saveAs($dir, $this->category()->getParam(self::PARAM_THUM_W, VVE_IMAGE_THUMB_W), $this->category()->getParam(self::PARAM_THUM_H, VVE_IMAGE_THUMB_H), 
               $this->category()->getParam(self::PARAM_THUM_C, false), 'main_thum.jpeg', IMAGETYPE_JPEG);
            $image->saveAs($dir, $this->category()->getParam(self::PARAM_MED_W, 300), $this->category()->getParam(self::PARAM_MED_H, 300), 
               false, 'main_med.jpeg', IMAGETYPE_JPEG);
            $image->saveAs($dir, $this->category()->getParam(self::PARAM_BIG_W, VVE_DEFAULT_PHOTO_W), $this->category()->getParam(self::PARAM_BIG_H, VVE_DEFAULT_PHOTO_H), 
               false, 'main.jpeg', IMAGETYPE_JPEG);
            $imageName = 'main';
            $rec->{Projects_Model_Projects::COLUMN_IMAGE} = true;
            $image->remove();
            
         }
         $model->save($rec);
         
         // přejmenování adresáře
         $newDirName = $rec->{Projects_Model_Projects::COLUMN_URLKEY};
         if($oldDirName != $newDirName && is_dir($this->module()->getDataDir().$oldDirName) 
            && !rename($this->module()->getDataDir().$oldDirName, $this->module()->getDataDir().$newDirName)){
            throw new UnexpectedValueException($this->tr('Složku s obrázky se nepodařilo přejmenovat'));
         }
         
         $this->infoMsg()->addMessage($this->tr('Projekt byl uložen'));
         $this->log('Upraven projekt '. $rec->{Projects_Model_Projects::COLUMN_NAME});
         $this->link()->route('project', array('prkey' => $rec->{Projects_Model_Projects::COLUMN_URLKEY}))->reload();
      }
      
      $this->view()->project = $rec;
      $this->view()->form = $form;
      $this->view()->dataDir = $this->module()->getDataDir(true).$rec->{Projects_Model_Projects::COLUMN_URLKEY}.'/';
      
   }
   
   /**
    * Metoda pro vytvoření formuláře editace projektu
    * @param Model_ORM_Record $prRecord -- objekt záznamu projekt
    * @return Form  
    */
   protected function createEditProjectForm($prRecord = null)
   {
      $form = new Form('edit_project');
      
      $elemName = new Form_Element_Text('name', $this->tr('Název'));
      $elemName->addValidation(new Form_Validator_NotEmpty());
      $form->addElement($elemName);
      
      $elemText = new Form_Element_TextArea('text', $this->tr('Popis'));
      $form->addElement($elemText);
      
      $elemImage = new Form_Element_File('image', $this->tr('Titulní obrázek'));
      $elemImage->addValidation(new Form_Validator_FileExtension(array('jpg', 'jpeg', 'png', 'gif')));
      $form->addElement($elemImage);
      
      $modelSec = new Projects_Model_Sections();
      $secs = $modelSec->where(Projects_Model_Sections::COLUMN_ID_CATEGORY.' = :idc', array('idc' => $this->category()->getId()))->records();
      
      $elemSec = new Form_Element_Select('section', $this->tr('Sekce'));
      foreach ($secs as $s) {
         $elemSec->setOptions(array($s->{Projects_Model_Sections::COLUMN_ID} => $s->{Projects_Model_Sections::COLUMN_NAME}), true);
      }
      if($this->getRequest('seckey', null) != null){
         foreach ($secs as $s) {
            if($this->getRequest('seckey') == $s->{Projects_Model_Sections::COLUMN_URLKEY}){
               $elemSec->setValues($s->{Projects_Model_Sections::COLUMN_ID});
            }
         }
      }
      $form->addElement($elemSec);
      
      $mProjects = new Projects_Model_Projects();
      $prs = $mProjects
            ->joinFK(Projects_Model_Projects::COLUMN_ID_SECTION)
            ->where(Projects_Model_Sections::COLUMN_ID_CATEGORY.' = :idc', array('idc' => $this->category()->getId()))
            ->records();
      $elemRealted = new Form_Element_Select('related', $this->tr('Relevantní projekty'));
      $elemRealted->setMultiple(true);
      foreach ($prs as $project) {
         if($prRecord != false AND $prRecord->{Projects_Model_Projects::COLUMN_ID} == $project->{Projects_Model_Projects::COLUMN_ID}){
            continue;
         }
         $elemRealted->setOptions(array($project->{Projects_Model_Projects::COLUMN_ID} => $project->{Projects_Model_Projects::COLUMN_NAME}), true);
      }
      $form->addElement($elemRealted);
      
      
      $elemUrl = new Form_Element_Text('url', $this->tr('URL klíč'));
      $elemUrl->addFilter(new Form_Filter_UrlKey());
      $form->addElement($elemUrl);
      
      if($prRecord != null){
         // add element for remove file
         $form->name->setValues($prRecord->{Projects_Model_Projects::COLUMN_NAME});
         $form->text->setValues($prRecord->{Projects_Model_Projects::COLUMN_TEXT});
         $form->url->setValues($prRecord->{Projects_Model_Projects::COLUMN_URLKEY});
         $form->section->setValues($prRecord->{Projects_Model_Projects::COLUMN_ID_SECTION});
         if($prRecord->{Projects_Model_Projects::COLUMN_IMAGE} != false){
            $elemDelImg = new Form_Element_Checkbox('delimg', $this->tr('Smazat přiřazený obrázek'));
            $form->addElement($elemDelImg, null, 3);
         }
         if($prRecord->{Projects_Model_Projects::COLUMN_RELATED} != null){
            $relateds = explode(';', $prRecord->{Projects_Model_Projects::COLUMN_RELATED});
            $form->related->setValues($relateds);
         }
      }
      
      $elemSave = new Form_Element_SaveCancel('save');
      $form->addElement($elemSave);
      
      return $form;
   }

   /**
    * Metoda provede vymazání projektu
    * @param Model_ORM_Record $prRecord 
    */
   protected function deleteProject($prRecord)
   {
      // výmaz dat z db
      $model = new Projects_Model_Projects();
      
      $model->delete($prRecord);
      
      // výmaz adresáře
      $dir = new Filesystem_Dir($this->module()->getDataDir().$prRecord->{Projects_Model_Projects::COLUMN_URLKEY});
      if($dir->exist()){
         $dir->rmDir();
      }
   }
   
   /**
    * Metoda provede vymazání sekce
    * @param int $ids -- id sekce
    */
   protected function deleteSection($ids)
   {
      // výmaz dat z db
      $modelPr = new Projects_Model_Projects();
      $modelSec = new Projects_Model_Sections();
      
      // načtou se projekty kvůli adresářům
      $projects = $modelPr
         ->columns(array(Projects_Model_Projects::COLUMN_URLKEY))
         ->where(Projects_Model_Projects::COLUMN_ID_SECTION." = :ids", array('ids' => $ids))
         ->records();
      
      if(!empty ($projects)){
         foreach ($projects as $project) {
            $dir = new Filesystem_Dir($this->module()->getDataDir().$project->{Projects_Model_Projects::COLUMN_URLKEY});
            if($dir->exist()){ $dir->rmDir();}
         }
      }
      $modelSec->delete($ids); // model má nastavenu relaci, takže vymaže i projekty
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
   public static function clearOnRemove(Category $category) {
   }

   /**
    * Metoda pro nastavení modulu
    */
   protected function settings(&$settings,Form &$form) {
      $fGrpView = $form->addGroup('view', $this->tr('Nastavení vzhledu'));

      $elemScroll = new Form_Element_Text('scroll', $this->tr('Počet položek na stránku'));
      $elemScroll->setSubLabel(sprintf($this->tr('Výchozí: %s položek. Pokud je zadána 0 budou vypsány všechny položky'),self::DEFAULT_ARTICLES_IN_PAGE));
      $elemScroll->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemScroll, $fGrpView);

      if(isset($settings['scroll'])) {
         $form->scroll->setValues($settings['scroll']);
      }

      // znovu protože mohl být už jednou validován bez těchto hodnot
      if($form->isValid()) {
         $settings['scroll'] = (int)$form->scroll->getValues();
      }
   }
}
?>
