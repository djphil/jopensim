<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die();
//jimport('joomla.application.component.modellist');
//jimport('joomla.application.component.model');
jimport('joomla.application.component.modeladmin');

class OpenSimModelOpenSim extends JModelAdmin {
	public $opensim;
	public $params;
	public $_os_db;
	public $_osgrid_db;
	public $os_user;
	public $moneyEnabled		= FALSE;
	public $userquery;
	public $_settingsData		= array();
	public $_moneySettingsData	= array();
	public $_total				= 0;
	public $_limit				= 20;
	public $_limitstart 		= 0;

	public function __construct($config = array()) {
		parent::__construct($config);
		$params			= JComponentHelper::getParams('com_opensim');
		$this->params	= $params;
		$osgriddbhost	= $params->get('opensimgrid_dbhost');
		$osgriddbuser	= $params->get('opensimgrid_dbuser');
		$osgriddbpasswd	= $params->get('opensimgrid_dbpasswd');
		$osgriddbname	= $params->get('opensimgrid_dbname');
		$osgriddbport	= $params->get('opensimgrid_dbport');
		$this->getSettingsData();
		$this->opensim		= new opensim($osgriddbhost,$osgriddbuser,$osgriddbpasswd,$osgriddbname,$osgriddbport);
		$this->userquery	= $this->opensim->getUserQuery(null);
		$this->_osgrid_db	= $this->getOpenSimGridDB();

		if($this->_settingsData['addons_currency'] == 1) $this->moneyEnabled = TRUE;
	}

	public function getSettingsData() {
		// Lets load the data if it doesn't already exist
		if (empty( $this->_settingsData )) {
			$settings = array();

			$params								= JComponentHelper::getParams('com_opensim');
			$this->params						= $params;

			$settings['opensim_host']			= $params->get('opensim_host');
			$settings['robust_port']			= $params->get('robust_port');
			$settings['getTextureEnabled']		= $params->get('getTextureEnabled');
			$settings['getTextureFormat']		= $params->get('getTextureFormat','png');

			$settings['oshost']					= $params->get('opensim_host');
			$settings['osport']					= $params->get('opensim_port');

			$settings['osdbhost']				= $params->get('opensimgrid_dbhost');
			$settings['osdbuser']				= $params->get('opensimgrid_dbuser');
			$settings['osdbpasswd']				= $params->get('opensimgrid_dbpasswd');
			$settings['osdbname']				= $params->get('opensimgrid_dbname');
			$settings['osdbport']				= $params->get('opensimgrid_dbport',3306);

			$settings['loginscreen_layout']		= $params->get('loginscreen_layout','classic');

			$settings['enableremoteadmin']		= $params->get('enableremoteadmin');
			$settings['remotehost']				= $params->get('remotehost');
			$settings['remoteport']				= $params->get('remoteport');
			$settings['remotepasswd']			= $params->get('remotepasswd');
			$settings['remoteadminsystem']		= $params->get('remoteadminsystem','single');

			$settings['addons_messages']		= $params->get('addons_messages');
			$settings['addons_profile']			= $params->get('addons_profile');
			$settings['addons_groups']			= $params->get('addons_groups');
			$settings['addons_search']			= $params->get('addons_search');
			$settings['addons_inworldauth']		= $params->get('addons_inworldauth');
			$settings['addons_terminalchannel']	= $params->get('addons_terminalchannel');
			$settings['addons_identminutes']	= $params->get('addons_identminutes');
			$settings['addons_currency']		= $params->get('addons_currency');
			$settings['addons_authorize']		= $params->get('addons_authorize');
			$settings['addons_authorizehg']		= $params->get('addons_authorizehg');
			$settings['addons']					= $settings['addons_messages'] + ($settings['addons_profile']*2) + ($settings['addons_groups']*4) + ($settings['addons_inworldauth']*8) + ($settings['addons_search']*16) + ($settings['addons_currency']*32) + ($settings['addons_authorize']*64);

			$settings['auth_minage']			= $params->get('auth_minage');
			$settings['auth_link']				= $params->get('auth_link');

			$settings['jopensim_userhome_region']		= $params->get('jopensim_userhome_region');
			$settings['jopensim_userhome_x']			= $params->get('jopensim_userhome_x');
			$settings['jopensim_userhome_y']			= $params->get('jopensim_userhome_y');
			$settings['jopensim_userhome_z']			= $params->get('jopensim_userhome_z');
			$settings['jopensim_defaultuserlevel']		= $params->get('jopensim_defaultuserlevel');
			$settings['jopensim_usersetting_flag3']		= $params->get('jopensim_usersetting_flag3');
			$settings['jopensim_usersetting_flag4']		= $params->get('jopensim_usersetting_flag4');
			$settings['jopensim_usersetting_flag5']		= $params->get('jopensim_usersetting_flag5');
			$settings['jopensim_usersetting_flag6']		= $params->get('jopensim_usersetting_flag6');
			$settings['jopensim_defaultusertype']		= $params->get('jopensim_defaultusertype');
			$settings['jopensim_usersetting_flags']		= $settings['jopensim_usersetting_flag3'] +
														  $settings['jopensim_usersetting_flag4'] +
														  $settings['jopensim_usersetting_flag5'] +
														  $settings['jopensim_usersetting_flag6'] +
														  $settings['jopensim_defaultusertype'];
			$settings['jopensim_usersetting_title']		= $params->get('jopensim_usersetting_title');

			$settings['jopensim_maps_cacheage']			= $params->get('jopensim_maps_cacheage',0);
			$settings['jopensim_maps_width']			= $params->get('jopensim_maps_width',600);
			$settings['jopensim_maps_width_style']		= $params->get('jopensim_maps_width_style','px');
			$settings['jopensim_maps_height']			= $params->get('jopensim_maps_height',400);
			$settings['jopensim_maps_height_style']		= $params->get('jopensim_maps_height_style','px');
			$settings['jopensim_maps_homename']			= $params->get('jopensim_maps_homename',JText::_('JOPENSIM_MAPS_DEFAULTNAME'));
			$settings['jopensim_maps_copyright']		= $params->get('jopensim_maps_copyright');
			$settings['jopensim_maps_homex']			= $params->get('jopensim_maps_homex',1000);
			$settings['jopensim_maps_homey']			= $params->get('jopensim_maps_homey',1000);
			$settings['jopensim_maps_offsetx']			= $params->get('jopensim_maps_offsetx',0);
			$settings['jopensim_maps_offsety']			= $params->get('jopensim_maps_offsety',0);
			$settings['jopensim_maps_zoomstart']		= $params->get('jopensim_maps_zoomstart',8);
			$settings['jopensim_maps_bubble_bgcolor']	= $params->get('jopensim_maps_bubble_bgcolor','#000000');
			$settings['jopensim_maps_bubble_alpha']		= $params->get('jopensim_maps_bubble_alpha','50');
			$settings['jopensim_maps_bubble_textcolor']	= $params->get('jopensim_maps_bubble_textcolor','#ffffff');
			$settings['jopensim_maps_bubble_linkcolor']	= $params->get('jopensim_maps_bubble_linkcolor','#ffffff');
			$settings['jopensim_maps_bubble_color']		= $settings['jopensim_maps_bubble_bgcolor'];
			$settings['jopensim_maps_showteleport']		= $params->get('jopensim_maps_showteleport',1);
			$settings['jopensim_maps_showcoords']		= $params->get('jopensim_maps_showcoords',0);
			$settings['jopensim_maps_link2article']		= $params->get('jopensim_maps_link2article',1);
			$settings['jopensim_maps_link2article_icon']= $params->get('jopensim_maps_link2article_icon',1);
			$settings['jopensim_maps_water']			= $params->get('jopensim_maps_water');
			if(!$settings['jopensim_maps_water']) {
				$settings['jopensim_maps_water']		= JUri::base(true)."/components/com_opensim/assets/images/water.jpg";
				$settings['jopensim_maps_displaytype']	= "auto";
				$settings['jopensim_maps_displayrepeat']= 1;
			} else {
				$settings['jopensim_maps_water']		= JUri::base(true)."/".$settings['jopensim_maps_water'];
				$settings['jopensim_maps_displaytype']	= $params->get('jopensim_maps_displaytype');
				$settings['jopensim_maps_displayrepeat']= $params->get('jopensim_maps_displayrepeat');
			}
			$settings['jopensim_maps_varregions']		= $params->get('jopensim_maps_varregions');
			$settings['jopensim_maps_visibility']		= $params->get('jopensim_maps_visibility',1);

			$settings['profile_display']				= $params->get('profile_display');
			$settings['profile_images']					= $params->get('profile_images');
			$settings['profile_images_maxwidth']		= $params->get('profile_images_maxwidth',512);
			$settings['profile_images_maxheight']		= $params->get('profile_images_maxheight',512);

			$settings['jopensimmoney_currencyname']				= $params->get('jopensimmoney_currencyname');
			$settings['jopensimmoneybanker']					= $params->get('jopensimmoneybanker');
			$settings['jopensimmoney_bankername']				= $params->get('jopensimmoney_bankername');
			$settings['jopensimmoney_startbalance']				= $params->get('jopensimmoney_startbalance',0);
			$settings['jopensimmoney_upload']					= $params->get('jopensimmoney_upload',0);
			$settings['jopensimmoney_groupcreation']			= $params->get('jopensimmoney_groupcreation',0);
			$settings['jopensimmoney_groupdividend']			= $params->get('jopensimmoney_groupdividend',0);
			$settings['jopensimmoney_zerolines']				= $params->get('jopensimmoney_zerolines',3);
			$settings['jopensimmoney_buycurrency']				= $params->get('jopensimmoney_buycurrency',0);
			$settings['jopensimmoney_buycurrency_url']			= $params->get('jopensimmoney_buycurrency_url',0);
			$settings['jopensimmoney_buycurrency_customized']	= $params->get('jopensimmoney_buycurrency_customized',0);
			$settings['jopensimmoney_buycurrency_custom_url']	= $params->get('jopensimmoney_buycurrency_custom_url',JURI::root());
			$settings['jopensimmoney_buycurrency_custom_msg']	= $params->get('jopensimmoney_buycurrency_custom_msg',JText::_('JOPENSIM_MONEY_BUYCURRENCY_MSG'));
			$settings['jopensimmoney_sendgridbalancewarning']	= $params->get('jopensimmoney_sendgridbalancewarning',0);
			$settings['jopensimmoney_warningrecipient']			= $params->get('jopensimmoney_warningrecipient','');
			$settings['jopensimmoney_warningsubject']			= $params->get('jopensimmoney_warningsubject','Grid Balance Warning');

			$settings['groupMinDividend']						= $params->get('jopensimmoney_groupdividend',0);

			$settings['search_objects']					= $params->get('search_objects');
			$settings['search_parcels']					= $params->get('search_parcels');
			$settings['search_parcelsales']				= $params->get('search_parcelsales');
//			$settings['search_popular']					= $params->get('search_popular');
			$settings['search_events']					= $params->get('search_events');
			$settings['search_classified']				= $params->get('search_classified');
			$settings['search_regions']					= $params->get('search_regions');
			$settings['events_post_access']				= $params->get('events_post_access');
			$settings['events_grouppower']				= $params->get('events_grouppower');
			$settings['eventtimedefault']				= $params->get('eventtimedefault');
			$settings['listmatureevents']				= $params->get('listmatureevents');

			$settings['classified_hide']				= $params->get('classified_hide',604800);
			$settings['classified_sort']				= $params->get('classified_sort','creationdate');
			$settings['classified_order']				= $params->get('classified_order','DESC');
			$settings['classified_images']				= $params->get('classified_images');
			$settings['classified_images_maxwidth']		= $params->get('classified_images_maxwidth',512);
			$settings['classified_images_maxheight']	= $params->get('classified_images_maxheight',512);

			$settings['lastnametype']					= $params->get('lastnametype');
			$settings['lastnamelist']					= $params->get('lastnamelist');
			$settings['userchange_firstname']			= $params->get('userchange_firstname');
			$settings['userchange_lastname']			= $params->get('userchange_lastname');
			$settings['userchange_email']				= $params->get('userchange_email');
			$settings['userchange_password']			= $params->get('userchange_password');

			$settings['jopensim_debug_path']			= $params->get('jopensim_debug_path',JPATH_SITE."/components/com_opensim/");
			if(substr($settings['jopensim_debug_path'],-1) != "/") $settings['jopensim_debug_path'] = $settings['jopensim_debug_path']."/"; // ensure it ends with a slash
			$settings['jopensim_debug_reminder']		= $params->get('jopensim_debug_reminder');
			$settings['jopensim_debug_access']			= $params->get('jopensim_debug_access');
			$settings['jopensim_debug_input']			= $params->get('jopensim_debug_input');
			$settings['jopensim_debug_profile']			= $params->get('jopensim_debug_profile');
			$settings['jopensim_debug_groups']			= $params->get('jopensim_debug_groups');
			$settings['jopensim_debug_search']			= $params->get('jopensim_debug_search');
			$settings['jopensim_debug_messages']		= $params->get('jopensim_debug_messages');
			$settings['jopensim_debug_currency']		= $params->get('jopensim_debug_currency');
			$settings['jopensim_debug_other']			= $params->get('jopensim_debug_other');
			$settings['jopensim_debug_settings']		= $params->get('jopensim_debug_settings');

			$settings['grp_readkey']					= $params->get('grp_readkey');
			$settings['grp_writekey']					= $params->get('grp_writekey');


			$this->_settingsData = $settings;
		}
		return $this->_settingsData;
	}

	public function settings2json() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select($db->quoteName('#__extensions.params'));
		$query->from($db->quoteName('#__extensions'));
		$query->where($db->quoteName('#__extensions.type').' = '.$db->quote("component"));
		$query->where($db->quoteName('#__extensions.element').' = '.$db->quote("com_opensim"));

		$db->setQuery($query);
		$db->execute();
		$foundparams = $db->getNumRows();
		if($foundparams == 1) {
			$exportjson = $db->loadResult();
			return $exportjson;
		} else {
			return null;
		}
	}

	public function getVersion() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select($db->quoteName('#__extensions.manifest_cache'));
		$query->from($db->quoteName('#__extensions'));
		$query->where($db->quoteName('#__extensions.type').' = '.$db->quote("component"));
		$query->where($db->quoteName('#__extensions.element').' = '.$db->quote("com_opensim"));

		$db->setQuery($query);
		$db->execute();

		$foundparams = $db->getNumRows();
		if($foundparams == 1) {
			$componentjson = $db->loadResult();
			$componentInfo = json_decode($componentjson, true);
			return $componentInfo['version'];
		} else {
			return null;
		}
	}

	public function checkversion() {
		$versionfile	= "https://www.jopensim.com/opensim/version3.txt";
		$recentversion	= @file_get_contents($versionfile);
		if(!$recentversion) return JText::_('UPDATEINFONOTAVAILABLE');
		$versioncheck	= version_compare($this->getVersion(),trim($recentversion));
		if($versioncheck < 0) {
			return JText::sprintf('UPDATEVERSION',$recentversion);
		} elseif($versioncheck > 0) {
			return "<i class='icon-warning-circle' style='color:orange;'></i>PreRelease?";
		} else {
			return JText::_('UP2DATE');
		}
//		elseif(trim($recentversion) == self::$version) return JText::_('UP2DATE');
//		else return JText::sprintf('UPDATEVERSION',$recentversion);
	}

	public function checkupdate() {
		$jopensimxml		= @simplexml_load_file("https://update.jopensim.com/components/com_opensim.xml");
		if(!is_object($jopensimxml)) return JText::_('UPDATEINFONOTAVAILABLE');
		$jversion			= new JVersion();
		$joomlaversion		= $jversion->getShortVersion();
		$targetversion		= substr($joomlaversion,0,2);
		$jopensimversion	= "";
		$jopensimchangelog	= "";
		foreach($jopensimxml->update AS $update) {
			if($update->targetplatform['version'] == $targetversion) {
				$jopensimversion	= $update->version;
				$jopensimchangelog	= $update->changelog;
			}
		}
		if(!$jopensimversion) {
			return JText::_('UPDATEINFONOTAVAILABLE');
		} else {
			$versioncheck	= version_compare($this->getVersion(),trim($jopensimversion));
			if($versioncheck < 0) {
				return JText::sprintf('UPDATEVERSION',$jopensimversion)."<br />".JText::sprintf('UPDATECHANGELOG',$jopensimchangelog);
			} elseif($versioncheck > 0) {
				return "<i class='icon-warning-circle' style='color:orange;'></i>PreRelease?";
			} else {
				return JText::_('UP2DATE');
			}
		}
//		return $jopensimlist;
	}

	public function importsettingsfile() {
		$input			= JFactory::getApplication()->input;
		$file			= $input->files->get('settingsimport');
		$filename		= $file['tmp_name'];
		$paramstring	= file_get_contents($filename);

		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$fields	= array(
		    $db->quoteName('params') . ' = ' . $db->quote($paramstring)
		);
		$conditions = array(
			$db->quoteName('type') . ' = '.$db->quote('component'),
			$db->quoteName('element').' = '.$db->quote('com_opensim')
		);
		$query->update($db->quoteName('#__extensions'))->set($fields)->where($conditions);
		$db->setQuery($query);
		$result = $db->execute();
	}

	public function convert2rgba($color,$alpha) {
		$default = 'rgb(0,0,0)';
 
		//Return default if no color provided
		if(empty($color)) return $default;
 
		//Sanitize $color if "#" is provided
		if ($color[0] == '#' ) {
			$color = substr( $color, 1 );
		}

		//Check if color has 6 or 3 characters and get values
		if (strlen($color) == 6) {
				$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
		} elseif ( strlen( $color ) == 3 ) {
				$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
		} else {
				return $default;
		}
 
		//Convert hexadec to rgb
		$rgb =  array_map('hexdec', $hex);
 
		//Check if opacity is set(rgba or rgb)
		if($alpha){
			if(abs($alpha) > 1)
			$alpha = 1.0;
			$output = 'rgba('.implode(",",$rgb).','.$alpha.')';
		} else {
			$output = 'rgb('.implode(",",$rgb).')';
		}
 
		//Return rgb(a) color string
		return $output;
	}

	public function saveConfig($data) {
//		$this->debugprint($data);
		if(!is_array($data) || count($data) == 0) return FALSE;
		foreach($data AS $configname => $configvalue) {
			$this->saveConfigValue($configname,$configvalue);
		}
	}

	public function saveConfigValue($name,$value) {
		return;
	}

	public function getOpenSimDB() {
		return $this->opensim->_os_db;
	}

	public function getOpenSimGridDB() {
		return $this->opensim->_osgrid_db;
	}

	public function checkLastnameAllowed($lastname) {
		switch($this->_settingsData['lastnametype']) {
			case 0:
				return TRUE;
			break;
			case -1:
				if(in_array($lastname,$this->_settingsData['lastnames'])) return FALSE;
				else return TRUE;
			break;
			case 1:
				if(in_array($lastname,$this->_settingsData['lastnames'])) return TRUE;
				else return FALSE;
			break;
		}
	}

	public function cleanIdents() { // removes old inworld idents if enabled
		return; // temporary disabled
		$identminutes = $this->_settingsData['identminutes'];
		if($identminutes > 0) {
			$db = JFactory::getDBO();
			$query = sprintf("DELETE FROM #__opensim_inworldident WHERE created < DATE_SUB(NOW(), INTERVAL %d MINUTE)",$identminutes);
			$db->setQuery($query);
			$db->execute();
		}
	}

	public function getContentTitleFromId($id) {
		$db = JFactory::getDBO();
		$query = sprintf("SELECT title FROM #__content WHERE id = '%d'",$id);
		$db->setQuery($query);
		$contentTitle = $db->loadResult();
		if($contentTitle) return $contentTitle;
		else return JText::_('NONE');
	}

	public function getTerminalList($inactive = 0) {
//		return array(); // temporary disabled
		$db = JFactory::getDBO();
		$query = "SELECT
					#__opensim_terminals.*,
					CONCAT('secondlife://',region,'/',location_x,'/',location_y,'/',location_z) AS surl
				FROM
					#__opensim_terminals";
		if(!$inactive) $query .= " WHERE active = '1'";
		$db->setQuery($query);
		$terminalList = $db->loadAssocList();
		return $terminalList;
	}

	public function opensimIsCreated() { // returns the opensim UUID if exists already for the user or FALSE if not
		$user	= JFactory::getUser();
		return $this->opensimRelation($user->id);
	}

	public function opensimRelation($uuid) {
		$db		= JFactory::getDBO();
		$query	= sprintf("SELECT opensimID FROM #__opensim_userrelation WHERE joomlaID = '%d'",$uuid);
		$db->setQuery($query);
		$uuid = $db->loadResult();
		if(!$uuid) return FALSE;
		else return $uuid;
	}

	public function opensimRelationReverse($uuid) {
		$db		= JFactory::getDBO();
		$query	= sprintf("SELECT joomlaID FROM #__opensim_userrelation WHERE opensimID = '%s'",$uuid);
		$db->setQuery($query);
		$uuid = $db->loadResult();
		if(!$uuid) return FALSE;
		else return $uuid;
	}

	public function opensimCreated($uuid) { // returns TRUE or FALSE if $uuid exists in the OpenSim database
		if(!$this->_osgrid_db) return FALSE;
		$opensim = $this->opensim;
		$query = $opensim->getUserDataQuery($uuid);
		$this->_osgrid_db->setQuery($query['userdata']);
		$this->_osgrid_db->execute();
		$num_rows = $this->_osgrid_db->getNumRows();
		if($num_rows == 1) return TRUE;
		else return FALSE;
	}

	public function getUserData($userid) {
		if(!$this->_osgrid_db) return FALSE;
		$userdata = array();
		$griddata = array();
		$authdata = array();
		$opensim = $this->opensim;
		$query = $opensim->getUserDataQuery($userid);
		$this->_osgrid_db->setQuery($query['userdata']);
		$userdata = $this->_osgrid_db->loadAssoc();
		$this->_osgrid_db->setQuery($query['griddata']);
		$griddata = $this->_osgrid_db->loadAssoc();
		$this->_osgrid_db->setQuery($query['authdata']);
		$authdata = $this->_osgrid_db->loadAssoc();
		if(!is_array($griddata)) $griddata = $this->emptyGridData(); // in case no home region is defined and/or user never was online yet, give an empty array to prevent php warnings
		if(!is_array($userdata)) $userdata = $this->emptyUserData();
		if(!is_array($authdata)) $authdata = $this->emptyAuthData();
		$juserdata = $this->getJuserData($userid);
		$retval = array_merge($userdata,$griddata,$authdata,$juserdata);
		return $retval;
	}

	public function emptyUserData() {
		$retval = array();
		$retval['uuid']			= null;
		$retval['firstname']	= null;
		$retval['lastname']		= null;
		$retval['name']			= null;
		$retval['email']		= null;
		$retval['userlevel']	= null;
		$retval['userflags']	= null;
		$retval['usertitle']	= null;
		$retval['born']			= null;
		return $retval;
	}

	public function emptyGridData() {
		$retval = array();
		$retval['last_login']	= null;
		$retval['last_logout']	= null;
		return $retval;
	}

	public function emptyAuthData() {
		$retval = array();
		$retval['passwordSalt']	= null;
		return $retval;
	}

	public function getUserDataList() {
		$app = JFactory::getApplication();
		if(!$this->_osgrid_db) return FALSE;
		$opensim = $this->opensim;

		$limitstart	= $app->getUserStateFromRequest( 'users_limitstart', 'limitstart', 0, 'int' );
		$limit		= $app->getUserStateFromRequest( 'global.list.limit', 'limit', $app->getCfg('list_limit'), 'int' );
		$orderby	= $app->getUserStateFromRequest( 'users_filter_order', 'filter_order', 'UserAccounts.Created', 'STR' );
		$orderdir	= $app->getUserStateFromRequest( 'users_filter_order_Dir', 'filter_order_Dir', 'desc', 'STR' );
		$search		= $app->getUserStateFromRequest( 'users_filter_search', 'filter_search', '', 'STR' );

		$this->userquery	= $this->opensim->getUserQuery($search,$orderby,$orderdir);
		$this->UserQueryObject = $this->opensim->getUserQueryObject($search,$orderby,$orderdir);

		$userquery = $this->userquery." ORDER BY ".$orderby." ".$orderdir;

		$this->_osgrid_db->setQuery($userquery,$limitstart,$limit);

		try {
			$this->os_user = $this->_osgrid_db->loadAssocList();
		} catch(Exception $e) {
			$errormsg = $e->getMessage();
			JFactory::getApplication()->enqueueMessage($errormsg." (".$this->userquery.")","error");
			return array();
		}

		foreach($this->os_user AS $userkey => $user) {
			$statusquery = $opensim->userGridStatusQuery($user['userid']);
			$this->_osgrid_db->setQuery($statusquery);
			$userstatus = $this->_osgrid_db->loadAssoc();
			if(!is_array($userstatus)) {
				$userstatus['last_login'] = "";
				$userstatus['last_logout'] = "";
			}
			$userstatus['online'] = $opensim->getUserPresence($user['userid']);
			$this->os_user[$userkey] = array_merge($this->os_user[$userkey],$userstatus);
		}

		return $this->os_user;
	}

	public function updateOsPwd($newpassword,$osid) {
		if(empty($this->_osgrid_db)) $this->getOpenSimGridDB();
		if(!$this->_osgrid_db) return FALSE;
		$opensim = $this->opensim;
		$update = $opensim->getOsTableField('passwordHash');
		$osdata = $this->getUserData($osid);
		$passwordSalt = md5(time());
		$update['fieldvalue'] = md5(md5($newpassword).":".$passwordSalt);
		$update['osid'] = $osid;
		$this->updateValues($update);
		$update = $opensim->getOsTableField('passwordSalt');
		$update['fieldvalue'] = $passwordSalt;
		$update['osid'] = $osid;
		$this->updateValues($update);
	}

	public function updateOsField($fieldname,$fieldvalue,$osid) {
		if(empty($this->_osgrid_db)) $this->getOpenSimGridDB();
		if(!$this->_osgrid_db) return FALSE;
		$opensim = $this->opensim;
		$update = $opensim->getOsTableField($fieldname);
		$update['fieldvalue'] = $fieldvalue;
		$update['osid'] = $osid;
		$this->updateValues($update);
	}

	public function updateValues($data) {
		if(empty($this->_osgrid_db)) $this->getOpenSimGridDB();
		if(!$this->_osgrid_db) return FALSE;
		$query = sprintf("UPDATE %s SET %s = '%s' WHERE %s = '%s'",
							$data['table'],
							$data['field'],
							$data['fieldvalue'],
							$data['userid'],
							$data['osid']);
		$this->_osgrid_db->setQuery($query);
		$debug[] = $query;
		$result = $this->_osgrid_db->execute();
		if($data['field'] == "passwordHash") return $query;
		else return $result;
	}

	public function getJuserData($uuid) { // Collect settings from Joomlas DB
		$db = JFactory::getDBO();
		$query = sprintf("SELECT im2email,visible,timezone FROM  #__opensim_usersettings WHERE `uuid` = '%s'",$uuid);
		$db->setQuery($query);
		$db->execute();
		if($db->getNumRows() == 1) {
			$jUserData = $db->loadAssoc();
		} else {
			$jUserData = array( 'im2email'	=> 0,
								'visible'	=> 0,
								'timezone'	=> "");
		}
		return $jUserData;
	}

	public function getUserFriends($userid) {
		if(empty($this->_osgrid_db)) $this->getOpenSimGridDB();
		if(!$this->_osgrid_db) return FALSE;
		$opensim = $this->opensim;
		$query = $opensim->getUserDataQuery($userid);
		$this->_osgrid_db->setQuery($query['friends']);
		$friends = $this->_osgrid_db->loadAssocList();
		return $friends;
	}

	public function float_safe($value) {
		$larr = localeconv();
		$search = array(
			$larr['decimal_point'],
			$larr['mon_decimal_point'],
			$larr['thousands_sep'],
			$larr['mon_thousands_sep'],
			$larr['currency_symbol'],
			$larr['int_curr_symbol']
		);
		$replace = array('.', '.', '', '', '', '');

		return str_replace($search, $replace, $value);
	}

	public function getEventCategory($cat = null) {
		$category = array(	'27' => JText::_('JOPENSIM_EVENTCATEGORY_ART'),
							'28' => JText::_('JOPENSIM_EVENTCATEGORY_CHARITY'),
							'22' => JText::_('JOPENSIM_EVENTCATEGORY_COMMERCIAL'),
							'18' => JText::_('JOPENSIM_EVENTCATEGORY_DISCUSSION'),
							'26' => JText::_('JOPENSIM_EVENTCATEGORY_EDUCATION'),
							'24' => JText::_('JOPENSIM_EVENTCATEGORY_GAMES'),
							'20' => JText::_('JOPENSIM_EVENTCATEGORY_MUSIC'),
							'29' => JText::_('JOPENSIM_EVENTCATEGORY_MISC'),
							'23' => JText::_('JOPENSIM_EVENTCATEGORY_NIGHTLIFE'),
							'25' => JText::_('JOPENSIM_EVENTCATEGORY_PAGEANT'),
							'19' => JText::_('JOPENSIM_EVENTCATEGORY_SPORT')
						);
		if(!$cat) return $category;
		if(array_key_exists($cat,$category)) {
			return $category[$cat];
		} else {
			return FALSE;
		}
	}

	public function getRegionDetails_old($uuid) {
		if(empty($this->_regiondata)) $this->getData();
		if(is_array($this->_regiondata)) {
			foreach($this->_regiondata AS $region) {
				if($region['uuid'] == $uuid) return $region;
			}
			return array("not found",$this->_regiondata);
		} else {
			return FALSE;
		}
	}

	public function getRegionDetails($uuid) {
		$regiondata = $this->opensim->getRegionData($uuid);
		if(is_array($regiondata)) {
			$regiondata['posX']		= intval($regiondata['posX']);
			$regiondata['posY']		= intval($regiondata['posY']);
			$regiondata['maplink']	= str_replace("-","",$uuid);
			$ownerdata = $this->opensim->getUserData($regiondata['owner_uuid']);
			if(array_key_exists("firstname",$ownerdata) && array_key_exists("lastname",$ownerdata)) {
				$regiondata['ownername'] = $ownerdata['firstname']." ".$ownerdata['lastname'];
			} else {
				$regiondata['ownername'] = "n/a";
			}
			$mapinfo = $this->getMapInfo($uuid);
			$regiondata['articleId']	= $mapinfo['articleId'];
			$regiondata['articleTitle']	= $this->getContentTitleFromId($mapinfo['articleId']);
			$regiondata['hidemap']		= $mapinfo['hidemap'];
			return $regiondata;
		} else {
			return FALSE;
		}
	}

	public function getMapInfo($regionUUID) {
		if(is_array($regionUUID)) {
			$region = $regionUUID[0];
		} else {
			$region = $regionUUID;
		}
		$retval	= array();
		$query	= sprintf("SELECT #__opensim_mapinfo.* FROM #__opensim_mapinfo WHERE regionUUID = '%s'",$region);
		$db		= JFactory::getDBO();
		$db->setQuery($query);
		$db->execute();
		if($db->getNumRows() == 1) {
			$retval = $db->loadAssoc();
			if($retval['articleId'] && $retval['articleId'] > 0) $retval['articleTitle'] = $this->getContentTitleFromId($retval['articleId']);
			else $retval['articleTitle'] = "";
		} else {
			$retval['regionUUID']	= $region;
			$retval['articleId']	= null;
			$retval['articleTitle'] = "";
			$retval['hidemap']		= 0;
			$retval['public']		= 0;
			$retval['guide']		= 0;
		}
		return $retval;
	}

	public function createImageFolder() {
		$imagepath = JPATH_SITE.DIRECTORY_SEPARATOR.'images';
		if(!is_dir($imagepath) || !is_writeable($imagepath)) return FALSE;
		$jopensimpath = $imagepath.DIRECTORY_SEPARATOR.'jopensim';
		if(!is_dir($jopensimpath)) mkdir($jopensimpath);
		$regionpath = $jopensimpath.DIRECTORY_SEPARATOR.'regions';
		if(!is_dir($regionpath)) mkdir($regionpath);
		return TRUE;
	}

	public function checkCacheFolder($dest = "regions") {
		if($dest == "regions") $cachefolder = JPATH_SITE.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'jopensim'.DIRECTORY_SEPARATOR.'regions';
		else $cachefolder = JPATH_SITE.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'jopensim'.DIRECTORY_SEPARATOR.'regions'.DIRECTORY_SEPARATOR.'varregions';
		$retval['path'] = $cachefolder;
		if(is_dir($cachefolder)) {
			$retval['existing'] = TRUE;
			if(is_writable($cachefolder)) {
				$retval['writeable'] = TRUE;
			} else {
				$retval['writeable'] = FALSE;
			}
		} else {
			$retval['existing'] = FALSE;
		}
		return $retval;
	}

	public function mapCacheRefresh($regionUID) {
		$refresh = $this->mapNeedsRefresh($regionUID);
		if($refresh === TRUE) $this->refreshMap($regionUID);
	}

	public function mapNeedsRefresh($regionUID) {
		$chachefolder = $this->checkCacheFolder();
		if($chachefolder['existing'] == FALSE || $chachefolder['writeable'] == FALSE) return FALSE;
		$regiondata = $this->getRegionDetails($regionUID);
		$regionimage = $chachefolder['path'].DIRECTORY_SEPARATOR.$regiondata['uuid'].".jpg";
		if(!is_file($regionimage)) {
			return TRUE;
		} else {
			if($this->_settingsData['jopensim_maps_cacheage'] == 0) return FALSE;
			$cachetime = time() - (60*$this->_settingsData['jopensim_maps_cacheage']);
			if($cachetime > filemtime($regionimage)) return TRUE;
			else return FALSE;
		}
	}

	public function refreshMap($regionUID) {
		$chachefolder = $this->checkCacheFolder();
		if($chachefolder['existing'] == FALSE || $chachefolder['writeable'] == FALSE) return FALSE;
		$regiondata = $this->getRegionDetails($regionUID);

		$regionimage = $chachefolder['path'].DIRECTORY_SEPARATOR.$regiondata['uuid'].".jpg";
		$os_regionimage = str_replace("-","",$regiondata['uuid']);
		$source = $regiondata['serverURI']."index.php?method=regionImage".$os_regionimage;

		$mapdata = $this->getMapContent($source);
		if(array_key_exists("error",$mapdata)) { // some error occurred, lets copy an error image for it
			$this->maperrorimage($regionimage,$mapdata['error']);
			return FALSE;
		} elseif(array_key_exists("file_content",$mapdata) && $mapdata['file_content']) {
			$fh = fopen($regionimage,"w");
			fwrite($fh,$mapdata['file_content']);
			fclose($fh);
		}
		if($this->_settingsData['jopensim_maps_varregions'] == 1 && ($regiondata['sizeX'] > 256 || $regiondata['sizeY'] > 256)) { // we got a varregion here, lets get V2 maptiles
			$cachefolder = $this->checkCacheFolder("varregions");
			$varregionsfolder = $cachefolder['path'];
			if($chachefolder['existing'] == FALSE || $chachefolder['writeable'] == FALSE) return FALSE;
			$mapstartX	= $regiondata['locX'] / 256;
			$mapstartY	= $regiondata['locY'] / 256;
			$mapendX	= $mapstartX + ($regiondata['sizeX'] / 256);
			$mapendY	= $mapstartY + ($regiondata['sizeY'] / 256);
			for($x = $mapstartX; $x < $mapendX; $x++) {
				for($y = $mapstartY; $y < $mapendY; $y++) {
					$mapname = "map-1-".$x."-".$y."-objects.jpg";
					$regionimage = $varregionsfolder.DIRECTORY_SEPARATOR.$mapname;
					$source = $this->_settingsData['opensim_host'].":".$this->_settingsData['robust_port']."/".$mapname;
					$mapdata = $this->getMapContent($source);
					if(array_key_exists("error",$mapdata)) { // some error occurred, lets copy an error image for it
						$this->maperrorimage($regionimage,$mapdata['error']);
						return FALSE;
					} elseif(array_key_exists("file_content",$mapdata) && $mapdata['file_content']) {
						$fh = fopen($regionimage,"w");
						fwrite($fh,$mapdata['file_content']);
						fclose($fh);
					}
				}
			}
		}
		return TRUE;
	}

	public function getMapContent($source) { // gets image data from external server
		// lets check, what possibilities to read outside files is present
		$curl = extension_loaded('curl');
		$fopen = ini_get('allow_url_fopen');
		$retval['file_content'] = "";

		if(!$curl && !$fopen) { // there is no way to read from outside :( at least display an error image
			$retval['error'] = "impossible reading";
		} elseif($curl) {
			ob_start();
			$ch = curl_init($source);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_exec($ch);
			$response = curl_getinfo($ch);
			if($response['http_code'] == 200) {
				$retval['file_content'] = ob_get_contents();
				ob_end_clean();
			} else { // could not open the image with cURL - display error image
				ob_end_clean();
				$retval['error'] = "cURL error ".$response['http_code'];
			}
		} else {
			$fexists = $this->http_test_existance($source);
			if($fexists['status'] == 200) {
				$handle = @fopen($source,'r');
				if($handle) {
					while (!feof($handle)) {
						$retval['file_content'] .= fread($handle,1024);
					}
					fclose($handle);
				} else { // could not open the image with fopen - display error image
					$retval['error'] = "fopen error (unknown)";
				}
			} else {
				$retval['error'] = $source."\nfopen error (status: ".$fexists['status'].")";
			}
		}
		return $retval;
	}

	public function maperrorimage($filename,$errormessage = "") {
		if(!$errormessage) $errormessage = "unknown error";
		$noregionimage = JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR."assets".DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."noregion.png";
		$img = imagecreatefrompng($noregionimage);
		$textcolor = ImageColorAllocate ($img, 255, 255, 0);
		ImageString($img,1,20,200, $errormessage, $textcolor);
		imagejpeg($img,$filename);
		imagedestroy($img);
	}


	// Many thanks to Alexander Brock through http://aktuell.de.selfhtml.org/artikel/php/existenz/ for this very useful function :)
	public function http_test_existance($url,$timeout = 10) {
		$timeout = (int)round($timeout/2+0.00000000001);
		$return = array();

		$inf = parse_url($url);

		if(!isset($inf['scheme']) or $inf['scheme'] !== 'http') return array('status' => -1);
		if(!isset($inf['host'])) return array('status' => -2);
		$host = $inf['host'];

		if(!isset($inf['path'])) return array('status' => -3);
		$path = $inf['path'];
		if(isset($inf['query'])) $path .= '?'.$inf['query'];

		if(isset($inf['port'])) $port = $inf['port'];
		else $port = 80;

		$pointer = fsockopen($host, $port, $errno, $errstr, $timeout);
		if(!$pointer) return array('status' => -4, 'errstr' => $errstr, 'errno' => $errno);
		socket_set_timeout($pointer, $timeout);

		$head =
		  'HEAD '.$path.' HTTP/1.1'."\r\n".
		  'Host: '.$host."\r\n";

		if(isset($inf['user']))
			$head .= 'Authorization: Basic '.
			base64_encode($inf['user'].':'.(isset($inf['pass']) ? $inf['pass'] : ''))."\r\n";
		if(func_num_args() > 2) {
			for($i = 2; $i < func_num_args(); $i++) {
				$arg = func_get_arg($i);
				if(
					strpos($arg, ':') !== false and
					strpos($arg, "\r") === false and
					strpos($arg, "\n") === false
				) {
					$head .= $arg."\r\n";
				}
			}
		}
		else $head .=
			'User-Agent: Selflinkchecker 1.0 ('.$_SERVER['PHP_SELF'].')'."\r\n";

		$head .=
			'Connection: close'."\r\n"."\r\n";

		fputs($pointer, $head);

		$response = '';

		$status = socket_get_status($pointer);
		while(!$status['timed_out'] && !$status['eof']) {
			$response .= fgets($pointer);
			$status = socket_get_status($pointer);
		}
		fclose($pointer);
		if($status['timed_out']) {
			return array('status' => -5, '_request' => $head);
		}

		$res = str_replace("\r\n", "\n", $response);
		$res = str_replace("\r", "\n", $res);
		$res = str_replace("\t", ' ', $res);

		$ares = explode("\n", $res);
		$first_line = explode(' ', array_shift($ares), 3);

		$return['status'] = trim($first_line[1]);
		$return['reason'] = trim($first_line[2]);

		foreach($ares as $line) {
			$temp = explode(':', $line, 2);
			if(isset($temp[0]) and isset($temp[1])) {
				$return[strtolower(trim($temp[0]))] = trim($temp[1]);
			}
		}
		$return['_response'] = $response;
		$return['_request'] = $head;

		return $return;
	}

	public function profile_wantmask() {
		$wantmask['build']		=   1;
		$wantmask['explore']	=   2;
		$wantmask['meet']		=   4;
		$wantmask['behired']	=  64;
		$wantmask['group']		=   8;
		$wantmask['buy']		=  16;
		$wantmask['sell']		=  32;
		$wantmask['hire']		= 128;
		return $wantmask;
	}

	public function profile_skilsmask() {
		return $this->profile_skillsmask(); // left here for compatibility of old modules
	}

	public function profile_skillsmask() {
		$skillsmask['textures']			=  1;
		$skillsmask['architecture']		=  2;
		$skillsmask['modeling']			=  8;
		$skillsmask['eventplanning']	=  4;
		$skillsmask['scripting']		= 16;
		$skillsmask['customcharacters']	= 32;
		return $skillsmask;
	}

	public function getprofile($userid) {
		if(!$this->_osgrid_db) return FALSE;
		$opensim	= $this->opensim;
		$db			= JFactory::getDBO();
		$query		= sprintf("SELECT #__opensim_userprofile.* FROM #__opensim_userprofile WHERE #__opensim_userprofile.avatar_id = '%s'",$userid);
		$db->setQuery($query);
		$profile = $db->loadAssoc();
		if(count($profile) == 0) { // in case no profile stored yet, fill it with empty values to avoid php notices
			$profile['error']			= JText::_('JOPENSIM_PROFILE_ERROR_NOTFOUND');
			$profile['aboutText']		= "";
			$profile['maturePublish']	= 0;
			$profile['partner']			= null;
			$profile['url']				= "";
			$profile['image']			= "";
			$profile['wantmask']		= 0;
			$profile['wanttext']		= "";
			$profile['skillsmask']		= 0;
			$profile['skillstext']		= "";
			$profile['languages']		= "";
			$profile['firstLifeText']	= "";
			$profile['firstLifeImage']	= "";
		}
		if($profile['partner']) {
			$partnernamequery = $opensim->getUserNameQuery($profile['partner']);
			$this->_osgrid_db->setQuery($partnernamequery);
			$partner = $this->_osgrid_db->loadAssoc();
			$profile['partnername'] = $partner['firstname']." ".$partner['lastname'];
		} else {
			$profile['partnername'] = null;
		}
		return $profile;
	}

	public function checkOsClient($uuid) {
		return $this->opensimCreated($uuid);
	}

	public function checkClient($uuid) {
		$query	= sprintf("SELECT * FROM #__opensim_moneybalances WHERE `user`= '%s'",$uuid);
		$db		= JFactory::getDBO();
		$db->setQuery($query);
		$db->execute();
		$num_rows = $db->getNumRows();
		if($num_rows == 1) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function getMoneySettings() {
		// Lets load the data if it doesn't already exist
		if (empty($this->_moneySettingsData)) {
			$settings = array();
			if (empty( $this->_settingsData )) {
				$this->getSettingsData();
			}
			$settings['bankerUID']			= $this->_settingsData['jopensimmoneybanker'];
			$settings['groupCharge']		= $this->_settingsData['jopensimmoney_groupcreation'];
			$settings['uploadCharge']		= $this->_settingsData['jopensimmoney_upload'];
			$settings['startBalance']		= $this->_settingsData['jopensimmoney_startbalance'];
			$settings['groupMinDividend']	= $this->_settingsData['jopensimmoney_groupdividend'];
			$settings['name']				= $this->_settingsData['jopensimmoney_currencyname'];
			$settings['bankerName']			= $this->_settingsData['jopensimmoney_bankername'];
			$settings['sendBalanceWarning']	= $this->_settingsData['jopensimmoney_sendgridbalancewarning'];
			$settings['warningRecipient']	= $this->_settingsData['jopensimmoney_warningrecipient'];
			$settings['warningSubject']		= $this->_settingsData['jopensimmoney_warningsubject'];
			$settings['showzerolines']		= $this->_settingsData['jopensimmoney_zerolines'];

			$this->_moneySettingsData = $settings;
		}
		return $this->_moneySettingsData;
	}

	public function setBalance($uuid,$amount) {
		$this->balanceExists($uuid); // $uuid could be a group, see if it exists and if not, create a balance line for it
		$query	= sprintf("UPDATE #__opensim_moneybalances SET balance = balance + %d WHERE `user`= '%s'",$amount,$uuid);
		$db		= JFactory::getDBO();
		$db->setQuery($query);
		$db->execute();
	}

	public function getBalance($uuid) {
		$query	= sprintf("SELECT #__opensim_moneybalances.balance FROM #__opensim_moneybalances WHERE `user`= '%s'",$uuid);
		$db		= JFactory::getDBO();
		$db->setQuery($query);
		$db->execute();
		$num_rows = $db->getNumRows();
		if($num_rows == 1) {
			return $db->loadResult();
		} else {
			return FALSE;
		}
	}

	public function getCurrencyName() {
		return $this->_settingsData['jopensimmoney_currencyname'];;
	}

	public function balanceExists($uuid) { // if this $uuid does not exist yet, it will create a 0 Balance for it
		$query	= sprintf("SELECT balance FROM #__opensim_moneybalances WHERE `user` = '%s'",$uuid);
		$db		= JFactory::getDBO();
		$db->setQuery($query);
		$db->execute();
		$num_rows = $db->getNumRows();
		if($num_rows == 0) {
			$query = sprintf("INSERT INTO #__opensim_moneybalances (`user`,`balance`) VALUES ('%s',0)",$uuid);
			$db->setQuery($query);
			$db->execute();
		}
	}

	public function TransferMoney($parameter) {
		$isSender	= $this->checkOsClient($parameter['senderID']);
		$isReceiver	= $this->checkOsClient($parameter['receiverID']);

		if($isSender === FALSE) {
			$retval['success']	= FALSE;
			$retval['message']	= "Could not locate senderID ".$parameter['senderID'];
		} elseif($isReceiver === FALSE) {
			$retval['success']	= FALSE;
			$retval['message']	= "Could not locate receiverID ".$parameter['receiverID'];
		} else {
			if(!$this->checkClient($parameter['senderID'])) $this->balanceExists($parameter['senderID']);
			if(!$this->checkClient($parameter['receiverID'])) $this->balanceExists($parameter['receiverID']);
			$parameter['time'] = time();
			$parameter['status'] = 0;
			$this->insertTransaction($parameter);

			$this->setBalance($parameter['receiverID'],$parameter['amount']);
			$this->setBalance($parameter['senderID'],-$parameter['amount']);

			$retval['success']				 = TRUE;
			$retval['clientUUID']			 = (isset($parameter['clientUUID']))			? $parameter['clientUUID']:null;
			$retval['clientSessionID']		 = (isset($parameter['clientSessionID']))		? $parameter['clientSessionID']:null;
			$retval['clientSecureSessionID'] = (isset($parameter['clientSecureSessionID']))	? $parameter['clientSecureSessionID']:null;;
		}
		return $retval;
	}

	public function insertTransaction($parameter) {

		$senderID 				= (isset($parameter['senderID']))				? $parameter['senderID']:"";
		$receiverID				= (isset($parameter['receiverID']))				? $parameter['receiverID']:"";
		$amount					= (isset($parameter['amount']))					? $parameter['amount']:0;
		$objectID				= (isset($parameter['objectID']))				? $parameter['objectID']:"";
		$regionHandle			= (isset($parameter['regionHandle']))			? $parameter['regionHandle']:"";
		$transactionType		= (isset($parameter['transactionType']))		? $parameter['transactionType']:"";
		$time					= (isset($parameter['time']))					? $parameter['time']:time();
		$senderSecureSessionID	= (isset($parameter['senderSecureSessionID']))	? $parameter['senderSecureSessionID']:"";
		$status					= (isset($parameter['status']))					? $parameter['status']:0;
		$description			= (isset($parameter['description']))			? $parameter['description']:"";

		$query = sprintf("INSERT INTO #__opensim_moneytransactions (`UUID`,sender,receiver,amount,objectUUID,regionHandle,type,`time`,`secure`,`status`,description)
															VALUES
																	(UUID(),'%s','%s','%d','%s','%s','%d','%d','%s','%d','%s')",
									$senderID,
									$receiverID,
									$amount,
									$objectID,
									$regionHandle,
									$transactionType,
									$time,
									$senderSecureSessionID,
									$status,
									$description);

		$db		= JFactory::getDBO();
		$db->setQuery($query);
		$db->execute();
	}

	public function transactionlist($uuid,$days = 0) {
		if(!array_key_exists("bankerUID",$this->_moneySettingsData) || !$this->_moneySettingsData['bankerUID']) $this->getMoneySettings();
		$transactions = $this->getTransactions($uuid,$days);
		if(is_array($transactions) && count($transactions) > 0) {
			foreach($transactions AS $key => $transaction) {
				if(($this->_settingsData['jopensimmoney_zerolines'] & 1) != 1 && $transaction['amount'] == 0) {
					unset($transactions[$key]);
					continue;
				}
				if($transaction['direction'] == "in") {
					$transactions[$key]['receivername'] = "";
					if($transaction['sender'] == $this->_moneySettingsData['bankerUID']) {
						$transactions[$key]['sendername'] =  ($this->_moneySettingsData['bankerName']) ? $this->_moneySettingsData['bankerName']:JText::_('JOPENSIM_MONEY_BANKERNAME');
					} else {
						$transactions[$key]['sendername'] = $this->getOpenSimName($transaction['sender'],"full");
					}
				} else {
					if($transaction['receiver'] == $this->_moneySettingsData['bankerUID']) {
						$transactions[$key]['receivername'] =  ($this->_moneySettingsData['bankerName']) ? $this->_moneySettingsData['bankerName']:JText::_('JOPENSIM_MONEY_BANKERNAME');
					} else {
						$transactions[$key]['receivername'] = $this->getOpenSimName($transaction['receiver'],"full");
					}
					$transactions[$key]['sendername'] = "";
				}
				$transactions[$key]['transactiontime'] = date(JText::_('JOPENSIM_MONEY_TIMEFORMAT'),$transaction['time']);
			}
		}
		return $transactions;
	}

	public function getTransactions($uuid = null,$days = 0) {
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		if($uuid) $query->select('IF(#__opensim_moneytransactions.receiver = '.$db->quote($uuid).',"in","out") AS direction');
		else $query->select('"none" AS direction');
		$query->select('#__opensim_moneytransactions.*');
		$query->from('#__opensim_moneytransactions');
		if($uuid) {
			$query->where('(#__opensim_moneytransactions.sender = '.$db->quote($uuid).' OR #__opensim_moneytransactions.receiver = '.$db->quote($uuid).')');
		}
		if($days > 0) {
			if($days == 365) $query->where('#__opensim_moneytransactions.`time` >= UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 1 YEAR))');
			else $query->where('#__opensim_moneytransactions.`time` >= UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL '.(int)$days.' DAY))');
		}
		$query->order('#__opensim_moneytransactions.`time` DESC');
		$db->setQuery($query);
		$transactions = $db->loadAssocList();
		$db->setQuery($query);
		$transactions = $db->loadAssocList();
		return $transactions;
	}

	public function getTransactionNames($transactionlist,$uuid = null) {
		if(!is_array($transactionlist)) return FALSE;
		if(count($transactionlist) == 0) return array();
		$opensim = $this->opensim;
		if($uuid) {
			if($uuid == $this->_moneySettingsData['bankerUID']) {
				$name = ($this->_moneySettingsData['bankerName']) ? $this->_moneySettingsData['bankerName']:JText::_('JOPENSIM_MONEY_BANKERNAME');
			} else {
				$name = $opensim->getUserName($uuid,"full");
			}
		}
		foreach($transactionlist AS $key => $transaction) {
			if($uuid) {
				if($transaction['direction'] == "in") {
					$transactionlist[$key]['receivername']	= $name;
					$transactionlist[$key]['sendername']	= $opensim->getUserName($transaction['sender'],"full");
				} elseif($transaction['direction'] == "out") {
					$transactionlist[$key]['receivername']	= $opensim->getUserName($transaction['receiver'],"full");
					$transactionlist[$key]['sendername']	= $name;
				} else {
					$transactionlist[$key]['receivername']	= $opensim->getUserName($transaction['receiver'],"full");
					$transactionlist[$key]['sendername']	= $opensim->getUserName($transaction['sender'],"full");
				}
			} else {
				$transactionlist[$key]['receivername']	= $opensim->getUserName($transaction['receiver'],"full");
				$transactionlist[$key]['sendername']	= $opensim->getUserName($transaction['sender'],"full");
			}
			if($transaction['receiver'] == $this->_moneySettingsData['bankerUID']) $transactionlist[$key]['receivername'] = ($this->_moneySettingsData['bankerName']) ? $this->_moneySettingsData['bankerName']:JText::_('JOPENSIM_MONEY_BANKERNAME');
			if($transaction['sender'] == $this->_moneySettingsData['bankerUID']) $transactionlist[$key]['sendername'] = ($this->_moneySettingsData['bankerName']) ? $this->_moneySettingsData['bankerName']:JText::_('JOPENSIM_MONEY_BANKERNAME');
		}
		return $transactionlist;
	}

	public function getTransactionOpenSimNames($items) {
		if(!is_array($items)) return FALSE;
		if(count($items) == 0) return $items;
		if (empty($this->_moneySettingsData)) $this->getMoneySettings();
		$bankerUID	= $this->_moneySettingsData['bankerUID'];
		$bankerName	= $this->_moneySettingsData['bankerName'];
		if(!$bankerName) $bankerName = $this->getOpenSimName($bankerUID);
		foreach($items AS $key => $item) {
			$items[$key]['sendername'] = ($item['sender'] == $bankerUID) ? $bankerName:$this->getOpenSimName($item['sender']);
			$items[$key]['receivername'] = ($item['receiver'] == $bankerUID) ? $bankerName:$this->getOpenSimName($item['receiver']);
		}
		return $items;
	}

	public function getOpenSimName($uuid) {
		$name		= $this->opensim->getUserName($uuid,'full');
		if(!$name) {
			$clientinfo	= $this->getClientInfo($uuid);
			if($clientinfo['userName'])	{
				$name = $clientinfo['userName'];
				if($clientinfo['grid'])		$name .= "@".$clientinfo['grid'];
			} else {
				// maybe is was a group?
				$name = $this->getGroupName($uuid);
				if(!$name) $name = "<acronym title='".$uuid."'>unknown</acronym>";
			}
		}
		return $name;
	}

	public function getGroupName($uuid) {
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		$query->select('#__opensim_group.Name');
		$query->from('#__opensim_group');
		$query->where('#__opensim_group.GroupID = '.$db->quote($uuid));
		$db->setQuery($query);
		$groupname = $db->loadResult();
		if($groupname) return "[".JText::_('JOPENSIM_GROUP')."] ".$groupname;
		else return FALSE;
	}

	public function removehidden($regionarray) {
		foreach($regionarray AS $key => $region) {
			if($region['hidemap'] == 1) unset($regionarray[$key]);
		}
		return $regionarray;
	}

	public function getClientInfo($uuid) {
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		$query->select('#__opensim_clientinfo.*');
		$query->from('#__opensim_clientinfo');
		$query->where('#__opensim_clientinfo.PrincipalID = '.$db->quote($uuid));
		$db->setQuery($query);
		$clientinfo = $db->loadAssoc();
		return $clientinfo;
	}

	public function frontendCSS() {
		return JPATH_COMPONENT_SITE.DIRECTORY_SEPARATOR."assets".DIRECTORY_SEPARATOR."opensim.override.css";
	}

	public function saveCSS() {
		$cssfile = $this->frontendCSS();
		if(!is_writable($cssfile)) {
			$retval['type']		= "error";
			$retval['message']	= JText::_('JOPENSIM_CSSSAVE_ERROR');
		} else {
			$csscontent = trim(JFactory::getApplication()->input->get('csscontent','','raw'));
			file_put_contents($cssfile, $csscontent);

			$retval['type']		= "message";
			$retval['message']	= JText::_('JOPENSIM_CSSSAVE_OK');
		}
		return $retval;
	}

	public function getForm( $data = array(), $loadData = true) {
		parent::getForm($data,$loadData);
	}

	public function debugprint($data,$exit = 1) {
		echo "<pre>\n";
		var_export($data);
		echo "</pre>\n";
		if($exit == 1) exit;
	}

	public function debuglog($data,$file = null, $line = null,$title = null) {
		$debuglogfile = JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR."debug.log";
		$fh = fopen($debuglogfile,"a+");
		$zeile = "";
		if($title)	$zeile .= "########## ".date("d.m.Y H:i:s").": ".$title." ##########\n";
		if($file)	$zeile .= "File: ".$file."\n";
		if($line)	$zeile .= "Zeile: ".$line."\n";
		$zeile .= @var_export($data,TRUE)."\n\n";
		fwrite($fh,$zeile);
		fclose($fh);
	}

	public function getGridStatus() {
		if(!$this->_osgrid_db) {
			$returnvalue['statusmsg'] = "<span style='color:#".$this->_settingsData['loginscreen_offline_color']."'>".JText::_('OFFLINE')."</span>";
		} else {
			$zeroUID		= "00000000-0000-0000-0000-000000000000";
			$lastDays		= $this->_settingsData['loginscreen_xdays'];
			$offlinecolor	= $this->_settingsData['loginscreen_offline_color'];
			$onlinecolor	= $this->_settingsData['loginscreen_online_color'];
			$returnvalue = array();

			$this->_osgrid_db->setQuery("SELECT uuid FROM regions");
			$regions = $this->_osgrid_db->loadColumn();
			if(intval($this->_settingsData['hiddenregions']) == 0) {
				$db = JFactory::getDbo();
				$query = "SELECT #__opensim_mapinfo.regionUUID FROM #__opensim_mapinfo WHERE #__opensim_mapinfo.hidemap = 1";
				$db->setQuery($query);
				$hiddenregions = $db->loadColumn();
				if(is_array($hiddenregions)) {
					$numrows = count($hiddenregions);
				} else {
					$numrows = 0;
				}
				$this->_settingsData['debug1'] = $numrows;
				if($numrows > 0) {
					$db->setQuery($query);
					$this->_settingsData['debug2'] = $hiddenregions;
					foreach($hiddenregions AS $hiddenregion) {
						$ishidden = array_search($hiddenregion,$regions);
						if($ishidden === FALSE) continue;
						else unset($regions[$ishidden]);
					}
				}
			}
			$returnvalue['totalregions'] = count($regions);

			if($this->_settingsData['loginscreen_gridstatus'] == -1) $returnvalue['status'] = "offline";
			elseif($this->_settingsData['loginscreen_gridstatus'] == 1) $returnvalue['status'] = "online";
			else {
				if($returnvalue['totalregions'] > 0)  $returnvalue['status'] = "online"; // Online Server needs more than 0 regions
				else  $returnvalue['status'] = "offline";
			}

			if($returnvalue['status'] == "online") $returnvalue['statusmsg'] = "<span class='jopensim_gridstatus' style='color:".$onlinecolor.";'>".JText::_('ONLINE')."</span>";
			else $returnvalue['statusmsg'] = "<span class='jopensim_gridstatus' style='color:".$offlinecolor."'>".JText::_('OFFLINE')."</span>";

			if(!$lastDays) $lastDays = $this->_settingsData['loginscreen_xdays'];
			$returnvalue['days'] = $lastDays;

			$this->_osgrid_db->setQuery(sprintf("SELECT COUNT(*) FROM Presence WHERE RegionID != '%s'",$zeroUID));
			$returnvalue['online'] = $this->_osgrid_db->loadResult();

			$tage = sprintf("%d",$lastDays);
			$jetzt = time();
			$lastloggedin = $jetzt - 60*60*24*$tage;
			$this->_osgrid_db->setQuery("SELECT COUNT(*) FROM GridUser WHERE Login > '$lastloggedin' OR Logout > '$lastloggedin'");
			$returnvalue['lastonline']		= $this->_osgrid_db->loadResult();

			$returnvalue['loginscreen_show_status']			= $this->_settingsData['loginscreen_show_status'];
			$returnvalue['loginscreen_show_regions']		= $this->_settingsData['loginscreen_show_regions'];
			$returnvalue['loginscreen_show_uniquevisitors']	= $this->_settingsData['loginscreen_show_uniquevisitors'];
			$returnvalue['loginscreen_show_totalusers']		= $this->_settingsData['loginscreen_show_totalusers'];
			$returnvalue['loginscreen_show_onlinenow']		= $this->_settingsData['loginscreen_show_onlinenow'];
			$returnvalue['hiddenregions']					= $this->_settingsData['hiddenregions'];
		}
		return $returnvalue;
	} //end getStatus

	public function getLoginscreenPositions() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select('#__opensim_loginscreen.*');
		$query->from($db->quoteName('#__opensim_loginscreen'));
		$query->where($db->quoteName('#__opensim_loginscreen.active').' = 1');
		$db->setQuery($query);
		$positions = $db->loadAssocList();
		if(count($positions) > 0) {
			foreach($positions AS $key => $position) {
				$query	= $db->getQuery(true);
				$query->select($db->quoteName('#__modules.id'));
				$query->select($db->quoteName('#__modules.title'));
				$query->select($db->quoteName('#__modules.module'));
				$query->select($db->quoteName('#__modules.published'));
				$query->from($db->quoteName('#__modules'));
				$query->where($db->quoteName('#__modules.position').' = '.$db->quote($position['positionname']));
				$db->setQuery($query);
				$positions[$key]['modules'] = $db->loadAssocList();
			}
		}
		return $positions;
	}

	public function checkPluginStatus($element,$folder) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select('#__extensions.*');
		$query->select($db->quoteName('#__viewlevels.title')." AS leveltitle");
		$query->from($db->quoteName('#__extensions'));
		$query->join('LEFT', '#__viewlevels ON #__viewlevels.id = #__extensions.access');
		$query->where($db->quoteName('#__extensions.element').' = '.$db->quote($element));
		$query->where($db->quoteName('#__extensions.folder').' = '.$db->quote($folder));
		$db->setQuery($query);
		$db->execute();
		$foundplugin = $db->getNumRows();
		if($foundplugin == 1) {
			return $db->loadAssoc();
		} else {
			return FALSE;
		}
	}

	public function __destruct() {
	}
}
?>