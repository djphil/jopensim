<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die();

// require_once(JPATH_COMPONENT_SITE.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'opensim.php');

class opensimModelInterfaceSearch extends opensimModelInterface {

	public $debug	= FALSE;

	public function __construct() {
		parent::__construct();
		if($this->jdebug['search']) $this->debug	= TRUE;
	}

	public function initMethods() { // empty this this method to avoid endless loops
		return;
	}

	public function initAddons() { // empty this this method to avoid endless loops
		return;
	}

	public function registerSearch($parameter) {
		if($this->debug === TRUE) $this->debuglog($parameter,"Parameter for ".__FUNCTION__);
		if(!array_key_exists("host",$parameter) || !$parameter['host'] || !array_key_exists("port",$parameter) || !$parameter['port']  || !array_key_exists("registerSearch",$parameter) || $parameter['registerSearch'] != "jOpenSim") {
			$retval['success'] = FALSE;
			$retval['errorMessage'] = "Invalid parameter for ".__FUNCTION__;
		}
		if($parameter['service'] == "online") {
			$time	= time();
			$db		= JFactory::getDBO();
			$query	= sprintf("INSERT INTO #__opensim_search_hostsregister 
							(`host`,`port`,`register`) VALUES (%1\$s,%2\$s,%3\$s)
						ON DUPLICATE KEY UPDATE
							`register` = '%3\$d'",
					$db->quote($parameter['host']),
					$db->quote($parameter['port']),
					$time);
			$db->setQuery($query);
			$result = $db->execute();
			if($result) {
				$retval['success']		= TRUE;
				$retval['message']		= "Host ".$parameter['host'].":".$parameter['port']." successfully registered";
			} else {
				$retval['success']		= FALSE;
				$retval['errorMessage'] = "Error while registering host ".$parameter['host'].":".$parameter['port'];
			}
		} elseif($parameter['service'] == "offline") {
			$db		= JFactory::getDBO();
			$query = $db->getQuery(true);
			$conditions = array(
				$db->quoteName('host').' = '.$db->quote($parameter['host']), 
				$db->quoteName('port').' = '.$db->quote($parameter['port'])
			);
			$query->delete($db->quoteName('#__opensim_search_hostsregister'));
			$query->where($conditions);
			$db->setQuery($query);
			$db->execute();
			$retval['success']		= TRUE;
			$retval['message']		= "Host ".$parameter['host'].":".$parameter['port']." successfully deregistered";
		} else {
			$retval['success'] = FALSE;
			$retval['errorMessage'] = "Invalid service value for ".__FUNCTION__;
		}
		return $retval;
	}

	public function init_SearchDataUpdate($parameter) {
		$updateInterval = $parameter['updateInterval'];
		$registeredHost = $parameter['openSimServIP'];

		if($updateInterval < 0) {
			$this->searchDataUpdate($parameter);
			$retval['errorMessage'] = "updateInterval disabled";
		} else {
			$retval = $this->searchDataUpdate($parameter);
		}
		return $retval;
	}

	public function searchDataUpdate($parameter) {
		if($this->debug === TRUE) $this->debuglog($parameter,"Parameter for ".__FUNCTION__);
		$registeredHost = $parameter['openSimServIP'];
		$host = parse_url($registeredHost);
		if($this->isregistered($host)) {
			$retval = $this->checkhost($host['host'],$host['port']);
		} else {
			$retval['success'] = FALSE;
			$retval['errorMessage'] = sprintf("Host %s with port %s not registered!",$host['host'],$host['port']);
		}
		return $retval;
	}

	public function isregistered($host) {
		$query = sprintf("SELECT * FROM #__opensim_search_hostsregister WHERE host = '%s' AND port = '%s'",$host['host'],$host['port']);
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$db->execute();
		$num_rows = $db->getNumRows();
		if($num_rows == 1) return TRUE;
		else return FALSE;
	}

	public function checkhost($host,$port) {
		$fp = fsockopen ($host,$port,$errno,$errstr, 10);
		$db = JFactory::getDBO();
		if(!$fp) {
			//Setting a "fake" update time so this host will have time to get back online
			$next = time() + 600; // 5 mins, so we don't get stuck

			$query = sprintf("UPDATE #__opensim_search_hostsregister SET failcounter = failcounter + 1, lastcheck = '%d' WHERE host = '%s' AND port = '%d'",$next,$host,$port);
			$db->setQuery($query);
			$db->execute();

			$retval['success'] = FALSE;
			$retval['errorMessage'] = sprintf("Host %s is not responding at port %s!",$host,$port);
		} else {
			$now = time();
			$query = sprintf("SELECT lastcheck FROM #__opensim_search_hostsregister WHERE host = '%s' AND port = '%d'",$host,$port);
			$db->setQuery($query);
			$expire = $db->loadResult();

			if($now < $expire) {
				$retval['success'] = TRUE;
				$retval['message'] = "DataSnapShot already up2date!";
			} else {
				$retval = $this->parseSearchData($host,$port);
			}
		}
		return $retval;
	}

	public function get_url($host,$port,$url) {
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

	public function parseSearchData($host,$port) {
		if($this->debug === TRUE) {
			$this->debuglog($host.":".$port,"Parameter for ".__FUNCTION__);
		}
		$jopensim_expire	= 0;
		$db					= JFactory::getDBO();

		$next				= time() + 600; // first set next check to 5 mins, so we don't get stuck
		$query				= sprintf("UPDATE #__opensim_search_hostsregister SET lastcheck = '%d' WHERE host = '%s' AND port = '%d'",$next,$host,$port);
		$db->setQuery($query);
		$db->execute();

		//
		// Load XML doc from URL
		//
		$objDOM = new DOMDocument();
		$objDOM->resolveExternals = false;
		$searchresponse = $this->get_url($host, $port, "?method=collector");

		// catch "unexpected" response
		if(!$searchresponse) {
			$retval['success'] = FALSE;
			$retval['errorMessage'] = "No search response";
			return $retval;
		} elseif($searchresponse == "Please try your request again later") {
			$retval['success'] = FALSE;
			$retval['errorMessage'] = $searchresponse."  (Collection request spam. reply try later)";
			return $retval;
		}

		$objDOM->loadXML($searchresponse);

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

			// First check if we already have a region that is the same
			$query = sprintf("SELECT * FROM #__opensim_search_regions WHERE regionuuid = '%s'",$regionuuid);
			$db->setQuery($query);
			$db->execute();
			$num_rows = $db->getNumRows();
			if($num_rows > 0) { // Region found, lets delete and rebuild all data
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
			}

			$data		= $region->getElementsByTagName("data")->item(0);
			$estate		= $data->getElementsByTagName("estate")->item(0);
			$username	= $estate->getElementsByTagName("name")->item(0)->nodeValue;
			$useruuid	= $estate->getElementsByTagName("uuid")->item(0)->nodeValue;
			$regionData	= $this->getRegionInfo($regionuuid);
			if(is_array($regionData) && array_key_exists("locX",$regionData) && array_key_exists("locY",$regionData)) {
				$locX	= $regionData['locX'];
				$locY	= $regionData['locY'];
			} else {
				$regioninfo = $opensim->getRegionData($regionuuid);
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
		$retval['success'] = TRUE;
		return $retval;
	}

	public function getRegionInfo($regionUUID) {
		$regioninfo = $this->opensim->getRegionData($regionUUID);
		return $regioninfo;
	}

	public function dir_places_query($parameter) {
		if($this->debug === TRUE) $this->debuglog($parameter,"Parameter for ".__FUNCTION__);
		$text			= $parameter['text'];
		$category		= $parameter['category'];
		$query_start	= $parameter['query_start'];

		$pieces			= explode(" ", $text);
		$text			= implode("%", $pieces);

		if ($text == "%%%") {
			$retval['success'] = FALSE;
			$retval['errorMessage'] = "Invalid search terms";
			return $retval;
		}

		$limit = sprintf(" LIMIT %d,100",$query_start);

		if ($category != -1) {
			$query = sprintf("SELECT #__opensim_search_parcels.* FROM #__opensim_search_parcels WHERE
								(#__opensim_search_parcels.searchcategory = -1 or #__opensim_search_parcels.searchcategory = '%1\$s')
							AND
								(#__opensim_search_parcels.parcelname LIKE '%%%2\$s%%'
								OR
								 #__opensim_search_parcels.description LIKE '%%%2\$s%%')
							ORDER BY
								#__opensim_search_parcels.dwell DESC,
								#__opensim_search_parcels.parcelname ASC %3\$s",
				$category,
				$text,
				$limit);
		} else {
			$query = sprintf("SELECT #__opensim_search_parcels.* FROM #__opensim_search_parcels WHERE
								#__opensim_search_parcels.parcelname LIKE '%%%1\$s%%'
							OR
								#__opensim_search_parcels.description LIKE '%%%1\$s%%'
							ORDER BY
								#__opensim_search_parcels.dwell DESC,
								#__opensim_search_parcels.parcelname ASC %2\$s",
				$text,
				$limit);
		}

		$db = JFactory::getDBO();
		$db->setQuery($query);
		$searchresult = $db->loadAssocList();

		$data = array();

		if(is_array($searchresult) && count($searchresult) > 0) {
			foreach($searchresult AS $row) {
				$data[] = array(
						"parcel_id" => $row["infouuid"],
						"name" => $row["parcelname"],
						"for_sale" => "False",
						"auction" => "False",
						"dwell" => $row["dwell"]);
			}
		}
		$retval['success']			= TRUE;
		$retval['errorMessage']		= "";
		$retval['data']				= $data;
		return $retval;
	}

	public function dir_popular_query($parameter) {
		if($this->debug === TRUE) $this->debuglog($parameter,"Parameter for ".__FUNCTION__);
		// Todo: dir_popular_query ... what results???
		$retval['success']			= TRUE;
		return $retval;
	}

	public function dir_land_query($parameter) {
		if($this->debug === TRUE) $this->debuglog($parameter,"Parameter for ".__FUNCTION__);
		$flags			= $parameter['flags'];
		$type			= $parameter['type'];
		$price			= $parameter['price'];
		$area			= $parameter['area'];
		$query_start	= $parameter['query_start'];

		$terms = array();
		$order = "lsq";
		if ($flags & 0x80000)	$order = "parcelname";
		if ($flags & 0x10000)	$order = "saleprice";
		if ($flags & 0x40000)	$order = "area";
		if (!($flags & 0x8000))	$order .= " desc";

		if ($flags & 0x100000)	$terms[] = sprintf("saleprice <= '%d'",$price);
		if ($flags & 0x200000)	$terms[] = sprintf("area >= '%d'",$area);

		if (($type & 26) == 2) { // Auction
			$retval['success'] = FALSE;
			$retval['errorMessage'] = "No auctions listed";
			return $retval;
		}

		if (($type & 24) == 8)	$terms[] = "parentestate = 1";
		if (($type & 24) == 16)	$terms[] = "parentestate <> 1";

		if ($flags & 0x800)		$terms[] = "mature = 'false'";
		if ($flags & 0x4000)	$terms[] = "mature = 'true'";

		$where = "";
		if (count($terms) > 0)
			$where = " WHERE " . join(" AND ", $terms);

		$limit = sprintf(" LIMIT %d,101",$query_start);

		$query = "SELECT #__opensim_search_parcelsales.*, #__opensim_search_parcelsales.saleprice/#__opensim_search_parcelsales.area as lsq from #__opensim_search_parcelsales" . $where .
					" ORDER BY " . $order . $limit;

		$db = JFactory::getDBO();
		$db->setQuery($query);
		$searchresult = $db->loadAssocList();

		$data = array();
		if(is_array($searchresult) && count($searchresult) > 0) {
			foreach($searchresult AS $row) {
				$data[] = array(
						"parcel_id"		=> $row["infoUUID"],
						"name"			=> $row["parcelname"],
						"auction"		=> "false",
						"for_sale"		=> "true",
						"sale_price"	=> $row["saleprice"],
						"landing_point"	=> $row["landingpoint"],
						"region_UUID"	=> $row["regionUUID"],
						"type"			=> "Mainland",
						"area"			=> $row["area"]);
			}
		}

		$retval['success']			= TRUE;
		$retval['errorMessage']		= "";
		$retval['data']				= $data;
		return $retval;
	}

	public function dir_events_query($parameter) {
		if($this->debug === TRUE) $this->debuglog($parameter,"Parameter for ".__FUNCTION__);
		if(!is_array($parameter) || !array_key_exists('text',$parameter) || !array_key_exists('query_start',$parameter) || !array_key_exists('flags',$parameter)) {
			$retval['success'] = FALSE;
			$retval['message'] = "Wrong parameters for function ".__FUNCTION__." at line ".__LINE__;
			return $retval;
		}
		$pieces = explode("|",$parameter['text']);
		if(count($pieces) != 3) {
			$retval['success'] = FALSE;
			$retval['message'] = "Invalid text parameter for function ".__FUNCTION__." at line ".__LINE__;
			return $retval;
		}

		$day		= $pieces[0];
		$category	= intval($pieces[1]);
		$searchterm	= $pieces[2];

		if($day == "u") {
			$stampadd = 0; // today
			$timestampSearchAdd = " + (duration * 60)";
		} else {
			$stampadd = 24*60*60*intval($day); // seconds of days in future
			$timestampSearchAdd = "";
		}

		$timestamp			= time();
		$timeCompound		= date("Y-m-d H:i:s",$timestamp);

		$dateTimeZone		= new DateTimeZone(date_default_timezone_get());
		$dateTimeZoneUTC	= new DateTimeZone('UTC');

		$dateTimeUTC		= new DateTime($timeCompound, $dateTimeZoneUTC);
		$timeOffset			= $dateTimeZone->getOffset($dateTimeUTC);

		$timestampUTC		= $timestamp - $timeOffset;
		$timestampSearch	= $timestampUTC + $stampadd;

		if($category > 0) {
			$categorywhere = sprintf(" AND category = '%d'",$category);
		} else {
			$categorywhere = "";
		}

		if($searchterm) {
			if($searchterm == "%%%") {
				$retval['success'] = FALSE;
				$retval['message'] = "Invalid search parameter for function ".__FUNCTION__." at line ".__LINE__;
				return $retval;
			}
			$searchwhere	= sprintf(" AND (name LIKE '%%%1\$s%%' OR description LIKE '%%%1\$s%%')",$searchterm);
		} else {
			$searchwhere	= "";
		}

		$db = JFactory::getDBO();

		if(array_key_exists("avatar_id",$parameter)) { // This is a request of the search module from 0.2.1 up ... we need to check the users timezone and convert if nessecary
			$timezonecalc	= $this->timezonecalc($parameter);
		} else {
			$settings		= $this->settings;
			$timezonecalc['timezone']	= $settings['eventtimedefault'];
			$timezonecalc['offset']		= FALSE; // "Old" module, no need for timezone calculations
		}

		$pieces = explode("|",$parameter['text']);
		$query = sprintf("SELECT
							#__opensim_search_events.*,
							IFNULL(#__opensim_search_allparcels.regionUUID,'%s') AS regionuuid,
							IFNULL(#__opensim_search_allparcels.landingpoint,'126/126/32') AS landingpoint
						FROM
							#__opensim_search_events LEFT JOIN #__opensim_search_allparcels USING(parcelUUID)
						WHERE
							dateUTC%s >= '%d'
						%s%s
						GROUP BY
							#__opensim_search_events.eventid",
					$this->uuidZero,
					$timestampSearchAdd,
					$timestampSearch,
					$categorywhere,
					$searchwhere);
		$db->setQuery($query);

		$searchresult = $db->loadAssocList();

		$data = array();

		if(is_array($searchresult) && count($searchresult) > 0) {
			foreach($searchresult AS $row) {
				if($timezonecalc['offset'] === TRUE) {
					$timeparam['timestamp']	= $row["dateUTC"];
					$timeparam['timezone']	= $timezonecalc['timezone'];
					$timevonverted			= $this->timezoneconvert($timeparam);
				} else {
					$timevonverted			= intval($row["dateUTC"]);
				}

				$date = strftime("%m/%d %I:%M %p",$timevonverted);
				$data[] = array(
						"region_UUID"	=> $row['regionuuid'],
						"landing_point"	=> $row['landingpoint'],
						"owner_id"		=> $row['owneruuid'],
						"name"			=> utf8_decode($row['name']),
						"event_id"		=> $row['eventid'],
						"date"			=> $date,
						"unix_time"		=> $timevonverted,
						"event_flags"	=> $row['eventflags']);
			}
		}

		$retval['success']			= TRUE;
		$retval['timezone']			= $timezonecalc['timezone'];
		$retval['errorMessage']		= "";
		$retval['data']				= $data;
		return $retval;
	}

	public function event_info_query($parameter) {
		if($this->debug === TRUE) $this->debuglog($parameter,"Parameter for ".__FUNCTION__);
		if(!is_array($parameter) || !array_key_exists('eventID',$parameter)) {
			$retval['success']			= FALSE;
			$retval['message'] 			= "Wrong parameters for function event_info_query()";
			return $retval;
		}
		
		if(array_key_exists("avatar_id",$parameter)) { // This is a request of the search module from 0.2.1 up ... we need to check the users timezone and convert if nessecary
			$timezonecalc = $this->timezonecalc($parameter);
		} else {
			$settings = $this->settings;
			$timezonecalc['timezone'] = $settings['eventtimedefault'];
			$timezonecalc['offset'] = FALSE; // "Old" module, no need for timezone calculations
		}

		$query			= sprintf("SELECT * FROM #__opensim_search_events WHERE #__opensim_search_events.eventid = '%d'",$parameter['eventID']);
		$db				= JFactory::getDBO();
		$db->setQuery($query);
		$searchresult	= $db->loadAssocList();

		$data = array();
		if(is_array($searchresult) && count($searchresult) > 0) {
			foreach($searchresult AS $row) {
				if($timezonecalc['offset'] === TRUE) {
					$timeparam['timestamp']	= $row["dateUTC"];
					$timeparam['timezone']	= $timezonecalc['timezone'];
					$timevonverted			= $this->timezoneconvert($timeparam);
				} else {
					$timevonverted			= intval($row["dateUTC"]);
				}

				$date						= strftime("%m/%d/%Y %I:%M %p",$timevonverted);
				$category					= $this->eventcategory($row['category']);
				$datacount					= count($data);
				if(!$row['description']) $row['description'] = JText::_('JOPENSIM_EVENT_NODESCRIPTION');

				$data[$datacount] = array(
					"event_id"			=> $row["eventid"],
					"creator"			=> $row["creatoruuid"],
					"name"				=> utf8_decode($row["name"]),
					"category"			=> $category,
					"description"		=> utf8_decode($row["description"]),
					"date"				=> $date,
					"dateUTC"			=> $timevonverted,
					"duration"			=> $row["duration"],
					"covercharge"		=> $row["covercharge"],
					"coveramount"		=> $row["coveramount"],
					"simname"			=> $row["simname"],
					"globalposition"	=> $row["globalPos"],
					"eventflags"		=> $row["eventflags"]);
				$data[$datacount]['mature']			= ($row["mature"] == "true") ? TRUE:FALSE;
			}
		}
		$retval['success']			= TRUE;
		$retval['errorMessage']		= "";
		$retval['data']				= $data;
		return $retval;
	}

	public function timezonecalc($parameter) {
		if($this->debug === TRUE) $this->debuglog($parameter,"Parameter for ".__FUNCTION__);
		$retval				= array();
		$retval['timezone']	= "";
		$db					= JFactory::getDBO();
		$query				= sprintf("SELECT timezone FROM #__opensim_usersettings WHERE `uuid`= '%s'",$parameter['avatar_id']);
		$db->setQuery($query);
		$db->execute();
		$num_rows = $db->getNumRows();
		if($num_rows == 1) $retval['timezone'] = $db->loadResult();
		if($num_rows != 1 || !$retval['timezone']) { // User has no settings saved yet, lets take the default timezone from jOpenSim settings
			$settings = $this->settings;
			$retval['timezone'] = $settings['eventtimedefault'];
		}
		if(!$retval['timezone']) $retval['timezone'] = "UTC"; // this should actually not happen, but keep it here to avoid problems
		if($retval['timezone'] == "UTC") { 
			$retval['offset'] = FALSE; // no need for calculations
		} else {
			$retval['offset'] = TRUE;
		}
		if($this->debug === TRUE) $this->debuglog($retval,"retval for ".__FUNCTION__);
		return $retval;
	}

	public function timezoneconvert($parameter) {
		if($this->debug === TRUE) $this->debuglog($parameter,"Parameter for ".__FUNCTION__);
		$eventTimeCompound		= date("Y-m-d H:i:s",$parameter['timestamp']);
		$eventtime['minutes']	= date("i",$parameter['timestamp']);
		$eventtime['hour']		= date("H",$parameter['timestamp']);
		$eventtime['day']		= date("d",$parameter['timestamp']);
		$eventtime['month']		= date("m",$parameter['timestamp']);
		$eventtime['year']		= date("Y",$parameter['timestamp']);

		$dateTimeZone			= new DateTimeZone($parameter['timezone']);
		$dateTimeZoneUTC		= new DateTimeZone('UTC');

		$dateTimeUTC			= new DateTime($eventTimeCompound, $dateTimeZoneUTC);
		$timeOffset				= $dateTimeZone->getOffset($dateTimeUTC);

		$timeconverted			= $parameter['timestamp'] + $timeOffset;

		return $timeconverted;
	}

	public function eventcategory($cat = null) {
		$category = array(
			18	=> "Discussion",
			19	=> "Sports",
			20	=> "Live Music",
			22	=> "Commercial",
			23	=> "Nightlife/Entertainment",
			24	=> "Games/Contests",
			25	=> "Pageants",
			26	=> "Education",
			27	=> "Arts and Culture",
			28	=> "Charity/Support Groups",
			29	=> "Miscellaneous");
		if(!$cat) {
			return $category;
		} else {
			if(array_key_exists($cat,$category)) {
				return $category[$cat];
			} else {
				return FALSE;
			}
		}
	}

	public function dir_classified_query($parameter) {
		if($this->debug === TRUE) $this->debuglog($parameter,"Parameter for ".__FUNCTION__);
		if(!is_array($parameter) || !array_key_exists("text",$parameter) || !array_key_exists("category",$parameter) || !array_key_exists("query_start",$parameter) || !array_key_exists("flags",$parameter) || $parameter['text'] == "%%%") {
			$retval['success'] = FALSE;
			$retval['errorMessage']	= "Invalid search terms";
		} else {
			$text				= $parameter['text'];
			$category			= intval($parameter['category']);
			$query_start		= intval($parameter['query_start']);
			$flags				= intval($parameter['flags']);
			$pgonly				= $flags & 6;
			$matureonly			= $flags & 72;
			$retval['pgonly']	= $pgonly;

			$settings			= $this->settings;
			$now				= time();

			$db					= JFactory::getDBO();
			$query				= $db->getQuery(true);

			$search				= '%'.$db->escape($text,true).'%';

			$query
				->select($db->quoteName('#__opensim_userclassifieds').".*")
				->from($db->quoteName('#__opensim_userclassifieds'))
				->where("(".$db->quoteName('#__opensim_userclassifieds.name')." LIKE ".$db->quote($search,false)." OR ".$db->quoteName('#__opensim_userclassifieds.description')." LIKE ".$db->quote($search,false).")")
				->where($db->quoteName('#__opensim_userclassifieds.expirationdate')." >= ".$db->quote($now))
				->order($db->quoteName('#__opensim_userclassifieds.'.$settings['classified_sort'])." ".$settings['classified_order']);

			if($pgonly > 0 && $matureonly == 0) { // mature or adult classified should not be included
				$query->where($db->quoteName('#__opensim_userclassifieds.classifiedflags')." & 72 = 0");
			} elseif($pgonly == 0 && $matureonly > 0) { // only mature or adult should be returned
				$query->where($db->quoteName('#__opensim_userclassifieds.classifiedflags')." & 72 > 0");
			}

			if($category != 0) { // search for special category
				$query->where($db->quoteName('#__opensim_userclassifieds.category')." = ".$db->quote($category));
			}

			$db->setQuery($query,$query_start,101);
			$db->execute();
			$num_rows = $db->getNumRows();
			$retval['success'] = TRUE;
			if($num_rows > 0) {
				$rows = $db->loadAssocList();
				if(is_array($rows) && count($rows) > 0) {
					$data = array();
					foreach($rows AS $row) {
						$data[] = array(
								"classifiedid"		=> $row["classifieduuid"],
								"name"				=> $row["name"],
								"classifiedflags"	=> $row["classifiedflags"],
								"creation_date"		=> $row["creationdate"],
								"expiration_date"	=> $row["expirationdate"],
								"priceforlisting"	=> $row["priceforlisting"]);
					}
					$retval['data'] = $data;
				} else {
					$retval['data'] = array();
				}
			} else {
				$retval['data'] = array();
			}
		}
		return $retval;
	}

	public function classifieds_info_query($parameter) {
		if($this->debug === TRUE) $this->debuglog($parameter,"Parameter for ".__FUNCTION__);
		if(!is_array($parameter) || !array_key_exists("classifiedID",$parameter)) {
			$retval['success'] = FALSE;
			$retval['errorMessage']	= "Invalid search terms for function classifieds_info_query";
		} else {
			$classifiedID = $parameter['classifiedID'];
			$db = JFactory::getDBO();
			$query = sprintf("SELECT #__opensim_userclassifieds.* FROM #__opensim_userclassifieds WHERE #__opensim_userclassifieds.classifieduuid = '%s'",$classifiedID);
			$db->setQuery($query);
			$db->execute();
			$num_rows = $db->getNumRows();
			if($num_rows == 1) {
				$retval['success'] = TRUE;
				$classified = $db->loadAssoc();
				$data[] = array(
					"classifieduuid"	=> $classified["classifieduuid"],
					"creatoruuid"		=> $classified["creatoruuid"],
					"creationdate"		=> $classified["creationdate"],
					"expirationdate"	=> $classified["expirationdate"],
					"category"			=> $classified["category"],
					"name"				=> $classified["name"],
					"description"		=> $classified["description"],
					"parceluuid"		=> $classified["parceluuid"],
					"parentestate"		=> $classified["parentestate"],
					"snapshotuuid"		=> $classified["snapshotuuid"],
					"simname"			=> $classified["simname"],
					"posglobal"			=> $classified["posglobal"],
					"parcelname"		=> $classified["parcelname"],
					"classifiedflags"	=> $classified["classifiedflags"],
					"priceforlisting"	=> $classified["priceforlisting"]);
				$retval['data'] = $data;
			} else {
				$retval['success'] = TRUE;
				$retval['data'] = array();
			}
		}
		return $retval;
	}

	public function addon_disabled($parameter) {
		$retval['success']	= FALSE;
		$retval['error']	= "Search Addon disabled in jOpenSim";
		$retval['params']	= $parameter;
		return $retval;
	}


}
?>