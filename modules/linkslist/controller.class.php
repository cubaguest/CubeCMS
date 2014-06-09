<?php

/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 */
class LinksList_Controller extends Controller {

   public function init()
   {
      parent::init();
      $this->actionsLabels = array(
          'main' => $this->tr('Seznam odkazů')
      );
   }
   
   /**
    * Kontroler pro zobrazení textu
    */
   public function mainController()
   {
      //    Kontrola práv
      $this->checkReadableRights();
      
      // načtení odakzů
      $m = new LinksList_Model_Links();
      $links = $m
          ->joinFK(LinksList_Model_Links::COLUMN_CATEGORY, array(Model_Category::COLUMN_NAME, Model_Category::COLUMN_URLKEY))
          ->where(LinksList_Model_Links::COLUMN_ID_CATEGORY." = :idc", array('idc' => $this->category()->getId()))
          ->records();
      foreach ($links as &$l) {
         if(is_file($this->module()->getDataDir().'link-'.$l->getPK().'.jpg')){
            $l->image = $this->module()->getDataDir().'link-'.$l->getPK().'.jpg';
            $l->imageSrc = $this->module()->getDataDir(true).'link-'.$l->getPK().'.jpg';
         }
      }
      $this->view()->links = $links;

      // načtení textu
      $text = Text_Model::getText($this->category()->getId(), Text_Model::TEXT_MAIN_KEY);
      if($text != false){
         $this->view()->text = (string)$text->{Text_Model::COLUMN_TEXT};
      }
   }

   /**
    * Kontroler pro editaci textu
    */
   public function editTextController()
   {
      $this->checkWritebleRights();

      $form = new Form("text_");

      $textarea = new Form_Element_TextArea('text', $this->tr("Text"));
      $textarea->setLangs();
      $form->addElement($textarea);

      $textRecord = Text_Model::getText($this->category()->getId(), self::TEXT_MAIN_KEY, true);
      if ($textRecord != false) {
         $form->text->setValues($textRecord->{Text_Model_Detail::COLUMN_TEXT});
      }

      $submit = new Form_Element_SaveCancel('send');
      $form->addElement($submit);

      if ($form->isSend() AND $form->send->getValues() == false) {
         $this->infoMsg()->addMessage($this->tr('Změny byly zrušeny'));
         $this->link()->route()->reload();
      }

      if ($form->isValid()) {
         // odtranění script, nebezpečných tagů a komentřů
         $text = vve_strip_html_comment($form->text->getValues());
         foreach ($text as $lang => $t) {
            $text[$lang] = preg_replace(array('@<script[^>]*?.*?</script>@siu'), array(''), $t);
         }
         $textRecord->{Text_Model::COLUMN_TEXT} = $text;
         $textRecord->{Text_Model::COLUMN_SUBKEY} = Text_Model::TEXT_MAIN_KEY;
         $textRecord->save();
         
         $this->log('úprava textu');
         $this->infoMsg()->addMessage($this->tr('Text byl uložen'));
         $this->link()->route()->reload();
      }
      // view
      $this->view()->template()->form = $form;
   }
   
   public function listController()
   {
      $this->checkWritebleRights();
      
      $this->checkDeleteLink();
      
      $m = new LinksList_Model_Links();
      
      $links = $m
          ->joinFK(LinksList_Model_Links::COLUMN_CATEGORY, array(Model_Category::COLUMN_NAME, Model_Category::COLUMN_URLKEY))
          ->where(LinksList_Model_Links::COLUMN_ID_CATEGORY." = :idc", array('idc' => $this->category()->getId()))
          ->records();
      
      foreach ($links as &$l) {
         if(is_file($this->module()->getDataDir().'link-'.$l->getPK().'.jpg')){
            $l->image = $this->module()->getDataDir().'link-'.$l->getPK().'.jpg';
            $l->imageSrc = $this->module()->getDataDir(true).'link-'.$l->getPK().'.jpg';
         }
      }
      
      $this->view()->links = $links;
   }
   
   public function changePositonController()
   {
      $id = (int)$_POST['id'];
      $newPos = (int)$_POST['pos'];
      if(!$id || !$newPos){
         throw new InvalidArgumentException($this->tr('Nebyly předán parametr id nebo pozice'));
      }
      LinksList_Model_Links::setRecordPosition($id, $newPos);
      $this->infoMsg()->addMessage($this->tr('Pozice byla upravena'));
   }
   
   public function addController()
   {
      $this->checkWritebleRights();
      
      $form = $this->createEditLinkForm();
      
      if($form->isSend() && $form->save->getValues() == false){
         $this->link()->route('list')->redirect();
      }
      
      if($form->isValid()){
         $this->processEditLinkForm($form);
         $this->infoMsg()->addMessage($this->tr('Odkaz byl uložen'));
         $this->link()->route('list')->redirect();
      }
      
      $this->view()->form = $form;
   }
   
   public function editController($id)
   {
      $this->checkWritebleRights();
      
      $rec = LinksList_Model_Links::getRecord($id);
      
      if(!$rec){
         throw new UnexpectedPageException($this->tr('Odkaz neexistuje'));
      }
      
      $form = $this->createEditLinkForm($rec);
      
      if($form->isSend() && $form->save->getValues() == false){
         $this->link()->route('list')->redirect();
      }
      
      if($form->isValid()){
         $this->processEditLinkForm($form, $rec);
         $this->infoMsg()->addMessage($this->tr('Odkaz byl uložen'));
         $this->link()->route('list')->redirect();
      }
      
      $this->view()->form = $form;
      $this->view()->linkRecord = $rec;
   }
   
   /**
    * 
    * @param Model_ORM_Record $link
    * @return \Form
    */
   protected function createEditLinkForm(Model_ORM_Record $link = null)
   {
      $f = new Form('editLink');
      
      $eTitle = new Form_Element_Text('title', $this->tr('Titulek'));
      $eTitle->addValidation(new Form_Validator_NotEmpty());
      $f->addElement($eTitle);
      
      $mCats = new Model_Category();
      $categories = $mCats->onlyWithAccess()->order(Model_Category::COLUMN_NAME)->records();
      
      $eCat = new Form_Element_Select('category', $this->tr('Interní kategorie'));
      $eCat->setSubLabel($this->tr('Jiný odkaz můžete vložit níže'));
      $eCat->addOption('Odakz níže', '0');
      foreach ($categories as $cat) {
         $eCat->addOption($cat->{Model_Category::COLUMN_NAME}, $cat->getPK());
      }
      
      $f->addElement($eCat);
      
      $eTarget = new Form_Element_Text('target', $this->tr('Odkaz'));
      $eTarget->setSubLabel($this->tr('Buď celý odkaz nebo relativní adresu'));
      $f->addElement($eTarget);
      
      $eImg = new Form_Element_File('img', $this->tr('Obrázek'));
      $eImg->addValidation(new Form_Validator_FileExtension('jpg'));
      $f->addElement($eImg);
      
      $eExtern = new Form_Element_Checkbox('external', $this->tr('Otevřít v novém okně'));
      $f->addElement($eExtern);
      
      $eSave= new Form_Element_SaveCancel('save');
      $f->addElement($eSave);
      
      if($f->isSend() && $f->category->getValues() == 0 && $f->target->getValues() == null){
         $eCat->setError($this->tr('Musíte vybrat kategorii nebo odkaz'));
      }
      
      if($link){
         $f->title->setValues($link->{LinksList_Model_Links::COLUMN_TITLE});
         $f->category->setValues($link->{LinksList_Model_Links::COLUMN_CATEGORY});
         $f->target->setValues($link->{LinksList_Model_Links::COLUMN_EXTERNAL});
         $f->external->setValues($link->{LinksList_Model_Links::COLUMN_TARGET});
      }
      
      return $f;
   }
   
   protected function processEditLinkForm(Form $form, Model_ORM_Record $linkRec = null)
   {
      if($linkRec == null){
         $linkRec = LinksList_Model_Links::getNewRecord();
      }
      
      $linkRec->{LinksList_Model_Links::COLUMN_TITLE} = $form->title->getValues();
      $linkRec->{LinksList_Model_Links::COLUMN_CATEGORY} = $form->category->getValues();
      $linkRec->{LinksList_Model_Links::COLUMN_EXTERNAL} = $form->external->getValues();
      $linkRec->{LinksList_Model_Links::COLUMN_ID_CATEGORY} = $this->category()->getId();
      $linkRec->{LinksList_Model_Links::COLUMN_TARGET} = $form->target->getValues();
      $linkRec->save();
      
      // process image
      if($form->img->getValues() != null){
         $file = new File($form->img);
         $file->move($this->module()->getDataDir())->rename('link-'.$linkRec->getPK().'.'.$file->getExtension(), false);
      }
   }

   protected function checkDeleteLink()
   {
      $f = new Form('linkDelete');
      
      $eId = new Form_Element_Hidden('id');
      $f->addElement($eId);
      
      $eSub = new Form_Element_Submit('del', $this->tr('Smazat'));
      $f->addElement($eSub);
      
      if($f->isValid()){
         $m = new LinksList_Model_Links();
         $m->delete($f->id->getValues());
         $this->infoMsg()->addMessage($this->tr('Odkaz byl smazán'));
         $this->link()->redirect();
      }
      
      $this->view()->formDelete = $f;
   }

   public function settings(&$settings, Form &$form)
   {
      parent::settings($settings, $form);
      // znovu protože mohl být už jednou validován bez těchto hodnot
      if ($form->isValid()) {
      }
   }

}
