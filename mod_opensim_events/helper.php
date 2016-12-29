<?php
/**
 * @module OpenSim Events
 * @copyright Copyright (C) 2015 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
**/

// no direct access
defined('_JEXEC') or die('Direct Access to this location is not allowed.');
 
class ModOpenSimEventsHelper {

	public	$numberevents;
	public	$eventsinadvance;
	public	$eventsinadvanceseconds;
	public	$showeventinprogress;
	public	$progressColor;
	public	$guesttimezone;
	public	$usertimezone;
	public	$showmature;
	public	$timeoffset;
	public	$utcstamp;
	public	$usertimeoffset;
	public	$usertime;
	public	$now;
	public	$dateformat;
	public	$timeformat;

	public function __construct($params)
    {
		$this->numberevents			= $params->get('numberevents');
		$this->eventsinadvance		= $params->get('eventsinadvance');

		$this->showeventinprogress	= $params->get('showeventinprogress');
		$this->progressColor		= $params->get('progressColor');
		$this->guesttimezone		= $params->get('guesttimezone');
		$this->usertimezone			= $params->get('guesttimezone');
		$this->showmature			= $params->get('showmature');
		$this->showdesc				= $params->get('showdesc');
		$this->truncatedesc			= $params->get('truncatedesc');
		$this->dateformat			= $params->get('eventdateformat');
		$this->timeformat			= $params->get('eventtimeformat');
		$this->timeoffset			= 0;
		$this->usertimeoffset		= 0;
		$this->now					= time();

		$timestamp			        = time();
		$timeCompound		        = date("Y-m-d H:i:s",$timestamp);
		$dateTimeZone		        = new DateTimeZone(date_default_timezone_get());
		$this->dTZ_utc = $dateTimeZoneUTC	= new DateTimeZone('UTC');
	
		$this->dateobject           = $dateTimeUTC = new DateTime($timeCompound);
		$this->timeoffset	        = $dateTimeZone->getOffset($dateTimeUTC);
		$this->utcstamp		        = time() - $this->timeoffset;

		switch($this->eventsinadvance)
        {
			case "24hours":
				$dateinterval = null;
				$this->eventsinadvanceseconds = time() + (60*60*24) - $this->timeoffset;
			break;
			case "7days":
				$dateinterval = null;
				$this->eventsinadvanceseconds = time() + (60*60*24*7) - $this->timeoffset;
			break;
			case "30days":
				$dateinterval = null;
				$this->eventsinadvanceseconds = time() + (60*60*24*30) - $this->timeoffset;
			break;
			case "3months":
				$dateinterval = new DateInterval('P3M');
			break;
			case "6months":
				$dateinterval = new DateInterval('P6M');
			break;
			case "12months":
				$dateinterval = new DateInterval('P12M');
			break;
			case "all":
				$dateinterval = null;
				$this->eventsinadvanceseconds = -1;
			break;
		}

		if (is_object($dateinterval))
        {
			$dateTimeUTC->add($dateinterval);
			$this->eventsinadvanceseconds = $dateTimeUTC->format("U") - $this->timeoffset;
		}
		$this->eventsinadvancedate = date("d.m.Y H:i:s",$this->eventsinadvanceseconds);
	}

	public function getEventList()
    {
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);

		$query->select("IF(".$db->quoteName('#__opensim_search_events.dateUTC')." < ".$this->utcstamp.",1,0) AS inprogress");
		$query->select('#__opensim_search_events.*');
		$query->from($db->quoteName('#__opensim_search_events'));
		if($this->eventsinadvanceseconds == -1) { // show all
			$query->where($db->quoteName('#__opensim_search_events.dateUTC')." > ".$this->utcstamp);
		} else {
			$query->where("(".$db->quoteName('#__opensim_search_events.dateUTC')." > ".$this->utcstamp." AND ".$db->quoteName('#__opensim_search_events.dateUTC')." < ".$this->eventsinadvanceseconds.")","OR");
		}
		if($this->showeventinprogress) {
			$query->where("(".$db->quoteName('#__opensim_search_events.dateUTC')." < ".$this->utcstamp." AND (".$db->quoteName('#__opensim_search_events.dateUTC')." + (".$db->quoteName('#__opensim_search_events.duration')." * 60) > ".$this->utcstamp."))");
		}
		if($this->numberevents > 0) {
			$query->setLimit($this->numberevents);
		}
		$query->order($db->quoteName('#__opensim_search_events.dateUTC'));
		$db->setQuery($query);
		$db->execute();
//		$this->events = $db->loadObjectList();
		$this->events = $db->loadAssocList();
		if(count($this->events) > 0) {
			foreach($this->events AS $key => $event) {
				$this->events[$key]['dateUTC']			= $this->events[$key]['dateUTC'] * 1;
				$this->events[$key]['utcDate']			= date("d.m.Y H:i:s",$event['dateUTC']);
				$this->events[$key]['test']				= (int)$event['dateUTC'] + $this->usertimeoffset;
				$this->events[$key]['usertimeoffset']	= $this->usertimeoffset;
				$this->events[$key]['userDate']			= date($this->dateformat,($event['dateUTC'] + $this->usertimeoffset));
				$this->events[$key]['userTime']			= date($this->timeformat,($event['dateUTC'] + $this->usertimeoffset));
				$this->events[$key]['categoryname']		= $this->getEventCategory($event['category']);
				$this->events[$key]['hostedby']			= $this->getUserName($event['owneruuid']);
			}
		}
		return $this->events;
	}

	public function getUserName($uuid) {
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);

		$query->select($db->quoteName('#__opensim_clientinfo.userName'));
		$query->from($db->quoteName('#__opensim_clientinfo'));
		$query->where($db->quoteName('#__opensim_clientinfo.PrincipalID')." = ".$db->quote($uuid));

		$db->setQuery($query);
		$db->execute();
		if($db->getNumRows() == 1) {
			return $db->loadResult();
		} else {
			return JText::_('MOD_OPENSIM_EVENTS_UNKNOWNUSER');
		}
	}

	public function getEventCategory($cat = null) {
		$category = array(
            '27' => JText::_('MOD_OPENSIM_EVENTS_CATEGORY_ART'),
			'28' => JText::_('MOD_OPENSIM_EVENTS_CATEGORY_CHARITY'),
			'22' => JText::_('MOD_OPENSIM_EVENTS_CATEGORY_COMMERCIAL'),
			'18' => JText::_('MOD_OPENSIM_EVENTS_CATEGORY_DISCUSSION'),
			'26' => JText::_('MOD_OPENSIM_EVENTS_CATEGORY_EDUCATION'),
			'24' => JText::_('MOD_OPENSIM_EVENTS_CATEGORY_GAMES'),
			'20' => JText::_('MOD_OPENSIM_EVENTS_CATEGORY_MUSIC'),
			'29' => JText::_('MOD_OPENSIM_EVENTS_CATEGORY_MISC'),
			'23' => JText::_('MOD_OPENSIM_EVENTS_CATEGORY_NIGHTLIFE'),
			'25' => JText::_('MOD_OPENSIM_EVENTS_CATEGORY_PAGEANT'),
			'19' => JText::_('MOD_OPENSIM_EVENTS_CATEGORY_SPORT')
		);
		
        if (!$cat)
        {
            return $category;
		}

        if (array_key_exists($cat,$category))
        {
            return $category[$cat];
		}
        
        else
        {
			return FALSE;
		}
	}

	public function getUserTime($userid) {
		$db =& JFactory::getDBO();
		$query = sprintf("SELECT #__opensim_usersettings.* FROM #__opensim_userrelation LEFT JOIN #__opensim_usersettings ON #__opensim_userrelation.opensimID = #__opensim_usersettings.uuid WHERE #__opensim_userrelation.joomlaID = '%d'",$userid);
		$db->setQuery($query);
		$db->execute();
		$num_rows = $db->getNumRows();
		if($num_rows == 1) {
			$usersettings = $db->loadAssocList();
			if($usersettings[0]['timezone']) {
				$this->usertimezone = $usersettings[0]['timezone'];
			} else {
				$this->usertimezone = $this->guesttimezone;
			}
		} else {
			$this->usertimezone = $this->guesttimezone;
		}

		$timestamp				= time();
		$timeCompound			= date("Y-m-d H:i:s",$timestamp);
		$dateTimeZone			= new DateTimeZone($this->usertimezone);
		$dateTimeZoneUTC		= new DateTimeZone('UTC');
		$dateTimeUTC			= new DateTime($timeCompound, $dateTimeZoneUTC);
		$this->usertimeoffset   = $dateTimeZone->getOffset($dateTimeUTC);
		$this->utctime			= date("d.m.Y H:i:s",(time() - $this->timeoffset));
		$this->usertime			= date("d.m.Y H:i:s",(time() - $this->timeoffset + $this->usertimeoffset));

		return $this->usertimezone;
	}

	public function getDurations()
    {
		$retval = array(
            '10'	=> "10 ".JText::_('MOD_OPENSIM_EVENTS_MINUTES'),
			'15'	=> "15 ".JText::_('MOD_OPENSIM_EVENTS_MINUTES'),
			'20'	=> "20 ".JText::_('MOD_OPENSIM_EVENTS_MINUTES'),
			'25'	=> "25 ".JText::_('MOD_OPENSIM_EVENTS_MINUTES'),
			'30'	=> "30 ".JText::_('MOD_OPENSIM_EVENTS_MINUTES'),
			'45'	=> "45 ".JText::_('MOD_OPENSIM_EVENTS_MINUTES'),
			'60'	=> "1 ".JText::_('MOD_OPENSIM_EVENTS_HOUR'),
			'90'    => "1.5 ".JText::_('MOD_OPENSIM_EVENTS_HOURS'),
			'120'   => "2 ".JText::_('MOD_OPENSIM_EVENTS_HOURS'),
			'150'	=> "2.5 ".JText::_('MOD_OPENSIM_EVENTS_HOURS'),
			'180'	=> "3 ".JText::_('MOD_OPENSIM_EVENTS_HOURS'),
			'240'	=> "4 ".JText::_('MOD_OPENSIM_EVENTS_HOURS'),
			'300'	=> "5 ".JText::_('MOD_OPENSIM_EVENTS_HOURS'),
			'360'	=> "6 ".JText::_('MOD_OPENSIM_EVENTS_HOURS'),
			'420'	=> "7 ".JText::_('MOD_OPENSIM_EVENTS_HOURS'),
			'480'	=> "8 ".JText::_('MOD_OPENSIM_EVENTS_HOURS'),
			'540'	=> "9 ".JText::_('MOD_OPENSIM_EVENTS_HOURS'),
			'600'	=> "10 ".JText::_('MOD_OPENSIM_EVENTS_HOURS'),
			'660'	=> "11 ".JText::_('MOD_OPENSIM_EVENTS_HOURS'),
			'720'	=> "12 ".JText::_('MOD_OPENSIM_EVENTS_HOURS'),
			'20160'	=> "2 weeks"
		);
		return $retval;
	}

	public function getEventIcons()
    {
		$basepath   = JURI::base( true ).'/modules/mod_opensim_events/assets/';
		$icon[0]	= '<span class="label label-default">PG</span>';    // $basepath."icon_event.png";
		$icon[1]	= '<span class="label label-info">M</span>';        // $basepath."icon_event_mature.png";
		$icon[2]	= '<span class="label label-danger">A</span>';      // $basepath."icon_event_adult.png";
		return $icon;
	}
} //end ModOpenSimEventsHelper
?>
