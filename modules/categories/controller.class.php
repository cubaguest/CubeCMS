<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class Categories_Controller extends Controller {
   private $categoriesArray = array();

   public function mainController() {
      $this->checkReadableRights();

      $formDelete = new Form('category_');

      $elemId = new Form_Element_Hidden('id');
      $formDelete->addElement($elemId);

      $submit = new Form_Element_Submit('remove');
      $formDelete->addElement($submit);
//
      if($formDelete->isValid()){
         $categories = unserialize(VVE_CATEGORIES_STRUCTURE);
         
         // mažeme kategorii z DB
         $model = new Model_Category();
         $model->deleteCategory($formDelete->id->getValues());

         // mažeme kategorii ze struktury
         $categories->removeCat($formDelete->id->getValues());
         $categories->saveStructure();
         $this->infoMsg()->addMessage($this->_("Kategorie byla smazána"));

//         $model = new Model_Config();
//         $model->saveCfg('CATEGORIES_STRUCTURE', serialize($categories));
         $this->link()->route()->reload();
      }
//
//      // form pro posun
//      $formMove = new Form('item');
//
//      $childKey = new Form_Element_Hidden('child_key');
//      $formMove->addElement($childKey);
//
//      $parentID = new Form_Element_Hidden('parent_id');
//      $formMove->addElement($parentID);
//
//      $moveTo = new Form_Element_Hidden('move_to');
//      $formMove->addElement($moveTo);
//
//      $submit = new Form_Element_Submit('move');
//      $formMove->addElement($submit);
//
//      if($formMove->isValid()){
//         $sections = unserialize(VVE_CATEGORIES_STRUCTURE);
//         $sections = new Menu_Sections(null);
//
//         $sections->moveChild($idParent, $key, $moveTo);
//
////         $model = new Model_Config();
////         $model->saveCfg('CATEGORIES_STRUCTURE', serialize($sections));
//         $this->link()->route()->reload();
//      }

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
      // nadřazená kategorie

      $menu = unserialize(VVE_CATEGORIES_STRUCTURE);
      $catModel = new Model_Category();
      $menu->setCategories($catModel->getCategoryList());

      $selCat = $menu->getCategory($this->getRequest('categoryid'));
      $menu->removeCat($this->getRequest('categoryid'));

      $this->catsToArrayForForm($menu);

      $form->parent_cat->setOptions($this->categoriesArray);
      $form->parent_cat->setValues($selCat->getParentId());


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

         // uprava struktury
         if($form->parent_cat->getValues() != $selCat->getParentId()){
            $menuRepair = unserialize(VVE_CATEGORIES_STRUCTURE);
            $cat = $menuRepair->getCategory($this->getRequest('categoryid'));
            $menuRepair->removeCat($this->getRequest('categoryid'));
            $menuRepair->addChild($cat, $form->parent_cat->getValues());
            $menuRepair->saveStructure();
         }

         $this->infoMsg()->addMessage('Kategorie byla uložena');
//         $this->link()->route('detail', array('categoryid' => $this->getRequest('categoryid')))->reload();
         $this->link()->route()->reload();

      }

      $this->view()->template()->form = $form;
      $this->view()->template()->addTplFile('edit.phtml');
   }

   public function addController() {
      $form = $this->createForm();

      $form->show_in_menu->setValues(true);

      // kategorie
      $menu = unserialize(VVE_CATEGORIES_STRUCTURE);
      $catModel = new Model_Category();
      $menu->setCategories($catModel->getCategoryList());
      
      $this->catsToArrayForForm($menu);
      $form->parent_cat->setOptions($this->categoriesArray);

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
         $lastId = $categoryModel->saveNewCategory($form->name->getValues(),$form->alt->getValues(),
             $form->module->getValues(),$form->keywords->getValues(),
             $form->description->getValues(),$urlkey,$form->priority->getValues(),$form->panel_left->getValues(),
             $form->panel_right->getValues(),$form->show_in_menu->getValues(),$form->show_when_login_only->getValues(),
             $rights,$form->sitemap_priority->getValues(),$form->sitemap_frequency->getValues());

         // po uložení vložíme do struktury
         if($lastId !== false){
            $menu = unserialize(VVE_CATEGORIES_STRUCTURE);
            $menu->addChild(new Category_Structure($lastId), $form->parent_cat->getValues());
            $menu->saveStructure();
         }

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

      // kaegorie
      $catSections = new Form_Element_Select('parent_cat', $this->_('Nadřazená kategorie'));
      $form->addElement($catSections, 'settings');

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

   private function catsToArrayForForm($categories) {
   // pokud je hlavní kategorie
      if($categories->getLevel() != 0) {
         $this->categoriesArray[str_repeat('&nbsp;', $categories->getLevel()*3).
             (string)$categories->getCatObj()->{Model_Category::COLUMN_CAT_LABEL}]
             = (string)$categories->getCatObj()->{Model_Category::COLUMN_CAT_ID};
      } else {
         $this->categoriesArray[$this->_('Kořen')] = 0;
      }
      if(!$categories->isEmpty()) {
         foreach ($categories as $cat) {
            $this->catsToArrayForForm($cat);
         }
      }
   }
   
}

?>