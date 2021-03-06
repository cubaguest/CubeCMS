<?php

/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */
class CustomBlocks_View extends View {

   public function mainView()
   {
      $this->template()->addFile($this->getTemplate('main'));
      
      if ($this->category()->getRights()->isWritable()) {
         if (!$this->toolbox instanceof Template_Toolbox2) {
            $this->toolbox = new Template_Toolbox2();
            $this->toolbox->setIcon(Template_Toolbox2::ICON_PEN);
         }

         $toolAdd = new Template_Toolbox2_Tool_PostRedirect('add_block', $this->tr('Přidat blok'), $this->link()->route('selectBlock'));
         $toolAdd->setIcon(Template_Toolbox2::ICON_ADD)->setTitle($this->tr("Přidat blok"));
         $this->toolbox->addTool($toolAdd);

         $toolSort = new Template_Toolbox2_Tool_PostRedirect('sort_blocks', $this->tr('Řadit bloky'), $this->link()->route('sortBlocks'));
         $toolSort->setIcon(Template_Toolbox2::ICON_MOVE_UP_DOWN)->setTitle($this->tr("Upravit pořadí bloků"));
         $this->toolbox->addTool($toolSort);

         if ($this->blocks) {
            foreach ($this->blocks as $block) {
               // toolboxy pro bloky
               $toolbox = new Template_Toolbox2();

               $toolEdit = new Template_Toolbox2_Tool_PostRedirect('edit_block', $this->tr('Upravit blok'), $this->link()->route('editBlock', array('id' => $block->getPK())));
               $toolEdit
                       ->setIcon(Template_Toolbox2::ICON_PEN)
                       ->setTitle($this->tr("Upravit obsah"));
               $toolbox->addTool($toolEdit);

               $toolMove = new Template_Toolbox2_Tool_PostRedirect('move_block', $this->tr('Přesunout do kateogrie'), $this->link()->route('moveBlock', array('id' => $block->getPK())));
               $toolMove
                       ->setIcon(Template_Toolbox2::ICON_EXPORT)
                       ->setTitle($this->tr("Přesunout blok do jiné kateogrie"));
               $toolbox->addTool($toolMove);

               // mazání potřebuje formulář bloku
               $this->formBlockDelete->id->setValues($block->getPK());
               $tooldel = new Template_Toolbox2_Tool_Form(clone $this->formBlockDelete);
               $tooldel->setIcon(Template_Toolbox2::ICON_DELETE)->setTitle($this->tr('Smazat blok'))
                       ->setConfirmMeassage($this->tr('Opravdu smazat blok?'))
                       ->setImportant(true);
               $toolbox->addTool($tooldel);
               
               $toolShare = new Template_Toolbox2_Tool_Button('link_block', $this->tr('Sdílení a vložení'));
               $toolShare
                       ->setIcon(Template_Toolbox2::ICON_SHARE)
                       ->addData('action', 'show-share-modal')
                       ->addData('pastelink', '{CUSTOMBLOCKS:'.$block->getPK().'}')
                       ->addData('sharelink', str_replace(Url_Link::getWebURL(), '/',  $this->link()->anchor('customblock-'.$block->getPK())))
                       ->addData('sharelinkfull', $this->link()->anchor('customblock-'.$block->getPK()))
                       ;
               $toolbox->addTool($toolShare);

               $block->toolbox = $toolbox;
            }
         }
         $this->template()->addFile('tpl://modal_share.phtml');
      }
   }

   public function selectBlockView()
   {
      $this->template()->addFile('tpl://customblocks:select_block.phtml');
      Template_Navigation::addItem($this->tr('Výběr typu bloku'), $this->link(), true);
      Template::addPageTitle($this->category()->getName());
      Template::addPageTitle($this->tr('Výběr typu bloku'));
      Template::setFullWidth(true);
   }

   public function addBlockView()
   {
      $this->template()->addFile('tpl://customblocks:edit_block.phtml');
      $name = $this->block['name'];
      $this->blockName = isset($name[Locales::getLang()]) ? $name[Locales::getLang()] : reset($name);
      Template_Navigation::addItem(sprintf($this->tr('Přidání bloku %s'), $this->blockName), $this->link(), true);
      Template::addPageTitle($this->category()->getName());
      Template::addPageTitle(sprintf($this->tr('Přidání bloku %s'), $this->blockName));
      Template::setFullWidth(true);
      $this->assignTinyMCE();
   }

   public function editBlockView()
   {
      $this->template()->addFile('tpl://customblocks:edit_block.phtml');
      $this->isEdit = true;
      $this->blockName = $this->blockRecord->{CustomBlocks_Model_Blocks::COLUMN_NAME};
      Template_Navigation::addItem(sprintf($this->tr('Úprava bloku %s'), $this->blockName), $this->link(), true);
      Template::addPageTitle($this->category()->getName());
      Template::addPageTitle(sprintf($this->tr('Úprava bloku %s'), $this->blockName));
      Template::setFullWidth(true);
      $this->assignTinyMCE();
   }

   public function sortBlocksView()
   {
      $this->template()->addFile('tpl://customblocks:sort_blocks.phtml');
      Template_Navigation::addItem($this->tr('Úprava pořadí bloků'), $this->link(), true);
      Template::setFullWidth(true);
   }

   public function moveBlockView()
   {
      $this->template()->addFile('tpl://customblocks:move_block.phtml');
      Template_Navigation::addItem(sprintf($this->tr('Přesun bloku %s do jiné kateogrie'), $this->block->{CustomBlocks_Model_Blocks::COLUMN_NAME}), $this->link(), true);
      Template::setFullWidth(true);
   }

   protected function assignTinyMCE()
   {
      if (!$this->form || !isset($this->block['items'])) {
         return;
      }
      foreach ($this->block['items'] as $index => $item) {
         if ($item['model'] == 'CustomBlocks_Model_Texts' && isset($item['tinymce']) && $item['tinymce'] == true) {
            $elementName = 'txt_' . $index;
            if ($this->form->$elementName instanceof Form_Element_TextArea) {
               if (is_bool($item['tinymce'])) {
                  $this->setTinyMCE($this->form->$elementName);
               } else {
                  $this->setTinyMCE($this->form->$elementName, $item['tinymce']);
               }
            }
         }
      }
   }

   public function contentFilter($params)
   {
      if (!isset($params[0])) {
         return null;
      }
      $idBlock = (int) $params[0];

      $block = CustomBlocks_Model_Blocks::getRecord($idBlock);
      $tplBlocks = $this->category()->getModule()->getTemplateParam('main', 'main.phtml', 'blocks', array());
      if (!isset($tplBlocks[$block->{CustomBlocks_Model_Blocks::COLUMN_TYPE}])) {
         return null;
      }
      $blockTpl = $tplBlocks[$block->{CustomBlocks_Model_Blocks::COLUMN_TYPE}]['template'];

      $this->template()->addFile('tpl://contentFilter.phtml');

      $loadfromModels = array();
      $blocksIds[] = $block->getPK();
      $block->block_struct = $tplBlocks[$block->{CustomBlocks_Model_Blocks::COLUMN_TYPE}];
      $block->block_tpl = $tplBlocks[$block->{CustomBlocks_Model_Blocks::COLUMN_TYPE}]['template'];
      $block->block_items = array();

      // projití a vybrání modelů
      foreach ($block->block_struct['items'] as $tplBlockItem) {
         if (!isset($loadfromModels[$tplBlockItem['model']])) {
            $loadfromModels[$tplBlockItem['model']] = array();
         }
         $loadfromModels[$tplBlockItem['model']] = $block->getPK();
      }

      $blocksData = array();
      foreach ($loadfromModels as $modelName => $id) {
         /* @var $model CustomBlocks_Model_Items */
         $model = new $modelName();
         $blocksData = array_merge($blocksData, $model
                         ->where(CustomBlocks_Model_Items::COLUMN_ID_BLOCK . " = :idb", arraY('idb' => $id))
                         ->records());
      }

      foreach ($blocksData as $key => $data) {
         $block->block_items[$data->{CustomBlocks_Model_Items::COLUMN_INDEX}] = clone $data;
         // není třeba aby byl dále použit
         unset($blocksData[$key]);
      }
      $this->template()->block = $block;
   }
}
