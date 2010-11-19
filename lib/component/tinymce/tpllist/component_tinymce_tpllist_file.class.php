<?php
/**
 * Třída pro komnponenty Wysiwing editoru TinyMCE
 */
class Component_TinyMCE_TPLList_System extends Component_TinyMCE_TPLList {
   protected function  loadList() {
      // načtení externích
      if(file_exists(Template::faceDir().Template::TEMPLATES_DIR.DIRECTORY_SEPARATOR.self::EXTERNAL_TEMPLATES_FILE)){
         $externalTpls = file_get_contents(Template::faceDir().Template::TEMPLATES_DIR.URL_SEPARATOR.self::EXTERNAL_TEMPLATES_FILE);
         $matches = array();
         preg_match_all('/\[[^][]+\]/', $externalTpls, $matches);
         foreach ($matches[0] as $extFile) {
            print ($extFile.",\n");
         }
      }
   }
}
?>
