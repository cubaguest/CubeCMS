<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class Categories_Controller extends Controller {
   private $categoriesArray = array();

   const MODULE_SPEC_FILE = 'spicifikation.html';

   public function mainController() {
      $this->checkWritebleRights();
      $formDelete = new Form('category_');

      $elemId = new Form_Element_Hidden('id');
      $formDelete->addElement($elemId);

      $submitDel = new Form_Element_SubmitImage('remove');
      $formDelete->addElement($submitDel);

      if($formDelete->isValid()) {
         $categories = unserialize(VVE_CATEGORIES_STRUCTURE);
         // načtení kategorie (nutné pro vyčištění)
         $cM = new Model_Category();
         $cat = $cM->getCategoryById($formDelete->id->getValues());

         // vymažeme práva
         $rModel = new Model_Rights();
         $rModel->deleteCatRights($formDelete->id->getValues());

         // mažeme kategorii z DB
         $cM->deleteCategory($formDelete->id->getValues());

         // vyčištění kategorie
         $moduleCtrName = $cat->{Model_Category::COLUMN_MODULE}.'_Controller';
         $rmCategoryObj = new Category($cat->{Model_Category::COLUMN_URLKEY},false, $cat);

         call_user_func($moduleCtrName."::clearOnRemove", $rmCategoryObj);

         // mažeme kategorii ze struktury
         $categories->removeCat($formDelete->id->getValues());
         $categories->saveStructure();
         $this->infoMsg()->addMessage($this->_("Kategorie byla smazána"));

         $this->link()->route()->reload();
      }

      // form pro posun
      $formMove = new Form('item_');

      $elemID = new Form_Element_Hidden('id');
      $formMove->addElement($elemID);

      $moveTo = new Form_Element_Hidden('move_to');
      $formMove->addElement($moveTo);

      $submitMove = new Form_Element_SubmitImage('move');
      $formMove->addElement($submitMove);

      if($formMove->isValid()) {
         $id = $formMove->id->getValues();
         $menu = unserialize(VVE_CATEGORIES_STRUCTURE);
         //         $menu = new Category_Structure(0);

         try {
            $parent = $menu->getCategory($menu->getCategory($id)->getParentId());
            if($formMove->move_to->getValues() == 'up') {
               $parent->swapChild($parent->getChild($id), $parent->prevChild($parent->getChild($id)));
            } else {
               $parent->swapChild($parent->getChild($id), $parent->nextChild($parent->getChild($id)));
            }

            $menu->saveStructure();
            $this->infoMsg()->addMessage($this->_("Pozice byla změněna"));
            $this->link()->route()->reload();
         } catch (Exception $e) {
            new CoreErrors($e);
         }
      }
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
         $cat = $model->getCategoryById($form->id->getValues());
         //mazání ikony
         if($cat->{Model_Category::COLUMN_ICON} != null){
            $file = new Filesystem_File($cat->{Model_Category::COLUMN_ICON},
                    AppCore::getAppWebDir().VVE_DATA_DIR.DIRECTORY_SEPARATOR
                            .Category::CATEGORY_ICONS_DIR.DIRECTORY_SEPARATOR);
            $file->delete();
         }

         $model->deleteCategory($form->id->getValues());
         $this->infoMsg()->addMessage($this->_('Kategorie byla smzána'));
         $this->link()->clear()->reload();
      }

      // nastavení viewru
      $this->view()->template()->cat = $this->getRequest('categoryid');
   }

   public function editController() {
      $this->checkWritebleRights();

      $form = $this->createForm();

      // načtení dat z modelu
      $model = new Model_Category();
      $cat = $model->getCategoryWoutRights($this->getRequest('categoryid'));

      $form->name->setValues($cat[Model_Category::COLUMN_CAT_LABEL]);
      $form->alt->setValues($cat[Model_Category::COLUMN_CAT_ALT]);
      $form->keywords->setValues($cat[Model_Category::COLUMN_KEYWORDS]);
      $form->description->setValues($cat[Model_Category::COLUMN_DESCRIPTION]);
      // nadřazená kategorie

      $menu = unserialize(VVE_CATEGORIES_STRUCTURE);
      $catModel = new Model_Category();
      $menu->setCategories($catModel->getCategoryList(true));

      $selCat = $menu->getCategory($this->getRequest('categoryid'));
      $menu->removeCat($this->getRequest('categoryid'));

      $this->catsToArrayForForm($menu);

      $form->parent_cat->setOptions($this->categoriesArray);
      $form->parent_cat->setValues($selCat->getParentId());

      $form->module->setValues($cat[Model_Category::COLUMN_MODULE]);
      $form->datadir->setValues($cat[Model_Category::COLUMN_DATADIR]);
      $form->urlkey->setValues($cat[Model_Category::COLUMN_URLKEY]);
      $form->priority->setValues($cat[Model_Category::COLUMN_PRIORITY]);
      $form->individual_panels->setValues($cat[Model_Category::COLUMN_INDIVIDUAL_PANELS]);
      $form->show_when_login_only->setValues($cat[Model_Category::COLUMN_CAT_SHOW_WHEN_LOGIN_ONLY]);
      $form->show_in_menu->setValues($cat[Model_Category::COLUMN_CAT_SHOW_IN_MENU]);
      $form->sitemap_priority->setValues($cat[Model_Category::COLUMN_CAT_SITEMAP_CHANGE_PRIORITY]);
      $form->sitemap_frequency->setValues($cat[Model_Category::COLUMN_CAT_SITEMAP_CHANGE_FREQ]);

      // práva
      $form->rights_default->setValues($cat[Model_Category::COLUMN_DEF_RIGHT]);
      $rModel = new Model_Rights();
      $rights = $rModel->getRights($this->getRequest('categoryid'));
      if($rights !== false) {
         while ($right = $rights->fetchObject()) {
            $grName = 'group_'.$right->{Model_Users::COLUMN_GROUP_NAME};
            $form->{$grName}->setValues($right->{Model_Rights::COLUMN_RIGHT});
         }
      }

      // přidání checkboxu pro odstranění ikony
      if($cat->{Model_Category::COLUMN_ICON} != null) {
         $elemDelIcon = new Form_Element_Checkbox('delete_icon', $this->_('Smazat ikonu'));
         $elemDelIcon->setSubLabel($this->_('Nahrán soubor:')." ".$cat->{Model_Category::COLUMN_ICON});
         $form->addElement($elemDelIcon, 'settings');
      }

      // odeslání formuláře
      if($form->isValid()) {
         // vygenerování url klíče
         $path = $menu->getPath($form->parent_cat->getValues());
         $urlkey = $form->urlkey->getValues();
         $names = $form->name->getValues();
         $p = end($path);
         $catObj = null;
         if($p->getCatObj() != null) {
            $catObj = $p->getCatObj()->getCatDataObj();
         }

         foreach ($urlkey as $lang => $variable) {
            // klíč podkategorií
            $urlPath = null;
            if($catObj != null) {
               $urlPath = $catObj[Model_Category::COLUMN_URLKEY][$lang];
            }
            if($urlPath != null) $urlPath .= URL_SEPARATOR;
            if($variable == null AND $names[$lang] == null) {
               $urlkey[$lang] = null;
            } else if($variable == null AND $names[$lang] != null) {
               $urlkey[$lang] = $urlPath.vve_cr_url_key(strtolower($names[$lang]));
            } else {
               $urlkey[$lang] = vve_cr_url_key(strtolower($variable));
            }
         }

         // zjištění jestli je možné vytvoři feedy
         $classRName = ucfirst($form->module->getValues())."_Routes::FEEDS";
         $feeds = false;
         if(constant($classRName) != null) {
            $feeds = true;
         }

         $datadir = null;
         if($form->datadir->getValues() == null){
            $datadir = $urlkey[Locale::getDefaultLang()];
            $last = strrpos($datadir,URL_SEPARATOR);
            if($last !== false){
               $datadir = substr($datadir,$last+1);
            }
         } else if($form->datadir->getValues() != $cat[Model_Category::COLUMN_DATADIR]) {
            $datadir = vve_cr_safe_file_name($form->datadir->getValues());
            // pokud byl předtím definován dojde k přesunu
            if($cat[Model_Category::COLUMN_DATADIR] != null) {
               $dir = new Filesystem_Dir(AppCore::getAppWebDir().VVE_DATA_DIR.DIRECTORY_SEPARATOR.$cat[Model_Category::COLUMN_DATADIR]);
               if($dir->exist()){
                  $dir->rename($datadir);
               }
               unset ($dir);
            }
         }

         // ikona
         $icon = $cat->{Model_Category::COLUMN_ICON};
         if($icon != null AND ($form->icon->getValues() != null OR
                         ($form->haveElement('delete_icon') AND $form->delete_icon->getValues() == true))) {
            $file = new Filesystem_File($cat->{Model_Category::COLUMN_ICON},
                    AppCore::getAppWebDir().VVE_DATA_DIR.DIRECTORY_SEPARATOR
                            .Category::CATEGORY_ICONS_DIR.DIRECTORY_SEPARATOR);
            $file->delete();
         }
         if($form->icon->getValues() != null) {
            $f = $form->icon->getValues();
            $icon = $f['name'];
         }

         // kategorie
         $categoryModel = new Model_Category();
         $categoryModel->saveEditCategory($this->getRequest('categoryid'), $form->name->getValues(),$form->alt->getValues(),
                 $form->module->getValues(), $form->keywords->getValues(),
                 $form->description->getValues(),$urlkey,$form->priority->getValues(),$form->individual_panels->getValues(),
                 $form->show_in_menu->getValues(),$form->show_when_login_only->getValues(),
                 $form->sitemap_priority->getValues(),$form->sitemap_frequency->getValues(),
                 $form->rights_default->getValues(), $feeds, $datadir, $icon);

         // práva
         $usrModel = new Model_Users();
         $groups = $usrModel->getGroups();
         while ($group = $groups->fetchObject()) {
            $grName = 'group_'.$group->{Model_Users::COLUMN_GROUP_NAME};
            $right = $form->{$grName}->getValues();
            $rModel->saveRight($right, $group->{Model_Users::COLUMN_ID_GROUP}, $this->getRequest('categoryid'));
         }

         // uprava struktury
         if($form->parent_cat->getValues() != $selCat->getParentId()) {
            $menuRepair = unserialize(VVE_CATEGORIES_STRUCTURE);
            $cat = $menuRepair->getCategory($this->getRequest('categoryid'));
            $menuRepair->removeCat($this->getRequest('categoryid'));
            $menuRepair->addChild($cat, $form->parent_cat->getValues());
            $menuRepair->saveStructure();
         }

         // vytvoření tabulek
         $modelDbSupport = new Model_Module();
         $modelDbSupport->installModuleTable($form->module->getValues());

         $this->infoMsg()->addMessage('Kategorie byla uložena');
         //         $this->link()->route('detail', array('categoryid' => $this->getRequest('categoryid')))->reload();
         $this->link()->route()->reload();

      }

      $this->view()->template()->form = $form;
      $this->view()->template()->edit = true;
      $this->view()->template()->catName = $cat->{Model_Category::COLUMN_CAT_LABEL};
   }

   public function addController() {
      $form = $this->createForm();

      $form->show_in_menu->setValues(true);

      // kategorie
      $menu = unserialize(VVE_CATEGORIES_STRUCTURE);
      $catModel = new Model_Category();
      $menu->setCategories($catModel->getCategoryList(true));

      $this->catsToArrayForForm($menu);
      $form->parent_cat->setOptions($this->categoriesArray);

      if($form->isValid()) {
         // vygenerování url klíče
         $path = $menu->getPath($form->parent_cat->getValues());

         $urlkey = $form->urlkey->getValues();
         $names = $form->name->getValues();
         foreach ($urlkey as $lang => $variable) {
            // klíč podkategorií
            $urlPath = null;
            $p = end($path);
            // pokud se vkládá do kořenu. Kořen nemá kategorii
            if($p->getCatObj() !== null) {
               $catObj = $p->getCatObj()->getCatDataObj();
               $urlPath = $catObj[Model_Category::COLUMN_URLKEY][$lang];
            }
            if($urlPath != null) $urlPath .= URL_SEPARATOR;

            if($variable == null AND $names[$lang] == null) {
               $urlkey[$lang] = null;
            } else if($variable == null) {
               $urlkey[$lang] = $urlPath.vve_cr_url_key(strtolower($names[$lang]));
            } else {
               $urlkey[$lang] = $urlPath.vve_cr_url_key(strtolower($variable));
            }
         }

         // zjištění jestli je možné vytvoři feedy
         $classRName = ucfirst($form->module->getValues())."_Routes::FEEDS";
         $feeds = false;
         if(constant($classRName) != null) {
            $feeds = true;
         }


         $icon = null;
         if($form->icon->getValues() != null) {
            $f = $form->icon->getValues();
            $icon = $f['name'];
         }

         // pokud není datadir tak jej nastavíme
//         $datadir = vve_cr_safe_file_name($form->datadir->getValues());
         if($form->datadir->getValues() == null){
            $dataDir = $urlkey[Locale::getDefaultLang()];
            $last = strrpos($dataDir,URL_SEPARATOR);
            if($last !== false){
               $dataDir = substr($dataDir,$last+1);
            }
         } else {
            $dataDir = vve_cr_safe_file_name($form->datadir->getValues());
         }

         $categoryModel = new Model_Category();
         $lastId = $categoryModel->saveNewCategory($form->name->getValues(),$form->alt->getValues(),
                 $form->module->getValues(), $form->keywords->getValues(),
                 $form->description->getValues(), $urlkey, $form->priority->getValues(), $form->individual_panels->getValues(),
                 $form->show_in_menu->getValues(), $form->show_when_login_only->getValues(),
                 $form->sitemap_priority->getValues(),$form->sitemap_frequency->getValues(),
                 $form->rights_default->getValues(), $feeds, $dataDir,$icon);

         // práva
         $usrModel = new Model_Users();
         $rModel = new Model_Rights();
         $groups = $usrModel->getGroups();
         while ($group = $groups->fetchObject()) {
            $grName = 'group_'.$group->{Model_Users::COLUMN_GROUP_NAME};
            $right = $form->{$grName}->getValues();
            $rModel->saveRight($right, $group->{Model_Users::COLUMN_ID_GROUP}, $lastId);
         }
         // po uložení vložíme do struktury
         if($lastId !== false) {
            $menu = unserialize(VVE_CATEGORIES_STRUCTURE);
            $menu->addChild(new Category_Structure($lastId), $form->parent_cat->getValues());
            $menu->saveStructure();
         }

         // vytvoření tabulek
         $modelDbSupport = new Model_Module();
         $modelDbSupport->installModuleTable($form->module->getValues());

         $this->infoMsg()->addMessage('Kategorie byla uložena');
         $this->link()->route()->reload();
      }

      $this->view()->template()->form = $form;
      $this->view()->template()->edit = false;
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
         $moduleName = null;
         // pokud existuje dokumentace tak načteme název modulu
         if(file_exists(AppCore::getAppLibDir().AppCore::MODULES_DIR.DIRECTORY_SEPARATOR
            .$module.DIRECTORY_SEPARATOR.'docs'.DIRECTORY_SEPARATOR.self::MODULE_SPEC_FILE)){
            $mcnt = file_get_contents(AppCore::getAppLibDir().AppCore::MODULES_DIR.DIRECTORY_SEPARATOR
            .$module.DIRECTORY_SEPARATOR.'docs'.DIRECTORY_SEPARATOR.self::MODULE_SPEC_FILE);
            $matches = array();
            if(preg_match('/class="moduleName">([^<]*)</', $mcnt, $matches)){
               $moduleName .= $matches[1].' - ';
            }
         }
         $moduleName .= $module;
         $options[$moduleName] = $module;
      }

      ksort($options);


      $catModule = new Form_Element_Select('module', $this->_('Modul'));
      $catModule->setOptions($options);
      $form->addElement($catModule, 'settings');

      $catDataDir = new Form_Element_Text('datadir', $this->_('Adresář s daty'));
      $catDataDir->setSubLabel($this->_('Název datového adresář (ne cestu). Do něj budou ukládány všechyn soubory modulu.
         Pokud zůstane prázdný, použije se název modulu. POZOR! změna tohoto parametru může zapříčinit ztrátu dat!'));
      $form->addElement($catDataDir, 'labels');

      // url klíč kategorie
      $catUrlKey = new Form_Element_Text('urlkey', $this->_('Url klíč'));
      $catUrlKey->setLangs();
      $catUrlKey->setSubLabel($this->_('Pokud není zadán, je url klíč generován automaticky'));
      $form->addElement($catUrlKey,'settings');

      // priorita
      $catPriority = new Form_Element_Text('priority', $this->_('Priorita kategorie'));
      $catPriority->setSubLabel('Čím větší tím bude kategorie vybrána jako výchozí');
      $catPriority->addValidation(New Form_Validator_IsNumber());
      $catPriority->addValidation(New Form_Validator_Length(1, 4));
      $catPriority->setValues(0);
      $form->addElement($catPriority, 'settings');

      // panely
      $catLeftPanel = new Form_Element_Checkbox('individual_panels', $this->_('Panely'));
      $catLeftPanel->setSubLabel(_('Zapnutí individuálního nastavení panelů'));
      $form->addElement($catLeftPanel, 'settings');

      $catShowInMenu = new Form_Element_Checkbox('show_in_menu', $this->_('Zobrazit v menu'));
      $form->addElement($catShowInMenu, 'settings');

      $catShowOnlyWhenLogin = new Form_Element_Checkbox('show_when_login_only', $this->_('Zobrazit pouze při přihlášení'));
      $form->addElement($catShowOnlyWhenLogin, 'settings');

      $elemIcon = new  Form_Element_File('icon', $this->_('Ikona'));
      $elemIcon->setUploadDir(AppCore::getAppWebDir().VVE_DATA_DIR.DIRECTORY_SEPARATOR
              .Category::CATEGORY_ICONS_DIR.DIRECTORY_SEPARATOR);
      $elemIcon->addValidation(new Form_Validator_FileExtension('jpg;png;gif'));
      $form->addElement($elemIcon,'settings');

      // práva
      $form->addGroup('rights', $this->_('Práva'), $this->_('Nastavení práv ke kategorii'));
      $grModel = new Model_Users();
      $groups = $grModel->getGroups();

      // pole s typy práv
      $rightsTypes = array('r--'=>'r--', '-w-'=>'-w-', '--c'=>'--c', 'rw-'=>'rw-',
              'r-c'=>'r-c', '-wc'=>'-wc', 'rwc'=>'rwc', '---' => '---');

      // výchozí práva kategorie
      $catGrpRigths = new Form_Element_Select('rights_default', $this->_('Výchozí práva'));
      $catGrpRigths->setOptions($rightsTypes);
      $form->addElement($catGrpRigths, 'rights');

      while ($group = $groups->fetchObject()) {
         if($group->{Model_Users::COLUMN_GROUP_LABEL} != null) {
            $grName = $group->{Model_Users::COLUMN_GROUP_LABEL};
         } else {
            $grName = $group->{Model_Users::COLUMN_GROUP_NAME};
         }
         $catGrpRigths = new Form_Element_Select('group_'.$group->{Model_Users::COLUMN_GROUP_NAME},
                 sprintf($this->_("Skupina\n \"%s\""), $grName));
         $catGrpRigths->setOptions($rightsTypes);
         $catGrpRigths->setValues(reset($rightsTypes));
         $form->addElement($catGrpRigths, 'rights');
      }

      $form->addGroup('sitemap', $this->_('Mapa stránek'), $this->_('Nastavení mapy stránek pro vyhledávače'));

      // nastvaení SITEMAPS
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
                         (string)$categories->getCatObj()->getLabel()]
                 = (string)$categories->getCatObj()->getId();
      } else {
         $this->categoriesArray[$this->_('Kořen')] = 0;
      }
      if(!$categories->isEmpty()) {
         foreach ($categories as $cat) {
            $this->catsToArrayForForm($cat);
         }
      }
   }

   public function moduleDocController() {
      if(file_exists(AppCore::getAppLibDir().AppCore::MODULES_DIR.DIRECTORY_SEPARATOR
      .$this->getRequestParam('module').DIRECTORY_SEPARATOR.'docs'.DIRECTORY_SEPARATOR.self::MODULE_SPEC_FILE)) {
         $this->view()->doc = file_get_contents(AppCore::getAppLibDir().AppCore::MODULES_DIR.DIRECTORY_SEPARATOR
                 .$this->getRequestParam('module').DIRECTORY_SEPARATOR.'docs'.DIRECTORY_SEPARATOR.self::MODULE_SPEC_FILE);
      } else {
         $this->view()->doc = $this->_('Dokumentace k modulu neexistuje');
      }
   }


   public function catSettingsController(){
      $this->checkWritebleRights();

      $categoryM = new Model_Category();
      $cat = $categoryM->getCategoryById($this->getRequest('categoryid'));
      if($cat === false) return false;
      $this->view()->catName = $cat->{Model_Category::COLUMN_CAT_LABEL};
      $this->view()->moduleName = $cat->{Model_Category::COLUMN_MODULE};

      $func = array(ucfirst($cat->{Model_Category::COLUMN_MODULE}).'_Controller','settingsController');

      $form = new Form('settings_');
      $form->addGroup('basic', 'Základní nasatvení');
      $md5FormEmpty = md5(serialize($form));

      if($cat->{Model_Category::COLUMN_PARAMS}!= null){
         $settings = unserialize($cat->{Model_Category::COLUMN_PARAMS});
      } else {
         $settings = array();
      }

      call_user_func_array($func, array(&$settings, &$form));

      // pokud je formulář prázdný
      if($md5FormEmpty == md5(serialize($form))){
         $form = null;
      } else {
         $form->addGroup('buttons');
         $elemSend = new Form_Element_Submit('send', 'Odeslat');
         $form->addElement($elemSend, 'buttons');
      }

      if($form != null AND $form->isValid()){
         // čištění nulových hodnot
         foreach ($settings as $key => $option){
            if($option === null OR empty ($option) OR $option === 0){
               unset($settings[$key]);
            }
         }
         $categoryM->saveCatParams($this->getRequest('categoryid'), serialize($settings));

         $this->infoMsg()->addMessage($this->_('Uloženo'));
         $this->link()->route()->reload();
      }

      $this->view()->form = $form;
   }
}

?>