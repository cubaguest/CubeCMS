<?php
class MessagesBoard_Controller extends Controller {
   const DEFAULT_MSGS_IN_PAGE = 10;

   const PARAM_EDITOR_TYPE = 'e_t';
   const PARAM_ALLOW_COLORS = 'a_c';
   const PARAM_COLORS = 'c';

   public function init()
   {
      parent::init();
      $this->actionsLabels = array(
          'main' => $this->tr('Seznam příspěvků')
      );
   }

   /**
    * Kontroler pro zobrazení novinek
    */
   public function mainController() {
      //        Kontrola práv
      $this->checkReadableRights();
      $model = new MessagesBoard_Model();

      // pokud je předáno id provede se přesměrování na stránku, kde je zpráva umístěna
      if($this->getRequestParam('id', false) != false){
         $rec = $model->columns(array(
            'pos' => '(SELECT COUNT(*) FROM {THIS} AS t2  WHERE '.$model->getTableShortName().'.`'.MessagesBoard_Model::COLUMN_ID
            .'` <= t2.`'.MessagesBoard_Model::COLUMN_ID.'`)'
         ))
            ->where(MessagesBoard_Model::COLUMN_ID_CATEGORY.' = :idc AND '.MessagesBoard_Model::COLUMN_ID.' = :idm', 
               array('idc' => $this->category()->getId(), 'idm' => $this->getRequestParam('id')))
            ->order(array(MessagesBoard_Model::COLUMN_TIME_ADD => Model_ORM::ORDER_DESC));
         
         $pos = (int)$rec->record(null, null, PDO::FETCH_OBJ)->pos;
         
         if($pos > $this->category()->getParam('scroll', self::DEFAULT_MSGS_IN_PAGE)){
            // dopočet stránky
            $p = ceil($pos/$this->category()->getParam('scroll', self::DEFAULT_MSGS_IN_PAGE));
            $this->link()->clear()->param(Component_Scroll::GET_PARAM, $p)->reload();
            return;
         } else {
            // jedná se o první stránku
            $this->link()->rmParam()->reload();
            return;
         }
      }
      
      // kontroly přidání příspěvku
      if($this->rights()->isWritable()){
         $this->addMsg();
         $this->deleteMsg();
      }
      
      // načtení článků
      $model
         ->where(MessagesBoard_Model::COLUMN_ID_CATEGORY.' = :idc ', array('idc' => $this->category()->getId()))
         ->joinFK(MessagesBoard_Model::COLUMN_ID_USER, array(Model_Users::COLUMN_USERNAME, Model_Users::COLUMN_NAME, Model_Users::COLUMN_SURNAME))
         ->order(array(MessagesBoard_Model::COLUMN_TIME_ADD => Model_ORM::ORDER_DESC));

      $scrollComponent = null;
      if($this->category()->getParam('scroll', self::DEFAULT_MSGS_IN_PAGE) != 0){
         $scrollComponent = new Component_Scroll();
         $scrollComponent->setConfig(Component_Scroll::CONFIG_CNT_ALL_RECORDS, $model->count());

         $scrollComponent->setConfig(Component_Scroll::CONFIG_RECORDS_ON_PAGE,
              $this->category()->getParam('scroll', self::DEFAULT_MSGS_IN_PAGE));
         
         $model->limit($scrollComponent->getStartRecord(), $scrollComponent->getRecordsOnPage());
      }

      $this->view()->scrollComp = $scrollComponent;
      $this->view()->messages = $model->records();
   }

   /**
    * Metoda smaže článek z dat
    * @param int $idArticle
    */
   protected function deleteMsg() {
      $form = new Form('msg_del_');
      
      $elemId = new Form_Element_Hidden('id');
      $form->addElement($elemId);
      
      $elemDel = new Form_Element_Submit('del', $this->tr('Smazat'));
      $form->addElement($elemDel);
      
      if($form->isValid()){
         $model = new MessagesBoard_Model();
         $model->delete($form->id->getValues());
         $this->infoMsg()->addMessage($this->tr('Položka byla smazána'));
         $this->link(true)->route()->reload();
      }
      
      $this->view()->formDelete = $form;
      
   }

   /**
    * Kontroler pro přidání novinky
    */
   public function addMsg() {
      $form = $this->createForm();
      
      if($form->isValid()) {
         $this->saveMsg($form);
         $this->infoMsg()->addMessage($this->tr('Zpráva byla uložena'));
         $this->link()->reload();
      }
      $this->view()->form = $form;
   }

   /**
    * controller pro úpravu novinky
    */
   public function editController() {
      $this->checkWritebleRights();

      // načtení zprávy
      $model = new MessagesBoard_Model();
      $msg = $model->record($this->getRequest('id'));
      
      if($msg == false OR $msg->isNew()){
         return false;
      }
      
      $form = $this->createForm($msg);
      
      $elemSave = new Form_Element_SaveCancel('save');
      $form->addElement($elemSave, 'main');

      if($form->isSend() AND $form->save->getValues() == false){
         $this->link()->route()->reload();
      }

      if($form->isValid()) {
         $this->saveMsg($form, $msg);
         $this->infoMsg()->addMessage($this->tr('Vzkaz byl uložen'));
         $this->link()->route()->reload();
      }
      $this->view()->form = $form;
   }
   
   /**
    * Metoda vytvoří formulář
    * @param Model_ORM_Record $msg
    * @return Form 
    */
   protected function createForm(Model_ORM_Record $msg = null)
   {
      $form = new Form('msg_');
      
      $fGrp = $form->addGroup('main', $this->tr('Zanechat vzkaz'));

      $elemText = new Form_Element_TextArea('text', $this->tr('Text'));
      $elemText->addValidation(new Form_Validator_NotEmpty());
      $elemText->addFilter(new Form_Filter_HTMLPurify('p[style],span[style],a[href|title],strong,em,img[src|alt],br,ul,li,ol'));
      
      $form->addElement($elemText, $fGrp);
      
      if($this->category()->getParam(self::PARAM_ALLOW_COLORS, true)){
         $elemColor = new Form_Element_Text('color', $this->tr('Barva'));
         $elemColor->addValidation(new Form_Validator_Regexp('/^#[a-f0-9]{6}$/i', $this->tr('Barva nebyla zadána ve správném formátu. Formát: #000000 pro černou barvu.')));
         $form->addElement($elemColor, $fGrp);
      }
      
      if(!Auth::isLogin()){
         $elemCaptcha = new Form_Element_Captcha('captcha');
         $form->addElement($elemCaptcha, $fGrp);
      }
      
      $elemSend = new Form_Element_Submit('save', $this->tr('Uložit'));
      $form->addElement($elemSend, $fGrp);
      
      if($msg != null){
         $form->text->setValues($msg->{MessagesBoard_Model::COLUMN_TEXT});
         if(isset ($form->color) && $msg->{MessagesBoard_Model::COLUMN_COLOR} != null ){
            $form->color->setValues($msg->{MessagesBoard_Model::COLUMN_COLOR});
         }
      }
      return $form;
   }

   /**
    * Uložení samotného článku
    * @param <type> $names
    * @param <type> $urlkeys
    * @param <type> $form
    */
   protected function saveMsg(Form $form, Model_ORM_Record $msg = null) {
      $model = new MessagesBoard_Model();
      
      if($msg == null){
         $msg = $model->newRecord();
      }
      
      $msg->{MessagesBoard_Model::COLUMN_TEXT} = $form->text->getValues();
      $msg->{MessagesBoard_Model::COLUMN_TEXT_CLEAR} = strip_tags($msg->{MessagesBoard_Model::COLUMN_TEXT});
      if(isset ($form->color)){
         $msg->{MessagesBoard_Model::COLUMN_COLOR} = $form->color->getValues();
      }
      if($msg->isNew()){
         $msg->{MessagesBoard_Model::COLUMN_ID_CATEGORY} = $this->category()->getId();
         $msg->{MessagesBoard_Model::COLUMN_ID_USER} = Auth::getUserId();
         $msg->{MessagesBoard_Model::COLUMN_IP_ADDRESS} = $_SERVER['REMOTE_ADDR'];
      }
      $model->save($msg);
   }

   /**
    * Smazání článků při odstranění kategorie
    * @param Category $category
    */
   public static function clearOnRemove(Category $category) {
      $model = new Articles_Model();
      $model->where(Articles_Model::COLUMN_ID_CATEGORY, $category->getId())->delete();
   }

   /**
    * Metoda pro nastavení modulu
    */
   protected function settings(&$settings,Form &$form) {
      $fGrpView = $form->addGroup('view', $this->tr('Nastavení vzhledu'));

      $elemScroll = new Form_Element_Text('scroll', $this->tr('Počet položek na stránku'));
      $elemScroll->setSubLabel(sprintf($this->tr('Výchozí: %s položek. Pokud je zadána 0 budou vypsány všechny položky'),self::DEFAULT_MSGS_IN_PAGE));
      $elemScroll->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemScroll, $fGrpView);

      if(isset($settings['scroll'])) {
         $form->scroll->setValues($settings['scroll']);
      }
      $fGrpEditSet = $form->addGroup('editSettings', $this->tr('Nastavení úprav'));

      $elemEditorType = new Form_Element_Select('editor_type', $this->tr('Typ editoru'));
      $elemEditorType->setOptions(array(
         $this->tr('žádný (pouze textová oblast)') => 'none',
         $this->tr('jednoduchý (Wysiwyg)') => 'simple',
         $this->tr('pokročilý (Wysiwyg)') => 'advanced',
         $this->tr('kompletní (Wysiwyg)') => 'full'
      ));
      $elemEditorType->setValues('advanced');
      if(isset($settings[self::PARAM_EDITOR_TYPE])) {
         $elemEditorType->setValues($settings[self::PARAM_EDITOR_TYPE]);
      }
      $form->addElement($elemEditorType, $fGrpEditSet);

      // znovu protože mohl být už jednou validován bez těchto hodnot
      if($form->isValid()) {
         $settings['scroll'] = (int)$form->scroll->getValues();
         $settings[self::PARAM_EDITOR_TYPE] = $form->editor_type->getValues();
      }
   }
}
?>
