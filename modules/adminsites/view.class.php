<?php
/** 
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class AdminSites_View extends View {
	public function mainView()
   {
      $this->template()->addFile('tpl://main.phtml');
      
      if(!empty($this->sites)){
         $toolboxSite = new Template_Toolbox2();
         $toolboxSite->setTemplate(Template_Toolbox2::TEMPLATE_INLINE);

         $toolEdit = new Template_Toolbox2_Tool_PostRedirect('edit', $this->tr('Upravit'));
         $toolEdit->setIcon(Template_Toolbox2::ICON_PEN);
         $toolboxSite->addTool($toolEdit);

         $toolDelete = new Template_Toolbox2_Tool_Form($this->formDelete);
         $toolDelete->setIcon(Template_Toolbox2::ICON_DELETE);
         $toolDelete->setImportant(true);
         $toolboxSite->addTool($toolDelete);
         
         foreach ($this->sites as $site) {
            $toolboxSite->sitedel->getForm()->id->setValues($site->getPK());
            if($site->{Model_Sites::COLUMN_IS_ALIAS}){
               $toolboxSite->edit->setAction($this->link()->route('editAlias', array('id' => $site->getPK())));
               $toolboxSite->sitedel->setConfirmMeassage($this->tr('Odebrat alias ke složce?'));
            } else {
               $toolboxSite->edit->setAction($this->link()->route('edit', array('id' => $site->getPK())));
               $toolboxSite->sitedel->setConfirmMeassage($this->tr('Opravdu smazat celý web i s daty a aliasy?'));
            }
            $site->toolbox = clone $toolboxSite;
         }
      }
      
   }
   
   public function addSiteView()
   {
      $this->template()->addFile('tpl://edit.phtml');
   }
   
   public function editSiteView()
   {
      $this->template()->addFile('tpl://edit.phtml');
   }
   
   public function addAliasView()
   {
      $this->template()->addFile('tpl://edit-alias.phtml');
   }
   
   public function editAliasView()
   {
      $this->template()->addFile('tpl://edit-alias.phtml');
   }
}
