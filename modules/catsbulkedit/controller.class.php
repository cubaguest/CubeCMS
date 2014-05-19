<?php

/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */
class CatsBulkEdit_Controller extends Controller {

   private $categories = array();
   
   private $modelCat;
   
   protected function init() 
   {
      $this->modelCat = new Model_Category();   
   }
   
   public function mainController()
   {
      $this->checkControllRights();

      $form = new Form('param_select');
      $form->setSendMethod('get');
      $form->setAction($this->link()->route('edit'));
      
      $eParam = new Form_Element_Select('param', $this->tr('Parametr'));
      $eParam->setOptions(array(
            $this->tr('Metadata') => array(
               $this->tr('Popisek') => 'description',
               $this->tr('Klíčová slova') => 'keywords',
            ),
            $this->tr('Základní') => array(
               $this->tr('Název') => 'name',
               $this->tr('Alternativní název') => 'altname',
               $this->tr('URL klíč') => 'urlkey',
               $this->tr('Priorita') => 'prioryty',
               $this->tr('Viditelnost') => 'visibility',
               $this->tr('Individuální panely') => 'indpanels',
               $this->tr('Výchozí práva') => 'def_rights',
               $this->tr('RSS exporty') => 'rss_exports',
               $this->tr('Titulní obrázek') => 'title_image',
//                $this->tr('Vlastník') => 'owner',
            ),
            $this->tr('Mapa stránek') => array(
               $this->tr('Frekvence změn') => 'sitemap_freq',
               $this->tr('Priorita') => 'sitemap_pri',
            ),
            ));
      $form->addElement($eParam);
      
      $elemSubmit = new Form_Element_Submit('send', $this->tr('Vybrat'));
      $form->addElement($elemSubmit);
      
      $this->view()->form = $form;
   }
   
   public function editController() 
   {
      $this->checkControllRights();
      $model = new Model_Category();
      
      $paramReq = $this->getRequestParam('param_selectparam', 'description');
      
      $form = new Form('edit_param_');
      switch ($paramReq) {
         case 'keywords':
            $paramName = $this->tr('klíčová slova');
            $param = Model_Category::COLUMN_KEYWORDS;
            $elem = new Form_Element_Text('param', $paramName);
            $elem->addFilter(new Form_Filter_StripTags());
            $elem->setDimensional();
            $elem->setLangs();
            
            break;
         case 'sitemap_freq':
            $paramName = $this->tr('frekvence změn');
            $param = Model_Category::COLUMN_SITEMAP_CHANGE_FREQ;
            $elem = new Form_Element_Select('param', $paramName);
            $elem->setDimensional();
            
            $opt = array( 
                  $this->tr('nikdy') => SiteMap::SITEMAP_SITE_CHANGE_NEVER,
                  $this->tr('ročně') => SiteMap::SITEMAP_SITE_CHANGE_YEARLY,
                  $this->tr('měsíčně') => SiteMap::SITEMAP_SITE_CHANGE_MONTHLY,
                  $this->tr('týdně') => SiteMap::SITEMAP_SITE_CHANGE_WEEKLY,
                  $this->tr('denně') => SiteMap::SITEMAP_SITE_CHANGE_DAILY,
                  $this->tr('každou hodinu') => SiteMap::SITEMAP_SITE_CHANGE_HOURLY,
                  $this->tr('vždy') => SiteMap::SITEMAP_SITE_CHANGE_ALWAYS,
                  );
            $elem->setOptions($opt);
            break;
         case 'sitemap_pri':
            $paramName = $this->tr('priorita');
            $param = Model_Category::COLUMN_SITEMAP_CHANGE_PRIORITY;
            $elem = new Form_Element_Text('param', $paramName);
            $elem->addValidation(new Form_Validator_NotEmpty());
            $elem->addValidation(new Form_Validator_IsNumber(null, Form_Validator_IsNumber::TYPE_FLOAT));
            $elem->addValidation(new Form_Validator_Range(0, 1));
            $elem->setDimensional();
            break;
         case 'prioryty':
            $paramName = $this->tr('priorita');
            $param = Model_Category::COLUMN_PRIORITY;
            $elem = new Form_Element_Text('param', $paramName);
            $elem->addValidation(new Form_Validator_NotEmpty());
            $elem->addValidation(new Form_Validator_IsNumber(null, Form_Validator_IsNumber::TYPE_INT));
            $elem->addValidation(new Form_Validator_Range(0, 1000));
            $elem->setDimensional();
            break;
         case 'altname':
            $paramName = $this->tr('alternativní název');
            $param = Model_Category::COLUMN_ALT;
            $elem = new Form_Element_Text('param', $paramName);
            $elem->addFilter(new Form_Filter_HTMLSpecialChars());
            $elem->setLangs();
            $elem->setDimensional();
            break;
         case 'name':
            $paramName = $this->tr('název');
            $param = Model_Category::COLUMN_NAME;
            $elem = new Form_Element_Text('param', $paramName);
            $elem->addValidation(new Form_Validator_NotEmpty(null, Locales::getDefaultLang()));
            $elem->addFilter(new Form_Filter_HTMLSpecialChars());
            $elem->setLangs();
            $elem->setDimensional();
            break;
         case 'urlkey':
            $paramName = $this->tr('URL adresa');
            $param = Model_Category::COLUMN_URLKEY;
            $elem = new Form_Element_Text('param', $paramName);
            $elem->addValidation(new Form_Validator_NotEmpty(null, Locales::getDefaultLang()));
            $elem->setLangs();
            $elem->setDimensional();
            break;
         case 'indpanels':
            $paramName = $this->tr('individuální panely');
            $param = Model_Category::COLUMN_INDIVIDUAL_PANELS;
            $elem = new Form_Element_Checkbox('param', $paramName);
            $elem->setDimensional();
            break;
         case 'rss_exports':
            $paramName = $this->tr('RSS exporty (pokud to kategorie umožňuje)');
            $param = Model_Category::COLUMN_FEEDS;
            $elem = new Form_Element_Checkbox('param', $paramName);
            $elem->setDimensional();
            break;
         case 'title_image':
            $paramName = $this->tr('Titulní obrázek kategorie');
            $param = Model_Category::COLUMN_IMAGE;
            $elem = new Form_Element_Select('param', $paramName);
            if(file_exists(Category::getImageDir(Category::DIR_ICON, true))){
               $dirIterator = new DirectoryIterator(Category::getImageDir(Category::DIR_ICON, true));
               $elem->setOptions(array($this->tr('Žádný') => ''), true);
               foreach ($dirIterator as $item) {
                  if($item->isDir() OR $item->isDot()) {
                     continue;
                  }
                  $elem->setOptions(array($item->getFilename() => $item->getFilename()), true);
               }
            }
            $elem->setDimensional();
            break;
         case 'def_rights':
            $paramName = $this->tr('výchozí práva');
            $param = Model_Category::COLUMN_DEF_RIGHT;
            $elem = new Form_Element_Select('param', $paramName);
            $elem->setDimensional();
            
            $opt = array( 
                  $this->tr('Žádná oprávnění (---)') => '---',
                  $this->tr('Oprávnění čtení (r--)') => 'r--',
                  $this->tr('Oprávnění záisu (rw-)') => 'rw-',
                  $this->tr('Oprávnění kontroly (rwc)') => 'rwc',
                  );
            $elem->setOptions($opt);
            break;
         case 'visibility':
            $paramName = $this->tr('viditelnost kategorie');
            $param = Model_Category::COLUMN_VISIBILITY;
            $elem = new Form_Element_Select('param', $paramName);
            $elem->setDimensional();
            
            $opt = array( 
                  $this->tr('Viditelná všem') => Model_Category::VISIBILITY_ALL,
                  $this->tr('Zkrytá') => Model_Category::VISIBILITY_HIDDEN,
                  $this->tr('Viditelná pouze administrátorům') => Model_Category::VISIBILITY_WHEN_ADMIN,
                  $this->tr('Viditelná všem administrátorům') => Model_Category::VISIBILITY_WHEN_ADMIN_ALL,
                  $this->tr('Viditelná pouze přihlášeným') => Model_Category::VISIBILITY_WHEN_LOGIN,
                  $this->tr('Viditelná pouze nepřihlášeným') => Model_Category::VISIBILITY_WHEN_NOT_LOGIN,
                  );
            $elem->setOptions($opt);
            break;
         case 'description':
         default:
            $paramName = $this->tr('popisek');
            $param = Model_Category::COLUMN_DESCRIPTION;
            $elem = new Form_Element_TextArea('param', $paramName);
            $elem->setLangs();
            $elem->setDimensional();
            break;
      }
      $form->addElement($elem);
      
      $this->view()->paramName = $paramName;
      $this->view()->param = $param;
      
      
      $elemSave = new Form_Element_SaveCancel('save');
      $form->addElement($elemSave); 
      
      if($form->isSend() && $form->save->getValues() == false){
         $this->link(true)->reload();
      }
      
      if($form->isValid()){
         $params = $form->param->getValues();
         foreach ($params as $id => $value) {
            $cat = $model->record($id);
            
            if($param == Model_Category::COLUMN_URLKEY){
               $value = vve_cr_url_key($value, false);
            }
            // assign value
            $cat->$param = $value;
            $cat->save();
            
         }
         if($param == Model_Category::COLUMN_NAME || $param == Model_Category::COLUMN_URLKEY){
            // create URL keys for empty
            $this->generateNewUrlkeys(Category_Structure::getStructure(Category_Structure::ALL));
         }
         $this->infoMsg()->addMessage($this->tr('Parametry byly uloženy'));
         Category_Structure::clearCache();
         $this->link(true)->reload();
      }
      
      $this->view()->form = $form;
      $this->view()->struct = Category_Structure::getStructure(Category_Structure::ALL);;
      
   }
   
   protected function generateNewUrlkeys(Category_Structure $structure, $parentKeys = null) 
   {
      foreach ($structure as $child){
         $cat = $this->modelCat->record($child->getId());
         $changed = false;
         $urlKeys = $cat->{Model_Category::COLUMN_URLKEY};
         foreach ($cat[Model_Category::COLUMN_NAME] as $lang => $name) {
            if($name != null && $urlKeys[$lang] == null ){
               $urlKeys[$lang] = 
                  ( $parentKeys != null ? $parentKeys[$lang]."/" : null ) . vve_cr_url_key($name);
               $changed = true;
            } 
            // remove url key if name is empty
            else if($name == null){ 
               $urlKeys[$lang] = null;
            }
         }
         $cat->{Model_Category::COLUMN_URLKEY} = $urlKeys;
         if($changed){
            $cat->save();
         }
         
         if(!$child->isEmpty()){
            $this->generateNewUrlkeys($child, $cat->{Model_Category::COLUMN_URLKEY});
         }
      }
   }
   
}
?>