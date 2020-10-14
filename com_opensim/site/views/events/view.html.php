<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');
jimport( 'joomla.html.parameter' );

class opensimViewevents extends JViewLegacy {

	public function display($tpl = null) {
		$this->assetpath		= JUri::base(true)."/components/com_opensim/assets/";
		$doc					= JFactory::getDocument();
		$doc->addStyleSheet($this->assetpath.'opensim.css');
		$doc->addStyleSheet($this->assetpath.'opensim.override.css');
		$params					= JComponentHelper::getParams('com_opensim');
		$app					= JFactory::getApplication();

		$layout					= $app->input->get('layout');
		$this->layout			= $layout;

		$model					= $this->getModel('events');
		$setting				= $model->getSettingsData();
		$opensim				= $model->opensim;

		$this->Itemid			= $app->input->get('Itemid');
		$task					= $app->input->get('task');

		$eventparams			= JComponentHelper::getParams('com_opensim');

//		$menu		= JSite::getMenu();
		$menu					= $app->getMenu();
		$active					= $menu->getActive($this->Itemid);

		if (is_object($active)) {
			$access_level		= $eventparams->get('events_post_access');
			$this->listmatureevents = $eventparams->get('listmatureevents');
			$group_access		= $eventparams->get('events_grouppower');
			$pageclass_sfx		= $eventparams->get('pageclass_sfx');
			if($access_level < 3 && is_array($group_access) && count($group_access) > 0) {
				$groupflag		= $model->getGroupAccessFlag($group_access);
			} else {
				$groupflag		= null;
			}
		} else {
			$access_level		= null;
			$group_access		= null;
			$pageclass_sfx		= "";
		}
		$this->pageclass_sfx	= $pageclass_sfx;


		$duration				= $model->getDurations();
		$this->duration			= $duration;

		$user					= JFactory::getUser();
		$created				= $model->opensimIsCreated(); // check if this user has already a related OpenSim account
		$osdata					= $model->getUserData($created);
		$osdata['access_level']	= $access_level;
		$formaction				= "";
		$eventdata				= array();

		if(!array_key_exists("timezone",$osdata) || !$osdata['timezone']) $osdata['timezone'] = $setting['eventtimedefault'];

		switch($layout) {
			case "submitevent":
		
				$osdata['eventparams']		= $eventparams;
				$osdata['created']			= $created;
				$osdata['groupflag']		= $groupflag;
				$osdata['publicRegions']	= $model->opensim->getPublicRegions();
				$ownerLand					= $model->getOwnerLand($created); // check the landowner access for this user
				$osdata['ownerLand']		= $ownerLand;
				$landGroups					= $model->getGroupLand($created,$groupflag); // check if the user is member of any group with land (=event hosting) access
				$osdata['groupLand']		= $landGroups;
				$publicLand					= $model->getPublicLand(); // Public regions do have '1' in the column `public` of the table #__opensim_mapinfo
				$osdata['publicLand']		= $publicLand;
		
				$landoption					= array();
				$landselected				= 0;
				$preselectedLand			= $app->input->get('eventlocation');
		
				switch($task) {
					case "inserterror":
						$session = JFactory::getSession();

						$eventdata['name']			= $session->get('jopensim_error_eventname');
						$eventdata['eventdate']		= $session->get('jopensim_error_eventdate');
						$eventdata['eventtime']		= $session->get('jopensim_error_eventtime');
						$osdata['timezone']			= $session->get('jopensim_error_eventtimezone');
						$eventdata['duration']		= $session->get('jopensim_error_eventduration');
						$preselectedLand			= $session->get('jopensim_error_eventlocation');
						$eventdata['category']		= $session->get('jopensim_error_eventcategory');
						$eventdata['covercharge']	= $session->get('jopensim_error_covercharge');
						$eventdata['description']	= $session->get('jopensim_error_description');
						$eventdata['eventflags']	= $session->get('jopensim_error_eventflags');

						$session->clear('jopensim_error_eventname');
						$session->clear('jopensim_error_eventdate');
						$session->clear('jopensim_error_eventtime');
						$session->clear('jopensim_error_eventtimezone');
						$session->clear('jopensim_error_eventduration');
						$session->clear('jopensim_error_eventlocation');
						$session->clear('jopensim_error_eventcategory');
						$session->clear('jopensim_error_covercharge');
						$session->clear('jopensim_error_description');
						$session->clear('jopensim_error_eventflags');

						$eventdata['mature']		= FALSE;
						$this->formtitle = JText::_('JOPENSIM_EVENT_CREATE');
						$formaction					= "insertevent";
					break;
					default:
						// calculate the next full hour for the users timezone
						$timestamp				= time();
						$timeCompound			= date("Y-m-d H:i:s");
						$dateTimeZone			= new DateTimeZone($osdata['timezone']);
						$dateTimeZoneUTC		= new DateTimeZone('UTC');
						$dateTime				= new DateTime($timeCompound, $dateTimeZone);
						$dateTimeUTC			= new DateTime($timeCompound, $dateTimeZoneUTC);
						$timestampUTC			= $dateTimeUTC->format("U");
						$differenceServerUTC	= $timestamp - $timestampUTC;
						$timeOffset				= $dateTimeZone->getOffset($dateTimeUTC);
						$utcDifference			= $timestamp + $differenceServerUTC + $timeOffset;
		
						$nexthour	= date("H",$utcDifference)+1;
						$nexthour	= str_pad($nexthour,2,"0",STR_PAD_LEFT).":00";
						$eventdata['name']			= "";
						$eventdata['eventdate']		= date("d/m/Y");
						$eventdata['eventtime']		= $nexthour;
						$eventdata['duration']		= 0;
						$eventdata['category']		= '29';
						$eventdata['covercharge']	= 0;
						$eventdata['description']	= "";
						$eventdata['eventflags']	= 0;
						$eventdata['mature']		= FALSE;
						$this->formtitle = JText::_('JOPENSIM_EVENT_CREATE');
						$formaction					= "insertevent";
					break;
				}

				if($access_level <= 1) {
					if(is_array($publicLand) && count($publicLand) > 0) {
						$landoption[] = "<option value='?' disabled='disabled'>".JText::_('EVENTLANDPUBLIC')."</option>\n";
						foreach($publicLand AS $land) {
							if($landselected == 0 && $preselectedLand == $land['parcelUUID']) $selected = " selected='selected'";
							else $selected = "";
							$landoption[] = "<option value='".$land['parcelUUID']."'".$selected.">".$land['parcelname']."</option>\n";
						}
					}
				}
		
				if($access_level <= 2) {
					if(is_array($landGroups) && count($landGroups) > 0) {
						$landoption[] = "<option value='?' disabled='disabled'>".JText::_('EVENTLANDGROUP')."</option>\n";
						foreach($landGroups AS $land) {
							if($landselected == 0 && $preselectedLand == $land['parcelUUID']) $selected = " selected='selected'";
							else $selected = "";
							$landoption[] = "<option value='".$land['parcelUUID']."'".$selected.">".$land['parcelname']."</option>\n";
						}
					}
				}
		
				if($access_level <= 3) {
					if(is_array($ownerLand) && count($ownerLand) > 0) {
						$landoption[] = "<option value='?' disabled='disabled'>".JText::_('EVENTLANDOWNER')."</option>\n";
						foreach($ownerLand AS $land) {
							if($landselected == 0 && $preselectedLand == $land['parcelUUID']) $selected = " selected='selected'";
							else $selected = "";
							$landoption[] = "<option value='".$land['parcelUUID']."'".$selected.">".$land['parcelname']."</option>\n";
						}
					}
				}
		
				$this->landoption		= $landoption;
		
				$this->timezones		= DateTimeZone::listIdentifiers();
				if(!array_key_exists('eventtimedefault',$setting) || !$setting['eventtimedefault']) $setting['eventtimedefault'] = "UTC";
				$this->currencyenabled	= $setting['addons_currency'];
				$this->eventcategories	= $model->getEventCategory();
		
			break;
			case "eventlist":
				$param['uuid']		= $created;
				$eventlist			= $model->getEventList($param);
				$this->ossettings	= $setting;
				$this->eventlist	= $eventlist;
				$this->usertimezone	= $osdata['timezone'];
				
			break;
		}
		$this->created		= $created;
		$this->osdata		= $osdata;
		$this->formaction	= $formaction;
		$this->eventdata	= $eventdata;
		parent::display($tpl);
	}
}
?>
