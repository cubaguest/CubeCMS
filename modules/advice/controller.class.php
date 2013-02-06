<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 */

class Advice_Controller extends Controller {
   const PARAM_COLORS = 'pc';
   const PARAM_ALLOW_DRUGS = 'ad';
   const PARAM_CONDITION_ID_CAT = 'cidc';
   const PARAM_ADMIN_RECIPIENTS = 'admin_rec';
   const PARAM_OTHER_RECIPIENTS = 'other_rec';
   const PARAM_ANSWER_FOOTER_TEXT = 'ans_f_text';

   protected $answerColors = array();


   protected function init()
   {
      parent::init();
      $defaultColors = array(
         '0' => $this->tr('žádná'), 
         "fffd54" => $this->tr("žlutá"),
         "ff546c" => $this->tr("růžová"), 
         "ce0000" => $this->tr("červená"),
         "bde925" => $this->tr("zelená"), 
         "54ebf1" => $this->tr("modrá"),
         "b436f6" => $this->tr("fialová")
      );
      $this->answerColors = $this->category()->getParam(self::PARAM_COLORS, $defaultColors);
      $this->view()->answerColors = $this->answerColors;
   }

   /**
    * Kontroler pro zobrazení textu
    */
   public function mainController() 
   {
      $this->checkReadableRights();
      
      $model = new Advice_Model();
      
      $model->groupBy(array(Advice_Model::COLUMN_ID))
         ->order(array(Advice_Model::COLUMN_DATE_ADD => Model_ORM::ORDER_DESC));
      
      $whereStrings = array(Advice_Model::COLUMN_IS_PUBLIC.' = 1', Advice_Model::COLUMN_ANSWER.' IS NOT NULL');
      $whereValues = array();
      
      if($this->getRequestParam('ac', 0) != 0){ // filtr kategorie
         $model->join(Advice_Model::COLUMN_ID, array('pradvcon_cat' => "Advice_Model_Connections"), Advice_Model_Connections::COLUMN_ID_QUESTION, null, Model_ORM::JOIN_LEFT);
         array_push($whereStrings, 'pradvcon_cat.'.Advice_Model_Connections::COLUMN_ID_CAT.' = :idc');
         $whereValues = array_merge($whereValues, array('idc' => $this->getRequestParam('ac')));
      }
      
      if($this->getRequestParam('ad', 0) != 0){ // filtr drogy
         $model->join(Advice_Model::COLUMN_ID, array('pradvcon_drug' => "Advice_Model_Connections"), Advice_Model_Connections::COLUMN_ID_QUESTION, null, Model_ORM::JOIN_LEFT);
         array_push($whereStrings, 'pradvcon_drug.'.Advice_Model_Connections::COLUMN_ID_CAT.' = :idd');
         $whereValues = array_merge($whereValues, array('idd' => $this->getRequestParam('ad')));
      }
      
      if($this->getRequestParam('as', null) != null){ // fulltext
         array_push($whereStrings, 'MATCH('.Advice_Model::COLUMN_NAME.', '.Advice_Model::COLUMN_QUESTION.', '.Advice_Model::COLUMN_ANSWER.') AGAINST (:search IN BOOLEAN MODE)');
         $model->columns(array('*', 'relevation' => 
            ' 5 * MATCH('.Advice_Model::COLUMN_NAME.') AGAINST (:ss1)'
            .' + MATCH('.Advice_Model::COLUMN_QUESTION.') AGAINST (:ss2)'
            .' + MATCH('.Advice_Model::COLUMN_ANSWER.') AGAINST (:ss3)'
            ));
         $model->setBindValues(array(
            'ss1' => $this->getRequestParam('as'), 
            'ss2' => $this->getRequestParam('as'), 
            'ss3' => $this->getRequestParam('as')
            ));
         
         $whereValues = array_merge($whereValues, array('search' => $this->getRequestParam('as'),));
         
         $model->order(array('relevation' => Model_ORM::ORDER_DESC), true);
      }
      
      $model->where(implode(' AND ', $whereStrings) , $whereValues);
      $scrollComponent = new Component_Scroll();
      $scrollComponent->setConfig(Component_Scroll::CONFIG_CNT_ALL_RECORDS, $model->count());
      $scrollComponent->setConfig(Component_Scroll::CONFIG_RECORDS_ON_PAGE, $this->category()->getParam('scroll', 25));
      $model->limit($scrollComponent->getStartRecord(), $scrollComponent->getRecordsOnPage());
      
      $questions = $model->records();   
      
      $this->view()->scrollComp = $scrollComponent;
      $this->view()->questions = $questions;
      
      $countCommon = $model->where(Advice_Model::COLUMN_ID_CATEGORY.' = :idc AND '.Advice_Model::COLUMN_IS_COMMON.' = 1', array('idc' => $this->category()->getId()))->count();
      $rows = 2;
      if($countCommon > 0){
         $fromRow = rand(0, $countCommon-$rows);
         $this->view()->questionsCommon = $model->limit($fromRow, $rows)->records();
      }
      
      // načtení drog a témat
      $this->loadCats();
      
   }
   
   public function answersController() 
   {
      $this->checkWritebleRights();
   }
   
   public function questionsListController()
   {
      $this->checkWritebleRights();
      // objekt komponenty JGrid
      $jqGrid = new Component_JqGrid();
      $jqGrid->request()->setDefaultOrderField(Advice_Model::COLUMN_DATE_ADD, 'desc');
      $model = new Advice_Model();
      $model->order(array($jqGrid->request()->orderField => $jqGrid->request()->order));
      // search
      if ($jqGrid->request()->isSearch()) {
         switch ($jqGrid->request()->searchType()) {
            case Component_JqGrid_Request::SEARCH_EQUAL:
               $model->where(Advice_Model::COLUMN_ID_CATEGORY.' = :idc AND '.$jqGrid->request()->searchField().' = :str',
                  array('str' => $jqGrid->request()->searchString(), 'idc' => (int)$this->category()->getId()));
               break;
            case Component_JqGrid_Request::SEARCH_NOT_EQUAL:
               $model->where(Advice_Model::COLUMN_ID_CATEGORY.' = :idc AND '.$jqGrid->request()->searchField().' != :str',
                  array('str' => $jqGrid->request()->searchString(), 'idc' => (int)$this->category()->getId()));
               break;
            case Component_JqGrid_Request::SEARCH_NOT_CONTAIN:
               $model->where(Advice_Model::COLUMN_ID_CATEGORY.' = :idc AND '.$jqGrid->request()->searchField().' NOT LIKE :str',
                  array('str' => '%'.$jqGrid->request()->searchString().'%', 'idc' => (int)$this->category()->getId()));
               break;
            case Component_JqGrid_Request::SEARCH_CONTAIN:
            default:
               $model->where(Advice_Model::COLUMN_ID_CATEGORY.' = :idc AND '.$jqGrid->request()->searchField().' LIKE :str',
                  array('str' => '%'.$jqGrid->request()->searchString().'%', 'idc' => (int)$this->category()->getId()));
               break;
         }

//         $jqGrid->respond()->setRecords($modelUsers->count());
//         $users = $modelUsers->limit(($jqGrid->request()->page - 1) * $jqGrid->respond()->getRecordsOnPage(), $jqGrid->request()->rows)
//            ->records();
      } else { // list
         $model->where(Advice_Model::COLUMN_ID_CATEGORY, (int)$this->category()->getId());
      }
      $jqGrid->respond()->setRecords($model->count());
      
      $questions = $model->limit(($jqGrid->request()->page - 1) * $jqGrid->respond()->getRecordsOnPage(), $jqGrid->request()->rows)->records();
      // out
      foreach ($questions as $question) {
//       array_push($jqGrid->respond()->rows, 
//       array('id' => $tax->{Shop_Model_Tax::COLUMN_ID},
//          Shop_Model_Tax::COLUMN_NAME => $tax->{Shop_Model_Tax::COLUMN_NAME},
//          Shop_Model_Tax::COLUMN_VALUE => $tax->{Shop_Model_Tax::COLUMN_VALUE},
//       ));
         array_push($jqGrid->respond()->rows, array(
            'id' => $question->{Advice_Model::COLUMN_ID},
            Advice_Model::COLUMN_ID => $question->{Advice_Model::COLUMN_ID},
            Advice_Model::COLUMN_QUESTION => str_replace(array("\n", "\r"), array("", ""), $question->{Advice_Model::COLUMN_QUESTION}),
            Advice_Model::COLUMN_IS_PUBLIC => (bool)$question->{Advice_Model::COLUMN_IS_PUBLIC},
            Advice_Model::COLUMN_IS_PUBLIC_ALLOW => (bool)$question->{Advice_Model::COLUMN_IS_PUBLIC_ALLOW},
            Advice_Model::COLUMN_ANSWER => str_replace(array("\n", "\r"), array("", ""), $question->{Advice_Model::COLUMN_ANSWER}),
            Advice_Model::COLUMN_DATE_ADD => $question->{Advice_Model::COLUMN_DATE_ADD},
            Advice_Model::COLUMN_COLOR => $question->{Advice_Model::COLUMN_COLOR},
//            'actions' => null
         ));
      }
      
      $this->view()->respond = $jqGrid->respond();
   }
   
   public function changeAttributeController()
   {
      $this->checkWritebleRights();
      
      if(!isset ($_POST['oper']) || !isset ($_POST['id'])){
         return false;
      }
      
      
      $attrib = $_POST['oper'];
      $id = $_POST['id'];
      $value = isset($_POST['v']) ? $_POST['v'] : null;
      if($value == 'false'){ $value = false; }
      else if($value == 'true') { $value = true; }
      
      
      $model = new Advice_Model();
      
      switch ($attrib) {
         case 'color':
            $record = $model->record($id);
            $record->{Advice_Model::COLUMN_COLOR} = $value;
            $model->save($record);
            $this->infoMsg()->addMessage($this->tr('Barva byla změněna'));
            break;
         
         case 'public':
            $record = $model->record($id);
            $record->{Advice_Model::COLUMN_IS_PUBLIC} = $value;
            $model->save($record);
            if($value == true){
               $this->infoMsg()->addMessage($this->tr('Dotaz byl zveřejněn'));
            } else {
               $this->infoMsg()->addMessage($this->tr('Zveřejnění bylo zrušeno'));
            }
            break;
         case 'del':
            $model->delete($id);
            $this->infoMsg()->addMessage($this->tr('Položka byla smazána'));
            break;
      }
      
   }
   
   public function editQuestionController()
   {
      $this->checkWritebleRights();
      
      $id = $this->getRequest('id');
      
      $model = new Advice_Model();
      $modelConn = new Advice_Model_Connections();
      
      $question = $model->record($id);
      
      if($question == false || $question->isNew()){
         return false;
      }
      
      $form = new Form('question_');
      
      $fGrpBase = $form->addGroup('base', $this->tr('Základní informace'));
      
      $eUserName = new Form_Element_Text('userName', $this->tr('Jméno'));
      $eUserName->setValues($question->{Advice_Model::COLUMN_QUESTIONER_NAME});
      $subLabel = null;
      
      $subLabel .= ($question->{Advice_Model::COLUMN_QUESTIONER_GENDER} == "M" ? $this->tr('muž') : $this->tr('žena')).', ';
      if($question->{Advice_Model::COLUMN_QUESTIONER_AGE} != null){
         $subLabel .= $question->{Advice_Model::COLUMN_QUESTIONER_AGE}.' let, ';
      }
      if($question->{Advice_Model::COLUMN_QUESTIONER_CITY} != null){
         $subLabel .= $question->{Advice_Model::COLUMN_QUESTIONER_CITY}.', ';
      }
      $eUserName->setSubLabel($subLabel);
      $form->addElement($eUserName, $fGrpBase);
      
      $eName = new Form_Element_Text('name', $this->tr('Nadpis'));
      $eName->setValues($question->{Advice_Model::COLUMN_NAME});
      $form->addElement($eName, $fGrpBase);
      
      
      $eQuestion = new Form_Element_TextArea('question', $this->tr('Dotaz'));
      $eQuestion->addValidation(new Form_Validator_NotEmpty());
      $eQuestion->setValues($question->{Advice_Model::COLUMN_QUESTION});
      
      $form->addElement($eQuestion, $fGrpBase);
      
      $eAnswer = new Form_Element_TextArea('answer', $this->tr('Odpověď'));
      $eAnswer->setValues($question->{Advice_Model::COLUMN_ANSWER});
      $form->addElement($eAnswer, $fGrpBase);
      
      $eColor = new Form_Element_Select('color', $this->tr('Barevné označení'));
      $eColor->setOptions($this->answerColors);
      $eColor->setValues($question->{Advice_Model::COLUMN_COLOR});
      $form->addElement($eColor, $fGrpBase);
      
      
      $fGrpCats = $form->addGroup('cats', $this->tr('Zařazení do kategorií a drog'));
      
      $elemCats = new Form_Element_Select('cats', $this->tr('Kategorie'));
      $elemCats->setMultiple(true);
      $elemCats->setSubLabel($this->tr('Pomocí klávesy Ctrl lze vybrat více voleb.'));
      $elemDrugs = new Form_Element_Select('drugs', $this->tr('Drogy'));
      $elemDrugs->setMultiple(true);
      $elemDrugs->setSubLabel($this->tr('Pomocí klávesy Ctrl lze vybrat více voleb.'));

      $modelCats = new Advice_Model_Categories();
      $cats = $modelCats->order(array(Advice_Model_Categories::COLUMN_ORDER))->records();
      
      foreach ($cats as $cat) {
         if($cat->{Advice_Model_Categories::COLUMN_IS_DRUG}){
            $elemDrugs->setOptions(array($cat->{Advice_Model_Categories::COLUMN_NAME} => $cat->{Advice_Model_Categories::COLUMN_ID}), true);
         } else {
            $elemCats->setOptions(array($cat->{Advice_Model_Categories::COLUMN_NAME} => $cat->{Advice_Model_Categories::COLUMN_ID}), true);
         }
      }
      
      $selectedCats = array();
      $selectedRecords = $modelConn->where(Advice_Model_Connections::COLUMN_ID_QUESTION.' = :idq', array('idq' => $question->{Advice_Model::COLUMN_ID}))
         ->records(PDO::FETCH_OBJ);
      if($selectedRecords != false){
         foreach ($selectedRecords as $selCat) {
            array_push($selectedCats, $selCat->{Advice_Model_Connections::COLUMN_ID_CAT});
         }
         $elemCats->setValues($selectedCats);
         $elemDrugs->setValues($selectedCats);
      }
      
      $form->addElement($elemCats, $fGrpCats);
      if($this->category()->getParam(self::PARAM_ALLOW_DRUGS, false)){
         $form->addElement($elemDrugs, $fGrpCats);
      }
      
      $fGrpOther = $form->addGroup('info_a_public', $this->tr('Zveřejnění a upozornění'));
      
      $eAllowPublic = new Form_Element_Checkbox('allowPublic', $this->tr('Souhlas se zveřejněním'));
      $eAllowPublic->setValues($question->{Advice_Model::COLUMN_IS_PUBLIC_ALLOW});
      $form->addElement($eAllowPublic, $fGrpOther);
      if($question->{Advice_Model::COLUMN_IS_PUBLIC_ALLOW}){
         $ePublic = new Form_Element_Checkbox('public', $this->tr('Veřejný'));
         $ePublic->setValues($question->{Advice_Model::COLUMN_IS_PUBLIC});
         $ePublic->setSubLabel($this->tr('Zveřejní dotaz.'));
         $form->addElement($ePublic, $fGrpOther);
         
         $eFAQ = new Form_Element_Checkbox('faq', $this->tr('Častý dotaz'));
         $eFAQ->setValues($question->{Advice_Model::COLUMN_IS_COMMON});
         $eFAQ->setSubLabel($this->tr('Označte pokud se jedná o často kladený dotaz.'));
         $form->addElement($eFAQ, $fGrpBase);
      } else {
         
      }
      
      if($question->{Advice_Model::COLUMN_QUESTIONER_EMAIL} != null){
         $eSendAnswer = new Form_Element_Checkbox('sendAnswer', $this->tr('Odeslat odpověď'));
         $eSendAnswer->setValues(false);
         $eSendAnswer->setSubLabel($this->tr('Odešle odpověď na uživatelův e-mail.'));
         $form->addElement($eSendAnswer, $fGrpOther);
         
         $eSendMail = new Form_Element_Text('sendMail', $this->tr('E-mail'));
         $eSendMail->addValidation(new Form_Validator_Email());
         $eSendMail->setValues($question->{Advice_Model::COLUMN_QUESTIONER_EMAIL});
         $form->addElement($eSendMail, $fGrpOther);
         
      }
      
      $eSave = new Form_Element_SaveCancel('save');
      $form->addElement($eSave);
      
      
      if($form->isSend() && $form->save->getValues() == false){
         $this->link()->route('answers')->reload();
      }
      
      if($form->isValid()){
         $question->{Advice_Model::COLUMN_NAME} = $form->name->getValues();
         $question->{Advice_Model::COLUMN_QUESTIONER_NAME} = $form->userName->getValues();
         $question->{Advice_Model::COLUMN_QUESTION} = $form->question->getValues();
         $question->{Advice_Model::COLUMN_ANSWER} = $form->answer->getValues();
         $question->{Advice_Model::COLUMN_COLOR} = $form->color->getValues();
         if(isset ($form->public)){
            $question->{Advice_Model::COLUMN_IS_PUBLIC} = $form->public->getValues();
            $question->{Advice_Model::COLUMN_IS_COMMON} = $form->faq->getValues();
         }

         $model->save($question);

         if(isset ($form->sendMail)){
            $question->{Advice_Model::COLUMN_QUESTIONER_EMAIL} = $form->sendMail->getValues();
            if($form->sendAnswer->getValues() == true){
               $this->createMailAnswerComplete($question);
            }
         }
         
         // výmaz starých propojení s daným dotazem
         $modelConn->where(Advice_Model_Connections::COLUMN_ID_QUESTION.' = :idq', array('idq' => $question->{Advice_Model::COLUMN_ID}))
            ->delete();
         
         // vytvoření nových
         $selCats = $form->cats->getValues();
         if(!empty ($selCats)){
            foreach ($selCats as $value) {
               $record = $modelConn->newRecord();
               $record->{Advice_Model_Connections::COLUMN_ID_QUESTION} = $question->{Advice_Model::COLUMN_ID};
               $record->{Advice_Model_Connections::COLUMN_ID_CAT} = $value;
               $modelConn->save($record);
            }
         }
         if(isset ($form->drugs)){
            $selDrugs = $form->drugs->getValues();
            if(!empty ($selDrugs)){
               foreach ($selDrugs as $value) {
                  $record = $modelConn->newRecord();
                  $record->{Advice_Model_Connections::COLUMN_ID_QUESTION} = $question->{Advice_Model::COLUMN_ID};
                  $record->{Advice_Model_Connections::COLUMN_ID_CAT} = $value;
                  $modelConn->save($record);
               }
            }
         }
         
         $this->infoMsg()->addMessage($this->tr('Odpověď byla uložena'));
         $this->link()->route('answers')->reload();
      }
      
      
      $this->view()->form = $form;
      $this->view()->question = $question;
   }
   
   public function addQuestionController()
   {
      $this->checkReadableRights();
      
      $form = new Form('addquestion_');
      
      $eName = new Form_Element_Text('name', $this->tr('Vaše jméno'));
      $eName->addValidation(new Form_Validator_NotEmpty());
      $eName->addFilter(new Form_Filter_StripTags());
      $eName->setSubLabel($this->tr('Popřípadě pseudonym.'));
      $form->addElement($eName);
      
      $eAge = new Form_Element_Text('age', $this->tr('Váš věk'));
      $eAge->addValidation(new Form_Validator_IsNumber($this->tr('V položce věk musí být zadáno celé číslo'), Form_Validator_IsNumber::TYPE_INT));
      $eAge->addFilter(new Form_Filter_StripTags());
      $form->addElement($eAge);
      
      $eGender = new Form_Element_Select('gender', $this->tr('Pohlaví'));
      $eGender->setOptions(array($this->tr('Muž') => 'M', $this->tr('Žena') => 'F'));
      $form->addElement($eGender);
      
      $eCity = new Form_Element_Text('city', $this->tr('Město / obec'));
      $eCity->addFilter(new Form_Filter_StripTags());
      $form->addElement($eCity);
      
      
      $eMail = new Form_Element_Text('mail', $this->tr('E-mail'));
      $eMail->setSubLabel($this->tr('Pokud vyplníte svůj e-mail budete automaticky upozorněni v okamžiku zodpovězení vašeho dotazu, popř. pro soukromou odpověď.'));
      $eMail->addFilter(new Form_Filter_StripTags());
      $form->addElement($eMail);
      
      $ePublic = new Form_Element_Checkbox('public', $this->tr('Věřejný dotaz'));
      $ePublic->setSubLabel($this->tr('Dotaz bude po zodpovězení zveřejněn v naší poradně.'));
      $ePublic->setValues(true);
      $form->addElement($ePublic);
      
      $eQuestion = new Form_Element_TextArea('question', $this->tr('Dotaz'));
      $eQuestion->addValidation(new Form_Validator_NotEmpty());
      $eQuestion->addFilter(new Form_Filter_StripTags());
      $form->addElement($eQuestion);
      
      $eAgree = new Form_Element_Checkbox('agree', $this->tr('Sohlas s podmínkami'));
      $eAgree->addValidation(new Form_Validator_NotEmpty($this->tr('Musíte souhlasit s podmínkami užití služby')));
      $form->addElement($eAgree);
      
//      $eSave = new Form_Element_Submit('save', $this->tr('Odeslat dotaz'));
//      $form->addElement($eSave);
      $eSave = new Form_Element_SaveCancel('save', array($this->tr('Odeslat dotaz'), $this->tr('Zpět')));
      $eSave->setCancelConfirm(false);
      $form->addElement($eSave);
      
      if($form->isSend()){
         if($form->save->getValues() == false){
            $this->link()->route()->reload();
         }
         if($form->public->getValues() == false){
            $form->mail->addValidation(new Form_Validator_NotEmpty($this->tr('Pokud dotaz nechcete zveřejnit, musíte zadat Váš e-mail. Jinak Vám nemáme jak odpovědět.')));
         }
      }
      
      if($form->isValid()){
         $model = new Advice_Model();
         
         try {
            $question = $model->newRecord();
            $question->{Advice_Model::COLUMN_ID_CATEGORY} = $this->category()->getId();
            $question->{Advice_Model::COLUMN_QUESTIONER_NAME} = $form->name->getValues();
            $question->{Advice_Model::COLUMN_QUESTIONER_AGE} = $form->age->getValues();
            $question->{Advice_Model::COLUMN_QUESTIONER_GENDER} = $form->gender->getValues();
            $question->{Advice_Model::COLUMN_QUESTIONER_CITY} = $form->city->getValues();
            $question->{Advice_Model::COLUMN_QUESTIONER_EMAIL} = $form->mail->getValues();
            $question->{Advice_Model::COLUMN_IS_PUBLIC_ALLOW} = $form->public->getValues();
            $question->{Advice_Model::COLUMN_QUESTION} = $form->question->getValues();

            $model->save($question);
            $this->createMailNewQuestion($question);

            $this->infoMsg()->addMessage($this->tr('Váš dotaz byl přijat. Co nejdříve Vám na něj odpovíme.'));
            $this->link()->route()->reload();
         } catch (Exception $exc) {
            echo $exc->getTraceAsString();
         }
      }
      
      // kategorie s podmínkami
      
      if($this->category()->getParam(self::PARAM_CONDITION_ID_CAT, 0) != 0) {
         $modelCat = new Model_Category();
         $catCond = $modelCat->record( $this->category()->getParam(self::PARAM_CONDITION_ID_CAT) );
         if($catCond != false){
            $this->view()->conditionCat = $catCond;
            $this->view()->conditionCatLink = $this->link(true)->category($catCond->{Model_Category::COLUMN_URLKEY});
         }
      }
      
      $this->view()->form = $form;
      
   }

   public function editCatsController()
   {
      $this->checkWritebleRights();
      
      $model = new Advice_Model_Categories();
      
      $formAddCat = new Form('add_cat_');
      
      $elemText = new Form_Element_Text('name', $this->tr('Název'));
      $elemText->addValidation(new Form_Validator_NotEmpty());
      $formAddCat->addElement($elemText);
      
      if($this->category()->getParam(self::PARAM_ALLOW_DRUGS, false)){
         $elemIsDrug = new Form_Element_Checkbox('isDrug', $this->tr('Droga'));
         $formAddCat->addElement($elemIsDrug);
      }
      
      $elemSave = new Form_Element_SaveCancel('save');
      $formAddCat->addElement($elemSave);
      
      if($formAddCat->isValid()){
         $record = $model->newRecord();
         if(isset ($formAddCat->isDrug)){
            $record->{Advice_Model_Categories::COLUMN_IS_DRUG} = $formAddCat->isDrug->getValues();
         }
         $record->{Advice_Model_Categories::COLUMN_NAME} = $formAddCat->name->getValues();
         $model->save($record);
         
         if(isset ($formAddCat->isDrug)){
            $this->infoMsg()->addMessage($this->tr('Kategorie/droga byla přidána'));
         } else {
            $this->infoMsg()->addMessage($this->tr('Kategorie byla přidána'));
         }
         $this->link()->reload();
      }
      
      $this->view()->formAdd = $formAddCat;
      
      $this->loadCats();
      
      $formSortCats = new Form('sort_cats_');
      
      $elemText = new Form_Element_Text('name', $this->tr('Název'));
      $elemText->setDimensional();
      $formSortCats->addElement($elemText);
      
      $elemDeleted = new Form_Element_Checkbox('delete', $this->tr('Smazat'));
      $elemDeleted->setDimensional();
      $formSortCats->addElement($elemDeleted);
      
      $elemSubmit = new Form_Element_SaveCancel('save', array($this->tr('Uložit'), $this->tr('Zavřít')));
      $formSortCats->addElement($elemSubmit);
      
      
      if($formSortCats->isSend() && $formSortCats->save->getValues() == false){
         $this->infoMsg()->addMessage($this->tr('Změny byly zrušeny'));
         $this->link()->route()->reload();
      }
      
      if($formSortCats->isValid()){
         $names = $formSortCats->name->getValues();
         $deletes = $formSortCats->delete->getValues();
         
         $order = 1;
         foreach ($names as $id => $text) {
            $record = $model->record($id);
            
            if(isset ($deletes[$id])){
               $model->delete($id);
            } else {
               $record->{Advice_Model_Categories::COLUMN_NAME} = $text;
               $record->{Advice_Model_Categories::COLUMN_ORDER} = $order;
               $model->save($record);
               $order++;
            }
         }
         
         $this->infoMsg()->addMessage($this->tr('Pořadí bylo uloženo'));
         $this->link()->reload();
      }
      
      $this->view()->formSortCats = $formSortCats;
      
      if($this->category()->getParam(self::PARAM_ALLOW_DRUGS, false)){
         $formSortDrugs = new Form('sort_drugs_');
      
         $elemText = new Form_Element_Text('name', $this->tr('Název'));
         $elemText->setDimensional();
         $formSortDrugs->addElement($elemText);
      
         $elemDeleted = new Form_Element_Checkbox('delete', $this->tr('Smazat'));
         $elemDeleted->setDimensional();
         $formSortDrugs->addElement($elemDeleted);
      
         $elemSubmit = new Form_Element_SaveCancel('save', array($this->tr('Uložit'), $this->tr('Zavřít')));
         $formSortDrugs->addElement($elemSubmit);
      
         if($formSortDrugs->isSend() && $formSortDrugs->save->getValues() == false){
            $this->infoMsg()->addMessage($this->tr('Změny byly zrušeny'));
            $this->link()->route()->reload();
         }
      
         if($formSortDrugs->isValid()){
            $names = $formSortDrugs->name->getValues();
            $deletes = $formSortDrugs->delete->getValues();
         
            $order = 1;
            foreach ($names as $id => $text) {
               $record = $model->record($id);
            
               if(isset ($deletes[$id])){
                  $model->delete($id);
               } else {
                  $record->{Advice_Model_Categories::COLUMN_NAME} = $text;
                  $record->{Advice_Model_Categories::COLUMN_ORDER} = $order;
                  $model->save($record);
                  $order++;
               }
            }
         
            $this->infoMsg()->addMessage($this->tr('Pořadí bylo uloženo'));
            $this->link()->reload();
         }
         $this->view()->formSortDrugs = $formSortDrugs;
      }
      
   }
   
   public function statsController()
   {
      $this->checkWritebleRights();
      
      $this->generateStatsController();
      
   }
   
   public function generateStatsController()
   {
      $this->checkWritebleRights();
      
      $formFullStats = new Form('full_stats_');
      $formFullStats->setAction($this->link()->route('generateStats'));
      
      $elemTypes = new Form_Element_Select('type', $this->tr('Typ'));
      $elemTypes->setOptions(array(
         'celkový přehled o počtech' => 'full',
         'pohlaví/věk' => 'sex_by_age',
         'pohlaví+věk/ za čas' => 'sexage_by_time',
      ));
      
      $formFullStats->addElement($elemTypes);
      
      $elemGen = new Form_Element_Submit('generate', $this->tr('Generovat'));
      $formFullStats->addElement($elemGen);
      
      if($formFullStats->isValid()){
         switch ($formFullStats->type->getValues()) {
            case 'sex_by_age':
               $this->genSexByAge();
               break;
            case 'sexage_by_time':
               $this->genSexAgeByTime();
               break;
            case 'full':
            default:
               $this->genFullStat();
               break;
         }
      }
      $this->view()->formFull = $formFullStats;
      
      $formTimeStats = new Form('time_stats_');
      $formTimeStats->setAction($this->link()->route('generateStats'));
      
      $elemTypes = new Form_Element_Select('type', $this->tr('Typ'));
      $elemTypes->setOptions(array(
         'pohlaví / kategorie dotazů' => 'sex_by_cats',
         'věk / kategorie dotazů' => 'age_by_cats',
      ));
      
      if($this->category()->getParam(self::PARAM_ALLOW_DRUGS, false)){
         $elemTypes->setOptions(array(
            'pohlaví / drogy' => 'sex_by_drugs',
            'věk / drogy' => 'age_by_drugs',
            'kategorie dotazů / drogy' => 'cats_by_drugs'), true);
      }
      
      $formTimeStats->addElement($elemTypes);
      
      $elemFormDate = new Form_Element_Text('fromDate', $this->tr('Od data'));
      $elemFormDate->addValidation(new Form_Validator_Date());
      $elemFormDate->addValidation(new Form_Validator_NotEmpty());
      $elemFormDate->addFilter(new Form_Filter_DateTimeObj());
      $date = new DateTime();
      $date->modify('-1 month');
      $date->modify('first day of this month');
      $elemFormDate->setValues(vve_date("%x", $date));
      
      $formTimeStats->addElement($elemFormDate);
      
      $elemDateRange = new Form_Element_Select('dateRange', $this->tr('Období'));
      $elemDateRange->setOptions(array(
         '1 měsíc' => 1,
         '3 měsíce' => 3,
         '6 měsíců' => 6,
         '12 měsíců' => 12,
      ));
      
      $formTimeStats->addElement($elemDateRange);
      
      
      $elemGen = new Form_Element_Submit('generate', $this->tr('Generovat'));
      $formTimeStats->addElement($elemGen);
      
      if($formTimeStats->isValid()){
         switch ($formTimeStats->type->getValues()) {
            case 'sex_by_cats':
               $this->genSexByCats($formTimeStats->fromDate->getValues(), $formTimeStats->dateRange->getValues(), false);
               break;
            case 'age_by_cats':
               $this->genAgeByCats($formTimeStats->fromDate->getValues(), $formTimeStats->dateRange->getValues(), false);
               break;
            case 'sex_by_drugs':
               $this->genSexByCats($formTimeStats->fromDate->getValues(), $formTimeStats->dateRange->getValues(), true);
               break;
            case 'age_by_drugs':
               $this->genAgeByCats($formTimeStats->fromDate->getValues(), $formTimeStats->dateRange->getValues(), true);
               break;
            case 'sex_by_drugs':
            default:
               $this->genCatsByDrugs($formTimeStats->fromDate->getValues(), $formTimeStats->dateRange->getValues());
               break;
         }
      }
      $this->view()->formTime = $formTimeStats;
      
   }
   
   public function repairTextsController()
   {
      
      $model = new Advice_Model();
      
      $c = $model->count();
      Debug::log($c);
      $pr = new Component_HTMLPurifier();
      $pr->setConfig('HTML.Allowed', 'p,a,span,strong,em');
      $pr->setConfig('AutoFormat.RemoveEmpty', true);
      $pr->setConfig('AutoFormat.RemoveSpansWithoutAttributes', true);
      
      for ($i = 0; $i < $c; $i=$i+100 ){
         $records = $model->limit($i, 100)->records();
         Debug::log("Running: ".$i.' - '.($i+100));
         
         if($records != false && !empty ($records)){
            foreach ($records as $record) {
         
               $str = $record->{Advice_Model::COLUMN_QUESTION};
               if(strpos($str,'<p') === false){
                  $str = "<p>$str</p>";
               }
               $str = $pr->purify($str);
               $record->{Advice_Model::COLUMN_QUESTION} = $str;
         
               $str = $record->{Advice_Model::COLUMN_ANSWER};
               if(strpos($str,'<p') === false){
                  $str = "<p>$str</p>";
               }
               $str = $pr->purify($str);
               $record->{Advice_Model::COLUMN_ANSWER} = $str;
               
               $model->save($record);
            }
         }
      }
      
   }

   private function loadCats()
   {
      $model = new Advice_Model_Categories();
      $records = $model->order(array(Advice_Model_Categories::COLUMN_ORDER))->records();
      $drugs = array();
      $cats = array();

      if(!empty ($records)){
         foreach ($records as $rec) {
            if($rec->{Advice_Model_Categories::COLUMN_IS_DRUG}){
               array_push($drugs, $rec);
            } else {
               array_push($cats, $rec);
            }
         }
      }
      $this->view()->drugs = $drugs;
      $this->view()->cats = $cats;
   }

      /**
    * odešle mail při zadání dotazu
    * @param Model_ORM_Record $question 
    */
   protected function createMailNewQuestion(Model_ORM_Record $question)
   {
      //* strings */
      $subject = 'Nový dotaz v poradně stránek '.VVE_WEB_NAME;
      
      $mail = new Email(true);
      
      // odeslání emailu
      $mail->message()->setSubject($mail->sanitize($subject));
      // odesílatele mailu nastavit na adresu zadanou ve formu
      if($question->{Advice_Model::COLUMN_QUESTIONER_EMAIL} != null){
         //$mail->setFrom($formQuestion->mail->getValues());
         $mail->message()->setSender(array($question->{Advice_Model::COLUMN_QUESTIONER_EMAIL} => $mail->sanitize($question->{Advice_Model::COLUMN_QUESTIONER_NAME}) ));
         $mail->message()->setFrom(array($question->{Advice_Model::COLUMN_QUESTIONER_EMAIL} => $mail->sanitize($question->{Advice_Model::COLUMN_QUESTIONER_NAME}) ));
         $mail->message()->setReplyTo($question->{Advice_Model::COLUMN_QUESTIONER_EMAIL});
      }

      $body = "<p>Na stránkách <a href=\"".Url_Request::getBaseWebDir()."\">".VVE_WEB_NAME."</a> byl dne ".  vve_date("%x") ." v ".vve_date("%X")
         . " vložen nový dotaz do kategorie <a href=\"".$this->link()->clear()."\">".$this->category()->getName()."</a>.</p>";
      
      $body .= $this->createQuestionHtmlTable($question);

      $mail->setContent(Email::getBaseHtmlMail($body), 'text/html', 'utf-8' );
      //$mail->addAddress($formQuestion->mail->getValues()); // odesílat?

      $adminMails = array(); // pokud je prázdný výtahneme nastavené maily
      // maily adminů - předané
      $str = $this->category()->getParam(self::PARAM_OTHER_RECIPIENTS, null);
      if($str != null) $adminMails = explode(';', $str);
      // maily adminů - z uživatelů
      $usersId = $this->category()->getParam(self::PARAM_ADMIN_RECIPIENTS, array());
      $modelusers = new Model_Users();
      foreach ($usersId as $id) {
         $user = $modelusers->record($id);
         $adminMails = array_merge($adminMails, explode(';', $user->{Model_Users::COLUMN_MAIL}));
      }
      
      $mail->addAddress($adminMails);
      $mail->sendMail();
   }
   
   /**
    * odešle mail při vyplnění odpovědi
    * @param Model_ORM_Record $question 
    */
   protected function createMailAnswerComplete(Model_ORM_Record $question)
   {
      //* strings */
      $subject = 'Zodpovězení dotazu na stránkách '.VVE_WEB_NAME;
      
      $mail = new Email(true);
      
      // odeslání emailu
      $mail->message()->setSubject($mail->sanitize($subject));
      // odesílatele mailu nastavit na adresu zadanou ve formu
//      if($question->{Advice_Model::COLUMN_QUESTIONER_EMAIL} != null){
//         //$mail->setFrom($formQuestion->mail->getValues());
//         $mail->message()->setSender(array($question->{Advice_Model::COLUMN_QUESTIONER_EMAIL} => $mail->sanitize($question->{Advice_Model::COLUMN_QUESTIONER_NAME}) ));
//         $mail->message()->setFrom(array($question->{Advice_Model::COLUMN_QUESTIONER_EMAIL} => $mail->sanitize($question->{Advice_Model::COLUMN_QUESTIONER_NAME}) ));
//         $mail->message()->setReplyTo($question->{Advice_Model::COLUMN_QUESTIONER_EMAIL});
//      }
      
      $body = "<p>Zasíláme Vám e-mail s odpovědí na Váš dotaz položený na stránkách <a href=\"".Url_Request::getBaseWebDir()."\">".VVE_WEB_NAME."</a>"
         ." dne ".  vve_date("%x")." v ".  vve_date("%X").".<br />"
         ."Dotaz byl vložen do kategorie <a href=\"".$this->link()->clear()."\">".$this->category()->getName()."</a>.</p>";
      
      $body .= $this->createQuestionHtmlTable($question);
      $body .= $this->createAnswerHtmlTable($question);
      
      if($this->category()->getParam(self::PARAM_ANSWER_FOOTER_TEXT, null) != null){
         $body .= '<p>'.$this->category()->getParam(self::PARAM_ANSWER_FOOTER_TEXT, null).'</p>';
      }
      
      $body .= '<p style="font-size: small;">Tento e-mail je generován automaticky systémem. Prosíme neodpovídejte na něj.</p>';
      
      $mail->setContent(Email::getBaseHtmlMail($body));

      $mail->addAddress($question->{Advice_Model::COLUMN_QUESTIONER_EMAIL});
      $mail->sendMail();
   }
   
   /* STATS MATHODS */
   
   private function genFullStat()
   {
      $file = new File_Excel('export_'.date("m-Y").'.xls');
      
      $modelQ = new Advice_Model();
      $modelCats = new Advice_Model_Categories();
      
      $excelObj = $file->getData();
      
      // Set properties
      $excelObj->getProperties()->setCreator("CubeCMS - ".VVE_WEB_NAME);
      $excelObj->getProperties()->setTitle("Poradna - celková statistika");
      $excelObj->getProperties()->setDescription("Poradna - celková statistika");

      $excelObj->setActiveSheetIndex(0);
      $sheet = $excelObj->getActiveSheet();

      // Header
      $sheet->SetCellValue('A1', 'Měsíc');
      $sheet->SetCellValue('B1', 'Dotazů');
      $sheet->SetCellValue('C1', 'Pohlaví');
      $sheet->SetCellValue('D1', 'Bydliště');
      $sheet->SetCellValue('E1', 'Věk');
      $sheet->SetCellValue('F1', 'Témata');
      $sheet->SetCellValue('G1', 'Drogy');
      // header style
      $styleHeaderArray = array( 'font' => array('bold' => true));
      $sheet->getStyle('A1:G1')->applyFromArray($styleHeaderArray);
         
      // styl pro konec roku
      $styleEndYearArray = array(
         'borders' => array(
            'bottom' => array( 'style' => PHPExcel_Style_Border::BORDER_THIN)
         )
      );
      
      // načteme od kterého měsíce a roku se začíná
      $firstRow = $modelQ
         ->where(Advice_Model::COLUMN_ID_CATEGORY.' = :idc', 
            array('idc' => $this->category()->getId()))
         
         ->order(array(Advice_Model::COLUMN_DATE_ADD => Model_ORM::ORDER_ASC))->record();
      
      $curDate = new DateTime($firstRow->{Advice_Model::COLUMN_DATE_ADD});
      
      $row = 2;
      while ($curDate->format("m-Y") != date("m-Y")) {
         // dotazy na základní parametry
         /*
         SELECT 
         COUNT(if(advice_question != '' , 1, NULL)) AS num_questions,
         COUNT(if(advice_questioner_gender != '' , 1, NULL)) AS num_sex,
         COUNT(if(advice_questioner_city != '' , 1, NULL)) AS num_address,
         COUNT(if(advice_questioner_age != 0 , 1, NULL)) AS num_age
         FROM `extc_pradvice`
         WHERE YEAR(advice_date_add) = 2009 AND MONTH(advice_date_add) = 10 ;
         */
         $modelQ = new Advice_Model();
         $numbers = $modelQ
            ->columns(array(
               'num_question' => 'COUNT(if('.Advice_Model::COLUMN_QUESTION.' != "" , 1, NULL))',
               'num_gender' => 'COUNT(if('.Advice_Model::COLUMN_QUESTIONER_GENDER.' != "" , 1, NULL))',
               'num_address' => 'COUNT(if('.Advice_Model::COLUMN_QUESTIONER_CITY.' != "" , 1, NULL))',
               'num_age' => 'COUNT(if('.Advice_Model::COLUMN_QUESTIONER_AGE.' != 0 , 1, NULL))',
               ))
            ->where(Advice_Model::COLUMN_ID_CATEGORY.' = :idc AND YEAR('.Advice_Model::COLUMN_DATE_ADD.') = :sely AND MONTH('.Advice_Model::COLUMN_DATE_ADD.') = :selm', 
               array(
                  'idc' => $this->category()->getId(),
                  'sely' => $curDate->format("Y"),
                  'selm' => $curDate->format("m"),
               ))
            ->record(null, null, PDO::FETCH_OBJ);
         
         // dotazy na kategorie a drogy
         /* SELECT 
         COUNT(if(advice_cat_is_drug = 0 , 1, NULL)) AS num_cats,
         COUNT(if(advice_cat_is_drug = 1 , 1, NULL)) AS num_drugs
         FROM extc_Advice_connections AS conn
         INNER JOIN podaneruce.extc_pradvice AS ques ON conn.id_advice_question = ques.id_advice_question
         INNER JOIN podaneruce.extc_Advice_cats AS cats ON conn.id_advice_cat = cats.id_advice_cat
         WHERE YEAR(advice_date_add) = 2009 AND MONTH(advice_date_add) = 10 ;
          */
         $modelQCats = new Advice_Model_Connections();
         $catsNum = $modelQCats->columns(array(
            'num_cats' => 'COUNT(if('.Advice_Model_Categories::COLUMN_IS_DRUG.' = 0 , 1, NULL))',
            'num_drugs' => 'COUNT(if('.Advice_Model_Categories::COLUMN_IS_DRUG.' = 1 , 1, NULL))',
            ))
            ->joinFK(Advice_Model_Connections::COLUMN_ID_CAT, array(Advice_Model_Categories::COLUMN_IS_DRUG), Model_ORM::JOIN_INNER)
            ->joinFK(Advice_Model_Connections::COLUMN_ID_QUESTION, array(Advice_Model::COLUMN_DATE_ADD, Advice_Model::COLUMN_ID_CATEGORY), Model_ORM::JOIN_INNER)
            
            ->where(Advice_Model::COLUMN_ID_CATEGORY.' = :idc AND YEAR('.Advice_Model::COLUMN_DATE_ADD.') = :sely AND MONTH('.Advice_Model::COLUMN_DATE_ADD.') = :selm', 
               array(
                  'idc' => $this->category()->getId(),
                  'sely' => $curDate->format("Y"),
                  'selm' => $curDate->format("m"),
            ))
            ->record(null, null, PDO::FETCH_OBJ);
         
         // write to xls obj
         $sheet->SetCellValue('A'.$row, $curDate->format("m/Y")); // Měsíc
         $sheet->SetCellValue('B'.$row, $numbers->num_question); // Dotazů
         $sheet->SetCellValue('C'.$row, $numbers->num_gender); // Pohlaví
         $sheet->SetCellValue('D'.$row, $numbers->num_address); // Bydliště
         $sheet->SetCellValue('E'.$row, $numbers->num_age); // Věk
         $sheet->SetCellValue('F'.$row, $catsNum->num_cats); // Témata
         $sheet->SetCellValue('G'.$row, $catsNum->num_drugs); // Drogy
         
         if($curDate->format("m") == 12){ // pokud je konec přidáme linku
            $sheet->getStyle('A'.$row.':G'.$row)->applyFromArray($styleEndYearArray);
         }
         
         $curDate->modify("+1 month");
         $row++;
      }
      
      // Set active sheet index to the first sheet, so Excel opens this as the first sheet
      $excelObj->setActiveSheetIndex(0);
//      $file->save();
      $file->send();
      
   }
   
   private function genSexByAge()
   {
      $ageArray = array( 0,14,18,23,30,40,1000);
      
      $file = new File_Excel('export_sex_by_age_'.date("m-Y").'.xls');
      
      $modelQ = new Advice_Model();
      $modelCats = new Advice_Model_Categories();
      
      $excelObj = $file->getData();
      
      // Set properties
      $excelObj->getProperties()->setCreator("CubeCMS - ".VVE_WEB_NAME);
      $excelObj->getProperties()->setTitle("Poradna - pohlaví / věk");
      $excelObj->getProperties()->setDescription("Poradna - pohlaví / věk");

      $excelObj->setActiveSheetIndex(0);
      $sheet = $excelObj->getActiveSheet();

      // Header
      $sheet->SetCellValue('A1', 'Věk');
      $sheet->SetCellValue('A2', 'Muži');
      $sheet->SetCellValue('A3', 'Ženy');
      
      $model = new Advice_Model();
      
      $numAges = count($ageArray);
      for ($i = 0 ; $i < $numAges-1; $i++) {
         // hlavička
         if($ageArray[$i+1] == 1000){
            $sheet->setCellValueByColumnAndRow($i+1, 1, $ageArray[$i].' a více');
         } else {
            $sheet->setCellValueByColumnAndRow($i+1, 1, $ageArray[$i].' - '.$ageArray[$i+1]);
         }
         
         // male
         $model
            ->where(Advice_Model::COLUMN_ID_CATEGORY." = :idc AND "
               .Advice_Model::COLUMN_QUESTIONER_AGE." >= :startage AND "
               .Advice_Model::COLUMN_QUESTIONER_AGE." < :endage AND "
               .Advice_Model::COLUMN_QUESTIONER_GENDER." = :sex"
               , array(
               'idc' => $this->category()->getId(),
               'startage' => $ageArray[$i],
               'endage' => $ageArray[$i+1],
               'sex' => "M",
            ));
         var_dump($model->getSQLQuery());
         $count = $model->count();
         
         $sheet->setCellValueByColumnAndRow($i+1, 2, $count);
         
         // female
         $model
            ->where(Advice_Model::COLUMN_ID_CATEGORY." = :idc AND "
               .Advice_Model::COLUMN_QUESTIONER_AGE." >= :startage AND "
               .Advice_Model::COLUMN_QUESTIONER_AGE." < :endage AND "
               .Advice_Model::COLUMN_QUESTIONER_GENDER." = :sex"
               , array(
               'idc' => $this->category()->getId(),
               'startage' => $ageArray[$i],
               'endage' => $ageArray[$i+1],
               'sex' => "F",
            ));
         $count = $model->count();
         
         $sheet->setCellValueByColumnAndRow($i+1, 3, $count);
      }
      
      $sheet->getStyle('A1:A3')->applyFromArray(array( 'font' => array('bold' => true)));
      $sheet->getStyle('A1:G1')->applyFromArray(array( 'font' => array('bold' => true)));
      
      // doplnění formátů
      $sheet->getStyle('A1:G1')->applyFromArray(array( 'borders' => array( 'bottom' => array( 'style' => PHPExcel_Style_Border::BORDER_THIN) ) ) );
      $sheet->getStyle('A1:A3')->applyFromArray(array( 'borders' => array( 'right' => array( 'style' => PHPExcel_Style_Border::BORDER_THIN) ) ) );
      
      
      $file->send();
   }
   
   private function genSexAgeByTime()
   {
      $ageArray = array(14,18,23,30,40,1000);
      $file = new File_Excel('export_sex_age_by_time_'.date("m-Y").'.xls');
      
      $modelQ = new Advice_Model();
      $modelCats = new Advice_Model_Categories();
      
      $excelObj = $file->getData();
      
      // Set properties
      $excelObj->getProperties()->setCreator("CubeCMS - ".VVE_WEB_NAME);
      $excelObj->getProperties()->setTitle("Poradna - celková statistika");
      $excelObj->getProperties()->setDescription("Poradna - celková statistika");

      $excelObj->setActiveSheetIndex(0);
      $sheet = $excelObj->getActiveSheet();

      $data = array();
      
      $firstRow = $modelQ
         ->where(Advice_Model::COLUMN_ID_CATEGORY.' = :idc', 
            array('idc' => $this->category()->getId()))
         
         ->order(array(Advice_Model::COLUMN_DATE_ADD => Model_ORM::ORDER_ASC))->record();
      
      $curDate = new DateTime($firstRow->{Advice_Model::COLUMN_DATE_ADD});
      // příprava pole pro řazení
      
      $countAges = count($ageArray);
      $ageCounterArray = array();
      for ($y = 0; $y < $countAges; $y++) {
         $ageCounterArray[$ageArray[$y]] = 0;
      }
      
      // styl pro konec roku
      $styleEndYearArray = array(
         'borders' => array(
            'bottom' => array( 'style' => PHPExcel_Style_Border::BORDER_THIN)
         )
      );
      $row = 2;
      while ($curDate->format("m/Y") != date("m/Y")) {
         $data[$curDate->format("m/Y")] = array('M' => $ageCounterArray, 'F' => $ageCounterArray);
         
         if($curDate->format("m") == 12){
            $sheet->getStyle('A'.$row.':M'.$row)->applyFromArray($styleEndYearArray);
         }
         $row++;
         $curDate->modify("+1 month");
      }

      $modelQ = new Advice_Model();
      // load all records
      $recs = $modelQ
         ->columns(array( Advice_Model::COLUMN_QUESTIONER_AGE, Advice_Model::COLUMN_QUESTIONER_GENDER, Advice_Model::COLUMN_DATE_ADD, ))
         ->where(Advice_Model::COLUMN_ID_CATEGORY.' = :idc AND '
            .Advice_Model::COLUMN_QUESTIONER_AGE.' IS NOT NULL AND '.Advice_Model::COLUMN_QUESTIONER_GENDER.' IS NOT NULL', 
               array( 'idc' => $this->category()->getId(), ))
         ->order(array(Advice_Model::COLUMN_DATE_ADD => Model_ORM::ORDER_ASC))
         ->records(PDO::FETCH_OBJ);
      
      // seřazení a výpočet dat
      foreach ($recs as $row) {
         $date = new DateTime($row->{Advice_Model::COLUMN_DATE_ADD});
         $dateF = $date->format("m/Y");
         
         if($row->{Advice_Model::COLUMN_QUESTIONER_GENDER} == 'F'){ $sexIndex = 'F';
         } else { $sexIndex = 'M'; }
         
         $age = $row->{Advice_Model::COLUMN_QUESTIONER_AGE};
         
         for ($y = 0; $y < $countAges; $y++) {
            if($age < $ageArray[$y]){
               $data[$dateF][$sexIndex][$ageArray[$y]]++;
               break;
            }
         }
      }
      
      // RENDER
      // header
      // header style
      $styleHeaderArray = array( 
         'font' => array('bold' => true),
         'borders' => array( 'bottom' => array( 'style' => PHPExcel_Style_Border::BORDER_THIN) )
      );
      $sheet->getStyle('A1:M1')->applyFromArray($styleHeaderArray);
      $startAge = 0;
      $col = 0;
      foreach ($ageArray as $age) {
         if($age < 100){
            $sheet->setCellValueByColumnAndRow($col+1, 1, 'Muži '.$startAge.' - '.$age);
            $sheet->setCellValueByColumnAndRow($col+2, 1, 'Ženy '.$startAge.' - '.$age);
         } else {
            $sheet->setCellValueByColumnAndRow($col+1, 1, 'Muži '.$startAge.' a více');
            $sheet->setCellValueByColumnAndRow($col+2, 1, 'Ženy '.$startAge.' a více ');
         }
         $sheet->getColumnDimensionByColumn($col+1)->setAutoSize(true);
         $sheet->getColumnDimensionByColumn($col+2)->setAutoSize(true);
         $col = $col+2;
         $startAge = $age;
      }
      
      // data
      $row = 2;
      foreach ($data as $date => $rowData) {
         $sheet->setCellValueByColumnAndRow(0, $row, $date);
         // jednotlivé položky
         for ($y = 0; $y < $countAges; $y++) {
            $sheet->setCellValueByColumnAndRow($y*2+1, $row, $rowData['M'][$ageArray[$y]]);
            $sheet->setCellValueByColumnAndRow($y*2+2, $row, $rowData['F'][$ageArray[$y]]);
         }
         $row++;
      }
      
      // Set active sheet index to the first sheet, so Excel opens this as the first sheet
      $excelObj->setActiveSheetIndex(0);
//      $file->save("php://output");
      $file->send();
   }
   
   private function genSexByCats(DateTime $fromdate, $nMonth, $isDrug = false)
   {
      $toDate = clone $fromdate;
      $toDate->modify("+".(int)$nMonth.' month');

      if($isDrug){
         $fileName = 'export_sex_by_drug_'.$fromdate->format("m-Y").'_on_'.$nMonth.'month.xls';
      } else {
         $fileName = 'export_sex_by_cat_'.$fromdate->format("m-Y").'_on_'.$nMonth.'month.xls';
      }
      
      $file = new File_Excel($fileName);
      
      $modelQ = new Advice_Model();
      $modelCats = new Advice_Model_Categories();
      
      $excelObj = $file->getData();
      
      // Set properties
      $excelObj->getProperties()->setCreator("CubeCMS - ".VVE_WEB_NAME);
      if($isDrug){
         $excelObj->getProperties()->setTitle("Poradna - pohlaví / droga od ".vve_date("%X", $fromdate).' do '.vve_date("%X", $toDate));
         $excelObj->getProperties()->setDescription("Poradna - pohlaví / droga od ".vve_date("%X", $fromdate).' do '.vve_date("%X", $toDate));
      } else {
         $excelObj->getProperties()->setTitle("Poradna - pohlaví / kategorie od ".vve_date("%X", $fromdate).' do '.vve_date("%X", $toDate));
         $excelObj->getProperties()->setDescription("Poradna - pohlaví / kategorie od ".vve_date("%X", $fromdate).' do '.vve_date("%X", $toDate));
      }

      $excelObj->setActiveSheetIndex(0);
      $sheet = $excelObj->getActiveSheet();

      // Header
      $sheet->SetCellValue('A1', $isDrug == true ? 'Droga' : 'Kategorie');
      $sheet->SetCellValue('B1', 'Muži');
      $sheet->SetCellValue('C1', 'Ženy');
      $sheet->getStyle('A1:C1')->applyFromArray(array( 
            'font' => array('bold' => true),
            'borders' => array( 'bottom' => array( 'style' => PHPExcel_Style_Border::BORDER_THIN) ) 
         ));
      $sheet->getColumnDimension('A')->setAutoSize(true);

      $cats = $modelCats->where(Advice_Model_Categories::COLUMN_IS_DRUG.' = :isDrug', array('isDrug' => $isDrug))
         ->order(array(Advice_Model_Categories::COLUMN_ORDER => Model_ORM::ORDER_ASC))
         ->records();
      
      // prepare model
      $modelQ ->columns(array(
               'num_male' => "COUNT(if(".Advice_Model::COLUMN_QUESTIONER_GENDER." = 'M' , 1, NULL))",
               'num_female' => "COUNT(if(".Advice_Model::COLUMN_QUESTIONER_GENDER." = 'F' , 1, NULL))",
               ))
            ->join(Advice_Model::COLUMN_ID, "Advice_Model_Connections", Advice_Model_Connections::COLUMN_ID_QUESTION, array());
      
      $row = 2;
      foreach ($cats as $cat) {
         /* SELECT 
            COUNT(if(prad.advice_questioner_gender = 'M' , 1, NULL)) as num_male,
            COUNT(if(prad.advice_questioner_gender = 'F' , 1, NULL)) as num_female
            FROM podaneruce.extc_pradvice AS prad
            JOIN podaneruce.extc_Advice_connections AS pradc ON pradc.id_advice_question = prad.id_advice_question
            WHERE pradc.id_advice_cat = 56 AND advice_date_add BETWEEN '2011-10-01' AND '2011-11-01';
         */
         
         $numbers = $modelQ
            ->where(Advice_Model_Connections::COLUMN_ID_CAT.' = :idcat AND '
               .Advice_Model::COLUMN_DATE_ADD.' BETWEEN :startDate AND :endDate', 
               array(
                  'idcat' => $cat->{Advice_Model_Categories::COLUMN_ID},
                  'startDate' => $fromdate->format("Y-m-d"),
                  'endDate' => $toDate->format("Y-m-d"),
               ))
            ->record(null,null,PDO::FETCH_OBJ);
         
         $sheet->setCellValueByColumnAndRow(0, $row, $cat->{Advice_Model_Categories::COLUMN_NAME});       
         $sheet->setCellValueByColumnAndRow(1, $row, $numbers->num_male);       
         $sheet->setCellValueByColumnAndRow(2, $row, $numbers->num_female);       
                  
         $row++;
      }
      
      $file->send();
   }
   
   private function genAgeByCats(DateTime $fromdate, $nMonth, $isDrug = false)
   {
      $ageArray = array(14,18,23,30,40,1000);
      
      $toDate = clone $fromdate;
      $toDate->modify("+".(int)$nMonth.' month');

      if($isDrug){
         $fileName = 'export_age_by_drug_'.$fromdate->format("m-Y").'_on_'.$nMonth.'month.xls';
      } else {
         $fileName = 'export_age_by_cat_'.$fromdate->format("m-Y").'_on_'.$nMonth.'month.xls';
      }
      
      $file = new File_Excel($fileName);
      
      $modelQ = new Advice_Model();
      $modelCats = new Advice_Model_Categories();
      
      $excelObj = $file->getData();
      
      // Set properties
      $excelObj->getProperties()->setCreator("CubeCMS - ".VVE_WEB_NAME);
      if($isDrug){
         $excelObj->getProperties()->setTitle("Poradna - věk / droga od ".vve_date("%X", $fromdate).' do '.vve_date("%X", $toDate));
         $excelObj->getProperties()->setDescription("Poradna - věk / droga od ".vve_date("%X", $fromdate).' do '.vve_date("%X", $toDate));
      } else {
         $excelObj->getProperties()->setTitle("Poradna - věk / kategorie od ".vve_date("%X", $fromdate).' do '.vve_date("%X", $toDate));
         $excelObj->getProperties()->setDescription("Poradna - věk / kategorie od ".vve_date("%X", $fromdate).' do '.vve_date("%X", $toDate));
      }

      $excelObj->setActiveSheetIndex(0);
      $sheet = $excelObj->getActiveSheet();

      // Header
      $sheet->SetCellValue('A1', $isDrug == true ? 'Droga' : 'Kategorie');
      $sheet->getStyle('A1:G1')->applyFromArray(array( 
            'font' => array('bold' => true),
            'borders' => array( 'bottom' => array( 'style' => PHPExcel_Style_Border::BORDER_THIN) ) 
         ));
      $sheet->getColumnDimension('A')->setAutoSize(true);

      $cats = $modelCats->where(Advice_Model_Categories::COLUMN_IS_DRUG.' = :isDrug', array('isDrug' => $isDrug))
         ->order(array(Advice_Model_Categories::COLUMN_ORDER => Model_ORM::ORDER_ASC))
         ->records();
      
      // create columns
      
      $fromAge = 0;
      $columsArray = array();
      $col = 1;
      foreach ($ageArray as $age){
         if($age < 100){
            $columsArray['num_'.$age] = "COUNT(if(".Advice_Model::COLUMN_QUESTIONER_AGE." >= $fromAge && ".Advice_Model::COLUMN_QUESTIONER_AGE." < $age , 1, NULL))";
            $sheet->setCellValueByColumnAndRow($col, 1, $fromAge.' - '.$age);
         } else {
            $columsArray['num_'.$age] = "COUNT(if(".Advice_Model::COLUMN_QUESTIONER_AGE." >= $fromAge, 1, NULL))";
            $sheet->setCellValueByColumnAndRow($col, 1, $fromAge.' a více');
         }
         $fromAge = $age;
         $col++;
      }
      
      // prepare model
      $modelQ ->columns($columsArray)
            ->join(Advice_Model::COLUMN_ID, "Advice_Model_Connections", Advice_Model_Connections::COLUMN_ID_QUESTION, array());
      
      $row = 2;
      foreach ($cats as $cat) {
         /* SELECT 
            COUNT(if(prad.advice_questioner_age >= 0 && prad.advice_questioner_age < 14 , 1, NULL)) as num_14
            ,COUNT(if(prad.advice_questioner_age >= 14 && prad.advice_questioner_age < 18 , 1, NULL)) as num_18
            ,COUNT(if(prad.advice_questioner_age >= 18 && prad.advice_questioner_age < 23 , 1, NULL)) as num_23
            ,COUNT(if(prad.advice_questioner_age >= 23 && prad.advice_questioner_age < 30 , 1, NULL)) as num_30
            ,COUNT(if(prad.advice_questioner_age >= 30 && prad.advice_questioner_age < 40 , 1, NULL)) as num_40
            ,COUNT(if(prad.advice_questioner_age >= 40 , 1, NULL)) as num_1000
            FROM podaneruce.extc_pradvice AS prad
            JOIN podaneruce.extc_Advice_connections AS pradc ON pradc.id_advice_question = prad.id_advice_question
            WHERE advice_date_add >= '2011-10-01' AND advice_date_add <= '2011-11-01' AND pradc.id_advice_cat = 56
         */
         
         $numbers = $modelQ
            ->where(Advice_Model_Connections::COLUMN_ID_CAT.' = :idcat AND '
               .Advice_Model::COLUMN_DATE_ADD.' BETWEEN :startDate AND :endDate', 
               array(
                  'idcat' => $cat->{Advice_Model_Categories::COLUMN_ID},
                  'startDate' => $fromdate->format("Y-m-d"),
                  'endDate' => $toDate->format("Y-m-d"),
               ))
            ->record(null,null,PDO::FETCH_OBJ);
         var_dump($numbers);
         
         $sheet->setCellValueByColumnAndRow(0, $row, $cat->{Advice_Model_Categories::COLUMN_NAME});       
         
         $col = 1;
         foreach ($ageArray as $age) {
            $sheet->setCellValueByColumnAndRow($col, $row, $numbers->{"num_".$age});       
            $col++;
         }
         $row++;
      }
      
      $file->send();
   }
   
   private function genCatsByDrugs(DateTime $fromdate, $nMonth)
   {
      $toDate = clone $fromdate;
      $toDate->modify("+".(int)$nMonth.' month');

      $fileName = 'export_cats_by_drug_'.$fromdate->format("m-Y").'_on_'.$nMonth.'month.xls';
      
      $file = new File_Excel($fileName);
      
      $modelQ = new Advice_Model();
      $modelCats = new Advice_Model_Categories();
      
      $excelObj = $file->getData();
      
      // Set properties
      $excelObj->getProperties()->setCreator("CubeCMS - ".VVE_WEB_NAME);
      $excelObj->getProperties()->setTitle("Poradna - kategorie / droga od ".vve_date("%X", $fromdate).' do '.vve_date("%X", $toDate));
      $excelObj->getProperties()->setDescription("Poradna - kategorie / droga od ".vve_date("%X", $fromdate).' do '.vve_date("%X", $toDate));

      $excelObj->setActiveSheetIndex(0);
      $sheet = $excelObj->getActiveSheet();

      // Header
//      $sheet->getStyle('A1:C1')->applyFromArray(array( 
//            'font' => array('bold' => true),
//            'borders' => array( 'bottom' => array( 'style' => PHPExcel_Style_Border::BORDER_THIN) ) 
//         ));
      
      $sheet->getColumnDimension('A:P')->setAutoSize(true);

      
      $cats = $modelCats->where(Advice_Model_Categories::COLUMN_IS_DRUG.' = 1', array())
         ->order(array(Advice_Model_Categories::COLUMN_ORDER => Model_ORM::ORDER_ASC))
         ->records();
      
      $drugs = $modelCats->where(Advice_Model_Categories::COLUMN_IS_DRUG.' = 0', array())
         ->order(array(Advice_Model_Categories::COLUMN_ORDER => Model_ORM::ORDER_ASC))
         ->records();
      
      $data = array();
      foreach ($cats as $cat) {
         
         foreach ($drugs as $drug) {
            $data[$cat->{Advice_Model_Categories::COLUMN_NAME}][$drug->{Advice_Model_Categories::COLUMN_NAME}] = 0;
         }
         
      }
      
      
      // prepare model
      $modelQ ->columns(array(
               'num_male' => "COUNT(if(".Advice_Model::COLUMN_QUESTIONER_GENDER." = 'M' , 1, NULL))",
               'num_female' => "COUNT(if(".Advice_Model::COLUMN_QUESTIONER_GENDER." = 'F' , 1, NULL))",
               ))
            ->join(Advice_Model::COLUMN_ID, "Advice_Model_Connections", Advice_Model_Connections::COLUMN_ID_QUESTION, array());
      
      $row = 2;
      foreach ($cats as $cat) {
         /* SELECT 
            COUNT(if(prad.advice_questioner_gender = 'M' , 1, NULL)) as num_male,
            COUNT(if(prad.advice_questioner_gender = 'F' , 1, NULL)) as num_female
            FROM podaneruce.extc_pradvice AS prad
            JOIN podaneruce.extc_Advice_connections AS pradc ON pradc.id_advice_question = prad.id_advice_question
            WHERE pradc.id_advice_cat = 56 AND advice_date_add BETWEEN '2011-10-01' AND '2011-11-01';
         */
         
         $numbers = $modelQ
            ->where(Advice_Model_Connections::COLUMN_ID_CAT.' = :idcat AND '
               .Advice_Model::COLUMN_DATE_ADD.' BETWEEN :startDate AND :endDate', 
               array(
                  'idcat' => $cat->{Advice_Model_Categories::COLUMN_ID},
                  'startDate' => $fromdate->format("Y-m-d"),
                  'endDate' => $toDate->format("Y-m-d"),
               ))
            ->record(null,null,PDO::FETCH_OBJ);
         
         $sheet->setCellValueByColumnAndRow(0, $row, $cat->{Advice_Model_Categories::COLUMN_NAME});       
         $sheet->setCellValueByColumnAndRow(1, $row, $numbers->num_male);       
         $sheet->setCellValueByColumnAndRow(2, $row, $numbers->num_female);       
                  
         $row++;
      }
      
      $file->send();
   }
   
   protected function settings(&$settings,Form &$form) 
   {
      $fGrpView = $form->addGroup('view', $this->tr('Nastavení vzhledu'));
      $fGrpAdvice = $form->addGroup('advice', $this->tr('Nastavení chování poradny'));
      
      $eColors = new Form_Element_TextArea('colors', $this->tr('Intení barvy dotazů'));
      $eColors->setSubLabel($this->tr('Je zadáván název a HTML HEX kód barevy (Např: žlutá:ffff00,červená:fd0006, atd). Více barev je odděleno čárkou a bez mezer. pro žádnou barvu zadat kód 0'));
      
//      Debug::log($settings[self::PARAM_COLORS]);
      
      if(isset($settings[self::PARAM_COLORS]) && is_array($settings[self::PARAM_COLORS]) ){
         $cStr = array();
         foreach ($settings[self::PARAM_COLORS] as $name => $value) {
            $cStr[] = $value.":".$name;
         }
      } else {
         $cStr = array();
         foreach ($this->view()->answerColors as $name => $value) {
            $cStr[] = $value.":".$name;
         }
      }
      $eColors->setValues(implode(',', $cStr ));
      
      $form->addElement($eColors, $fGrpAdvice);
      
      $eAllowDrugs = new Form_Element_Checkbox('allowDrugs', $this->tr('Zapnout podporu drog'));
      if(isset($settings[self::PARAM_ALLOW_DRUGS])){
         $eAllowDrugs->setValues($settings[self::PARAM_ALLOW_DRUGS]);
      }
      $form->addElement($eAllowDrugs, $fGrpAdvice);
      
      $modelCats = new Model_Category();
      $cats = $modelCats->records();
      
      $eCatWithConditions = new Form_Element_Select('idcatcond', $this->tr('Kategorie s podmínkami užití'));
      $eCatWithConditions->setOptions(array($this->tr('Źádné podmínky') => "0"));
      foreach ($cats as $cat) {
         $eCatWithConditions->setOptions(array((string)$cat->{Model_Category::COLUMN_NAME} => $cat->{Model_Category::COLUMN_CAT_ID}), true);
      }
      if(isset($settings[self::PARAM_CONDITION_ID_CAT])){
         $eCatWithConditions->setValues($settings[self::PARAM_CONDITION_ID_CAT]);
      }
      $form->addElement($eCatWithConditions, $fGrpAdvice);
      
      $grpAdmin = $form->addGroup('admins', 'Nastavení upozornění',
              'Nastavení příjemců příjemců při vložení dotazu do poradny.');

      // maily správců
      $elemEamilRec = new Form_Element_TextArea('otherRec', 'Adresy správců');
      $elemEamilRec->setSubLabel('E-mailové adresy správců, kterým chodí upozornění na nové dotazy.
Může jich být více a jsou odděleny středníkem. Místo tohoto boxu
lze využít následující výběr již existujících uživatelů.');
      $form->addElement($elemEamilRec, $grpAdmin);

      if (isset($settings[self::PARAM_OTHER_RECIPIENTS])) {
         $form->otherRec->setValues($settings[self::PARAM_OTHER_RECIPIENTS]);
      }

      $elemAdmins = new Form_Element_Select('adminsRec', 'Adresy uživatelů v systému');
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
      $elemAdmins->setOptions($usersIds);
      $elemAdmins->setMultiple();
      $elemAdmins->html()->setAttrib('size', 4);
      if (isset($settings[self::PARAM_ADMIN_RECIPIENTS])) {
         $elemAdmins->setValues($settings[self::PARAM_ADMIN_RECIPIENTS]);
      }

      $form->addElement($elemAdmins, $grpAdmin);
      
      
      $grpMails = $form->addGroup('mails', 'Nastavení e-mailů',
              'Nastavení textů e-mailů.');
      
      $elemEmailAnsFooter = new Form_Element_TextArea('mailAnsFooter', 'Text v zápatí');
      $elemEmailAnsFooter->setSubLabel('Text, který je přidán do zápatí e-mailů s odpovědí.');
      $form->addElement($elemEmailAnsFooter, $grpMails);

      if (isset($settings[self::PARAM_ANSWER_FOOTER_TEXT])) {
         $form->mailAnsFooter->setValues($settings[self::PARAM_ANSWER_FOOTER_TEXT]);
      }
      
      if($form->isValid()){
         if($form->colors->getValues() != null){
            $colors = array();
            
            foreach (explode(',', $form->colors->getValues()) as $item) {
               $parts = explode(':', $item);
               if(count($parts) == 2 && (preg_match('/^[a-f0-9]{6}$/i', trim($parts[1]) ) || trim($parts[1]) == 0 ) ){
                  $colors[(string)trim($parts[1])] = $parts[0];
               }
            }
            if(!empty ($colors)){
               $settings[self::PARAM_COLORS] = $colors;
            }
         }
         $settings[self::PARAM_ALLOW_DRUGS] = $form->allowDrugs->getValues();
         $settings[self::PARAM_CONDITION_ID_CAT] = $form->idcatcond->getValues();
         $settings[self::PARAM_ADMIN_RECIPIENTS] = $form->adminsRec->getValues();
         $settings[self::PARAM_OTHER_RECIPIENTS] = $form->otherRec->getValues();
         $settings[self::PARAM_ANSWER_FOOTER_TEXT] = $form->mailAnsFooter->getValues();
      }
   }
   
   
   /* privátní funkce pro tvorbu mailů */
   private function createHtmlTableRow($name, $value, $twoCols = true, $sufix = null)
   {
      $str = null;
      if ($value != null && $value !== 0) {
         if ($twoCols) {
            $str = '<tr><th align="left"><strong>' . $name . ':</strong></th><td>' . $value . ' ' . $sufix . '</td></tr>';
         } else {
            $str = '<tr><th align="left" colspan="2"><strong>' . $name . ':</strong></th></tr>'
               . '<tr><td colspan="2">' . $value . ' ' . $sufix . '</td></tr>';
         }
      }
      return $str;
   }
   
   private function createQuestionHtmlTable($question)
   {
      $str = '<h2>Dotaz</h2>'
         .'<table cellspacing="0" cellpadding="4" border="1" style="width: 600px">';
      
      $str .= $this->createHtmlTableRow('Datum vložení', vve_date("%x %X", new DateTime($question->{Advice_Model::COLUMN_DATE_ADD})) )
         .$this->createHtmlTableRow('Jméno (přezdívka)', $question->{Advice_Model::COLUMN_QUESTIONER_NAME})
         .$this->createHtmlTableRow('Věk', $question->{Advice_Model::COLUMN_QUESTIONER_AGE}, true, 'let')
         .$this->createHtmlTableRow('Pohlaví', $question->{Advice_Model::COLUMN_QUESTIONER_GENDER} == "M" ? "Muž" : "Žena")
         .$this->createHtmlTableRow('Město / obec', $question->{Advice_Model::COLUMN_QUESTIONER_CITY})
         .$this->createHtmlTableRow('E-mail', $question->{Advice_Model::COLUMN_QUESTIONER_EMAIL})
         .$this->createHtmlTableRow('Souhlas se zveřejněním?', $question->{Advice_Model::COLUMN_IS_PUBLIC_ALLOW} == true ? "Ano" : "Ne")
         .$this->createHtmlTableRow('Dotaz', $question->{Advice_Model::COLUMN_QUESTION}, false);
      $str .= '</table>';

//      $str .= '<h3>Text dotazu:</h3>';
//      $str .= '<p>'.$question->{Advice_Model::COLUMN_QUESTION}.'</p>';

      return $str;
   }
   
   private function createAnswerHtmlTable($question)
   {
      $str = '<h2>Odpověď</h2>'
         .'<table cellspacing="0" cellpadding="4" border="1" style="width: 600px">';
      
      $str .= $this->createHtmlTableRow('Datum odpovědi', vve_date("%x %X") )
         .$this->createHtmlTableRow('Název', $question->{Advice_Model::COLUMN_NAME})
         .$this->createHtmlTableRow('Odpověď', $question->{Advice_Model::COLUMN_ANSWER}, false);
      $str .= '</table>';
//      $str .= '<h3>Text Odpovědi:</h3>';
//      $str .= '<p>'.$question->{Advice_Model::COLUMN_ANSWER}.'</p>';

      return $str;
   }
}
