<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class Panels_Controller extends Controller {

   public function mainController() {

      $panelPositions = vve_parse_cfg_value(VVE_PANEL_TYPES);

      $catModel = new Model_Category();
      $categories = $catModel->getCategoryList(true);
      $catArr = array();
      foreach ($categories as $cat) {
         $catArr[(string)$cat->{Model_Category::COLUMN_CAT_LABEL}] = $cat->{Model_Category::COLUMN_CAT_ID};
      }

      $addForm = new Form('newpanel_');
      $panelCategory = new Form_Element_Select('panel_cat', $this->_('Panel kategorie'));
      $panelCategory->setOptions($catArr);
      $addForm->addElement($panelCategory);

      $panelType = new Form_Element_Select('panel_box', $this->_('Box panelu'));
      $panelType->setOptions($panelPositions);
      $addForm->addElement($panelType);

      $panelOrder = new Form_Element_Text('panel_order', $this->_('Řazení panelu'));
      $panelOrder->setValues(0);
      $addForm->addElement($panelOrder);

      $submit = new Form_Element_Submit('send', $this->_('Uložit'));
      $addForm->addElement($submit);

      if($addForm->isValid()) {
         $panelModel = new Model_Panel();
         if($panelModel->savePanel($addForm->panel_cat->getValues(), $addForm->panel_box->getValues(),
             $addForm->panel_order->getValues()) !== false) {
            $this->infoMsg()->addMessage($this->_("Panel byl přidán"));
            $this->link()->reload();
         }
      }

      $editForm = new Form('editpanels_');
      $panelCategory = new Form_Element_Select('panel_cat', $this->_('Panel kategorie'));
      $panelCategory->setOptions($catArr);
      $editForm->addElement($panelCategory);

      $panelType = new Form_Element_Select('panel_box', $this->_('Box panelu'));
      $panelType->setOptions($panelPositions);
      $editForm->addElement($panelType);

      $panelOrder = new Form_Element_Text('panel_order', $this->_('Řazení panelu'));
      $editForm->addElement($panelOrder);

      $panelId = new Form_Element_Hidden('panel_id');
      $editForm->addElement($panelId);

      $panelRemove = new Form_Element_Checkbox('panel_remove');
      $editForm->addElement($panelRemove);

      $submit = new Form_Element_Submit('send', $this->_('Uložit'));
      $editForm->addElement($submit);

      if($editForm->isValid()) {
         $pM = new Model_Panel();
         $arr = $editForm->panel_cat->getValues();
         foreach ($arr as $key => $v) {
            if($editForm->panel_remove->getValues($key) == true) {
               $pM->deletePanel($editForm->panel_id->getValues($key));
            } else {
               $pM->savePanel($editForm->panel_cat->getValues($key), $editForm->panel_box->getValues($key),
                   $editForm->panel_order->getValues($key), $editForm->panel_id->getValues($key));
            }

         }
         $this->infoMsg()->addMessage($this->_("Změny byly uloženy"));
         $this->link()->reload();      }

      // view
      $this->view()->template()->addTplFile('newpanel.phtml');
      $this->view()->template()->addForm = $addForm;
      $this->view()->template()->editForm = $editForm;
      $this->view()->template()->addTplFile('list.phtml');
   }



   private function catsToArrayForForm($categories) {
   // pokud je hlavní kategorie
      if($categories->getLevel() != 0) {
         $this->categoriesArray[str_repeat('&nbsp;', $categories->getLevel()*3).
             (string)$categories->getCatObj()->{Model_Category::COLUMN_CAT_LABEL}]
             = (string)$categories->getCatObj()->{Model_Category::COLUMN_CAT_ID};
      } else {
         $this->categoriesArray[$this->_('Kořen')] = 0;
      }
      if(!$categories->isEmpty()) {
         foreach ($categories as $cat) {
            $this->catsToArrayForForm($cat);
         }
      }
   }

}

?>