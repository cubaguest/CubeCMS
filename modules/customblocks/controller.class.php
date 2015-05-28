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
      if(empty($tplBlocks)){
         throw new InvalidArgumentException($this->tr('Šablona nemá definovány bloky. Kontaktujte webmastera.'));
      }

      $this->checkDeleteBlock($tplBlocks);
      // načtení jednodlivých bloků a jejich obsahů

      $blocks = CustomBlocks_Model_Blocks::getBlocks($this->category()->getId());
      if($blocks){
         $loadfromModels = array();
         $blocksIds = array();

         foreach ($blocks as &$block) {
            if(isset($tplBlocks[$block->{CustomBlocks_Model_Blocks::COLUMN_TYPE}])){
               $blocksIds[] = $block->getPK();
               $block->block_strunct = $tplBlocks[$block->{CustomBlocks_Model_Blocks::COLUMN_TYPE}];
               $block->block_name = $tplBlocks[$block->{CustomBlocks_Model_Blocks::COLUMN_TYPE}]['name'];
               $block->block_tpl = $tplBlocks[$block->{CustomBlocks_Model_Blocks::COLUMN_TYPE}]['template'];
               $block->block_items = array();

               // projití a vybrání modelů
               foreach ($block->block_strunct['items'] as $tplBlockItem) {
                  $loadfromModels[] = $tplBlockItem['model'];
               }

            }
         }
         // odstranění duplicit aby se nenačítalo z jednoho modelu několikrát
         $loadfromModels = array_unique($loadfromModels);

         // načtení dat z jednotlivých modelů pohromadě, čímž se dosáhne pouze páru dotazů na db
         $blocksData = array();
         foreach ($loadfromModels as $modelName) {
            /* @var $model CustomBlocks_Model_Items */
            $model = new $modelName();
            $blocksData = array_merge($blocksData, 
                $model
                  ->where(CustomBlocks_Model_Items::COLUMN_ID_BLOCK." IN (".$model->getWhereINPlaceholders($blocksIds).")", 
                     $model->getWhereINValues($blocksIds))
                  ->records());
         }

         // zařazení dat do bloků pod svoje indexy
         foreach ($blocks as &$block) {
            foreach ($blocksData as $key => $data) {
               if($block->getPK() == $data->{CustomBlocks_Model_Items::COLUMN_ID_BLOCK}){
                  $block->block_items[$data->{CustomBlocks_Model_Items::COLUMN_INDEX}] = clone $data;
                  // není třeba aby byl dále použit
                  unset($blocksData[$key]);
               }
            }
         }

//         foreach ($blocks as $block) {
//            Debug::log($block->block_items);
//         }

      }
      $this->view()->blocks = $blocks;
   }
   
   protected function checkDeleteBlock($tplBlocks)
   {
      if(!$this->rights()->isWritable()){
         return;
      }
      
      $fDelete = new Form('block_delete_');
      
      $eId = new Form_Element_Hidden('id');
      $fDelete->addElement($eId);
      
      $eSubmit = new Form_Element_Submit('delete', $this->tr('Smazat blok'));
      $fDelete->addElement($eSubmit);
      
      if($fDelete->isValid()){
         $modelBlocks = new CustomBlocks_Model_Blocks();
         $block = $modelBlocks->record($fDelete->id->getValues());
         
         if($block && isset($tplBlocks[$block->{CustomBlocks_Model_Blocks::COLUMN_TYPE}])){
            foreach ($tplBlocks[$block->{CustomBlocks_Model_Blocks::COLUMN_TYPE}]['items'] as $index => $item) {
               /* @var $model CustomBlocks_Model_Items */
               $model = new $item['model'](array('dir' => $this->module()->getDataDir()));
               
               $item = $model
                   ->where(CustomBlocks_Model_Items::COLUMN_ID_BLOCK." = :idb AND ".CustomBlocks_Model_Items::COLUMN_INDEX." = :ind",
                       array('idb' => $block->getPK(), 'ind' => $index))
                   ->record();
               if($item){
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
         if(!isset($block['img'])){
            $block['img'] = pathinfo($block['template'], PATHINFO_FILENAME).'.jpg';
         }
         if(is_file(Face::getCurrent()->getDir().'modules/'.$this->module()->getName().'/images/'.$block['img'])){
            $img = Face::getCurrent()->getDir().'modules/'.$this->module()->getName().'/images/'.$block['img'];
         } else if(is_file($this->module()->getLibDir().'images'.DIRECTORY_SEPARATOR.$block['img'])){
            $img = $this->module()->getLibDir().'images'.DIRECTORY_SEPARATOR.$block['img'];
         }
         
         $blocksData[] = array(
             'name' => isset($block['name'][Locales::getLang()]) ? $block['name'][Locales::getLang()] : reset($block['name']),
             'img' =>  $img,
             'url' => $this->link()->route('addBlock', array('type' => $type)),
         );
      }
      
      $this->view()->blocks = $blocksData;
   }
   
   public function addBlockController($type)
   {
      $this->checkWritebleRights();
      $blocks = $this->view()->getCurrentTemplateParam('blocks');
      
      if(!isset($blocks[$type])){
         throw new InvalidArgumentException($this->tr('Nebyl předán podporovaný typ bloku'));
      }
      $block = $blocks[$type];
      
      $form = $this->createForm($block);
      
      if($form->isValid()){
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
      if(!$blockRecord || !isset($blocks[$blockRecord->{CustomBlocks_Model_Blocks::COLUMN_TYPE}])){
         throw new UnexpectedPageException();
      }
      
      $block = $blocks[$blockRecord->{CustomBlocks_Model_Blocks::COLUMN_TYPE}];
      $form = $this->createForm($block, $blockRecord);
      
      if($form->isValid()){
         $this->processForm($form, $block, $this->getRequest('type'), $blockRecord->getPK());
         
         $this->log(sprintf('Upraven volitelný blok %s', $blockRecord->getPK()));
         $this->infoMsg()->addMessage($this->tr('Blok byl Uložen'));
         $this->link()->route()->redirect();
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

      if($form->isSend() && $form->save->getValues() == false){
         $this->link()->route()->redirect();
      }
      
      if($form->isValid()){
         $ids = $form->id->getValues();
         $model = new CustomBlocks_Model_Blocks();
         
         $stmt = $model->query("UPDATE {THIS} SET `".TextBlocks_Model::COLUMN_ORDER."` = :ord WHERE ".TextBlocks_Model::COLUMN_ID." = :id");
         foreach ($ids as $index => $id) {
            CustomBlocks_Model_Blocks::setRecordPosition($id, $index+1);
         }
         
         $this->infoMsg()->addMessage($this->tr('Pořadí bylo uloženo'));
         $this->link()->route()->redirect();
      }
      
      $this->view()->blocks = $blocks;
      $this->view()->form = $form;
      $this->view()->blocks = $blocks;
   }
   
   
   /* metody pro zpracování bloků */
   
   protected function createForm($block, Model_ORM_Record $blockRecord = null)
   {
      $form = new Form('block_edit');
      
      $elemBlockName = new Form_Element_Text('blockname', $this->tr('Název bloku'));
      $elemBlockName->addValidation(new Form_Validator_NotEmpty());
      $elemBlockName->setLangs();
      if($blockRecord){
         $elemBlockName->setValues($blockRecord->{CustomBlocks_Model_Blocks::COLUMN_NAME});
      }
      $form->addElement($elemBlockName);
      
      foreach ($block['items'] as $index => $item) {
         // načtení uložených dat
         $modelName = $item['model'];
         $method = 'createFormElement'.substr($modelName, strrpos($modelName, '_')+1);
         if(method_exists($this, $method) && class_exists($modelName)){
            $name = isset($item['name'][Locales::getLang()]) ? $item['name'][Locales::getLang()] : reset($item['name']);
            
            $elementRecord = null;
            if($blockRecord){
               /* @var $model CustomBlocks_Model_Items */
               $model = new $modelName();
               
               $elementRecord = $model
                   ->where(CustomBlocks_Model_Items::COLUMN_ID_BLOCK." = :idb AND ".CustomBlocks_Model_Items::COLUMN_INDEX." = :ind",
                       array('idb' => $blockRecord->getPK(), 'ind' => $index) )
                   ->record();
               if(!$elementRecord){
                  $elementRecord = null;
               }
            }
            $customElement = $this->$method($index, $name, $item, $elementRecord);
            $form->addElement($customElement);
         }
      }
      
      $submit = new Form_Element_SaveCancel('save');
      $form->addElement($submit);
      
      if($form->isSend() && $form->save->getValues() == false){
         $this->link()->route()->redirect();
      }
      
      return $form;
   }
   
   protected function processForm($form, $blockData, $blockType, $idSotredBlock = null)
   {
      // uložení bloku
      $model = new CustomBlocks_Model_Blocks();
      if($idSotredBlock == null){
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
         
         $method = 'processFormElement'.substr($item['model'], strrpos($item['model'], '_')+1);
         if(method_exists($this, $method)){
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
      $elemImg = new Form_Element_File('img_'.$index, $name);
      $elemImg->setUploadDir($this->module()->getDataDir().CustomBlocks_Model_Images::DIR_IMG);
      $elemImg->setOverWrite(false);
      $elemImg->addValidation(new Form_Validator_FileExtension('jpg;png;bmp;gif'));
      if($record){
         $src = Utils_Image::cache($record->getUrl($this->getModule()), 500, 50);
         $elemImg->setSubLabel('<strong>'.$this->tr('Aktuálně').':</strong> <img src="'.$src.'" />');
      }
      
      return $elemImg;
   }
   
   protected function processFormElementImages($index, Form $form, $idBlock)
   {
      $name = 'img_'.$index;
      if(!isset($form->$name) || $form->$name->getValues() == null){
         return;
      }
      
      $item = CustomBlocks_Model_Images::getItem($idBlock, $index);
      if(!$item){
         $item = CustomBlocks_Model_Images::getNewRecord();
         $item->{CustomBlocks_Model_Items::COLUMN_ID_BLOCK} = $idBlock;
         $item->{CustomBlocks_Model_Items::COLUMN_INDEX} = $index;
      }
      
      $file = $form->$name->createFileObject();
      $item->{CustomBlocks_Model_Images::COLUMN_FILE} = $file->getName();
      $item->save();
   }
   
   protected function createFormElementTexts($index, $name, $blockItem, Model_ORM_Record $record = null)
   {
      $elem = new Form_Element_TextArea('txt_'.$index, $name);
      $elem->setLangs();
      if($record){
         $elem->setValues($record->{CustomBlocks_Model_Texts::COLUMN_CONTENT});
      }
      return $elem;
   }
   
   protected function processFormElementTexts($index, Form $form, $idBlock)
   {
      $name = 'txt_'.$index;
      if(!isset($form->$name)){
         return;
      }
      
      $item = CustomBlocks_Model_Texts::getItem($idBlock, $index);
      if(!$item){
         $item = CustomBlocks_Model_Texts::getNewRecord();
         $item->{CustomBlocks_Model_Items::COLUMN_ID_BLOCK} = $idBlock;
         $item->{CustomBlocks_Model_Items::COLUMN_INDEX} = $index;
      }
      $item->{CustomBlocks_Model_Texts::COLUMN_CONTENT} = $form->$name->getValues();
      $item->save();
   }
   
   protected function createFormElementVideos($index, $name, $blockItem, Model_ORM_Record $record = null)
   {
      $elem = new Form_Element_Text('videourl_'.$index, $name);
      $elem->addValidation(new Form_Validator_Url());
      if($record){
         $elem->setValues($record->{CustomBlocks_Model_Videos::COLUMN_URL});
      }
      return $elem;
   }
   
   protected function processFormElementVideos($index, Form $form, $idBlock)
   {
      $name = 'videourl_'.$index;
      if(!isset($form->$name)){
         return;
      }
      
      $item = CustomBlocks_Model_Videos::getItem($idBlock, $index);
      if(!$item){
         $item = CustomBlocks_Model_Videos::getNewRecord();
         $item->{CustomBlocks_Model_Items::COLUMN_ID_BLOCK} = $idBlock;
         $item->{CustomBlocks_Model_Items::COLUMN_INDEX} = $index;
      }
      $item->{CustomBlocks_Model_Videos::COLUMN_URL} = $form->$name->getValues();
      $item->save();
   }
   
   protected function createFormElementEmbeds($index, $name, $blockItem, Model_ORM_Record $record = null)
   {
      $elem = new Form_Element_TextArea('embed_'.$index, $name);
      if($record){
         $elem->setValues($record->{CustomBlocks_Model_Embeds::COLUMN_CONTENT});
      }
      return $elem;
   }
   
   protected function processFormElementEmbeds($index, Form $form, $idBlock)
   {
      $name = 'embed_'.$index;
      if(!isset($form->$name)){
         return;
      }
      
      $item = CustomBlocks_Model_Embeds::getItem($idBlock, $index);
      if(!$item){
         $item = CustomBlocks_Model_Embeds::getNewRecord();
         $item->{CustomBlocks_Model_Items::COLUMN_ID_BLOCK} = $idBlock;
         $item->{CustomBlocks_Model_Items::COLUMN_INDEX} = $index;
      }
      $item->{CustomBlocks_Model_Embeds::COLUMN_CONTENT} = $form->$name->getValues();
      $item->save();
   }
}