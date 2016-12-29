<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2016 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// For JED
define( '_JEXEC', 1 );
defined('_JEXEC') or die('Restricted Access');

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

$redirect = "Location: ".$source;
header($redirect);

?>