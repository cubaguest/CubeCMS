<?php

class AdminCustomMenu_Controller extends Controller {

   private $catsArray = array();

   protected function init()
   {
      //		Kontrola práv
      $this->checkControllRights();
      parent::init();
   }

   public function mainController()
   {
      /**
       * @todo - asi vytvořit nějaký algoritmus, který bude menu vytvářet podla face pokud neexitují
       * To samé patří přidat 
       */
      $menusFace = Face::getCurrent()->getParam('positions', 'custommenu');
      $boxesFace = array();
      // vytvoření pole pro boxy
      foreach ($menusFace as $key => $name) {
         $boxesFace[$key] = array('name' => $name, 'items' => array());
      }

      $formItem = $this->createFormMenuItem();
      $this->processEditMenuItemForm($formItem);
      $this->view()->form = $formItem;
      
      $formMenu = $this->createFormMenu();
      $this->processEditMenuForm($formMenu);
      $this->view()->formMenu = $formMenu;

      // zařazení položek
      // načtení položek z jednotlivých menu
      if(!Url_Request::isXHRRequest()){
         $model = new AdminCustomMenu_Model_Items();
         $structureRoots = $model->getRoots();
         $this->view()->structure = $structureRoots;
      }

// testy
      $ft = new Form('ftest');

      $inp = new Form_Element_Text('id', 'ID');
      $ft->addElement($inp);

      $send = new Form_Element_Submit('send', 'proved');
      $ft->addElement($send);

      if($ft->isValid()){
         $rec = AdminCustomMenu_Model_Items::getRecord($ft->id->getValues());

         $p = $rec->getParent();

         Debug::log($p->{AdminCustomMenu_Model_Items::COLUMN_NAME}, $rec->getPK(), $p->getPK());
      }

      $this->view()->formTest = $ft;
   }
   
   protected function processEditMenuForm(Form $form)
   {
      if($form->isValid()){
         $item = AdminCustomMenu_Model_Items::getNewRecord();
         $item->{AdminCustomMenu_Model_Items::COLUMN_NAME} = $form->name->getValues();
         $item->save();
         /* @var $item AdminCustomMenu_Model_Items_Record */
         $item->setAsRoot();
         $this->infoMsg()->addMessage($this->tr('Menu bylo vytvořeno'));
         $this->link()->redirect();
      }
   }
   
   protected function processEditMenuItemForm(Form $form)
   {
      if($form->isValid()){
         $root = AdminCustomMenu_Model_Items::getRecord($form->root->getValues());
         /* @var $root AdminCustomMenu_Model_Items_Record */
         
         /* @var $item AdminCustomMenu_Model_Items_Record */
         $item = AdminCustomMenu_Model_Items::getNewRecord();
         $item->{AdminCustomMenu_Model_Items::COLUMN_NAME} = $form->name->getValues();
         $item->{AdminCustomMenu_Model_Items::COLUMN_ACTIVE} = $form->active->getValues();
         $item->{AdminCustomMenu_Model_Items::COLUMN_BOX} = $root->{AdminCustomMenu_Model_Items::COLUMN_BOX};
         $item->{AdminCustomMenu_Model_Items::COLUMN_ID_CATEGORY}= $form->cat->getValues();
         $item->{AdminCustomMenu_Model_Items::COLUMN_LINK} = $form->link->getValues();
         $item->{AdminCustomMenu_Model_Items::COLUMN_NEW_WINDOW} = $form->newWin->getValues();
         $root->addNode($item); // save se provádí i přidáním
         
         $this->infoMsg()->addMessage($this->tr('Položka menu byla vytvořena'));
         $this->link()->redirect();
      }
   }

   public function editController()
   {
      $action = $this->getRequestParam('action', false);
      $id = $this->getRequestParam('id');
      if (!$action) {
         throw new UnexpectedValueException('Nebyla předána akce');
      }
      if ($action == 'edit') {
         $this->createFormMenuItem();
      } else if ($action == "delete" && $id != null) {
         $model = new AdminCustomMenu_Model_Items();
         $model->delete($id);
         $this->infoMsg()->addMessage($this->tr('Položka byla smazána'));
         $this->link()->route()->rmParam()->file()->redirect();
      } else if ($action == "changeState" && $id != null) {
//         AdminCustomMenu_Model_Items::changeState($id);
//         $this->infoMsg()->addMessage($this->tr('Položka byla smazána'));
//         $this->link()->route()->rmParam()->file()->redirect();
      }
   }
   
   public function editMenuController($id)
   {
      $item = AdminCustomMenu_Model_Items::getRecord($id);
      $form = $this->createFormMenu($item);
      if($form->isValid()){
         $item->{AdminCustomMenu_Model_Items::COLUMN_NAME} = $form->name->getValues();
         $item->save();
         $this->infoMsg()->addMessage($this->tr('Položky byla uložena'));
         $this->link()->redirect();
      }
      
      $this->view()->form = $form;
   }
   
   public function editMenuItemController($id)
   {
      $item = AdminCustomMenu_Model_Items::getRecord($id);
      $form = $this->createFormMenuItem($item);
      
      if($form->isValid()){
         $item->{AdminCustomMenu_Model_Items::COLUMN_NAME} = $form->name->getValues();
         $item->{AdminCustomMenu_Model_Items::COLUMN_LINK} = $form->link->getValues();
         $item->{AdminCustomMenu_Model_Items::COLUMN_ID_CATEGORY} = $form->cat->getValues();
//         $item->{AdminCustomMenu_Model_Items::COLUMN_NAME} = $form->root->getValues();
         $item->{AdminCustomMenu_Model_Items::COLUMN_ACTIVE} = $form->active->getValues();
         $item->{AdminCustomMenu_Model_Items::COLUMN_NEW_WINDOW} = $form->newWin->getValues();
         $item->save();
         $this->infoMsg()->addMessage($this->tr('Položky byla uložena'));
         $this->link()->redirect();
      }
      
      $this->view()->form = $form;
   }

   protected function createFormMenuItem(Model_ORM_Tree_Record $item = null)
   {
      $f = new Form('edit_menu_item_');

      $eId = new Form_Element_Hidden('id');
      $f->addElement($eId);

      $eName = new Form_Element_Text('name', $this->tr('Název'));
      $eName->setLangs();
      $eName->addValidation(New Form_Validator_NotEmpty(null, Locales::getDefaultLang(true)));
      $f->addElement($eName);

      $eLink = new Form_Element_Text('link', $this->tr('Odkaz'));
//      $eLink->addValidation(new Form_Validator_Url());
      $eLink->setSubLabel($this->tr('Místo odkazu můžete vybrat kategorii stránek níže'));
      $f->addElement($eLink);

      $eCats = new Form_Element_Select('cat', $this->tr('Kategorie stránek'));
      $eCats->setOptions(array($this->tr('Žádná') => 0));
      $this->loadCats();
      foreach ($this->catsArray as $id => $name) {
         $eCats->setOptions(array($name => $id), true);
      }
      $f->addElement($eCats);

      $eRoot = new Form_Element_Select('root', $this->tr('Umístění'));
      $model = new AdminCustomMenu_Model_Items();
      $structureRoots = $model->getRoots();
      foreach ($structureRoots as $node) {
         $eRoot->addOption($node->{AdminCustomMenu_Model_Items::COLUMN_NAME}, $node->getPK());
      }
      $f->addElement($eRoot);

      $eAct = new Form_Element_Checkbox('active', $this->tr('Aktivní'));
      $eAct->setValues(true);
      $f->addElement($eAct);

      $eNewWin = new Form_Element_Checkbox('newWin', $this->tr('Nové okno'));
      $eNewWin->setValues(false);
      $f->addElement($eNewWin);

      $eSave = new Form_Element_Submit('save', $this->tr('Uložit'));
      $f->addElement($eSave);

      if($item != null){
         $f->id->setValues($item->getPK());
         $f->name->setValues($item->{AdminCustomMenu_Model_Items::COLUMN_NAME});
         $f->link->setValues($item->{AdminCustomMenu_Model_Items::COLUMN_LINK});
         $f->cat->setValues($item->{AdminCustomMenu_Model_Items::COLUMN_ID_CATEGORY});
//         $f->root->setValues($item->{AdminCustomMenu_Model_Items::COLUMN_NAME});
         $f->active->setValues($item->{AdminCustomMenu_Model_Items::COLUMN_ACTIVE});
         $f->newWin->setValues($item->{AdminCustomMenu_Model_Items::COLUMN_NEW_WINDOW});
      }

      return $f;
   }

   protected function createFormMenu(Model_ORM_Tree_Record $menu = null)
   {
      $f = new Form('edit_menu_');

      $eId = new Form_Element_Hidden('id');
      $f->addElement($eId);

      $eName = new Form_Element_Text('name', $this->tr('Název'));
      $eName->setLangs();
      $eName->addValidation(New Form_Validator_NotEmpty(null, Locales::getDefaultLang(true)));
      $f->addElement($eName);

      $eSave = new Form_Element_Submit('save', $this->tr('Uložit'));
      $f->addElement($eSave);

      if($menu != null){
         $f->id->setValues($menu->getPK());
         $f->name->setValues($menu->{AdminCustomMenu_Model_Items::COLUMN_NAME});
      }
      return $f;
   }

   public function moveItemController()
   {
      $this->checkWritebleRights();

      $idParent = (int) $this->getRequestParam('parent');
      $idParentOld = (int) $this->getRequestParam('parentold');
      $id = (int) $this->getRequestParam('id');
      $index = (int) $this->getRequestParam('position');
      $indexOld = (int) $this->getRequestParam('positionold');

      // oprava pozice pokud se posunuje dolů
      if($idParent == $idParentOld && $index > $indexOld){
         $index++;
      }
      
      $model = new AdminCustomMenu_Model_Items();

      $item = $model->record($id);
      $parent = $model->record($idParent);

      $model->moveNode($item, $parent, $index);
   }

   public function getTreeController()
   {
      $this->checkWritebleRights();

      $model = new AdminCustomMenu_Model_Items();

      $roots = $model
              ->joinFK(AdminCustomMenu_Model_Items::COLUMN_ID_CATEGORY)
              ->getRoots();
      $treeArray = array();
      foreach ($roots as $node) {
         $treeArray[] = array_merge(
                 $this->createTreeItem($node, 'root'), array('children' => $this->createTreeArray($node))
         );
      }
      Template_Output::setOutputType('json');
      Template_Output::sendHeaders();
      echo json_encode($treeArray);
      die;
   }

   protected function createTreeArray(Model_ORM_Tree_Record $tree)
   {
      $childs = $tree->getNodes();
      $treeData = array();
      if(!$tree->isEmpty()){
         foreach ($childs as $node) {
            $type = $node->{AdminCustomMenu_Model_Items::COLUMN_LINK} != null ? 'globe' : '';
      //         $type = $node->isEmpty() ? $type : 'folder';
            $treeData[] = array_merge(
                    $this->createTreeItem($node, $type), array('children' => $this->createTreeArray($node))
            );
         }
      }
      return $treeData;
   }

   protected function createTreeItem($node, $type = null)
   {
      $link = $node->{AdminCustomMenu_Model_Items::COLUMN_LINK};
      if($node->{AdminCustomMenu_Model_Items::COLUMN_ID_CATEGORY} != null){
         
      }
      return array(
          'id' => 'menu-item-' . $node->getPK(),
          'itemid' => $node->getPK(),
          'text' => (string) $node->{AdminCustomMenu_Model_Items::COLUMN_NAME}. ' <em>(ID: '.$node->getPK().')</em>',
          'children' => array(),
//             'li_attr' => array(),
             'a_attr' => array(
                 'class' => $node->{AdminCustomMenu_Model_Items::COLUMN_ACTIVE} ? 'active' : 'inactive'
             ),
          'type' => $type,
          'data' => array(
              'type' => $type,
              'itemid' => $node->getPK(),
              'name' => $node->{AdminCustomMenu_Model_Items::COLUMN_NAME}->toArray(),
              'link' => $link,
              'newWin' => (bool)$node->{AdminCustomMenu_Model_Items::COLUMN_NEW_WINDOW},
              'state' => $node->{AdminCustomMenu_Model_Items::COLUMN_ACTIVE},
              'idcat' => $node->{AdminCustomMenu_Model_Items::COLUMN_ID_CATEGORY},
              'catname' => (string)$node->{Model_Category::COLUMN_NAME},
              'box' => (string)$node->{AdminCustomMenu_Model_Items::COLUMN_BOX},
          ),
      );
   }

   protected function loadCats()
   {
      $struct = Category_Structure::getStructure(Category_Structure::ALL);
      $this->fillInCats($struct);
   }

   private function fillInCats($struct, $level = 0)
   {
      foreach ($struct as $i) {
         $this->catsArray[$i->getCatObj()->getId()] = str_repeat('.', $level * 3) . $i->getCatObj()->getName();
         if (!empty($i)) {
            $this->fillInCats($i, $level + 1);
         }
      }
   }

}
