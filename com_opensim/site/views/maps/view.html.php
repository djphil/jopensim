<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2015 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class opensimViewmaps extends JViewLegacy {

	public function display($tpl = null) {
		$this->mparams	= &JComponentHelper::getParams('com_menus');

		$app	= JFactory::getApplication(); // Access the Application Object
		$menu	= $app->getMenu(); // Load the JMenuSite Object
		$active	= $menu->getActive(); // Load the Active Menu Item as an stdClass Object

		$showpageheading		= $active->params->get('show_page_heading');
		$pageheading			= $active->params->get('page_heading');
		$this->pageclass_sfx	= $active->params->get('pageclass_sfx');

		$this->pageheading		= "";

		if($showpageheading == "1") {
			$this->showpageheading	= TRUE;
			$this->pageheading		= ($pageheading) ? $pageheading:$this->mparams->get('page_heading');
		} elseif($showpageheading == "0") {
			$this->showpageheading	= FALSE;
		} else {
			$this->globalheading = $this->mparams->get('show_page_heading');
			if($this->globalheading == 1) {
				$this->showpageheading	= TRUE;
				$this->pageheading		= ($pageheading) ? $pageheading:$this->mparams->get('page_heading');
			} else {
				$this->showpageheading	= FALSE;
			}
		}

		$model = $this->getModel('maps');
		$asseturl = JUri::base(true)."/components/com_opensim/assets/";
		$this->settingsdata = $model->getSettingsData();
//		if(!$this->settingsdata['jopensim_maps_water']) $this->settingsdata['jopensim_maps_water'] = $asseturl."water.jpg";
		if($this->settingsdata['jopensim_maps_displaytype'] == "fill") {
			$this->backgroundsize	= "100% 100%";
		} else {
			$this->backgroundsize	= $this->settingsdata['jopensim_maps_displaytype'];
		}

		if($this->settingsdata['jopensim_maps_displayrepeat'] == 1) {
			$this->backgroundrepeat = "repeat";
		} else {
			$this->backgroundrepeat = "no-repeat";
		}


		$usecustomcenter = $active->params->get('jopensim_usecustomcenter',0);
		if($usecustomcenter == 1) {
			$this->jmapX		= $active->params->get('jopensim_maps_homex',1000);
			$this->jmapY		= $active->params->get('jopensim_maps_homey',1000);
			$this->jmapXoffset	= $active->params->get('jopensim_maps_offsetx',0);
			$this->jmapYoffset	= $active->params->get('jopensim_maps_offsety',0);
		} else {
			$this->jmapX		= $this->settingsdata['jopensim_maps_homex'];
			$this->jmapY		= $this->settingsdata['jopensim_maps_homey'];
			$this->jmapXoffset	= $this->settingsdata['jopensim_maps_offsetx'];
			$this->jmapYoffset	= $this->settingsdata['jopensim_maps_offsety'];
		}

		$doc = JFactory::getDocument();
		$doc->addStyleSheet($asseturl.'opensim.css');
		$doc->addScript("http://maps.google.com/maps/api/js?v=3&key=AIzaSyBACCLjQjfliUdoyI90ZS5HNf7M22TYORI");
		$doc->addScript($asseturl."infobubble-min.js");
//		$doc->addScript("http://google-maps-utility-library-v3.googlecode.com/svn/trunk/infobubble/src/infobubble.js");
		$doc->addScript($asseturl."opensimmaps.js");
		$doc->addScript($asseturl."copyright.js");

		$tpl = "mapview";

		parent::display($tpl);
	}
}
?>