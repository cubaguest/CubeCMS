<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class ShopProductGeneral_View extends Shop_Product_View {
   public function mainView() 
   {
      $this->template()->addFile('tpl://list.phtml');

      if($this->category()->getRights()->isWritable()) {
         $toolbox = new Template_Toolbox2();
         $toolbox->setIcon(Template_Toolbox2::ICON_WRENCH);

         $toolAdd = new Template_Toolbox2_Tool_PostRedirect('add_product', $this->tr('Přidat produkt'),
                 $this->link()->route('add'));
         $toolAdd->setIcon('page_add.png')->setTitle($this->tr("Přidat nový produkt"));
         $toolbox->addTool($toolAdd);

         $this->toolbox = $toolbox;

//         if(isset ($_GET['l']) AND isset ($this->text[Text_Model::COLUMN_TEXT][$_GET['l']])){
//            $l = $_GET['l'];
//            $this->text->{Text_Model::COLUMN_TEXT} = $this->text[Text_Model::COLUMN_TEXT][$l];
//            if($this->text[Text_Model::COLUMN_LABEL][$l] != null){
//               $this->text->{Text_Model::COLUMN_LABEL} = $this->text[Text_Model::COLUMN_LABEL][$l];
//            } else {
//               $obj = Category::getSelectedCategory()->getCatDataObj();
//               $this->text->{Text_Model::COLUMN_LABEL} = $obj[Model_Category::COLUMN_NAME][$l];
//            }
//            unset ($obj);
//         }
      }
   }

   public function detailView() 
   {
      $this->template()->addFile('tpl://detail.phtml');

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
      Template_Navigation::addItem($this->product->{Shop_Model_Product::COLUMN_NAME}, $this->link()->route('detail'));
      Template_Navigation::addItem($this->tr('Úprava'), $this->link());
   }
   
   public function editVariantsView() 
   {
      $this->editProductVariants();
      Template_Navigation::addItem($this->product->{Shop_Model_Product::COLUMN_NAME}, $this->link()->route('detail'));
      Template_Navigation::addItem($this->tr('Úprava variant'), $this->link());
   }
   
   public function addView() 
   {
      $this->editProduct(false);
      Template_Navigation::addItem($this->tr('Přidání produktu'), $this->link());
   }
   
}

?>