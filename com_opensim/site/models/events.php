<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2017 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die();

/*jimport('joomla.application.component.model');*/
require_once(JPATH_COMPONENT_SITE.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'opensim.php');

class opensimModelEvents extends OpenSimModelOpenSim {

	var $_data;
	var $_data_ext;
	var $_regiondata	= null;
	var $_settingsData;
	var $filename		= "events.php";
	var $view			= "events";
	var $_os_db;
	var $_osgrid_db;
	var $_db;

	public function __construct() {
		parent::__construct();
		$this->getSettingsData();
		$this->_os_db = $this->getOpenSimDB();
		$this->_osgrid_db = $this->getOpenSimGridDB();
	}

	public function getEventList($params = null) {
		$timestamp			= time();
		$timeCompound		= date("Y-m-d H:i:s",$timestamp);
	
		$dateTimeZone		= new DateTimeZone(date_default_timezone_get());
		$dateTimeZoneUTC	= new DateTimeZone('UTC');
	
		$dateTimeUTC		= new DateTime($timeCompound, $dateTimeZoneUTC);
		$timeOffset			= $dateTimeZone->getOffset($dateTimeUTC);

		$osuid = $this->opensimIsCreated();
		$jsettings = $this->getJuserData($osuid);
		$this->ossettings = $this->_settingsData;
		if(!array_key_exists("timezone",$jsettings) || !$jsettings['timezone']) $usertimezone = $this->ossettings['eventtimedefault'];
		else $usertimezone = $jsettings['timezone'];

		$dateTimeZoneUser	= new DateTimeZone($usertimezone);
		$userTimeOffset		= $dateTimeZoneUser->getOffset($dateTimeUTC);
	
		$timeconverted		= $timestamp - $timeOffset;
		$usertimeconverted	= $timestamp + $userTimeOffset;

		if(!$params || !is_array($params)) {
			if(!array_key_exists("listmatureevents",$this->ossettings)) $maturesetting = 0;
			else $maturesetting = $this->ossettings['listmatureevents'];
			if($maturesetting == "1") $flagwhere = "";
			else $flagwhere = " AND eventflags = '0'";
			$query = sprintf("SELECT * FROM #__opensim_search_events WHERE dateUTC >= '%d'%s ORDER BY dateUTC",$timeconverted,$flagwhere);
		} else { // temporary still the same
			if(!array_key_exists("listmatureevents",$this->ossettings)) $maturesetting = 0;
			else $maturesetting = $this->ossettings['listmatureevents'];
			if($maturesetting == "1") $flagwhere = "";
			else $flagwhere = " AND eventflags = '0'";
			$query = sprintf("SELECT * FROM #__opensim_search_events WHERE (dateUTC + (duration * 60))>= '%d'%s ORDER BY dateUTC",$timeconverted,$flagwhere);
		}
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$db->execute();
		$num_rows = $db->getNumRows();
		$retval['num_rows']					= $num_rows;
		$retval['params']					= $params;
		$retval['timestamp']				= $timestamp;
		$retval['timestamptDate']			= date("d.m.Y H:i:s",$timestamp);
		$retval['timeOffset']				= $timeOffset;
		$retval['timeconverted']			= $timeconverted;
		$retval['timeconvertedDate']		= date("d.m.Y H:i:s",$timeconverted);
		$retval['usertimeconverted']		= $usertimeconverted;
		$retval['usertimeconvertedDate']	= date("d.m.Y H:i:s",$usertimeconverted - $timeOffset);
		$retval['query']					= $query;
		$retval['events']					= $db->loadAssocList();
		if(is_array($retval['events']) && count($retval['events']) > 0) {
			$suchmuster		= '/([0-9]*),([0-9]*),([0-9]*)/';
			foreach($retval['events'] AS $key => $event) {
				$runbydata = $this->getUserData($retval['events'][$key]['owneruuid']);
				if(array_key_exists("firstname",$runbydata) && array_key_exists("lastname",$runbydata)) {
					$retval['events'][$key]['ownername'] = $runbydata['firstname']." ".$runbydata['lastname'];
				} else {
					$retval['events'][$key]['ownername'] = JText::_('JOPENSIM_UNKNOWN_USER');
				}
				if(is_array($params) && array_key_exists("uuid",$params)) {
					if($params['uuid'] == $retval['events'][$key]['owneruuid'] || $params['uuid'] == $retval['events'][$key]['creatoruuid']) $retval['events'][$key]['editflag'] = 1;
					else $retval['events'][$key]['editflag'] = 0;
				} else {
					$retval['events'][$key]['editflag'] = 0;
				}
				preg_match($suchmuster,$retval['events'][$key]['globalPos'],$treffer);
				if(is_array($treffer) && count($treffer) == 4) {
					$retval['events'][$key]['posX']			= $treffer[1];
					$retval['events'][$key]['posY']			= $treffer[2];
					$retval['events'][$key]['regionPosX']	= $treffer[1] - intval($treffer[1] / 256) * 256;
					$retval['events'][$key]['regionPosY']	= $treffer[2] - intval($treffer[2] / 256) * 256;
					$retval['events'][$key]['posZ']			= $treffer[3];
					if($retval['events'][$key]['simname']) {
						$retval['events'][$key]['surl']		= $retval['events'][$key]['simname']."/".$retval['events'][$key]['regionPosX']."/".$retval['events'][$key]['regionPosY']."/".$retval['events'][$key]['posZ'];
					} else {
						$param['posX'] = $treffer[1];
						$param['posY'] = $treffer[2];
						$regioninfo		= $this->opensim->globalPosition2regionPosition($param);
						$retval['events'][$key]['surl']		= $regioninfo['regionname']."/".$retval['events'][$key]['regionPosX']."/".$retval['events'][$key]['regionPosY']."/".$retval['events'][$key]['posZ'];
						$retval['events'][$key]['simname']	= $regioninfo['regionname'];
					}
				} else { // globalPosition seems to be invalid :(
					$retval['events'][$key]['posX']			= 0;
					$retval['events'][$key]['posY']			= 0;
					$retval['events'][$key]['regionPosX']	= 0;
					$retval['events'][$key]['regionPosY']	= 0;
					$retval['events'][$key]['posZ']			= 0;
					if($retval['events'][$key]['simname']) {
						$retval['events'][$key]['surl']		= $retval['events'][$key]['simname']."/127/127"; // create a pseudo-surl in the middle of the region
					} else {
						$retval['events'][$key]['surl']		= null;
					}
				}
				$retval['events'][$key]['utcdate']			= date(JText::_('JOPENSIM_EVENT_DATETIME_FORMAT'),$retval['events'][$key]['dateUTC']);
				$retval['events'][$key]['userdatetime']		= date(JText::_('JOPENSIM_EVENT_DATETIME_FORMAT'),$retval['events'][$key]['dateUTC']+$userTimeOffset);
				$retval['events'][$key]['userdate']			= date(JText::_('JOPENSIM_EVENT_DATE_FORMAT'),$retval['events'][$key]['dateUTC']+$userTimeOffset);
				$retval['events'][$key]['usertime']			= date(JText::_('JOPENSIM_EVENT_TIME_FORMAT'),$retval['events'][$key]['dateUTC']+$userTimeOffset);
				$retval['events'][$key]['categoryname']	= $this->getEventCategory($retval['events'][$key]['category']);
			}
		} else {
			$retval['events'] = array();
		}
		return $retval;
	}

	public function getOwnerLand($created) {
		$opensim	= $this->opensim;
		$ownerLand	= $opensim->ownerLand($created); // check the landowner access for this user
		return $ownerLand;
	}

	public function getGroupLand($created,$groupflag) {
		$opensim	= $this->opensim;
		$groupland	= $opensim->groupLand4user($created,$groupflag); // check if the user is member of any group with land (=event hosting) access
		return $groupland;
	}

	public function getPublicLand() {
		$opensim		= $this->opensim;
		$publicLand		= null;
		$publicRegions	= $opensim->getPublicRegions(); // Public regions do have '1' in the column `public` of the table #__opensim_mapinfo
		if(is_array($publicRegions) && count($publicRegions) > 0) { // we found some public regions ... get the parcels in there
			$publicLand = array();
			foreach($publicRegions AS $publicRegion) {
				$publicParcel		= $opensim->getRegionLand($publicRegion); // get the parcels in these public regions
				if(is_array($publicParcel) && count($publicParcel) > 0) {
					$publicLand = array_merge($publicLand,$publicParcel);
				}
			}
		}
		return $publicLand;
	}

	public function getGroupAccessFlag($flagarray) {
		if(!is_array($flagarray) || count($flagarray) == 0) return FALSE;
		$groupflag = 0;
		if(in_array("OPT1",$flagarray)) $groupflag += 536870912;
		if(in_array("OPT2",$flagarray)) $groupflag += 1073741824;
		if(in_array("OPT3",$flagarray)) $groupflag += 2147483648;
		if(in_array("OPT4",$flagarray)) $groupflag += 4294967296;
		if(in_array("OPT5",$flagarray)) $groupflag += 2199023255552;
		return $groupflag;
	}

	public function insertEvent($data) {
		$opensim			= $this->opensim;
		$retval				= array();
		$retval['error']	= 0;
		if(!$data['eventname']) { // Error: no name given for event
			$retval['error'] |= 1;
		}
		$datum = explode("/",$data['eventdate']);
		if(count($datum) != 3) {
			$retval['error'] |= 2; // Error: invalid date for event
		}
		if(!$datum[2]) $datum[2] = date("Y");
		if(!checkdate($datum[1],$datum[0],$datum[2])) {
			$retval['error'] |= 2; // Error: invalid date for event
		}
		$osuid					= $this->opensimIsCreated();
		if(!$osuid) {
			$retval['error'] |= 4; // Error: user does not have any inworld account
		}

		if($retval['error'] > 0) { // some error occured, lets go back and display messages
			return $retval;
		}

		// From here, nothing "should" go wrong anymore

		$eventTimeCompound	= $datum[2]."-".$datum[1]."-".$datum[0]." ".$data['eventtime'].":00";
		$eventtime = explode(":",$data['eventtime']);

		$dateTimeZone			= new DateTimeZone($data['eventtimezone']);
		$dateTimeZoneUTC		= new DateTimeZone('UTC');

		$dateTime				= new DateTime($eventTimeCompound, $dateTimeZone);
		$dateTimeUTC			= new DateTime($eventTimeCompound, $dateTimeZoneUTC);

		$timestamp				= mktime($eventtime[0],$eventtime[1],0,$datum[1],$datum[0],$datum[2]);
		$timestampEvent			= $dateTime->format("U");
		$timestampUTC			= $dateTimeUTC->format("U");
		$differenceServerUTC	= $timestamp - $timestampUTC;

		$utctimestamp			= $timestampEvent + $differenceServerUTC;
		$retval['timestampUTC']	= $utctimestamp;
		$retval['datetimeUTC']	= date("d.m.Y H:i:s",$utctimestamp);

		$parceldata				= $this->getParcelInfo($data['eventlocation']);
		$retval['globalPos']	= $parceldata;
		$retval['simname']		= $parceldata['regionName'];

		if($data['eventflags'] > 0) $mature = "true";
		else $mature = "false";

//		for($i=0; $i<100; $i++) { // debug 100er
//			$data['eventname'] = "[".$i."]".$data['eventname'];

		$db						= JFactory::getDbo();

		$newEvent				= new stdClass();

		$newEvent->owneruuid	= $osuid;
		$newEvent->name			= $data['eventname'];
		$newEvent->creatoruuid	= $osuid;
		$newEvent->category		= $data['eventcategory'];
		$newEvent->description	= $data['description'];
		$newEvent->dateUTC		= $utctimestamp;
		$newEvent->duration		= $data['eventduration'];
		$newEvent->covercharge	= $data['covercharge'];
		$newEvent->coveramount	= $data['covercharge'];
		$newEvent->simname		= $parceldata['regionName'];
		$newEvent->globalPos	= $parceldata['globalPosition'];
		$newEvent->parcelUUID	= $data['parceluuid'];
		$newEvent->parcelName	= $parceldata['parcelname'];
		$newEvent->landingpoint	= $parceldata['landingpoint'];
		$newEvent->eventflags	= $data['eventflags'];
		$newEvent->mature		= $mature;

		$result = $db->insertObject('#__opensim_search_events', $newEvent);

		if($result !== TRUE) {
			$retval['error'] |= 8; // Error inserting to database
		}

//		} // ende debug 100er

		return $retval;
	}

	public function deleteEvent($eventid) {
		$retval	= array();
		$osuid	= $this->opensimIsCreated();
		if(!$osuid) {
			$retval['error'] |= 4; // Error: user does not have any inworld account
		}

		if(array_key_exists("error",$retval) && $retval['error'] > 0) { // some error occured, lets go back and display messages
			return $retval;
		}

		// From here, nothing "should" go wrong anymore
		$query	= sprintf("DELETE FROM #__opensim_search_events WHERE eventid = '%1\$d' AND (owneruuid = '%2\$s' OR creatoruuid = '%2\$s')",$eventid,$osuid);
		$db		= JFactory::getDBO();
		$db->setQuery($query);
		$db->query();
		$retval['ok'] = 1;
		return $retval;
	}

	public function getDurations() {
		$retval = array(	 '10'	=> "10 ".JText::_('JOPENSIM_MINUTES'),
							 '15'	=> "15 ".JText::_('JOPENSIM_MINUTES'),
							 '20'	=> "20 ".JText::_('JOPENSIM_MINUTES'),
							 '25'	=> "25 ".JText::_('JOPENSIM_MINUTES'),
							 '30'	=> "30 ".JText::_('JOPENSIM_MINUTES'),
							 '45'	=> "45 ".JText::_('JOPENSIM_MINUTES'),
							 '60'	=> "1 ".JText::_('JOPENSIM_HOUR'),
							 '90'	=> "1.5 ".JText::_('JOPENSIM_HOURS'),
							'120'	=> "2 ".JText::_('JOPENSIM_HOURS'),
							'150'	=> "2.5 ".JText::_('JOPENSIM_HOURS'),
							'180'	=> "3 ".JText::_('JOPENSIM_HOURS'),
							'240'	=> "4 ".JText::_('JOPENSIM_HOURS'),
							'300'	=> "5 ".JText::_('JOPENSIM_HOURS'),
							'360'	=> "6 ".JText::_('JOPENSIM_HOURS'),
							'420'	=> "7 ".JText::_('JOPENSIM_HOURS'),
							'480'	=> "8 ".JText::_('JOPENSIM_HOURS'),
							'540'	=> "9 ".JText::_('JOPENSIM_HOURS'),
							'600'	=> "10 ".JText::_('JOPENSIM_HOURS'),
							'660'	=> "11 ".JText::_('JOPENSIM_HOURS'),
							'720'	=> "12 ".JText::_('JOPENSIM_HOURS')
						);
		return $retval;
	}

	public function getParcelInfo($parcelUUID) {
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(TRUE);
		$query
			->select($db->quoteName('#__opensim_search_allparcels.parcelname'))
			->select($db->quoteName('#__opensim_search_allparcels.regionName'))
			->select($db->quoteName('#__opensim_search_allparcels.regionUUID'))
			->select($db->quoteName('#__opensim_search_allparcels.landingpoint'))
			->from($db->quoteName('#__opensim_search_allparcels'))
			->where($db->quoteName('#__opensim_search_allparcels.parcelUUID')." = ".$db->quote($parcelUUID));
		$db->setQuery($query);
		$db->execute();
		$foundParcelRegion = $db->getNumRows();
		if($foundParcelRegion == 0) return FALSE;
		$parceldata = $db->loadAssoc();
		$parceldata['globalPosition'] = $this->getGlobalPosition($parcelUUID);
		return $parceldata;
	}

	public function getGlobalPosition($parcelUUID) {
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(TRUE);
		$query
			->select($db->quoteName('#__opensim_search_allparcels.regionUUID'))
			->select($db->quoteName('#__opensim_search_allparcels.landingpoint'))
			->from($db->quoteName('#__opensim_search_allparcels'))
			->where($db->quoteName('#__opensim_search_allparcels.parcelUUID')." = ".$db->quote($parcelUUID));
		$db->setQuery($query);
		$db->execute();
		$foundParcelRegion = $db->getNumRows();
		if($foundParcelRegion == 0) return FALSE;
		$parceldata = $db->loadAssoc();

		$query = $db->getQuery(TRUE);
		$query
			->select($db->quoteName('#__opensim_search_regions.locX'))
			->select($db->quoteName('#__opensim_search_regions.locY'))
			->from($db->quoteName('#__opensim_search_regions'))
			->where($db->quoteName('#__opensim_search_regions.regionuuid')." = ".$db->quote($parceldata['regionUUID']));
		$db->setQuery($query);
		$db->execute();
		$foundRegion = $db->getNumRows();
		if($foundRegion == 0) return FALSE;
		$regiondata = $db->loadAssoc();
		if($regiondata['locX'] == 0 || $regiondata['locY'] == 0) return FALSE;
		$parcellocation = explode("/",$parceldata['landingpoint']);
		if(!is_array($parcellocation) || count($parcellocation) != 3) return FALSE;
		$globalposition = "<".($regiondata['locX'] + round($parcellocation[0])).",".($regiondata['locY'] + round($parcellocation[1])).",".round($parcellocation[2]).">";
		return $globalposition;
	}

}
?>
