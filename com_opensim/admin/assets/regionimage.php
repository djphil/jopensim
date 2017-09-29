<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2017 FoTo50 http://www.jopensim.com
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// For JED
define( '_JEXEC', 1 );
defined('_JEXEC') or die('Restricted Access');

function image_error($errormessage = "") {
	if(!$errormessage) $errormessage = "unknown error";
	$mypath = pathinfo($_SERVER['SCRIPT_FILENAME']);
	$noregionimage = $mypath['dirname'].'/images/noregion.png';
	/*$noregionimage = './noregion.png';*/
	$img = imagecreatefrompng($noregionimage);
	$textcolor = ImageColorAllocate ($img, 255, 255, 0);
	ImageString($img,5,20,200, $errormessage, $textcolor);
	header("Content-Type: image/png");
	imagepng($img);
	imagedestroy($img);
}

$server		= (isset($_REQUEST['mapserver']))	? $_REQUEST['mapserver']:"localhost";
$port		= (isset($_REQUEST['mapport']))		? $_REQUEST['mapport']:"9000";
$scale		= (isset($_REQUEST['scale']))		? $_REQUEST['scale']:"256";

$defaultX	= (isset($_REQUEST['defaultX']))	? $_REQUEST['defaultX']:0;
$defaultY	= (isset($_REQUEST['defaultY']))	? $_REQUEST['defaultY']:0;

$serveruri = $server.":".$port;
if(substr($serveruri,0,4) != "http") $serveruri = "http://".$serveruri;
if(isset($_REQUEST['uuid'])) $uuid = str_replace("-","",$_REQUEST['uuid']);
else $uuid = "";

$source			= $serveruri."/index.php?method=regionImage".$uuid."";
$curl			= extension_loaded('curl');
$file_content	= "";

if(!$curl) { // there is no way to read from outside :( at least display an error image
	image_error("read error");
} else {
	ob_start();
	$ch = curl_init($source);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_exec($ch);
	$response = curl_getinfo($ch);
	if($response['http_code'] == 200) {
		$file_content = ob_get_contents();
		ob_end_clean();
	} else { // could not open the image with cURL - display error image
		ob_end_clean();
		image_error("cURL error ".$response['http_code']);
	}
}


// scale the data to the final image and send it to the browser
if($file_content) {
	$img = imagecreatefromstring($file_content);
	if($defaultX > 0 && $defaultY > 0) {
		$defaultY = 256 - $defaultY;
		$orange = ImageColorAllocate ($img,249,202,15);
		imageellipse($img,$defaultX,$defaultY,10,10,$orange);
		imageellipse($img,$defaultX,$defaultY,11,11,$orange);
		imageellipse($img,$defaultX,$defaultY,12,12,$orange);
		imageellipse($img,$defaultX,$defaultY,13,13,$orange);
	}
	if($scale == 256) {
		header("Content-Type: image/png");
		imagepng($img);
		imagedestroy($img);
	} else {
		$img_scaled = imagecreatetruecolor($scale,$scale);
		imagecopyresampled($img_scaled,$img,0,0,0,0,$scale,$scale,256,256);
		header("Content-Type: image/png");
		imagepng($img_scaled);
		imagedestroy($img);
		imagedestroy($img_scaled);
	}
} else {
	image_error("empty image");
}
?>