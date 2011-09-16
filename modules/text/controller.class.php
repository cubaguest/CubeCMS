<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 */

class Text_Controller extends Controller {
   const TEXT_MAIN_KEY = 'main';
   const TEXT_PANEL_KEY = 'panel';
   const TEXT_PRIVATE_KEY = 'private';

   const PARAM_ALLOW_PRIVATE = 'allow_private';
   const PARAM_EDITOR_TYPE = 'editor';
   const PARAM_ALLOW_SCRIPT_IN_TEXT = 'allow_script';
   const PARAM_TPL_MAIN = 'tplmain';
   const PARAM_TPL_PANEL = 'tplpanel';

   /**
    *
    * @var Text_Model
    */
   private $textModel = null;

   protected function init()
   {
      parent::init();
      $this->textModel = new Text_Model();
   }

   /**
 * Kontroler pro zobrazení textu
 */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();
      $this->exportTextController();
   }

   public function exportTextController() {
      $this->checkReadableRights();

      $model = new Text_Model();
      $modelPrivate = new Text_Model_Private();
      
      // text
      $text = $this->loadData(self::TEXT_MAIN_KEY);
      $this->view()->text = $text;
      
      if($text != false AND $this->category()->getParam(self::PARAM_ALLOW_PRIVATE, false)== true AND Auth::isLogin()){
         $textPrivate = $this->loadData(self::TEXT_PRIVATE_KEY);

         if($this->category()->getRights()->isControll() OR $modelPrivate->haveGroup($textPrivate->{Text_Model::COLUMN_ID}, Auth::getGroupId())
            OR $modelPrivate->haveUser($textPrivate->{Text_Model::COLUMN_ID}, Auth::getUserId())){
               $this->view()->textPrivate = $textPrivate;
         }
      }
   }

   public function contentController() {
      $this->mainController();
   }

   /**
    * Kontroler pro editaci textu
    */
   public function editController() {
      $this->checkWritebleRights();

      $textRec = $this->loadData(self::TEXT_MAIN_KEY);
      $form = $this->createEditForm($textRec);

      if($form->isSend() AND $form->send->getValues() == false){
         $this->infoMsg()->addMessage($this->tr('Změny byly zrušeny'));
         $this->link()->route()->reload();
      }

      if($form->isValid()){
         try {
            // odtranění script, nebezpečných tagů a komentřů
            $this->processFormData($form, $textRec, self::TEXT_MAIN_KEY);
            $this->log('úprava textu');
            $this->infoMsg()->addMessage($this->tr('Text byl uložen'));
            $this->link()->route()->reload();
         } catch (PDOException $e) {
            CoreErrors::addException($e);
         }
      }
      // view
      $this->view()->form = $form;
   }
   
   /**
    * Metoda vrací objek záznamu
    * @param const $subkey 
    * @return Model_ORM_Record
    */
   private function loadData($subkey = self::TEXT_MAIN_KEY)
   {
      $textRecord = $this->textModel->where(Text_Model::COLUMN_ID_CATEGORY.' = :idc AND '.Text_Model::COLUMN_SUBKEY.' = :subkey',
         array('idc' => $this->category()->getId(), 'subkey' => $subkey))
         ->record();
      return $textRecord;
   }

   /**
    *
    * @param Model_ORM_Record $rec
    * @return Form 
    */
   private function createEditForm(Model_ORM_Record $rec)
   {
      $form = new Form("text_");
      
      $grpText = $form->addGroup('text', $this->tr('Text'));
      
      $label = new Form_Element_Text('label', $this->tr('Nadpis'));
      $label->addFilter(new Form_Filter_StripTags());
      $label->setSubLabel($this->tr('Doplní se namísto nadpisu stránky'));
      $label->setLangs();
      $form->addElement($label, $grpText);

      $textarea = new Form_Element_TextArea('text', $this->tr("Text"));
      $textarea->setLangs();
      $textarea->addValidation(new Form_Validator_NotEmpty(null, Locales::getDefaultLang(true)));
      $form->addElement($textarea, $grpText);

      if($rec != false){
         $form->text->setValues($rec->{Text_Model::COLUMN_TEXT});
         $form->label->setValues($rec->{Text_Model::COLUMN_LABEL});
      }

      $submit = new Form_Element_SaveCancel('send');
      $form->addElement($submit);
      
      return $form;
   }

   protected function processFormData(Form $form, $textRec, $subkey = self::TEXT_MAIN_KEY)
   {
      $text = vve_strip_html_comment($form->text->getValues());
      if ($this->category()->getParam(self::PARAM_ALLOW_SCRIPT_IN_TEXT, false) == false) {
         foreach ($text as $lang => $t) {
            $text[$lang] = preg_replace(array('@<script[^>]*?.*?</script>@siu'), array(''), $t);
         }
      }

      if ($textRec == false) {
         $textRec = $model->newRecord();
         $textRec->{Text_Model::COLUMN_ID_CATEGORY} = $this->category()->getId();
         $textRec->{Text_Model::COLUMN_SUBKEY} = $subkey;
      }
      $textRec->{Text_Model::COLUMN_TEXT} = $text;
      $textRec->{Text_Model::COLUMN_TEXT_CLEAR} = vve_strip_tags($text);
      if(isset($form->label)){
         $textRec->{Text_Model::COLUMN_LABEL} = $form->label->getValues();
      }
      $this->textModel->save($textRec);
   }

   /**
    * Kontroler pro editaci textu
    */
   public function editPanelController() {
      $this->checkWritebleRights();
      
      $textRec = $this->loadData(self::TEXT_PANEL_KEY);
      $form = $this->createEditForm($textRec);
      unset ($form->label);

      if($form->isSend() AND $form->send->getValues() == false){
         $this->infoMsg()->addMessage($this->tr('Změny byly zrušeny'));
         $this->link()->route()->reload();
      }

      if($form->isValid()){
         try {
            $this->processFormData($form, $textRec, self::TEXT_PANEL_KEY);
            $this->log('Úprava textu panelu');
            $this->infoMsg()->addMessage($this->tr('Text panelu byl uložen'));
            $this->link()->route()->reload();
         } catch (PDOException $e) {
            new CoreErrors($e);
         }
      }
      // view
      $this->view()->template()->form = $form;
   }

   public function editPrivateController() {
      $this->checkWritebleRights();
      
      $modelPrivate = new Text_Model_Private();
      
      $textRec = $this->loadData(Text_Controller::TEXT_PRIVATE_KEY);
      
      $form = $this->createEditForm($textRec);
      unset ($form->label);
      
      $grpAccess = $form->addGroup('access', $this->tr('Přístupy'), 
         $this->tr('Uživatelé nebo skupiny které uvidí privátní text. Stačí vybrat skupinu.'), 0);
      // groups
      $elemGroups = new Form_Element_Select('groups', $this->tr('Skupiny'));
      $elemGroups->setMultiple(true);
      
      $groupsModel = new Model_Groups();
      $groups = $groupsModel->groupsForThisWeb()->records();
      foreach ($groups as $grp) {
          $elemGroups->setOptions(array($grp->{Model_Users::COLUMN_GROUP_LABEL}.'('.$grp->{Model_Users::COLUMN_GROUP_NAME}.')' 
          => $grp->{Model_Users::COLUMN_GROUP_ID}), true);
      }
      if($textRec != false){
         $selGrps = $modelPrivate->getGroupsConnect($textRec->{Text_Model::COLUMN_ID});
         foreach ($selGrps as $grp) {
            $elemGroups->setValues($grp->{Text_Model_Private::COLUMN_T_H_G_ID_GROUP},$grp->{Text_Model_Private::COLUMN_T_H_G_ID_GROUP});
         }
      }
      $form->addElement($elemGroups, $grpAccess);
      // users
      $elemUsers = new Form_Element_Select('users', $this->tr('Uživatelé'));
      $elemUsers->setMultiple(true);
      
      $modelUsers = new Model_Users();
      $users = $modelUsers->usersForThisWeb()->records(PDO::FETCH_OBJ);
      foreach ($users as $usr) {
          $elemUsers->setOptions(array($usr->{Model_Users::COLUMN_NAME} ." ".$usr->{Model_Users::COLUMN_SURNAME}
              .' ('.$usr->{Model_Users::COLUMN_USERNAME}.') - '.$usr->{Model_Users::COLUMN_GROUP_LABEL}.' ('.$usr->{Model_Users::COLUMN_GROUP_NAME}.')'
              => $usr->{Model_Users::COLUMN_ID}), true);
      }
      if($textRec != false){
         $selUsrs = $modelPrivate->getUsersConnect($textRec->{Text_Model::COLUMN_ID});
         foreach ($selUsrs as $usr) {
            $elemUsers->setValues($usr->{Text_Model_Private::COLUMN_T_H_U_ID_USER},$usr->{Text_Model_Private::COLUMN_T_H_U_ID_USER});
         }
      }

      $form->addElement($elemUsers, $grpAccess);

      $submit = new Form_Element_SaveCancel('send');
      $form->addElement($submit);

      if($form->isSend() AND $form->send->getValues() == false){
         $this->infoMsg()->addMessage($this->tr('Změny byly zrušeny'));
         $this->link()->route()->reload();
      }

      if($form->isValid()){
         try {
            
            $this->processFormData($form, $textRec, self::TEXT_PRIVATE_KEY);
            $this->log('Úprava privátního textu');
            // uložíme skupiny
            $modelPrivate->saveGroupsConnect($textRec->{Text_Model::COLUMN_ID}, $form->groups->getValues());
            // uložíme uživatele
            $modelPrivate->saveUsersConnect($textRec->{Text_Model::COLUMN_ID}, $form->users->getValues());
            $this->infoMsg()->addMessage($this->tr('Privátní text byl uložen'));
            $this->link()->route()->reload();
         } catch (PDOException $exc) {
            new CoreErrors($e);
         }
      }
      // view
      $this->view()->form = $form;
   }

   public function settings(&$settings, Form &$form) {
      $fGrpViewSet = $form->addGroup('view', $this->tr('Nastavení vzhledu'));

      $componentTpls = new Component_ViewTpl();
      $componentTpls->setConfig(Component_ViewTpl::PARAM_MODULE, 'text');

      $elemTplMain = new Form_Element_Select('tplMain', $this->tr('Hlavní šablona'));
      $elemTplMain->setOptions(array_flip($componentTpls->getTpls()));
      if(isset($settings[self::PARAM_TPL_MAIN])) {
         $elemTplMain->setValues($settings[self::PARAM_TPL_MAIN]);
      }
      $form->addElement($elemTplMain, $fGrpViewSet);
      unset ($componentTpls);

      $fGrpEditSet = $form->addGroup('editSettings', $this->tr('Nastavení úprav'));

      $elemEditorType = new Form_Element_Select('editor_type', $this->tr('Typ editoru'));
      $elemEditorType->setOptions(array(
         $this->tr('žádný (pouze textová oblast)') => 'none',
         $this->tr('jednoduchý (Wysiwyg)') => 'simple',
         $this->tr('pokročilý (Wysiwyg)') => 'advanced',
         $this->tr('kompletní (Wysiwyg)') => 'full'
      ));
      $elemEditorType->setValues('advanced');
      if(isset($settings[self::PARAM_EDITOR_TYPE])) {
         $elemEditorType->setValues($settings[self::PARAM_EDITOR_TYPE]);
      }

      $form->addElement($elemEditorType, $fGrpEditSet);

      $elemAllowScripts = new Form_Element_Checkbox('allow_script', $this->tr('Povolit scripty v textu'));
      $elemAllowScripts->setSubLabel($this->tr('Umožňuje vkládání javascriptů přímo do textu. POZOR! Lze tak vložit útočníkův kód do stránek. (Filtrují se všechny javascripty.)'));
      $elemAllowScripts->setValues(false);
      if(isset($settings[self::PARAM_ALLOW_SCRIPT_IN_TEXT])) {
         $elemAllowScripts->setValues($settings[self::PARAM_ALLOW_SCRIPT_IN_TEXT]);
      }
      $form->addElement($elemAllowScripts, $fGrpEditSet);

      $fGrpPrivate = $form->addGroup('privateZone', $this->tr('Privátní zóna'), $this->tr("Privátní zóna povoluje
         vložení textů, které jsou viditelné pouze vybraným uživatelům. U každého článku tak
         vznikne další textové okno s výběrem uživatelů majících přístup k těmto textům."));

      $elemAllowPrivateZone = new Form_Element_Checkbox('allow_private_zone',
              $this->tr('Povolit privátní zónu'));
      $form->addElement($elemAllowPrivateZone, $fGrpPrivate);
      if(isset($settings[self::PARAM_ALLOW_PRIVATE])) {
         $form->allow_private_zone->setValues((bool)$settings[self::PARAM_ALLOW_PRIVATE]);
      }
      // znovu protože mohl být už jednou validován bez těchto hodnot
      if($form->isValid()) {
         $settings[self::PARAM_ALLOW_PRIVATE] = $form->allow_private_zone->getValues();
         $settings[self::PARAM_EDITOR_TYPE] = $form->editor_type->getValues();
         $settings[self::PARAM_ALLOW_SCRIPT_IN_TEXT] = $form->allow_script->getValues();
         $settings[self::PARAM_TPL_MAIN] = $form->tplMain->getValues();
      }
   }
}

?>