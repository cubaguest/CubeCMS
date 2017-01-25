<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class ShopProductParams_View extends View {
   public function mainView()
   {
      $this->template()->addFile('tpl://main.phtml');
      Template_Module::setEdit(true);
   }
   
   public function editGroupController()
   {
      // nemí nic protože se vrací jako xhr třídou XHR_Respond_VVEAPI
   }

   public function editAttributeController()
   {
      // nemí nic protože se vrací jako xhr třídou XHR_Respond_VVEAPI
   }

   public function groupsListView()
   {
      $this->template()->addFile('tpl://groups.phtml');
   }

   public function variantsListView()
   {
      $this->template()->addFile('tpl://variants.phtml');
   }
}
