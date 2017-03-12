<?php
/**
 * @module      OpenSim jSonStats (mod_opensim_jsonstats)
 * @copyright   Copyright (C) djphil 2017, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 * @creative    CC-BY-NC-SA 4.0
**/

// no direct access
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

$doc = JFactory::getDocument();
$assetpath = JUri::base(true)."/modules/mod_opensim_jsonstats/assets/";
$doc->addStyleSheet($assetpath.'mod_opensim_jsonstats.css');
// $doc->addScript($assetpath.'jquery.min.js');

require_once __DIR__ . '/helper.php';
require_once __DIR__ . '/functions.php';

/*
[Startup]
    Stats_URI = "jsonSimStats"
*/

// Load variables
$a = $params->get('domainname');
$b = $params->get('domainport');
$c = $params->get('jsonstatsssl');
$d = $params->get('jsonstatsuri');
$jsonstatsURI = getjSonStatsURI($a, $b, $c, $d);
$refreshrate = $params->get('jsonstatsrefreshrate');
// $version = $params->get('jsonstatsversion');
// $json = curl_get_contents($jsonstatsURI);

// get module params
$moduleclass_sfx = $params->get('moduleclass_sfx');
$layout = $params->get('layout', 'default');
require JModuleHelper::getLayoutPath('mod_opensim_jsonstats', $layout);
?>

<!--Temporary fix conflicts with Joomla jQuery Library-->
<script src="<?php echo $assetpath?>jquery.min.js"></script>
