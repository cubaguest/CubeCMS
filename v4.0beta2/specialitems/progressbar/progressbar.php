<?php
/**
 * Skript zobrazuje prograsbar
 * 
 * Zatím betaverze
 */
require_once '../../lib/sessions.class.php';

define('SESSION_NAME', 'vypecky_session');
define('SESSION_PROGRESS_NAME', 'progress');


$session = new Sessions(SESSION_NAME);

//echo '<pre>';
//print_r($_SESSION);
//echo '</pre>';

//$progres = $session->get(SESSION_PROGRESS_NAME);
$progres = $_SESSION['progress'];

$images = $progres['percent']/10;
$images = round($images);

//echo 'Progress: '.$progres.'%';
echo("<?xml version=\"1.0\" encoding=\"UTF-8\" ?>");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Insert title here</title>
<script language="javascript" type="text/javascript">
setTimeout("window.location.reload(true)",100);
</script>
</head>

<body onload="<?if($progres['percent'] == -1){echo("window.close();");} ?>">
<? echo($progres['text'].': '.$progres['percent']); ?> % <br />
<? for ($i=0; $i <= $images; $i++){ ?>
<img src="../../images/progressbar.png" alt="progressbar"/>
<?}?><br />
<? echo($progres['message']); ?><br />
<a href="javascript:self.close()">close window</a>
</body>
</html>

<?
if($progres['percent'] == -1){
	$session->remove(SESSION_PROGRESS_NAME);
}
?>