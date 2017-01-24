<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class ShopProductGeneralAdmin_View extends Shop_Product_View {
   public function mainView() 
   {
      $this->template()->addFile('tpl://list.phtml');
      Template_Module::setEdit(true);
      
      $toolbox = new Template_Toolbox2();
      $toolEdit = new Template_Toolbox2_Tool_Redirect('editProduct', 
              $this->tr('Upravit produkt'));
      $toolEdit->setIcon(Template_Toolbox2::ICON_PAGE_EDIT);
      $toolbox->addTool($toolEdit);

      $toolEditV = new Template_Toolbox2_Tool_Redirect('editVariant', 
              $this->tr('Upravit varianty produktu'));
      $toolEditV->setIcon('code-fork');
      $toolbox->addTool($toolEditV);

      $toolEditImg = new Template_Toolbox2_Tool_Redirect('editImages', 
              $this->tr('Upravit obrázky produktu'));
      $toolEditImg->setIcon(Template_Toolbox2::ICON_IMAGE_WRENCH);
      $toolbox->addTool($toolEditImg);
      
      $toolEditParams = new Template_Toolbox2_Tool_Redirect('editParams', 
              $this->tr('Upravit parametry produktu'));
      $toolEditParams->setIcon(Template_Toolbox2::ICON_COG);
      $toolbox->addTool($toolEditParams);

      $toolCopy = new Template_Toolbox2_Tool_Redirect('duplicate', 
              $this->tr('Duplikovat produkt'));
      $toolCopy->setIcon(Template_Toolbox2::ICON_COPY);
      $toolbox->addTool($toolCopy);


      $toolDel = new Template_Toolbox2_Tool_PostRedirect('delete', 
              $this->tr('Smazat produkt'));
      $toolDel->setIcon(Template_Toolbox2::ICON_DELETE);
      $toolDel->setImportant(true);
      $toolbox->addTool($toolDel);
      
      foreach ($this->products as $p) {
         $toolbox->editProduct->setAction($this->link()->route('edit', array('urlkey' => (string)$p->getUrlKey())));
         $toolbox->editVariant->setAction($this->link()->route('editVariants', array('urlkey' => (string)$p->getUrlKey())));
         $toolbox->editImages->setAction($this->link()->route('editImages', array('urlkey' => (string)$p->getUrlKey())));
         $toolbox->editParams->setAction($this->link()->route('editParams', array('urlkey' => (string)$p->getUrlKey())));
         $toolbox->duplicate->setAction($this->link()->param('action','duplicate')->param('idp', $p->getPK()));
         $toolbox->delete->setAction($this->link()->param('idp', $p->getPK())->param('action', 'delete'));
         $toolbox->delete->setConfirmMeassage(sprintf($this->tr('Smazat produkt %s ?'), $p->{Shop_Model_Product::COLUMN_NAME}));
         $p->toolbox = clone $toolbox;
      }
      
   }

   public function detailView() 
   {
      $this->template()->addFile('tpl://detail.phtml');
      Template_Module::setEdit(true);

      if($this->category()->getRights()->isWritable()) {
         $toolbox = new Template_Toolbox2();
         $toolbox->setIcon(Template_Toolbox2::ICON_WRENCH);

         $toolEdit = new Template_Toolbox2_Tool_PostRedirect('edit_product', $this->tr('Upravit zboží'),
                 $this->link()->route('edit'));
         $toolEdit->setIcon('page_edit.png')->setTitle($this->tr("Upravit vybrané zboží"));
         $toolbox->addTool($toolEdit);
         
         $toolEditVariants = new Template_Toolbox2_Tool_PostRedirect('edit_product_variants', $this->tr('Upravit varianty'),
                 $this->link()->route('editVariants'));
         $toolEditVariants->setIcon('page_attach.png')->setTitle($this->tr("Upravit varianty vybraného zboží"));
         $toolbox->addTool($toolEditVariants);

         $toolState = new Template_Toolbox2_Tool_Form($this->formState);
         $toolState->setIcon($this->formState->state->getValues() ? "enable.png" : "disable.png")->setTitle($this->formState->change->getLabel());
         $toolbox->addTool($toolState);

         $tooldel = new Template_Toolbox2_Tool_Form($this->formDelete);
         $tooldel->setIcon('page_delete.png')->setTitle($this->tr('Smazat zboží'))
            ->setConfirmMeassage($this->tr('Opravdu smazat toto zboží?'));
         $toolbox->addTool($tooldel);

         $toolDuplicate = new Template_Toolbox2_Tool_Form($this->formDuplicate);
         $toolDuplicate->setIcon('page_copy.png')->setTitle($this->tr('Duplikovat zboží'));
         $toolbox->addTool($toolDuplicate);

         $this->toolbox = $toolbox;
      }
      Template_Navigation::addItem($this->product->{Shop_Model_Product::COLUMN_NAME}, $this->link());
   }
   
   public function editView() 
   {
      $this->editProduct(true);
      Template_Module::setEdit(true);
      Template_Navigation::addItem($this->product->{Shop_Model_Product::COLUMN_NAME}, $this->link()->route('edit'));
      Template_Navigation::addItem($this->tr('Úprava'), $this->link());
   }
   
   public function editImagesView() 
   {
      $this->editImages(true);
      Template_Module::setEdit(true);
      Template_Navigation::addItem($this->product->{Shop_Model_Product::COLUMN_NAME}, $this->link()->route('edit'));
      Template_Navigation::addItem($this->tr('Úprava obrázků produktu'), $this->link());
      
      $this->moduleActionButtons = array(
         array(
            'link' => $this->link()->route(),
            'title' => $this->tr('Zavřít úpravu obrázků a přejít zpět na seznam produktů'),
            'icon' => 'chevron-left',
            'name' => $this->tr('Zpět na seznam'),
         ),
      );
   }
   
   public function editVariantsView() 
   {
      $this->editProductVariants();
      Template_Module::setEdit(true);
      Template_Navigation::addItem($this->product->{Shop_Model_Product::COLUMN_NAME}, $this->link()->route('edit'));
      Template_Navigation::addItem($this->tr('Úprava variant'), $this->link());

      $this->moduleActionButtons = array(
         array(
            'link' => $this->link()->route(),
            'title' => $this->tr('Zavřít úpravu variant a přejít zpět na seznam produktů'),
            'icon' => 'chevron-left',
            'name' => $this->tr('Zpět na seznam'),
         ),
      );
   }
   
   public function editParamsView() 
   {
      $this->editProductParams();
      Template_Module::setEdit(true);
      Template_Navigation::addItem($this->product->{Shop_Model_Product::COLUMN_NAME}, $this->link()->route('edit'));
      Template_Navigation::addItem($this->tr('Úprava parametrů'), $this->link());

      $this->moduleActionButtons = array(
         array(
            'link' => $this->link()->route(),
            'title' => $this->tr('Zavřít úpravu parametrů a přejít zpět na seznam produktů'),
            'icon' => 'chevron-left',
            'name' => $this->tr('Zpět na seznam'),
         ),
      );
   }
   
   public function addView() 
   {
      Template_Module::setEdit(true);
      $this->editProduct(false);
      Template_Navigation::addItem($this->tr('Přidání produktu'), $this->link());
   }
   
}
