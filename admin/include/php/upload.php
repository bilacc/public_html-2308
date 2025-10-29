<?php
require_once('../../../lib/functions.php');

$ds = DIRECTORY_SEPARATOR;
$storeFolder = '../../../upload_data/tmp';
 
if (!empty($_FILES)){
	$type = key($_FILES);
	$cnt = $_SESSION[$type]['cnt'];
	
	$_SESSION[$type][$cnt] = $_FILES[$type];	
	$_SESSION[$type]['cnt']++;
	
    $tempFile = $_FILES[$type]['tmp_name'];                   
    $targetPath = dirname( __FILE__ ) . $ds. $storeFolder . $ds; 
    $targetFile =  $targetPath. clean_uri($_FILES[$type]['name']); 
	move_uploaded_file($tempFile,$targetFile);
}
?> 
