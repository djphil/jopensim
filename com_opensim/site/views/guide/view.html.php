<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2017 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');


class opensimViewguide extends JViewLegacy {

	public function display($tpl = null) {
		$params					= JComponentHelper::getParams('com_opensim');
		$this->host				= $params->get('opensim_host');
		$this->port				= $params->get('robust_port');
		$this->showclassified	= $params->get('classified_guide');
		$mapmodel				= $this->getModel('maps');
		$regionmodel			= $this->getModel('regions');
		$showcasemodel			= $this->getModel('showcase');
		$this->assetpath		= JUri::base(true)."/components/com_opensim/assets/";
		$doc = JFactory::getDocument();
		$doc->addStyleSheet($this->assetpath.'opensim.css');
		$cacheurl				= JPATH_SITE.DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."jopensim".DIRECTORY_SEPARATOR."regions".DIRECTORY_SEPARATOR;

		$this->tmpl				= JFactory::getApplication()->input->get('tmpl');

		$this->regions			= array();
		$allregions				= $regionmodel->getRegions();
		$regionimage			= "<img class='img-thumbnail' src='%1\$s%2\$s' width='%4\$d' height='%4\$d' alt='%3\$s' title='%3\$s' />";
		$cachepath				= JPATH_SITE.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'jopensim'.DIRECTORY_SEPARATOR.'regions'.DIRECTORY_SEPARATOR;
		$cacheurl				= JURI::root()."images/jopensim/regions/";

		$assetinfo				= pathinfo(JPATH_COMPONENT_SITE);
		$assetpath				= "components/".$assetinfo['basename']."/assets/";
		$asseturl				= "components/".$assetinfo['basename']."/assets/regionimage.php?uuid=";

		foreach($allregions AS $region) {
			$regioninfo = $mapmodel->getMapInfo($region['uuid']);
			if($regioninfo['guide'] == 1) {
				$mapcounter	= count($this->regions);
				$this->regions[$mapcounter] = $region;
				if(is_file($cachepath.$region['uuid'].".jpg")) {
					$mapimage = sprintf($regionimage,$cacheurl.$region['uuid'].".jpg","",$region['regionName'],150);
				} else {
					$params = sprintf("%1\$s&mapserver=%2\$s&mapport=%3\$s&scale=128",str_replace("-","",$region['uuid']),$region['serverIP'],$region['serverHttpPort']);
					$mapimage = sprintf($regionimage,$asseturl,$params,$region['regionName'],150);
				}
				$this->regions[$mapcounter]['mapimage'] = $mapimage;
			}
		}
		if($this->showclassified) {
			$this->classifieds	= $showcasemodel->getClassifieds();
			if(is_array($this->classifieds) && count($this->classifieds) > 0) {
				$assetimage		= "<img class='img-thumbnail' src='%1\$s' width='%3\$d' height='%3\$d' alt='%2\$s' title='%2\$s' />";
				foreach($this->classifieds AS $key => $classified) {
					if(!$classified['linklocal']) { // this location is currently not available, dont show it at all
						unset($this->classifieds[$key]);
						continue;
					}
					$this->classifieds[$key]['image'] = sprintf($assetimage,$classified['imageurl'],$this->classifieds[$key]['name'],150);
				}
			}
		}

		parent::display($tpl);
	}
}
?>