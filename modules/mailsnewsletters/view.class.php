<?php
/** 
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class MailsNewsletters_View extends View {
   public function init()
   {
      Template_Module::setEdit(true);
   }
   
	public function mainView() {
      $this->composeView();
	}
	
	public function composeView() {
	   $this->template()->addFile('tpl://compose.phtml');

	   Template_Navigation::addItem($this->tr('Vytvoření newsleteru'), $this->link(), null, null, null, true);
	   
	   $settings = new Component_TinyMCE_Settings_Mail();
// 	   $settings->setForceDir($this->newsletterDataDir );
	   $settings->setVariablesURL($this->link()->route('replacements'));
	   $settings->setTemplatesList((string)$this->link()->route('tpls')->param('export', 'jsmce'));
	   $settings->setForceDir($this->newsletterDataDir );
	   $this->setTinyMCE($this->form->content, $settings);
	}

	public function listView()
	{
	   $this->template()->addFile('tpl://newsleters_list.phtml');
	   
	   Template_Navigation::addItem($this->tr('Přehled newsletterů'), $this->link(), null, null, null, true);
	   
	   $toolbox = new Template_Toolbox2();
	   
	   $toolStatus = new Template_Toolbox2_Tool_Form($this->formStatus);
	   $toolStatus->setIcon('enable.png');
	   $toolbox->addTool($toolStatus);
	   
	   $toolPreview = new Template_Toolbox2_Tool_Redirect('previewNewsletter', $this->tr('Náhled newsletteru'));
	   $toolPreview->setIcon('eye.png')->setAction($this->link()->route('previewNewsletter'));
	   $toolbox->addTool($toolPreview);
	   
	   $toolEdit = new Template_Toolbox2_Tool_Redirect('editForm', $this->tr('Upravit newsletter'));
	   $toolEdit->setIcon('page_edit.png')->setAction($this->link()->route("compose"));
	   $toolbox->addTool($toolEdit);
	   
	   
	   $toolDelete = new Template_Toolbox2_Tool_Form($this->formDelete);
	   $toolDelete->setIcon('delete.png')->setConfirmMeassage($this->tr('Opravdu smazat newsletter?'));
	   $toolbox->addTool($toolDelete);
	   
	   $this->toolboxItem = $toolbox;
	}
	
	public function tplsView()
	{
	   if ($this->export == 'jsmce'){
	      // create tinyMCE export
	      Template_Output::setOutputType('js');
	      Template_Output::sendHeaders();
	      
	      $tplList = new Component_TinyMCE_TPLList();
	      foreach ($this->templates as $tpl) {
	         $tplList->addTpl($tpl->{MailsNewsletters_Model_Templates::COLUMN_NAME},
	             $this->link()->clear()->route('tplPreview', array('id' => $tpl->{MailsNewsletters_Model_Templates::COLUMN_ID}) ) );
	      }
	      
	      echo $tplList;
	      die;
	      return;
	   }
	   
	   $this->template()->addFile('tpl://tpl_list.phtml');
	   Template_Navigation::addItem($this->tr('Přehled šablon'), $this->link(), null, null, null, true);
	   
	   $toolbox = new Template_Toolbox2();
	   
	   $toolEdit = new Template_Toolbox2_Tool_Redirect('editForm', $this->tr('Upravit šablonu'));
	   $toolEdit->setIcon('page_edit.png')->setAction($this->link()->route("tplEdit"));
	   $toolbox->addTool($toolEdit);
	   
	   $toolPreview = new Template_Toolbox2_Tool_Redirect('previewTpl', $this->tr('Náhled šablony'));
	   $toolPreview->setIcon('eye.png')->setAction($this->link()->route('previewTpl'));
	   $toolbox->addTool($toolPreview);
	   
	   $toolDelete = new Template_Toolbox2_Tool_Form($this->formDelete);
	   $toolDelete->setIcon('delete.png')->setConfirmMeassage($this->tr('Opravdu smazat šablonu?'));
	   $toolbox->addTool($toolDelete);
	   
	   $this->toolboxItem = $toolbox;
	}
	
	public function tplAddView()
	{
	   $this->template()->addFile('tpl://tpl_edit.phtml');
	   Template_Navigation::addItem($this->tr('Přidání šablony'), $this->link(), null, null, null, true);
      
      $settings = new Component_TinyMCE_Settings_Mail();
//       $settings->document_base_url = $this->category()->getModule()->getDataDir(true).$this->newsletterDataDir."/";
      $settings->setForceDir($this->newsletterDataDir );
      
      $settings->setVariablesURL($this->link()->route('replacements'));
      $settings->setTemplatesList((string)$this->link()->route('tpls')->param('export', 'jsmce'));
      
      $this->setTinyMCE($this->form->content, $settings);
	}
	
	public function tplEditView()
	{
	   $this->tplAddView();
	}
	
	public function tplUploadView()
	{
	   $this->template()->addFile('tpl://tpl_upload.phtml');
	   Template_Navigation::addItem($this->tr('Nahrání šablony'), $this->link(), null, null, null, true);
	}
	
   public function tplPreviewView() 
   {
      $this->template()->addFile('tpl://tpl_preview.phtml');
   }
   
   public function newsletterPreviewView() 
   {
      $this->tplPreviewView();
   }
	
   public static function unscribeView($data)
   {
      $tpl = new Template_ModuleStatic(new Url_Link_ModuleRequest(), 'mailsnewsletters');
      $tpl->addFile('tpl://unscribe.phtml');
      $tpl->setVars($data);
      return $tpl;
   }

   public static function showNewsletterView($data)
   {
      $tpl = new Template_ModuleStatic(new Url_Link_ModuleRequest(), 'mailsnewsletters');
      $tpl->addFile('tpl://newsletter.phtml');
      $tpl->setVars($data);
      Template_Output::sendHeaders();
      echo $tpl;
      die;
   }
}
?>