<?php
class Projects_Controller extends Controller {
   const PARAM_THUM_W = 'tw';
   const PARAM_THUM_H = 'th';
   const PARAM_THUM_C = 'tc';
   const PARAM_MED_W = 'mw';
   const PARAM_MED_H = 'mh';
   const PARAM_BIG_W = 'bw';
   const PARAM_BIG_H = 'bh';

   protected $useSection = true;


   protected function init()
   {
      parent::init();
      // registrace modulu fotogalerie pro obsluhu galerie
      $this->registerModule('photogalery');
      $this->loadSections();
      $this->processCheckDeleteSetion();
   }

   /**
    * Kontroler pro zobrazení novinek
    */
   public function mainController() 
   {
      //        Kontrola práv
      $this->checkReadableRights();
      
      $this->view()->imagesPath = $this->module()->getDataDir(true);
      $this->view()->dataDir = $this->module()->getDataDir(false);
      
      // načtení textu
      $textM = new Text_Model();
      $textRecord = $textM->where(Text_Model::COLUMN_ID_CATEGORY.' = :idc AND '.Text_Model::COLUMN_SUBKEY.' = :subkey', 
         array('idc' => $this->category()->getId(), 'subkey' => Text_Controller::TEXT_MAIN_KEY) )->record();
      $this->view()->text = $textRecord;
   }
   
   protected function loadSections()
   {
      $modelSec = new Projects_Model_Sections();
      
      $modelSec->join(Projects_Model_Sections::COLUMN_ID, 'Projects_Model_Projects', Projects_Model_Projects::COLUMN_ID_SECTION)
         ->where(Projects_Model_Sections::COLUMN_ID_CATEGORY.' = :idc', array('idc' => $this->category()->getId()))
            // ordery atd
         ->order(array(
            Projects_Model_Sections::COLUMN_ORDER => Model_ORM::ORDER_ASC,
            Projects_Model_Projects::COLUMN_ORDER => Model_ORM::ORDER_ASC,
            ))
         ->groupBy(Projects_Model_Sections::COLUMN_ID);
//      Debug::log($modelSec->getSQLQuery());
      $this->view()->sections = $modelSec->records();
   }
   
   protected function processCheckDeleteSetion()
   {
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
            if($this->getRequestParam('backlink')){
               $this->link()->redirect($this->getRequestParam('backlink'));
            } else {
               $this->link()->route()->redirect();
            }
         }
         $this->view()->formDelete = $formDelete;
      }
   }
   
   public function projectController($prkey) 
   {
      $this->checkReadableRights();

      $model = new Projects_Model_Projects();
      
      $pr = $model
         ->joinFk(Projects_Model_Projects::COLUMN_ID_SECTION)
         ->joinFk(Projects_Model_Projects::COLUMN_ID_USER, array(Model_Users::COLUMN_USERNAME, Model_Users::COLUMN_NAME, Model_Users::COLUMN_SURNAME))
         ->where(
            Projects_Model_Projects::COLUMN_URLKEY.' = :prkey '
            .' AND '.Projects_Model_Sections::COLUMN_ID_CATEGORY.' = :idcat'
            , array(
            'prkey' => $prkey,
            'idcat' => $this->category()->getId()
         ))->record();
      
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
            $this->view()->projectsRelatedDataDir = $this->module()->getDataDir(true);
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
            $this->link()->route()->redirect();
         }
         $this->view()->formDelete = $formDelete;
      }
      
   }
   
   public function sectionController($seckey) 
   {
      $this->checkReadableRights();
      $modelSec = new Projects_Model_Sections();
      
      $this->processCheckDeleteSetion();
      
      $sec = $modelSec->where(
         Projects_Model_Sections::COLUMN_URLKEY.' = :seckey'
         .' AND '.Projects_Model_Sections::COLUMN_ID_CATEGORY.' = :idcat', 
         array(
         'seckey' => $seckey,
         'idcat' => $this->category()->getId()
         ))->record();
      
      if($sec == false){ return false; }
      
      $this->view()->section = $sec;
      
      // vyčistit projekty, rptože jsou načteny všechny
      $this->view()->projects = $sec->getProjects();
      $this->view()->imagesPath = $this->module()->getDataDir(true);
      $this->view()->dataDir = $this->module()->getDataDir(false);
   }

   
   public function addSectionController()
   {
      $this->checkControllRights();
      
      $form = $this->createEditSectionForm();
      
      if($form->isSend() && $form->save->getValues() == false){
         $this->infoMsg()->addMessage($this->tr('Změny byly zrušeny'));
         if($this->getRequestParam('backlink')){
            $this->link()->redirect($this->getRequestParam('backlink'));
         } else {
            $this->link()->route()->redirect();
         }
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
//         $rec->{Projects_Model_Sections::COLUMN_WEIGHT} = $form->weight->getValues();
         
         $model->save($rec);
         
         $this->infoMsg()->addMessage($this->tr('Sekce byla uložena'));
         $this->log('Přidána sekce do projektů');
         if($this->getRequestParam('backlink')){
            $this->link()->redirect($this->getRequestParam('backlink'));
         } else {
            $this->link()->route()->redirect();
         }
      }
      
      $this->view()->form = $form;
   }
   
   public function editSectionController()
   {
      $this->checkControllRights();
      
      $model = new Projects_Model_Sections();
      $sec = $model->where(
         Projects_Model_Sections::COLUMN_URLKEY.' = :seckey'
         .' AND '.Projects_Model_Sections::COLUMN_ID_CATEGORY.' = :idcat', 
         array(
         'seckey' => $this->getRequest('seckey'),
         'idcat' => $this->category()->getId()
         ))->record();
      if($sec == false){ return false; }

      $form = $this->createEditSectionForm($sec);

      if($form->isSend() && $form->save->getValues() == false){
         $this->infoMsg()->addMessage($this->tr('Změny byly zrušeny'));
         if($this->getRequestParam('backlink')){
            $this->link()->redirect($this->getRequestParam('backlink'));
         } else {
            $this->link()->route('section')->redirect();
         }
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
//         $sec->{Projects_Model_Sections::COLUMN_WEIGHT} = $form->weight->getValues();
         
         $model->save($sec);
         
         $this->infoMsg()->addMessage($this->tr('Sekce byla uložena'));
         $this->log('Upravena sekce projektů');
         if($this->getRequestParam('backlink')){
            $this->link()->redirect($this->getRequestParam('backlink'));
         } else {
            $this->link()->route('section', array('seckey' => $sec->{Projects_Model_Sections::COLUMN_URLKEY}))->redirect();
         }
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
      $grp = $form->addGroup('base', $this->tr('Popis sekce'));
      
      $elemName = new Form_Element_Text('name', $this->tr('Název'));
      $elemName->addValidation(new Form_Validator_NotEmpty());
      $form->addElement($elemName, $grp);
      
      $elemText = new Form_Element_TextArea('text', $this->tr('Popis'));
      $form->addElement($elemText, $grp);
      
      $elemUrl = new Form_Element_Text('url', $this->tr('URL klíč'));
      $elemUrl->addFilter(new Form_Filter_UrlKey());
      $elemUrl->setAdvanced(true);
      $form->addElement($elemUrl, $grp);
      
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
         if($this->getRequestParam('backlink')){
            $this->link()->redirect($this->getRequestParam('backlink'));
         } else {
            $this->link()->route()->redirect();
         }
      }
      
      if($form->isValid()){
         $model = new Projects_Model_Projects();
         $rec = $model->newRecord();
         
         $rec->{Projects_Model_Projects::COLUMN_ID_SECTION} = $form->section->getValues();
         $rec->{Projects_Model_Projects::COLUMN_ID_USER} = Auth::getUserId();
         $rec->{Projects_Model_Projects::COLUMN_ID_USER_LAST_EDIT} = Auth::getUserId();
         $rec->{Projects_Model_Projects::COLUMN_TIME_ADD} = new DateTime();
         $rec->{Projects_Model_Projects::COLUMN_NAME} = $form->name->getValues();
         $rec->{Projects_Model_Projects::COLUMN_NAME_SHORT} = $form->shortName->getValues();
         $rec->{Projects_Model_Projects::COLUMN_TEXT} = $form->text->getValues();
         $rec->{Projects_Model_Projects::COLUMN_TEXT_CLEAR} = strip_tags($rec->{Projects_Model_Projects::COLUMN_TEXT});
         $rec->{Projects_Model_Projects::COLUMN_TPL_PARAMS} = htmlentities($form->tplParams->getValues());
         $rec->{Projects_Model_Projects::COLUMN_PLACE} = $form->place->getValues();
         $rec->{Projects_Model_Projects::COLUMN_IMPORTANT} = $form->important->getValues();
         $date = $form->dateadd->getValues();
         $date->setTime(date("H"), date("i"), date("s"));
         $rec->{Projects_Model_Projects::COLUMN_TIME_ADD} = $date;
         if($form->related->getValues() != null){
            $rec->{Projects_Model_Projects::COLUMN_RELATED} = implode(';', $form->related->getValues());
         }
         
         
         $urls = $form->url->getValues();
         $names = $form->name->getValues();
         foreach ($urls as $lang => $value) {
            $urlBase = $value != null ? $value : $names[$lang];
            $rec[Projects_Model_Projects::COLUMN_URLKEY][$lang] = $this->createUniqueProjectUrlKey( $urlBase, $lang );
         }
         
         // zpracovánbí obrázku
         $dir = $this->module()->getDataDir().$rec[Projects_Model_Projects::COLUMN_URLKEY][Locales::getDefaultLang()].DIRECTORY_SEPARATOR;
         
         // miniatura
         if($form->imageThumb->getValues() != null){
            // zadaná miniatura
            $thumb = new File_Image($form->imageThumb);
            $thumb->move($dir);
            $rec->{Projects_Model_Projects::COLUMN_THUMB} = $thumb->getName();
         }
            
         if($form->image->getValues() != null){
            $image = new File_Image($form->image);
            $image->move($dir);
            
            // miniatura
            if($rec->{Projects_Model_Projects::COLUMN_THUMB} == null){
               // miniatura z titulního
               $thumbParts = pathinfo($image->getName());
               $thumbName = $thumbParts['filename'] . '_thumb.' . $thumbParts['extension'];
               
               $thumb = $image->copy($dir, true, $thumbName);
               $rec->{Projects_Model_Projects::COLUMN_THUMB} = $thumb->getName();
            }
            $rec->{Projects_Model_Projects::COLUMN_IMAGE} = $image->getName();
         }
         
         $model->save($rec);
         
         $this->infoMsg()->addMessage($this->tr('Projekt byl uložen'));
         $this->log('Přidán projekt '. $rec->{Projects_Model_Projects::COLUMN_NAME});
         $this->link()->route('project', array('prkey' => $rec->{Projects_Model_Projects::COLUMN_URLKEY}))->redirect();
      }
      
      $this->view()->form = $form;
   }
   
   public function editProjectController()
   {
      $this->checkWritebleRights();
      
      $model = new Projects_Model_Projects();
      
      $rec = $model->joinFk(Projects_Model_Projects::COLUMN_ID_SECTION)
         ->where(
            Projects_Model_Projects::COLUMN_URLKEY.' = :prkey '
            .' AND '.Projects_Model_Sections::COLUMN_ID_CATEGORY.' = :idcat'
            , array(
            'prkey' => $this->getRequest('prkey'),
            'idcat' => $this->category()->getId()
         ))->record();
      
      if($rec == false OR (!$this->getRights()->isControll() AND $rec->{Projects_Model_Projects::COLUMN_ID_USER} != Auth::getUserId())){
         return false;
      }
      
      $form = $this->createEditProjectForm($rec);
      
      if($form->isSend() && $form->save->getValues() == false){
         $this->infoMsg()->addMessage($this->tr('Změny byly zrušeny'));
         if($this->getRequestParam('backlink')){
            $this->link()->redirect($this->getRequestParam('backlink'));
         } else {
            $this->link()->route('project')->redirect();
         }
      }
      
      
      if($form->isValid()){
         $rec->{Projects_Model_Projects::COLUMN_ID_SECTION} = $form->section->getValues();
         $rec->{Projects_Model_Projects::COLUMN_ID_USER_LAST_EDIT} = Auth::getUserId();
         $rec->{Projects_Model_Projects::COLUMN_NAME} = $form->name->getValues();
         $rec->{Projects_Model_Projects::COLUMN_NAME_SHORT} = $form->shortName->getValues();
         $rec->{Projects_Model_Projects::COLUMN_TEXT} = $form->text->getValues();
         $rec->{Projects_Model_Projects::COLUMN_TEXT_CLEAR} = strip_tags($rec->{Projects_Model_Projects::COLUMN_TEXT});
         $rec->{Projects_Model_Projects::COLUMN_TPL_PARAMS} = htmlentities($form->tplParams->getValues());
         $rec->{Projects_Model_Projects::COLUMN_PLACE} = $form->place->getValues();
         $rec->{Projects_Model_Projects::COLUMN_IMPORTANT} = $form->important->getValues();
         
         $date = $form->dateadd->getValues();
         $dateOld = new DateTime($rec->{Projects_Model_Projects::COLUMN_TIME_ADD});
         $date->setTime($dateOld->format("H"), $dateOld->format("i"), $dateOld->format("s"));
         
         $rec->{Projects_Model_Projects::COLUMN_TIME_ADD} = $form->dateadd->getValues();
         $relProjects = $form->related->getValues();
         if(is_array($relProjects) && !empty ($relProjects)){
            $rec->{Projects_Model_Projects::COLUMN_RELATED} = implode(';', $relProjects);
         } else {
            $rec->{Projects_Model_Projects::COLUMN_RELATED} = null;
         }
         
         $oldDirName = $rec[Projects_Model_Projects::COLUMN_URLKEY][Locales::getDefaultLang()];
         
         $urls = $form->url->getValues();
         $names = $form->name->getValues();
         foreach ($urls as $lang => $value) {
            $urlBase = $value != null ? $value : $names[$lang];
            $rec[Projects_Model_Projects::COLUMN_URLKEY][$lang] = $this->createUniqueProjectUrlKey( $urlBase, $lang, $rec->getPK() );
         }
         
         $rec->{Projects_Model_Projects::COLUMN_KEYWORDS} = $form->keywords->getValues();
         $rec->{Projects_Model_Projects::COLUMN_DESCRIPTION} = $form->desc->getValues();
         
         // mazání obrázku
         if(isset ($form->delimgtitle) AND $form->delimgtitle->getValues() == true){
            $rec->{Projects_Model_Projects::COLUMN_IMAGE} = null;
         }
         if(isset ($form->delimgthumb) AND $form->delimgthumb->getValues() == true){
            $rec->{Projects_Model_Projects::COLUMN_THUMB} = null;
         }
         
         // zpracovánbí obrázku
         $dir = $this->module()->getDataDir().$rec[Projects_Model_Projects::COLUMN_URLKEY][Locales::getDefaultLang()].DIRECTORY_SEPARATOR;
         // miniatura
         if($form->imageThumb->getValues() != null){
            // zadaná miniatura
            $thumb = new File_Image($form->imageThumb);
            $thumb->move($dir);
//            $thumb->getData()->resize(
//                  $this->category()->getParam(self::PARAM_THUM_W, VVE_IMAGE_THUMB_W),
//                  $this->category()->getParam(self::PARAM_THUM_H, VVE_IMAGE_THUMB_H),
//                  $this->category()->getParam(self::PARAM_THUM_C, true) == true
//                  ? File_Image_Base::RESIZE_CROP : File_Image_Base::RESIZE_AUTO
//            );
//            
//            $thumb->save();
            $rec->{Projects_Model_Projects::COLUMN_THUMB} = $thumb->getName();
         }
         
         // titulní obrázek
         if($form->image->getValues() != null){
            $image = new File_Image($form->image);
            $image->move($dir);
            
            // miniatura
            if($form->imageThumb->getValues() == null){
               // miniatura z titulního
               $thumbParts = pathinfo($image->getName());
               $thumbName = $thumbParts['filename'] . '_thumb.' . $thumbParts['extension'];
               
               $thumb = $image->copy($dir, true, $thumbName);
               
//               $thumb->getData()->resize(
//                     $this->category()->getParam(self::PARAM_THUM_W, VVE_IMAGE_THUMB_W),
//                     $this->category()->getParam(self::PARAM_THUM_H, VVE_IMAGE_THUMB_H),
//                     $this->category()->getParam(self::PARAM_THUM_C, true) == true
//                     ? File_Image_Base::RESIZE_CROP : File_Image_Base::RESIZE_AUTO
//               );
               $thumb->save();
               
               $rec->{Projects_Model_Projects::COLUMN_THUMB} = $thumb->getName();
            }
//            $image->getData()->resize(
//                  $this->category()->getParam(self::PARAM_BIG_W, VVE_DEFAULT_PHOTO_W),
//                  $this->category()->getParam(self::PARAM_BIG_H, VVE_DEFAULT_PHOTO_H), File_Image_Base::RESIZE_AUTO );
//            
//            $image->save();
            $rec->{Projects_Model_Projects::COLUMN_IMAGE} = $image->getName();
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
         if($this->getRequestParam('backlink')){
            $this->link()->redirect($this->getRequestParam('backlink'));
         } else {
            $this->link()->route('project', array('prkey' => $rec->{Projects_Model_Projects::COLUMN_URLKEY}))->redirect();
         }
      }
      
      $this->view()->project = $rec;
      $this->view()->form = $form;
      $this->view()->dataDir = $this->module()->getDataDir(true).$rec->{Projects_Model_Projects::COLUMN_URLKEY}.'/';
      
   }
   
   public function sortProjectsController($seckey = null)
   {
      $this->checkWritebleRights();
      
      $model = new Projects_Model_Projects();
      $modelS = new Projects_Model_Sections();
      if($seckey){
         $section = $modelS
             ->where(Projects_Model_Sections::COLUMN_URLKEY." = :ukey", array('ukey' => $seckey))
             ->record();
      } else {
         $section = $modelS
             ->where(Projects_Model_Sections::COLUMN_ID_CATEGORY." = :idc", array('idc' => $this->category()->getId()))
             ->record();
      }
      if(!$section){
         throw new UnexpectedPageException();
      }
      
      $projects = $model
          ->where(Projects_Model_Projects::COLUMN_ID_SECTION." = :ids", array('ids' => $section->getPK()))
          ->records();

      $form = new Form('person_order_');
      
      $eId = new Form_Element_Hidden('id');
      $eId->setDimensional();
      
      $form->addElement($eId);
      
      $eSave = new Form_Element_SaveCancel('save');
      $form->addElement($eSave);

      if($form->isSend() && $form->save->getValues() == false){
         if($this->getRequestParam('backlink')){
            $this->link()->redirect($this->getRequestParam('backlink'));
         } else {
            $this->link()->route('section')->redirect();
         }
      }
      
      if($form->isValid()){
         $ids = $form->id->getValues();
         foreach ($ids as $index => $id) {
            /* @var $project Model_ORM_Ordered_Record */
            $project = Projects_Model_Projects::getRecord($id);
            $project->setRecordPosition($index + 1);
         }
         $this->infoMsg()->addMessage($this->tr('Pořadí bylo uloženo'));
         if($this->getRequestParam('backlink')){
            $this->link()->redirect($this->getRequestParam('backlink'));
         } else {
            $this->link()->route('section')->redirect();
         }
      }
      
      $this->view()->projects = $projects;
      $this->view()->form = $form;
   }
   
   public function sortSectionsController()
   {
      $this->checkWritebleRights();
      
      $form = new Form('section_order_');
      
      $eId = new Form_Element_Hidden('id');
      $eId->setDimensional();
      
      $form->addElement($eId);
      
      $eSave = new Form_Element_SaveCancel('save');
      $form->addElement($eSave);

      if($form->isSend() && $form->save->getValues() == false){
         $this->link()->route()->redirect();
      }
      
      if($form->isValid()){
         $ids = $form->id->getValues();
         foreach ($ids as $index => $id) {
            /* @var $project Model_ORM_Ordered_Record */
            $section = Projects_Model_Sections::getRecord($id);
            $section->setRecordPosition($index + 1);
         }
         $this->infoMsg()->addMessage($this->tr('Pořadí bylo uloženo'));
         $this->link()->route()->redirect();
      }
      
      $model = new Projects_Model_Sections();
      $this->view()->sections = $model->where(Projects_Model_Sections::COLUMN_ID_CATEGORY." = :idc", array('idc' => $this->category()->getId()))->records();;
      $this->view()->form = $form;
   }
   
   public function manageSectionsController()
   {
      $this->checkWritebleRights();
      
      if(isset($_POST['action']) && $_POST['action'] == 'sort'){
         if(!isset($_POST['id']) || !isset($_POST['pos'])){
            throw new InvalidArgumentException($this->tr('Nebyly předány všechny aprametry'));
         }
         $id = (int)$_POST['id'];
         $pos = (int)$_POST['pos'];
         Projects_Model_Sections::setRecordPosition($id, $pos);
//         $this->infoMsg()->addMessage($this->tr('Pozice byla změněna'));
         return;
      }
      
      $model = new Projects_Model_Sections();
      $this->view()->sections = $model
          ->where(Projects_Model_Sections::COLUMN_ID_CATEGORY." = :idc", array('idc' => $this->category()->getId()))
          ->records();
   }
   
   public function editTextController() {
      $this->checkControllRights();
      $form = new Form('list_text_', true);
      
      $textM = new Text_Model();
      $textRecord = $textM->where(Text_Model::COLUMN_ID_CATEGORY.' = :idc AND '.Text_Model::COLUMN_SUBKEY.' = :subkey', 
         array('idc' => $this->category()->getId(), 'subkey' => Text_Controller::TEXT_MAIN_KEY) )->record();

      $elemText = new Form_Element_TextArea('text', $this->tr('Text'));
      $elemText->setLangs();
      if($textRecord != false){
         $elemText->setValues($textRecord->{Text_Model::COLUMN_TEXT});
      }
      $form->addElement($elemText);

      $elemS = new Form_Element_SaveCancel('save');
      $form->addElement($elemS);

      if($form->isSend() AND $form->save->getValues() == false){
         $this->infoMsg()->addMessage($this->tr('Úpravy úvodního textu byly zrušeny'));
         $this->link()->route()->redirect();
      }

      if($form->isValid()) {
         if($textRecord == false){
            $textRecord = $textM->newRecord();
         }
         
         $textRecord->{Text_Model::COLUMN_TEXT} = $form->text->getValues(); 
         $textRecord->{Text_Model::COLUMN_TEXT_CLEAR} = vve_strip_tags($form->text->getValues()); 
         $textRecord->{Text_Model::COLUMN_ID_CATEGORY} = $this->category()->getId(); 
         $textRecord->{Text_Model::COLUMN_SUBKEY} = Text_Controller::TEXT_MAIN_KEY; 
         
         $textM->save($textRecord);

         $this->infoMsg()->addMessage($this->tr('Úvodní text byl uložen'));
         $this->link()->route()->redirect();
      }

      $this->view()->form = $form;
   }
   
   /**
    * Metoda pro vytvoření formuláře editace projektu
    * @param Model_ORM_Record $prRecord -- objekt záznamu projekt
    * @return Form  
    */
   protected function createEditProjectForm($prRecord = null)
   {
      $form = new Form('edit_project');
      
      $fGrpBase = $form->addGroup('base', $this->tr('Základní informace'));
      $fGrpInclusion = $form->addGroup('inclusion', $this->tr('Zařazení'));
      $fGrpAppearance = $form->addGroup('appearance', $this->tr('Vzhled'));
      $fGrpSEO = $form->addGroup('seo', $this->tr('SEO'));
      
      $elemName = new Form_Element_Text('name', $this->tr('Název'));
      $elemName->setLangs();
      $elemName->addValidation(new Form_Validator_NotEmpty(null, array(Locales::getDefaultLang())));
      $form->addElement($elemName, $fGrpBase);
      
      $elemShortName = new Form_Element_Text('shortName', $this->tr('Zkrácený název / popisek'));
      $elemShortName->setLangs();
      $elemShortName->setSubLabel($this->tr('Bývá využit při výpis seznamu projektů. znak "\" nahrazen zalomením řádku'));
      $form->addElement($elemShortName, $fGrpBase);
      
      $elemPlace = new Form_Element_Text('place', $this->tr('Místo/umístění'));
      $form->addElement($elemPlace, $fGrpBase);
      
      $elemText = new Form_Element_TextArea('text', $this->tr('Popis'));
      $elemText->setLangs();
      $form->addElement($elemText, $fGrpBase);
      
      $elemImage = new Form_Element_File('image', $this->tr('Titulní obrázek'));
      $elemImage->addValidation(new Form_Validator_FileExtension(array('jpg', 'jpeg', 'png', 'gif')));
      $elemImage->setSubLabel($this->tr('Další obrázky je možné přidat po uložení'));
      $form->addElement($elemImage, $fGrpAppearance);
      
      $elemImageThumb = new Form_Element_File('imageThumb', $this->tr('Titulní obrázek - miniatura'));
      $elemImageThumb->addValidation(new Form_Validator_FileExtension(array('jpg', 'jpeg', 'png', 'gif')));
      $elemImageThumb->setSubLabel($this->tr('Pokud není zadán, je vytvořena miniatura z titulního.'));
//      $elemImageThumb->setAdvanced(true);
      $form->addElement($elemImageThumb, $fGrpAppearance);
      
      $modelSec = new Projects_Model_Sections();
      $secs = $modelSec->where(Projects_Model_Sections::COLUMN_ID_CATEGORY.' = :idc', array('idc' => $this->category()->getId()))->records();
      
      $elemSec = new Form_Element_Select('section', $this->tr('Sekce'));
      $elemSec->setAdvanced($this->useSection ? false : true);
          
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
      $form->addElement($elemSec, $fGrpInclusion);
      
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
         $elemRealted->addOption($project->{Projects_Model_Projects::COLUMN_NAME}, $project->{Projects_Model_Projects::COLUMN_ID});
      }
      $form->addElement($elemRealted, $fGrpInclusion);
      
      $elemImportant = new Form_Element_Checkbox('important', $this->tr('Důležitý projekt'));
      $elemImportant->setSubLabel($this->tr('Projekt se tak například zobrazí na různých stránkách, například v panelu.'));
      $form->addElement($elemImportant, $fGrpInclusion);
      
      
      $elemDateAdd = new Form_Element_Text('dateadd', $this->tr('Datum přidání'));
      $elemDateAdd->setValues(strftime("%x"));
      $elemDateAdd->addFilter(new Form_Filter_DateTimeObj());
      $elemDateAdd->addValidation(new Form_Validator_NotEmpty());
      $elemDateAdd->setAdvanced(true);
      $form->addElement($elemDateAdd, $fGrpInclusion);
      
      $elemUrl = new Form_Element_UrlKey('url', $this->tr('URL klíč'));
      $elemUrl->setLangs();
      $elemUrl->setAdvanced(true);
      $elemUrl->setCheckingUrl($this->link()->route('checkProjectUrlkey'))
         ->setUpdateFromElement($elemName);
      if($prRecord != null){
         $elemUrl->setAutoUpdate(false);
      }
      $form->addElement($elemUrl, $fGrpSEO);
      
      $elemKeywords = new Form_Element_Text('keywords', $this->tr('Klíčová slova'));
      $elemKeywords->setAdvanced(true);
      $form->addElement($elemKeywords, $fGrpSEO);
      
      $elemDesc = new Form_Element_TextArea('desc', $this->tr('Popis pro vyhledávače'));
      $elemDesc->setAdvanced(true);
      $form->addElement($elemDesc, $fGrpSEO);
      
      $elemTplParams = new Form_Element_Text('tplParams', $this->tr('Parametry šablony'));
      $elemTplParams->setSubLabel($this->tr('Parametry projektu pro šablonu (např barva, ...). Formát: "název:hodnota;název:hodnota". Parametry určuje kodér šablony.'));
      $elemTplParams->setAdvanced(true);
      $form->addElement($elemTplParams, $fGrpAppearance);
      
      if($prRecord != null){
         // add element for remove file
         $form->name->setValues($prRecord->{Projects_Model_Projects::COLUMN_NAME});
         $form->shortName->setValues($prRecord->{Projects_Model_Projects::COLUMN_NAME_SHORT});
         $form->text->setValues($prRecord->{Projects_Model_Projects::COLUMN_TEXT});
         $form->url->setValues($prRecord->{Projects_Model_Projects::COLUMN_URLKEY});
         $form->keywords->setValues($prRecord->{Projects_Model_Projects::COLUMN_KEYWORDS});
         $form->desc->setValues($prRecord->{Projects_Model_Projects::COLUMN_DESCRIPTION});
         $form->section->setValues($prRecord->{Projects_Model_Projects::COLUMN_ID_SECTION});
         $form->tplParams->setValues($prRecord->{Projects_Model_Projects::COLUMN_TPL_PARAMS});
         $form->place->setValues($prRecord->{Projects_Model_Projects::COLUMN_PLACE});
         $form->important->setValues($prRecord->{Projects_Model_Projects::COLUMN_IMPORTANT});
         $date = new DateTime($prRecord->{Projects_Model_Projects::COLUMN_TIME_ADD});
         $form->dateadd->setValues(Utils_DateTime::fdate("%x", $date));
         
         if($prRecord->{Projects_Model_Projects::COLUMN_IMAGE} != null){
            $elemDelImgTitle = new Form_Element_Checkbox('delimgtitle', $this->tr('Smazat titulní obrázek'));
            $form->addElement($elemDelImgTitle, $fGrpAppearance, 1);
         }
         if($prRecord->{Projects_Model_Projects::COLUMN_THUMB} != null){
            $elemDelImgTitleThumb = new Form_Element_Checkbox('delimgthumb', $this->tr('Smazat miniaturu titulního obrázeku'));
//            $elemDelImgTitleThumb->setAdvanced(true);
            $form->addElement($elemDelImgTitleThumb, $fGrpAppearance, $prRecord->{Projects_Model_Projects::COLUMN_IMAGE} != null ? 3 : 2);
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
   
   public function checkProjectUrlkeyController()
   {
      $this->checkReadableRights();
      
      $this->view()->urlkey = $this->createUniqueProjectUrlKey($_POST['key'],$_POST['lang']);
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

   protected function createUniqueProjectUrlKey($key, $lang, $id = 0)
   {
      $model = new Projects_Model_Projects();
      $step = 1;
      $key = vve_cr_url_key($key);
      $origPart = $key;
      
      $where = '('.$lang.')'.Projects_Model_Projects::COLUMN_URLKEY.' = :ukey AND '.Projects_Model_Projects::COLUMN_ID.' != :idp';
      $keys = array('ukey' => $key, 'idp' => (int)$id);
      
      while ($model->where($where, $keys)->count() != 0 ) {
         $keys['ukey'] = $origPart.'-'.$step;
         $step++;
      }
      return $keys['ukey'];
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

   /**
    * Metoda pro nastavení modulu
    */
   protected function settings(&$settings,Form &$form) {
      $projectImagesGrp = $form->addGroup('titleImg',$this->tr('Nastavení titulního obrázku'));
      
      $elemSW = new Form_Element_Text('image_thumb_w', 'Šířka miniatury titulního obrázku (px)');
      $elemSW->addValidation(new Form_Validator_IsNumber());
      $elemSW->setSubLabel('Výchozí: '.VVE_IMAGE_THUMB_W.'px');
      $form->addElement($elemSW, $projectImagesGrp);
      if(isset($settings[self::PARAM_THUM_W])) {
         $form->image_thumb_w->setValues($settings[self::PARAM_THUM_W]);
      }

      $elemSH = new Form_Element_Text('image_thumb_h', 'Výška miniatury titulního obrázku (px)');
      $elemSH->addValidation(new Form_Validator_IsNumber());
      $elemSH->setSubLabel('Výchozí: '.VVE_IMAGE_THUMB_H.'px');
      $form->addElement($elemSH, $projectImagesGrp);
      if(isset($settings[self::PARAM_THUM_H])) {
         $form->image_thumb_h->setValues($settings[self::PARAM_THUM_H]);
      }

      $elemSC = new Form_Element_Checkbox('image_thumb_c', 'Ořezávat miniaturu titulního obrázku');
      $elemSC->setValues(true);
      if(isset($settings[self::PARAM_THUM_C])) {
         $elemSC->setValues($settings[self::PARAM_THUM_C]);
      }
      $form->addElement($elemSC, $projectImagesGrp);
      
      
      $elemW = new Form_Element_Text('image_w', 'Šířka titulního obrázku (px)');
      $elemW->addValidation(new Form_Validator_IsNumber());
      $elemW->setSubLabel('Výchozí: '.VVE_DEFAULT_PHOTO_W.'px');
      $form->addElement($elemW, $projectImagesGrp);
      if(isset($settings[self::PARAM_BIG_W])) {
         $form->image_w->setValues($settings[self::PARAM_BIG_W]);
      }

      $elemH = new Form_Element_Text('image_h', 'Výška titulního obrázku (px)');
      $elemH->addValidation(new Form_Validator_IsNumber());
      $elemH->setSubLabel('Výchozí: '.VVE_DEFAULT_PHOTO_H.'px');
      $form->addElement($elemH, $projectImagesGrp);
      if(isset($settings[self::PARAM_BIG_H])) {
         $form->image_h->setValues($settings[self::PARAM_BIG_H]);
      }

      $phCtrl = new Photogalery_Controller($this);
      $phCtrl->settings($settings, $form);
      $form->removeElement('tplMain');
      
      
      // znovu protože mohl být už jednou validován bez těchto hodnot
      if($form->isValid()) {
         $settings[self::PARAM_THUM_W] = $form->image_thumb_w->getValues();
         $settings[self::PARAM_THUM_H] = $form->image_thumb_h->getValues();
         $settings[self::PARAM_THUM_C] = $form->image_thumb_c->getValues();
         
         $settings[self::PARAM_BIG_W] = $form->image_w->getValues();
         $settings[self::PARAM_BIG_H] = $form->image_h->getValues();
      }
   }
}
