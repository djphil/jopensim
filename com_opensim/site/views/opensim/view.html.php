<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

$_REQUEST['tmpl'] = "component";

class opensimViewopensim extends JViewLegacy {

	public function display($tpl = null) {
		if(!array_key_exists("HTTP_USER_AGENT",$_SERVER)) $_SERVER['HTTP_USER_AGENT'] = "opensimviewer"; // To avoid notices

		$model				= $this->getModel('opensim');
		$this->gridstatus	= $model->getGridStatus();
		$this->assetpath	= JUri::base(true)."/components/com_opensim/assets/";
		$doc				= JFactory::getDocument();
		$doc->addStyleSheet($this->assetpath.'opensim.css');
		$doc->addStyleSheet($this->assetpath.'opensim.override.css');
		$regions			= $model->getData();
		$this->settingsdata	= $model->getSettingsData();

		if($this->settingsdata['loginscreen_layout'] == "classic") {

			if(intval($this->settingsdata['hiddenregions']) == 0) {
				$regionarray	= $model->removehidden($regions['regions']);
			} else {
				$regionarray	= $regions['regions'];
			}

			$this->regions		= $regionarray;
			$this->totalusers	= $model->opensim->countActiveUsers();

		} else {
			$this->loginscreenpositions = $model->getLoginscreenPositions();
			if($this->settingsdata['jopensim_loginscreen_customcss']) {
				$document = JFactory::getDocument();
				$document->addStyleDeclaration($this->settingsdata['jopensim_loginscreen_customcss']);
			}
			
			$tpl = "custom";
		}

		parent::display($tpl);
	}
}
?>