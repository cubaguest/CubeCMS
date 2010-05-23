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

   protected $config = array(self::PARAM_ID_ARTICLE => 0,
                             self::PARAM_ID_CATEGORY => 0,
                             self::PARAM_NEW_ARE_PUBLIC => true,
                             self::PARAM_CLOSED => false,
                             self::PARAM_ADMIN => false,
                             self::PARAM_ALLOW_TAGS => '<a><br><em>><strong><sub><sup><ul><li><ol>'
      );

   private $model = null;
   
   /**
    * Metoda inicializace, je spuštěna pří vytvoření objektu
    */
   protected function init(){
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
      $addForm = new Form('comment_');
      $elemNick = new Form_Element_Text('nick', _('Nick'));
      $elemNick->addValidation(new Form_Validator_NotEmpty());
      if(Auth::isLogin()){
         $elemNick->setValues(Auth::getUserName());
      }

      $addForm->addElement($elemNick);

      $elemText = new Form_Element_TextArea('comment', _('komentář'));
      $elemText->addValidation(new Form_Validator_NotEmpty());
      $elemText->addValidation(new Form_Validator_Length(10, 200));
      $addForm->addElement($elemText);

      $elemRe = new Form_Element_Hidden('parent');
      $elemRe->setValues(0);
      $addForm->addElement($elemRe);

      $elemAdd = new Form_Element_Submit('add', _('Uložit'));
      $addForm->addElement($elemAdd);
      if($addForm->isValid()){
         $isPublic = false;
         if($this->getConfig(self::PARAM_NEW_ARE_PUBLIC) == true
                 OR $this->getConfig(self::PARAM_ADMIN) == true){
            $isPublic = true;
         }
         // odstranění nepovolených tagů
         $string = strip_tags($addForm->comment->getValues(), $this->getConfig(self::PARAM_ALLOW_TAGS));

         $this->model->saveComment(htmlspecialchars($addForm->nick->getValues()), $string,
                 $this->getConfig(self::PARAM_ID_CATEGORY), $this->getConfig(self::PARAM_ID_ARTICLE), 
                 $addForm->parent->getValues(),$isPublic);

         if($this->getConfig(self::PARAM_NEW_ARE_PUBLIC) == true
                 OR $this->getConfig(self::PARAM_ADMIN) == true){
            $this->infoMsg()->addMessage(_('Komentář byl uložen'));
         } else {
            $this->infoMsg()->addMessage(_('Komentář byl uložen a čeká na schválení'));
         }
         $this->pageLink()->reload();
      }
      $this->template()->formAdd = $addForm;

      // cenzůra
      $formCensore = new Form('comment_');
      $elemId = new Form_Element_Hidden('id');
      $elemId->addValidation(new Form_Validator_IsNumber());
      $formCensore->addElement($elemId);

      $elemCensore = new Form_Element_SubmitImage('censored');
      $formCensore->addElement($elemCensore);

      if($formCensore->isValid()){
         $this->model->changeCensored($formCensore->id->getValues());
         $this->infoMsg()->addMessage(_('Komentář byl cenzůrován'));
         $this->pageLink()->reload();
      }
      $this->template->formCensore = $formCensore;

      // zveřejnění
      $formPublic = new Form('comment_');
      $elemId = new Form_Element_Hidden('id');
      $elemId->addValidation(new Form_Validator_IsNumber());
      $formPublic->addElement($elemId);

      $elemPublicB = new Form_Element_SubmitImage('public');
      $formPublic->addElement($elemPublicB);

      if($formPublic->isValid()){
         $this->model->changePublic($formPublic->id->getValues());
         $this->infoMsg()->addMessage(_('Komentář byl publikován'));
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
      if($this->getConfig(self::PARAM_CLOSED) == false){
         $this->template()->addTplFile('comments/add.phtml');
      }
   }

   /**
    * Metoda vrací počet komentářů
    * @return int
    */
   public function getCountComments(){
      return $this->model->getCountComments($this->getConfig(self::PARAM_ID_CATEGORY),
              $this->getConfig(self::PARAM_ID_ARTICLE));
   }
}
?>