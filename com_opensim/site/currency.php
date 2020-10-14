<?php
/*
 * @component jOpenSim Component
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */

// For JED
if(!defined('_JEXEC')) define( '_JEXEC', 1 );
defined('_JEXEC') or die('Restricted Access');

function parseXmlRpc($xmlstring) {
	$suchmuster = '/\<methodName\>([^\<]*)/';
	preg_match_all($suchmuster,$xmlstring,$treffer);
	if(count($treffer) == 2) {
		if(isset($treffer[1][0])) $retval['method'] = $treffer[1][0];
		else $retval['method'] = FALSE;
	} else {
		$retval['method'] = FALSE;
	}
	$retval['treffer']	= $treffer;
	$retval['decode'] = xmlrpc_decode($xmlstring);
	return $retval;
}

$xmlrpc		= file_get_contents("php://input");

$request	= parseXmlRpc($xmlrpc);

$request = xmlrpc_encode_request($request['method'],$request['decode']);
$context = stream_context_create(array('http' => array(
    'method' => "POST",
    'header' => "Content-Type: text/xml",
    'content' => $request
)));

$servername	= $_SERVER['SERVER_NAME'];
$serverport	= $_SERVER['SERVER_PORT'];
$scriptname	= $_SERVER['SCRIPT_NAME'];
$strlen		= strlen("components/com_opensim/currency.php");
$joomlabase	= substr($scriptname,0,($strlen * -1));

if($serverport == "443") $url = "https://";
else $url = "http://";

$url .= $servername.$joomlabase."index.php?option=com_opensim&view=interface";

$file = file_get_contents($url, false, $context);
$response = xmlrpc_decode($file);
if ($response && xmlrpc_is_fault($response)) {
    trigger_error("xmlrpc: $response[faultString] ($response[faultCode])");
} else {
    echo xmlrpc_encode($response);
}

?>