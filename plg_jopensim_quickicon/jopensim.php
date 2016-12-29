<?php
/**
 * jOpenSim QuickIcon Plugin
 * @package jOpenSim.Plugins
 * @subpackage QuickIcon
 *
 * @copyright (C) 2016 FoTO50. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.jopensim.com
 **/
defined ( '_JEXEC' ) or die ();


class plgQuickiconJopensim extends JPlugin {

	public function __construct(& $subject, $config) {
		if (!is_dir(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_opensim")) return false;

		$this->loadLanguage('plg_quickicon_jopensim.sys', JPATH_ADMINISTRATOR);

		$doc = JFactory::getDocument();
		$doc->addStyleSheet('components/com_opensim/assets/opensim.css');

		parent::__construct ( $subject, $config );
	}

	/**
	 * Display jOpenSim backend icon in Joomla 2.5+
	 *
	 * @param string $context
	 */
	public function onGetIcons($context) {
		if (!$context == 'mod_quickicon' || !JFactory::getUser()->authorise('core.manage', 'com_opensim')) {
			return;
		}

		if (!is_dir(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_opensim")) return;

		$document	= JFactory::getDocument();
//		$style		= "#com_opensim_icon a { color:#f00; }\n";
		$style		= "li#com_opensim_icon a { background: url('../plugins/quickicon/jopensim/assets/jopensimquickicon.png') no-repeat 0px 0px; }\n";
		$style	   .= "li#com_opensim_icon a:hover { background: url('../plugins/quickicon/jopensim/assets/jopensimquickicon.png') no-repeat 0px -36px #eee; }\n";
		$document->addStyleDeclaration($style);

		if(!(class_exists('opensim'))) {
			require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_opensim'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'opensim.class.php');
		}
		$cparams	= JComponentHelper::getParams('com_opensim');
		$os_host	= $cparams->get('opensimgrid_dbhost', $cparams->get('opensim_dbhost', null));
		$os_db		= $cparams->get('opensimgrid_dbname', $cparams->get('opensim_dbname', null));
		$os_user	= $cparams->get('opensimgrid_dbuser', $cparams->get('opensim_dbuser', null));
		$os_pwd		= $cparams->get('opensimgrid_dbpasswd', $cparams->get('opensim_dbpasswd', null));
		$os_port	= $cparams->get('opensimgrid_dbport', $cparams->get('opensim_dbport', null));
		$opensim	= new opensim($os_host,$os_user,$os_pwd,$os_db,$os_port);
		$regions	= $opensim->countRegions();
		$users		= $opensim->countPresence();
//		$test = JFactory::getUser()->authorise('core.manage', 'com_opensim');
		$link = 'index.php?option=com_opensim';
		$img = 'jopensim_quickicon';
		$text = JText::_('COM_JOPENSIM');

		return array( array(
			'link'		=> JRoute::_($link),
			'image'		=> $img,
			'text'		=> $text,
			'access'	=> array('core.manage', 'com_opensim'),
			'class'		=> 'jopensimquickicon',
			'group'		=> 'PLG_QUICKICON_JOPENSIM',
			'id'		=> 'com_opensim_icon' ) );
	}
}