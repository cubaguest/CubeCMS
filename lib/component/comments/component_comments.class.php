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

   protected $config = array(self::PARAM_ID_ARTICLE => 0,
           self::PARAM_ID_CATEGORY => 0,
           self::PARAM_NEW_ARE_PUBLIC => true,
           self::PARAM_CLOSED => false,
           self::PARAM_ADMIN => false,
           self::PARAM_CAPTCHA_TIME => 15,
           self::PARAM_ALLOW_TAGS => array('a','br','em','strong','sub',
              'sup','ul','li','ol')
   );

   private $model = null;

   /**
    * Metoda inicializace, je spuštěna pří vytvoření objektu
    */
   protected function init() {
      $this->model = new Component_Comments_Model();
   }

   /**
    * Metoda pro spouštění některých akcí přímo v kontroleru
    */
   public function runCtrlPart() {
   }

   /**
    * Spuštění pluginu
    * @param mixed $params -- parametry epluginu (pokud je třeba)
    */
   public function mainController() {
      // form pro přidání
      $addForm = new Form('comment_new_');
      $elemNick = new Form_Element_Text('nick', _('Nick'));
      $elemNick->addValidation(new Form_Validator_NotEmpty());
      if(Auth::isLogin()) {
         $elemNick->setValues(Auth::getUserName());
      }

      $addForm->addElement($elemNick);

      $elemText = new Form_Element_TextArea('comment', _('komentář'));
      $elemText->addValidation(new Form_Validator_NotEmpty());
      $elemText->addValidation(new Form_Validator_Length(10, 200));
      $elemText->addFilter(new Form_Filter_StripTags($this->getConfig(self::PARAM_ALLOW_TAGS)));
      $addForm->addElement($elemText);

      $elemRe = new Form_Element_Hidden('parent');
      $elemRe->setValues(0);
      $addForm->addElement($elemRe);

      $elemAdd = new Form_Element_Submit('add', _('Uložit'));
      $addForm->addElement($elemAdd);

      if($addForm->isValid()) {
         if(!isset ($_SESSION['comment_captcha_time']) OR
                 ($_SESSION['comment_captcha_time']+$this->getConfig(self::PARAM_CAPTCHA_TIME) > time())) {
            $this->errMsg()->addMessage(_('Komentář byl odeslán příliš rychle nebo nebyl odeslán kontrolní čas'));
         } else {
            $isPublic = false;
            if($this->getConfig(self::PARAM_NEW_ARE_PUBLIC) == true
                    OR $this->getConfig(self::PARAM_ADMIN) == true) {
               $isPublic = true;
            }
            $string = $addForm->comment->getValues();
            $this->model->saveComment(htmlspecialchars($addForm->nick->getValues()), $string,
                    $this->getConfig(self::PARAM_ID_CATEGORY), $this->getConfig(self::PARAM_ID_ARTICLE),
                    $addForm->parent->getValues(),$isPublic);

            if($this->getConfig(self::PARAM_NEW_ARE_PUBLIC) == true
                    OR $this->getConfig(self::PARAM_ADMIN) == true) {
               $this->infoMsg()->addMessage(_('Komentář byl uložen'));
            } else {
               $this->infoMsg()->addMessage(_('Komentář byl uložen a čeká na schválení'));
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

      $elemCensore = new Form_Element_SubmitImage('censored');
      $formCensore->addElement($elemCensore);

      if($formCensore->isValid()) {
         $this->model->changeCensored($formCensore->id->getValues());
         $this->infoMsg()->addMessage(_('Komentáři byla změněna cenzůra'));
         $this->pageLink()->reload();
      }
      $this->template->formCensore = $formCensore;

      // zveřejnění
      $formPublic = new Form('comment_public_');
      $elemId = new Form_Element_Hidden('id');
      $elemId->addValidation(new Form_Validator_IsNumber());
      $formPublic->addElement($elemId);

      $elemPublicB = new Form_Element_SubmitImage('public');
      $formPublic->addElement($elemPublicB);

      if($formPublic->isValid()) {
         $this->model->changePublic($formPublic->id->getValues());
         $this->infoMsg()->addMessage(_('Komentáři byla změněna publikace'));
         $this->pageLink()->reload();
      }
      $this->template->formPublic = $formPublic;
   }

   /**
    * Metoda nastaví id šablony pro výpis
    * @param integer -- id šablony (jakékoliv)
    */
   public function mainView() {
      $this->template()->comments = $this->model->getComments($this->getConfig(self::PARAM_ID_CATEGORY),
              $this->getConfig(self::PARAM_ID_ARTICLE), !$this->getConfig(self::PARAM_ADMIN));
      $this->template()->isClosed = $this->getConfig(self::PARAM_CLOSED);
      $this->template()->admin = $this->getConfig(self::PARAM_ADMIN);
      $this->template()->countComments = $this->model->getCountComments($this->getConfig(self::PARAM_ID_CATEGORY),
              $this->getConfig(self::PARAM_ID_ARTICLE));
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