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
      $text = $model->getText($this->category()->getId(),self::TEXT_MAIN_KEY);

      if($this->category()->getParam(self::PARAM_ALLOW_PRIVATE, false)== true AND Auth::isLogin()){
         $textPrivate = $model->getText($this->category()->getId(),self::TEXT_PRIVATE_KEY);

         if($this->category()->getRights()->isControll() OR $modelPrivate->haveGroup($textPrivate->{Text_Model::COLUMN_ID}, Auth::getGroupId())
            OR $modelPrivate->haveUser($textPrivate->{Text_Model::COLUMN_ID}, Auth::getUserId())){
               $this->view()->textPrivate = $textPrivate;
         }
      }
      $this->view()->text = $text;

   }

   public function contentController() {
      $this->mainController();
   }

   /**
    * Kontroler pro editaci textu
    */
   public function editController() {
      $this->checkWritebleRights();

      $form = new Form("text_");
      
      $label = new Form_Element_Text('label', $this->tr('Nadpis'));
      $label->addFilter(new Form_Filter_StripTags());
      $label->setSubLabel($this->tr('Doplní se namísto nadpisu stránky'));
      $label->setLangs();
      $form->addElement($label);

      $textarea = new Form_Element_TextArea('text', $this->tr("Text"));
      $textarea->setLangs();
      $textarea->addValidation(new Form_Validator_NotEmpty(null, Locales::getDefaultLang(true)));
      $form->addElement($textarea);

      $model = new Text_Model_Detail();
      $text = $model->getText($this->category()->getId(), self::TEXT_MAIN_KEY);
      if($text != false){
         $form->text->setValues($text->{Text_Model_Detail::COLUMN_TEXT});
         $form->label->setValues($text->{Text_Model_Detail::COLUMN_LABEL});
      }

      $submit = new Form_Element_SaveCancel('send');
      $form->addElement($submit);

      if($form->isSend() AND $form->send->getValues() == false){
         $this->infoMsg()->addMessage($this->tr('Změny byly zrušeny'));
         $this->link()->route()->reload();
      }

      if($form->isValid()){
         try {
            // odtranění script, nebezpečných tagů a komentřů
            $text = vve_strip_html_comment($form->text->getValues());
            if($this->category()->getParam(self::PARAM_ALLOW_SCRIPT_IN_TEXT, false) == false){
               foreach ($text as $lang => $t) {
                  $text[$lang] = preg_replace(array('@<script[^>]*?.*?</script>@siu'), array(''), $t);
               }
            }

            $model->saveText($text, $form->label->getValues(),
                    $this->category()->getId(), self::TEXT_MAIN_KEY);
            $this->log('úprava textu');
            $this->infoMsg()->addMessage($this->tr('Text byl uložen'));
            $this->link()->route()->reload();
         } catch (PDOException $e) {
            new CoreErrors($e);
         }
      }
      // view
      $this->view()->template()->form = $form;
   }

   /**
    * Kontroler pro editaci textu
    */
   public function editPanelController() {
      $this->checkWritebleRights();

      $form = new Form("text_");

      $textarea = new Form_Element_TextArea('text', $this->tr("Text"));
      $textarea->setLangs();
      $form->addElement($textarea);

      $model = new Text_Model_Detail();
      $text = $model->getText($this->category()->getId(), self::TEXT_PANEL_KEY);
      if($text != false){
         $form->text->setValues($text->{Text_Model_Detail::COLUMN_TEXT});
      }

      $submit = new Form_Element_SaveCancel('send');
      $form->addElement($submit);

      if($form->isSend() AND $form->send->getValues() == false){
         $this->infoMsg()->addMessage($this->tr('Změny byly zrušeny'));
         $this->link()->route()->reload();
      }

      if($form->isValid()){
         try {
            // odtranění script, nebezpečných tagů a komentřů
            $text = vve_strip_html_comment($form->text->getValues());
            if($this->category()->getParam(self::PARAM_ALLOW_SCRIPT_IN_TEXT, false) == false){
               foreach ($text as $lang => $t) {
                  $text[$lang] = preg_replace(array('@<script[^>]*?.*?</script>@siu'), array(''), $t);
               }
            }
            $model->saveText($form->text->getValues(), null, $this->category()->getId(),self::TEXT_PANEL_KEY);
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
      $modelUsers = new Model_Users();
      $model = new Text_Model();
      $modelPrivate = new Text_Model_Private();
      
      $form = new Form("text_");

      $grpText = $form->addGroup('text', $this->tr('Text'));

      $textarea = new Form_Element_TextArea('text', $this->tr("Text"));
      $textarea->setLangs();
      $form->addElement($textarea, $grpText);

      $text = $model->getText($this->category()->getId(), self::TEXT_PRIVATE_KEY);
      if($text != false){
         $form->text->setValues($text->{Text_Model_Detail::COLUMN_TEXT});
      }

      $grpAccess = $form->addGroup('access', $this->tr('Přístupy'), $this->tr('Uživatelé nebo skupiny které uvidí privátní text. Stačí vybrat skupinu.'));
      // groups
      $elemGroups = new Form_Element_Select('groups', $this->tr('Skupiny'));
      $elemGroups->setMultiple(true);
      $groups = $modelUsers->getGroups()->fetchAll(PDO::FETCH_OBJ);
      foreach ($groups as $grp) {
          $elemGroups->setOptions(array($grp->{Model_Users::COLUMN_GROUP_NAME} => $grp->{Model_Users::COLUMN_GROUP_ID}), true);
      }
      if($text != false){
         $selGrps = $modelPrivate->getGroupsConnect($text->{Text_Model::COLUMN_ID});
         foreach ($selGrps as $grp) {
            $elemGroups->setValues($grp->{Text_Model_Private::COLUMN_T_H_G_ID_GROUP},$grp->{Text_Model_Private::COLUMN_T_H_G_ID_GROUP});
         }
      }
      $form->addElement($elemGroups, $grpAccess);
      // users
      $elemUsers = new Form_Element_Select('users', $this->tr('Uživatelé'));
      $elemUsers->setMultiple(true);
      $users = $modelUsers->getUsersList()->fetchAll(PDO::FETCH_OBJ);
      foreach ($users as $usr) {
          $elemUsers->setOptions(array($usr->{Model_Users::COLUMN_USERNAME}.' ('.$usr->{Model_Users::COLUMN_NAME}
          .' '.$usr->{Model_Users::COLUMN_SURNAME}.')' => $usr->{Model_Users::COLUMN_ID}), true);
      }
      if($text != false){
         $selUsrs = $modelPrivate->getUsersConnect($text->{Text_Model::COLUMN_ID});
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
            // odtranění script, nebezpečných tagů a komentřů
            $text = vve_strip_html_comment($form->text->getValues());
            if ($this->category()->getParam(self::PARAM_ALLOW_SCRIPT_IN_TEXT, false) == false) {
               foreach ($text as $lang => $t) {
                  $text[$lang] = preg_replace(array('@<script[^>]*?.*?</script>@siu'), array(''), $t);
               }
            }
            $id = $model->saveText($form->text->getValues(), null,
                  $this->category()->getId(), self::TEXT_PRIVATE_KEY);
            $this->log('Úprava privátního textu');
            // uložíme skupiny
            $modelPrivate->saveGroupsConnect($id, $form->groups->getValues());
            // uložíme uživatele
            $modelPrivate->saveUsersConnect($id, $form->users->getValues());
            $this->infoMsg()->addMessage($this->tr('Privátní text byl uložen'));
            $this->link()->route()->reload();
         } catch (PDOException $exc) {
            new CoreErrors($e);
         }
      }
      // view
      $this->view()->form = $form;
   }

   public function settings($settings, Form $form) {
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
         $form->allow_private_zone->setValues($settings[self::PARAM_ALLOW_PRIVATE]);
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