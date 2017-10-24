<?php 
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 */

class Forms_Controller extends Controller {

   /**
 * Kontroler pro zobrazení textu
 */
   public function mainController() {
      $this->checkReadableRights();
      
      $model = new Forms_Model();
      
      $forms = $model
         ->order(array(Forms_Model::COLUMN_NAME => Model_ORM::ORDER_ASC))
         ->records();
      
      $this->view()->forms = $forms;
      
      $formDelete = new Form('form_delete_');
      $eId = new Form_Element_Hidden('id');
      $formDelete->addElement($eId);
      
      $eDelete = new Form_Element_Submit('delete', $this->tr('Smazat'));
      $formDelete->addElement($eDelete);
      
      if($formDelete->isValid()){
         $model->delete($formDelete->id->getValues());
         $this->infoMsg()->addMessage($this->tr('Formulář byl smazán'));
         $this->link()->reload();
      }
      
      $this->view()->formDelete = $formDelete;
      
      $formChangeStatus = new Form('form_status_');
      $eId = new Form_Element_Hidden('id');
      $formChangeStatus->addElement($eId);
      
      $echange = new Form_Element_Submit('change', $this->tr('Změnit stav'));
      $formChangeStatus->addElement($echange);
      
      if($formChangeStatus->isValid()){
         $form = $model->record($formChangeStatus->id->getValues());
         $form->{Forms_Model::COLUMN_ACTIVE} = !$form->{Forms_Model::COLUMN_ACTIVE};
         $model->save($form);
         $this->infoMsg()->addMessage($this->tr('Stav formuláře byl změněn'));
         $this->link()->reload();
      }
      $this->view()->formChangeStatus = $formChangeStatus;
   }
   
   public function createFormController()
   {
      $this->checkWritebleRights();
      
      $form = $this->createEditForm();
      
      if($form->isValid()){
         $struct = json_decode($form->data->getValues());
         
         $modelForms = new Forms_Model();
         
         $formRec = $modelForms->newRecord();
         $formRec->{Forms_Model::COLUMN_NAME} = $form->name->getValues();
         $formRec->{Forms_Model::COLUMN_MSG} = $form->messageSend->getValues();
         $formRec->{Forms_Model::COLUMN_ACTIVE} = $form->active->getValues();
         $formRec->{Forms_Model::COLUMN_SEND_TO_USERS} = null;
         if($form->sysRecipients->getValues() != null && is_array($form->sysRecipients->getValues())){
            $formRec->{Forms_Model::COLUMN_SEND_TO_USERS} = implode(";", $form->sysRecipients->getValues());
         }
         $formRec->{Forms_Model::COLUMN_SEND_TO_MAILS} = $form->otherRecipients->getValues();
         
         $modelForms->save($formRec);
         
         $this->saveFormStruct($formRec->getPK(), $struct);
         
         if(CoreErrors::isEmpty()){
            $this->infoMsg()->addMessage($this->tr('Formulář byl uložen'));
            $this->link()->route()->reload();
         }
      }
      
      $this->view()->form = $form;
   }
   
   public function editFormController()
   {
      $this->checkWritebleRights();
      
      $model = new Forms_Model();
      $modelElements = new Forms_Model_Elements();
      
      $formRec = $model->record($this->getRequest('id'));
      
      if(!$formRec){
         return false;
      }
      
      $formElements = $modelElements
         ->where(Forms_Model_Elements::COLUMN_ID_FORM." = :idf", array('idf' => $formRec->{Forms_Model::COLUMN_ID}))
         ->order(array(Forms_Model_Elements::COLUMN_ORDER => Model_ORM::ORDER_ASC))
         ->records();
         
      $elements = array();   
      foreach ($formElements as $e) {
         $obj = $this->getBaseElementObj();
         $obj->name = $e->{Forms_Model_Elements::COLUMN_NAME};
         $obj->label = $e->{Forms_Model_Elements::COLUMN_LABEL};
         $obj->note = $e->{Forms_Model_Elements::COLUMN_NOTE};
         $obj->type = $e->{Forms_Model_Elements::COLUMN_TYPE};
         $obj->required = $e->{Forms_Model_Elements::COLUMN_REQUIRED};
         $obj->value = $e->{Forms_Model_Elements::COLUMN_VALUE};
         if($e->{Forms_Model_Elements::COLUMN_IS_MULTIPLE}){
            $obj->value = unserialize($e->{Forms_Model_Elements::COLUMN_VALUE});
            $obj->isMultiple = $e->{Forms_Model_Elements::COLUMN_IS_MULTIPLE};
         }
         $obj->id = $e->{Forms_Model_Elements::COLUMN_ID};
         if($e->{Forms_Model_Elements::COLUMN_OPTIONS} != null){
            $obj->options = unserialize($e->{Forms_Model_Elements::COLUMN_OPTIONS});
         }
         $obj->order = $e->{Forms_Model_Elements::COLUMN_ORDER};
         $obj->validator = $e->{Forms_Model_Elements::COLUMN_VALIDATOR};
         $elements[] = $obj;
      }   
      
      $form = $this->createEditForm($formRec, $elements);
      
      if($form->isValid()){
         $struct = json_decode($form->data->getValues());
         
         $modelForms = new Forms_Model();
         $modelElements = new Forms_Model_Elements();
         
         $formRec->{Forms_Model::COLUMN_NAME} = $form->name->getValues();
         $formRec->{Forms_Model::COLUMN_MSG} = $form->messageSend->getValues();
         $formRec->{Forms_Model::COLUMN_ACTIVE} = $form->active->getValues();
         $formRec->{Forms_Model::COLUMN_SEND_TO_USERS} = null;
         if($form->sysRecipients->getValues() != null && is_array($form->sysRecipients->getValues())){
            $formRec->{Forms_Model::COLUMN_SEND_TO_USERS} = implode(";", $form->sysRecipients->getValues());
         }
         $formRec->{Forms_Model::COLUMN_SEND_TO_MAILS} = $form->otherRecipients->getValues();
         
         $modelForms->save($formRec);
         
         $this->saveFormStruct($formRec->{Forms_Model::COLUMN_ID}, $struct, explode(';', $form->delete->getValues() ));
         
         if(CoreErrors::isEmpty()){
            $this->infoMsg()->addMessage($this->tr('Formulář byl uložen'));
            $this->link()->route()->reload($this->getRequestParam('back'));
         }
      }
      
      $this->view()->form = $form;
   }

   protected function saveFormStruct($formId, $elements, $deleteIds = array())
   {
      $modelElements = new Forms_Model_Elements();
      $existNames = array();
      foreach ($elements as $element) {
         if($element->id == null){
            $elemRec = $modelElements->newRecord();
            $name = $element->name;
            if($name == null){
               $name = $element->label;
            }
            $sname = substr(lcfirst(implode('', array_map('ucfirst', explode('-', Utils_Url::toUrlKey($name))))), 0, 40);
         } else {
            $elemRec = $modelElements->record($element->id);
            $sname = Utils_Url::toUrlKey($element->name);
            if($element->name == null){
               $sname = Utils_Url::toUrlKey($element->label);
            }
         }
         $basename = $sname;
         $i = 1;
         while (isset($existNames[$sname])) {
            $sname = $basename.'_'.$i;
            $i++;
         }
         $existNames[$sname] = true;
         $elemRec->{Forms_Model_Elements::COLUMN_ID_FORM} = $formId;
         $elemRec->{Forms_Model_Elements::COLUMN_NAME} = $sname;
         $elemRec->{Forms_Model_Elements::COLUMN_NOTE} = $element->note;
         $elemRec->{Forms_Model_Elements::COLUMN_LABEL} = $element->label;
         $elemRec->{Forms_Model_Elements::COLUMN_TYPE} = $element->type;
         $elemRec->{Forms_Model_Elements::COLUMN_REQUIRED} = $element->required;
         
         if(isset($element->value)){
            if( $element->type == "select" || $element->type == "selectcountry" ){
               $elemRec->{Forms_Model_Elements::COLUMN_VALUE} = serialize($element->value);
            } else if($element->type == "checkbox" || $element->type == "radio"){
               if($element->value == "on"){
                  $elemRec->{Forms_Model_Elements::COLUMN_VALUE} = true;
               } else {
                  $elemRec->{Forms_Model_Elements::COLUMN_VALUE} = false;
               }
            } else {
               $elemRec->{Forms_Model_Elements::COLUMN_VALUE} = $element->value;
            }
         }
         if(!empty($element->options) ){
            $elemRec->{Forms_Model_Elements::COLUMN_OPTIONS} = serialize($element->options);
         }
         $elemRec->{Forms_Model_Elements::COLUMN_IS_MULTIPLE} = $element->isMultiple;
         $elemRec->{Forms_Model_Elements::COLUMN_ORDER} = $element->order;
         $elemRec->{Forms_Model_Elements::COLUMN_VALIDATOR} = $element->validator;
          
         try {
            $modelElements->save($elemRec);
         } catch (PDOException $e) {
            new CoreErrors($e);
         }
      }
      // delete elements
      foreach ($deleteIds as $id) {
         try {
            $modelElements->delete($id);
         } catch (PDOException $e) {
            new CoreErrors($e);
         }
      }
      
   }

   protected function createEditForm($formRec = null, $formElemets = array(), Url_Link $cancelLink = null)
   {
      $form = new Form('form_cr_');
      
      $elemName = new Form_Element_Text('name', $this->tr('Název formuláře'));
      $elemName->setValues('Název formuláře');
      $elemName->addValidation(new Form_Validator_NotEmpty());
      $form->addElement($elemName);
      
      $elemMessageSend = new Form_Element_TextArea('messageSend', $this->tr('Zpráva po odeslání'));
      $elemMessageSend->setValues('Formulář byl odeslán');
      $form->addElement($elemMessageSend);
      
      if(VVE_DEBUG_LEVEL > 1){
         $elemFormData = new Form_Element_TextArea('data', $this->tr('Data formuláře'));
      } else {
         $elemFormData = new Form_Element_Hidden('data', $this->tr('Data formuláře'));
      }
      $elemFormData->addValidation(new Form_Validator_NotEmpty());

      if(empty($formElemets)){
         $textLine = $this->getBaseElementObj();
         $textLine->name = "elem-1";
         $textLine->note = "";
         $textLine->label = $this->tr('Název');
         $textLine->type = "text";
         $textLine->required = false;
         $textLine->value = null;
         $textLine->id = null;
         $textLine->options = array();
         $textLine->order = 1;
         $textLine->validator = null;
         $formElemets[] = $textLine;
      
         $button = $this->getBaseElementObj();
         $button->name = "submit";
         $button->note = "";
         $button->label = $this->tr('Odeslat');
         $button->type = "submit";
         $button->required = false;
         $button->value = null;
         $button->id = null;
         $button->options = array();
         $button->order = 2;
         $button->validator = null;
         $formElemets[] = $button;
      }
      
      $elemFormData->setValues(json_encode($formElemets));
      $form->addElement($elemFormData);
      
      $elemDeleted = new Form_Element_Hidden('delete', $this->tr('Smazat elementy'));
      $form->addElement($elemDeleted);
      
      $elemActive = new Form_Element_Checkbox('active', $this->tr('Formulář je aktivní'));
      $elemActive->setValues(true);
      $form->addElement($elemActive);
      
      
      $elemSysRecipients = new Form_Element_Select('sysRecipients', 'Odeslat na e-mail uživatelů');
      $elemSysRecipients->setSubLabel($this->tr('Můžete definovat také e-mailové adresy v textovém poli níže'));
      // načtení uživatelů
      $modelUsers = new Model_Users();
      $users = $modelUsers->usersForThisWeb(true)->records(PDO::FETCH_OBJ);
      $usersIds = array();
      foreach ($users as $user) {
         if($user->{Model_Users::COLUMN_MAIL} != null){
            $usersIds[$user->{Model_Users::COLUMN_NAME} ." ".$user->{Model_Users::COLUMN_SURNAME}
              .' ('.$user->{Model_Users::COLUMN_USERNAME}.') - '.$user->{Model_Users::COLUMN_MAIL}] = $user->{Model_Users::COLUMN_ID};
         }
      }
      $elemSysRecipients->setOptions($usersIds);
      $elemSysRecipients->setMultiple();
      $elemSysRecipients->html()->setAttrib('size', 4);
      $form->addElement($elemSysRecipients);
      
      $elemOtherRecipients = new Form_Element_TextArea('otherRecipients', $this->tr('Odeslat také na e-maily'));
      $elemOtherRecipients->setSubLabel($this->tr('E-mailové adresy oddělené středníkem'));
      $form->addElement($elemOtherRecipients);
      
      $elemSave = new Form_Element_SaveCancel('save');
      $form->addElement($elemSave);
      
      if($formRec != null){
         $form->name->setValues($formRec->{Forms_Model::COLUMN_NAME});
         $form->messageSend->setValues($formRec->{Forms_Model::COLUMN_MSG});
         $form->active->setValues($formRec->{Forms_Model::COLUMN_ACTIVE});
         if($formRec->{Forms_Model::COLUMN_SEND_TO_USERS} != null){
            $selecteUsers = explode(";", $formRec->{Forms_Model::COLUMN_SEND_TO_USERS});
            $form->sysRecipients->setValues($selecteUsers);
         }
         $form->otherRecipients->setValues($formRec->{Forms_Model::COLUMN_SEND_TO_MAILS});
      }
      
      if($form->isSend() && $form->save->getValues() == false){
         if($cancelLink != null){
            $cancelLink->reload();
         }
         $this->link()->route()->reload();
      }
      
      return $form;
   }
   
   private function getBaseElementObj()
   {
      $elem = new Object();
      $elem->name = "elem-1";
      $elem->note = "";
      $elem->label = $this->tr('Název');
      $elem->type = "text";
      $elem->required = false;
      $elem->value = null;
      $elem->id = null;
      $elem->options = array();
      $elem->order = 1;
      $elem->validator = null;
      $elem->isMultiple = false;
      return $elem;
   }
   
   public function previewFormController()
   {
      $this->checkControllRights();
      
      $model = new Forms_Model();
      
      $form = $model->record($this->getRequest('id'));
      
      if($form == false){
         return false;
      }
      
      $formPreview = $this->createDynamicForm($form->{Forms_Model::COLUMN_ID});
      
      $elemSendToMail = new Form_Element_Checkbox('sendToMail', $this->tr('Odeslat e-mailem'));
      $formPreview->addElement($elemSendToMail);
      
      if($formPreview->isValid()){
         $sendData = array();
         foreach ($formPreview as $elem) {
            if(($elem instanceof Form_Element_Submit) == false
               && ($elem instanceof Form_Element_Hidden) == false){
               $sendData[$elem->getLabel()] = $elem->getValues();
            }
         }
         $this->view()->sendData = $sendData;
         
         if($formPreview->sendToMail->getValues() == true){
            self::sendMail($formPreview, $form);
         }
         
         $this->infoMsg()->addMessage($form->{Forms_Model::COLUMN_MSG}, false);
      }
      
      $this->view()->form = $form;
      $this->view()->formPreview = $formPreview;
   }


   protected static function createDynamicForm($idForm)
   {
      if(!function_exists('errHandlerTmp')){
         function errHandlerTmp (){};
      }
      $modelElements = new Forms_Model_Elements();
      $elements = $modelElements
         ->where(Forms_Model_Elements::COLUMN_ID_FORM." = :idf", array('idf' => (int)$idForm))
         ->order(array(Forms_Model_Elements::COLUMN_ORDER => Model_ORM::ORDER_ASC))
         ->records();
      
      $form = new Form('dnymic_form_'.$idForm);
      $link = new Url_Link();
      $form->setAction($link
         ->param(Url_Request::URL_TYPE_MODULE_PROCESS, 'Forms')
         ->param(Url_Request::URL_TYPE_MODULE_PROCESS_DO, 'processFrom')
         ->param('formid', $idForm)
         );
      $formGrp = null;
      foreach ($elements as $e) {
         $formElement = new Form_Element_Hidden('not');
         // vytvoření elementu
         switch ($e->{Forms_Model_Elements::COLUMN_TYPE}) {
            case 'submit':
               $formElement = new Form_Element_Submit($e->{Forms_Model_Elements::COLUMN_NAME}, $e->{Forms_Model_Elements::COLUMN_LABEL});
               break;
            case 'label':
               $formGrp = $form->addGroup(vve_cr_url_key($e->{Forms_Model_Elements::COLUMN_NAME}), $e->{Forms_Model_Elements::COLUMN_LABEL});
               break;
            case 'checkbox':
               $formElement = new Form_Element_Checkbox($e->{Forms_Model_Elements::COLUMN_NAME}, $e->{Forms_Model_Elements::COLUMN_LABEL});
               break;
            case 'select':
               $formElement = new Form_Element_Select($e->{Forms_Model_Elements::COLUMN_NAME}, $e->{Forms_Model_Elements::COLUMN_LABEL});
               $opt = unserialize($e->{Forms_Model_Elements::COLUMN_OPTIONS});
               foreach ($opt as $value) {
                  $formElement->setOptions(array($value => $value), true);
               }
               $formElement->setMultiple($e->{Forms_Model_Elements::COLUMN_IS_MULTIPLE});
               break;
            case 'selectcountry':
               $formElement = new Form_Element_CountrySelect($e->{Forms_Model_Elements::COLUMN_NAME}, $e->{Forms_Model_Elements::COLUMN_LABEL});
               break;
            case 'radio':
               $formElement = new Form_Element_Radio($e->{Forms_Model_Elements::COLUMN_NAME}, $e->{Forms_Model_Elements::COLUMN_LABEL});
               $opt = unserialize($e->{Forms_Model_Elements::COLUMN_OPTIONS});
               foreach ($opt as $value) {
                  $formElement->setOptions(array($value => $value), true);
               }
               break;
            case 'textarea':
               $formElement = new Form_Element_TextArea($e->{Forms_Model_Elements::COLUMN_NAME}, $e->{Forms_Model_Elements::COLUMN_LABEL});
               break;
            case 'file':
               $formElement = new Form_Element_File($e->{Forms_Model_Elements::COLUMN_NAME}, $e->{Forms_Model_Elements::COLUMN_LABEL});
               if($e->{Forms_Model_Elements::COLUMN_VALIDATOR} == 'imgonly'){
                  $formElement->addValidation(new Form_Validator_FileExtension(Form_Validator_FileExtension::IMG));
               } else if($e->{Forms_Model_Elements::COLUMN_VALIDATOR} == 'doconly'){
                  $formElement->addValidation(new Form_Validator_FileExtension(Form_Validator_FileExtension::DOC));
               } else {
                  $formElement->addValidation(new Form_Validator_FileExtension($e->{Forms_Model_Elements::COLUMN_VALIDATOR}));
               }
               
               break;
            case 'captcha':
               $formElement = new Form_Element_Captcha($e->{Forms_Model_Elements::COLUMN_NAME}, $e->{Forms_Model_Elements::COLUMN_LABEL});
               break;
            case 'text':
            default:
               $formElement = new Form_Element_Text($e->{Forms_Model_Elements::COLUMN_NAME}, $e->{Forms_Model_Elements::COLUMN_LABEL});
               
               break;
         }
         // validace
         if($e->{Forms_Model_Elements::COLUMN_REQUIRED} == true){
            $formElement->addValidation(new Form_Validator_NotEmpty());
         }
         $formElement->setSubLabel($e->{Forms_Model_Elements::COLUMN_NOTE});
         
         switch ($e->{Forms_Model_Elements::COLUMN_VALIDATOR}) {
            case "mail":
               $formElement->addValidation(new Form_Validator_Email());
               break;
            case "phone":
               $formElement->addValidation(new Form_Validator_Regexp(Form_Validator_Regexp::REGEXP_PHONE_CZSK));
               break;
            case "url":
               $formElement->addValidation(new Form_Validator_Url());
               break;
         }
         
         // výchozí hodnota
         if($e->{Forms_Model_Elements::COLUMN_VALUE} != null){
            set_error_handler('errHandlerTmp');
            $data = @unserialize($e->{Forms_Model_Elements::COLUMN_VALUE});
            restore_error_handler();
            if($data !== false || $e->{Forms_Model_Elements::COLUMN_VALUE} === 'b:0;'){
               $formElement->setValues(unserialize($e->{Forms_Model_Elements::COLUMN_VALUE}));
            } else {
               $formElement->setValues($e->{Forms_Model_Elements::COLUMN_VALUE});
            }

         }
         
         $form->addElement($formElement, $formGrp);
      }
      
      return $form;
   }
   
   // metodu volat jako statickou metodu kontroleru, pokud je v parametrech $_GET procesAction
   public static function processFrom()
   {
      if(!isset($_GET['formid'])){
         return;
      }
      
      $form = self::createDynamicForm((int)$_GET['formid']);
      $link = new Url_Link();
      
      
      if($form->isValid()){
         
         $params = array(
            'pagename' => null,
            'pagelink' => (string)$link,
            'pageinfo' => Category::getSelectedCategory()->getName(),
            'categoryname' => Category::getSelectedCategory()->getName(),
            'categorylink' => (string)$link->clear(),
            'redirectlink' => $link->clear(),
         );
         
         $formModel = new Forms_Model();
         $fRec = $formModel->record((int)$_GET['formid']);
         // update send counter
         $fRec->{Forms_Model::COLUMN_SENDED} = $fRec->{Forms_Model::COLUMN_SENDED}+1;
         $fRec->save();
         // semd to mail
         $mail = true;
         if($mail){
            self::sendMail($form, $fRec, array(
               '{PAGE_NAME}' => $params['pagename'],
               '{PAGE_LINK}' => $params['pagelink'],
               '{CATEGORY_NAME}' => $params['categoryname'],
               '{CATEGORY_LINK}' => $params['categorylink'],
               '{PAGE_INFO}' => $params['pageinfo'],
            ));
         }
         AppCore::getInfoMessages()->addMessage($fRec->{Forms_Model::COLUMN_MSG});
         $link->rmParam(Url_Request::URL_TYPE_MODULE_PROCESS)->rmParam(Url_Request::URL_TYPE_MODULE_PROCESS_DO)->redirect();
      }
   }
   
   /**
    * Metoda vytvoří šablonu pro formulář
    * @param unknown_type $idForm
    * @param unknown_type $params
    * @param unknown_type $onlyActive
    * @return Forms_Template
    */
   public static function dynamicForm($idForm, $params = array(), $onlyActive = true)
   {
      $link = new Url_Link();
      $tpl = new Forms_Template();
      
      $params = array_merge(array(
         'pagename' => null,
         'pagelink' => (string)$link,
         'categoryname' => Category::getSelectedCategory()->getName(),
         'categorylink' => (string)$link->clear(),
         'redirectlink' => $link->clear(),
      ), $params);
      
      $formModel = new Forms_Model();
      if($onlyActive){
         $formModel->where(Forms_Model::COLUMN_ID." = :idf AND ".Forms_Model::COLUMN_ACTIVE." = 1", array('idf' => $idForm));
         $fRec = $formModel->record();
      } else {
         $fRec = $formModel->record($idForm);
      }
      if($fRec == false){
         return null;
      }
      $form = self::createDynamicForm($fRec->{Forms_Model::COLUMN_ID});
      $tpl->dynamicFormRecord = $fRec;
      $tpl->dynamicForm = $form;
      $form->isValid();
      return $tpl;
   }
   
   /**
    * Filtrace obsahu a dosazení formulářů
    * @param string $cnt
    * @return string
    * @deprecated - používat radši dynamické generování content filtrů
    */
   public static function contentFilter($cnt) {
      $cnt = preg_replace_callback('/\{[A-Z]+:([0-9]+)\}/', function($matches){
            return Forms_Controller::dynamicForm($matches[1]);
         }, $cnt);
      return $cnt;
   }
   
   /**
    * Metoda vytvoří a odešle email
    * @param Form $form
    * @param type $formRec
    * @param type $replacements
    * @param string $msg 
    * 
    * Přepisové hosnoty:
    * '{PAGE_INFO}' = odkaz na kategorii a podstránku,
    * '{PAGE_LINK}' = odkaz podstránky,
    * '{PAGE_NAME}' = název podstránky,
    * '{CATEGORY_LINK}' = odkaz na kategorii,
    * '{CATEGORY_NAME}' = název kategorie,
    * '{WEB_LINK}' = odkaz na web,
    * '{WEB_NAME}' = název stránek,
    * '{FORM_NAME}' = název formuláře,
    * '{DATE}' = aktuální datum,
    * '{TIME}' = aktuální čas,
    * '{DATA}' = odeslaná data,
    * '{IP_ADDRESS}' = IP adresa odesílatele,
    * '{FOOTER_INFO}' = informace v zápatí,
    * '{GENERATE_WARNING}' = varování o automatickém generování,
    */
   protected static function sendMail(Form $form, $formRec, $replacements = array(), $msg = null)
   {
      $link = new Url_Link();
      $mail = new Email(true);
      $tr = new Translator_Module('forms');
      
      $mail->setSubject($formRec->{Forms_Model::COLUMN_NAME});
      
      if($msg == null){
         $msg = '<h1>'.$tr->tr('Ze stránek {WEB_LINK} byl odeslán formulář "{FORM_NAME}"').'</h1>'
            . '<p>'.$tr->tr('Stránka: {PAGE_INFO}.').'</p>'
            . '<p>'.$tr->tr('Odesláno: <strong>{DATE} {TIME}</strong>.').'</p>'
            . '<hr />'
            . '<h2>'.$tr->tr('Odeslaná data').': </h2>'
            . '<p><table cellpadding="5" border="1">'
            . '{DATA}'
            . ' </table></p>'
            . '<hr />'
            . '<p>{FOOTER_INFO}</p>';
      }
      
      $data = null;
      $sendFromEmail = null;
      $removeFiles = array();
      foreach ($form as $element) {
         // přeskakování elementů které nejsou
         if( $element->getName(false) == 'sendToMail' 
            || $element instanceof Form_Element_Submit || $element instanceof Form_Element_Hidden ){
            continue;
         }
         
         $val = null;
         if(is_array($element->getValues())){
            foreach ($element->getValues() as $v) {
               $val .= htmlspecialchars($v)."<br />";
            }
         } else if(is_bool($element->getValues())){
            $val = $element->getValues() == true ? $tr->tr('Ano') : $tr->tr('Ne');
         } else {
            $val = htmlspecialchars($element->getValues());
         }
         
         if($element instanceof Form_Element_File){
            $val = $tr->tr('Nevloženo');
            if($element->getValues() != null){
               $file = $element->createFileObject();
               $val = $file->getName();
               $filename = (string)$file->rename($file->getName());
               $mail->addAttachment($filename);
               $removeFiles[] = $file;
            }
         }
         
         if($val == null){
            $val = $tr->tr('Nevyplněno');
         }
         $data .= '<tr>';
         $data .= '<th style="text-align: left;">'.$element->getLabel().'</th>';
         if(filter_var($val, FILTER_VALIDATE_EMAIL)){
            $data .= '<td><a href="mailto:'.$val.'">'.nl2br($val).'</a></td>';
         } else if(filter_var($val, FILTER_VALIDATE_URL)){
            $data .= '<td><a href="'.$val.'">'.nl2br($val).'</a></td>';
         } else {
            $data .= '<td>'.nl2br($val).'</td>';
         }
         $data .= '</tr>';
         
         if($element instanceof Form_Element_Text
            && $element->hasValidator("Form_Validator_Email")){
            $sendFromEmail = $element->getValues();
         }
      }
      
      $replacements = array_merge(array(
         '{PAGE_INFO}' => null,
         '{PAGE_LINK}' => null,
         '{PAGE_NAME}' => null,
         '{CATEGORY_LINK}' => (string)$link,
         '{CATEGORY_NAME}' => Category::getSelectedCategory()->getName(),
         '{PAGE_INFO}' => null,
         '{WEB_LINK}' => '<a href="'.$link->clear(true).'">{WEB_NAME}</a>',
         '{WEB_NAME}' => VVE_WEB_NAME,
         '{FORM_NAME}' => $formRec->{Forms_Model::COLUMN_NAME},
         '{DATE}' => vve_date("%x"),
         '{TIME}' => vve_date("%X"),
         '{DATA}' => $data,
         '{IP_ADDRESS}' => $_SERVER['REMOTE_ADDR'],
         '{FOOTER_INFO}' => null,
         '{GENERATE_WARNING}' => $tr->tr('Tento e-mail je genrován automaticky. Neodpovídejte na něj!'),
      ), $replacements);
      
      $pageInfo = null;
      if(!isset($replacements['{PAGE_INFO}']) || $replacements['{PAGE_INFO}'] == null){
         $replacements['{PAGE_INFO}'] = '<a href="{CATEGORY_LINK}">{CATEGORY_NAME}</a>';
         if(isset($replacements['{PAGE_NAME}']) && $replacements['{PAGE_NAME}'] != null){
            $replacements['{PAGE_INFO}'] .= ' / <a href="{PAGE_LINK}">{PAGE_NAME}</a>';
         }
      }
      
      $mail->setReplacements($replacements);   
      $mail->setContent( Email::getBaseHtmlMail($msg) );
      
      if($sendFromEmail != null){
         $mail->setFrom($sendFromEmail);
      }
      
      $recipients = array();
      if($formRec->{Forms_Model::COLUMN_SEND_TO_MAILS}){ // pokud je prázdný výtahneme nastavené maily
         $recipients = explode(';', $formRec->{Forms_Model::COLUMN_SEND_TO_MAILS});
      }
      
      if($formRec->{Forms_Model::COLUMN_SEND_TO_USERS}){ // pokud je prázdný výtahneme nastavené maily
         // maily adminů - z uživatelů
         $usersId = explode(':', $formRec->{Forms_Model::COLUMN_SEND_TO_USERS});
         $modelusers = new Model_Users();
         foreach ($usersId as $id) {
            $user = $modelusers->record($id);
            if($user->{Model_Users::COLUMN_MAIL} == null){ continue; }
            $mailAddress = explode(';', $user->{Model_Users::COLUMN_MAIL});
            $recipients[$mailAddress[0]] = $user->{Model_Users::COLUMN_NAME}." ".$user->{Model_Users::COLUMN_SURNAME};
         }
      }
      $mail->addAddress($recipients);
      $mail->send();
      
      foreach ($removeFiles as $file) {
         $file->delete();
      }
   }
}

