<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2018 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class opensimViewmaps extends JViewLegacy {

	public function display($tpl = null) {
		$this->mparams	= JComponentHelper::getParams('com_menus');

		$app	= JFactory::getApplication(); // Access the Application Object
		$menu	= $app->getMenu(); // Load the JMenuSite Object
		$active	= $menu->getActive(); // Load the Active Menu Item as an stdClass Object

		$showpageheading		= $active->params->get('show_page_heading');
		$pageheading			= $active->params->get('page_heading');
		$this->pageclass_sfx	= $active->params->get('pageclass_sfx');

		$this->pageheading		= "";
		$layout					= $this->getLayout();

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
		$regionmodel	= $this->getModel('regions',true);
		$eventmodel		= $this->getModel('events',true);
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


		$usecustomcenter	= $active->params->get('jopensim_usecustomcenter',0); // "old" Google layout
		$usecustomsettings	= $active->params->get('jopensim_usecustomsetting',0); // Leaflet layout
		if($usecustomcenter == 1 || $usecustomsettings == 1) {
			$this->jmapX		= $active->params->get('jopensim_maps_homex',1000);
			$this->jmapY		= $active->params->get('jopensim_maps_homey',1000);
			$this->jmapXoffset	= $active->params->get('jopensim_maps_offsetx',0);
			$this->jmapYoffset	= $active->params->get('jopensim_maps_offsety',0);
			$this->zoomStart	= $active->params->get('jopensim_maps_zoomstart',8);
		} else {
			$this->jmapX		= $this->settingsdata['jopensim_maps_homex'];
			$this->jmapY		= $this->settingsdata['jopensim_maps_homey'];
			$this->jmapXoffset	= $this->settingsdata['jopensim_maps_offsetx'];
			$this->jmapYoffset	= $this->settingsdata['jopensim_maps_offsety'];
			$this->zoomStart	= $this->settingsdata['jopensim_maps_zoomstart'];
		}
		$this->mousezoom		= $active->params->get('jopensim_mousezoom',1);
		$this->usev2maptiles	= $this->settingsdata['jopensim_maps_varregions'];
		$this->host				= $this->settingsdata['opensim_host'];
		$this->port				= $this->settingsdata['robust_port'];

		$doc = JFactory::getDocument();
		$doc->addStyleSheet($asseturl.'opensim.css');
		switch($layout) {
			case "leaflet":           
				$this->regions	= $regionmodel->regions;
				$this->regioncoords = array();
				foreach($this->regions AS $key => $region) {
					$regioninfo	= $model->getMapInfo($region['uuid']);
					if($regioninfo['hidemap'] == 1) {
						unset($this->regions[$key]);
						continue;
					}
					if($region['sizeX'] > 256) {
						$sizeMultiplyer = $region['sizeX'] / 256;
						for($x = 0; $x < $sizeMultiplyer; $x++) {
							for($y = 0; $y < $sizeMultiplyer; $y++) {
								$this->regioncoords[] = (($region['locY'] / 256)+$y)."-".(($region['locX'] / 256)+$x);
							}
						}
					} else {
						$this->regioncoords[] = ($region['locX'] / 256)."-".($region['locY'] / 256);
					}
				}
				$this->markerClassified			= $active->params->get('jopensim_marker_classifieds',0);
				$this->iconClassifiedDefault	= $active->params->get('jopensim_marker_classifieds_icon',$asseturl."images/marker_default_jopensim.png");
				$this->iconClassifiedShopping	= JUri::base(true).'/'.$active->params->get('jopensim_marker_classifieds_icon_shopping',$this->iconClassifiedDefault);
				$this->iconClassifiedLandrental	= JUri::base(true).'/'.$active->params->get('jopensim_marker_classifieds_icon_landrental',$this->iconClassifiedDefault);
				$this->iconClassifiedPropRental	= JUri::base(true).'/'.$active->params->get('jopensim_marker_classifieds_icon_propertyrental',$this->iconClassifiedDefault);
				$this->iconClassifiedAttraction	= JUri::base(true).'/'.$active->params->get('jopensim_marker_classifieds_icon_attraction',$this->iconClassifiedDefault);
				$this->iconClassifiedProducts	= JUri::base(true).'/'.$active->params->get('jopensim_marker_classifieds_icon_newproducts',$this->iconClassifiedDefault);
				$this->iconClassifiedEmployment	= JUri::base(true).'/'.$active->params->get('jopensim_marker_classifieds_icon_employment',$this->iconClassifiedDefault);
				$this->iconClassifiedWanted		= JUri::base(true).'/'.$active->params->get('jopensim_marker_classifieds_icon_wanted',$this->iconClassifiedDefault);
				$this->iconClassifiedService	= JUri::base(true).'/'.$active->params->get('jopensim_marker_classifieds_icon_service',$this->iconClassifiedDefault);
				$this->iconClassifiedPersonal	= JUri::base(true).'/'.$active->params->get('jopensim_marker_classifieds_icon_personal',$this->iconClassifiedDefault);

				$this->classifieds = array();
				if($this->markerClassified) {
					$showcasemodel		= $this->getModel('showcase',true);
					$classifiedMature	= $active->params->get('jopensim_marker_classifieds_mature',0);
					$this->classifieds	= $showcasemodel->getClassifieds(null,$classifiedMature);
					if(is_array($this->classifieds) && count($this->classifieds) > 0) {
						// add region coords for map to array to see if marker should be set
						foreach($this->classifieds AS $key => $classified) {
							$markerX = $classified['globalpos']['posX'] / 256;
							$markerY = $classified['globalpos']['posY'] / 256;
							$regioncoordX = intval($markerX);
							$regioncoordY = intval($markerY);
							if (in_array($regioncoordX."-".$regioncoordY, $this->regioncoords)) {
								$this->classifieds[$key]['regioncoords']	= $regioncoordX."-".$regioncoordY;
								$this->classifieds[$key]['markerX']			= $markerX;
								$this->classifieds[$key]['markerY']			= $markerY;
							} else {
								unset($this->classifieds[$key]); // the region for this classified is (at the moment?) not present
							}
						}
					}
					if(count($this->classifieds) == 0) $this->markerClassified = 0; // disable classified markers in case nothing left after cleaning not present regions
				}

				$this->markerEvents		= $active->params->get('jopensim_marker_events',0);
				$this->iconEvents		= JUri::base(true).'/'.$active->params->get('jopensim_marker_events_icon','images/jopensim/marker/default_jopensim.png');

				$this->eventlist		= $eventmodel->getEventList();
				$this->events			= array();
				if(is_array($this->eventlist['events']) && count($this->eventlist['events']) > 0) {
					foreach($this->eventlist['events'] AS $event) {
						$globalPos		= explode(",",substr($event['globalPos'],1,strlen($event['globalPos'])-2));
						$markerX		= $globalPos[0] / 256;
						$markerY		= $globalPos[1] / 256;
						$regioncoordX	= intval($markerX);
						$regioncoordY	= intval($markerY);
						if (in_array($regioncoordX."-".$regioncoordY, $this->regioncoords)) {
							$key = count($this->events);
							$this->events[$key] = $event;
							$this->events[$key]['regioncoords']	= $regioncoordX."-".$regioncoordY;
							$this->events[$key]['markerX']			= $markerX;
							$this->events[$key]['markerY']			= $markerY;
						}
					}
					if(count($this->events) == 0) $this->markerEvents = 0; // disable event markers in case nothing left after cleaning not present regions
				} else {
					$this->markerEvents = 0; // currently no events at all? disable event markers
				}

				// need this for testing without the need to add events inworld every day ;)
//				$this->events[0]["name"]			= "TestEvent1";
//				$this->events[0]["regioncoords"]	= "1001-997";
//				$this->events[0]["markerX"]			= "1001.2";
//				$this->events[0]["markerY"]			= "997.2";
//
//				$this->events[1]["name"]			= "TestEvent2";
//				$this->events[1]["regioncoords"]	= "1001-997";
//				$this->events[1]["markerX"]			= "1001.7";
//				$this->events[1]["markerY"]			= "997.7";
//
//				$this->events[2]["name"]			= "TestEvent3";
//				$this->events[2]["regioncoords"]	= "1001-997";
//				$this->events[2]["markerX"]			= "1001.7";
//				$this->events[2]["markerY"]			= "997.2";

// wont be implemented, left here for planned custom markers ;)
//				$this->markerPicks		= $active->params->get('jopensim_marker_picks',0);
//				$this->iconPicks		= JUri::base(true).'/'.$active->params->get('jopensim_marker_picks_icon','images/jopensim/marker/default_jopensim.png');

				$this->jopensim_maps_showteleport	= $this->settingsdata['jopensim_maps_showteleport'];
				$this->jopensim_maps_showcoords		= $this->settingsdata['jopensim_maps_showcoords'];
				$doc->addStyleSheet($asseturl.'opensimleafletmap.css');
				$doc->addStyleSheet($asseturl.'leaflet.css');
				$doc->addScript($asseturl."leaflet.js");
				$doc->addStyleSheet($asseturl.'MarkerCluster.css');
				$doc->addStyleSheet($asseturl.'MarkerCluster.Default.css');
				$doc->addScript($asseturl."leaflet.markercluster-src.js");
				$doc->addStyleSheet($asseturl.'easy-button.css');
				$doc->addScript($asseturl."easy-button.js");
				$doc->addScript($asseturl."jopensimleaflet.js");
				$doc->addScript($asseturl."leaflet.featuregroup.subgroup.js");
				$doc->addStyleSheet($asseturl.'leaflet-search.css');
				$doc->addScript($asseturl."leaflet-search.js");
				$this->layerBackground	= $asseturl."images/layers.png";
				$this->iconDummy		= $asseturl."images/markerdummy.png";
			break;
			default:
				$doc->addScript("https://maps.google.com/maps/api/js?v=3&key=AIzaSyBACCLjQjfliUdoyI90ZS5HNf7M22TYORI");

				$doc->addScriptDeclaration('InfoBubble.prototype.CLOSE_SRC_ = "'.JUri::base(true).'/components/com_opensim/assets/images/exit_small.png";');
				$doc->addScript($asseturl."infobubble.js");

				$doc->addScript($asseturl."opensimmaps.js");
				$doc->addScript($asseturl."copyright.js");
			break;
		}

		$tpl = "mapview";

		parent::display($tpl);
	}
}
?>