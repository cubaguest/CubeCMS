<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class AdminHtaccess_Controller extends Controller {
   public function mainController() 
   {
      $this->checkControllRights();

      // check writable
      if(!is_writable(Face::getCurrent()->getDir())){
         throw new ForbiddenAccessException($this->tr('Aplikace nemá přístup k zápisu do souboru htaccess.'));
      }
    
      
      $m = new Model_Sites();
      
      $this->view()->sites = $m->order(array(Model_Sites::COLUMN_DOMAIN))->records();
      
   }
   
   public function editController($id)
   {
      $this->checkControllRights();
      
      $site = Model_Sites::getRecord($id);
      
      if(!$site){
         throw new UnexpectedPageException();
      }
      
      $form = new Form('htaccess_');
      
      if($site->{Model_Sites::COLUMN_IS_MAIN}){
         $eWWW = new Form_Element_Checkbox('www', $this->tr('Aktivovat přesměrování na www'));
         $eWWW->setValues(Model_Config::getValue('HTACCESS_WWW_REDIRECT', false));
         $form->addElement($eWWW);

         $eExpiration = new Form_Element_Checkbox('caching', $this->tr('Aktivovat expiraci statických zdrojů'));
         $eExpiration->setValues(Model_Config::getValue('HTACCESS_USE_CACHE', false));
         $form->addElement($eExpiration);
      }
      $eCusotm = new Form_Element_TextArea('custom', $this->tr('Vlastní řetězec'));
      $eCusotm->setValues($this->getHtaccessCustomContent($site->{Model_Sites::COLUMN_DIR}));
      $form->addElement($eCusotm);
      
      $eSave= new Form_Element_SaveCancel('save', array($this->tr('Uložit'), $this->tr('Zavřít')));
      $form->addElement($eSave);
      
      if($form->isSend() && $form->save->getValues() == false){
         $this->link()->route()->redirect();
      }
      
      if($form->isValid()){
         if($site->{Model_Sites::COLUMN_IS_MAIN}){
            self::generateMainHtaccess(
                $form->caching->getValues(),
                $form->www->getValues(),
                $form->custom->getValues()
                );
            Model_ConfigGlobal::setValue('HTACCESS_WWW_REDIRECT', (bool)$form->www->getValues(), 'bool');
            Model_ConfigGlobal::setValue('HTACCESS_USE_CACHE', (bool)$form->caching->getValues(), 'bool');
         } else {
            self::generateSubHtaccess(
                $site->{Model_Sites::COLUMN_DIR},
                $form->custom->getValues()
                );
         }
         $this->infoMsg()->addMessage($this->tr('Htaccess byl uložen'));
         $this->link()->redirect();
      }
      
      $this->view()->form = $form;
      $this->view()->domain = $site;
      $this->view()->current = self::getHtaccessContent($site->{Model_Sites::COLUMN_DIR});
   }
   
   
   public static function getHtaccessCustomContent($dir = null)
   {
      if(is_file(AppCore::getAppLibDir().$dir.DIRECTORY_SEPARATOR.'.htaccess')){
         $cnt = file_get_contents(AppCore::getAppLibDir().$dir.DIRECTORY_SEPARATOR.'.htaccess');
         $matches = array();
         preg_match('/#CUBECMS(.+)#CUBECMS/s',$cnt,$matches);
         if(isset($matches[1])){
            return $matches[1];
         }
         
      }
      return null;
   }
   
   public static function getHtaccessContent($dir = null)
   {
      if(is_file(AppCore::getAppLibDir().$dir.DIRECTORY_SEPARATOR.'.htaccess')){
         return file_get_contents(AppCore::getAppLibDir().$dir.DIRECTORY_SEPARATOR.'.htaccess');
      }
      return null;
   }
   
   
   protected static function createHtaccessBase()
   {
      $str = "
RewriteRule ^config/config\.php$ / [R=permanent,L]
RewriteCond %{REQUEST_URI} !\.[[:alnum:]]+$
RewriteRule ^(.+[^/])$ /$1/ [L,R=301,QSA]

RewriteRule ^admin/$ /ucet/ [L,R=301,QSA]
#RewriteRule ^login/$ /ucet/ [L,R=301,QSA]

RewriteCond %{REQUEST_FILENAME} -s [OR]
RewriteCond %{REQUEST_FILENAME} -l [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^.*$ - [NC,L]
RewriteRule ^.*$ /index.php [NC,L]
\n";
      return $str;
   }
   
   protected static function createHtaccessWWWRedirect()
   {
      $str = "
RewriteCond %{HTTP_HOST} !^www\.
RewriteRule ^(.*)$ http://www.%{HTTP_HOST}/$1 [R=301,L]
\n";
      return $str;
   }

   protected static function createHtaccessSubDomains($wwwRedirect = false)
   {
      $model = new Model_Sites();
      $sites = $model->where(Model_Sites::COLUMN_IS_MAIN." = 0", array())->records();
      
      $primaryDomainsParts = explode('.', CUBE_CMS_PRIMARY_DOMAIN);
      $basePrimaryDomain = $primaryDomainsParts[count($primaryDomainsParts)-2].'.'.$primaryDomainsParts[count($primaryDomainsParts)-1];
      
      $str = null;
      foreach ($sites as $site) {
         // jestli je zapnut www redirect a primární doména se neshoduje z doménou webu, přidej www redirect
         if($basePrimaryDomain != $site->getBaseDomain() && strpos($site->getFullDomain(), 'www.') === 0){
             $str .=
                  "RewriteCond %{HTTP_HOST} ^".str_replace('www.', '', $site->getFullDomain())."$\n"
                  ."RewriteRule ^(.*)$ http://".$site->getFullDomain()."/$1 [R=301,L]\n";  
         }
         
         $str .= "RewriteCond %{HTTP_HOST} ^".$site->getFullDomain()."$\n"
             ."RewriteRule ^(.*)$ /".$site->{Model_Sites::COLUMN_DIR}."/$1 [L,QSA]\n";

      }
      return $str;
   }
   
   protected static function createHtaccessExpires()
   {
      $str = "
<IfModule mod_expires.c>
   ExpiresActive On
   ExpiresDefault A300
   ExpiresByType image/x-icon A2592000
   ExpiresByType application/x-javascript A3600
   ExpiresByType text/css A604800
   ExpiresByType image/gif A604800
   ExpiresByType image/png A604800
   ExpiresByType image/jpeg A604800
   ExpiresByType text/plain A300
   ExpiresByType application/x-shockwave-flash A604800
   ExpiresByType video/x-flv A604800
   ExpiresByType application/pdf A604800
   ExpiresByType text/html A300
</IfModule>

<ifModule mod_headers.c>
   <FilesMatch \".(eot|ttf|otf|woff)\">
      Header set Access-Control-Allow-Origin \"*\"
   </FilesMatch>
   <filesMatch \"\.(css)$\">
      Header set Cache-Control \"public\"
      Header set Cache-Control \"max-age=604800\"
   </filesMatch>
   <filesMatch \"\.(ico|jpe?g|png|gif|swf|css|flv|pdf|eot|ttf|otf|woff)$\">
      Header set Cache-Control \"public\"
      Header set Cache-Control \"max-age=2592000\"
   </filesMatch>
   <filesMatch \"\.(js|json)$\">
      Header set Cache-Control \"private\"
   </filesMatch>
   <filesMatch \"\.(x?html?|php|phtml)$\">
      Header set Cache-Control \"private, max-age=60, must-revalidate\"
   </filesMatch>
</ifModule>
\n";
      return $str;
   }
   
   protected static function createHtaccessInternalApps()
   {
      $str = "
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^cache/imgc/([a-z0-9]+)/([x0-9]+c?(?:-f_[0-9]*(?:_[0-9]*)?(?:_[0-9]*)?(?:_[0-9]*)?(?:_[0-9]*)?)?)/(.+)$ index.php?internalApp=imagecacher&s=$3&tf=$1&is=$2 [L,R=302,QSA]
\n";
      return $str;
   }
   
   protected static function createHtaccessCustom($cnt = false, $dir = null)
   {
      if(!$cnt){
         $cnt = self::getHtaccessCustomContent($dir);
      }
      $cnt = rtrim($cnt);
      return "#CUBECMS\n$cnt\n#CUBECMS\n\n";
   }
   
   protected static function createHtaccessSubDomainStatic()
   {
      $m = new Model_Sites();
//      $mainsSite = $m->where(Model_Sites::COLUMN_IS_MAIN." = 1", array())->record();
//      $domain = $mainsSite->getFullDomain();
      $domain = CUBE_CMS_PRIMARY_DOMAIN;
      
      $str = "
RewriteCond $1 ^jscripts/
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_URI} \.(js)$
Rewriterule (.*) /index.php?internalApp=proxyjs&path=$1 [R=301,L,QSA]

RewriteCond $1 ^faces/[a-z]+/jscripts/
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_URI} \.(js)$
Rewriterule (.*) /index.php?internalApp=proxyjs&path=$1 [R=301,L,QSA]    

RewriteCond $1 ^modules/[a-z]+/jscripts/
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_URI} \.(js)$
Rewriterule (.*) /index.php?internalApp=proxyjs&path=$1 [R=301,L,QSA]  

RewriteCond $1 ^jscripts/      
RewriteCond %{REQUEST_FILENAME} !-f
Rewriterule (.*) http://$domain/$1 [L]
RewriteCond $1 ^images/
RewriteCond %{REQUEST_FILENAME} !-f
Rewriterule (.*) http://$domain/$1 [L]
RewriteCond $1 ^fonts/
RewriteCond %{REQUEST_FILENAME} !-f
Rewriterule (.*) http://$domain/$1 [L]
RewriteCond $1 ^data/
RewriteCond %{REQUEST_FILENAME} !-f
Rewriterule (.*) http://$domain/$1 [L]
RewriteCond $1 ^stylesheets/
RewriteCond %{REQUEST_FILENAME} !-f
Rewriterule (.*) http://$domain/$1 [L]
RewriteCond $1 ^faces/
RewriteCond %{REQUEST_FILENAME} !-f
Rewriterule (.*) http://$domain/$1 [L]

RewriteCond %{REQUEST_FILENAME} !-f
Rewriterule modules/([a-z]+)/stylesheets/(.*) http://$domain/modules/$1/stylesheets/$2 [L,R=303]
RewriteCond %{REQUEST_FILENAME} !-f
Rewriterule modules/([a-z]+)/jscripts/(.*) http://$domain/modules/$1/jscripts/$2 [L,R=303]
RewriteCond %{REQUEST_FILENAME} !-f
Rewriterule modules/([a-z]+)/images/(.*) http://$domain/modules/$1/images/$2 [L,R=303]
RewriteCond %{REQUEST_FILENAME} !-f
Rewriterule faces/([A-Za-z0-1]+)/images/(.*) http://$domain/faces/$1/images/$2 [L,R=303]   
\n";
      return $str;

   }
   

   public static function generateMainHtaccess(
       $enableCaching = null,
       $wwwRedirect = null,
       $customCnt = false
       )
   {
      if($enableCaching === null){
         $enableCaching = Model_ConfigGlobal::getValue('HTACCESS_USE_CACHE', false);
      }
      if($wwwRedirect === null){
         $wwwRedirect = Model_ConfigGlobal::getValue('HTACCESS_WWW_REDIRECT', true);
      }
      $content = "RewriteEngine On\n";
      if($enableCaching){
         $content .= self::createHtaccessExpires();
      }
      if($wwwRedirect){
         $content .= self::createHtaccessWWWRedirect();
      }
      $content .= self::createHtaccessCustom($customCnt);
      
      $content .= self::createHtaccessSubDomains($wwwRedirect);
      $content .= self::createHtaccessInternalApps();
      $content .= self::createHtaccessBase();
      if(is_writable(AppCore::getAppLibDir().'.htaccess')){
         file_put_contents(AppCore::getAppLibDir().'.htaccess', $content);
      }
   }
   
   public static function generateSubHtaccess($siteDir, $customCnt = false)
   {
      $content = "RewriteEngine On\n";
      $content .= self::createHtaccessCustom($customCnt, $siteDir);
      $content .= self::createHtaccessSubDomainStatic();
      $content .= self::createHtaccessInternalApps();
      $content .= self::createHtaccessBase();
      if(is_writable(AppCore::getAppLibDir().$siteDir.DIRECTORY_SEPARATOR.'.htaccess')){
         file_put_contents(AppCore::getAppLibDir().$siteDir.DIRECTORY_SEPARATOR.'.htaccess', $content);
      }
   }
}
