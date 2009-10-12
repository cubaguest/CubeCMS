<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class Categories_Controller extends Controller {
   private $sections = array();
   private $sectionsArray = array();

   public function mainController() {
      $this->checkReadableRights();

      $formDelete = new Form('section_');

      $elemId = new Form_Element_Hidden('id');
      $formDelete->addElement($elemId);

      $elemCatId = new Form_Element_Hidden('cat_id');
      $formDelete->addElement($elemCatId);

      $submit = new Form_Element_Submit('remove');
      $formDelete->addElement($submit);

      if($formDelete->isValid()){
         $sections = unserialize(VVE_CATEGORIES_STRUCTURE);
//         $sections = new Menu_Sections(null);
         // mažeme kategorii
         if($formDelete->cat_id->getValues() != null){
            $sections->removeCat($formDelete->id->getValues(), $formDelete->cat_id->getValues());
            $this->infoMsg()->addMessage($this->_("Kategorie byla odebrána ze sekce"));
         }
         // mažeme sekci
         else {
            $sections->removeSec($formDelete->id->getValues());
            $this->infoMsg()->addMessage($this->_("Sekce byla smazána"));
         }
         $model = new Model_Config();
         $model->saveCfg('CATEGORIES_STRUCTURE', serialize($sections));
         $this->link()->route()->reload();
      }

      // form pro posun
      $formMove = new Form('item');

      $childKey = new Form_Element_Hidden('child_key');
      $formMove->addElement($childKey);

      $parentID = new Form_Element_Hidden('parent_id');
      $formMove->addElement($parentID);

      $moveTo = new Form_Element_Hidden('move_to');
      $formMove->addElement($moveTo);

      $submit = new Form_Element_Submit('move');
      $formMove->addElement($submit);

      if($formMove->isValid()){
         $sections = unserialize(VVE_CATEGORIES_STRUCTURE);
         $sections = new Menu_Sections(null);

         $sections->moveChild($idParent, $key, $moveTo);

//         $model = new Model_Config();
//         $model->saveCfg('CATEGORIES_STRUCTURE', serialize($sections));
         $this->link()->route()->reload();
      }

      // nastavení viewru
      $this->view()->template()->addTplFile('list.phtml');
      $this->view()->template()->addCssFile('style.css');
   }

   public function showController() {
      $this->checkReadableRights();

      $form = new Form('category_');
      $elemIdCat = new Form_Element_Hidden('id');
      $form->addElement($elemIdCat);
      $submit = new Form_Element_Submit('delete');
      $form->addElement($submit);

      if($form->isValid()) {
         $model = new Model_Category();
         $model->deleteCategory($form->id->getValues());
         $this->infoMsg()->addMessage($this->_('Kategorie byla smzána'));
         $this->link()->clear()->reload();
      }


      // nastavení viewru
      $this->view()->template()->addTplFile('detail.phtml');
      $this->view()->template()->cat = $this->getRequest('categoryid');
   }

   public function editController() {
      $form = $this->createForm();

      // načtení dat z modelu
      $model = new Model_Category();
      $cat = $model->getCategoryWoutRights($this->getRequest('categoryid'));

      //      var_dump($cat);

      $form->name->setValues($cat[Model_Category::COLUMN_CAT_LABEL]);
      $form->alt->setValues($cat[Model_Category::COLUMN_CAT_ALT]);
      $form->keywords->setValues($cat[Model_Category::COLUMN_KEYWORDS]);
      $form->description->setValues($cat[Model_Category::COLUMN_DESCRIPTION]);
//      $form->section->setValues($cat[Model_Category::COLUMN_SEC_ID]);
      $form->module->setValues($cat[Model_Category::COLUMN_MODULE]);
      $form->urlkey->setValues($cat[Model_Category::COLUMN_URLKEY]);
      $form->priority->setValues($cat[Model_Category::COLUMN_PRIORITY]);
      $form->panel_left->setValues($cat[Model_Category::COLUMN_CAT_LPANEL]);
      $form->panel_right->setValues($cat[Model_Category::COLUMN_CAT_RPANEL]);
      $form->panel_right->setValues($cat[Model_Category::COLUMN_CAT_RPANEL]);
      $form->show_when_login_only->setValues($cat[Model_Category::COLUMN_CAT_SHOW_WHEN_LOGIN_ONLY]);
      $form->show_in_menu->setValues($cat[Model_Category::COLUMN_CAT_SHOW_IN_MENU]);
      $form->sitemap_priority->setValues($cat[Model_Category::COLUMN_CAT_SITEMAP_CHANGE_PRIORITY]);
      $form->sitemap_frequency->setValues($cat[Model_Category::COLUMN_CAT_SITEMAP_CHANGE_FREQ]);

      // práva
      $rights = array();
      $usrModel = new Model_Users();
      $groups = $usrModel->getGroups();
      while ($group = $groups->fetch()) {
         $grName = Model_Category::COLUMN_GROUP_PREFIX.$group[Model_Users::COLUMN_GROUP_NAME];
         $form->{$grName}->setValues($cat[Model_Category::COLUMN_GROUP_PREFIX
             .$group[Model_Users::COLUMN_GROUP_NAME]]);
      }

      // odeslání formuláře
      if($form->isValid()) {
      // vygenerování url klíče
         $urlkey = $form->urlkey->getValues();
         $names = $form->name->getValues();
         foreach ($urlkey as $lang => $variable) {
            if($variable == null) {
               $urlkey[$lang] = vve_cr_url_key($names[$lang]);
            } else {
               $urlkey[$lang] = vve_cr_url_key($urlkey[$lang]);
            }
         }

         // práva
         $rights = array();
         $usrModel = new Model_Users();
         $groups = $usrModel->getGroups();
         while ($group = $groups->fetch()) {
            $grName = Model_Category::COLUMN_GROUP_PREFIX.$group[Model_Users::COLUMN_GROUP_NAME];
            $rights[$grName] = $form->{$grName}->getValues();
         }

         $categoryModel = new Model_Category();
         $categoryModel->saveEditCategory($this->getRequest('categoryid'), $form->name->getValues(),$form->alt->getValues(),
             $form->module->getValues(),$form->keywords->getValues(),
             $form->description->getValues(),$urlkey,$form->priority->getValues(),$form->panel_left->getValues(),
             $form->panel_right->getValues(),$form->show_in_menu->getValues(),$form->show_when_login_only->getValues(),
             $rights,$form->sitemap_priority->getValues(),$form->sitemap_frequency->getValues());

         $this->infoMsg()->addMessage('Kategorie byla uložena');
         $this->link()->route('detail', array('categoryid' => $this->getRequest('categoryid')))->reload();

      }

      $this->view()->template()->form = $form;
      $this->view()->template()->addTplFile('edit.phtml');
   }

   public function addController() {
      $form = $this->createForm();

      $form->show_in_menu->setValues(true);

      if($form->isValid()) {

      // vygenerování url klíče
         $urlkey = $form->urlkey->getValues();
         $names = $form->name->getValues();
         foreach ($urlkey as $lang => $variable) {
            if($variable == null) {
               $urlkey[$lang] = vve_cr_url_key($names[$lang]);
            } else {
               $urlkey[$lang] = vve_cr_url_key($urlkey[$lang]);
            }
         }

         // práva
         $rights = array();
         $usrModel = new Model_Users();
         $groups = $usrModel->getGroups();
         while ($group = $groups->fetch()) {
            $grName = Model_Category::COLUMN_GROUP_PREFIX.$group[Model_Users::COLUMN_GROUP_NAME];
            $rights[$grName] = $form->{$grName}->getValues();
         }

         $categoryModel = new Model_Category();
         $categoryModel->saveNewCategory($form->name->getValues(),$form->alt->getValues(),
             $form->module->getValues(),$form->keywords->getValues(),
             $form->description->getValues(),$urlkey,$form->priority->getValues(),$form->panel_left->getValues(),
             $form->panel_right->getValues(),$form->show_in_menu->getValues(),$form->show_when_login_only->getValues(),
             $rights,$form->sitemap_priority->getValues(),$form->sitemap_frequency->getValues());

         $this->infoMsg()->addMessage('Kategorie byla uložena');
         $this->link()->route()->reload();
      }

      $this->view()->template()->form = $form;
      $this->view()->template()->addTplFile('edit.phtml');
   }

   /**
    * Metoda vytvoří strukturu formuláře
    * @return Form
    */
   private function createForm() {
      $form = new Form('category');

      $form->addGroup('labels', $this->_('Popisky'), $this->_('Název a popisek kategorie'));
      // název kategorie
      $catName = new Form_Element_Text('name', $this->_('Název'));
      $catName->setLangs();
      $catName->addValidation(new Form_Validator_NotEmpty(null, Locale::getDefaultLang(true)));
      $form->addElement($catName, 'labels');

      // popisek kategorie
      $catAlt = new Form_Element_Text('alt', $this->_('Popisek'));
      $catAlt->setLangs();
      $form->addElement($catAlt, 'labels');

      // keywords
      $catKeywords = new Form_Element_Text('keywords', $this->_('Klíčová slova'));
      $catKeywords->setLangs();
      $catKeywords->setSubLabel($this->_('Pro vyhledávače'));
      $form->addElement($catKeywords, 'labels');

      // description
      $catDescription = new Form_Element_TextArea('description', $this->_('Popis kategorie'));
      $catDescription->setLangs();
      $catDescription->setSubLabel($this->_('Pro vyhledávače'));
      $form->addElement($catDescription, 'labels');

      // SETTINGS
      $form->addGroup('settings', $this->_('Nastavení'), $this->_('Položky související s nastavením kategorie'));

      // sekce
//      $secModel = new Model_Sections();
//      $sections = $secModel->getSections();
//      $options = null;
//      foreach ($sections as $section) {
//         $options[(string)$section->{Model_Sections::COLUMN_SEC_LABEL}] = $section->{Model_Sections::COLUMN_SEC_ID};
//      }
//      $catSections = new Form_Element_Select('section', $this->_('Sekce'));
//      $catSections->setOptions($options);
//      $form->addElement($catSections, 'settings');

      // moduly
      $moduleModel = new Model_Module();
      $modules = $moduleModel->getModules();
      $options = null;
      foreach ($modules as $module) {
         $options[$module] = $module;
      }
      $catModule = new Form_Element_Select('module', $this->_('Modul'));
      $catModule->setOptions($options);
      $form->addElement($catModule, 'settings');

      // url klíč kategorie
      $catUrlKey = new Form_Element_Text('urlkey', $this->_('Url klíč'));
      $catUrlKey->setLangs();
      $catUrlKey->setSubLabel($this->_('Pokud není zadán, je url klíč generován automaticky'));
      $form->addElement($catUrlKey,'settings');

      // priorita
      $catPriority = new Form_Element_Text('priority', $this->_('Priorita kategorie'));
      $catPriority->setSubLabel('Čím menší tím bude kategorie výše');
      $catPriority->addValidation(New Form_Validator_IsNumber());
      $catPriority->addValidation(New Form_Validator_Length(1, 4));
      $catPriority->setValues(0);
      $form->addElement($catPriority, 'settings');

      // panely
      $catLeftPanel = new Form_Element_Checkbox('panel_left', $this->_('Levý panel'));
      $form->addElement($catLeftPanel, 'settings');
      $catRightPanel = new Form_Element_Checkbox('panel_right', $this->_('Pravý panel'));
      $form->addElement($catRightPanel, 'settings');

      $catShowInMenu = new Form_Element_Checkbox('show_in_menu', $this->_('Zobrazit v menu'));
      $form->addElement($catShowInMenu, 'settings');

      $catShowOnlyWhenLogin = new Form_Element_Checkbox('show_when_login_only', $this->_('Zobrazit pouze při přihlášení'));
      $form->addElement($catShowOnlyWhenLogin, 'settings');

      // práva
      $form->addGroup('rights', $this->_('Práva'), $this->_('Nastavení práv ke kategorii'));
      $grModel = new Model_Users();
      $groups = $grModel->getGroups();

      // pole s typy práv
      $rightsTypes = array('r--'=>'r--', '-w-'=>'-w-', '--c'=>'--c', 'rw-'=>'rw-',
          'r-c'=>'r-c', '-wc'=>'-wc', 'rwc'=>'rwc', '---' => '---');

      while ($group = $groups->fetch()) {
         $catGuestRigths = new Form_Element_Select('group_'.$group[Model_Users::COLUMN_GROUP_NAME],
             sprintf($this->_('Práva pro %s'), $group[Model_Users::COLUMN_GROUP_NAME]));
         $catGuestRigths->setOptions($rightsTypes);
         $form->addElement($catGuestRigths, 'rights');
      }

      $form->addGroup('sitemap', $this->_('Mapa stránek'), $this->_('Nastavení mapy stránek pro vyhledávače'));

      // nastvaení SITEMPS
      // priorita
      $catSitemapPriority = new Form_Element_Text('sitemap_priority', $this->_('Priorita kategorie v sitemap'));
      $catSitemapPriority->setSubLabel('0 - 1, čím větší, tím výše kategorie bude');
      $catSitemapPriority->setValues(0);
      $catSitemapPriority->addValidation(New Form_Validator_IsNumber(null, 'float'));
      $form->addElement($catSitemapPriority, 'sitemap');

      // frekvence změny
      $freqOptions = array($this->_('Vždy') => 'always', $this->_('každou hodinu') => 'hourly',
          $this->_('Denně') => 'daily', $this->_('Týdně') => 'weekly', $this->_('Měsíčně') => 'monthly',
          $this->_('Ročně') => 'yearly', $this->_('Nikdy') => 'never');
      $catSitemapChangeFrequency = new Form_Element_Select('sitemap_frequency', $this->_('Frekvence změn'));
      $catSitemapChangeFrequency->setOptions($freqOptions);
      $form->addElement($catSitemapChangeFrequency, 'sitemap');


      // tlačítko odeslat
      $submitButton = new Form_Element_Submit('send', $this->_('Uložit'));
      $form->addElement($submitButton);

      return $form;
   }

   public function addsectionController(){
      $this->checkWritebleRights();
      // objekt se sekcemi
      $menu = unserialize(VVE_CATEGORIES_STRUCTURE);
      $menu->setLabel($this->_('Hlavní sekce'));
      $this->sectionsToArrayForForm($menu);

      $form = new Form('section');

      $sections = new Form_Element_Select('parent', $this->_('Nadřazená sekce'));
      $sections->setOptions($this->sections);
      $form->addElement($sections);

      $label = new Form_Element_Text('label', $this->_('Název'));
      $label->setLangs();
      $label->addValidation(new Form_Validator_Length(5, 30));
      $label->addValidation(new Form_Validator_NotEmpty(null, Locale::getDefaultLang(true)));
      $form->addElement($label);

      $form->addElement(new Form_Element_Submit('send', $this->_('Odeslat')));

      if($form->isValid()){
         $newSec = new Menu_Sections($form->label->getValues());
         $menu->addChild($newSec, $form->parent->getValues());

//         print (base64_encode(serialize($menu)));

         $model = new Model_Config();
         $model->saveCfg('CATEGORIES_STRUCTURE', serialize($menu));
         $this->infoMsg()->addMessage($this->_("Sekce byla uložena"));
         $this->link()->route()->reload();
      }

      $this->view()->template()->form = $form;
      $this->view()->template()->addTplFile('editSection.phtml');
   }

   private function sectionsToArrayForForm($sections) {
      if($sections instanceof Menu_Sections){
         $this->sections[str_repeat('&nbsp;', $sections->getLevel()*3).$sections->getLabel()]
            = $sections->getId();
         foreach ($sections->getChildrens() as $child){
            $this->sectionsToArrayForForm($child);
         }
      }
   }

   public function connectController() {
      $this->checkWritebleRights();
      $form = new Form('connect');
      
      // kategorie
      $modelCat = new Model_Category();
      $cat = $modelCat->getCategoryList(false);

      $categories = array();
      foreach ($cat as $catVal) {
         $categories[(string)$catVal[Model_Category::COLUMN_CAT_LABEL]] = $catVal[Model_Category::COLUMN_CAT_ID];
      }
      $selCat = new Form_Element_Select('cat', $this->_('Kategorie'));
      $selCat->setOptions($categories);
      $form->addElement($selCat);

      // sekce
      $menu = unserialize(VVE_CATEGORIES_STRUCTURE);
      $menu->setLabel($this->_('Hlavní sekce'));
      $this->sectionsToArrayForForm($menu);

      $sections = new Form_Element_Select('section', $this->_('Sekce'));
      $sections->setOptions($this->sections);
      $form->addElement($sections);

      $submit = new Form_Element_Submit('send', $this->_('Odeslat'));
      $form->addElement($submit);

      if($form->isValid()){
         $sections = unserialize(VVE_CATEGORIES_STRUCTURE);
//         $sections = new Menu_Sections(null);
         $sections->addChild($form->cat->getValues(), $form->section->getValues());

         $model = new Model_Config();
         $model->saveCfg('CATEGORIES_STRUCTURE', serialize($sections));
         $this->infoMsg()->addMessage($this->_("Propojení bylo uloženo"));
         $this->link()->route()->reload();
      }

      $this->view()->template()->form = $form;
      $this->view()->template()->addTplFile('connect.phtml');
   }

   public function editSectionController() {
      $this->checkWritebleRights();
      // objekt se sekcemi
      $menu = unserialize(VVE_CATEGORIES_STRUCTURE);
//      echo "<pre>";
//      print_r($menu);
//      echo "</pre>";
      $menuSelSection = $menu->getSection($this->getRequest('secid'));

      $menuForForm = unserialize(VVE_CATEGORIES_STRUCTURE);
      $menuForForm->setLabel($this->_('Hlavní sekce'));
      $menuForForm->removeSec($this->getRequest('secid'));

      $this->sectionsToArrayForForm($menuForForm);
      // odstranění sekce z výpisu

      

      if($menuSelSection == false){
         $this->errMsg()->addMessage($this->_('Neexistující sekce'));
         return false;
      }

//      unset($this->sections[array_search($this->getRequest('secid'), $this->sections)]);

      $form = new Form('section');

      $sections = new Form_Element_Select('parent', $this->_('Nadřazená sekce'));
      $sections->setOptions($this->sections);
      $sections->setValues($menuSelSection->getParentId());
      $form->addElement($sections);

      $label = new Form_Element_Text('label', $this->_('Název'));
      $label->setLangs();
      $label->setValues($menuSelSection->getLabels());
      $label->addValidation(new Form_Validator_Length(5, 30));
      $label->addValidation(new Form_Validator_NotEmpty(null, Locale::getDefaultLang(true)));
      $form->addElement($label);

      $form->addElement(new Form_Element_Submit('send', $this->_('Odeslat')));

      if($form->isValid()){
         $menuSelSection->setLabels($form->label->getValues());
         // pokud se přesunuje
         if($menuSelSection->getParentId() != $form->parent->getValues()){
            // smazání staré pozice sekce
            $menu->removeSec($this->getRequest('secid'));
            // uložení sekce pod novou sekci
            $menu->addChild($menuSelSection, $form->parent->getValues());
         }
         

         // uložení do konfigu
         $model = new Model_Config();
         $model->saveCfg('CATEGORIES_STRUCTURE', serialize($menu));
         $this->infoMsg()->addMessage($this->_("Sekce byla uložena"));
         $this->link()->route()->reload();
      }

      $this->view()->template()->form = $form;
      $this->view()->template()->addTplFile('editSection.phtml');
   }
}

?>