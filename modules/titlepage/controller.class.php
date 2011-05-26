<?php
class TitlePage_Controller extends Controller {
   const ITEM_TYPE_TEXT = 'text';
   const ITEM_TYPE_MENU = 'menu';
   const ITEM_TYPE_VIDEO = 'video';
   const ITEM_TYPE_ARTICLE = 'articles';
   const ITEM_TYPE_ARTICLEWGAL = 'articleswgal';
   const ITEM_TYPE_ACTION = 'actions';
   const ITEM_TYPE_ACTIONWGAL = 'actionswgal';
   const ITEM_TYPE_NEWS = 'news';

   const IMAGE_W = 600;
   const IMAGE_H = 225;

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
         $data = $link = $title = $image = $imageAlt = $nameCat = null;
         $dataObj = new Object();
         $linkCat = $this->link()->clear();
         $name = unserialize($item->{TitlePage_Model_Items::COLUMN_NAME});
         if(isset ($name[Locales::getLang()])){
            $name = $name[Locales::getLang()];
         } else {
            $name = null;
         }
         switch ($item->{TitlePage_Model_Items::COLUMN_TYPE}) {
            case self::ITEM_TYPE_TEXT:
            case self::ITEM_TYPE_MENU:
               $d = unserialize($item->{TitlePage_Model_Items::COLUMN_DATA});
               if(isset ($d[Locales::getLang()]))
                  $data = $d[Locales::getLang()];
               $dataObj = $d;
               if($item->{TitlePage_Model_Items::COLUMN_IMAGE} != null) {
                  $image = $imageAlt = $item->{TitlePage_Model_Items::COLUMN_IMAGE};
               }
               break;
            case self::ITEM_TYPE_VIDEO:
               $data = $item->{TitlePage_Model_Items::COLUMN_DATA};
               break;
            case self::ITEM_TYPE_ARTICLE:
            case self::ITEM_TYPE_ARTICLEWGAL:
            case self::ITEM_TYPE_NEWS:
               if($item->{TitlePage_Model_Items::COLUMN_ID_EXTERN} != 0){ // aktuální
                  $modelArticles = new Articles_Model();
                  $article = $modelArticles->joinFK(Articles_Model::COLUMN_ID_USER, array(Model_Users::COLUMN_USERNAME))
                  ->record($item->{TitlePage_Model_Items::COLUMN_ID_EXTERN});
               } else { // vybraný
                  $modelArticles = new Articles_Model();
                  $article = $modelArticles->joinFK(Articles_Model::COLUMN_ID_USER, array(Model_Users::COLUMN_USERNAME))
                  ->limit(0,1)->order(array(Articles_Model::COLUMN_ADD_TIME))->record();
               }
               if($article == false OR (string)$article->{Articles_Model_Detail::COLUMN_URLKEY} == null){
                  /* zde odstranit zastaralý item (asi pořešit pokud je více jazyků) */
                  continue;
               }

               $dataObj = $article;

               $modelCat = new Model_Category();
               $cat = $modelCat->getCategoryById($item->{TitlePage_Model_Items::COLUMN_ID_CATEGORY});

               if($cat == false OR (string)$cat->{Model_Category::COLUMN_URLKEY} == null) continue; // nepřeložená kategorie nemá url

               $link = new Url_Link_Module(true);
               $link->setModuleRoutes(new Articles_Routes(AppCore::getUrlRequest()));

               $linkCat = $link->category($cat->{Model_Category::COLUMN_URLKEY});
               $link = $linkCat->route('detail', array('urlkey' => $article->{Articles_Model_Detail::COLUMN_URLKEY}));

               if((string)$article->{Articles_Model_Detail::COLUMN_ANNOTATION} != null){
                  $data = $article->{Articles_Model_Detail::COLUMN_ANNOTATION};
               } else {
                  $data = strip_tags($article->{Articles_Model_Detail::COLUMN_TEXT}, VVE_SHORT_TEXT_TAGS);
               }

               $title = $article->{Articles_Model_Detail::COLUMN_NAME};
               $nameCat = $cat->{Model_Category::COLUMN_CAT_LABEL};

               // pokud je galerie s článkem
               if($item->{TitlePage_Model_Items::COLUMN_TYPE} == self::ITEM_TYPE_ARTICLEWGAL){
                  $modelPhoto = new PhotoGalery_Model_Images();
                  $images = $modelPhoto->getImages($item->{TitlePage_Model_Items::COLUMN_ID_CATEGORY},
                     $article->{Articles_Model_Detail::COLUMN_ID}, 1)->fetchObject();
                  if($images != false){
                     $image = Url_Request::getBaseWebDir().VVE_DATA_DIR.URL_SEPARATOR.$cat->{Model_Category::COLUMN_DATADIR}
                     .URL_SEPARATOR.$article[Articles_Model_Detail::COLUMN_URLKEY][Locales::getDefaultLang()]
                     .URL_SEPARATOR.Photogalery_Controller::DIR_MEDIUM.URL_SEPARATOR
                     .$images->{PhotoGalery_Model_Images::COLUMN_FILE};
                     $imageAlt = $images->{PhotoGalery_Model_Images::COLUMN_FILE};
                  }
                  unset ($images);
               }
               // pokud je článek a obsahuje obrázek, použijeme ten
               else if ($item->{TitlePage_Model_Items::COLUMN_TYPE} == self::ITEM_TYPE_ARTICLE
                  OR $item->{TitlePage_Model_Items::COLUMN_TYPE} == self::ITEM_TYPE_NEWS){
                  $dom = new DOMDocument();
                  $dom->loadHTML((string)$article->{Articles_Model_Detail::COLUMN_TEXT});
                  $xml = simplexml_import_dom($dom);
                  $images = $xml->xpath('//img');
                  if(!empty ($images)){
                     $image = $images[0]['src'];
                     $imageAlt = basename($images[0]['src']);
                  }
               }
               unset ($cat);
               unset ($article);
               break;
            case self::ITEM_TYPE_ACTION:
            case self::ITEM_TYPE_ACTIONWGAL:
               if($item->{TitlePage_Model_Items::COLUMN_ID_EXTERN} != 0){ // aktuální
                  $modelActions = new Actions_Model_Detail();
                  $action = $modelActions->getActionById($item->{TitlePage_Model_Items::COLUMN_ID_EXTERN});
               } else { // vybraný
                  $modelActions = new Actions_Model_List();
                  $action = $modelActions->getFeaturedActions($item->{TitlePage_Model_Items::COLUMN_ID_CATEGORY})->fetch();
               }
               if($action == false) continue; /* To samé co pro články */

               $dataObj = $action;

               $modelCat = new Model_Category();
               $cat = $modelCat->getCategoryById($item->{TitlePage_Model_Items::COLUMN_ID_CATEGORY});

               if($cat == false OR (string)$cat->{Model_Category::COLUMN_URLKEY} == null) continue; // nepřeložená kategorie nemá url

               $link = new Url_Link_Module(true);
               $link->setModuleRoutes(new Actions_Routes(AppCore::getUrlRequest()));

               $linkCat = $link->category($cat->{Model_Category::COLUMN_URLKEY});
               $link = $linkCat->route('detail', array('urlkey' => $action->{Actions_Model_Detail::COLUMN_URLKEY}));

//               if((string)$ac ->{Actions_Model_Detail::::COLUMN_ANNOTATION} != null){
//                  $data = $article->{Articles_Model_Detail::COLUMN_ANNOTATION};
//               } else {
                  $data = strip_tags($action->{Actions_Model_Detail::COLUMN_TEXT}, '<a><strong><em>');
//               }

               $title = $action->{Actions_Model_Detail::COLUMN_NAME};
               $nameCat = $cat->{Model_Category::COLUMN_CAT_LABEL};
               if($action->{Actions_Model_Detail::COLUMN_IMAGE} != null){
                  $image = Url_Request::getBaseWebDir().VVE_DATA_DIR.URL_SEPARATOR.$cat->{Model_Category::COLUMN_DATADIR}
                     .URL_SEPARATOR.$action[Actions_Model_Detail::COLUMN_URLKEY][Locales::getDefaultLang()]
                     .URL_SEPARATOR.$action->{Actions_Model_Detail::COLUMN_IMAGE};
                  $imageAlt = $action->{Actions_Model_Detail::COLUMN_IMAGE};
               }

               // pokud je akce s galerí a akce nemá titulní obrázek
               if($item->{TitlePage_Model_Items::COLUMN_TYPE} == self::ITEM_TYPE_ACTIONWGAL
                  AND $image == null){
                  $modelPhoto = new PhotoGalery_Model_Images();
                  $images = $modelPhoto->getImages($item->{TitlePage_Model_Items::COLUMN_ID_CATEGORY},
                     $action->{Actions_Model_Detail::COLUMN_ID}, 1)->fetchObject();
                  $image = Url_Request::getBaseWebDir().VVE_DATA_DIR.URL_SEPARATOR.$cat->{Model_Category::COLUMN_DATADIR}
                     .URL_SEPARATOR.$action[Articles_Model_Detail::COLUMN_URLKEY][Locales::getDefaultLang()]
                     .URL_SEPARATOR.Photogalery_Controller::DIR_MEDIUM.URL_SEPARATOR
                     .$images->{PhotoGalery_Model_Images::COLUMN_FILE};
                  $imageAlt = $images->{PhotoGalery_Model_Images::COLUMN_FILE};
                  unset ($images);
               }
               unset ($cat);
               unset ($action);
               break;
            default:
               break;
         }
         if(empty ($data)) continue;

         array_push($itemsList, array('itemObj' => $item,
                                      'id' => $item->{TitlePage_Model_Items::COLUMN_ID},
                                      'type' => $item->{TitlePage_Model_Items::COLUMN_TYPE},
                                      'name' => $name,
                                      'nameCat' => $nameCat,
                                      'title' => $title,
                                      'image' => $image,
                                      'imageAlt' => $imageAlt,
                                      'data' => $data,
                                      'dataObj' => $dataObj,
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
         self::ITEM_TYPE_ARTICLEWGAL => array(
            'link' => $this->link()->route('addItem', array('type' => self::ITEM_TYPE_ARTICLEWGAL)),
            'name' => $this->_('Článek s galerií'),
            'title' => $this->_('Přidání položky s článkem s galerií'),
            'desc' => $this->_('Přidání položky s existujícím článkem s galerií v systému')
         ),
         self::ITEM_TYPE_NEWS => array(
            'link' => $this->link()->route('addItem', array('type' => self::ITEM_TYPE_NEWS)),
            'name' => $this->_('Novinka'),
            'title' => $this->_('Přidání položky s novinkou'),
            'desc' => $this->_('Přidání položky s existující novinkou v systému')
         ),
         self::ITEM_TYPE_ACTION => array(
            'link' => $this->link()->route('addItem', array('type' => self::ITEM_TYPE_ACTION)),
            'name' => $this->_('Akce'),
            'title' => $this->_('Přidání položky s akcí'),
            'desc' => $this->_('Přidání položky s existující akcí v systému')
         ),
         self::ITEM_TYPE_ACTIONWGAL => array(
            'link' => $this->link()->route('addItem', array('type' => self::ITEM_TYPE_ACTIONWGAL)),
            'name' => $this->_('Akce s galerií'),
            'title' => $this->_('Přidání položky s akcí s galerií'),
            'desc' => $this->_('Přidání položky s existující akcí s galerií v systému')
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
         case self::ITEM_TYPE_NEWS:
         case self::ITEM_TYPE_ARTICLEWGAL:
         case self::ITEM_TYPE_ACTION:
         case self::ITEM_TYPE_ACTIONWGAL:
            $this->editItemArticleCtrl(null, $this->getRequest('type'));
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
         case self::ITEM_TYPE_ARTICLEWGAL:
         case self::ITEM_TYPE_NEWS:
         case self::ITEM_TYPE_ACTION:
         case self::ITEM_TYPE_ACTIONWGAL:
            $this->editItemArticleCtrl($item, $item->{TitlePage_Model_Items::COLUMN_TYPE});
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
      $image = null;
      if($item != null){
         $id=$item->{TitlePage_Model_Items::COLUMN_ID};
         $form->name->setValues(unserialize($item->{TitlePage_Model_Items::COLUMN_NAME}));
         $form->text->setValues(unserialize($item->{TitlePage_Model_Items::COLUMN_DATA}));
         $form->columns->setValues($item->{TitlePage_Model_Items::COLUMN_COLUMNS});
         // pokud je obrázek přidáme element s checkboxem pro odstranění
         if($item->{TitlePage_Model_Items::COLUMN_IMAGE} != null){
            $image = $item->{TitlePage_Model_Items::COLUMN_IMAGE};
            $elemDelImg = new Form_Element_Checkbox('delimg', $this->_('Smazat obrázek'));
            $elemDelImg->setSubLabel(sprintf($this->_('Uložen obrázek: %s'),$item->{TitlePage_Model_Items::COLUMN_IMAGE}));
            $form->addElement($elemDelImg);
         }
      }

      $elemSubmit = new Form_Element_Submit('save', $this->_('Uložit'));
      $form->addElement($elemSubmit);

      if($form->isValid()){
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
            $imgObj->resampleImage($this->category()->getParam('image_w', self::IMAGE_W),
               $this->category()->getParam('image_h', self::IMAGE_H),
               $this->category()->getParam('image_crop', true));
            $imgObj->save();
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
         $modelItems->saveItem(self::ITEM_TYPE_VIDEO, strip_tags($form->videoObj->getValues(),'<object><param><embed>'),
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

      $catEmptyMsg = $this->_('Není vytvořena žádná kategorie pro požadovaný panel');
      switch ($type) {
         case self::ITEM_TYPE_ACTION:
            $module = 'actions';
            $catEmptyMsg = $this->_('Není vytvořena žádná kategorie s akcí');
            break;
         case self::ITEM_TYPE_ACTIONWGAL:
            $module = 'actionswgal';
            $catEmptyMsg = $this->_('Není vytvořena žádná kategorie s akcí s fotogalerií');
            break;
         case self::ITEM_TYPE_NEWS:
            $module = 'news';
            $catEmptyMsg = $this->_('Není vytvořena žádná kategorie s novinkami');
            break;
         case self::ITEM_TYPE_ARTICLEWGAL:
            $module = 'articleswgal';
            $catEmptyMsg = $this->_('Není vytvořena žádná kategorie s články');
            break;
         case self::ITEM_TYPE_ARTICLE:
         default:
            $catEmptyMsg = $this->_('Není vytvořena žádná kategorie s články');
            $module = 'articles';
            break;
      }
      $this->view()->type = $type; // typ editace
      $modelItems = new TitlePage_Model_Items();

      $elemName = new Form_Element_Text('name', $this->_('Název'));
      $elemName->setLangs();
      $form->addElement($elemName);

      $elemCategory = new Form_Element_Select('category_id', $this->_('Kategorie'));
      $elemCategory->addValidation(new Form_Validator_IsNumber());
      $elemCategory->addValidation(new Form_Validator_NotEmpty());
      $modelCat = new Model_Category();
      $cats = $modelCat->getCategoryListByModule($module)->fetchAll();
      if(empty ($cats)){
         $this->errMsg()->addMessage($catEmptyMsg);
         return false;
      }
      foreach ($cats as $cat) {
         $elemCategory->setOptions(array(vve_tpl_truncate((string)$cat->{Model_Category::COLUMN_CAT_LABEL},100) => $cat->{Model_Category::COLUMN_CAT_ID}),true);
      }
      $form->addElement($elemCategory);

      $elemArticle = new Form_Element_Select('article_id', $this->_('Článek'));
      $elemArticle->addValidation(new Form_Validator_IsNumber());
      $elemArticle->addValidation(new Form_Validator_NotEmpty());

      if($item != null){
         $this->getListController($type, $item->{TitlePage_Model_Items::COLUMN_ID_CATEGORY});
         $arts = $this->view()->list;
      } else {
         $this->getListController($type, reset($cats)->{Model_Category::COLUMN_CAT_ID});
         $arts = $this->view()->list;
      }
      $elemArticle->setOptions(array($this->_('Aktuální - naposledy přidaný') => 0),true);
      foreach ($arts as $art) {
         $elemArticle->setOptions(array((string)$art['text'] => $art['id']),true);
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
                 null, $form->columns->getValues(),$id, $form->article_id->getValues());

         $this->infoMsg()->addMessage($this->_('Položka byla uložena'));
         $this->link()->route('editList')->reload();
      }
      $this->view()->form = $form;
   }

   public function getListController($type = self::ITEM_TYPE_ARTICLE, $idc = 0) {
      $this->checkWritebleRights();
      $idc = $this->getRequestParam('idc', $idc);
      $result = array();
      switch ($this->getRequestParam('type', $type)) {
         case self::ITEM_TYPE_ARTICLE:
         case self::ITEM_TYPE_ARTICLEWGAL:
         case self::ITEM_TYPE_NEWS:
            array_push($result,array('id' => 0, 'text' => $this->_('Aktuální - naposledy přidaný')));
            $modelArticles = new Articles_Model_List();
            $articles = $modelArticles->getList($idc, 0, 100)->fetchAll();
            foreach ($articles as $art) {
               array_push($result, array('id' => (int)$art->{Articles_Model_Detail::COLUMN_ID},
                       'text' => vve_tpl_truncate((string)$art->{Articles_Model_Detail::COLUMN_NAME},100)));
            }
            break;
         case self::ITEM_TYPE_ACTION:
         case self::ITEM_TYPE_ACTIONWGAL:
            array_push($result,array('id' => 0, 'text' => $this->_('Aktuální - naposledy přidaný')));
            $modelActions = new Actions_Model_List();
            $toTime = new DateTime();
            $toTime->modify('+ 1 year');
            $actions = $modelActions->getActions($idc, new DateTime(), $toTime)->fetchAll();
            foreach ($actions as $act) {
               array_push($result, array('id' => (int)$act->{Actions_Model_Detail::COLUMN_ID},
                       'text' => vve_tpl_truncate((string)$act->{Actions_Model_Detail::COLUMN_NAME},100)));
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
      $this->infoMsg()->addMessage($this->_('Položka byla smazána'));
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
   public function settings(&$settings,Form &$form) {
      $form->addGroup('basic', 'Základní nastavení');

      $form->addGroup('images', 'Nastavení obrázků');

      $elemSW = new Form_Element_Text('image_w', 'Šířka titulního obrázku (px)');
      $elemSW->addValidation(new Form_Validator_IsNumber());
      $elemSW->setSubLabel('Výchozí: '.self::IMAGE_W.'px');
      $form->addElement($elemSW, 'images');
      if(isset($settings['image_w'])) {
         $form->image_w->setValues($settings['image_w']);
      }

      $elemSH = new Form_Element_Text('image_h', 'Výška titulního obrázku (px)');
      $elemSH->addValidation(new Form_Validator_IsNumber());
      $elemSH->setSubLabel('Výchozí: '.self::IMAGE_H.'px');
      $form->addElement($elemSH, 'images');
      if(isset($settings['image_h'])) {
         $form->image_h->setValues($settings['image_h']);
      }

      $elemCropImg = new Form_Element_Checkbox('image_crop', 'Titulní obrázek ořezat');
      $elemCropImg->setValues(true);
      $form->addElement($elemCropImg, 'images');
      if(isset($settings['image_crop'])) {
         $form->image_crop->setValues($settings['image_crop']);
      }

      if($form->isValid()){
         $settings['image_w'] = $form->image_w->getValues();
         $settings['image_h'] = $form->image_h->getValues();
         $settings['image_crop'] = $form->image_crop->getValues();
      }
   }
}
?>