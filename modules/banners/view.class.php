<?php
class Banners_View extends View {
   public function mainView() 
   {
      Template_Module::setEdit(true);
      $this->template()->addTplFile('main.phtml');
      
      $toolbox = new Template_Toolbox2();

      $toolChangeStatus = new Template_Toolbox2_Tool_Form($this->formChangeStatus);
      $toolChangeStatus->setIcon('enable.png');
      $toolbox->addTool($toolChangeStatus);
      
      $toolAddEv = new Template_Toolbox2_Tool_Redirect('previewBanner', $this->tr('Náhled baneru'));
      $toolAddEv->setIcon('image.png');
      $toolbox->addTool($toolAddEv);
      
      $toolHome = new Template_Toolbox2_Tool_Redirect('editBanner', $this->tr('Upravit banner'));
      $toolHome->setIcon('image_edit.png')->setAction($this->link()->route("edit"));
      $toolbox->addTool($toolHome);

      $toolRemove = new Template_Toolbox2_Tool_Form($this->formDelete);
      $toolRemove->setIcon('image_delete.png')->setConfirmMeassage($this->tr('Opravdu smazat banner?'));
      $toolbox->addTool($toolRemove);

      $this->toolboxItem = $toolbox;
   }

   public function addView() 
   {
      Template_Module::setEdit(true);
      $this->template()->addTplFile('edit.phtml');
      Template_Navigation::addItem($this->tr('Přidání banneru'), $this->link(), null, null, null, true);
   }
   
   public function editView() 
   {
      $this->edit = true;
      Template_Module::setEdit(true);
      $this->template()->addTplFile('edit.phtml');
      Template_Navigation::addItem($this->tr('Úprava banneru'), $this->link(), null, null, null, true);
   }
   
   public static function getBanners($boxName)
   {
      $model = new Banners_Model();
      $link = new Url_Link_ModuleStatic();
      $tpl = new Template_Module(new Url_Link_Module(), Category::getSelectedCategory());
      
      $tpl->linkClick = $link->module('banners')->action('click', 'html');
      $tpl->dir = Url_Request::getBaseWebDir()."data/".Banners_Controller::DATA_DIR."/";
      $tpl->boxName = $boxName;

      // parametry boxu
      $boxes = Face::getParamStatic('positions', 'banners', array());
      if(!isset($boxes[$boxName])){
         return null;
      }
      
      $model->where(Banners_Model::COLUMN_BOX." = :box AND ".Banners_Model::COLUMN_ACTIVE." = 1", 
              array('box' => $boxName));
      
      if(isset($boxes[$boxName]['random']) && $boxes[$boxName]['random'] == true){
         // náhodně poskládat
         //shuffle($banners);
         $model->order(array('RAND()' => Model_ORM::ORDER_ASC));
      }
      
      if(isset($boxes[$boxName]['limit'])){
         $model->limit(0, (int)$boxes[$boxName]['limit']);
      }
      
      $banners = $model->records();
      $tpl->banners = $banners;
      
      $tplPath = Template::faceDir().'modules'.DIRECTORY_SEPARATOR.'banners'.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR;
      // kontrola jestli exituje šablona pro daný box, jinak se použije výchozí
      if(isset($boxes[$boxName]['tpl']) && is_file($tplPath.$boxes[$boxName]['tpl'])){
         $tpl->addFile('tpl://banners:'.$boxes[$boxName]['tpl']);
      } else if(is_file($tplPath.$boxName.'.phtml')){
         $tpl->addFile('tpl://banners:'.$boxName.'.phtml');
      } else {
         $tpl->addFile('tpl://banners:banners.phtml');
      }
      return $tpl;
   }
}
?>