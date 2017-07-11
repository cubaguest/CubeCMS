<?php

/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */
class CustomBlocks_Controller extends Controller {

   public function mainController()
   {
      // load vars by template
      $tplBlocks = $this->view()->getCurrentTemplateParam('blocks');
      if (empty($tplBlocks)) {
         throw new InvalidArgumentException($this->tr('Šablona nemá definovány bloky. Kontaktujte webmastera.'));
      }

      $this->checkDeleteBlock($tplBlocks);
      // načtení jednodlivých bloků a jejich obsahů

      $blocks = CustomBlocks_Model_Blocks::getBlocks($this->category()->getId());
      if ($blocks) {
         $loadfromModels = array();
         $blocksIds = array();

         foreach ($blocks as &$block) {
            if (isset($tplBlocks[$block->{CustomBlocks_Model_Blocks::COLUMN_TYPE}])) {
               $blocksIds[] = $block->getPK();
               $block->block_struct = $tplBlocks[$block->{CustomBlocks_Model_Blocks::COLUMN_TYPE}];
               $block->block_tpl = $tplBlocks[$block->{CustomBlocks_Model_Blocks::COLUMN_TYPE}]['template'];
               $block->block_items = array();

               // projití a vybrání modelů
               foreach ($block->block_struct['items'] as $tplBlockItem) {
                  if (!isset($loadfromModels[$tplBlockItem['model']])) {
                     $loadfromModels[$tplBlockItem['model']] = array();
                  }
                  $loadfromModels[$tplBlockItem['model']][] = $block->getPK();
               }
            }
         }
         // odstranění duplicit aby se nenačítalo z jednoho modelu několikrát
//         $loadfromModels = array_unique($loadfromModels);
//         Debug::log($blocksIds, $loadfromModels);
         // načtení dat z jednotlivých modelů pohromadě, čímž se dosáhne pouze páru dotazů na db
         $blocksData = array();
         foreach ($loadfromModels as $modelName => $ids) {
            /* @var $model CustomBlocks_Model_Items */
            $model = new $modelName();
            $blocksData = array_merge($blocksData, $model
                            ->where(CustomBlocks_Model_Items::COLUMN_ID_BLOCK . " IN (" . $model->getWhereINPlaceholders($ids) . ")", $model->getWhereINValues($ids))
                            ->records());
         }

         // zařazení dat do bloků pod svoje indexy
         foreach ($blocks as &$block) {
            foreach ($blocksData as $key => $data) {
               if ($block->getPK() == $data->{CustomBlocks_Model_Items::COLUMN_ID_BLOCK}) {
                  $block->block_items[$data->{CustomBlocks_Model_Items::COLUMN_INDEX}] = clone $data;
                  // není třeba aby byl dále použit
                  unset($blocksData[$key]);
               }
            }
         }
      }
      $this->view()->blocks = $blocks;
   }

   protected function checkDeleteBlock($tplBlocks)
   {
      if (!$this->rights()->isWritable()) {
         return;
      }

      $fDelete = new Form('block_delete_');

      $eId = new Form_Element_Hidden('id');
      $fDelete->addElement($eId);

      $eSubmit = new Form_Element_Submit('delete', $this->tr('Smazat blok'));
      $fDelete->addElement($eSubmit);

      if ($fDelete->isValid()) {
         $modelBlocks = new CustomBlocks_Model_Blocks();
         $block = $modelBlocks->record($fDelete->id->getValues());

         if ($block && isset($tplBlocks[$block->{CustomBlocks_Model_Blocks::COLUMN_TYPE}])) {
            foreach ($tplBlocks[$block->{CustomBlocks_Model_Blocks::COLUMN_TYPE}]['items'] as $index => $item) {
               /* @var $model CustomBlocks_Model_Items */
               $model = new $item['model'](array('dir' => $this->module()->getDataDir()));

               $item = $model
                       ->where(CustomBlocks_Model_Items::COLUMN_ID_BLOCK . " = :idb AND " . CustomBlocks_Model_Items::COLUMN_INDEX . " = :ind", array('idb' => $block->getPK(), 'ind' => $index))
                       ->record();
               if ($item) {
                  $model->delete($item);
               }
            }
         }
         $modelBlocks->delete($block);
         $this->log(sprintf('Smazán volitelný blok', $fDelete->id->getValues()));
         $this->infoMsg()->addMessage($this->tr('Blok byl smazán'));
         $this->link()->redirect();
      }

      $this->view()->formBlockDelete = $fDelete;
   }

   public function selectBlockController()
   {
      $this->checkWritebleRights();
      $blocks = $this->view()->getCurrentTemplateParam('blocks');

      $blocksData = array();
      foreach ($blocks as $type => $block) {
         // obrázek ve face ?
         $img = false;
         if (!isset($block['img'])) {
            $block['img'] = pathinfo($block['template'], PATHINFO_FILENAME) . '.jpg';
         }
         if (is_file(Face::getCurrent()->getDir() . 'modules/' . $this->module()->getName() . '/images/' . $block['img'])) {
            $img = Face::getCurrent()->getDir() . 'modules/' . $this->module()->getName() . '/images/' . $block['img'];
         } else if (is_file($this->module()->getLibDir() . 'images' . DIRECTORY_SEPARATOR . $block['img'])) {
            $img = $this->module()->getLibDir() . 'images' . DIRECTORY_SEPARATOR . $block['img'];
         }

         $blocksData[] = array(
             'name' => isset($block['name'][Locales::getLang()]) ? $block['name'][Locales::getLang()] : reset($block['name']),
             'img' => $img,
             'url' => $this->link()->route('addBlock', array('type' => $type)),
         );
      }

      $this->view()->blocks = $blocksData;
   }

   public function addBlockController($type)
   {
      $this->checkWritebleRights();
      $blocks = $this->view()->getCurrentTemplateParam('blocks');

      if (!isset($blocks[$type])) {
         throw new InvalidArgumentException($this->tr('Nebyl předán podporovaný typ bloku'));
      }
      $block = $blocks[$type];

      $form = $this->createForm($block);
      if(isset($block['defaultName'])){
         $form->blockname->setValues($block['defaultName']);
      }

      if ($form->isValid()) {
         $blockRecord = $this->processForm($form, $block, $this->getRequest('type'));

         $this->log(sprintf('Přidán volitelný blok %s', $blockRecord->getPK()));
         $this->infoMsg()->addMessage($this->tr('Blok byl uložen na konec'));
         $this->link()->route()->redirect();
      }

      $this->view()->form = $form;
      $this->view()->block = $block;
   }

   public function editBlockController($id)
   {
      $this->checkWritebleRights();
      $blocks = $this->view()->getCurrentTemplateParam('blocks');

      $blockRecord = CustomBlocks_Model_Blocks::getRecord($id);
      if (!$blockRecord || !isset($blocks[$blockRecord->{CustomBlocks_Model_Blocks::COLUMN_TYPE}])) {
         throw new UnexpectedPageException();
      }

      $block = $blocks[$blockRecord->{CustomBlocks_Model_Blocks::COLUMN_TYPE}];
      $form = $this->createForm($block, $blockRecord);

      if ($form->isValid()) {
         $this->processForm($form, $block, $this->getRequest('type'), $blockRecord->getPK());
         $this->log(sprintf('Upraven volitelný blok %s', $blockRecord->getPK()));
         $this->infoMsg()->addMessage($this->tr('Blok byl Uložen'));
         if($form->save->getValues() == Form_Element_SaveCancelStay::STATE_SAVE_CLOSE){
            $this->link()->route()->redirect();
         } else {
            $this->link()->redirect();
         }
      }

      $this->view()->form = $form;
      $this->view()->block = $block;
      $this->view()->blockRecord = $blockRecord;
   }

   public function sortBlocksController()
   {
      $this->checkWritebleRights();

      $blocks = CustomBlocks_Model_Blocks::getBlocks($this->category()->getId());
      $form = new Form('blocks_order_');

      $eId = new Form_Element_Hidden('id');
      $eId->setDimensional();

      $form->addElement($eId);

      $eSave = new Form_Element_SaveCancel('save');
      $form->addElement($eSave);

      if ($form->isSend() && $form->save->getValues() == false) {
         $this->link()->route()->redirect();
      }

      if ($form->isValid()) {
         $ids = $form->id->getValues();
         foreach ($ids as $index => $id) {
            CustomBlocks_Model_Blocks::setRecordPosition($id, $index + 1);
         }

         $this->infoMsg()->addMessage($this->tr('Pořadí bylo uloženo'));
         $this->link()->route()->redirect();
      }

      $this->view()->blocks = $blocks;
      $this->view()->form = $form;
      $this->view()->blocks = $blocks;
   }

   public function moveBlockController($id)
   {
      $block = CustomBlocks_Model_Blocks::getRecord($id);
      if (!$block) {
         throw new UnexpectedPageException();
      }

      $fMove = new Form('block_move_');

      $eCat = new Form_Element_Select('idcat', $this->tr('Cílová kategorie'));
      $cats = Model_Category::getCategoryListByModule('customblocks');
      $templates = $this->module()->getAllTemplates();
      foreach ($cats as $cat) {
         $catObj = new Category(null, false, $cat);
         $eCat->addOption($cat->{Model_Category::COLUMN_NAME}
                 . ', ' . $this->tr('šablona: ') . '"' . $templates['main'][$catObj->getParam('tpl_action_main')]['name'] . '"'
                 . ($this->category()->getId() == $cat->getPK() ? ' (' . $this->tr('Aktuální') . ')' : ''), $cat->getPK());
      }
      $fMove->addElement($eCat);


      $eSave = new Form_Element_SaveCancel('move', array($this->tr('Přesunout'), $this->tr('Zrušit')));
      $fMove->addElement($eSave);

      if ($fMove->isValid()) {
         $cat = new Category($fMove->idcat->getValues());

         // přesun obrázků
         $mBlockImages = new CustomBlocks_Model_Images();
         $images = $mBlockImages->where(CustomBlocks_Model_Images::COLUMN_ID_BLOCK . " = :idb", array('idb' => $block->getPK()))->records();
         foreach ($images as $img) {
            $file = new File($img->{CustomBlocks_Model_Images::COLUMN_FILE}, $this->module()->getDataDir());
            $file->move($cat->getModule()->getDataDir());
         }

         // přesun souborů
         $mBlockFiles = new CustomBlocks_Model_Files();
         $files = $mBlockFiles->where(CustomBlocks_Model_Files::COLUMN_ID_BLOCK . " = :idb", array('idb' => $block->getPK()))->records();
         foreach ($files as $f) {
            $file = new File($f->{CustomBlocks_Model_Files::COLUMN_FILE}, $this->module()->getDataDir());
            $file->move($cat->getModule()->getDataDir());
         }

         // přesun bloku
         $block->moveRecordByGroup(array(CustomBlocks_Model_Blocks::COLUMN_ID_CAT => $fMove->idcat->getValues()));
         // hotovo
         $this->infoMsg()->addMessage(sprintf($this->tr('Blok byl přesunut do kateogrie <a href="%s">%s</a>'), $this->link(true)->category($cat->getUrlKey()), $cat->getName()
         ));
         $this->link()->route()->redirect();
      }


      $this->view()->formMove = $fMove;
      $this->view()->block = $block;
   }

   /* metody pro zpracování bloků */

   protected function createForm($block, Model_ORM_Record $blockRecord = null)
   {
      $form = new Form('block_edit');

      $elemBlockName = new Form_Element_Text('blockname', $this->tr('Název bloku'));
      $elemBlockName->addValidation(new Form_Validator_NotEmpty(null, Locales::getDefaultLang()));
      $elemBlockName->setLangs();
      if ($blockRecord) {
         $elemBlockName->setValues($blockRecord->{CustomBlocks_Model_Blocks::COLUMN_NAME});
      }
      $form->addElement($elemBlockName);

      foreach ($block['items'] as $index => $item) {
         // načtení uložených dat
         $modelName = $item['model'];
         $method = 'createFormElement' . substr($modelName, strrpos($modelName, '_') + 1);
         if (method_exists($this, $method) && class_exists($modelName)) {
            $name = isset($item['name'][Locales::getLang()]) ? $item['name'][Locales::getLang()] : reset($item['name']);

            $elementRecord = null;
            if ($blockRecord) {
               /* @var $model CustomBlocks_Model_Items */
               $model = new $modelName();

               $elementRecord = $model
                       ->where(CustomBlocks_Model_Items::COLUMN_ID_BLOCK . " = :idb AND " . CustomBlocks_Model_Items::COLUMN_INDEX . " = :ind", array('idb' => $blockRecord->getPK(), 'ind' => (string) $index))
                       ->record();

               if (!$elementRecord) {
                  $elementRecord = null;
               }
            }
            /* @var $customElement Form_Element */
            if(isset($item['element']) && $item['element'] instanceof Form_Element){
               $customElement = $item['element'];
            } else {
               $customElement = $this->$method($index, $name, $item, $elementRecord);
               if(isset($item['description']) && isset($item['description'][Locales::getLang()])){
                  $customElement->setSubLabel($item['description'][Locales::getLang()]);
               }
            }
               
            $form->addElement($customElement);
         }
      }

      $submit = new Form_Element_SaveCancelStay('save');
      $form->addElement($submit);

      if ($form->isSend() && $form->save->getValues() == false) {
         $this->link()->route()->redirect();
      }

      return $form;
   }

   protected function processForm($form, $blockData, $blockType, $idSotredBlock = null)
   {
      // uložení bloku
      $model = new CustomBlocks_Model_Blocks();
      if ($idSotredBlock == null) {
         $blockRecord = $model->newRecord();
         $blockRecord->{CustomBlocks_Model_Blocks::COLUMN_ID_CAT} = $this->category()->getId();
         $blockRecord->{CustomBlocks_Model_Blocks::COLUMN_TYPE} = $blockType;
      } else {
         $blockRecord = $model->record($idSotredBlock);
      }

      $blockRecord->{CustomBlocks_Model_Blocks::COLUMN_NAME} = $form->blockname->getValues();
      $blockRecord->save();

      foreach ($blockData['items'] as $index => $item) {
         // načtení uložených dat

         $method = 'processFormElement' . substr($item['model'], strrpos($item['model'], '_') + 1);
         if (method_exists($this, $method)) {
            $this->$method($index, $form, $blockRecord->getPK());
         }
      }
      return $blockRecord;
   }

   /* Metody pro zpracování bloků  */

   /**
    * Vytvoří element obrázku
    * @param int $index
    * @param string $name
    * @param array $blockItem -- informace o položce bloku
    * @param Model_ORM_Record $record
    * @return \Form_Element_File
    */
   protected function createFormElementImages($index, $name, $blockItem, CustomBlocks_Model_Images_Record $record = null)
   {
      $elemImg = new Form_Element_Image('img_' . $index, $name);
      $elemImg->setUploadDir($this->module()->getDataDir() . CustomBlocks_Model_Images::DIR_IMG);
      $elemImg->setOverWrite(false);
//      $elemImg->addValidation(new Form_Validator_FileExtension('jpg;png;bmp;gif'));
      if ($record) {
//         $src = Utils_Image::cache($record->getUrl($this->getModule()), 500, 50);
//         $elemImg->setSubLabel('<strong>' . $this->tr('Aktuálně') . ':</strong> <img src="' . $src . '" />');
         $elemImg->setImage($record->getPath($this->getModule()));
         $elemImg->setAllowDelete();
      }

      return $elemImg;
   }

   protected function processFormElementImages($index, Form $form, $idBlock)
   {
      $name = 'img_' . $index;
      if (!isset($form->$name)) {
         return;
      }

      $item = CustomBlocks_Model_Images::getItem($idBlock, $index);
      if (!$item) {
         $item = CustomBlocks_Model_Images::getNewRecord();
         $item->{CustomBlocks_Model_Items::COLUMN_ID_BLOCK} = $idBlock;
         $item->{CustomBlocks_Model_Items::COLUMN_INDEX} = $index;
      }
//      var_dump('name', $name, $form->$name->getValues() );
//      var_dump($form->$name->createFileObject());
//      die;
      if($form->$name->getValues() != null){
         $file = $form->$name->createFileObject();
         $item->{CustomBlocks_Model_Images::COLUMN_FILE} = $file->getName();
         $item->save();
      } else {
         if(!$item->isNew()){
            $m = new CustomBlocks_Model_Images();
            $m->delete($item);
         }
      }
   }

   protected function createFormElementGallery($index, $name, $blockItem, CustomBlocks_Model_Gallery_Record $record = null)
   {
      $elemImg = new Form_Element_ImagesUploader('img_' . $index, $name);
      $elemImg->setMaxFiles(50);
      $elemImg->setMaxFileSize(6*1024*1024);
//      Debug::log($record);
      $elemImg->addValidation(new Form_Validator_FileExtension('jpg;png;bmp;gif'));
      if ($record) {
         $elemImg->setOverWrite(false);
         // stačí nastavit upload dir a pak se to načte samo z adresáře. Asi není třeba předávat přes setValues do formu
         $elemImg->setUploadDir($record->getPath($this->module()));
         $elemImg->setValues($elemImg);
      }

      return $elemImg;
   }

   protected function processFormElementGallery($index, Form $form, $idBlock)
   {
      $name = 'img_' . $index;
      if (!isset($form->$name)) {
         return;
      }

      $items = CustomBlocks_Model_Gallery::getItems($idBlock, $index);
      $formImages = array();
      $formImagesTMP = $form->$name->getValues();
      if (!empty($formImagesTMP)) {
         foreach ($formImagesTMP as $img) {
            $formImages[$img['name']] = $img;
         }
      }
      // projdi uložené a smaž, které nejsou v odeslaných
      $path = CustomBlocks_Model_Gallery::getImagesPath($this->module(), $idBlock, $index);
      FS_Dir::checkStatic($path);
      if (empty($items)) {
         if (!empty($formImages)) {
            foreach ($formImages as $img) {
               // move file to datadir
               $img = new File($img);
               $img->move($path);
               $imgObj = CustomBlocks_Model_Gallery::getNewRecord();
               $imgObj->{CustomBlocks_Model_Items::COLUMN_ID_BLOCK} = $idBlock;
               $imgObj->{CustomBlocks_Model_Items::COLUMN_INDEX} = $index;
               $imgObj->{CustomBlocks_Model_Gallery::COLUMN_FILE} = $img->getName();
               $imgObj->save();
            }
         } else {
            // je prázdný i form i uložené. čili odebrat všechny záznamy, které patří k dan
         }
      } else {
         $model = new CustomBlocks_Model_Gallery(array('dir' => $path));
         
         if (!empty($formImages)) {
            // odebrání již uložených
            foreach ($items as $storedImg) {
               if (isset($formImages[$storedImg->{CustomBlocks_Model_Gallery::COLUMN_FILE}])) {
                  unset($formImages[$storedImg->{CustomBlocks_Model_Gallery::COLUMN_FILE}]);
               } else {
                  // není ve formu, odebrat z db
                  $model->delete($storedImg);
               }
            }
            // uložení nových obrázků
            foreach ($formImages as $img) {
               // move file to datadir
               $img = new File($img);
               $img->move($path);
               $imgObj = CustomBlocks_Model_Gallery::getNewRecord();
               $imgObj->{CustomBlocks_Model_Items::COLUMN_ID_BLOCK} = $idBlock;
               $imgObj->{CustomBlocks_Model_Items::COLUMN_INDEX} = $index;
               $imgObj->{CustomBlocks_Model_Gallery::COLUMN_FILE} = $img->getName();
               $imgObj->save();
            }
         } else {
            // vymazání db záznamů s obrázky
            $model->where(CustomBlocks_Model_Gallery::COLUMN_ID_BLOCK." = :idb AND ".CustomBlocks_Model_Gallery::COLUMN_INDEX." = :idi", 
                    array('idb' => $idBlock, 'idi' => (string)$index))
                    ->delete();
         }
      }
   }

   /**
    * Vytvoří element souboru (např ke stažení)
    * @param int $index
    * @param string $name
    * @param array $blockItem -- informace o položce bloku
    * @param Model_ORM_Record $record
    * @return \Form_Element_File
    */
   protected function createFormElementFiles($index, $name, $blockItem, CustomBlocks_Model_Files_Record $record = null)
   {
      $elemFile = new Form_Element_FileAdv('file_' . $index, $name);
      $elemFile->setUploadDir($this->module()->getDataDir(false) . CustomBlocks_Model_Files::DIR_FILES);
      $elemFile->setOverWrite(false);
      if ($record && $record->{CustomBlocks_Model_Files::COLUMN_FILE}) {
         $elemFile->setAllowDelete(true);
         $elemFile->setValues($record->getPath($this->getModule()));
      }

      return $elemFile;
   }

   protected function processFormElementFiles($index, Form $form, $idBlock)
   {
      $name = 'file_' . $index;
      if (!isset($form->$name)) {
         return;
      }

      $item = CustomBlocks_Model_Files::getItem($idBlock, $index);
      if (!$item) {
         $item = CustomBlocks_Model_Files::getNewRecord();
         $item->{CustomBlocks_Model_Items::COLUMN_ID_BLOCK} = $idBlock;
         $item->{CustomBlocks_Model_Items::COLUMN_INDEX} = $index;
      }
//      Debug::log($name, $form->$name->getValues());
      if($form->$name->getValues() != null){
         $file = $form->$name->createFileObject();
         $item->{CustomBlocks_Model_Files::COLUMN_FILE} = $file->getName();
      } else {
         if(!$item->isNew()){
            $m = new CustomBlocks_Model_Files();
            $m->delete($item);
         }
      }
      $item->save();
   }

   protected function createFormElementTexts($index, $name, $blockItem, Model_ORM_Record $record = null)
   {
      if (isset($blockItem['short']) && $blockItem['short'] == true) {
         $elem = new Form_Element_Text('txt_' . $index, $name);
      } else if (isset($blockItem['select']) && is_array($blockItem['select'])) {
         $elem = new Form_Element_Select('txt_' . $index, $name);
         $elem->setOptions($blockItem['select']);
      } else {
         $elem = new Form_Element_TextArea('txt_' . $index, $name);
      }

      if (!isset($blockItem['lang']) || $blockItem['lang'] == true) {
         $elem->setLangs();
      }

      if (isset($blockItem['select']) && is_array($blockItem['select'])) {
         $elem->setLangs(false);
      }
      if ($record) {
         if ($elem instanceof Form_Element_Select || !$elem->isMultiLang()) {
            $elem->setValues($record->{CustomBlocks_Model_Texts::COLUMN_CONTENT}[Locales::getDefaultLang()]);
         } else {
            $elem->setValues($record->{CustomBlocks_Model_Texts::COLUMN_CONTENT});
         }
      }
      return $elem;
   }

   protected function processFormElementTexts($index, Form $form, $idBlock)
   {
      $name = 'txt_' . $index;
      if (!isset($form->$name)) {
         return;
      }

      $item = CustomBlocks_Model_Texts::getItem($idBlock, $index);
      if (!$item) {
         $item = CustomBlocks_Model_Texts::getNewRecord();
         $item->{CustomBlocks_Model_Items::COLUMN_ID_BLOCK} = $idBlock;
         $item->{CustomBlocks_Model_Items::COLUMN_INDEX} = $index;
      }
      if ($form->$name instanceof Form_Element_Select || !$form->$name->isMultiLang()) {
         $item->{CustomBlocks_Model_Texts::COLUMN_CONTENT}[Locales::getDefaultLang()] = $form->$name->getValues();
      } else {
         $item->{CustomBlocks_Model_Texts::COLUMN_CONTENT} = $form->$name->getValues();
      }
      $item->save();
   }

   protected function createFormElementVideos($index, $name, $blockItem, Model_ORM_Record $record = null)
   {
      $elem = new Form_Element_Text('videourl_' . $index, $name);
      $elem->addValidation(new Form_Validator_Url());
      if ($record) {
         $elem->setValues($record->{CustomBlocks_Model_Videos::COLUMN_URL});
      }
      return $elem;
   }

   protected function processFormElementVideos($index, Form $form, $idBlock)
   {
      $name = 'videourl_' . $index;
      if (!isset($form->$name)) {
         return;
      }

      $item = CustomBlocks_Model_Videos::getItem($idBlock, $index);
      if (!$item) {
         $item = CustomBlocks_Model_Videos::getNewRecord();
         $item->{CustomBlocks_Model_Items::COLUMN_ID_BLOCK} = $idBlock;
         $item->{CustomBlocks_Model_Items::COLUMN_INDEX} = $index;
      }
      $item->{CustomBlocks_Model_Videos::COLUMN_URL} = $form->$name->getValues();
      $item->save();
   }

   protected function createFormElementEmbeds($index, $name, $blockItem, Model_ORM_Record $record = null)
   {
      $elem = new Form_Element_TextArea('embed_' . $index, $name);
      if ($record) {
         $elem->setValues($record->{CustomBlocks_Model_Embeds::COLUMN_CONTENT});
      }
      return $elem;
   }

   protected function processFormElementEmbeds($index, Form $form, $idBlock)
   {
      $name = 'embed_' . $index;
      if (!isset($form->$name)) {
         return;
      }

      $item = CustomBlocks_Model_Embeds::getItem($idBlock, $index);
      if (!$item) {
         $item = CustomBlocks_Model_Embeds::getNewRecord();
         $item->{CustomBlocks_Model_Items::COLUMN_ID_BLOCK} = $idBlock;
         $item->{CustomBlocks_Model_Items::COLUMN_INDEX} = $index;
      }
      $item->{CustomBlocks_Model_Embeds::COLUMN_CONTENT} = $form->$name->getValues();
      $item->save();
   }

}
