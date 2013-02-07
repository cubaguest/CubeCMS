<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 */

class ShopProductVariants_Controller extends Controller {
   
   protected function init()
   {
      $this->checkControllRights();
      $this->category()->getModule()->setDataDir('shop');
   }
   
   public function mainController() 
   {
      //		Kontrola práv
      $modelGroups = new Shop_Model_AttributesGroups();
      $modelAttributes = new Shop_Model_Attributes();


      // form přidání skupiny
      $this->view()->formEditGroup = $this->formEditGroup();
//      $formAddGroup = new Form("attrgroup_add_");
//      $elemGroupId = new Form_Element_Hidden('id');
//      $formAddGroup->addElement($elemGroupId);
//
//      $elemGroupName = new Form_Element_Text('name', $this->tr('Název skupiny'));
//      $elemGroupName->setLangs();
//      $elemGroupName->addValidation(new Form_Validator_NotEmpty(null, Locales::getDefaultLang()));
//      $elemGroupName->addFilter(new Form_Filter_StripTags());
//      $formAddGroup->addElement($elemGroupName);
//
//      $elemGroupSave = new Form_Element_Submit('save', $this->tr('Uložit'));
//      $formAddGroup->addElement($elemGroupSave);
//
//      if($this->getRequestParam('editg') != null
//         && ($group = $modelGroups->record($this->getRequestParam('editg'))) ){
//         $formAddGroup->id->setValues($group->getPK());
//         $formAddGroup->name->setValues($group->{Shop_Model_AttributesGroups::COLUMN_NAME});
//      }

//      if($formAddGroup->isValid()){
//         if($formAddGroup->id->getValues() == null){
//            // je nová skupina
//            $group = $modelGroups->newRecord();
//         } else {
//            // úprava skupiny
//            $group = $modelGroups->record($formAddGroup->id->getValues());
//         }
//         $group->{Shop_Model_AttributesGroups::COLUMN_NAME} = $formAddGroup->name->getValues();
//         $group->save();
//         $this->infoMsg()->addMessage($this->tr('Skupiny byla uložena'));
//         $this->link()->rmParam('editg')->reload();
//      }
//      $this->view()->formAddGroup = $formAddGroup;

      // form přidání atributu
      $this->view()->formEditVariant = $this->formEditVariant();
//      $formAddAttr = new Form("attr_add_");
//      $elemAttrId = new Form_Element_Hidden('id');
//      $formAddAttr->addElement($elemAttrId);
//
//      $elemAttrIdGroup = new Form_Element_Hidden('idg');
//      $elemAttrIdGroup->setValues($this->getRequestParam('g'));
//      $formAddAttr->addElement($elemAttrIdGroup);
//
//      $elemAttrName = new Form_Element_Text('name', $this->tr('Hodnota'));
//      $elemAttrName->setLangs();
//      $elemAttrName->addValidation(new Form_Validator_NotEmpty(null, Locales::getDefaultLang()));
//      $elemAttrName->addFilter(new Form_Filter_StripTags());
//      $formAddAttr->addElement($elemAttrName);
//
//      $elemAttrSave = new Form_Element_Submit('save', $this->tr('Uložit'));
//      $formAddAttr->addElement($elemAttrSave);

//      if($this->getRequestParam('editv') != null
//         && ($attr = $modelAttributes->record((int)$this->getRequestParam('editv'))) ){
//         $formAddAttr->id->setValues($attr->getPK());
//         $formAddAttr->name->setValues($attr->{Shop_Model_Attributes::COLUMN_NAME});
//      }

//      // mazání
//      if($this->getRequestParam('deletev') != null){
//         $modelAttributes->delete((int)$this->getRequestParam('deletev', 0));
//         $this->infoMsg()->addMessage($this->tr('Hodnota byla smazána'));
//         $this->link()->param('deletev')->reload();
//      }
//
//      if($this->getRequestParam('deleteg') != null){
//         $modelGroups->delete((int)$this->getRequestParam('deleteg', 0));
//         $this->infoMsg()->addMessage($this->tr('Skupiny se všemi hodnotami a všechny varianty zboží byly smazány'));
//         $this->link()->param('deleteg')->reload();
//      }
   }

   /**
    * Upravuje parametr
    * Parametr: action - (edit;delete;move)
    */
   public function editGroupController()
   {
      // mazání
      if($this->getRequestParam('action', false) == 'delete'
         && $this->getRequestParam('id', false) != null){
         (new Shop_Model_AttributesGroups())->delete($this->getRequestParam('id'));
         $this->infoMsg()->addMessage($this->tr('Skupina byla smazána'));
         $this->link()->rmParam('action')->rmParam('id')->redirect();
      }
      // přesun
      else if($this->getRequestParam('action', false) == 'changepos'
         && $this->getRequestParam('id', false) != null
         && $this->getRequestParam('pos', false) != null
      ){
         Shop_Model_AttributesGroups::changeOrder(
            $this->getRequestParam('id'),
            $this->getRequestParam('pos')
         );

         $this->infoMsg()->addMessage($this->tr('Skupina byla přesunuta na novou pozici'));
         $this->link()
            ->rmParam('action')
            ->rmParam('id')
            ->rmParam('pos')
            ->redirect();
      }

      // úprava a přidání
      $this->formEditGroup();
   }

   /**
    * Upravuje hodnotu parametru
    * Parametr: action - (edit;delete;move)
    */
   public function editVariantController()
   {
      // mazání
      if($this->getRequestParam('action', false) == 'delete'
         && $this->getRequestParam('id', false) != null){
         (new Shop_Model_Attributes())->delete($this->getRequestParam('id'));
         $this->infoMsg()->addMessage($this->tr('Vyrianta byla smazána'));
         $this->link()->rmParam('action')->rmParam('id')->redirect();
      }
      // přesun
      else if($this->getRequestParam('action', false) == 'changepos'
         && $this->getRequestParam('id', false) != null
         && $this->getRequestParam('pos', false) != null
      ){
         Shop_Model_Attributes::changeOrder(
            $this->getRequestParam('id'),
            $this->getRequestParam('pos')
         );

         $this->infoMsg()->addMessage($this->tr('Varianta byla přesunuta na novou pozici'));
         $this->link()
            ->rmParam('action')
            ->rmParam('id')
            ->rmParam('pos')
            ->redirect();
      }
      // úprava a přidání
      $this->formEditVariant();
   }

   /**
    * Varcí seznam skupin
    */
   public function groupsListController()
   {
      $this->view()->groups = (new Shop_Model_AttributesGroups())
         ->order(Shop_Model_AttributesGroups::COLUMN_ORDER)
         ->records();
   }

   /**
    * Vrací seznam hodnot dané skupiny
    */
   public function variantsListController()
   {
      $idg = $this->getRequest('idg');

      $this->view()->variants = (new Shop_Model_Attributes())
         ->where(Shop_Model_Attributes::COLUMN_ID_GROUP." = :id",
         array('id' => $idg))
         ->order(Shop_Model_Attributes::COLUMN_ORDER)
         ->records();
   }

   /**
    * @param null $param
    * @return Form
    */
   protected function formEditGroup($group = null)
   {
      $form = new Form('group_edit_');

      $elemName = new Form_Element_Text('name', $this->tr('Název'));
      $elemName->addValidation(new Form_Validator_NotEmpty());
      $elemName->addFilter(new Form_Filter_HTMLSpecialChars());
      $form->addElement($elemName);

      $elemIdParam = new Form_Element_Hidden('id');
      $form->addElement($elemIdParam);

      $elemSave = new Form_Element_Submit('save', $this->tr('Uložit'));
      $form->addElement($elemSave);

      if($form->isValid()){
         $model = new Shop_Model_AttributesGroups();
         if($form->id->getValues() == null || !($group = $model->record($form->id->getValues()))){
            $group = $model->newRecord();
         }

         $group->{Shop_Model_AttributesGroups::COLUMN_NAME} = $form->name->getValues();
         $group->save();

         $this->infoMsg()->addMessage($this->tr('Skupiny byla uložena'));
         $this->link()->redirect();
      }
      return $form;
   }

   /**
    * @param null $value
    * @return Form
    */
   protected function formEditVariant($attr = null)
   {
      $form = new Form('variant_edit_');

      $elemName = new Form_Element_Text('name', $this->tr('Název'));
      $elemName->addValidation(new Form_Validator_NotEmpty());
      $elemName->addFilter(new Form_Filter_HTMLSpecialChars());
      $form->addElement($elemName);

      $elemIdGroup = new Form_Element_Hidden('idGroup');
      $form->addElement($elemIdGroup);

      $elemIdValue = new Form_Element_Hidden('id');
      $form->addElement($elemIdValue);

      $elemSave = new Form_Element_Submit('save', $this->tr('Uložit'));
      $form->addElement($elemSave);

      if($form->isValid()){
         $model = new Shop_Model_Attributes();

         $attr = $model->newRecord();

         if($form->id->getValues() != null){
            $attr = $model->record($form->id->getValues());
         }
         $attr->{Shop_Model_Attributes::COLUMN_ID_GROUP} = $form->idGroup->getValues();
         $attr->{Shop_Model_Attributes::COLUMN_NAME} = $form->name->getValues();

         $attr->save();

         $this->infoMsg()->addMessage($this->tr('Atribut byl uložen'));
         $this->link()->redirect();
      }

      return $form;
   }

   /*public function attrGroupsListController()
   {
      $this->checkReadableRights();
      // objekt komponenty JGrid
      $jqGrid = new Component_JqGrid();
      $jqGrid->request()->setDefaultOrderField(Shop_Model_AttrGroups::COLUMN_NAME);
      // search
      
      $model = new Shop_Model_AttrGroups();

      $order = Model_ORM::ORDER_ASC;
      if($jqGrid->request()->order == 'desc'){
         $order = Model_ORM::ORDER_DESC;
      }
      
      switch ($jqGrid->request()->orderField) {
         case 'id':
            $model->order(array(Shop_Model_AttrGroups::COLUMN_ID => $order));
            break;
         case 'name':
         default:
            $model->order(array(Shop_Model_AttrGroups::COLUMN_NAME => $order));
            break;
      }
      
      if ($jqGrid->request()->isSearch()) {
//         $count = $modelAddresBook->searchCount($jqGrid->request()->searchString(),
//            (int)$this->getRequestParam('idgrp', Mails_Model_Groups::GROUP_ID_ALL),
//            $jqGrid->request()->searchField(),$jqGrid->request()->searchType());
//         $jqGrid->respond()->setRecords($count);
//
//         $book = $modelAddresBook->search($jqGrid->request()->searchString(),
//            (int)$this->getRequestParam('idgrp', Mails_Model_Groups::GROUP_ID_ALL),
//            $jqGrid->request()->searchField(),$jqGrid->request()->searchType(),
//            ($jqGrid->request()->page - 1) * $jqGrid->respond()->getRecordsOnPage(),
//            $jqGrid->request()->rows, $jqGrid->request()->orderField, $jqGrid->request()->order);
      } else {
      // list
//         $groups = $this->getAllowedGroups($jqGrid);
      }
      
      $jqGrid->respond()->sql = $model->getSQLQuery();
      
      $jqGrid->respond()->setRecords($model->count());
      
      $fromRow = ($jqGrid->request()->page - 1) * $jqGrid->respond()->getRecordsOnPage();
      
      $groups = $model->limit($fromRow, $jqGrid->request()->rows)
         ->records(PDO::FETCH_OBJ);
      // out
      foreach ($groups as $grp) {
         array_push($jqGrid->respond()->rows, 
            array('id' => $grp->{Shop_Model_AttrGroups::COLUMN_ID},
                  Shop_Model_AttrGroups::COLUMN_NAME => $grp->{Shop_Model_AttrGroups::COLUMN_NAME},
                  Shop_Model_AttrGroups::COLUMN_ID => $grp->{Shop_Model_AttrGroups::COLUMN_ID}
            ));
      }
      $this->view()->respond = $jqGrid->respond();
   }  */

   /*public function editAttrGroupController()
   {
      $this->checkWritebleRights();
      $model = new Shop_Model_AttrGroups();
      $this->view()->allOk = false;
      
      // část komponenty jqgrid pro formy
      $jqGridReq = new Component_JqGrid_FormRequest();
      switch ($jqGridReq->getRequest()) {
         case Component_JqGrid_FormRequest::REQUEST_TYPE_ADD:
            $jqGridReq->id = null;
         case Component_JqGrid_FormRequest::REQUEST_TYPE_EDIT:
            // kontrola položek
            if($jqGridReq->{Shop_Model_AttrGroups::COLUMN_NAME} == null){
               $this->errMsg()->addMessage($this->tr('Nebyly zadány všechny povinné údaje'));
               return;
            }
            
            $record = $model->record($jqGridReq->id);
            $record->mapArray($jqGridReq);
            $model->save($record);
            $this->infoMsg()->addMessage($this->tr('Skupina byla uložena'));
            break;
         case Component_JqGrid_FormRequest::REQUEST_TYPE_DELETE:
            foreach ($jqGridReq->getIds() as $id) {
               $model->delete($id);
            }
            $this->infoMsg()->addMessage($this->tr('Vybrané skupiny včetně atributů byly smazány'));
            break;
         default:
            $this->errMsg()->addMessage($this->tr('Nepodporovaný typ operace'));
            break;
      }
      if ($this->errMsg()->isEmpty()) {
         $this->view()->allOk = true;
      }
   } */
   
   /*public function attrListController()
   {
      $this->checkReadableRights();
      
      $gid = $this->getRequestParam('gid', null);
      if($gid == null){
         throw new UnexpectedValueException($this->tr('ID skupiny nebylo předáno'));
      }
      
      // objekt komponenty JGrid
      $jqGrid = new Component_JqGrid();
      $jqGrid->request()->setDefaultOrderField(Shop_Model_Attributes::COLUMN_NAME);
      
      $model = new Shop_Model_Attributes();
      $model->where(Shop_Model_Attributes::COLUMN_ID_GROUP.' = :gid', array('gid' => $gid));

      $order = Model_ORM::ORDER_ASC;
      if($jqGrid->request()->order == 'desc'){
         $order = Model_ORM::ORDER_DESC;
      }
      
      switch ($jqGrid->request()->orderField) {
         case 'id':
            $model->order(array(Shop_Model_Attributes::COLUMN_ID => $order));
            break;
         case 'name':
         default:
            $model->order(array(Shop_Model_Attributes::COLUMN_NAME => $order));
            break;
      }
      
      if ($jqGrid->request()->isSearch()) {
//         $count = $modelAddresBook->searchCount($jqGrid->request()->searchString(),
//            (int)$this->getRequestParam('idgrp', Mails_Model_Groups::GROUP_ID_ALL),
//            $jqGrid->request()->searchField(),$jqGrid->request()->searchType());
//         $jqGrid->respond()->setRecords($count);
//
//         $book = $modelAddresBook->search($jqGrid->request()->searchString(),
//            (int)$this->getRequestParam('idgrp', Mails_Model_Groups::GROUP_ID_ALL),
//            $jqGrid->request()->searchField(),$jqGrid->request()->searchType(),
//            ($jqGrid->request()->page - 1) * $jqGrid->respond()->getRecordsOnPage(),
//            $jqGrid->request()->rows, $jqGrid->request()->orderField, $jqGrid->request()->order);
      } else {
      // list
//         $groups = $this->getAllowedGroups($jqGrid);
      }
      
      $jqGrid->respond()->sql = $model->getSQLQuery();
      
      $jqGrid->respond()->setRecords($model->count());
      
      $fromRow = ($jqGrid->request()->page - 1) * $jqGrid->respond()->getRecordsOnPage();
      
      $attribs = $model->limit($fromRow, $jqGrid->request()->rows)
         ->records(PDO::FETCH_OBJ);
      // out
      foreach ($attribs as $attr) {
         array_push($jqGrid->respond()->rows, 
            array('id' => $attr->{Shop_Model_Attributes::COLUMN_ID},
               Shop_Model_Attributes::COLUMN_NAME => $attr->{Shop_Model_Attributes::COLUMN_NAME},
               Shop_Model_Attributes::COLUMN_ID => $attr->{Shop_Model_Attributes::COLUMN_ID}
            ));
      }
      $this->view()->respond = $jqGrid->respond();
   }      */

   /*public function editAttrController()
   {
      $this->checkWritebleRights();
      $model = new Shop_Model_Attributes;
      $this->view()->allOk = false;
      
      if($this->getRequestParam('gid', null) == null){
         throw new UnexpectedValueException($this->tr('ID skupiny nebylo předáno'));
      }
      
      // část komponenty jqgrid pro formy
      $jqGridReq = new Component_JqGrid_FormRequest();
      switch ($jqGridReq->getRequest()) {
         case Component_JqGrid_FormRequest::REQUEST_TYPE_ADD:
            $jqGridReq->id = null;
         case Component_JqGrid_FormRequest::REQUEST_TYPE_EDIT:
            // kontrola položek
            if($jqGridReq->{Shop_Model_Attributes::COLUMN_NAME} == null){
               $this->errMsg()->addMessage($this->tr('Nebyly zadány všechny povinné údaje'));
               return;
            }
            $record = $model->record($jqGridReq->id);
            if($record == false || $record->isNew()){
               $record = $model->newRecord();
               $record->{Shop_Model_Attributes::COLUMN_ID_GROUP} = $this->getRequestParam('gid');
            }
            $record->mapArray($jqGridReq);
            $model->save($record);
            $this->infoMsg()->addMessage($this->tr('Atribut byl uložen'));
            break;
         case Component_JqGrid_FormRequest::REQUEST_TYPE_DELETE:
            foreach ($jqGridReq->getIds() as $id) {
               $model->delete($id);
            }
            $this->infoMsg()->addMessage($this->tr('Vybrané atributy byly smazány'));
            break;
         default:
            $this->errMsg()->addMessage($this->tr('Nepodporovaný typ operace'));
            break;
      }
      if ($this->errMsg()->isEmpty()) {
         $this->view()->allOk = true;
      }
   }*/
}
