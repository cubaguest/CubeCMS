<?php
class CustomMenu_Controller extends Controller {
   private $catsArray = array();


   protected function init()
   {
      //		Kontrola práv
      $this->checkControllRights();
      parent::init();
   }

   public function mainController() {
      $menus = $this->category()->getParam('positions', null);
      if(empty($menus)){
         return;
      }

      $this->view()->form = $this->createFormMenuItem();

      $boxes = array();
      // vytvoření pole pro boxy
      foreach($menus as $key => $name){
         $boxes[$key] = array('name' => $name, 'items' => array());
      }

      // zařazení položek
      // načtení položek z jednotlivých menu
      $model = new CustomMenu_Model_Items();
      $records = $model
         ->joinFK(CustomMenu_Model_Items::COLUMN_ID_CATEGORY)
         ->order(array(CustomMenu_Model_Items::COLUMN_BOX => Model_ORM::ORDER_ASC, CustomMenu_Model_Items::COLUMN_ORDER => Model_ORM::ORDER_ASC))
         ->records();
      $linkCat = new Url_Link(true);
      foreach($records as $item){
         // createCatLink
         $item->catLink = (string)$linkCat->category($item->{Model_Category::COLUMN_URLKEY});
         $boxes[$item->{CustomMenu_Model_Items::COLUMN_BOX}]['items'][] = $item;
      }

      $this->view()->boxes = $boxes;
   }

   public function editController() {
      $action = $this->getRequestParam('action', false);
      $id = $this->getRequestParam('id');
      if(!$action){
         throw new UnexpectedValueException('Nebyla předána akce');
      }
      if($action == 'edit'){
         $this->createFormMenuItem();
      } else if($action == "delete" && $id != null){
         $model = new CustomMenu_Model_Items();
         $model->delete($id);
         $this->infoMsg()->addMessage($this->tr('Položka byla smazána'));
         $this->link()->route()->rmParam()->redirect();

      } else if($action == "changeState" && $id != null){
         CustomMenu_Model_Items::changeState($id);
         $this->infoMsg()->addMessage($this->tr('Položka byla smazána'));
         $this->link()->route()->rmParam()->redirect();

      } else if($action == "changepos" && $id != null
         && ($pos = $this->getRequestParam('pos')) != null){
         CustomMenu_Model_Items::changeOrder($id, $pos);
         $this->infoMsg()->addMessage($this->tr('Položka byla přesunuta'));
         $this->link()->route()->rmParam()->redirect();
      }

   }

   protected function createFormMenuItem($item = null)
   {
      $f = new Form('edit_menu_item_');

      $eId = new Form_Element_Hidden('id');
      $f->addElement($eId);

      $eName = new Form_Element_Text('name', $this->tr('Název'));
      $eName->setLangs();
      $eName->addValidation(New Form_Validator_NotEmpty(null, Locales::getDefaultLang(true)));
      $f->addElement($eName);

      $eLink = new Form_Element_Text('link', $this->tr('Odkaz'));
      $eLink->addValidation(new Form_Validator_Url());
      $eLink->setSubLabel($this->tr('Místo odkazu můžete vybrat kategorii stránek níže'));
      $f->addElement($eLink);

      $eCats = new Form_Element_Select('cat', $this->tr('Kategorie stránek'));
      $eCats->setOptions(array($this->tr('Žádná') => 0));
      $this->loadCats();
      foreach ($this->catsArray as $id => $name) {
         $eCats->setOptions(array($name => $id), true);
      }
      $f->addElement($eCats);

      $eBox = new Form_Element_Select('box', $this->tr('Umístění'));
      $boxes = $this->category()->getParam('positions', null);
      foreach($boxes as $key => $name){
         $eBox->addOption($name, $key);
      }
      $f->addElement($eBox);

      $eAct = new Form_Element_Checkbox('active', $this->tr('Aktivní'));
      $eAct->setValues(true);
      $f->addElement($eAct);

      $eNewWin = new Form_Element_Checkbox('newWin', $this->tr('Nové okno'));
      $eNewWin->setValues(false);
      $f->addElement($eNewWin);

      $eSave = new Form_Element_Submit('save', $this->tr('Uložit'));
      $f->addElement($eSave);

      if($f->isSend()){
         if($f->link->getValues() == null && $f->cat->getValues() == 0){
            $eCats->setError($this->tr('Nebyl zadán odkaz položky. Musíte zadat buď odkaz nebo kategorii'));
         }
      }

      if($f->isValid()){
         $model = new CustomMenu_Model_Items();
         if($f->id->getValues() != null){
            $item = $model->record($f->id->getValues());
         } else {
            $item = $model->newRecord();
         }

         $item->{CustomMenu_Model_Items::COLUMN_NAME} = $f->name->getValues();
         $item->{CustomMenu_Model_Items::COLUMN_LINK} = $f->link->getValues();
         $item->{CustomMenu_Model_Items::COLUMN_BOX} = $f->box->getValues();
         $item->{CustomMenu_Model_Items::COLUMN_ID_CATEGORY} = $f->cat->getValues();
         $item->{CustomMenu_Model_Items::COLUMN_ACTIVE} = $f->active->getValues();
         $item->{CustomMenu_Model_Items::COLUMN_NEW_WINDOW} = $f->newWin->getValues();
         $item->save();

         $this->infoMsg()->addMessage($this->tr('Položka byla uložena'));
         $this->link()->redirect();
      }

      return $f;
   }

   protected function loadCats()
   {
      $struct = Category_Structure::getStructure(Category_Structure::ALL);
      $this->fillInCats($struct);
   }

   private function fillInCats($struct, $level = 0)
   {
      foreach($struct as $i){
         $this->catsArray[$i->getCatObj()->getId()] = str_repeat('.', $level*3).$i->getCatObj()->getName();
         if(!empty($i)){
            $this->fillInCats($i, $level+1);
         }
      }
   }

}
