<?php

/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 */
class QuickTools_Controller extends Controller {
   const TEXT_MAIN_KEY = 'main';
   const DEFAUL_NESTED_LEVEL = 2;

   const PARAM_NESTED_LEVEL = 'allow_private';
   const PARAM_EDITOR_TYPE = 'editor';
   const PARAM_ALLOW_SCRIPT_IN_TEXT = 'allow_script';
   const PARAM_TPL_MAIN = 'tplmain';
   const PARAM_TPL_PANEL = 'tplpanel';

   /**
    * Kontroler pro zobrazení textu
    */
   public function mainController()
   {
      //		Kontrola práv
      $this->checkReadableRights();
      
      $this->view()->tools = self::getAllTools();
   }
   
   public function addToolController()
   {
      $form = $this->createForm();
      
      if($form->isSend() AND $form->save->getValues() == false){
         $this->infoMsg()->addMessage($this->tr('Změny byly zrušeny'));
         $this->link()->route()->reload();
      }
      
      if($form->isValid()){
         $model = new QuickTools_Model();
         $newRec = $model->newRecord();
         
         $newRec->{QuickTools_Model::COLUMN_NAME} = $form->name->getValues();
         $newRec->{QuickTools_Model::COLUMN_ICON} = $form->icon->getValues();
         $newRec->{QuickTools_Model::COLUMN_URL} = $form->url->getValues();
         $newRec->{QuickTools_Model::COLUMN_ID_USER} = Auth::getUserId();
         
         $model->save($newRec);
         
         $this->infoMsg()->addMessage($this->tr('Nástroj byl přidán'));
         $this->link()->route()->reload();
      }
      
      $this->view()->form = $form;
   }

   
   private function createForm()
   {
      $form = new Form('tool_');
      
      $elemName = new Form_Element_Text('name', $this->tr('Název nástroje'));
      $elemName->addValidation(new Form_Validator_NotEmpty());
      $form->addElement($elemName);
      
      $elemUrl = new Form_Element_Text('url', $this->tr('Adresa akce'));
      $elemUrl->setSubLabel($this->tr('Adresa, kam se má přejít po kliknutí. Například adresa přidání novinky.'));
      $elemUrl->addValidation(new Form_Validator_NotEmpty());
//      $elemUrl->addValidation(new Form_Validator_Url());
      $form->addElement($elemUrl);
      
      $elemIcon = new Form_Element_Select('icon', $this->tr('Ikona'));

      $icons = array();
      foreach (new DirectoryIterator(AppCore::getAppWebDir().'images'.DIRECTORY_SEPARATOR.'icons') as $fileInfo) {
         if($fileInfo->isFile() AND preg_match('#^(.+?)(_t)?\.(jpg|gif|png)#i', $fileInfo->getFilename()) ){
            $icons[$fileInfo->getFilename()] = 'images/icons/'.$fileInfo->getFilename();
         }
      }

      ksort($icons);

      $elemIcon->setOptions($icons, true);

      // pokud existuje složka s ikonama ve face tak projít i tu
      $dirFace = Template::faceDir().'images'.DIRECTORY_SEPARATOR.'icons';
      if(file_exists($dirFace) AND is_dir($dirFace)){
         foreach (new DirectoryIterator($dirFace) as $fileInfo) {
            if($fileInfo->isFile() AND preg_match('#^(.+?)(_t)?\.(jpg|gif|png)#i', $fileInfo->getFilename()) ){
               $elemIcon->setOptions(array($fileInfo->getFilename() => Template::FACES_DIR.'/'.Template::face().'/images/icons/'.$fileInfo->getFilename()), true);
            }
         }
      }
      
      $form->addElement($elemIcon);
      
      
      $elemSave = new Form_Element_SaveCancel('save');
      $form->addElement($elemSave);
      
      return $form;
   }

   public static function getAllTools()
   {
      $model = new QuickTools_Model();
      $records = $model->where(QuickTools_Model::COLUMN_ID_USER.' = :idu', array('idu' => Auth::getUserId()))->order(array(QuickTools_Model::COLUMN_ORDER => Model_ORM::ORDER_ASC))->records();
      return $records;
   }

   public function settings(&$settings, Form &$form)
   {
//      $fGrpViewSet = $form->addGroup('view', $this->tr('Nastavení vzhledu'));
//
//      $eLevel = new Form_Element_Text('level', $this->tr('Zanoření'));
//      $eLevel->setSubLabel(sprintf($this->tr("Výchozí zanoření: %s"), self::DEFAUL_NESTED_LEVEL));
//      $eLevel->addValidation(new Form_Validator_IsNumber());
//
//      $form->addElement($eLevel, $fGrpViewSet);
//
//      if (isset($settings[self::PARAM_NESTED_LEVEL])) {
//         $form->level->setValues($settings[self::PARAM_NESTED_LEVEL]);
//      }
//      // znovu protože mohl být už jednou validován bez těchto hodnot
//      if ($form->isValid()) {
//         $settings[self::PARAM_NESTED_LEVEL] = $form->level->getValues();
//      }
   }

}
?>