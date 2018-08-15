<?php
/**
 * @module      OpenSim Panorama (mod_opensim_panorama)
 * @copyright   Copyright (C) djphil 2018, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 * @creative    CC-BY-NC-SA 4.0
**/

// no direct access
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

$doc = JFactory::getDocument();
$assetpath = JUri::base(true)."/modules/mod_opensim_panorama/assets/";
$doc->addStyleSheet($assetpath.'mod_opensim_panorama.css');
$doc->addScript($assetpath.'three.min.js');

require_once __DIR__ . '/helper.php';

// Load variables
// $panoramaimg = $params->get('panoramaimg');
$panoramaimg = JUri::base(true)."/".$params->get('panoramaimg');
$panoramalon = $params->get('panoramalon', 0);
$panoramalat = $params->get('panoramalat', 0);
$panoramafov = $params->get('panoramafov', 75);
$panoramaanim = $params->get('panoramaanim', 1);
$panoramaspeed = $params->get('panoramaspeed', 0.125);
$panoramadisto = $params->get('panoramadisto', 0);
$panoramastyle = $params->get('panoramastyle', 1);
$panoramaheight = $params->get('panoramaheight', 300);
$panoramawidth = $params->get('panoramawidth', 600);
$panoramaborder = $params->get('panoramaborder', 1);
$panoramacolor = $params->get('panoramacolor', '#000000');
$panoramarounded = $params->get('panoramarounded', 1);
$panoramaradius = $params->get('panoramaradius', 5);
$panoramagrabbing = $params->get('panoramagrabbing', 1);
$panoramagrabstyle = $params->get('panoramagrabstyle', 'grab');

// Adjust variables
if ($panoramalon > 180.0) $panoramalon = 180.0;
if ($panoramalon < -180.0) $panoramalon = -180.0;
if ($panoramalat > 180.0) $panoramalat = 180.0;
if ($panoramalat < -180.0) $panoramalat = -180.0;
if ($panoramafov > 150.0) $panoramafov = 150.0;
if ($panoramafov < -150.0) $panoramafov = -150.0;
if ($panoramaspeed > 1.0) $panoramaspeed = 1.0;
if ($panoramaspeed < -1.0) $panoramaspeed = -1.0;

// Get module params
$moduleclass_sfx = $params->get('moduleclass_sfx');
$layout = $params->get('layout', 'default');
require JModuleHelper::getLayoutPath('mod_opensim_panorama', $layout);
?>
