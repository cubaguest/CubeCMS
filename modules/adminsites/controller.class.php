<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class AdminSites_Controller extends Controller {

   const SITES_TPL_DIR = '_subsite_tpl';

   protected function init()
   {
      $this->checkControllRights();
   }
   
   public function mainController()
   {
      if(!is_dir($this->getSitesTplDir())){
         $this->errMsg()->addMessage($this->tr('Systém nepodporuje přidávání dalších webů. Kontaktujte webmastera.'));
         return true;
      }
      
      $model = new Model_Sites();
      
      $fDelete = new Form('sitedel');
      $eid = new Form_Element_Hidden('id');
      $fDelete->addElement($eid);
      
      $eDel = new Form_Element_Submit('del', $this->tr('Smazat web'));
      $fDelete->addElement($eDel);
      
      if($fDelete->isValid()){
         // detekce jestli je alias
         $site = $model->record($fDelete->id->getValues());
         
         if($site->{Model_Sites::COLUMN_IS_ALIAS}){
            $model->delete($site);
            $this->infoMsg()->addMessage($this->tr('Alias byl smazán'));
         } else {
            $db = new Model_DbSupport();
            if($site->{Model_Sites::COLUMN_TB_PREFIX} == null){
               throw new UnexpectedValueException('Pokus o smazání celé db');
            }
            $tables = $db->getTablesByPrefix($site->{Model_Sites::COLUMN_TB_PREFIX});
            if(!empty($tables)){
               foreach ($tables as $table) {
                  Model_DbSupport::dropTable($table);
               }
            }
            // dir
            if($site->{Model_Sites::COLUMN_DIR} == null){
               throw new UnexpectedValueException('Pokus o smazání kořene webu');
            }
            $dir = new FS_Dir($site->{Model_Sites::COLUMN_DIR}, AppCore::getAppLibDir());
            if($dir->exist()){
               $dir->delete();
            }
            
            // mazání aliasu
            $model
                ->where(Model_Sites::COLUMN_IS_ALIAS." = 1 AND ".Model_Sites::COLUMN_DIR." = :dir", array('dir' => $site->{Model_Sites::COLUMN_DIR}))
                ->delete();
            $model->delete($site);
            $this->infoMsg()->addMessage($this->tr('Web včetně aliasů byl smazán'));
         }
         $this->link()->route()->redirect();
      }
      $this->view()->formDelete = $fDelete;
      
      $sites = $model
          ->where(Model_Sites::COLUMN_IS_MAIN." = 0", array())
          ->order(Model_Sites::COLUMN_DOMAIN)
          ->records();
      
      $this->view()->sites = $sites;
   }
   
   public function addSiteController()
   {
      $form = $this->createSiteEditForm();
      
      if($form->isValid()){
         $model = new Model_Sites();
         $domain = $form->domain->getValues();
         $newDir = str_replace(array('.', '/', '-', '_'), array('','','',''), $domain);
         $dbPrefix = $newDir.'_';
         
         // create face
         $this->createWebsiteDir($newDir);
         
         $this->createNewSiteDb($dbPrefix);
         if(isset($form->dataset) && $form->dataset->getValues() != null){
            $this->installSiteScript($dbPrefix, $form->dataset->getValues(), $newDir);
            $this->createNewSiteData($dbPrefix, $form->dataset->getValues(), $newDir);
         }
//         die;
         $this->processNewSiteConfig($newDir, $dbPrefix);
         
         // donasatvení konfigurací
         Model_Config::setSiteConfigValue($dbPrefix, 'WEB_NAME', $form->name->getValues());
         Model_Config::setSiteConfigValue($dbPrefix, 'MAIN_PAGE_TITLE', $form->name->getValues());
         Model_Config::setSiteConfigValue($dbPrefix, 'TEMPLATE_FACE', $form->face->getValues());
         Model_Config::setSiteConfigValue($dbPrefix, 'SUB_SITE_DIR', $newDir);
         
         $newDomain = $model->newRecord();
         $newDomain->{Model_Sites::COLUMN_DIR} = $newDir;
         $newDomain->{Model_Sites::COLUMN_DOMAIN} = $domain;
         $newDomain->{Model_Sites::COLUMN_TB_PREFIX} = $dbPrefix;
         $newDomain->save();
         
         AdminHtaccess_Controller::generateMainHtaccess();
         AdminHtaccess_Controller::generateSubHtaccess($newDir);
         
         $this->infoMsg()->addMessage($this->tr('Web byl vytvořen'));
         $this->link()->route()->redirect();
      }
         
      $this->view()->form = $form;
   }
   
   public function editSiteController($id)
   {
      
      $model = new Model_Sites();
      $record = $model->record($id);
      
      if(!$record){
         throw new UnexpectedPageException();
      }
      $form = $this->createSiteEditForm($record);
      $this->view()->site = $record;
      
      if($form->isValid()){
         $model = new Model_Sites();
         $domain = $form->domain->getValues();
         
         // donasatvení konfigurací
         Model_Config::setSiteConfigValue($record->{Model_Sites::COLUMN_TB_PREFIX}, 'WEB_NAME', $form->name->getValues());
         Model_Config::setSiteConfigValue($record->{Model_Sites::COLUMN_TB_PREFIX}, 'MAIN_PAGE_TITLE', $form->name->getValues());
         Model_Config::setSiteConfigValue($record->{Model_Sites::COLUMN_TB_PREFIX}, 'TEMPLATE_FACE', $form->face->getValues());
         
         $record->{Model_Sites::COLUMN_DOMAIN} = $domain;
         $record->save();
         
         AdminHtaccess_Controller::generateMainHtaccess();
         AdminHtaccess_Controller::generateSubHtaccess($record->{Model_Sites::COLUMN_DIR});
         
         $this->infoMsg()->addMessage($this->tr('Web byl uložen'));
         $this->link()->route()->redirect();
      }
         
      $this->view()->form = $form;
   }
   
   public function addAliasController()
   {
      $form = $this->createAliasEditForm();
      
      if($form->isValid()){
         $model = new Model_Sites();
         
         $original = $model
             ->where(Model_Sites::COLUMN_DIR.' = :dir', array('dir' => $form->dir->getValues()))
             ->record();
         
         $new = $model->newRecord();
         $new->{Model_Sites::COLUMN_DOMAIN} = $form->domain->getValues();
         $new->{Model_Sites::COLUMN_DIR} = $original->{Model_Sites::COLUMN_DIR};
         $new->{Model_Sites::COLUMN_TB_PREFIX} = $original->{Model_Sites::COLUMN_TB_PREFIX};
         $new->{Model_Sites::COLUMN_IS_ALIAS} = true;
         $new->save();
         
         AdminHtaccess_Controller::generateMainHtaccess();
         AdminHtaccess_Controller::generateSubHtaccess($new->{Model_Sites::COLUMN_DIR});
         
         $this->infoMsg()->addMessage($this->tr('Alias byl vytvořen'));
         $this->link()->route()->redirect();
      }
         
      $this->view()->form = $form;
   }
   
   public function editAliasController($id)
   {
      $model = new Model_Sites();
      $record = $model->record($id);
      if(!$record){
         throw new UnexpectedPageException();
      }
      $form = $this->createAliasEditForm($record);
      $this->view()->site = $record;
      
      if($form->isValid()){
         $original = $model
             ->where(Model_Sites::COLUMN_DIR.' = :dir', array('dir' => $form->dir->getValues()))
             ->record();
         
         $record->{Model_Sites::COLUMN_DOMAIN} = $form->domain->getValues();
         $record->{Model_Sites::COLUMN_DIR} = $original->{Model_Sites::COLUMN_DIR};
         $record->{Model_Sites::COLUMN_TB_PREFIX} = $original->{Model_Sites::COLUMN_TB_PREFIX};
         $record->save();

         AdminHtaccess_Controller::generateMainHtaccess();
         AdminHtaccess_Controller::generateSubHtaccess($record->{Model_Sites::COLUMN_DIR});
         
         $this->infoMsg()->addMessage($this->tr('Alias byl vytvořen'));
         $this->link()->route()->redirect();
      }
         
      $this->view()->form = $form;
   }
   
   
   protected function createSiteEditForm(Model_ORM_Record $site = null)
   {
      $f = new Form('siteedit');
      // domian
      
      $eName = new Form_Element_Text('name', $this->tr('Jméno'));
      $eName->addValidation(new Form_Validator_NotEmpty());
      $f->addElement($eName);
      
      $eDomain = new Form_Element_Text('domain', $this->tr('Doména'));
      $eDomain->setSubLabel($this->tr('Buď celý název domény nebo jenom název subdomény (3.řád)'));
      $eDomain->addValidation(new Form_Validator_NotEmpty());
      $f->addElement($eDomain);
      
      // vzhled
      $eFace = new Form_Element_Select('face', $this->tr('Vzhled'));
      foreach (Face::getFaces() as $dir => $face) {
         $eFace->addOption($face['name'].' ('.$face['version'].')', $dir);
      }
      $f->addElement($eFace);
      
      if($site){
         $f->name->setValues(Model_Config::getSiteConfigValue($site->{Model_Sites::COLUMN_TB_PREFIX}, 'WEB_NAME'));
         $f->face->setValues(Model_Config::getSiteConfigValue($site->{Model_Sites::COLUMN_TB_PREFIX}, 'TEMPLATE_FACE'));
         $f->domain->setValues($site->{Model_Sites::COLUMN_DOMAIN});
         $f->domain->setSubLabel($this->tr('Změna domény neupraví složku s daty.'));
      } else {
         $datasets = new DirectoryIterator($this->getSitesTplDir().'_install');
         
         if(!empty($datasets)){
            $elemBaseData = new Form_Element_Select('dataset', $this->tr('Naplnit daty'));
            $elemBaseData->addOption($this->tr('Žádná data'), false);
            
            foreach ($datasets as $dir) {
               if($dir->isDot()){
                  continue;
               }
               $desc = $dir->getBasename();
               if(is_file($dir->getRealPath().DIRECTORY_SEPARATOR.'name.txt')){
                  $desc = file_get_contents($dir->getRealPath().DIRECTORY_SEPARATOR.'name.txt');
               }
               $elemBaseData->addOption($desc, $dir->getBasename());
            }
            $f->addElement($elemBaseData);
         }
      }
      
      $eSave = new Form_Element_SaveCancel('save');
      $f->addElement($eSave);
      
      if($f->isSend() && $f->save->getValues() == false){
         $this->link()->route()->redirect();
      }
      
      return $f;
   }
   
   protected function createAliasEditForm(Model_ORM_Record $site = null)
   {
      $f = new Form('aliasedit');
      // domian
      
      $eDomain = new Form_Element_Text('domain', $this->tr('Doména'));
      $eDomain->setSubLabel($this->tr('Buď celý název domény nebo jenom název subdomény (3.řád)'));
      $eDomain->addValidation(new Form_Validator_NotEmpty());
      $f->addElement($eDomain);
      
      // vzhled
      $eDir = new Form_Element_Select('dir', $this->tr('Složka'));
      $modelSites = new Model_Sites();
      
      $sites = $modelSites
          ->order(Model_Sites::COLUMN_DIR)
          ->where(Model_Sites::COLUMN_IS_MAIN." = 0 AND ".Model_Sites::COLUMN_IS_ALIAS.' = 0', array())
          ->records();
      
      foreach ($sites as $s) {
         $eDir->addOption($s->{Model_Sites::COLUMN_DIR}.' ('.$s->getFullDomain().')', $s->{Model_Sites::COLUMN_DIR});
      }
      $f->addElement($eDir);
      
      $eSave = new Form_Element_SaveCancel('save');
      $f->addElement($eSave);
      
      if($site){
         $f->domain->setValues($site->{Model_Sites::COLUMN_DOMAIN});
         $f->dir->setValues($site->{Model_Sites::COLUMN_DIR});
      }
      
      if($f->isSend() && $f->save->getValues() == false){
         $this->link()->route()->redirect();
      }
      
      return $f;
   }

   protected function getSitesTplDir()
   {
      return AppCore::getAppLibDir().self::SITES_TPL_DIR.DIRECTORY_SEPARATOR;
   }

   protected function processNewSiteConfig($dir, $dbPrefix)
   {
      $cnt = file_get_contents(AppCore::getAppLibDir().$dir.DIRECTORY_SEPARATOR.AppCore::ENGINE_CONFIG_DIR.DIRECTORY_SEPARATOR.'config.php');
      $newCnt = str_replace('{PREFIX}', $dbPrefix, $cnt);
      file_put_contents(AppCore::getAppLibDir().$dir.DIRECTORY_SEPARATOR.AppCore::ENGINE_CONFIG_DIR.DIRECTORY_SEPARATOR.'config.php', $newCnt);
   }
   
   protected function createNewSiteDb($dbPrefix)
   {
      $dbConnection = Db_PDO::getInstance();
      
      $sqlCnt = file_get_contents(AppCore::getAppLibDir().'install'.DIRECTORY_SEPARATOR.'sql'.DIRECTORY_SEPARATOR.'install_subsite.sql');
      $cntForRun = str_replace('{PREFIX}', $dbPrefix, $sqlCnt);
      
      $dbConnection->exec($cntForRun);
   }
   
   protected function createNewSiteData($dbPrefix, $datasetName, $siteDir)
   {
      $dbConnection = Db_PDO::getInstance();
      
      $sqlCnt = file_get_contents($this->getSitesTplDir().'_install'.DIRECTORY_SEPARATOR. $datasetName .DIRECTORY_SEPARATOR.'data.sql');
      $cntForRun = str_replace('{PREFIX}', $dbPrefix, $sqlCnt);
      $dbConnection->exec($cntForRun);
      
      // kopírování dat
      $dataDir = new FS_Dir('data', $this->getSitesTplDir().'_install'.DIRECTORY_SEPARATOR. $datasetName);
      if($dataDir->exist()){
         $dataDir->copyContent(AppCore::getAppLibDir().$siteDir.DIRECTORY_SEPARATOR.CUBE_CMS_DATA_DIR);
      }
   }
   
   protected function installSiteScript($dbPrefix, $datasetName, $newDir)
   {
      // moduly
      if(is_file($this->getSitesTplDir().'_install'.DIRECTORY_SEPARATOR. $datasetName .DIRECTORY_SEPARATOR.'modules.txt')){
         $modules = file($this->getSitesTplDir().'_install'.DIRECTORY_SEPARATOR. $datasetName .DIRECTORY_SEPARATOR.'modules.txt', FILE_IGNORE_NEW_LINES);
         if(!empty($modules)){
//            $dbConnection = Db_PDO::getInstance();
            
            foreach ($modules as $module) {
               $class = ucfirst($module).'_Module';
               if(class_exists($class)){
                  $mObj = new $class($module, array(), null, $dbPrefix);
               } else {
                  $mObj = new Module($module, array(), null, $dbPrefix);
               }    
            }
         }
      }
//      die;
      
//      $modulesFile = str_replace('data.sql', 'modules.txt', $file);
//      $phpFile = str_replace('data.sql', 'install.php', $file);
//      
//      if(is_file($this->getSitesTplDir().$modulesFile)){
//         
//      }
//      
//      if(is_file($this->getSitesTplDir().$phpFile)){
//         include_once $this->getSitesTplDir().$phpFile;
//      }
   }
   
   protected function createWebsiteDir($newdir)
   {
      $tplDir = new FS_Dir(self::SITES_TPL_DIR, AppCore::getAppLibDir());
      if(!is_dir(AppCore::getAppLibDir().$newdir)){
         $tplDir->copyContent(AppCore::getAppLibDir().$newdir);
      }
      if(is_dir(AppCore::getAppLibDir().$newdir.DIRECTORY_SEPARATOR.'_install')){
         FS_Dir::deleteStatic(AppCore::getAppLibDir().$newdir.DIRECTORY_SEPARATOR.'_install');
      }
      
   }
   
}
