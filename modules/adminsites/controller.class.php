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
            $tables = $db->getTablesByPrefix($site->{Model_Sites::COLUMN_TB_PREFIX});
            
            foreach ($tables as $table) {
//               Model_DbSupport::dropTable($table);
            }
            
            $dir = new FS_Dir($site->{Model_Sites::COLUMN_DIR}, AppCore::getAppLibDir());
            if($dir->exist()){
//               $dir->delete();
            }
            $model->delete($site);
            $this->infoMsg()->addMessage($this->tr('Web byl smazán'));
         }
         $this->link()->route()->redirect();
      }
      $this->view()->formDelete = $fDelete;
      
      $sites = $model
          ->where(Model_Sites::COLUMN_IS_MAIN." = 0", array())
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
         
         $this->processNewSiteConfig($newDir, $dbPrefix);
         
         // donasatvení konfigurací
         Model_Config::setSiteConfigValue($dbPrefix, 'WEB_NAME', $form->name->getValues());
         Model_Config::setSiteConfigValue($dbPrefix, 'MAIN_PAGE_TITLE', $form->name->getValues());
         Model_Config::setSiteConfigValue($dbPrefix, 'TEMPLATE_FACE', $form->face->getValues());
         
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
      
      $eSave = new Form_Element_SaveCancel('save');
      $f->addElement($eSave);
      
      if($site){
         $f->name->setValues(Model_Config::getSiteConfigValue($site->{Model_Sites::COLUMN_TB_PREFIX}, 'WEB_NAME'));
         $f->face->setValues(Model_Config::getSiteConfigValue($site->{Model_Sites::COLUMN_TB_PREFIX}, 'TEMPLATE_FACE'));
         $f->domain->setValues($site->{Model_Sites::COLUMN_DOMAIN});
         $f->domain->setSubLabel($this->tr('Změna domény neupraví složku s daty.'));
      }
      
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
   
   protected function createWebsiteDir($newdir)
   {
      $tplDir = new FS_Dir(self::SITES_TPL_DIR, AppCore::getAppLibDir());
      $tplDir->copyContent(AppCore::getAppLibDir().$newdir);
      
   }
   
}
