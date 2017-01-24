<?php
class AdminCustomMenu_View extends View {
   public function mainView() {
      $this->template()->addTplFile('main.phtml');
   }

   public static function getMenu($nameOrId)
   {
      $model = new CustomMenu_Model_Items();
      $tpl = new Template_Module(new Url_Link_Module(), Category::getSelectedCategory());

      $name = $nameOrId;
      if(is_int($name)){
         $root = AdminCustomMenu_Model_Items::getRecord($name);
         $name = $root->{AdminCustomMenu_Model_Items::COLUMN_BOX};
      }
      
      $tpl->box = $name;

      // parametry boxu
      $boxes = Face::getParamStatic('positions', 'custommenu', array());
      if(!isset($boxes[$name])){
         return null;
      }

      $model
         ->joinFK(AdminCustomMenu_Model_Items::COLUMN_ID_CATEGORY)
         ->where(AdminCustomMenu_Model_Items::COLUMN_BOX." = :box AND ".AdminCustomMenu_Model_Items::COLUMN_ACTIVE." = 1",
            array('box' => $name))
         ->order(CustomMenu_Model_Items::COLUMN_ORDER);
      if(!Auth::isAdmin()){
         $model->where(" AND ( ".Model_Category::COLUMN_DISABLE.'_'.Locales::getLang(). " = 0 OR ".AdminCustomMenu_Model_Items::COLUMN_ID_CATEGORY." = 0 )", array(), true);
      }
      
      $items = $model->records();
    
      if(empty($items)){
         return false;
      }
      $link = new Url_Link(true);
      foreach ($items as $i) {
         if($i->{AdminCustomMenu_Model_Items::COLUMN_LINK} == null){
            $i->{AdminCustomMenu_Model_Items::COLUMN_LINK} = (string)$link->category($i->{Model_Category::COLUMN_URLKEY});
         }
      }

      $tpl->items = $items;

      if($tpl->existTpl('menu-'.$name.'.phtml', 'custommenu')){
         $tpl->addFile('tpl://custommenu:menu-'.$name.'.phtml');
      } else {
         $tpl->addFile('tpl://custommenu:menu.phtml');
      }
      return $tpl;
   }

   public function editMenuView()
   {
      $this->template()->addFile('tpl://editform.phtml');
   }
   
   public function editMenuItemView()
   {
      $this->template()->addFile('tpl://editform.phtml');
   }
}

