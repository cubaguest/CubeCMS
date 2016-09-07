<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 */

class TextStatic_Controller extends Controller {

   const PARAM_TPL = 'tplfile';

   /**
    *
    * @var Text_Model
    */
   protected $textModel = null;

   /**
 * Kontroler pro zobrazení textu
 */
   public function mainController() 
   {
      //		Kontrola práv
      $this->checkReadableRights();
      $this->view()->texts = self::getTexts($this->category());
   }
   
   public static function getTexts(Category_Core $category)
   {
      $fileds = $category->getParam('fields');
      $tr = new Translator();
      if(!$fileds){
         throw new Exception($tr->tr('Kategorie není nastavena. Kontaktujte webmastera.'));
      }
      
      // load fields
      $loadFields = array_keys($fileds);
      $toView = array();
      
      foreach ($fileds as $key => $desc) {
         $text = Text_Model::getNewRecord();
         $text->{Text_Model::COLUMN_LABEL} = $desc['name'];
         $text->tinymce = $desc['tinymce']; // kvůli přenosu do viewru a vytvoření formu
         $text->{Text_Model::COLUMN_SUBKEY} = $key;
         $toView[$key] = $text;
      }
      $texts = Text_Model::getTextsByKeys($category->getId(), $loadFields, true);
      foreach ($texts as $text) {
         $key = (string)$text->{Text_Model::COLUMN_SUBKEY};
         if(isset($toView[$key])){
            $toView[(string)$text->{Text_Model::COLUMN_SUBKEY}] = $text;
            $text->{Text_Model::COLUMN_LABEL} = $fileds[$key]['name'];
            $text->tinymce = $fileds[$key]['tinymce'];
         }
      }
      
      return $toView;
   }
   

   /**
    * Kontroler pro editaci textu
    */
   public function editController() 
   {
      $this->checkWritebleRights();
      
      $texts = self::getTexts($this->category());
      
      $f = new Form('stext_');
      
      foreach ($texts as $text) {
         if($text->tinymce === false){
            $e = new Form_Element_Text($text->{Text_Model::COLUMN_SUBKEY}, $text->{Text_Model::COLUMN_LABEL});
         } else {
            $e = new Form_Element_TextArea($text->{Text_Model::COLUMN_SUBKEY}, $text->{Text_Model::COLUMN_LABEL});
         }
         $e->setLangs();
         $e->setValues($text->{Text_Model::COLUMN_TEXT});
         $f->addElement($e);
      }
      
      $f->addElement(new Form_Element_SaveCancel('save'));

      if($f->isSend() && $f->save->getValues() == false){
         $this->link()->route()->redirect();
      }
      if($f->isValid()){
         foreach ($texts as $key => $obj) {
            $obj->{Text_Model::COLUMN_TEXT} = $f->{$key}->getValues();
            $obj->save();
         }
         $this->infoMsg()->addMessage($this->tr('Texty byly uloženy'));
         $this->link()->route()->redirect();
      }
      // view
      $this->view()->texts = $texts;
      $this->view()->form = $f;
   }
   
   /**
    * Metoda vrací objek záznamu
    * @param const $subkey 
    * @return Model_ORM_Record
    */
   protected function loadData($subkey = self::TEXT_MAIN_KEY, $loadAllLangs = true)
   {
      $textRecord = $this->textModel->where(Text_Model::COLUMN_ID_CATEGORY.' = :idc AND '.Text_Model::COLUMN_SUBKEY.' = :subkey',
         array('idc' => $this->category()->getId(), 'subkey' => $subkey))
         ->joinFK(Text_Model::COLUMN_ID_USER_EDIT)
         ->setSelectAllLangs($loadAllLangs)->record();
      return $textRecord;
   }

   protected function loadDataByKeys($keys, $loadAllLangs = true)
   {
      $forWhere = array();
      foreach($keys as $id => $key){
         $forWhere[':key_'.$id] = $key;
      }

      $tmp = $this->textModel->where(Text_Model::COLUMN_ID_CATEGORY.' = :idc AND '.Text_Model::COLUMN_SUBKEY.' IN ('.implode(', ',array_keys($forWhere)).')',
         array_merge(array('idc' => $this->category()->getId()), $forWhere))
         ->joinFK(Text_Model::COLUMN_ID_USER_EDIT)
         ->setSelectAllLangs($loadAllLangs)
         ->records();

      $records = array();
      foreach($tmp as $rec){
         $records[$rec->{Text_Model::COLUMN_SUBKEY}] = $rec;
      }

      return $records;
   }

   /**
    *
    * @param Model_ORM_Record $rec
    * @return Form 
    */
   protected function createEditForm($rec, $customFields = array(), $mainText = false)
   {
      $form = new Form("text_");
      
      $grpText = $form->addGroup('text', $this->tr('Text'));
      $grpView = $form->addGroup('view', $this->tr('Vzhed'));
      
      if(($this->category() instanceof Category_Admin) == false){
         $label = new Form_Element_Text('label', $this->tr('Nadpis'));
         $label->addFilter(new Form_Filter_StripTags());
         $label->setSubLabel($this->tr('Doplní se namísto nadpisu stránky'));
         $label->setLangs();
         $form->addElement($label, $grpText);
      }

      $textarea = new Form_Element_TextArea('text', $this->tr("Text"));
      $textarea->setLangs();
      $textarea->addValidation(new Form_Validator_NotEmpty(null, Locales::getDefaultLang(true)));
      $form->addElement($textarea, $grpText);
      
      // custom fileds
      if($fields = $this->customFields){
         foreach($fields as $field => $label){
            if(isset($this->customFieldsTypes[$field]) && $this->customFieldsTypes[$field] == 'text'){
               $elem = new Form_Element_Text('filed_'.$field, $label);
            } else {
               $elem = new Form_Element_TextArea('filed_'.$field, $label);
            }
            $elem->setLangs();
            if(isset($customFields[$field])){
               $elem->setValues($customFields[$field]->{Text_Model::COLUMN_TEXT});
            }
            $form->addElement($elem, $grpText);
         }
         $this->view()->fields = array_keys($this->customFields);
      }
      
      if($mainText && ($this->category() instanceof Category_Admin) == false){
         $perex = new Form_Element_TextArea('desc', $this->tr("Popis"));
         $perex->setLangs();
         $perex->setSubLabel($this->tr('Krátký popisek. Bývá uveden v přehledech a pro vyhledávače.'));
         $form->addElement($perex, $grpText);
         /* titulní obrázek */
         if(Face::getCurrent()->getParam('category_title_image', null, true)){
            $elemImage = new Form_Element_ImageSelector('image', $this->tr('Titulní obrázek'));
            $elemImage->setUploadDir(Category::getImageDir(Category::DIR_IMAGE, true));
            $elemImage->setValues($this->category()->getCatDataObj()->{Model_Category::COLUMN_ICON});
            $form->addElement($elemImage, $grpView);
         }
      }
      
      if($rec instanceof Model_ORM_Record){
         $form->text->setValues($rec->{Text_Model::COLUMN_TEXT});
         if(isset($form->label)){
            $form->label->setValues($rec->{Text_Model::COLUMN_LABEL});
         }
         if($mainText && isset($form->desc)){
            $form->desc->setValues( isset($rec->catdesc) ? $rec->catdesc : $this->category()->getDataObj()->{Model_Category::COLUMN_DESCRIPTION});
            if(isset($form->image)){
               $form->image->setValues( isset($rec->catimg) ? $rec->catimg : $this->category()->getDataObj()->{Model_Category::COLUMN_IMAGE});
            }
         }
      }

      $submits = new Form_Element_Multi_Submit('send');
      $eCancel = new Form_Element_Submit('cancel', $this->tr('Zrušit'));
      $eCancel->html()->addClass('btn-danger');
      $submits->addElement($eCancel);
      $eSave = new Form_Element_Submit('save', $this->tr('Uložit'));
      $eSave->html()->addClass('btn-success');
      $submits->addElement($eSave);
      if(($this->category() instanceof Category_Admin) == false) {
         $ePreview = new Form_Element_Submit('preview', $this->tr('Náhled'));
         $submits->addElement($ePreview);
      }
      $form->addElement($submits);
      
      return $form;
   }

   /**
    * Metoda zpracuje data a uloží je do modelu
    * @param Form $form -- objekt formuláře
    * @param type $textRec -- objekt záznamu v db
    * @param type $subkey -- subklíč dat
    * @param type $elementName -- název elementu s daty
    */
   protected function processFormData(Form $form, $textRec, $subkey = Text_Model::TEXT_MAIN_KEY, $elementName = 'text', $toTemp = false)
   {
      if(!isset ($form->{$elementName})){
         throw new InvalidArgumentException($this->tr('Nebyl předán správný název formulářového prvku s daty'));
      }
      $text = vve_strip_html_comment($form->{$elementName}->getValues());
      if ($this->category()->getParam(self::PARAM_ALLOW_SCRIPT_IN_TEXT, false) == false) {
         foreach ($text as $lang => $t) {
            $text[$lang] = preg_replace(array('@<script[^>]*?.*?</script>@siu'), array(''), $t);
         }
      }

      if ($textRec == false) {
         $textRec = $this->textModel->newRecord();
      }
      if($textRec->isNew()){
         $textRec->{Text_Model::COLUMN_ID_CATEGORY} = $this->category()->getId();
         $textRec->{Text_Model::COLUMN_SUBKEY} = $subkey;
      }
      $textRec->{Text_Model::COLUMN_TEXT} = $text;
      $textRec->{Text_Model::COLUMN_TEXT_CLEAR} = vve_strip_tags($text);
      if(isset($form->label)){
         $textRec->{Text_Model::COLUMN_LABEL} = $form->label->getValues();
      }
      $textRec->{Text_Model::COLUMN_ID_USER_EDIT} = Auth::getUserId();
      if(isset($form->desc)){
         $textRec->catdesc = $form->desc->getValues();
      }
      if($subkey == Text_Model::TEXT_MAIN_KEY && isset($form->image)){
         $img = $form->image->getValues();
         $textRec->catimage = $img ? $img['name'] : null;
      }
      $textRec->category = Auth::getUserId();

      if($toTemp){
         $this->seveTempRecord($textRec, $subkey);
      } else {
         $textRec->save();
         if($subkey == Text_Model::TEXT_MAIN_KEY && !$this->category() instanceof Category_Admin){
            $cat = $this->category()->getDataObj();
            if($cat instanceof Model_ORM_Record){
               $cat->{Model_Category::COLUMN_DESCRIPTION} = $textRec->catdesc;
               $cat->{Model_Category::COLUMN_IMAGE} = $textRec->catimage;
               $cat->save();
            }
         }
      }
      return $textRec;
   }

   protected function processTempData($record)
   {
      $record->save();
      $this->clearTempRecord($record->{Text_Model::COLUMN_SUBKEY});
      if($record->{Text_Model::COLUMN_SUBKEY} == Text_Model::TEXT_MAIN_KEY){
         $cat = $this->category()->getDataObj();
         if($cat instanceof Model_ORM_Record){
            $cat->{Model_Category::COLUMN_DESCRIPTION} = $record->catdesc;
            $cat->{Model_Category::COLUMN_IMAGE} = $record->catimage;
            $cat->save();
         }
      }
   }
   
   protected function loadTempRecord($key = Text_Model::TEXT_MAIN_KEY)
   {
      $f = AppCore::getAppCacheDir().$key."_prew_c".$this->category()->getId()."_u".Auth::getUserId().".tmp";
      if(is_file($f) && filesize($f) > 0){
         return unserialize(file_get_contents($f));
      }
      return false;
   }

   protected function seveTempRecord($record, $key = Text_Model::TEXT_MAIN_KEY)
   {
      $f = AppCore::getAppCacheDir().$key."_prew_c".$this->category()->getId()."_u".Auth::getUserId().".tmp";
      file_put_contents($f, serialize($record));
   }
   
   protected function clearTempRecord($key = Text_Model::TEXT_MAIN_KEY)
   {
      $f = AppCore::getAppCacheDir().$key."_prew_c".$this->category()->getId()."_u".Auth::getUserId().".tmp";
      if(is_file($f)){
         @unlink($f);
      }
   }
   
   protected function isTempRecord($key = Text_Model::TEXT_MAIN_KEY)
   {
      $f = AppCore::getAppCacheDir().$key."_prew_c".$this->category()->getId()."_u".Auth::getUserId().".tmp";
      return ( is_file($f) && filesize($f) > 0 );
   }
   
   /**
    * Kontroler pro editaci textu
    */
   public function editPanelController() 
   {
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

   public function editPrivateController() 
   {
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
            new CoreErrors($exc);
         }
      }
      // view
      $this->view()->form = $form;
   }

   public function settings(&$settings, Form &$form) 
   {
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
      }
   }

   public static function categoryDuplicate(Category_Core $oldCat, Category_Core $newCat) 
   {
      $model = new Text_Model();
      $subkeys = array(Text_Model::TEXT_MAIN_KEY, Text_Model::TEXT_PRIVATE_KEY, Text_Model::TEXT_PANEL_KEY);
      foreach ($subkeys as $subkey) {
         $record = $model->where(Text_Model::COLUMN_ID_CATEGORY." = :idc AND ".Text_Model::COLUMN_SUBKEY." = :subkey",
               array('idc' => $oldCat->getId(), 'subkey' => $subkey))->record();
         if($record && !$record->isNew()){ // @todo record() vrací i nový záznam
            $record->setNew();
            $record->{Text_Model::COLUMN_ID_CATEGORY} = $newCat->getId();
            $record->{Text_Model::COLUMN_ID_USER_EDIT} = Auth::getUserId();
            $record->save();
         }
      }
   }
   
   /* TOHEL JE NAPROSTÁ KRAVINA. VYMAZAT!! */
   protected static function setTextData(Model_ORM_Record $record, $data) 
   {
      $model = new Text_Model();
      $record->{Text_Model::COLUMN_DATA} = serialize($data);
   }
   
   protected static function getTextData(Model_ORM_Record $record) 
   {
      if($record->{Text_Model::COLUMN_DATA} != null){
         return unserialize($record->{Text_Model::COLUMN_DATA});
      }
      return array();
   }
}

