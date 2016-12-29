<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2015 FoTo50 http://www.jopensim.com
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// For JED
define( '_JEXEC', 1 );
defined('_JEXEC') or die('Restricted Access');

// Many thanks to Alexander Brock through http://aktuell.de.selfhtml.org/artikel/php/existenz/ for this very useful function :)
function http_test_existance($url,$timeout = 10) {
	$timeout = (int)round($timeout/2+0.00000000001);
	$return = array();

//### 1 ###
	$inf = parse_url($url);

	if(!isset($inf['scheme']) or $inf['scheme'] !== 'http') return array('status' => -1);
	if(!isset($inf['host'])) return array('status' => -2);
	$host = $inf['host'];

	if(!isset($inf['path'])) return array('status' => -3);
	$path = $inf['path'];
	if(isset($inf['query'])) $path .= '?'.$inf['query'];

	if(isset($inf['port'])) $port = $inf['port'];
	else $port = 80;

//### 2 ###
	$pointer = fsockopen($host, $port, $errno, $errstr, $timeout);
	if(!$pointer) return array('status' => -4, 'errstr' => $errstr, 'errno' => $errno);
	socket_set_timeout($pointer, $timeout);

//### 3 ###
	$head =
	  'HEAD '.$path.' HTTP/1.1'."\r\n".
	  'Host: '.$host."\r\n";

	if(isset($inf['user']))
		$head .= 'Authorization: Basic '.
		base64_encode($inf['user'].':'.(isset($inf['pass']) ? $inf['pass'] : ''))."\r\n";
	if(func_num_args() > 2) {
		for($i = 2; $i < func_num_args(); $i++) {
			$arg = func_get_arg($i);
			if(
				strpos($arg, ':') !== false and
				strpos($arg, "\r") === false and
				strpos($arg, "\n") === false
			) {
				$head .= $arg."\r\n";
			}
		}
	}
	else $head .= 
		'User-Agent: Selflinkchecker 1.0 ('.$_SERVER['PHP_SELF'].')'."\r\n";

	$head .=
		'Connection: close'."\r\n"."\r\n";

//### 4 ###
	fputs($pointer, $head);

	$response = '';

	$status = socket_get_status($pointer);
	while(!$status['timed_out'] && !$status['eof']) {
		$response .= fgets($pointer);
		$status = socket_get_status($pointer);
	}
	fclose($pointer);
	if($status['timed_out']) {
		return array('status' => -5, '_request' => $head);
	}

//### 5 ###
	$res = str_replace("\r\n", "\n", $response);
	$res = str_replace("\r", "\n", $res);
	$res = str_replace("\t", ' ', $res);

	$ares = explode("\n", $res);
	$first_line = explode(' ', array_shift($ares), 3);

	$return['status'] = trim($first_line[1]);
	$return['reason'] = trim($first_line[2]);

	foreach($ares as $line) {
		$temp = explode(':', $line, 2);
		if(isset($temp[0]) and isset($temp[1])) {
			$return[strtolower(trim($temp[0]))] = trim($temp[1]);
		}
	}
	$return['_response'] = $response;
	$return['_request'] = $head;

	return $return;
}

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




if(isset($_REQUEST['mapserver'])) $server = $_REQUEST['mapserver'];
else $server = "localhost";
if(isset($_REQUEST['mapport'])) $port = $_REQUEST['mapport'];
else $port = "9000";
if(isset($_REQUEST['scale'])) $scale = $_REQUEST['scale'];
else $scale = "256";

if(isset($_REQUEST['defaultX'])) $defaultX = $_REQUEST['defaultX'];
else $defaultX = 0;
if(isset($_REQUEST['defaultY'])) $defaultY = $_REQUEST['defaultY'];
else $defaultY = 0;


$serveruri = $server.":".$port;
if(substr($serveruri,0,4) != "http") $serveruri = "http://".$serveruri;
if(isset($_REQUEST['uuid'])) $uuid = str_replace("-","",$_REQUEST['uuid']);
else $uuid = "";

$source = $serveruri."/index.php?method=regionImage".$uuid."";
//echo $source;
//exit;

$curl = extension_loaded('curl');
$fopen = ini_get('allow_url_fopen');

$file_content = "";

if(!$curl && !$fopen) { // there is no way to read from outside :( at least display an error image
	image_error("read error");
} elseif($curl) {
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
} else {
	$fexists = http_test_existance($source);
	if($fexists['status'] == 200) {
		$handle = @fopen($source,'r');
		if($handle) {
			while (!feof($handle)) {
				$file_content .= fread($handle,1024);
			}
			fclose($handle);
		} else { // could not open the image with fopen - display error image
			image_error("fopen error (unknown)");
		}
	} else {
		image_error("fopen error (status: ".$fexists['status'].")");
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