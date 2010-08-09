<?php
class TitlePage_Controller extends Controller {
   const ITEM_TYPE_TEXT = 'text';
   const ITEM_TYPE_MENU = 'menu';
   const ITEM_TYPE_VIDEO = 'video';
   const ITEM_TYPE_ARTICLE = 'articles';
   const ITEM_TYPE_NEWS = 'news';

      /**
    * Kontroler pro zobrazení novinek
    */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();
      $this->loadItemsList();
   }

   public function editListController() {
      $this->checkWritebleRights();
      $this->loadItemsList();
   }

   private function loadItemsList() {
      $modelI = new TitlePage_Model_Items();

      $items = $modelI->getItems();

      $itemsList = array();
      foreach ($items as $item) {
         $link = $title = $image = $nameCat = null;
         $linkCat = $this->link()->clear();
         $name = unserialize($item->{TitlePage_Model_Items::COLUMN_NAME});
         $name = $name[Locales::getLang()];
         switch ($item->{TitlePage_Model_Items::COLUMN_TYPE}) {
            case self::ITEM_TYPE_TEXT:
            case self::ITEM_TYPE_MENU:
               $data = unserialize($item->{TitlePage_Model_Items::COLUMN_DATA});
               $data = $data[Locales::getLang()];
               if(empty ($data)) continue;
               if($item->{TitlePage_Model_Items::COLUMN_IMAGE} != null) {
                  $image = $item->{TitlePage_Model_Items::COLUMN_IMAGE};
               }
               break;
            case self::ITEM_TYPE_VIDEO:
               $data = $item->{TitlePage_Model_Items::COLUMN_DATA};
               if(empty ($data)) continue;
               break;
            case self::ITEM_TYPE_ARTICLE:
            case self::ITEM_TYPE_NEWS:
               if($item->{TitlePage_Model_Items::COLUMN_ID_EXTERN} != 0){ // aktuální
                  $modelArticles = new Articles_Model_Detail();
                  $article = $modelArticles->getArticleById($item->{TitlePage_Model_Items::COLUMN_ID_EXTERN});
               } else { // vybraný
                  $modelArticles = new Articles_Model_List();
                  $article = $modelArticles->getList($item->{TitlePage_Model_Items::COLUMN_ID_CATEGORY}, 0, 1);
                  $article = $article->fetch();
               }

               $modelCat = new Model_Category();
               $cat = $modelCat->getCategoryById($item->{TitlePage_Model_Items::COLUMN_ID_CATEGORY});

               $link = new Url_Link_Module(true);
               $link->setModuleRoutes(new Articles_Routes(AppCore::getUrlRequest()));

               $linkCat = $link->category($cat->{Model_Category::COLUMN_URLKEY});
               $link = $linkCat->route('detail', array('urlkey' => $article->{Articles_Model_Detail::COLUMN_URLKEY}));

               if((string)$article->{Articles_Model_Detail::COLUMN_ANNOTATION} != null){
                  $data = $article->{Articles_Model_Detail::COLUMN_ANNOTATION};
               } else {
                  $data = strip_tags($article->{Articles_Model_Detail::COLUMN_TEXT}, '<a><strong><em>');
               }

               $title = $article->{Articles_Model_Detail::COLUMN_NAME};
               $nameCat = $cat->{Model_Category::COLUMN_CAT_LABEL};

               if(empty ($data)) continue;
               break;
            default:
               break;
         }


         array_push($itemsList, array('itemObj' => $item,
                                      'id' => $item->{TitlePage_Model_Items::COLUMN_ID},
                                      'type' => $item->{TitlePage_Model_Items::COLUMN_TYPE},
                                      'name' => $name,
                                      'nameCat' => $nameCat,
                                      'title' => $title,
                                      'image' => $image,
                                      'data' => $data,
                                      'cols' => $item->{TitlePage_Model_Items::COLUMN_COLUMNS},
                                      'link' => (string)$link,
                                      'linkCat' => (string)$linkCat,
                                      ));
      }

      $this->view()->list = $itemsList;
   }

   public function addSelectItemController() {
      $this->checkWritebleRights();

      $addItems = array(
         self::ITEM_TYPE_TEXT => array(
            'link' => $this->link()->route('addItem', array('type' => self::ITEM_TYPE_TEXT)),
            'name' => $this->_('Text'),
            'title' => $this->_('Přidání textové položky'),
            'desc' => $this->_('Přidání textové položky s obrázkem')
         ),
         self::ITEM_TYPE_MENU => array(
            'link' => $this->link()->route('addItem', array('type' => self::ITEM_TYPE_MENU)),
            'name' => $this->_('Menu'),
            'title' => $this->_('Přidání položky s menu'),
            'desc' => $this->_('Přidání položky s menu. (např. podmenu nebo rychlá navigace)')
         ),
         self::ITEM_TYPE_VIDEO => array(
            'link' => $this->link()->route('addItem', array('type' => self::ITEM_TYPE_VIDEO)),
            'name' => $this->_('Video'),
            'title' => $this->_('Přidání položky s videem'),
            'desc' => $this->_('Přidání položky s videem z některého z video serverů (např. Youtube)')
         ),
         self::ITEM_TYPE_ARTICLE => array(
            'link' => $this->link()->route('addItem', array('type' => self::ITEM_TYPE_ARTICLE)),
            'name' => $this->_('Článek'),
            'title' => $this->_('Přidání položky s článkem'),
            'desc' => $this->_('Přidání položky s existujícím článkem v systému')
         ),
         self::ITEM_TYPE_NEWS => array(
            'link' => $this->link()->route('addItem', array('type' => self::ITEM_TYPE_NEWS)),
            'name' => $this->_('Novinka'),
            'title' => $this->_('Přidání položky a novinkou'),
            'desc' => $this->_('Přidání položky s existující novinkou v systému')
         )
      );

      $this->view()->addItems = $addItems;
   }
   public function addItemController() {
      $this->checkWritebleRights();
      switch ($this->getRequest('type')) {
         case self::ITEM_TYPE_TEXT:
            $this->editItemTextCtrl();
            break;
         case self::ITEM_TYPE_VIDEO:
            $this->editItemVideoCtrl();
            break;
         case self::ITEM_TYPE_MENU:
            $this->editItemMenuCtrl();
            break;
         case self::ITEM_TYPE_ARTICLE:
            $this->editItemArticleCtrl(null, self::ITEM_TYPE_ARTICLE);
            break;
         case self::ITEM_TYPE_NEWS:
            $this->editItemArticleCtrl(null, self::ITEM_TYPE_NEWS);
            break;
         default:
            return false;
            break;
      }
   }

   public function editItemController() {
      $this->checkWritebleRights();
      $modelItem = new TitlePage_Model_Items();
      $item = $modelItem->getItem($this->getRequest('id'));
      switch ($item->{TitlePage_Model_Items::COLUMN_TYPE}) {
         case self::ITEM_TYPE_TEXT:
            $this->editItemTextCtrl($item);
            break;
         case self::ITEM_TYPE_VIDEO:
            $this->editItemVideoCtrl($item);
            break;
         case self::ITEM_TYPE_MENU:
            $this->editItemMenuCtrl($item);
            break;
         case self::ITEM_TYPE_ARTICLE:
            $this->editItemArticleCtrl($item, self::ITEM_TYPE_ARTICLE);
            break;
         case self::ITEM_TYPE_NEWS:
            $this->editItemArticleCtrl($item, self::ITEM_TYPE_NEWS);
            break;
         default:
            return false;
            break;
      }

   }

   // kontroler pro editaci textu
   private function editItemTextCtrl($item = null){
      $form = new Form('text_item_');

      $this->view()->type = self::ITEM_TYPE_TEXT; // typ editace


      $elemName = new Form_Element_Text('name', $this->_('Název'));
      $elemName->setLangs();
      $form->addElement($elemName);

      $elemText = new Form_Element_TextArea('text', $this->_('Text'));
      $elemText->setLangs();
      $elemText->addValidation(new Form_Validator_NotEmpty(null, Locales::getDefaultLang()));
      $form->addElement($elemText);

      $elemNumColumns = new Form_Element_Text('columns', $this->_('Počet sloupců'));
      $elemNumColumns->setValues(1);
      $elemNumColumns->addValidation(new Form_Validator_IsNumber());
      $elemNumColumns->addValidation(new Form_Validator_NotEmpty());
      $form->addElement($elemNumColumns);

      $elemImage = new Form_Element_File('image', $this->_('Obrázek'));
      $elemImage->addValidation(New Form_Validator_FileExtension(array('jpg', 'png')));
      $elemImage->setUploadDir($this->category()->getModule()->getDataDir());
      $form->addElement($elemImage);
      
      $id = null;
      if($item != null){
         $id=$item->{TitlePage_Model_Items::COLUMN_ID};
         $form->name->setValues(unserialize($item->{TitlePage_Model_Items::COLUMN_NAME}));
         $form->text->setValues(unserialize($item->{TitlePage_Model_Items::COLUMN_DATA}));
         $form->columns->setValues($item->{TitlePage_Model_Items::COLUMN_COLUMNS});
         // pokud je obrázek přidáme element s checkboxem pro odstranění
         if($item->{TitlePage_Model_Items::COLUMN_IMAGE} != null){
            $elemDelImg = new Form_Element_Checkbox('delimg', $this->_('Smazat obrázek'));
            $elemDelImg->setSubLabel(sprintf($this->_('Uložen obrázek: %s'),$item->{TitlePage_Model_Items::COLUMN_IMAGE}));
            $form->addElement($elemDelImg);
         }
      }

      $elemSubmit = new Form_Element_Submit('save', $this->_('Uložit'));
      $form->addElement($elemSubmit);

      if($form->isValid()){
         $image = null;
         // mazání
         if($form->image->getValues() != null OR ($form->haveElement('delimg') AND $form->delimg->getValues() == true)){
            $file = new Filesystem_File($item->{TitlePage_Model_Items::COLUMN_IMAGE}, $this->category()->getModule()->getDataDir());
            $file->delete();
            $image = null;
            unset ($file);
         }
         // ulož nový
         if($form->image->getValues() != null){
            $imgObj = $form->image->createFileObject('Filesystem_File_Image');
            // není resize
//            $imgObj = new Filesystem_File_Image();
            $image = $imgObj->getName();
            unset ($imgObj);
         }
   
         $modelItems = new TitlePage_Model_Items();
         // ulož
         $modelItems->saveItem(self::ITEM_TYPE_TEXT, serialize($form->text->getValues()),
                 $this->category()->getId(), serialize($form->name->getValues()),
                 $image, $form->columns->getValues(),$id);

         $this->infoMsg()->addMessage($this->_('Položka byla uložena'));
         $this->link()->route('editList')->reload();
      }
      $this->view()->form = $form;
   }
   
   // kontroler pro editaci videa
   private function editItemVideoCtrl($item = null){
      $form = new Form('text_item_');

      $this->view()->type = self::ITEM_TYPE_VIDEO; // typ editace
      $modelItems = new TitlePage_Model_Items();

      $elemName = new Form_Element_Text('name', $this->_('Název'));
      $elemName->setLangs();
      $form->addElement($elemName);

      $elemVideoObj = new Form_Element_TextArea('videoObj', $this->_('Objekt videa'));
      $elemVideoObj->addValidation(new Form_Validator_NotEmpty(null, Locales::getDefaultLang()));
      $elemVideoObj->setSubLabel($this->_('Pouze element <object> a jeho obsah'));
      $form->addElement($elemVideoObj);

      $elemNumColumns = new Form_Element_Text('columns', $this->_('Počet sloupců'));
      $elemNumColumns->setValues(1);
      $elemNumColumns->addValidation(new Form_Validator_IsNumber());
      $elemNumColumns->addValidation(new Form_Validator_NotEmpty());
      $form->addElement($elemNumColumns);

      $elemSubmit = new Form_Element_Submit('save', $this->_('Uložit'));
      $form->addElement($elemSubmit);

      $id = null;
      if($item != null){
         $id = $item->{TitlePage_Model_Items::COLUMN_ID};
         $form->videoObj->setValues($item->{TitlePage_Model_Items::COLUMN_DATA});
         $form->name->setValues(unserialize($item->{TitlePage_Model_Items::COLUMN_NAME}));
         $form->columns->setValues($item->{TitlePage_Model_Items::COLUMN_COLUMNS});
      }

      if($form->isValid()){
         // ulož
         $modelItems->saveItem(self::ITEM_TYPE_VIDEO, strip_tags($form->videoObj->getValues(),'<object><param>'),
                 $this->category()->getId(), serialize($form->name->getValues()),
                 $image, $form->columns->getValues(),$id);

         $this->infoMsg()->addMessage($this->_('Položka byla uložena'));
         $this->link()->route('editList')->reload();
      }
      $this->view()->form = $form;
   }

   // kontroler pro editaci menu
   private function editItemMenuCtrl($item = null){
      $form = new Form('text_item_');

      $this->view()->type = self::ITEM_TYPE_MENU; // typ editace

      $modelItems = new TitlePage_Model_Items();

      $elemName = new Form_Element_Text('name', $this->_('Název'));
      $elemName->setLangs();
      $form->addElement($elemName);

      $elemText = new Form_Element_TextArea('menu', $this->_('Odkazy'));
      $elemText->setSubLabel($this->_('Seznam odkazů. Může mít i více úrovní. (odkazy uzavřené v tagu "li")'));
      $elemText->setLangs();
      $elemText->addValidation(new Form_Validator_NotEmpty(null, Locales::getDefaultLang()));
      $form->addElement($elemText);

      $elemNumColumns = new Form_Element_Text('columns', $this->_('Počet sloupců'));
      $elemNumColumns->setValues(1);
      $elemNumColumns->addValidation(new Form_Validator_IsNumber());
      $elemNumColumns->addValidation(new Form_Validator_NotEmpty());
      $form->addElement($elemNumColumns);

      $elemSubmit = new Form_Element_Submit('save', $this->_('Uložit'));
      $form->addElement($elemSubmit);

      $id = null;
      if($item != null){
         $id = $item->{TitlePage_Model_Items::COLUMN_ID};
         $form->name->setValues(unserialize($item->{TitlePage_Model_Items::COLUMN_NAME}));
         $form->menu->setValues(unserialize($item->{TitlePage_Model_Items::COLUMN_DATA}));
         $form->columns->setValues($item->{TitlePage_Model_Items::COLUMN_COLUMNS});
      }

      if($form->menu->getValues() == null){
         $defArray = array();
         foreach (Locales::getAppLangs() as $lang) {
            $defArray[$lang] = '<ul><li><a href="" title="">Link</a></li></ul>';
         }
         $form->menu->setValues($defArray);
      }

      if($form->isValid()){
         // výběr odkazů
//         $menus = $form->menu->getValues();

//         $links = array();
//         foreach (Locales::getAppLangs() as $key => $value) {
//            $links[$value] = array();
//         }

//         foreach ($menus as $lang => $value) {
//            $matches = array();
//            preg_match_all('/<a href=\"([^\"]*)\">(.*)<\/a>/iU', $value, $matches);
//            foreach ($matches[0] as $key => $match) {
//               array_push($links[$lang], array('name' => $matches[2][$key], 'url' => $matches[1][$key]));
//            }
//         }
//         var_dump($links);
//         var_dump($matches);
//         flush();
         $modelItems->saveItem(self::ITEM_TYPE_MENU, serialize($form->menu->getValues()),
                 $this->category()->getId(), serialize($form->name->getValues()),
                 $image, $form->columns->getValues(),$id);

         $this->infoMsg()->addMessage($this->_('Položka byla uložena'));
         $this->link()->route('editList')->reload();

         // ulož
      }
      $this->view()->form = $form;
   }

   private function editItemArticleCtrl($item = null, $type = self::ITEM_TYPE_ARTICLE) {
      $form = new Form('article_item_');

      switch ($type) {
         case self::ITEM_TYPE_NEWS:
            $module = 'news';
            break;
         case self::ITEM_TYPE_ARTICLE:
         default:
            $module = 'articles';
            break;
      }
      $this->view()->type = self::ITEM_TYPE_ARTICLE; // typ editace
      $modelItems = new TitlePage_Model_Items();

      $elemName = new Form_Element_Text('name', $this->_('Název'));
      $elemName->setLangs();
      $form->addElement($elemName);

      $elemCategory = new Form_Element_Select('category_id', $this->_('Kategorie'));
      $elemCategory->addValidation(new Form_Validator_IsNumber());
      $elemCategory->addValidation(new Form_Validator_NotEmpty());
      $modelCat = new Model_Category();
      $cats = $modelCat->getCategoryListByModule($module);
      if($cats == false){
         $this->errMsg()->addMessage($this->_('Není vytvořena žádná kategorie s články'));
      }
      $cats = $cats->fetchAll();
      foreach ($cats as $cat) {
         $elemCategory->setOptions(array(vve_tpl_truncate((string)$cat->{Model_Category::COLUMN_CAT_LABEL},100) => $cat->{Model_Category::COLUMN_CAT_ID}),true);
      }
      $form->addElement($elemCategory);

      $elemArticle = new Form_Element_Select('article_id', $this->_('Článek'));
      $elemArticle->addValidation(new Form_Validator_IsNumber());
      $elemArticle->addValidation(new Form_Validator_NotEmpty());

      if($item != null){
         $arts = $this->getArticles($item->{TitlePage_Model_Items::COLUMN_ID_CATEGORY});
      } else {
         $arts = $this->getArticles(reset($cats)->{Model_Category::COLUMN_CAT_ID});
      }
      $elemArticle->setOptions(array($this->_('Aktuální - naposledy přidaný') => 0),true);
      foreach ($arts as $art) {
         $elemArticle->setOptions(array(vve_tpl_truncate((string)$art->{Articles_Model_Detail::COLUMN_NAME},100) => $art->{Articles_Model_Detail::COLUMN_ID}),true);
      }
      $form->addElement($elemArticle);

      $elemNumColumns = new Form_Element_Text('columns', $this->_('Počet sloupců'));
      $elemNumColumns->setValues(1);
      $elemNumColumns->addValidation(new Form_Validator_IsNumber());
      $elemNumColumns->addValidation(new Form_Validator_NotEmpty());
      $form->addElement($elemNumColumns);

      $elemSubmit = new Form_Element_Submit('save', $this->_('Uložit'));
      $form->addElement($elemSubmit);

      $id = null;
      if($item != null){
         $id = $item->{TitlePage_Model_Items::COLUMN_ID};
         $form->category_id->setValues($item->{TitlePage_Model_Items::COLUMN_ID_CATEGORY});
         $form->article_id->setValues($item->{TitlePage_Model_Items::COLUMN_ID_EXTERN});
      }

      if($form->isValid()){
         // ulož
         $modelItems->saveItem($type, null,
                 $form->category_id->getValues(), serialize($form->name->getValues()),
                 $image, $form->columns->getValues(),$id, $form->article_id->getValues());

         $this->infoMsg()->addMessage($this->_('Položka byla uložena'));
         $this->link()->route('editList')->reload();
      }
      $this->view()->form = $form;
   }

   private function getArticles($idCat) {
      $model = new Articles_Model_List();
      $articles = $model->getList($idCat, 0, 100);
      return $articles->fetchAll();
   }

   public function getArticlesListController() {
      $this->checkWritebleRights();
      $result = array();
      switch ($this->getRequestParam('type')) {
         case self::ITEM_TYPE_ARTICLE:
         case self::ITEM_TYPE_NEWS:
            $modelArticles = new Articles_Model_List();
            $articles = $this->getArticles($this->getRequestParam('idc'));
            foreach ($articles as $art) {
               array_push($result, array('id' => $art->{Articles_Model_Detail::COLUMN_ID},
                       'text' => vve_tpl_truncate((string)$art->{Articles_Model_Detail::COLUMN_NAME},100)));
            }
            break;
         default:
            $this->errMsg()->addMessage($this->_('Nepodporovaný typ seznamu'));
            break;
      }

      $this->view()->list = (array)$result;
   }

   public function changePositionController() {
      $this->checkWritebleRights();

      $modelItems = new TitlePage_Model_Items();
      
      $modelItems->setPositions($this->getRequestParam('item'));
      $this->infoMsg()->addMessage($this->_('Pozice byla uložena'));
   }

   public function deleteItemController() {
      $this->checkWritebleRights();
      $modelItems = new TitlePage_Model_Items();
      $modelItems->deleteItem($this->getRequestParam('delete_id'));
      $this->infoMsg()->addMessage($this->_('Prvek byl smazán'));
      $this->link()->route('editList')->reload();
   }

   /**
    * Smazání článků při odstranění kategorie
    * @param Category $category
    */
   public static function clearOnRemove(Category $category) {
   }

   /**
    * Metoda pro nastavení modulu
    */
   public static function settingsController(&$settings,Form &$form) {
   }
}
?>