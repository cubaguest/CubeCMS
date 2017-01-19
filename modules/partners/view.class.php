<?php
class Partners_View extends View {
   public function mainView() {
      $this->template()->addFile("tpl://list.phtml");

      if($this->category()->getRights()->isWritable()) {
         $toolbox = new Template_Toolbox2();

         $toolAdd = new Template_Toolbox2_Tool_PostRedirect('partner_add', $this->tr('Přidat partnera'), $this->link()->route('add'));
         $toolAdd->setIcon(Template_Toolbox2::ICON_ADD)->setTitle($this->tr("Přidat nového partnera"));
         $toolbox->addTool($toolAdd);
         
         $toolAddG = new Template_Toolbox2_Tool_PostRedirect('grp_add', $this->tr('Přidat skupinu'), $this->link()->route('addGroup'));
         $toolAddG->setIcon(Template_Toolbox2::ICON_ADD)->setTitle($this->tr("Přidat novou skupinu"));
         $toolbox->addTool($toolAddG);
         
         if($this->partners != false && count($this->partners) > 1){
            $toolOrder = new Template_Toolbox2_Tool_PostRedirect('partners_edit_order', $this->tr('Upravit pořadí'), $this->link()->route('editOrder'));
            $toolOrder->setIcon(Template_Toolbox2::ICON_MOVE_UP_DOWN)->setTitle($this->tr("Upravit pořadí partnerů"));
            $toolbox->addTool($toolOrder);
         }
         
         if($this->partnersGroups != false && count($this->partnersGroups) > 1){
            $toolOrderG = new Template_Toolbox2_Tool_PostRedirect('groups_edit_order', $this->tr('Upravit pořadí skupin'), $this->link()->route('editGroupsOrder'));
            $toolOrderG->setIcon(Template_Toolbox2::ICON_MOVE_UP_DOWN)->setTitle($this->tr("Upravit pořadí skupin"));
            $toolbox->addTool($toolOrderG);
         }
         
         $toolEditText = new Template_Toolbox2_Tool_PostRedirect('partner_edittext', $this->tr('Upravit úvodní text'), $this->link()->route('editText'));
         $toolEditText->setIcon(Template_Toolbox2::ICON_PAGE_EDIT)->setTitle($this->tr("Upravit úvodní text"));
         $toolbox->addTool($toolEditText);

         $this->toolbox = $toolbox;

         $toolboxEdit = new Template_Toolbox2();
         $toolboxEdit->setIcon(Template_Toolbox2::ICON_WRENCH);

         $toolEdit = new Template_Toolbox2_Tool_PostRedirect('partner_edit', $this->tr("Upravit partnera"));
         $toolEdit->setIcon(Template_Toolbox2::ICON_PEN)->setTitle($this->tr('Upravit partnera'));
         $toolboxEdit->addTool($toolEdit);
         
         $toolChangeVis = new Template_Toolbox2_Tool_Form($this->formVisibility);
         $toolChangeVis->setIcon(Template_Toolbox2::ICON_PREVIEW);
         $toolboxEdit->addTool($toolChangeVis);

         $toolDelete = new Template_Toolbox2_Tool_Form($this->formDelete);
         $toolDelete->setIcon(Template_Toolbox2::ICON_DELETE);
         $toolDelete->setConfirmMeassage($this->tr('Opravdu smazat partnera?'));
         $toolboxEdit->addTool($toolDelete);
         
         $this->toolboxEdit = $toolboxEdit;
      }
      
      $this->imagePath = $this->category()->getModule()->getDataDir(true);
   }

   /**
    * Viewer pro přidání článku
    */
   public function addView() {
      Template_Module::setEdit(true);
      $this->template()->addFile("tpl://edit.phtml");
      Template_Navigation::addItem($this->tr('Přidání položky'), $this->link());
   }
   
   /**
    * Viewer pro přidání článku
    */
   public function addGroupView() {
      Template_Module::setEdit(true);
      $this->template()->addFile("tpl://edit_group.phtml");
      Template_Navigation::addItem($this->tr('Přidání skupiny položek'), $this->link());
   }

   /**
    * Viewer pro editaci novinky
    */
   public function editView() {
      Template_Module::setEdit(true);
      $this->edit = true;
      $this->template()->addFile("tpl://edit.phtml");
      // cestak obrázků
      $this->imagePath = $this->category()->getModule()->getDataDir(true);
      Template_Navigation::addItem($this->tr('Úprava položky'), $this->link());
   }
   
   /**
    * Viewer pro editaci novinky
    */
   public function editGroupView() {
      Template_Module::setEdit(true);
      $this->edit = true;
      $this->template()->addFile("tpl://edit_group.phtml");
      // cestak obrázků
      $this->imagePath = $this->category()->getModule()->getDataDir(true);
      Template_Navigation::addItem($this->tr('Úprava skupiny položek'), $this->link());
   }

   public function editOrderView()
   {
      Template_Module::setEdit(true);
      $this->template()->addFile('tpl://edit_order.phtml');
      Template_Navigation::addItem($this->tr('Řazení položek'), $this->link());
   }
   
   public function editGroupsOrderView()
   {
      Template_Module::setEdit(true);
      $this->template()->addFile('tpl://edit_order_groups.phtml');
      Template_Navigation::addItem($this->tr('Řazení skupin položek'), $this->link());
   }
   
   public function editTextView()
   {
      Template_Module::setEdit(true);
      $this->template()->addFile('tpl://edit_text.phtml');
      $this->setTinyMCE($this->form->text, 'advanced');
      Template_Navigation::addItem($this->tr('Úprava úvodního textu'), $this->link());
   }
}
