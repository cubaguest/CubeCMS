<?php

/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */
class Events_View extends View {

   public function mainView()
   {
      $this->template()->addFile('tpl://main.phtml');
//      Template_Module::setEdit(true);
      $this->createToolbox();
   }
   
   protected function createToolbox()
   {
      if ($this->category()->getRights()->isControll()) {
         $toolbox = new Template_Toolbox2();

         $toolHome = new Template_Toolbox2_Tool_Redirect('home', $this->tr('Úvodní strana'));
         $toolHome->setIcon('home')->setAction($this->link()->route());
         $toolbox->addTool($toolHome);

         $toolAddEv = new Template_Toolbox2_Tool_Redirect('eventAdd', $this->tr('Přidat událost'));
         $toolAddEv->setIcon(Template_Toolbox2::ICON_ADD)->setAction($this->link()->route('addEvent'));
         $toolbox->addTool($toolAddEv);

         $toolEventsList = new Template_Toolbox2_Tool_Redirect('eventsList', $this->tr('Správa událostí'));
         $toolEventsList->setIcon('list')->setAction($this->link()->route('listEvents'));
         $toolbox->addTool($toolEventsList);

         $toolCatsList = new Template_Toolbox2_Tool_Redirect('catsList', $this->tr('Správa kategorií'));
         $toolCatsList->setIcon('list')->setAction($this->link()->route('listCats'));
         $toolbox->addTool($toolCatsList);

         $toolAddCat = new Template_Toolbox2_Tool_Redirect('catAdd', $this->tr('Přidat kategorii'));
         $toolAddCat->setIcon(Template_Toolbox2::ICON_ADD)->setAction($this->link()->route('addCat'));
         $toolbox->addTool($toolAddCat);

         $toolExports = new Template_Toolbox2_Tool_Redirect('export', $this->tr('Export dat'));
         $toolExports->setIcon(Template_Toolbox2::ICON_EXPORT)->setAction($this->link()->route('exports'));
         $toolbox->addTool($toolExports);

         $this->toolbox = $toolbox;
      }
   }

   public function addCatView()
   {
      $this->template()->addFile('tpl://editCat.phtml');
      Template_Module::setEdit(true);
   }
   
   public function editCatView()
   {
      $this->addCatView();
   }

   public function listCatsView()
   {
      $this->template()->addFile('tpl://listCats.phtml');
      Template_Module::setEdit(true);

      // toolbox pro item
      $toolbox = new Template_Toolbox2();
      $toolbox->setIcon(Template_Toolbox2::ICON_PEN);
      
      $toolGenerateToken = new Template_Toolbox2_Tool_Form($this->formGenToken);
      $toolGenerateToken->setIcon('key_refresh.png');
      $toolbox->addTool($toolGenerateToken);
      
      $toolSetDefault = new Template_Toolbox2_Tool_Form($this->formSetPublic);
      $toolSetDefault->setIcon('eye.png');
      $toolbox->addTool($toolSetDefault);
      
      $toolEdit = new Template_Toolbox2_Tool_PostRedirect('editCat', $this->tr('Upravit'), $this->link());
      $toolEdit->setIcon(Template_Toolbox2::ICON_PEN);
      $toolbox->addTool($toolEdit);
      
      $toolRemove = new Template_Toolbox2_Tool_Form($this->formDelete);
      $toolRemove->setIcon(Template_Toolbox2::ICON_DELETE)->setConfirmMeassage($this->tr('Opravdu smazat kategorii včetně přiřazených akcí?'));
      $toolRemove->setImportant(true);
      $toolbox->addTool($toolRemove);
      
      $this->toolboxItem = $toolbox;
      
      $this->createToolbox();
   }

   public function listEventsView()
   {
      $this->template()->addFile('tpl://listEvents.phtml');
      Template_Module::setEdit(true);

      $toolbox = new Template_Toolbox2();
      $toolbox->setIcon(Template_Toolbox2::ICON_PEN);
      
      $toolRecomended = new Template_Toolbox2_Tool_Form($this->formRecomended);
      $toolRecomended->setIcon('star');
      $toolbox->addTool($toolRecomended);
      
      $toolVisible = new Template_Toolbox2_Tool_Form($this->formVisible);
      $toolVisible->setIcon('eye');
      $toolbox->addTool($toolVisible);
      
      $toolClone = new Template_Toolbox2_Tool_PostRedirect('cloneEvent', $this->tr('Duplikovat'), $this->link()->route('addEvent'));
      $toolClone->setIcon('copy');
      $toolbox->addTool($toolClone);
      
      $toolEdit = new Template_Toolbox2_Tool_PostRedirect('editEvent', $this->tr('Upravit'), $this->link());
      $toolEdit->setIcon(Template_Toolbox2::ICON_PEN);
      $toolbox->addTool($toolEdit);
      
      $toolRemove = new Template_Toolbox2_Tool_Form($this->formDelete);
      $toolRemove->setIcon(Template_Toolbox2::ICON_DELETE)->setConfirmMeassage($this->tr('Opravdu smazat položku?'));
      $toolRemove->setImportant(true);
      $toolbox->addTool($toolRemove);
      
      $this->toolboxItem = $toolbox;
      
      $this->createToolbox();
   }
   
   public function addEventView()
   {
      $this->template()->addFile('tpl://editEvent.phtml');
      Template_Module::setEdit(true);
      $this->setTinyMCE($this->form->desc, 'advanced');
   }
   
   public function editEventView()
   {
      $this->addEventView();
   }

   public function exportsView()
   {
      $this->createToolbox();
      $this->template()->addFile('tpl://exports.phtml');
      Template_Module::setEdit(true);
   }
}
