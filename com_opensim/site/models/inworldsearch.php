<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2015 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die();

require_once(JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'opensim.php');

class opensimModelinworldsearch extends OpenSimModelOpenSim {

	var $_settingsData;
	var $filename = "inworldsearch.php";
	var $view = "inworld";
	var $_os_db = null;
	var $_osgrid_db = null;

	public function __construct() {
		parent::__construct();
		$this->getSettingsData();
		$this->getOpenSimDB();
		$this->_os_db =& $this->getOpenSimDB();
		$this->_osgrid_db =& $this->getOpenSimGridDB();
	}

	public function searchAll($searchterm) {
		$retval = array();
		if($this->_settingsData['search_objects'] == 1)		$retval['JOPENSIM_SEARCH_OBJECTS']		= $this->searchobjects($searchterm);
		if($this->_settingsData['search_parcels'] == 1)		$retval['JOPENSIM_SEARCH_PARCELS']		= $this->searchparcels($searchterm);
		if($this->_settingsData['search_parcelsales'] == 1)	$retval['JOPENSIM_SEARCH_PARCELSALES']	= $this->searchparcelsale($searchterm);
//		if($this->_settingsData['search_events'] == 1)		$retval['JOPENSIM_SEARCH_EVENTS']		= $this->searchevents($searchterm);
//		if($this->_settingsData['search_classified'] == 1)	$retval['JOPENSIM_SEARCH_CLASSIFIEDS']	= $this->searchclassifieds($searchterm);
		if($this->_settingsData['search_regions'] == 1)		$retval['JOPENSIM_SEARCH_REGIONS']		= $this->searchregions($searchterm);

		if(count($retval) > 0) return $retval;
		else return null;
	}

	public function searchobjects($searchterm) {
		$db =& JFactory::getDBO();
		$unknown = JText::_('JOPENSIM_SEARCH_UNKNOWN');
		$query = sprintf("SELECT
							#__opensim_search_objects.*,
							IFNULL(#__opensim_search_regions.regionname,'%2\$s') AS region
						FROM
							#__opensim_search_objects LEFT JOIN #__opensim_search_regions USING(regionuuid)
						WHERE
							#__opensim_search_objects.name LIKE '%%%1\$s%%'
						OR
							#__opensim_search_objects.description LIKE '%%%1\$s%%'
						ORDER BY
							#__opensim_search_objects.name",$searchterm,$unknown);
		$db->setQuery($query);
		$searchresults = $db->loadAssocList();
		if(is_array($searchresults) && count($searchresults) > 0) {
			foreach($searchresults AS $key => $searchresult) {
				$location = explode("/",$searchresult['location']);
				$searchresults[$key]['surl']		= $searchresult['region']."/".intval($location[0])."/".intval($location[1])."/".intval($location[2]);
				$searchresults[$key]['regionname']	= $searchresult['region'];
			}
			return $searchresults;
		} else {
			return array();
		}
	}

	public function searchparcels($searchterm) {
		$db =& JFactory::getDBO();
		$unknown = JText::_('JOPENSIM_SEARCH_UNKNOWN');
		$query = sprintf("SELECT
							#__opensim_search_parcels.*,
							#__opensim_search_parcels.parcelname AS name,
							IFNULL(#__opensim_search_regions.regionname,'%2\$s') AS region
						FROM
							#__opensim_search_parcels LEFT JOIN #__opensim_search_regions ON #__opensim_search_parcels.regionUUID = #__opensim_search_regions.regionuuid
						WHERE
							#__opensim_search_parcels.parcelname LIKE '%%%1\$s%%'
						OR
							#__opensim_search_parcels.description LIKE '%%%1\$s%%'
						ORDER BY
							#__opensim_search_parcels.parcelname",$searchterm,$unknown);
		$db->setQuery($query);
		$searchresults = $db->loadAssocList();
		if(is_array($searchresults) && count($searchresults) > 0) {
			foreach($searchresults AS $key => $searchresult) {
				$location = explode("/",$searchresult['landingpoint']);
				$searchresults[$key]['surl']		= $searchresult['region']."/".intval($location[0])."/".intval($location[1])."/".intval($location[2]);
				$searchresults[$key]['regionname']	= $searchresult['region'];
			}
			return $searchresults;
		} else {
			return array();
		}
	}

	public function searchparcelsale($searchterm) {
		$db =& JFactory::getDBO();
		$unknown = JText::_('JOPENSIM_SEARCH_UNKNOWN');
		$query = sprintf("SELECT
							#__opensim_search_parcelsales.*,
							#__opensim_search_parcelsales.parcelname AS name,
							IFNULL(#__opensim_search_regions.regionname,'%2\$s') AS region
						FROM
							#__opensim_search_parcelsales LEFT JOIN #__opensim_search_regions ON #__opensim_search_parcelsales.regionUUID = #__opensim_search_regions.regionuuid
						WHERE
							#__opensim_search_parcelsales.parcelname LIKE '%%%1\$s%%'
						ORDER BY
							#__opensim_search_parcelsales.parcelname",$searchterm,$unknown);
		$db->setQuery($query);
		$searchresults = $db->loadAssocList();
		if(is_array($searchresults) && count($searchresults) > 0) {
			foreach($searchresults AS $key => $searchresult) {
				$location = explode("/",$searchresult['landingpoint']);
				$searchresults[$key]['surl']		= $searchresult['region']."/".intval($location[0])."/".intval($location[1])."/".intval($location[2]);
				$searchresults[$key]['regionname']	= $searchresult['region'];
			}
			return $searchresults;
		} else {
			return array();
		}
	}

	// Currently not implemented yet but prepared for soon ;)
	public function searchevents($searchterm) {
		return array();
	}

	// Currently not implemented yet but prepared for soon ;)
	public function searchclassifieds($searchterm) {
		return array();
	}

	public function searchregions($searchterm) {
		$db =& JFactory::getDBO();
		$query = sprintf("SELECT
							#__opensim_search_regions.*,
							#__opensim_search_regions.regionname AS name,
							CONCAT(#__opensim_search_regions.regionname,'/') AS surl
						FROM
							#__opensim_search_regions
						WHERE
							#__opensim_search_regions.regionname LIKE '%%%1\$s%%'
						ORDER BY
							 #__opensim_search_regions.regionname",$searchterm);
		$db->setQuery($query);
		$searchresults = $db->loadAssocList();
		if(is_array($searchresults) && count($searchresults) > 0) {
			return $searchresults;
		} else {
			return array();
		}
	}

	public function getResultlines($results) {
		if(is_array($results) && count($results) > 0) {
			$retval = array();
			foreach($results AS $option => $result) {
				switch($option) {
					case "JOPENSIM_SEARCH_OBJECTS":
						if(count($result) > 0) {
							$retval['JOPENSIM_SEARCH_OBJECTS'][]			= sprintf("<th>%s</th><th>%s</th>",JText::_('JOPENSIM_SEARCHTITLE_OBJECT'),JText::_('JOPENSIM_SEARCHTITLE_REGION'));
							foreach($result AS $line) {
								$retval['JOPENSIM_SEARCH_OBJECTS'][]		= sprintf("<td><a href='secondlife:/"."/%s'>%s</a></td><td>%s</td>",$line['surl'],$line['name'],$line['region']);
							}
						} else {
							$retval['JOPENSIM_SEARCH_OBJECTS'][]            = JText::_('JOPENSIM_SEARCH_NOTHINGFOUND');
						}
					break;
					case "JOPENSIM_SEARCH_PARCELS":
						if(count($result) > 0) {
							$retval['JOPENSIM_SEARCH_PARCELS'][]			= sprintf("<th>%s</th><th>%s</th>",JText::_('JOPENSIM_SEARCHTITLE_PARCEL'),JText::_('JOPENSIM_SEARCHTITLE_REGION'));
							foreach($result AS $line) {
								$retval['JOPENSIM_SEARCH_PARCELS'][]		= sprintf("<td><a href='secondlife:/"."/%s'>%s</a></td><td>%s</td>",$line['surl'],$line['name'],$line['region']);
							}
						} else {
							$retval['JOPENSIM_SEARCH_PARCELS'][]            = JText::_('JOPENSIM_SEARCH_NOTHINGFOUND');
						}
					break;
					case "JOPENSIM_SEARCH_PARCELSALES":
						if(count($result) > 0) {
							$retval['JOPENSIM_SEARCH_PARCELSALES'][]        = sprintf("<th>%s</th><th>%s</th><th>%s</th><th>%s</th>",JText::_('JOPENSIM_SEARCHTITLE_PARCEL'),JText::_('JOPENSIM_SEARCHTITLE_PARCELAREA'),JText::_('JOPENSIM_SEARCHTITLE_PARCELPRICE'),JText::_('JOPENSIM_SEARCHTITLE_REGION'));
							foreach($result AS $line) {
								$retval['JOPENSIM_SEARCH_PARCELSALES'][]    = sprintf("<td><a href='secondlife:/"."/%s'>%s</a></td><td>%s</td><td>%s</td><td>%s</td>",$line['surl'],$line['name'],$line['area'],$line['saleprice'],$line['region']);
							}
						} else {
							$retval['JOPENSIM_SEARCH_PARCELSALES'][]        = JText::_('JOPENSIM_SEARCH_NOTHINGFOUND');
						}
					break;
					case "JOPENSIM_SEARCH_POPULARPLACES":
						if(count($result) > 0) {
							foreach($result AS $line) {
								$retval['JOPENSIM_SEARCH_POPULARPLACES'][]	= sprintf("%s",$line['name']);
							}
						} else {
							$retval['JOPENSIM_SEARCH_POPULARPLACES'][]      = JText::_('JOPENSIM_SEARCH_NOTHINGFOUND');
						}
					break;
					case "JOPENSIM_SEARCH_REGIONS":
						if(count($result) > 0) {
							$retval['JOPENSIM_SEARCH_REGIONS'][]			= sprintf("<th>%s</th>",JText::_('JOPENSIM_SEARCHTITLE_REGION'));
							foreach($result AS $line) {
								$retval['JOPENSIM_SEARCH_REGIONS'][]		= sprintf("<td><a href='secondlife:/"."/%s'>%s</a></div>",$line['surl'],$line['name']);
							}
						} else {
							$retval['JOPENSIM_SEARCH_REGIONS'][]            = JText::_('JOPENSIM_SEARCH_NOTHINGFOUND');
						}
					break;
				}
			}
			return $retval;
		} else {
			return null;
		}	
	}
}
?>