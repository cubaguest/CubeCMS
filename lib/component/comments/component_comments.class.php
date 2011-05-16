<?php
/**
 * Třída Komponenty pro práci s komentáři ke článku, galerii, atd
 *
 * @copyright  	Copyright (c) 2008-2010 Jakub Matas
 * @version    	$Id: $ VVE 6.1.0 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída Komponenty pro komentáře
 */

class Component_Comments extends Component {
   const PARAM_ID_ARTICLE = 'id_art';
   const PARAM_ID_CATEGORY = 'id_cat';
   const PARAM_NEW_ARE_PUBLIC = 'public';
   const PARAM_CLOSED = 'closed';
   const PARAM_ADMIN = 'admin';
   const PARAM_ALLOW_TAGS = 'allowed_tags';
   const PARAM_CAPTCHA_TIME = 'ctime';
   const PARAM_MAX_CHARS = 'chars';
   const PARAM_FACEBOOK = 'fcb';
   const COOKIE_VIEWED = 'viewed';

   protected $config = array(self::PARAM_ID_ARTICLE => 0,
           self::PARAM_ID_CATEGORY => 0,
           self::PARAM_NEW_ARE_PUBLIC => true,
           self::PARAM_CLOSED => false,
           self::PARAM_ADMIN => false,
           self::PARAM_FACEBOOK => false,
           self::PARAM_CAPTCHA_TIME => 15,
           self::PARAM_MAX_CHARS => 500,
           self::PARAM_ALLOW_TAGS => array('a','br','em','strong','sub',
              'sup','ul','li','ol')
   );

   private $model = null;

   private $cookieName = 'viewed_c';

   /**
    * Metoda inicializace, je spuštěna pří vytvoření objektu
    */
   protected function init() {
      $this->cookieName = VVE_SESSION_NAME.'_c';
      $this->model = new Component_Comments_Model();
   }

   /**
    * Metoda pro spouštění některých akcí přímo v kontroleru
    */
   public function runCtrlPart() {
   }

   private function saveViewedComments($arrayids) {
      setcookie($this->cookieName, implode(',', $arrayids), time()+60*60*24*365); // na rok uložíme cookie
   }

   private function getViewedComments() {
      if(isset ($_COOKIE[$this->cookieName])) return explode(',', $_COOKIE[$this->cookieName]);
      return array();
   }


   /**
    * Spuštění pluginu
    * @param mixed $params -- parametry epluginu (pokud je třeba)
    */
   public function mainController() {
      // facebook comments
      if($this->getConfig(self::PARAM_FACEBOOK)){

         return;
      }

      // načtení komentářů
      $this->model->where(Component_Comments_Model::COL_ID_CAT.' = :idc AND '.Component_Comments_Model::COL_ID_ART.' = :ida',
         array('idc' => $this->getConfig(self::PARAM_ID_CATEGORY), 'ida' => $this->getConfig(self::PARAM_ID_ARTICLE)));
      if($this->getConfig(self::PARAM_ADMIN) != true){
         $this->model->where('AND '.Component_Comments_Model::COL_PUBLIC.' = 1', array(), true);
      }
      $this->model->order(array(Component_Comments_Model::COL_ORDER => Model_ORM::ORDER_ASC));
      $this->template()->comments = $this->model->records();
      $this->template()->countComments = $this->model->count();
      $this->template()->unreaded = count($this->template()->comments);

      // projití komentářu a uložení neviděných
      $commViewedForSave = array();
      $commViewed = $this->getViewedComments();
      foreach ($this->template()->comments as &$comment) {
         $comment->viewed = false;
         if(!empty ($commViewed) AND in_array($comment->{Component_Comments_Model::COL_ID},$commViewed)){
            $comment->viewed = true;
            $this->template()->unreaded--;
         }
         array_push($commViewedForSave, $comment->{Component_Comments_Model::COL_ID});
      }
      $this->saveViewedComments($commViewedForSave);

      // form pro přidání
      $addForm = new Form('comment_new_');
      $elemNick = new Form_Element_Text('nick', $this->tr('Nick'));
      $elemNick->addValidation(new Form_Validator_NotEmpty());
      if(Auth::isLogin()) {
         $elemNick->setValues(Auth::getUserName());
      }

      $addForm->addElement($elemNick);

      $elemText = new Form_Element_TextArea('comment', $this->tr('komentář'));
      $elemText->addValidation(new Form_Validator_NotEmpty());
      $elemText->addValidation(new Form_Validator_Length(10, $this->getConfig(self::PARAM_MAX_CHARS)));
      $elemText->addFilter(new Form_Filter_StripTags($this->getConfig(self::PARAM_ALLOW_TAGS)));
      $addForm->addElement($elemText);

      $elemRe = new Form_Element_Hidden('parent');
      $elemRe->setValues(0);
      $addForm->addElement($elemRe);

      $elemAdd = new Form_Element_Submit('add', $this->tr('Uložit'));
      $addForm->addElement($elemAdd);

      if($addForm->isValid()) {
         if(!isset ($_SESSION['comment_captcha_time']) OR
                 ($_SESSION['comment_captcha_time']+$this->getConfig(self::PARAM_CAPTCHA_TIME) > time())) {
            $this->errMsg()->addMessage($this->tr('Komentář byl odeslán příliš rychle nebo nebyl odeslán kontrolní čas'));
         } else {
            $comment = $this->model->newRecord();

            if($this->getConfig(self::PARAM_NEW_ARE_PUBLIC) == true
                    OR $this->getConfig(self::PARAM_ADMIN) == true) {
               $comment->{Component_Comments_Model::COL_PUBLIC} = true;
            }
            $comment->{Component_Comments_Model::COL_COMMENT} = $addForm->comment->getValues();
            $comment->{Component_Comments_Model::COL_NICK} = htmlspecialchars($addForm->nick->getValues());
            $comment->{Component_Comments_Model::COL_ID_CAT} = $this->getConfig(self::PARAM_ID_CATEGORY);
            $comment->{Component_Comments_Model::COL_ID_ART} = $this->getConfig(self::PARAM_ID_ARTICLE);

            $comment->{Component_Comments_Model::COL_ID_PARENT} = $addForm->parent->getValues();

            $id = $this->model->save($comment);

            // uložení do zobrazených
            $commViewedForSave = $this->getViewedComments();
            $commViewedForSave[] = $id;
            $this->saveViewedComments($commViewedForSave);

            if($comment->{Component_Comments_Model::COL_PUBLIC} == true) {
               $this->infoMsg()->addMessage($this->tr('Komentář byl uložen'));
            } else {
               $this->infoMsg()->addMessage($this->tr('Komentář byl uložen a čeká na schválení'));
            }
            $this->pageLink()->reload();
         }
      }
      $this->template()->formAdd = $addForm;

      $_SESSION['comment_captcha_time'] = time();

      // cenzůra
      $formCensore = new Form('comment_censore_');
      $elemId = new Form_Element_Hidden('id');
      $elemId->addValidation(new Form_Validator_IsNumber());
      $formCensore->addElement($elemId);

      $elemCensore = new Form_Element_Submit('censored',$this->tr('Změnit cenzůru'));
      $formCensore->addElement($elemCensore);

      if($formCensore->isValid()) {
         $this->model->changeCensored($formCensore->id->getValues());
         $this->infoMsg()->addMessage($this->tr('Komentáři byla změněna cenzůra'));
         $this->pageLink()->reload();
      }
      $this->template()->formCensore = $formCensore;

      // zveřejnění
      $formPublic = new Form('comment_public_');
      $elemId = new Form_Element_Hidden('id');
      $elemId->addValidation(new Form_Validator_IsNumber());
      $formPublic->addElement($elemId);

      $elemPublicB = new Form_Element_Submit('public', $this->tr('Změnit zveřejnění'));
      $formPublic->addElement($elemPublicB);

      if($formPublic->isValid()) {
         $this->model->changePublic($formPublic->id->getValues());
         $this->infoMsg()->addMessage($this->tr('Komentáři bylo změněno zveřejnění'));
         $this->pageLink()->reload();
      }
      $this->template()->formPublic = $formPublic;
   }

   /**
    * Metoda nastaví id šablony pro výpis
    * @param integer -- id šablony (jakékoliv)
    */
   public function mainView() {
      $this->template()->isClosed = $this->getConfig(self::PARAM_CLOSED);
      $this->template()->admin = $this->getConfig(self::PARAM_ADMIN);
      // facebook comments
      if($this->getConfig(self::PARAM_FACEBOOK)){
         $this->template()->addTplFile('comments/fcb.phtml');
         return;
      }

      // toolbox
      if($this->getConfig(self::PARAM_ADMIN) == true){
         $toolbox = new Template_Toolbox2();
         $toolbox->setIcon(Template_Toolbox2::ICON_WRENCH);
         // zveřejnění
         $toolPublic = new Template_Toolbox2_Tool_Form($this->template()->formPublic);
         $toolPublic->setIcon('comment.png');
         $toolbox->addTool($toolPublic);
         // cenzura
         $toolCensore = new Template_Toolbox2_Tool_Form($this->template()->formCensore);
         $toolCensore->setIcon('comment_error.png');
         $toolbox->addTool($toolCensore);

         $this->template()->toolboxComment = $toolbox;
      }


      $this->template()->addTplFile('comments/list.phtml');

      if($this->getConfig(self::PARAM_CLOSED) == false) {
         $this->template()->addTplFile('comments/add.phtml');
      }
      $this->template()->ctTime = $this->getConfig(self::PARAM_CAPTCHA_TIME);
   }

   /**
    * Metoda vrací počet komentářů
    * @return int
    */
   public function getCountComments() {
      return $this->model->getCountComments($this->getConfig(self::PARAM_ID_CATEGORY),
              $this->getConfig(self::PARAM_ID_ARTICLE));
   }

   public function deleteAll() {

   }
}
?>