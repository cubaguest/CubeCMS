<?php

/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */
class Categories_Controller extends Controller {
   const MODULE_SPEC_FILE = 'spicifikation.html';
   const MODULE_ADMIN_FILE = 'admin';

   private $categoriesArray = array();
   /**
    * jestli se pracuje se strukturou administračního menu
    * @var bool
    */
   private $isMainStruct = true;

   public function mainController()
   {
      $this->checkWritebleRights();

      $this->setMainStruct(true);
//       if ($this->routes()->getActionName() != 'main') {
//          $this->setMainStruct(false);
//       }

      $formDelete = new Form('category_', true);

      $elemId = new Form_Element_Hidden('id');
      $formDelete->addElement($elemId);

      $submitDel = new Form_Element_SubmitImage('delete');
      $formDelete->addElement($submitDel);

      if ($formDelete->isValid()) {
         $categories = Category_Structure::getStructure(!$this->isMainStruct());
         // načtení kategorie (nutné pro vyčištění)
         $cM = new Model_Category();
         $cat = $cM->record($formDelete->id->getValues());

         // vymažeme práva
         $rModel = new Model_Rights();
         $rModel->deleteRightsByCatID($formDelete->id->getValues());

         // mažeme kategorii z DB
         $cM->delete($formDelete->id->getValues());

         // vyčištění kategorie
         $moduleCtrName = $cat->{Model_Category::COLUMN_MODULE} . '_Controller';
         $rmCategoryObj = new Category($cat->{Model_Category::COLUMN_URLKEY}, false, $cat);

         call_user_func($moduleCtrName . "::clearOnRemove", $rmCategoryObj);

         // mažeme kategorii ze struktury
         $categories->removeCat($formDelete->id->getValues());
         $categories->saveStructure(!$this->isMainStruct());
         $this->infoMsg()->addMessage($this->tr("Kategorie byla smazána"));
         $this->log('Smazána kategorie :"' . $cat->{Model_Category::COLUMN_CAT_LABEL} . '"');
         $this->gotoBack();
      }

      $structure = Category_Structure::getStructure(!$this->isMainStruct());
      $structure->withHidden(true);
      if ($structure != false) {
         $catModel = new Model_Category();
         $structure->setCategories($catModel
            ->join(Model_Category::COLUMN_CAT_ID, "Model_Rights", Model_Rights::COLUMN_ID_CATEGORY, array(Model_Rights::COLUMN_RIGHT))
            ->groupBy(array(Model_Category::COLUMN_CAT_ID))
            ->records(Model_ORM::FETCH_PKEY_AS_ARR_KEY));
//         $structure->setCategories($catModel->getCategoryList());
         $this->view()->structure = $structure;
      }
      $this->view()->isMainMenu = $this->isMainStruct();
//      Debug::log($structure);
   }

   public function adminMenuController()
   {
      $this->mainController();
   }

   public function editController()
   {
      $this->checkWritebleRights();

      $form = $this->createForm();

      // načtení dat z modelu
      $categoryModel = new Model_Category();
      $record = $categoryModel->record($this->getRequest('categoryid'));

      if($record == false){
         return false;
      }
      
      $form->name->setValues($record->{Model_Category::COLUMN_CAT_LABEL});
      $form->alt->setValues($record->{Model_Category::COLUMN_CAT_ALT});
      $form->keywords->setValues($record->{Model_Category::COLUMN_KEYWORDS});
      $form->description->setValues($record->{Model_Category::COLUMN_DESCRIPTION});
      // nadřazená kategorie

      $structure = Category_Structure::getStructure(!$this->isMainStruct());
      $structure->withHidden(true);
      $structure->setCategories($categoryModel->setSelectAllLangs(true)->records(Model_ORM::FETCH_PKEY_AS_ARR_KEY));

      $selCat = $structure->getCategory($this->getRequest('categoryid'));
      $structure->removeCat($this->getRequest('categoryid'));
      $this->catsToArrayForForm($structure);
      $form->parent_cat->setOptions($this->categoriesArray);
      $form->parent_cat->setValues($selCat->getParentId());

      $form->module->setValues($record->{Model_Category::COLUMN_MODULE});
      $form->feedexp->setValues($record->{Model_Category::COLUMN_FEEDS});
      $form->datadir->setValues($record->{Model_Category::COLUMN_DATADIR});
      $form->urlkey->setValues($record->{Model_Category::COLUMN_URLKEY});
      $form->priority->setValues($record->{Model_Category::COLUMN_PRIORITY});
      $form->individual_panels->setValues($record->{Model_Category::COLUMN_INDIVIDUAL_PANELS});
      $form->visibility->setValues($record->{Model_Category::COLUMN_VISIBILITY});
      $form->sitemap_priority->setValues($record->{Model_Category::COLUMN_CAT_SITEMAP_CHANGE_PRIORITY});
      $form->sitemap_frequency->setValues($record->{Model_Category::COLUMN_CAT_SITEMAP_CHANGE_FREQ});

      // práva
      $form->rights_default->setValues($record->{Model_Category::COLUMN_DEF_RIGHT});
      //nastavení výchozího práva pro všehny skupiny
      foreach ($form as $ename => $element) {
         if(strpos($ename, 'group_') !== false){
            $element->setValues($record->{Model_Category::COLUMN_DEF_RIGHT});
         }
      }
      
      $rModel = new Model_Rights();
      $rights = $rModel->joinFK(Model_Rights::COLUMN_ID_GROUP, array(Model_Groups::COLUMN_NAME))
         ->where(Model_Rights::COLUMN_ID_CATEGORY.' = :idc', 
            array('idc' => $record->{Model_Category::COLUMN_ID}))->records();
      if ($rights !== false) {
         foreach ($rights as $right) {
            $grName = 'group_' . $right->{Model_Groups::COLUMN_NAME};
            if($form->haveElement($grName)){
               $form->{$grName}->setValues($right->{Model_Rights::COLUMN_RIGHT});
            }
         }
      }

//    Checkbox pro regeneraci url klíčů při přesunu  settings
      $elemRegenUrls = new Form_Element_Checkbox('regenerateUrls', $this->tr('Opravit URL'));
      $elemRegenUrls->setSubLabel($this->tr('Opravit URL adresu kategorie a všech potomků podle struktury'));
      $form->addElement($elemRegenUrls, 'settings', 4);


      if ($form->isSend() AND $form->send->getValues() == false) {
         $this->infoMsg()->addMessage($this->tr('Změny byly zrušeny'));
         $this->gotoBack();
      }

      // odeslání formuláře
      if ($form->isValid()) {
         // vygenerování url klíče
         $path = $structure->getPath($form->parent_cat->getValues());
         if($form->regenerateUrls->getValues() === true){
            foreach (Locales::getAppLangs() as $lang) {
               $urlkey[$lang] = null;
            }
         } else {
            $urlkey = $form->urlkey->getValues();
         }
         $names = $form->name->getValues();
         $p = end($path);
         $catObj = null;
         if ($p->getCatObj() != null) {
            $catObj = $p->getCatObj()->getCatDataObj();
         }

         foreach ($urlkey as $lang => $variable) {
            // klíč podkategorií
            $urlPath = null;
            if ($catObj != null) {
               $urlPath = $catObj[Model_Category::COLUMN_URLKEY][$lang];
            }
            if ($urlPath != null) {
               $urlPath .= URL_SEPARATOR;
            }
            if ($variable == null AND $names[$lang] == null) {
               $urlkey[$lang] = null;
            } else if ($variable == null AND $names[$lang] != null) {
               $urlkey[$lang] = $urlPath . vve_cr_url_key(strtolower($names[$lang]));
            } else {
               $urlkey[$lang] = vve_cr_url_key(strtolower($variable), false);
            }
         }
         
         // regenerace klíčů kategorií
         if($form->regenerateUrls->getValues() === true){
            // @TODO asi doplnit i hlavní kategorii, ale ta je předem daná
            // potomci
            if(!$selCat->isEmpty()){
               foreach ($selCat->getChildrens() as $child) {
                  $this->repairUrlKeys($categoryModel, $child, $urlkey);
               }
            }
         }

         // zjištění jestli je možné vytvoři feedy
         $feeds = false;
         if ($form->feedexp->getValues() == true
            AND file_exists(AppCore::getAppLibDir() . AppCore::MODULES_DIR . DIRECTORY_SEPARATOR
               . $form->module->getValues() . DIRECTORY_SEPARATOR . 'rss.class.php')) {
            $feeds = true;
         }

         $datadir = null;
         if ($form->datadir->getValues() == null) {
            $datadir = $urlkey[Locales::getDefaultLang()];
            $last = strrpos($datadir, URL_SEPARATOR);
            if ($last !== false) {
               $datadir = substr($datadir, $last + 1);
            }
         } else {
            $datadir = vve_cr_safe_file_name($form->datadir->getValues());
            // pokud byl předtím definován dojde k přesunu
            if ($record->{Model_Category::COLUMN_DATADIR} != $datadir) {
               $dir = new Filesystem_Dir(AppCore::getAppWebDir() . VVE_DATA_DIR . DIRECTORY_SEPARATOR . $record->{Model_Category::COLUMN_DATADIR});
               if ($dir->exist()) {
                  $dir->rename($datadir);
               }
               unset($dir);
            }
         }
         
         $record->{Model_Category::COLUMN_NAME} = $form->name->getValues();
         $record->{Model_Category::COLUMN_ALT} = $form->alt->getValues();
         $record->{Model_Category::COLUMN_MODULE} = $form->module->getValues();
         $record->{Model_Category::COLUMN_KEYWORDS} = $form->keywords->getValues();
         $record->{Model_Category::COLUMN_DESCRIPTION} = $form->description->getValues();
         $record->{Model_Category::COLUMN_URLKEY} = $urlkey;
         $record->{Model_Category::COLUMN_PRIORITY} = $form->priority->getValues();
         $record->{Model_Category::COLUMN_INDIVIDUAL_PANELS} = $form->individual_panels->getValues();
         $record->{Model_Category::COLUMN_VISIBILITY} = $form->visibility->getValues();
         $record->{Model_Category::COLUMN_SITEMAP_CHANGE_PRIORITY} = $form->sitemap_priority->getValues();
         $record->{Model_Category::COLUMN_SITEMAP_CHANGE_FREQ} = $form->sitemap_frequency->getValues();
         $record->{Model_Category::COLUMN_DEF_RIGHT} = $form->rights_default->getValues();
         $record->{Model_Category::COLUMN_FEEDS} = $feeds;
         $record->{Model_Category::COLUMN_DATADIR} = $datadir;

         $categoryModel->save($record);

         // práva
         $this->assignRights($record->{Model_Category::COLUMN_ID}, $form);

         // uprava struktury
         if ($form->parent_cat->getValues() != $selCat->getParentId()) {
            $menuRepair = Category_Structure::getStructure(!$this->isMainStruct());
            $cat = $menuRepair->getCategory($this->getRequest('categoryid'));
            $menuRepair->removeCat($this->getRequest('categoryid'));
            $menuRepair->addChild($cat, $form->parent_cat->getValues());
            $menuRepair->saveStructure(!$this->isMainStruct());
         }

         // vytvoření tabulek
         // instalace
         $mInsClass = ucfirst($form->module->getValues()) . '_Install';
         $mInstall = new $mInsClass();
         $mInstall->installModule();

         $this->infoMsg()->addMessage('Kategorie byla uložena');
         $this->log('Upravena kategorie "' . $record[Model_Category::COLUMN_NAME][Locales::getDefaultLang()] . '"');
         if ($form->gotoSettings->getValues() == true) {
            $this->link()->route('settings', array('categoryid' => $this->getRequest('categoryid')))->reload();
         } else {
            $this->gotoBack();
         }
      }

      $this->view()->template()->form = $form;
      $this->view()->template()->edit = true;
      $this->view()->template()->catName = $record->{Model_Category::COLUMN_CAT_LABEL};
   }

   public function addController()
   {
      $this->checkWritebleRights();
      $categoryModel = new Model_Category();
      $form = $this->createForm();

      $form->gotoSettings->setValues(true);

      // kategorie
      $structure = Category_Structure::getStructure(!$this->isMainStruct());
      $structure->withHidden(true);
      if ($structure != false) {
         $structure->setCategories($categoryModel->getCategoryList());
      }
      $this->catsToArrayForForm($structure);
      $form->parent_cat->setOptions($this->categoriesArray);

      $form->module->setValues('text');
      if($this->getRequestParam('id', 0) != 0){
         $form->parent_cat->setValues($this->getRequestParam('id',0));
      }

      if ($form->isSend() AND $form->send->getValues() == false) {
         $this->gotoBack();
      }

      if ($form->isValid()) {
         // vygenerování url klíče
         $path = $structure->getPath($form->parent_cat->getValues());

         $urlkey = $form->urlkey->getValues();
         $names = $form->name->getValues();
         foreach ($urlkey as $lang => $variable) {
            // klíč podkategorií
            $urlPath = null;
            $p = end($path);
            // pokud se vkládá do kořenu. Kořen nemá kategorii
            if ($p->getCatObj() !== null) {
               $catObj = $p->getCatObj()->getCatDataObj();
               $urlPath = $catObj[Model_Category::COLUMN_URLKEY][$lang];
            }
            if ($urlPath != null)
               $urlPath .= URL_SEPARATOR;

            if ($variable == null AND $names[$lang] == null) {
               $urlkey[$lang] = null;
            } else if ($variable == null) {
               $urlkey[$lang] = $urlPath . vve_cr_url_key(strtolower($names[$lang]));
            } else {
               $urlkey[$lang] = $urlPath . vve_cr_url_key(strtolower($variable), false);
            }
         }

         // zjištění jestli je možné vytvoři feedy
         $feeds = false;
         if ($form->feedexp->getValues() == true
            AND file_exists(AppCore::getAppLibDir() . AppCore::MODULES_DIR . DIRECTORY_SEPARATOR
               . $form->module->getValues() . DIRECTORY_SEPARATOR . 'rss.class.php')) {
            $feeds = true;
         }

         // pokud není datadir tak jej nastavíme
         if ($form->datadir->getValues() == null) {
            $dataDir = $urlkey[Locales::getDefaultLang()];
            $last = strrpos($dataDir, URL_SEPARATOR);
            if ($last !== false) {
               $dataDir = substr($dataDir, $last + 1);
            }
         } else {
            $dataDir = vve_cr_safe_file_name($form->datadir->getValues());
         }

         $record = $categoryModel->newRecord();

         $record->{Model_Category::COLUMN_NAME} = $form->name->getValues();
         $record->{Model_Category::COLUMN_ALT} = $form->alt->getValues();
         $record->{Model_Category::COLUMN_MODULE} = $form->module->getValues();
         $record->{Model_Category::COLUMN_KEYWORDS} = $form->keywords->getValues();
         $record->{Model_Category::COLUMN_DESCRIPTION} = $form->description->getValues();
         $record->{Model_Category::COLUMN_URLKEY} = $urlkey;
         $record->{Model_Category::COLUMN_PRIORITY} = $form->priority->getValues();
         $record->{Model_Category::COLUMN_INDIVIDUAL_PANELS} = $form->individual_panels->getValues();
         $record->{Model_Category::COLUMN_VISIBILITY} = $form->visibility->getValues();
         $record->{Model_Category::COLUMN_SITEMAP_CHANGE_PRIORITY} = $form->sitemap_priority->getValues();
         $record->{Model_Category::COLUMN_SITEMAP_CHANGE_FREQ} = $form->sitemap_frequency->getValues();
         $record->{Model_Category::COLUMN_DEF_RIGHT} = $form->rights_default->getValues();
         $record->{Model_Category::COLUMN_FEEDS} = $feeds;
         $record->{Model_Category::COLUMN_DATADIR} = $dataDir;

         $lastId = $categoryModel->save($record);

         // práva
         $this->assignRights($lastId, $form);
         
         // po uložení vložíme do struktury
         if ($lastId !== false) {
            $newStructure = Category_Structure::getStructure(!$this->isMainStruct());
            $newStructure->addChild(new Category_Structure($lastId), $form->parent_cat->getValues());
            $newStructure->saveStructure(!$this->isMainStruct());
         }

         // instalace
         $mInsClass = ucfirst($form->module->getValues()) . '_Install';
         $mInstall = new $mInsClass();
         $mInstall->installModule();
         $this->log('Přidána nová kategorie "' . $names[Locales::getDefaultLang()] . '"');
         $this->infoMsg()->addMessage('Kategorie byla uložena');
//         var_dump($form->gotoSettings->getValues());flush();
         if ($form->gotoSettings->getValues() == true) {
            $this->link()->param('id')->route('settings', array('categoryid' => $lastId))->reload();
         } else {
            $this->gotoBack();
         }
      }

      $this->view()->template()->form = $form;
      $this->view()->template()->edit = false;
   }

   private function getGroups()
   {
      VVE_SUB_SITE_DOMAIN == null ? $domain = 'www' : $domain = VVE_SUB_SITE_DOMAIN;
      $groupsModel = new Model_Groups();
      
      $groupsModel->columns(array('*', 'domains' => 'GROUP_CONCAT(' . Model_Sites::COLUMN_DOMAIN . ')'))
            ->join(Model_Groups::COLUMN_ID, array('tsg' => 'Model_SitesGroups'), Model_SitesGroups::COLUMN_ID_GROUP, false)
            ->join(array('tsg' => Model_SitesGroups::COLUMN_ID_SITE), array('t_s' => 'Model_Sites'), Model_Sites::COLUMN_ID, false)
            ->groupBy(array(Model_Groups::COLUMN_ID));

      $adminSites = Auth::getUserSites();
      if(!empty($adminSites)){
         $groupsModel->where('('.Model_Groups::COLUMN_IS_ADMIN.' = 0 ) '
            .'AND (t_s.'.Model_Sites::COLUMN_ID.' IS NULL OR t_s.'.Model_Sites::COLUMN_DOMAIN.' = :domain)', array('domain' => $domain));
      } else {
//         $groupsModel->where('('.Model_Groups::COLUMN_IS_ADMIN.' = 0)'
//            .' OR ('.Model_Groups::COLUMN_IS_ADMIN.' = 1 AND t_s.'.Model_Sites::COLUMN_ID.' IS NOT NULL)', 
//            array());
         
      }
      return $groupsModel->records();
   }

   private function assignRights($idc, $form)
   {
      VVE_SUB_SITE_DOMAIN == null ? $domain = 'www' : $domain = VVE_SUB_SITE_DOMAIN;
      $rModel = new Model_Rights();
      foreach ($this->getGroups() as $group) {
         // admin nenní nutné vyplňovat má vždy rwc
         $allowedDomains = explode(',', $group->domains);
//         Debug::log($group->{Model_Groups::COLUMN_NAME}.' save', $group->{Model_Groups::COLUMN_IS_ADMIN},in_array($domain, $allowedDomains), $group->domains);
         
         $grName = 'group_' . $group->{Model_Groups::COLUMN_NAME};
         if($form->haveElement($grName)){
            $right = $form->{$grName}->getValues();
         } 
         else if($group->{Model_Groups::COLUMN_IS_ADMIN}) {
            $right = 'rwc';
         } 
         else {
            $right = 'r--';
         }
         
         $rModel->where(Model_Rights::COLUMN_ID_CATEGORY.' = :idc AND '.Model_Rights::COLUMN_ID_GROUP.' = :idg', 
            array('idc' => $idc, 'idg' => $group->{Model_Groups::COLUMN_ID})
         );
         
         if($right == $form->rights_default->getValues() AND !in_array($domain, $allowedDomains)// odstranění práv u skupiny, které nepatří k webu
            OR ($group->{Model_Groups::COLUMN_IS_ADMIN} == true AND (in_array($domain, $allowedDomains) OR $group->domains == null))
            ){
            $rModel->delete(); // čištění
         } else {
            $rightRec = $rModel->record();
            if($rightRec == false) $rightRec = $rModel->newRecord();
            $rightRec->{Model_Rights::COLUMN_RIGHT} = $right;
            if($rightRec->isNew()){
               $rightRec->{Model_Rights::COLUMN_ID_CATEGORY} = $idc;
               $rightRec->{Model_Rights::COLUMN_ID_GROUP} = $group->{Model_Groups::COLUMN_ID};
            }
            $rModel->save($rightRec);
         }
      }
   }

   /**
    * Metoda vytvoří strukturu formuláře
    * @return Form
    */
   private function createForm()
   {
      $form = new Form('category', true);

      $form->addGroup('labels', $this->tr('Popisky'), $this->tr('Název a popisek kategorie'));
      // název kategorie
      $catName = new Form_Element_Text('name', $this->tr('Název'));
      $catName->setLangs();
      $catName->addValidation(new Form_Validator_NotEmpty(null, Locales::getDefaultLang(true)));
      $form->addElement($catName, 'labels');

      // popisek kategorie
      $catAlt = new Form_Element_Text('alt', $this->tr('Popisek'));
      $catAlt->setLangs();
      $form->addElement($catAlt, 'labels');

      // keywords
      $catKeywords = new Form_Element_Text('keywords', $this->tr('Klíčová slova'));
      $catKeywords->setLangs();
      $catKeywords->setSubLabel($this->tr('Pro vyhledávače'));
      $form->addElement($catKeywords, 'labels');

      // description
      $catDescription = new Form_Element_TextArea('description', $this->tr('Popis kategorie'));
      $catDescription->setLangs();
      $catDescription->setSubLabel($this->tr('Pro vyhledávače'));
      $form->addElement($catDescription, 'labels');

      // SETTINGS
      $form->addGroup('settings', $this->tr('Nastavení'), $this->tr('Položky související s nastavením kategorie'));

      // kaegorie
      $catSections = new Form_Element_Select('parent_cat', $this->tr('Nadřazená kategorie'));
      $form->addElement($catSections, 'settings');

      // moduly
      $moduleModel = new Model_Module();
      $modules = $moduleModel->getModules();
      $options = array();
      foreach ($modules as $module) {
         if($module[0] == '.' OR is_file(AppCore::getAppLibDir() . AppCore::MODULES_DIR . DIRECTORY_SEPARATOR
               . $module . DIRECTORY_SEPARATOR . 'docs' . DIRECTORY_SEPARATOR . self::MODULE_ADMIN_FILE)) continue;

         $moduleName = null;
         // pokud existuje dokumentace tak načteme název modulu
         if (file_exists(AppCore::getAppLibDir() . AppCore::MODULES_DIR . DIRECTORY_SEPARATOR
               . $module . DIRECTORY_SEPARATOR . 'docs' . DIRECTORY_SEPARATOR . self::MODULE_SPEC_FILE)) {
            $mcnt = file_get_contents(AppCore::getAppLibDir() . AppCore::MODULES_DIR . DIRECTORY_SEPARATOR
                  . $module . DIRECTORY_SEPARATOR . 'docs' . DIRECTORY_SEPARATOR . self::MODULE_SPEC_FILE);
            $matches = array();
            if (preg_match('/class="moduleName">([^<]*)</', $mcnt, $matches)) {
               $moduleName .= $matches[1] . ' - ';
            }
         }
         $moduleName .= $module;
         $options[$moduleName] = $module;
      }

      ksort($options);


      $catModule = new Form_Element_Select('module', $this->tr('Modul'));
      $catModule->setOptions($options);
      $form->addElement($catModule, 'settings');

      $catFeedExp = new Form_Element_Checkbox('feedexp', $this->tr('Povolit export zdrojů'));
      $catFeedExp->setSubLabel($this->tr('Pokud modul podporuje export RSS/ATOM zdrojů'));
      $catFeedExp->setValues(true);
      $form->addElement($catFeedExp, 'settings');

      $catDataDir = new Form_Element_Text('datadir', $this->tr('Adresář s daty'));
      $catDataDir->setSubLabel($this->tr('Název datového adresář (ne cestu). Do něj budou ukládány všechyn soubory modulu.
         Pokud zůstane prázdný, použije se název modulu. POZOR! změna tohoto parametru může zapříčinit ztrátu dat!'));
      $form->addElement($catDataDir, 'labels');

      // url klíč kategorie
      $catUrlKey = new Form_Element_Text('urlkey', $this->tr('Url klíč'));
      $catUrlKey->setLangs();
      $catUrlKey->setSubLabel($this->tr('Pokud není zadán, je url klíč generován automaticky'));
      $form->addElement($catUrlKey, 'settings');

      // priorita
      $catPriority = new Form_Element_Text('priority', $this->tr('Priorita kategorie'));
      $catPriority->setSubLabel('Čím větší tím bude větší šance, že kategorie bude vybrána jako výchozí');
      $catPriority->addValidation(New Form_Validator_IsNumber());
      $catPriority->addValidation(New Form_Validator_Length(1, 4));
      $catPriority->setValues(0);
      $form->addElement($catPriority, 'settings');

      // panely
      $catLeftPanel = new Form_Element_Checkbox('individual_panels', $this->tr('Panely'));
      $catLeftPanel->setSubLabel($this->tr('Zapnutí individuálního nastavení panelů'));
      $form->addElement($catLeftPanel, 'settings');

//      $catShowInMenu = new Form_Element_Checkbox('show_in_menu', $this->tr('Zobrazit v menu'));
//      $form->addElement($catShowInMenu, 'settings');
//
//      $catShowOnlyWhenLogin = new Form_Element_Checkbox('show_when_login_only', $this->tr('Zobrazit pouze při přihlášení'));
//      $form->addElement($catShowOnlyWhenLogin, 'settings');
      $catVisibility = new Form_Element_Select('visibility', $this->tr('Viditelnost'));
      $catVisibility->setOptions(array_flip($this->getVisibilityTypes()));
      $form->addElement($catVisibility, 'settings');


      // práva
      $form->addGroup('rights', $this->tr('Práva'), $this->tr('Nastavení práv ke kategorii (r - čtení, w - zápis, c - úplná kontrola)'));

      // pole s typy práv
      $rightsTypes = array(
         $this->tr('Pouze čtení (r--)') => 'r--', 
         $this->tr('Pouze zápis (-w-)') => '-w-', 
         $this->tr('Pouze kontrola (--c)') => '--c', 
         $this->tr('Čtení a zápis (rw-)') => 'rw-',
         $this->tr('Čtení a kontrola (r-c)') => 'r-c', 
         $this->tr('Zápis a kontrola (-wc)') => '-wc', 
         $this->tr('Všechna oprávnění (rwc)') => 'rwc', 
         $this->tr('Žádná oprávnění (---)') => '---');

      // výchozí práva kategorie
      $catGrpRigths = new Form_Element_Select('rights_default', $this->tr('Výchozí práva'));
      $catGrpRigths->setOptions($rightsTypes);
      $catGrpRigths->setSubLabel($this->tr('Výchozí práva pro nově přidané skupiny a všechny ostatní uživatele'));
      $form->addElement($catGrpRigths, 'rights');

      VVE_SUB_SITE_DOMAIN == null ? $domain = 'www' : $domain = VVE_SUB_SITE_DOMAIN;
      foreach ($this->getGroups() as $group) {
         // admin nenní nutné vyplňovat má vždy rwc
         $allowedDomains = explode(',', $group->domains);
         if ($group->{Model_Groups::COLUMN_IS_ADMIN} == true 
            AND (in_array($domain, $allowedDomains) OR $group->domains == null)){
            continue;
         }
         
         $catGrpRigths = new Form_Element_Select('group_' . $group->{Model_Groups::COLUMN_NAME},
               sprintf($this->tr("Skupina\n \"%s\""), $group->{Model_Groups::COLUMN_NAME}));
         $catGrpRigths->setSubLabel(sprintf($this->tr("Skupina\n \"%s\""), $group->{Model_Groups::COLUMN_LABEL}));
         $catGrpRigths->setOptions($rightsTypes);
         $catGrpRigths->setValues(reset($rightsTypes));
         $form->addElement($catGrpRigths, 'rights');
      }

      $form->addGroup('sitemap', $this->tr('Mapa stránek'), $this->tr('Nastavení mapy stránek pro vyhledávače'));

      // nastvaení SITEMAPS
      // priorita
      $catSitemapPriority = new Form_Element_Text('sitemap_priority', $this->tr('Priorita kategorie v sitemap'));
      $catSitemapPriority->setSubLabel('0 - 1, čím větší, tím výše kategorie bude');
      $catSitemapPriority->setValues(0);
      $catSitemapPriority->addValidation(New Form_Validator_IsNumber(null, Form_Validator_IsNumber::TYPE_FLOAT));
      $form->addElement($catSitemapPriority, 'sitemap');

      // frekvence změny
      $freqOptions = array($this->tr('Vždy') => 'always', $this->tr('každou hodinu') => 'hourly',
         $this->tr('Denně') => 'daily', $this->tr('Týdně') => 'weekly', $this->tr('Měsíčně') => 'monthly',
         $this->tr('Ročně') => 'yearly', $this->tr('Nikdy') => 'never');
      $catSitemapChangeFrequency = new Form_Element_Select('sitemap_frequency', $this->tr('Frekvence změn'));
      $catSitemapChangeFrequency->setOptions($freqOptions);
      $catSitemapChangeFrequency->setValues('yearly');
      $form->addElement($catSitemapChangeFrequency, 'sitemap');


      // tlačítko odeslat
//      $submitButton = new Form_Element_Submit('send', $this->tr('Uložit'));
//      $form->addElement($submitButton);
      $submitButton = new Form_Element_SaveCancel('send');
      $form->addElement($submitButton);

      $elemGoSet = new Form_Element_Checkbox('gotoSettings', $this->tr('Přejít na nastavení kategorie'));
      $elemGoSet->setSubLabel($this->tr('Každá kategorie má podle zvoleného modulu další nastavení. Např: modul "articles" má počet článků na stránku.'));

      $form->addElement($elemGoSet);

      return $form;
   }

   private function catsToArrayForForm($categories)
   {
      // pokud je hlavní kategorie
      if ($categories->getLevel() != 0) {
         $this->categoriesArray[str_repeat('.', $categories->getLevel() * 3) .
            (string) $categories->getCatObj()->getLabel() . ' - id: ' . $categories->getCatObj()->getId()]
            = (string) $categories->getCatObj()->getId();
      } else {
         $this->categoriesArray[$this->tr('Kořen')] = 0;
      }
      if (!$categories->isEmpty()) {
         foreach ($categories as $cat) {
            $this->catsToArrayForForm($cat);
         }
      }
   }

   public function moduleDocController()
   {
      if (file_exists(AppCore::getAppLibDir() . AppCore::MODULES_DIR . DIRECTORY_SEPARATOR
            . $this->getRequestParam('module') . DIRECTORY_SEPARATOR . 'docs' . DIRECTORY_SEPARATOR . self::MODULE_SPEC_FILE)) {
         $this->view()->doc = file_get_contents(AppCore::getAppLibDir() . AppCore::MODULES_DIR . DIRECTORY_SEPARATOR
               . $this->getRequestParam('module') . DIRECTORY_SEPARATOR . 'docs' . DIRECTORY_SEPARATOR . self::MODULE_SPEC_FILE);
      } else {
         $this->view()->doc = $this->tr('Dokumentace k modulu neexistuje');
      }
   }

   public function catSettingsController()
   {
      $this->checkWritebleRights();
      $categoryM = new Model_Category();
      $cat = $categoryM->record($this->getRequest('categoryid'));
      if ($cat === false)
         return false;
      $this->view()->catName = $cat->{Model_Category::COLUMN_CAT_LABEL};
      $this->view()->moduleName = $cat->{Model_Category::COLUMN_MODULE};
      $ctrlClass = ucfirst($cat->{Model_Category::COLUMN_MODULE}) . '_Controller';
      $viewClass = ucfirst($cat->{Model_Category::COLUMN_MODULE}) . '_View';
      $ctrl = new $ctrlClass(new Category(null, false, $cat), $this->routes(), $this->view(), $this->link());
      $ctrl->viewSettingsController();
      $this->view()->mview = $ctrl->view();
   }

   private function isMainStruct()
   {
//       if (isset($_SESSION['structAdmin']) AND $_SESSION['structAdmin'] == true) {
//          return false;
//       }
      return true;
   }

   private function setMainStruct($main = true)
   {
//       if ($main === true) {
//          $_SESSION['structAdmin'] = false;
//          $this->isMainStruct = true;
//          unset($_SESSION['structAdmin']);
//       } else {
//          $this->isMainStruct = false;
//          $_SESSION['structAdmin'] = true;
//       }
   }

   private function gotoBack()
   {
//       if ($this->isMainStruct()) {
         $this->link()->route()->param('id')->reload();
//       } else {
//          $this->link()->route('adminMenu')->param('id')->reload();
//       }
   }

   public function changeIndPanelsController()
   {
      $this->checkWritebleRights();
      if ($this->getRequestParam('idc', 0) == 0) {
         $this->errMsg()->addMessage($this->tr('Chybně přenesené ID kategorie'));
         return;
      }
      $enabled = $this->getRequestParam('enabled', false);
      if ($enabled == 'false') {
         $enabled = false;
      } else if ($enabled == 'true') {
         $enabled = true;
      }

      $model = new Model_Category();

      $record = $model->record($this->getRequestParam('idc'));
      $this->view()->data = var_export($enabled, true);

      if ($record != false) {
         $record->{Model_Category::COLUMN_INDIVIDUAL_PANELS} = $enabled;
         $model->save($record);
         $this->infoMsg()->addMessage($this->tr('Panely byly změněny'));
      }
   }

   public function changeVisibilityController()
   {
      $this->checkWritebleRights();
      if ($this->getRequestParam('idc', 0) == 0) {
         $this->errMsg()->addMessage($this->tr('Chybně přenesené ID kategorie'));
         return;
      }
      $visId = $this->getRequestParam('value', Model_Category::VISIBILITY_ALL);

      $visTypes = $this->getVisibilityTypes();
      if(!isset ($visTypes[$visId])){
         $this->errMsg()->addMessage($this->tr('Nesprávný typ viditelnosti'));
         return;
      }

      $model = new Model_Category();
      $record = $model->record($this->getRequestParam('idc'));
      $record->{Model_Category::COLUMN_VISIBILITY} = $visId;
      $model->save($record);
      $this->infoMsg()->addMessage($this->tr('Viditelnost byla změněna'));
   }

   public function getCatInfoController()
   {
      $this->checkWritebleRights();
      $idc = $this->getRequestParam('idc', 0);

      $model = new Model_Category();
      $modelR = new Model_Rights();

      $cat = $model->record($idc);
      if ($cat == false) {
         throw new UnexpectedValueException($this->tr('Kategorie nenalezena'));
      }

      $this->view()->catInfo = $cat;


      $rights = $modelR->joinFK(Model_Rights::COLUMN_ID_GROUP, array(Model_Groups::COLUMN_NAME))->where(Model_Rights::COLUMN_ID_CATEGORY . ' = :idc', array('idc' => $idc))->records();
      $this->view()->catRights = $rights;
      $this->view()->visTypes = $this->getVisibilityTypes();
   }

   public function moveCatController()
   {
      $this->checkWritebleRights();

      $idc = $this->getRequestParam('idc');
      $newParentCatId = $this->getRequestParam('parent', 0);
      $position = $this->getRequestParam('position', 0);
      $regenerate = ($this->getRequestParam('regenerate', false) != 'false');

      try {
         $this->moveCategory($idc, $newParentCatId, $regenerate, $position);
         $this->infoMsg()->addMessage($this->tr('Struktura byla změněna'));
         $this->log('Úprava struktury menu');
      } catch (Exception $exc) {
         $this->errMsg()->addMessage($this->tr("Chyba při přesunu struktury"));
      }
   }

   private function moveCategory($idMovedCat, $idNewParentCat, $regenerateUrls = false, $position = 0)
   {
      $structure = Category_Structure::getStructure(!$this->isMainStruct());
      $movedCat = $structure->getCategory($idMovedCat);
      // definice ve struktuře pro přesun
      $oldParentCatId = $movedCat->getParentId();

      // přesun do jiné kategorie
      $structure->removeCat($idMovedCat);
      $structure->addChild($movedCat, $idNewParentCat, $position);
      $structure->saveStructure();

      // pokud se přesunuje ve struktuře
      if ($oldParentCatId != $idNewParentCat AND $regenerateUrls) {
         $modelCat = new Model_Category();
         // dočtou se data o kategoriích
         $movedCat->withHidden(true);// (i zkryté)
         $movedCat->setCategories($modelCat->setSelectAllLangs(true)->records(Model_ORM::FETCH_PKEY_AS_ARR_KEY));

         // generace polí
         $newParentUrlKeys = array();
         foreach (Locales::getAppLangs() as $lang) {
            $newParentUrlKeys[$lang] = null;
         }
         
         // url kíč nové nadřazené kategorie
         if($idNewParentCat != 0){
            $newParentCat = $modelCat->where($idNewParentCat)->record();
            if($newParentCat != false){
               $newParentUrlKeys = $newParentCat->{Model_Category::COLUMN_URLKEY};
            }
         }
         
         $this->repairUrlKeys($modelCat, $movedCat, $newParentUrlKeys);
         
         $this->view()->status = 'moving';
      }
   }


      /**
    * Meoda vrátí pole s kategoriema, které jsou potomky upravované kategorie (včetně upravované kategorie)
    * @param Category_Structure $category
    * @return <type>
    */
   private function getCategoryChildrens(Category_Structure $category)
   {
      $returnArr = array();
      if(!$category->isEmpty()){
         foreach ($category->getChildrens() as $child) {
            array_push($returnArr, $child->getCatObj()->getCatDataObj());
            $returnArr = array_merge($returnArr, $this->getCategoryChildrens($child));
         }
      }
      return $returnArr;
   }

   /**
    * Metoda opraví všechny url klíče kategorie a jejích potomků
    * @param Model_Category $model
    * @param Category_Structure $category
    * @param type $parentUrlKeys 
    */
   private function repairUrlKeys(Model_Category $model, Category_Structure $category, $parentUrlKeys = array())
   {
      $record = $category->getCatObj()->getCatDataObj();
//      var_dump($parentUrlKeys);
//      echo 'old url keys<br />';
//      var_dump($record[Model_Category::COLUMN_URLKEY]);
      // jednotlivé jazyky
      foreach ($parentUrlKeys as $lang => $parentKey) {
         $lastpos = strrpos($record[Model_Category::COLUMN_URLKEY][$lang], '/');
         if($lastpos !== false){
            $urlpart = substr($record[Model_Category::COLUMN_URLKEY][$lang], $lastpos+1);
         } else {
            $urlpart = $record[Model_Category::COLUMN_URLKEY][$lang];
         }
         
//         echo 'urlpart '.$lang.' '.$urlpart.'<br />';
         // pokud nemá definovaný url klíč
         if($urlpart == null && $record[Model_Category::COLUMN_URLKEY][$lang] != null){
            $urlpart = vve_cr_url_key($record[Model_Category::COLUMN_URLKEY][$lang]);
         }
         // ????
         if($urlpart == null){ continue; }
         
         $record[Model_Category::COLUMN_URLKEY][$lang] = $parentKey == null ? $urlpart : $parentKey.'/'.$urlpart;
      }
      // save
//      var_dump($record[Model_Category::COLUMN_URLKEY]);
      $model->save($record);
      
      // potomci
      if(!$category->isEmpty()){
         foreach ($category->getChildrens() as $chidl) {
            $this->repairUrlKeys($model, $chidl, $record[Model_Category::COLUMN_URLKEY]);
         }
      }
      
   }


   private function getVisibilityTypes()
   {
      return array(
         Model_Category::VISIBILITY_HIDDEN => $this->tr('Nikomu'),
         Model_Category::VISIBILITY_WHEN_ADMIN => $this->tr('Pouze administrátorům'),
//         Model_Category::VISIBILITY_WHEN_ADMIN_ALL => $this->tr('Pouze admin. (včetně subdomén)'),
         Model_Category::VISIBILITY_WHEN_LOGIN => $this->tr('Pouze přihlášeným'),
         Model_Category::VISIBILITY_WHEN_NOT_LOGIN => $this->tr('Pouze nepřihlášeným'),
         Model_Category::VISIBILITY_ALL => $this->tr('Všem')
      );
   }
}
?>