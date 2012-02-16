<?php
class Search_Controller extends Controller {
   public static $apis = array('Cube CMS verze 6/7' => 'vve_7');


   public function mainController() {
      $model = new Search_Model_Api();
      
      $searchStr = $this->getRequestParam('q', null);
      $results = null;

      // hledá se
      if($searchStr != null) {
         // nastavení vyledávání
         Search::factory($searchStr);
         
         $source = $this->getRequestParam('s', 'all');
         // kvůli identifikaci webu - stejná session pro několik webů viz. global auth
         $sessionName = 'search_'.md5(VVE_WEB_NAME);
         // jetli je nové nebo staré hledání
         if(VVE_DEBUG_LEVEL == 0 // pouze pokud je vypnut debug režim používat cache
            AND isset ($_SESSION[$sessionName]) AND $_SESSION[$sessionName]['string'] == $searchStr
            AND $_SESSION[$sessionName]['source'] == '$source') {
            $results = $_SESSION[$sessionName]['results'];
         } else {
            if($source == 'all') {
               $apis = $model->getApis($this->category()->getId());
               $this->search($searchStr);
               $this->searchExtSource($searchStr,$apis);
            } else if($source == 'this') {
               $this->search($searchStr);
            } else {
               $apis = $model->getApi($source);
               $this->searchExtSource($searchStr,$apis);
            }
            // řazení a zvýraznění
            $results = Search::getResults();
            $results = Search::sortResults($results);
            $results = Search::prepareResultsForView($results);
            $_SESSION[$sessionName] = array('string' => $searchStr, 'results' => $results, 'source' => $source);
         }
         
         $this->view()->countAllResults = count($results);

         $scrollComponent = new Component_Scroll();
         $scrollComponent->setConfig(Component_Scroll::CONFIG_CNT_ALL_RECORDS,$this->view()->countAllResults);
         $scrollComponent->setConfig(Component_Scroll::CONFIG_RECORDS_ON_PAGE,
                 $this->category()->getModule()->getParam('scroll', 5));

         $this->view()->scrollComp = $scrollComponent;
         $this->view()->search = true;
         $this->view()->results = array_slice($results, $scrollComponent->getStartRecord(), $scrollComponent->getRecordsOnPage());
      }
      $this->view()->searchString = htmlspecialchars($searchStr);
      $this->view()->searchTarget = $this->getRequestParam('s', 'all');
      $this->view()->apis = $model->getApis($this->category()->getId());
   }

   /*
    * API pro vyhledávání
   */
   public function searchController() {
      // nastavení vyledávání
      Search::factory($this->getRequestParam('q'));
      Search::setResultLenght($this->getRequestParam('reslen',VVE_SEARCH_RESULT_LENGHT));
      switch ($this->getRequest('type')) {
         case 'json':
            $tplOut = new Template_Output('json');
            $this->search($this->getRequestParam('q'));
            $data = json_encode(Search::getResults());
            $tplOut->setContentLenght(strlen($data));
            print ($data);
            flush();
            exit();

            break;
         case 'php':
            $tplOut = new Template_Output('txt');
            $this->search($this->getRequestParam('q'));
            $data = serialize(Search::getResults());
            $tplOut->setContentLenght(strlen($data));
            print ($data);
            flush();
            exit();
            break;
      }

   }

   private function search($string) {
      $catM = new Model_Category();

      // hledání v kategoríích
      $catResults = $catM->onlyWithAccess()->search($string);
      
      $searchMain = new Search();
      $catRelevantion = array();
      if($catResults != false){
         foreach ($catResults as $result) {
            $searchMain->addResult(null, null, $result->{Model_Category::COLUMN_DESCRIPTION},
                 $result->{Search::COLUMN_RELEVATION},
                 $result->{Model_Category::COLUMN_CAT_LABEL},
                 $searchMain->link()->category($result->{Model_Category::COLUMN_URLKEY}));
            $catRelevantion[$result->{Model_Category::COLUMN_CAT_ID}] = $result->{Search::COLUMN_RELEVATION};
         }
      }
      unset ($searchMain);

      // načtení všech kategorií, ke kterým má uživatel práva
      $catM = new Model_Category();
      $categories = $catM->onlyWithAccess()->records();
      
      foreach ($categories as $cat) {
         // kontrola souboru (jestli modul má vyhledávání)
         if(!file_exists(AppCore::getAppLibDir().AppCore::MODULES_DIR.DIRECTORY_SEPARATOR
         .$cat->{Model_Category::COLUMN_MODULE}.DIRECTORY_SEPARATOR.'search.class.php')) continue;

            // objekt kategorie
            $catObj = new Category(null, false, $cat);
            // jméno třídy hledání
            $sClassName = ucfirst($catObj->getModule()->getName())."_Search";
            
         $baseRel = 0;
         if(isset ($catRelevantion[$catObj->getId()])){
            $baseRel = $catRelevantion[$catObj->getId()];
         }

         $search = new $sClassName($catObj, $baseRel);
         $search->runSearch();
      }
   }

   private function searchOnlyCat($string, $idc)
   {
      
   }


   private function searchExtSource($string, $apis) {
      /*
       * Načítání externích url při použití fopen
      */
      foreach ($apis as $row){
         $url = str_replace('{search}', urlencode($string), $row->{Search_Model_Api::COLUMN_URL});
         // úpravy url
         switch ($row->{Search_Model_Api::COLUMN_API}) {
            case 'vve_6':
            // pokud je engine vve v6 tak doplníme délku resultu
               $url .= 'search_api.php?q='.urlencode($string).'&reslen='.VVE_SEARCH_RESULT_LENGHT;
               $res = file_get_contents($url);
               $apiResults = unserialize($res);
               foreach ($apiResults as $result) {
                  Search::addExternalResult($result);
               }
               break;
         }
      }
   }

   public function editsapiController() {
      $model = new Search_Model_Api();

      $formAdd = new Form('search_add_');
      $elemUrl = new Form_Element_Text('url', $this->tr('Url adresa'));
      $elemUrl->addValidation(new Form_Validator_NotEmpty());
      $formAdd->addElement($elemUrl);

      $elemName = new Form_Element_Text('name', $this->tr('Název'));
      $elemName->addValidation(new Form_Validator_NotEmpty());
      $formAdd->addElement($elemName);

      $elemApi = new Form_Element_Select('api', $this->tr('API'));
      $elemApi->setOptions(self::$apis);
      $formAdd->addElement($elemApi);

      $elemsub = new Form_Element_Submit('save', $this->tr('Uložit'));
      $formAdd->addElement($elemsub);

      if($formAdd->isValid()) {
         $model->saveApi($formAdd->url->getValues(), $formAdd->api->getValues(),
                 $formAdd->name->getValues(),$this->category()->getId());

         $this->infoMsg()->addMessage($this->tr('Api bylo uloženo'));
         $this->link()->reload();
      }

      $formDelete = new Form('search_delete_');
      $elemId = new Form_Element_Hidden('id');
      $formDelete->addElement($elemId);

      $elemDel = new Form_Element_SubmitImage('delete', $this->tr('Smazat'));
      $formDelete->addElement($elemDel);

      if($formDelete->isValid()) {
         $model->deleteApi($formDelete->id->getValues());

         $this->infoMsg()->addMessage($this->tr('Api bylo smazáno'));
         $this->link()->reload();
      }


      $this->view()->apiLabels = $this->tr('<p>API, neboli rozhraní pro vyhledávání</p>
         <h3>Zadávání</h3>
         <p>Do url se zadává cesta k API hledání a místo hledaného řetězce se zadá "{search}".
         Pouze u stránek postavených na frameworku VVE verze 6 stačí zadat pouze URL adresu kategorii. Viz příklady dále:<br /><br />
         <strong>Příklady:</strong><br />
         VVE: "http://www.domena.com/hledej/" -- odkaz na ketegorii s hledáním<br />
         google: "http://www.google.cz/search?q={search}"<br />
         </p>');
      $this->view()->formAdd = $formAdd;
      $this->view()->formDel = $formDelete;
      $this->view()->apis = $model->getApis($this->category()->getId());
   }

   public static function settingsController(&$settings,Form &$form) {
      $fGrpView = $form->addGroup('view', 'Nastavení vzhledu');

      $elemScroll = new Form_Element_Text('scroll', 'Počet položek na stránku');
      $elemScroll->setSubLabel('Výchozí: 5 položek. Pokud je zadána 0 budou vypsány všechny položky');
      $elemScroll->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemScroll, $fGrpView);

      if(isset($settings['scroll'])) {
         $form->scroll->setValues($settings['scroll']);
      }
      // znovu protože mohl být už jednou validován bez těchto hodnot
      if($form->isValid()) {
         $settings['scroll'] = $form->scroll->getValues();
      }
   }
}
?>