<?php 
session_start();
?>
<!DOCTYPE html>
<html>
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
      <title>Facebook Access token generator for CubeCMS</title>
   </head>
   <body>
<form method="post" action="">
   <p>
      <label>Page ID:</label>
      <input name="pageid" value="<?php 
         if(isset($_POST['pageid'])){echo $_POST['pageid'];}
         else if(isset($_SESSION['pageId'])){echo $_SESSION['pageId'];}
         ?>" type="text"/><br />
      <label>APP ID:</label>
      <input name="appid" value="<?php 
         if(isset($_POST['appid'])){echo $_POST['appid'];}
         else if(isset($_SESSION['appId'])){echo $_SESSION['appId'];}
         ?>" type="text"/><br />
      <label>APP Secret:</label>
      <input name="appsecret" value="<?php 
         if(isset($_POST['appsecret'])){echo $_POST['appsecret'];}
         else if(isset($_SESSION['secret'])){echo $_SESSION['secret'];}
         ?>" type="text"/><br />
      <label>Access token:</label>
      <input name="accesstoken" value="<?php 
         if(isset($_POST['accesstoken'])){echo $_POST['accesstoken'];}
         if(isset($_GET['code'])){echo $_GET['code'];}
         ?>" type="text"/><br />
      <label>Krok:</label>
      <select name="step">
         <option value="1">1</option>
         <option value="2" <?php 
            if( ( isset($_POST['step']) && $_POST['step'] == 1 ) 
               OR (isset($_SESSION['pageId']))){echo 'selected="selected"';}?>>2</option>
      </select>
      <input type="submit" value="send" name="send" />
   </p>
</form>

<?php

if(!isset($_POST['send'])){
   die;
}

include_once '../../lib/nonvve/facebook/facebook.php';

if($_POST['step'] == 1){
   $facebook = new Facebook(array(
      'appId'  => $_POST['appid'],
      'secret' => $_POST['appsecret'],
      'cookie' => true
   ));
   
   $permisions = array(
      'manage_pages',
      'read_stream',
      'publish_stream',
      'create_event',
      'publish_checkins',
      'read_friendlists', // realy?
      'manage_friendlists', // realy?
      'offline_access',
   );
   
   echo '<a href="'.$facebook->getLoginUrl(array('scope'=>  implode(',', $permisions))).'">overit</a>';//
   
   $_SESSION['pageId'] = $_POST['pageid'];
   $_SESSION['appId'] = $_POST['appid'];
   $_SESSION['secret'] = $_POST['appsecret'];
   
} else if($_POST['step'] == 2){
   $facebook = new Facebook(array(
      'appId'  => $_POST['appid'],
      'secret' => $_POST['appsecret'],
      'cookie' => true
   ));
   
   echo "<h2>User wall</h2>";
   $user = $facebook->api('/me','GET');
   echo "<strong>Name:</strong> ".$user['name']." <br />";
   echo "<strong>ID:</strong> ".$user['id']." <br />";
   echo "<strong>Profile:</strong> ".$user['link']." <br />";
   echo "<strong>Access_token:</strong> ".$facebook->getAccessToken();
   
   echo "<h2>User Apps and pages access tokens</h2>";
   $result = $facebook->api("/me/accounts");
	foreach ($result['data'] as $item) {
      echo "<h3>".$item['name']." - ".$item['id']."</h3>";
      echo "<strong>Category:</strong> ".$item['category']."<br />";
      echo "<strong>Access_token:</strong> ".$item['access_token']."<br /><br />";
   };
   
} else if($_POST['step'] == 3){
   
}


?>
   </body>
</html>