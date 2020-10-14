<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die();
/*jimport('joomla.application.component.model');*/
require_once(JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'opensim.php');

class OpenSimModelSearch extends OpenSimModelOpenSim {
	var $_settingsData;
	var $filename = "search.php";
	var $view = "search";
	var $_os_db;

	public function __construct() {
		parent::__construct();
		$this->getSettingsData();
	}

	public function getoptions($reihe = "intern") {
		$db = JFactory::getDBO();
		$query = "SELECT #__opensim_search_options.* FROM #__opensim_search_options";
		if($reihe == "intern") $query .= " ORDER BY reiheintern";
		else $query .= " ORDER BY reihe";
		$db->setQuery($query);
		$options = $db->loadAssocList();
		if(is_array($options) && count($options) > 0) {
			$retval = array();
			foreach($options AS $key => $option) {
				$retval[$option['searchoption']] = $option;
				$retval[$option['searchoption']]['name'] = JText::_($option['searchoption']);
				switch($option['searchoption']) {
					case "JOPENSIM_SEARCH_OBJECTS":
						$searchsetting = "search_objects";
					break;
					case "JOPENSIM_SEARCH_PARCELS":
						$searchsetting = "search_parcels";
					break;
					case "JOPENSIM_SEARCH_PARCELSALES":
						$searchsetting = "search_parcelsales";
					break;
					case "JOPENSIM_SEARCH_POPULARPLACES":
						$searchsetting = "search_popular";
					break;
					case "JOPENSIM_SEARCHEVENTS":
						$searchsetting = "search_events";
					break;
					case "JOPENSIM_SEARCHCLASSIFIED":
						$searchsetting = "search_classified";
					break;
					case "JOPENSIM_SEARCH_REGIONS":
						$searchsetting = "search_regions";
					break;
					default:
						$searchsetting = "";
					break;
				}
				if($searchsetting) {
					if($this->_settingsData[$searchsetting] == 1) $retval[$option['searchoption']]['enabled'] = TRUE;
					else $retval[$option['searchoption']]['enabled'] = FALSE;
				} else {
					$retval[$option['searchoption']]['enabled'] = FALSE;
				}
			}
			return $retval;
		} else {
			return array();
		}
	}

	public function saveOptions($data) {
		$sort		= $data['sortsearchoptions'];
		$db = JFactory::getDBO();
		// First we disable all
		$query = "UPDATE #__opensim_search_options SET enabled = '0'";
		$db->setQuery($query);
		$db->execute();
		// Now enable all from the array
		if(count($sort) > 0) {
			foreach($sort AS $reihe => $option) {
				$query = sprintf("UPDATE #__opensim_search_options SET reihe = '%s' WHERE searchoption = '%s'",$reihe,$option);
				$db->setQuery($query);
				$db->execute();
			}
		}
	}

	public function rebuildAll() {
		$db = JFactory::getDBO();
		$query = "SELECT #__opensim_search_hostsregister.host, #__opensim_search_hostsregister.port FROM #__opensim_search_hostsregister";
		$db->setQuery($query);
		$registered = $db->loadAssocList();
		if(is_array($registered) && count($registered) > 0) {
			foreach($registered AS $simulator) $this->rebuildHost($simulator['host'],$simulator['port']);
		}
	}

	public function rebuildHost($host,$port) {
		$jopensim_expire = 0;
		$db = JFactory::getDBO();
		$next = time() + 600; // first set next check to 5 mins, so we don't get stuck
		$query = sprintf("UPDATE #__opensim_search_hostsregister SET lastcheck = '%d' WHERE host = '%s' AND port = '%d'",$next,$host,$port);
		$db->setQuery($query);
		$db->execute();

		$objDOM		= new DOMDocument();
		$objDOM->resolveExternals = false;
		$xmldata	= $this->getDataFromHost($host, $port, "?method=collector");
		if(!$xmldata) {
			return FALSE;
		} elseif($xmldata == "Please try your request again later") {
			JFactory::getApplication()->enqueueMessage($xmldata,"error");
			return FALSE;
		}

		$objDOM->loadXML($xmldata);

		//
		// Grabbing the expire to update
		//
		$regiondata = $objDOM->getElementsByTagName("regiondata")->item(0);
		$expire = $regiondata->getElementsByTagName("expire")->item(0)->nodeValue;
	
		//
		// Calculate new expire
		//
		if($expire < $jopensim_expire) $newexpire = $jopensim_expire;
		else $newexpire = $expire;
		$next = time() + $newexpire;
		$query = sprintf("UPDATE #__opensim_search_hostsregister SET lastcheck = '%d' WHERE host = '%s' AND port = '%d'",$next,$host,$port);
		$db->setQuery($query);
		$db->execute();
	
		$regionlist = $regiondata->getElementsByTagName("region");
		foreach($regionlist as $region) {
			// Start reading the Region info
			$info			= $region->getElementsByTagName("info")->item(0);
			$regionuuid		= $info->getElementsByTagName("uuid")->item(0)->nodeValue;
			$regionname		= $info->getElementsByTagName("name")->item(0)->nodeValue;
			$regionhandle	= $info->getElementsByTagName("handle")->item(0)->nodeValue;
			$url			= $info->getElementsByTagName("url")->item(0)->nodeValue;
	
			$query = sprintf("DELETE FROM #__opensim_search_regions WHERE regionuuid = '%s'",$regionuuid);
			$db->setQuery($query);
			$db->execute();
			$query = sprintf("DELETE FROM #__opensim_search_parcels WHERE regionUUID = '%s'",$regionuuid);
			$db->setQuery($query);
			$db->execute();
			$query = sprintf("DELETE FROM #__opensim_search_objects WHERE regionuuid = '%s'",$regionuuid);
			$db->setQuery($query);
			$db->execute();
			$query = sprintf("DELETE FROM #__opensim_search_allparcels WHERE regionUUID = '%s'",$regionuuid);
			$db->setQuery($query);
			$db->execute();
			$query = sprintf("DELETE FROM #__opensim_search_parcelsales WHERE regionUUID = '%s'",$regionuuid);
			$db->setQuery($query);
			$db->execute();
	
			$data		= $region->getElementsByTagName("data")->item(0);
			$estate		= $data->getElementsByTagName("estate")->item(0);
			$username	= $estate->getElementsByTagName("name")->item(0)->nodeValue;
			$useruuid	= $estate->getElementsByTagName("uuid")->item(0)->nodeValue;
			$regionData	= $this->opensim->getRegionData($regionuuid);
			if(is_array($regionData) && array_key_exists("locX",$regionData) && array_key_exists("locY",$regionData)) {
				$locX	= $regionData['locX'];
				$locY	= $regionData['locY'];
			} else {
				$regioninfo = $this->opensim->getRegionData($regionuuid);
				if(is_array($regioninfo)) {
					$locX	= array_key_exists("locX",$regioninfo) ? $regioninfo['locX']:0;
					$locY	= array_key_exists("locY",$regioninfo) ? $regioninfo['locY']:0;
				} else {
					$locX	= 0;
					$locY	= 0;
				}
			}
	
			// Now add the new info to the database
			$query = sprintf("INSERT INTO #__opensim_search_regions (regionname,regionuuid,regionhandle,url,owner,owneruuid,locX,locY) VALUES ('%s','%s','%s','%s','%s','%s','%d','%d')",
								$db->escape($regionname),
								$regionuuid,
								$regionhandle,
								$url,
								$db->escape($username),
								$useruuid,
								$locX,
								$locY);
			$db->setQuery($query);
			$db->execute();
	
			// Start reading the parcel info
			$parcel = $data->getElementsByTagName("parcel");
			foreach($parcel as $value) {
	
				$parcelname			= $value->getElementsByTagName("name")->item(0)->nodeValue;
				$parceluuid			= $value->getElementsByTagName("uuid")->item(0)->nodeValue;
				$infouuid			= $value->getElementsByTagName("infouuid")->item(0)->nodeValue;
				$parcellanding		= $value->getElementsByTagName("location")->item(0)->nodeValue;
				$parceldescription	= $value->getElementsByTagName("description")->item(0)->nodeValue;
				$parcelarea			= $value->getElementsByTagName("area")->item(0)->nodeValue;
				$parcelcategory		= $value->getAttributeNode("category")->nodeValue;
				$parcelsaleprice	= $value->getAttributeNode("salesprice")->nodeValue;
				$dwell				= $value->getElementsByTagName("dwell")->item(0)->nodeValue;
				$owner				= $value->getElementsByTagName("owner")->item(0);
				$ownername			= $owner->getElementsByTagName("name")->item(0)->nodeValue;
				$owneruuid			= $owner->getElementsByTagName("uuid")->item(0)->nodeValue;
	
				// Adding support for groups
				$group				= $value->getElementsByTagName("group")->item(0);
				if ($group != "") {
					$groupuuid	= $group->getElementsByTagName("groupuuid")->item(0)->nodeValue;
				} else {
					$groupuuid	= "00000000-0000-0000-0000-000000000000";
				}
	
				// Check bits on Public, Build, Script
				$parcelforsale		= $value->getAttributeNode("forsale")->nodeValue;
				$parceldirectory	= $value->getAttributeNode("showinsearch")->nodeValue;
				$parcelbuild		= $value->getAttributeNode("build")->nodeValue;
				$parcelscript		= $value->getAttributeNode("scripts")->nodeValue;
				$parcelpublic		= $value->getAttributeNode("public")->nodeValue;
	
				$debugoutput =	"parcelname:        ".$parcelname."\n".
						 		"parceluuid:        ".$parceluuid."\n".
								"infouuid:          ".$infouuid."\n".
								"parcellanding:     ".$parcellanding."\n".
								"parceldescription: ".$parceldescription."\n".
								"parcelarea:        ".$parcelarea."\n".
								"parcelcategory:    ".$parcelcategory."\n".
								"parcelsaleprice:   ".$parcelsaleprice."\n".
								"dwell:             ".$dwell."\n".
								"owneruuid:         ".$owneruuid."\n".
								"parcelforsale:     ".$parcelforsale."\n".
								"parceldirectory:   ".$parceldirectory."\n".
								"parceldirectory:   ".$parceldirectory."\n".
								"parcelbuild:       ".$parcelbuild."\n".
								"parcelpublic:      ".$parcelpublic."\n";
	
				$query = sprintf("INSERT INTO #__opensim_search_allparcels (
											regionUUID,
											regionName,
											parcelname,
											ownerUUID,
											ownerName,
											groupUUID,
											landingpoint,
											parcelUUID,
											infoUUID,
											parcelarea)
										VALUES
											('%s','%s','%s','%s','%s','%s','%s','%s','%s','%d')",
						$regionuuid,
						$db->escape($regionname),
						$db->escape($parcelname),
						$owneruuid,
						$db->escape($ownername),
						$groupuuid,
						$parcellanding,
						$parceluuid,
						$infouuid,
						$parcelarea);
				$db->setQuery($query);
				$db->execute();
	
				if ($parceldirectory == "true") {
					$query = sprintf("INSERT INTO #__opensim_search_parcels (regionUUID,parcelname,parcelUUID,landingpoint,description,searchcategory,`build`,`script`,`public`,dwell,infouuid) VALUES ('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')",
						$regionuuid,
						$db->escape($parcelname),
						$parceluuid,
						$parcellanding,
						$db->escape($parceldescription),
						$parcelcategory,
						$parcelbuild,
						$parcelscript,
						$parcelpublic,
						$dwell,
						$infouuid);
					$db->setQuery($query);
					$db->execute();
				}
				if ($parcelforsale == "true") {
					$query = sprintf("INSERT INTO #__opensim_search_parcelsales (regionUUID,parcelname,parcelUUID,area,saleprice,landingpoint,infoUUID,dwell,parentestate,mature) VALUES ('%s','%s','%s','%d','%d','%s','%s','%d','%d','%s')",
						$regionuuid,
						$db->escape($parcelname),
						$parceluuid,
						$parcelarea,
						$parcelsaleprice,
						$parcellanding,
						$infouuid,
						$dwell,
						"1",
						"false");
					$db->setQuery($query);
					$db->execute();
				}
			}
			// Handle objects
			$objects = $data->getElementsByTagName("object");
			foreach($objects as $value) {
				$uuid			= $value->getElementsByTagName("uuid")->item(0)->nodeValue;
				$regionuuid		= $value->getElementsByTagName("regionuuid")->item(0)->nodeValue;
				$parceluuid		= $value->getElementsByTagName("parceluuid")->item(0)->nodeValue;
				$title			= $value->getElementsByTagName("title")->item(0)->nodeValue;
				$description	= $value->getElementsByTagName("description")->item(0)->nodeValue;
				$flags			= $value->getElementsByTagName("flags")->item(0)->nodeValue;
				$location		= $value->getElementsByTagName("location")->item(0)->nodeValue;
	
				$query = sprintf("INSERT INTO #__opensim_search_objects (objectuuid,parceluuid,location,`name`,description,regionuuid) VALUES ('%s','%s','%s','%s','%s','%s')",
					$uuid,
					$parceluuid,
					$location,
					$db->escape($title),
					$db->escape($description),
					$regionuuid);
				$db->setQuery($query);
				$db->execute();
			}
		}



	}

	public function getDataFromHost($host,$port,$url) {
		$url = "http://$host:$port/$url";
	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	
		$data = curl_exec($ch);
		if (!curl_errno($ch)) {
			curl_close($ch);
			return $data;
		}
		return "";
	}


	public function getSearchdata($datatype) {
		$table = "";
		$order = "";
		$retval = array();

		switch($datatype) {
			case "objects":
				$table	= "#__opensim_search_objects";
			break;
			case "parcels":
				$table	= "#__opensim_search_parcels";
			break;
			case "parcelsales":
				$table	= "#__opensim_search_parcelsales";
			break;
			case "classifieds":
				$table	= "#__opensim_userclassifieds";
				$order	= "#__opensim_userclassifieds.creationdate DESC";
			break;
			case "events":
				$table	= "#__opensim_search_events";
				$order	= "#__opensim_search_events.dateUTC DESC";
			break;
			case "regions":
				$table	= "#__opensim_search_regions";
			break;
		}

		if($table) {
			$db = JFactory::getDBO();
			$query = "SELECT ".$table.".* FROM ".$table;
			if($order) $query .= " ORDER BY ".$order;
			$db->setQuery($query);
			$retval = $db->loadAssocList();
		}

		// Complete data from other search tables
		switch($datatype) {
			case "objects":
				$retval = $this->completeObjects($retval);
			break;
			case "parcels":
			case "parcelsales":
				$retval = $this->completeParcels($retval);
			break;
			case "classifieds":
				$retval = $this->completeClassifieds($retval);
			break;
			case "events":
				$retval = $this->completeEvents($retval);
			break;
		}

		return $retval;
	}

	public function completeObjects($objectdata) {
		if(is_array($objectdata) && count($objectdata) > 0) {
			foreach($objectdata AS $key => $val) {
				$placeNames = $this->getPlaceNames($val['parceluuid']);
				$objectdata[$key]['parcelName'] = (is_array($placeNames) && array_key_exists("parcelname",$placeNames)) ? $placeNames['parcelname']:JText::_('JOPENSIM_SEARCH_UNKNOWNPARCEL');
				$objectdata[$key]['regionName'] = (is_array($placeNames) && array_key_exists("regionName",$placeNames)) ? $placeNames['regionName']:JText::_('JOPENSIM_SEARCH_UNKNOWNREGION');
				if($objectdata[$key]['regionName'] && $objectdata[$key]['regionName'] != JText::_('JOPENSIM_SEARCH_UNKNOWNREGION')) {
					$location = $this->roundLocation($val['location']);
					$surl = $objectdata[$key]['regionName']."/".$location;
					$objectdata[$key]['surl'] = "<a href='secondlife://".$surl."'>".$surl."</a>";
				} else {
					$objectdata[$key]['surl'] = "";
				}
			}
		}
		return $objectdata;
	}

	public function completeParcels($parceldata) {
		if(is_array($parceldata) && count($parceldata) > 0) {
			foreach($parceldata AS $key => $val) {
				$placeNames = $this->getPlaceNames($val['parcelUUID']);
				$parceldata[$key]['regionName'] = (is_array($placeNames) && array_key_exists("regionName",$placeNames)) ? $placeNames['regionName']:JText::_('JOPENSIM_SEARCH_UNKNOWNREGION');
				if($parceldata[$key]['regionName'] && $parceldata[$key]['regionName'] != JText::_('JOPENSIM_SEARCH_UNKNOWNREGION')) {
					$location = $this->roundLocation($val['landingpoint']);
					$surl = $parceldata[$key]['regionName']."/".$location;
					$parceldata[$key]['surl'] = "<a href='secondlife://".$surl."'>".$surl."</a>";
				} else {
					$parceldata[$key]['surl'] = JText::_('JOPENSIM_SEARCH_LOCATION_UNAVAILABLE');
				}
			}
		}
		return $parceldata;
	}

	public function completeClassifieds($classifieds) {
		if(is_array($classifieds) && count($classifieds) > 0) {
			$nowUTC = date("U");
			foreach($classifieds AS $key => $val) {
				$classifieds[$key]['creatorName'] = $this->opensim->getUserName($val['creatoruuid'],"fullname");
				if($classifieds[$key]['expirationdate'] < $nowUTC) $classifieds[$key]['isexpired'] = TRUE;
				else $classifieds[$key]['isexpired'] = FALSE;
				$posglobal = str_replace("<","",$val['posglobal']);
				$posglobal = str_replace(">","",$posglobal);
				$locationglobal = explode(", ",$posglobal);
				if(is_array($locationglobal) && count($locationglobal) == 3) {
					$pos['posX'] = $locationglobal[0];
					$pos['posY'] = $locationglobal[1];
					$pos['posZ'] = $locationglobal[2];
					$localpos = $this->opensim->globalPosition2regionPosition($pos);
					if(!$localpos) {
						$classifieds[$key]['surl'] = JText::_('JOPENSIM_SEARCH_LOCATION_UNAVAILABLE');
					} else {
						unset($localpos['regionname']);
						$localstring = implode("/",$localpos);
						$classifieds[$key]['localstring'] = $localstring;
						$localstring = $this->roundLocation($localstring);
						$classifieds[$key]['localstring2'] = $localstring;
						$surl = $classifieds[$key]['simname']."/".$localstring;
						$classifieds[$key]['surl'] = "<a href='secondlife://".$surl."'>".$surl."</a>";
					}
				} else {
					$classifieds[$key]['surl'] = JText::_('JOPENSIM_SEARCH_LOCATION_UNAVAILABLE');
				}
			}
		}
		return $classifieds;
	}

	public function completeEvents($events) {
		if(is_array($events) && count($events) > 0) {
			foreach($events AS $key => $val) {
				$events[$key]['ownerName'] = $this->opensim->getUserName($val['owneruuid'],"fullname");
				$events[$key]['creatorName'] = $this->opensim->getUserName($val['creatoruuid'],"fullname");

				$posglobal = str_replace("<","",$val['globalPos']);
				$posglobal = str_replace(">","",$posglobal);
				$locationglobal = explode(",",$posglobal);
				if(is_array($locationglobal) && count($locationglobal) == 3) {
					$pos['posX'] = $locationglobal[0];
					$pos['posY'] = $locationglobal[1];
					$pos['posZ'] = $locationglobal[2];
					$localpos = $this->opensim->globalPosition2regionPosition($pos);
					if(!$localpos) {
						$events[$key]['surl'] = JText::_('JOPENSIM_SEARCH_LOCATION_UNAVAILABLE');
					} else {
						unset($localpos['regionname']);
						$localstring = implode("/",$localpos);
						$events[$key]['localstring'] = $localstring;
						$localstring = $this->roundLocation($localstring);
						$events[$key]['localstring2'] = $localstring;
						$surl = $events[$key]['simname']."/".$localstring;
						$events[$key]['surl'] = "<a href='secondlife://".$surl."'>".$surl."</a>";
					}
				} else {
					$events[$key]['surl'] = JText::_('JOPENSIM_SEARCH_LOCATION_UNAVAILABLE');
				}
				if($val['parcelUUID']) {
					$placeNames = $this->getPlaceNames($val['parcelUUID']);
					$objectdata[$key]['parcelName'] = (is_array($placeNames) && array_key_exists("parcelname",$placeNames)) ? $placeNames['parcelname']:JText::_('JOPENSIM_SEARCH_UNKNOWNPARCEL');
				} else {
					$objectdata[$key]['parcelName'] = JText::_('JOPENSIM_SEARCH_UNKNOWNPARCEL');
				}
			}
		}
		return $events;
	}

	public function roundLocation($location) {
		$retval = "";
		$locarray = explode("/",$location);
		if(count($locarray) == 3) {
			$locX	= round($locarray[0]);
			$locY	= round($locarray[1]);
			$locZ	= round($locarray[2]);
			$retval	= $locX."/".$locY."/".$locZ;
		}
		return $retval;
	}

	public function getPlaceNames($parcelUUID) {
		$db = JFactory::getDBO();
		$query = "SELECT #__opensim_search_allparcels.parcelname, #__opensim_search_allparcels.regionName FROM #__opensim_search_allparcels WHERE #__opensim_search_allparcels.parcelUUID = '".$db->escape($parcelUUID)."'";
		$db->setQuery($query);
		$retval = $db->loadAssocList();
		if(is_array($retval) && count($retval) > 0) {
			return $retval[0];
		} else {
			return FALSE;
		}
	}

	public function countSearchContent() {
		$retval = array();
		$db = JFactory::getDBO();
		$query = "SELECT #__opensim_search_objects.* FROM #__opensim_search_objects";
		$db->setQuery($query);
		$db->execute();
		$retval['objects'] = $db->getNumRows();
		$query = "SELECT #__opensim_search_parcels.* FROM #__opensim_search_parcels";
		$db->setQuery($query);
		$db->execute();
		$retval['parcels'] = $db->getNumRows();
		$query = "SELECT #__opensim_search_parcelsales.* FROM #__opensim_search_parcelsales";
		$db->setQuery($query);
		$db->execute();
		$retval['parcelsales'] = $db->getNumRows();
		$query = "SELECT #__opensim_search_events.* FROM #__opensim_search_events";
		$db->setQuery($query);
		$db->execute();
		$retval['events'] = $db->getNumRows();
		$nowUTC = date("U");
		$query = sprintf("SELECT #__opensim_search_events.* FROM #__opensim_search_events WHERE #__opensim_search_events.dateUTC < '%d'",$nowUTC);
		$db->setQuery($query);
		$db->execute();
		$retval['events_old'] = $db->getNumRows();
		$query = "SELECT #__opensim_userclassifieds.* FROM #__opensim_userclassifieds";
		$db->setQuery($query);
		$db->execute();
		$retval['classifieds'] = $db->getNumRows();
		$query = sprintf("SELECT #__opensim_userclassifieds.* FROM #__opensim_userclassifieds WHERE #__opensim_userclassifieds.expirationdate < '%d'",$nowUTC);
		$db->setQuery($query);
		$db->execute();
		$retval['classifieds_expired'] = $db->getNumRows();
		$query = "SELECT #__opensim_search_regions.* FROM #__opensim_search_regions";
		$db->setQuery($query);
		$db->execute();
		$retval['regions'] = $db->getNumRows();
		return $retval;
	}

	public function getRegisteredHosts() {
		$db = JFactory::getDBO();
		$query = "SELECT #__opensim_search_hostsregister.* FROM #__opensim_search_hostsregister ORDER BY #__opensim_search_hostsregister.register DESC";
		$db->setQuery($query);
		$hosts = $db->loadAssocList();
		return $hosts;
	}

	public function removehost($data) {
		if(array_key_exists("host",$data) && array_key_exists("port",$data)) {
			$db = JFactory::getDBO();
			$query = sprintf("DELETE FROM #__opensim_search_hostsregister WHERE #__opensim_search_hostsregister.host = '%s' AND #__opensim_search_hostsregister.port = '%d'",$data['host'],$data['port']);
			$db->setQuery($query);
			$db->execute();
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function purgedata($section) {
		$db = JFactory::getDBO();
		$table	= "";
		$where	= "";
		$tables	= array();
		switch($section) {
			case "objects":
				$table = "#__opensim_search_objects";
			break;
			case "parcels":
				$tables[] = "#__opensim_search_parcels";
				$tables[] = "#__opensim_search_allparcels";
			break;
			case "parcelsales":
				$table = "#__opensim_search_parcelsales";
			break;
			case "regions":
				$table = "#__opensim_search_regions";
			break;
		}
		if($table) {
			$query = sprintf("TRUNCATE TABLE %s",$db->escape($table));
			$db->setQuery($query);
			$db->execute();
			return TRUE;
		}
		if(count($tables) > 0) {
			foreach($tables AS $table) {
				$query = sprintf("TRUNCATE TABLE %s",$db->escape($table));
				$db->setQuery($query);
				$db->execute();
			}
			return TRUE;
		}
		switch($section) {
			case "oldclassified":
				$table	= "#__opensim_userclassifieds";
				$where	= "expirationdate";
			break;
			case "oldevents":
				$table = "#__opensim_search_events";
				$where	= "dateUTC";
			break;
		}
		if($table) {
			$query = sprintf("DELETE FROM %1\$s WHERE %1\$s.%2\$s < UNIX_TIMESTAMP(UTC_TIMESTAMP())",$db->escape($table),$db->escape($where));
			$db->setQuery($query);
			$db->execute();
		}
		return TRUE;
	}

	public function eventdelete($id) {
		$db		= JFactory::getDbo();
		$conditions = array(
			$db->quoteName('eventid').' = '.$db->quote($id)
		);
		$query	= $db->getQuery(true);
		$query->delete($db->quoteName('#__opensim_search_events'));
		$query->where($conditions);
		$db->setQuery($query);
		$db->execute();
	}

	public function renewclassified($id) {
		$db			= JFactory::getDbo();

		if($this->_settingsData['classified_hide'] == -1) { //hide never
			$expirationdate = 4294967295; // this is the max positive integer value that DB field can take
		} else {
			$expirationdate = time() + $this->_settingsData['classified_hide'];
		}
		$fields	= array(
				$db->quoteName('expirationdate').' = '.$db->quote($expirationdate)
		);

		$conditions = array(
			$db->quoteName('classifieduuid').' = '.$db->quote($id)
		);
		$query	= $db->getQuery(true);
		$query->update($db->quoteName('#__opensim_userclassifieds'))->set($fields)->where($conditions);
		$db->setQuery($query);
		$db->execute();
	}

	public function deleteclassified($id) {
		$db		= JFactory::getDbo();
		$conditions = array(
			$db->quoteName('classifieduuid').' = '.$db->quote($id)
		);
		$query	= $db->getQuery(true);
		$query->delete($db->quoteName('#__opensim_userclassifieds'));
		$query->where($conditions);
		$db->setQuery($query);
		$db->execute();
	}
}
?>
