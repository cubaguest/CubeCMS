<?php
class CustomMenu_View extends View {
   public function mainView() {
      $this->template()->addTplFile('main.phtml');
   }

   public static function getMenu($name)
   {
      $model = new CustomMenu_Model_Items();
      $tpl = new Template_Module(new Url_Link_Module(), Category::getSelectedCategory());

      $tpl->box = $name;

      // parametry boxu
      $boxes = Face::getParamStatic('positions', 'custommenu', array());
      if(!isset($boxes[$name])){
         return null;
      }

      $model
         ->joinFK(CustomMenu_Model_Items::COLUMN_ID_CATEGORY)
         ->where(CustomMenu_Model_Items::COLUMN_BOX." = :box AND ".CustomMenu_Model_Items::COLUMN_ACTIVE." = 1",
            array('box' => $name))
         ->order(CustomMenu_Model_Items::COLUMN_ORDER);
      if(!Auth::isAdmin()){
         $model->where(" AND ".Model_Category::COLUMN_DISABLE.'_'.Locales::getLang(). " = 0", array(), true);
      }
      
      $items = $model->records();
    
      if(empty($items)){
         return false;
      }
      $link = new Url_Link(true);
      foreach ($items as $i) {
         if($i->{CustomMenu_Model_Items::COLUMN_LINK} == null){
            $i->{CustomMenu_Model_Items::COLUMN_LINK} = (string)$link->category($i->{Model_Category::COLUMN_URLKEY});
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

}

