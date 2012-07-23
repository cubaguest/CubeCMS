<?php
class Banners_Controller extends Controller {
   const DATA_DIR = 'banners';


   protected function init() {
      parent::init();
      $this->module()->setDataDir(self::DATA_DIR);
   }

   /**
    * Kontroler pro zobrazení novinek
    */
   public function mainController() 
   {
      //		Kontrola práv
      $this->checkControllRights();
      $model = new Banners_Model();
      $modelClicks = new Banners_Model_Clicks();

      $formDelete = new Form('banner_delete_');
      $eId = new Form_Element_Hidden('id');
      $formDelete->addElement($eId);
      
      $eDelete = new Form_Element_Submit('delete', $this->tr('Smazat'));
      $formDelete->addElement($eDelete);
      
      if($formDelete->isValid()){
         $model->delete($formChangeStatus->id->getValues());
         $this->infoMsg()->addMessage($this->tr('Banner byl smazán'));
         $this->link()->reload();
      }
      
      $this->view()->formDelete = $formDelete;
      
      $formChangeStatus = new Form('banner_status_');
      $eId = new Form_Element_Hidden('id');
      $formChangeStatus->addElement($eId);
      
      $echange = new Form_Element_Submit('change', $this->tr('Změnit stav'));
      $formChangeStatus->addElement($echange);
      
      if($formChangeStatus->isValid()){
         $banner = $model->record($formChangeStatus->id->getValues());
         $banner->{Banners_Model::COLUMN_ACTIVE} = !$banner->{Banners_Model::COLUMN_ACTIVE};
         $model->save($banner);
         $this->infoMsg()->addMessage($this->tr('Stav baneru byl změněn'));
         $this->link()->reload();
      }
      $this->view()->formChangeStatus = $formChangeStatus;
      
      // načtení abnerů a boxů
      $boxes = self::getBoxes();
      
      $banners = $model
      ->columns(array('*', 'clicks' => 
          '(SELECT COUNT(*) FROM '.$modelClicks->getTableName().' AS tbc '
            .'WHERE tbc.'.Banners_Model_Clicks::COLUMN_ID_BANNER
              .' = '.$model->getTableShortName().'.'.Banners_Model::COLUMN_ID.' '
            .' AND '.Banners_Model_Clicks::COLUMN_TIME.' >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH) )'
          ))
      ->order(array(Banners_Model::COLUMN_BOX => Model_ORM::ORDER_ASC, Banners_Model::COLUMN_ORDER => Model_ORM::ORDER_ASC))
      ->records();
      
      foreach ($banners as $banner) {
         if(isset($boxes[$banner->{Banners_Model::COLUMN_BOX}])) {
            $boxes[$banner->{Banners_Model::COLUMN_BOX}]['banners'][] = $banner;
         }
      }
      
      $this->view()->banners = $banners;
      $this->view()->boxes = $boxes;
   }

   public function addController() 
   {
      $this->checkControllRights();
      
      $form = $this->createForm();
      
      if($form->isValid()){
         $this->saveBanner($form);
         
         $this->infoMsg()->addMessage($this->tr('Banner byl uložen'));
         $this->link()->route()->reload();
      }
      
      $this->view()->form = $form;
   }
   
   public function editController()
   {
      $this->checkControllRights();
      $model = new Banners_Model();
      $banner = $model->record($this->getRequest('id', 0));
      
      if (!$banner) {
         return false;
      }
      
      $form = $this->createForm($banner);
      
      if($form->isValid()){
         $this->saveBanner($form, $banner);
         $this->infoMsg()->addMessage($this->tr('Banner byl uložen'));
         $this->link()->route()->reload();
      }
      
      
      $this->view()->form = $form;
      $this->view()->banner = $banner;
   }
   
   protected function createForm(Model_ORM_Record $banner = null) 
   {
      $form = new Form('banner_');
      
      $name = new Form_Element_Text('name', $this->tr('Název'));
      $name->addValidation(new Form_Validator_NotEmpty());
      $form->addElement($name);
      
      $url = new Form_Element_Text('url', $this->tr('URL'));
      $url->addValidation(new Form_Validator_NotEmpty());
      $url->addValidation(new Form_Validator_Url());
      $form->addElement($url);
      
      $file = new Form_Element_File('file', $this->tr('Soubor'));
      $file->addValidation(new Form_Validator_NotEmpty());
      $file->addValidation(new Form_Validator_FileExtension('jpg;png;gif;swf'));
      $file->setUploadDir((string)$this->module()->getDataDir());
      $form->addElement($file);
      
      $box = new Form_Element_Select('box', $this->tr('Umístění'));
      foreach (self::getBoxes() as $key => $info) {
         $box->setOptions(array($info['label'] => $key), true);
      }
      $form->addElement($box);
      
      // active
      $active = new Form_Element_Checkbox('active', $this->tr('Aktivní'));
      $active->setValues(true);
      $form->addElement($active);
      // new window
      $newWin = new Form_Element_Checkbox('newWin', $this->tr('Otevřít v novém okně'));
      $newWin->setSubLabel($this->tr('Nelze použít pro Flash animace'));
      $newWin->setValues(true);
      $form->addElement($newWin);
      
      // order - asi select a zařadit za, ale mohl by být jenom v hlavní metodě a ajax req přes move seznamu
      
      $save = new Form_Element_SaveCancel('save');
      $form->addElement($save);
      
      if($banner != null){
         $form->name->setValues($banner->{Banners_Model::COLUMN_NAME});
         $form->url->setValues($banner->{Banners_Model::COLUMN_URL});
         $form->box->setValues($banner->{Banners_Model::COLUMN_BOX});
         $form->active->setValues($banner->{Banners_Model::COLUMN_ACTIVE});
         $form->newWin->setValues($banner->{Banners_Model::COLUMN_NEW_WINDOW});
         $file->removeValidation('Form_Validator_NotEmpty');
         $file->setSubLabel( sprintf( $this->tr('Nahrán soubor %s'), $banner->{Banners_Model::COLUMN_FILE}) );
      }
      
      if($form->isSend() && $form->save->getValues() == false){
         $this->link()->route()->reload();
      }
      
      return $form;
   }
   
   protected function saveBanner(Form $form, Model_ORM_Record $banner = null) 
   {
      $model = new Banners_Model();
      if($banner == null){
         $banner = $model->newRecord();
         
         // dopočet maximální pozice
         $lastPos = $model->where(Banners_Model::COLUMN_BOX." = :box", 
                 array('box' => $form->box->getValues()))->count();
         $banner->{Banners_Model::COLUMN_ORDER} = $lastPos+1;
      }
      
      $banner->{Banners_Model::COLUMN_NAME} = $form->name->getValues();
      $banner->{Banners_Model::COLUMN_URL} = $form->url->getValues();
      
      $file = $form->file->getValues();
      if($file != null){
         $banner->{Banners_Model::COLUMN_FILE} = $file['name'];
      }
      
      $banner->{Banners_Model::COLUMN_BOX} = $form->box->getValues();
      $banner->{Banners_Model::COLUMN_ACTIVE} = $form->active->getValues();
      //       $banner->{Banners_Model::COLUMN_} = $form->->getValues();
      //       $banner->{Banners_Model::COLUMN_} = $form->->getValues();

      // recalculate orders ?
      
      $model->save($banner);
      
   }
   
   protected static function getBoxes() 
   {
      $positions = Template_Face::moduleParam('banners', 'positions', array()); 
      foreach ($positions as &$box) {
         $box = array_merge(array('random' => false, 'limit' => 0, 'banners' => array()), $box );
      }
      return $positions;
   }
   
   public static function clickController() {
      $model = new Banners_Model_Clicks();
      $modelBanner = new Banners_Model();
      $banner = $modelBanner->record((int)$_GET['bid']);
      
      $link = new Url_Link(true);
      
      if($banner == false){
         $link->clear(true)->reload();
      }
      
      $click = $model->newRecord();
      
      $click->{Banners_Model_Clicks::COLUMN_ID_BANNER} = $banner->{Banners_Model::COLUMN_ID};
      $click->{Banners_Model_Clicks::COLUMN_IP} = ip2long($_SERVER['REMOTE_ADDR']);
      $click->{Banners_Model_Clicks::COLUMN_BROWSER} = $_SERVER['HTTP_USER_AGENT'];
      $model->save($click);
      
      $link->reload($banner->{Banners_Model::COLUMN_URL},302);
//       exit;
   }
   
   public function clicksListController() {
      $this->checkControllRights();
      $idb = $this->getRequestParam('idb');
      $days = array();
      $date = new DateTime();
      $date->modify('-1 month');
      
      while ($date->format("d-m-Y") != date("d-m-Y")){
         $days[vve_date("%x", $date)] = 0;
         $date->modify('+1 day');
      }
      
      $model = new Banners_Model_Clicks();
      
      $clicks = $model
         ->where(Banners_Model_Clicks::COLUMN_ID_BANNER.' = :idb AND '.Banners_Model_Clicks::COLUMN_TIME.' >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)', 
                 array('idb' => $idb))
         ->records();
      
      foreach ($clicks as $c) {
         $date = new DateTime($c->{Banners_Model_Clicks::COLUMN_TIME});
         $days[vve_date("%x", $date)]++;
      }
      
      $this->view()->days = $days;
   }
   
   public function moveBannerController()
   {
      $this->checkControllRights();
      
      $idb = $this->getRequestParam('idb');
      $boxName = $this->getRequestParam('box');
      $newPos = (int)$this->getRequestParam('pos', 0) + 1;
      
      $this->view()->idb = $idb;
      $this->view()->name = $boxName;
      $this->view()->newpos = $newPos;
      
      $model = new Banners_Model();
      $model->lock();
      $banner = $model->record($idb);
      if(!$banner){
         $this->errMsg()->addMessage($this->tr('Banner se nepodařilo přesunout, protože neexistuje'));
         return;
      }
      $oldBoxName = $banner->{Banners_Model::COLUMN_BOX};
      $oldPos = $banner->{Banners_Model::COLUMN_ORDER};
      
      try {
         if($oldBoxName == $boxName){
            // přesun ve stejném boxu
            if($newPos > $banner->{Banners_Model::COLUMN_ORDER}){
               // přesun dolů
               $model->where(
                     Banners_Model::COLUMN_ORDER.' > :opos AND '.Banners_Model::COLUMN_ORDER." <= :npos AND ".Banners_Model::COLUMN_BOX." = :box",
                     array( 'npos' => $newPos, 'opos' => $oldPos, 'box' => $boxName )
                     )
               ->update(array(Banners_Model::COLUMN_ORDER => array( 'stmt' => Banners_Model::COLUMN_ORDER.'-1') ));
            
            } else if($newPos < $banner->{Banners_Model::COLUMN_ORDER}){
               $this->infoMsg()->addMessage('přesun nahoru');
               $model->where(
                     Banners_Model::COLUMN_ORDER.' >= :npos AND '.Banners_Model::COLUMN_ORDER." < :opos AND ".Banners_Model::COLUMN_BOX." = :box",
                     array( 'npos' => $newPos, 'opos' => $oldPos, 'box' => $boxName )
                     )
               ->update(array(Banners_Model::COLUMN_ORDER => array( 'stmt' => Banners_Model::COLUMN_ORDER.'+1') ));
            }
            $banner->{Banners_Model::COLUMN_ORDER} = $newPos;
            $model->save($banner);
         } else {
            // update všech pod novou pozicí
            // nový box - vytvořit místo pro banner
            $model->where( Banners_Model::COLUMN_ORDER.' >= :npos AND '.Banners_Model::COLUMN_BOX." = :box",
                  array( 'npos' => $newPos, 'box' => $boxName ))
                  ->update(array(Banners_Model::COLUMN_ORDER => array( 'stmt' => Banners_Model::COLUMN_ORDER.'+1') ));
         
            // přesun do jiného boxu a update pozice
            $banner->{Banners_Model::COLUMN_BOX} = $boxName;
            $banner->{Banners_Model::COLUMN_ORDER} = $newPos;
            $model->save($banner);
         
            // starý box - přesunutí všech pozic pod banerem nahoru
            $model->where( Banners_Model::COLUMN_ORDER.' >= :opos AND '.Banners_Model::COLUMN_BOX." = :box",
                  array( 'opos' => $oldPos, 'box' => $oldBoxName ))
                  ->update(array(Banners_Model::COLUMN_ORDER => array( 'stmt' => Banners_Model::COLUMN_ORDER.'-1') ));
         }
      } catch (Exception $e) {
         
      }
      $model->unLock();
   }
}
?>