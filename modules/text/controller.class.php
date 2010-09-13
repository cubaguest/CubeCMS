<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 */

class Text_Controller extends Controller {
   const TEXT_MAIN_KEY = 'main';
   const TEXT_PANEL_KEY = 'panel';
   const TEXT_PRIVATE_KEY = 'private';

   const PARAM_ALLOW_PRIVATE = 'allow_private';


   /**
 * Kontroler pro zobrazení textu
 */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();
      $model = new Text_Model();
      $modelPrivate = new Text_Model_Private();
      // text
      $text = $model->getText($this->category()->getId(),self::TEXT_MAIN_KEY);

      if($this->category()->getParam(self::PARAM_ALLOW_PRIVATE, false)== true AND Auth::isLogin()){
         $textPrivate = $model->getText($this->category()->getId(),self::TEXT_PRIVATE_KEY);

         if(Auth::getGroupName() == 'admin' OR $modelPrivate->haveGroup($textPrivate->{Text_Model::COLUMN_ID}, Auth::getGroupId())
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
      
      $label = new Form_Element_Text('label', $this->_('Nadpis'));
      $label->setSubLabel($this->_('Doplní se namísto nadpisu stránky'));
      $label->setLangs();
      $form->addElement($label);

      $textarea = new Form_Element_TextArea('text', $this->_("Text"));
      $textarea->setLangs();
      $textarea->addValidation(new Form_Validator_NotEmpty(null, Locales::getDefaultLang(true)));
      $form->addElement($textarea);

      $model = new Text_Model_Detail();
      $text = $model->getText($this->category()->getId(), self::TEXT_MAIN_KEY);
      if($text != false){
         $form->text->setValues($text->{Text_Model_Detail::COLUMN_TEXT});
         $form->label->setValues($text->{Text_Model_Detail::COLUMN_LABEL});
      }

      $submit = new Form_Element_Submit('send', $this->_("Uložit"));
      $form->addElement($submit);

      if($form->isValid()){
         try {
            $model->saveText($form->text->getValues(), $form->label->getValues(),
                    $this->category()->getId(), self::TEXT_MAIN_KEY);
            $this->log('úprava textu');
            $this->infoMsg()->addMessage($this->_('Text byl uložen'));
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

      $textarea = new Form_Element_TextArea('text', $this->_("Text"));
      $textarea->setLangs();
      $form->addElement($textarea);

      $model = new Text_Model_Detail();
      $text = $model->getText($this->category()->getId(), self::TEXT_PANEL_KEY);
      if($text != false){
         $form->text->setValues($text->{Text_Model_Detail::COLUMN_TEXT});
      }

      $submit = new Form_Element_Submit('send', $this->_("Uložit"));
      $form->addElement($submit);

      if($form->isValid()){
         try {
            $model->saveText($form->text->getValues(), null, $this->category()->getId(),self::TEXT_PANEL_KEY);
            $this->log('Úprava textu panelu');
            $this->infoMsg()->addMessage($this->_('Text panelu byl uložen'));
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

      $grpText = $form->addGroup('text', $this->_('Text'));

      $textarea = new Form_Element_TextArea('text', $this->_("Text"));
      $textarea->setLangs();
      $form->addElement($textarea, $grpText);

      $text = $model->getText($this->category()->getId(), self::TEXT_PRIVATE_KEY);
      if($text != false){
         $form->text->setValues($text->{Text_Model_Detail::COLUMN_TEXT});
      }

      $grpAccess = $form->addGroup('access', $this->_('Přístupy'), $this->_('Uživatelé nebo skupiny které uvidí privátní text. Stačí vybrat skupinu.'));
      // groups
      $elemGroups = new Form_Element_Select('groups', $this->_('Skupiny'));
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
      $elemUsers = new Form_Element_Select('users', $this->_('Uživatelé'));
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

      $submit = new Form_Element_Submit('send', $this->_("Uložit"));
      $form->addElement($submit);

      if($form->isValid()){
         $id = $model->saveText($form->text->getValues(), null,
                    $this->category()->getId(), self::TEXT_PRIVATE_KEY);
         $this->log('Úprava privátního textu');
         // uložíme skupiny
         $modelPrivate->saveGroupsConnect($id, $form->groups->getValues());
         // uložíme uživatele
         $modelPrivate->saveUsersConnect($id, $form->users->getValues());
         $this->infoMsg()->addMessage($this->_('Privátní text byl uložen'));
         $this->link()->route()->reload();
      }
      // view
      $this->view()->form = $form;
   }

   public function textController() {}

   public static function  settingsController(&$settings, Form &$form) {
      $fGrpPrivate = $form->addGroup('privateZone', 'Privátní zóna', "Privátní zóna povoluje
         vložení textů, které jsou viditelné pouze vybraným uživatelům. U každého článku tak
         vznikne další textové okno s výběrem uživatelů majících přístup k těmto textům.");

      $elemAllowPrivateZone = new Form_Element_Checkbox('allow_private_zone',
              'Povolit privátní zónu');
      $form->addElement($elemAllowPrivateZone, $fGrpPrivate);
      if(isset($settings[self::PARAM_ALLOW_PRIVATE])) {
         $form->allow_private_zone->setValues($settings[self::PARAM_ALLOW_PRIVATE]);
      }

      // znovu protože mohl být už jednou validován bez těchto hodnot
      if($form->isValid()) {
         $settings[self::PARAM_ALLOW_PRIVATE] = $form->allow_private_zone->getValues();
      }
   }
}

?>