<?php
/**
 * @module OpenSim Balance (mod_opensim_balance)
 * @copyright Copyright (C) 2017 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
**/

// no direct access
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

if (!defined('DS')) define("DS",DIRECTORY_SEPARATOR);

// require the opensim class

// include the helper file
require_once(dirname(__FILE__).DS.'helper.php');

// get a parameter from the module's configuration
$moduleTag          = $params->get('module_tag', 'div');
$bootstrapSize      = (int) $params->get('bootstrap_size', 0);
$moduleClass        = $bootstrapSize != 0 ? ' span' . $bootstrapSize : '';
$headerTag          = htmlspecialchars($params->get('header_tag', 'h3'));
$headerClass        = htmlspecialchars($params->get('header_class', 'page-header'));
$moduleclass_sfx    = htmlspecialchars($params->get('moduleclass_sfx'));
$layout				= $params->get('layout', 'default');

$doc = JFactory::getDocument();

// $doc->addStyleSheet(JURI::base( true ).'/media/jui/css/icomoon.css');
// $assetpath = JUri::base(true)."/components/com_opensim/assets/";
// $doc->addStyleSheet($assetpath.'opensim.css');
// $doc->addStyleSheet($assetpath.'opensim.override.css');

$assetpath = JUri::base(true)."/modules/mod_opensim_balance/assets/";
$doc->addStyleSheet($assetpath.'mod_opensim_balance.css');

$user			= JFactory::getUser();
$userid			= $user->id;
$os_balance		= new ModOpenSimBalanceHelper($params);
$userbalance	= $os_balance->getUserBalance($userid);
$buylink		= JRoute::_('index.php?Itemid='.$os_balance->buylink);
$selllink		= JRoute::_('index.php?Itemid='.$os_balance->selllink);
$displaylink    = JRoute::_('index.php?Itemid='.$os_balance->displaylink);

require_once(dirname(__FILE__).DS.'assets/styles.php');

if ($os_balance->showcurrency) {
    jimport('joomla.application.component.helper');
    $comp_params = JComponentHelper::getParams('com_opensim');
    $currency = $comp_params->get('jopensimmoney_currencyname',"");
} else {
	$currency = "";
}

// include the template for display
if ($userbalance === FALSE) return null;
else require JModuleHelper::getLayoutPath('mod_opensim_balance', $layout);

?>

<script>$('.disabled').click(function(e) {e.preventDefault();});</script>
