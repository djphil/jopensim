<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// For JED
define( '_JEXEC', 1 );
defined('_JEXEC') or die('Restricted Access');

$server		= (isset($_REQUEST['mapserver']))	? $_REQUEST['mapserver']:"localhost";
$port		= (isset($_REQUEST['mapport']))		? $_REQUEST['mapport']:"9000";
$scale		= (isset($_REQUEST['scale']))		? $_REQUEST['scale']:"256";

$defaultX	= (isset($_REQUEST['defaultX']))	? $_REQUEST['defaultX']:0;
$defaultY	= (isset($_REQUEST['defaultY']))	? $_REQUEST['defaultY']:0;

$serveruri = $server.":".$port;

if(substr($serveruri,0,4) != "http") $serveruri = "http://".$serveruri;
if(isset($_REQUEST['uuid'])) $uuid = str_replace("-","",$_REQUEST['uuid']);
else $uuid = "";

$source = $serveruri."/index.php?method=regionImage".$uuid."";

$redirect = "Location: ".$source;
header($redirect);
?>