<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');
// JLoader::register('jOpenSimHelper', JPATH_COMPONENT.'/helpers/jopensimhelper.php');

class opensimViewjopensimimage extends JViewLegacy {
	public function display($tpl = null) {
		JFactory::getApplication()->getMessageQueue(TRUE);
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_opensim'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'opensim.class.php');

		$cparams	= JComponentHelper::getParams('com_opensim');
		$os_host	= $cparams->get('opensimgrid_dbhost', $cparams->get('opensim_dbhost', null));
		$os_db		= $cparams->get('opensimgrid_dbname', $cparams->get('opensim_dbname', null));
		$os_user	= $cparams->get('opensimgrid_dbuser', $cparams->get('opensim_dbuser', null));
		$os_pwd		= $cparams->get('opensimgrid_dbpasswd', $cparams->get('opensim_dbpasswd', null));
		$os_port	= $cparams->get('opensimgrid_dbport', $cparams->get('opensim_dbport', null));
		$opensim	= new opensim($os_host,$os_user,$os_pwd,$os_db,$os_port);

		$regions	= $opensim->countRegions();
		$users		= $opensim->countPresence();

		if($regions == 0) {
			$iconfile		= JPATH_ADMINISTRATOR."/components/com_opensim/assets/jopensim_quickicon_offline.png";
		} elseif($users == 0) {
			$iconfile		= JPATH_ADMINISTRATOR."/components/com_opensim/assets/jopensim_quickicon_nouser.png";
		} else {
			$iconfile		= JPATH_ADMINISTRATOR."/components/com_opensim/assets/jopensim_quickicon.png";
		}

		$img	= new JImage($iconfile);
		$icon	= $img->getHandle();
		$white	= imagecolorallocate($icon, 255, 255, 255);

		if($regions > 0) {
			$length = strlen($regions);
			if($length > 2) $font = 3;
			else $font = 5;
			$x = 10 - (4 * $length);
			imagestring($icon, $font, $x, 3, $regions, $white);
		}

		if($users > 0) {
			$length = strlen($users);
			if($length > 2) $font = 3;
			else $font = 5;
			$x = 37 - (4 * $length);
			imagestring($icon, $font, $x, 31, $users, $white);
		}

		header("Content-Type: image/png");
		imagepng($icon);
		imagedestroy($icon);
		
		exit;
//		parent::display($tpl);
	}
}
?>