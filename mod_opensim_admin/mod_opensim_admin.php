<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2016 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

    require_once __DIR__ . '/helper.php';

	$language = JFactory::getLanguage();
	$language->load('mod_opensim_admin.ini', JPATH_ADMINISTRATOR);
	$language->load('com_opensim', JPATH_ADMINISTRATOR);

    $db     = JFactory::getDBO();
    $user   = JFactory::getUser();

	if(!(class_exists('opensim'))) {
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_opensim'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'opensim.class.php');
	}
	$cparams	= JComponentHelper::getParams('com_opensim');
	$os_host	= $cparams->get('opensimgrid_dbhost', null);
	$os_db		= $cparams->get('opensimgrid_dbname', null);
	$os_user	= $cparams->get('opensimgrid_dbuser', null);
	$os_pwd		= $cparams->get('opensimgrid_dbpasswd', null);
	$os_port	= $cparams->get('opensimgrid_dbport', null);

	$opensim			= new opensim($os_host,$os_user,$os_pwd,$os_db,$os_port);
	$opensim->connect2osgrid();

	if(!is_object($opensim->_osgrid_db)) {
		$layout = "noconnection";
	} else {
		$jopensimversion	= $opensim->getversion();
		$layout = "default";

		$document		= JFactory::getDocument();
		$document->addStyleSheet(JURI::base(true).'/components/com_opensim/assets/quickiconstyle.css?v='.$jopensimversion);

		if (!$user->authorise('core.manage', 'com_opensim')){
			return;
		}

		$regions				= $opensim->countRegions();
		$users					= $opensim->countPresence();

	    $modOpenSimAdminHelper	= new modOpenSimAdminHelper($opensim);

		$buttons				= $modOpenSimAdminHelper->getButtons();

	    $quicklinks				= $params->get('view_quicklinks', 1);
	    $recentonline			= $params->get('view_recentonline', 1);
	    $recentonlinelimit		= $params->get('view_recentonline_number', 5);
	    $recentregistered		= $params->get('view_recentregistered', 1);
	    $recentregisteredlimit	= $params->get('view_recentregistered_number', 5);
	    $topgroups				= $params->get('view_topgroups', 1);
	    $topgroupslimit			= $params->get('view_topgroups_number', 5);

	    if ($quicklinks) {
			$buttons				= $modOpenSimAdminHelper->getButtons();
	    }

	    if ($recentonline) {
		    $recentonline_items 	= $modOpenSimAdminHelper->getRecentOnline($recentonlinelimit);
	    }

	    if ($recentregistered) {
		    $recentregistered_items = $modOpenSimAdminHelper->getRecentRegistered($recentregisteredlimit);
	    }

	    if ($topgroups) {
		    $topgroup_items 		= $modOpenSimAdminHelper->getTopGroups($topgroupslimit);
	    }

	    if (!$quicklinks && !$recentonline && !$recentregistered && !$topgroups) {
	        return;
	    }
	}

require JModuleHelper::getLayoutPath('mod_opensim_admin', $params->get('layout', $layout));
