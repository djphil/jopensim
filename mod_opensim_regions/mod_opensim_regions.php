<?php
/*
 * @module OpenSim Regions
 * @copyright Copyright (C) 2018 FoTo50 http://www.jopensim.com
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$jopensimpath = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_opensim'.DIRECTORY_SEPARATOR;

if (!class_exists( 'opensimModelOpensim' )){
	require_once($jopensimpath.'models'.DIRECTORY_SEPARATOR.'opensim.php');
}

if (!class_exists( 'opensim' )){
	require_once($jopensimpath.'includes'.DIRECTORY_SEPARATOR.'opensim.class.php');
}

// include the helper file
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'helper.php');
$regionhelper = new ModOpenSimRegionsHelper($params);
$regions = $regionhelper->getRegions();

// Table style
$tableclass = "table";
if ($params->get('tableclasscondensed')) $tableclass .= " table-condensed";
if ($params->get('tableclassstriped')) $tableclass .= " table-striped";
if ($params->get('tableclasshover')) $tableclass .= " table-hover";

// include the template for display
require(JModuleHelper::getLayoutPath('mod_opensim_regions'));
?>
