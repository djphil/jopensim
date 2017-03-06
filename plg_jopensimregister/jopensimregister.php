<?php
/**
* jOpenSimRegister Plugin for Joomla 3.x
* @version $Id: jOpenSimRegister.php $
* @package: jOpenSimRegister
* ===================================================
* @author
* Name: FoTo50, www.jopensim.com
* Email: foto50@jopensim.com
* Url: http://www.jopensim.com
* ===================================================
* @copyright (C) 2017 FoTo50, (www.jopensim.com). All rights reserved.
* @license see http://www.gnu.org/licenses/gpl-2.0.html  GNU/GPL.
* You can use, redistribute this file and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation.
*/

defined('_JEXEC') or die();

if(!defined('DS')) define("DS",DIRECTORY_SEPARATOR);

jimport('joomla.plugin');
jimport('joomla.html.parameter' );
jimport('joomla.application.component.view');
jimport('joomla.application.component.helper');

require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_opensim'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'opensim.class.php');

class plgUserjOpensimRegister extends JPlugin {
	public $_osgrid_db		= null;
	public $admin_model		= null;
	public $opensim			= null;
	public $_settingsData	= null;
	public $doredirect		= null;
	public $redirectafter	= "";
	public $configvalues	= array("lastnametype",
									"lastnamelist",
									"defaulthome",
									"mapstartX",
									"mapstartY",
									"mapstartZ"
									);
	public $debugstop;

	public function __construct($subject, $config) {
		parent::__construct($subject, $config);
		$this->loadLanguage();
		JFormHelper::addFieldPath(dirname(__FILE__) . '/fields');
		JLoader::import('joomla.application.component.model'); 
		JLoader::import( 'opensim', JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_opensim'.DIRECTORY_SEPARATOR.'models' );
		$this->admin_model		= JModelLegacy::getInstance( 'ModelOpenSim','OpenSim' );
		$this->doredirect		= $this->params->get('plgJopensimDoRedirect');
		$this->redirectafter	= $this->params->get('plgJopensimRedirectAfter');
	}

	public function onContentPrepareForm($form, $data) {
		if($this->doredirect == 1) {
			$view	= JFactory::getApplication()->input->get('view','','method','string');
			$layout	= JFactory::getApplication()->input->get('layout','','method','string');
			if($view == "registration" && $layout == "complete") {
				$menu			= &JSite::getMenu();
				$item			= $menu->getItem($this->redirectafter);
				$link			= new JURI($item->link);
				$link->setVar('Itemid', $this->redirectafter);
				$redirect		= $link->toString();
				JFactory::getApplication()->redirect($redirect);
			}
		}
		$task	= JFactory::getApplication()->input->get('task','','method','string');
		if($task == "nojopensim") {
			return TRUE;
		}
		$avatarwidth		= $this->params->get('plgJopensimRegisterAvatarWidth',220);
		$avatarwidthstyle	= $this->params->get('plgJopensimRegisterAvatarWidthStyle','px');
		$avatarcolumns		= $this->params->get('plgJopensimRegisterAvatarColumns',1);
		$avatarcolumnspace	= $this->params->get('plgJopensimRegisterAvatarColumnspace',10);
		$joomlaformenabled	= $this->params->get('plgJopensimRegisterFormJoomla');

		// Add CSS and JS for the TOS field
		$doc = JFactory::getDocument();
		$css = "#jform_jopensimregister_jopensimavatar div.plg_jopensimregister_clear {display:inline-block;}
				#jform_jopensimregister_jopensimavatar div:nth-of-type(".$avatarcolumns."n+".$avatarcolumns.") {display:block;height:".$avatarcolumnspace."px;}
				#jform_jopensimregister_jopensimavatar label.jopensimavatar {width:".$avatarwidth.$avatarwidthstyle.";}
				";
		$doc->addStyleDeclaration($css);

		$formname = $form->getName();

		// Load user_profile plugin language
		$lang = JFactory::getLanguage();
		$lang->load('plg_user_jopensimregister', JPATH_ADMINISTRATOR);

		$registerForms = $this->getEnabledForms();

		if (!($form instanceof JForm)) {
			$this->_subject->setError('JERROR_NOT_A_FORM');
			return FALSE;
		}

		// Check we are manipulating a valid form.
//		if (!in_array($name, array('com_users.profile', 'com_users.registration','com_users.user','com_admin.profile'))) {
		if ($formname != 'com_users.registration' || $joomlaformenabled == 0) {
			return TRUE;
		}

		// Add the registration fields to the form.
		JForm::addFormPath(dirname(__FILE__) . '/profiles');

		$settings		= $this->admin_model->getSettingsData();

		$lastnametype 				= $settings['lastnametype'];
		$lastnamelist				= $settings['lastnamelist'];
		$jopensim_userhome_region	= $settings['jopensim_userhome_region'];
		$jopensim_userhome_x		= $settings['jopensim_userhome_x'];
		$jopensim_userhome_y		= $settings['jopensim_userhome_y'];
		$jopensim_userhome_z		= $settings['jopensim_userhome_z'];

		// if allowed names only, we change from text field to select field for lastnames
		if($lastnametype != 1) {
			$form->loadFile('profile_text', false);
		} else {
			$form->loadFile('profile', false);
		}

		$avatararticle = $this->params->get('plgJopensimRegisterAvatarArticle');
		$avatarenabled = $this->params->get('plgJopensimRegisterAvatarSelect', 0);

		// We need to be in the registration form, field needs to be enabled and we need an article ID
		if ($formname != 'com_users.registration' || !$avatarenabled) {
			// We only want the avatar article in the registration form
			$form->removeField('jopensimavatar', 'jopensimregister');
		} else {
			// Push the article ID into the field.
			$form->setFieldAttribute('jopensimavatar', 'article', $avatararticle, 'jopensimregister');
			$form->setFieldAttribute('jopensimavatar', 'required', TRUE, 'jopensimregister');
		}



		if ($formname == 'com_users.profile' || $formname == 'com_users.registration' || $formname == 'com_users.user') {
			// Toggle whether the something field is required.
			if ($this->params->get('plgJopensimRegisterUser', 'required') == 'required') {
				$form->setFieldAttribute('firstname', 'required', TRUE, 'jopensimregister');
				$form->setFieldAttribute('lastname', 'required', TRUE, 'jopensimregister');
			}
		}                       
	}



	// before saving, check if First Name/Last Name is free and if Last Name is allowed due to jOpenSim settings
	public function onUserBeforeSave($user,$isnew,$new) {
		if(!$isnew) return TRUE; // Existing users are not needed here

		$firstname	= JArrayHelper::getValue($new['jopensimregister'], 'firstname', null, 'string');
		$lastname	= JArrayHelper::getValue($new['jopensimregister'], 'lastname', null, 'string');

		JPlugin::loadLanguage( 'plg_user_jOpenSimRegister', JPATH_ADMINISTRATOR );

		$option					= JFactory::getApplication()->input->get('option','','method','string');
		$view					= JFactory::getApplication()->input->get('view','','method','string');
		$task					= JFactory::getApplication()->input->get('task','','method','string');
		$retval					= FALSE;
		$plgRegisterjOpenSim	=& JPluginHelper::getPlugin('user', 'jopensimregister');
		$registerForms = $this->getEnabledForms();

		if($option == "com_users" && $task == "register") {
			$retval = $this->checkUser($firstname,$lastname);
		}

		if($option == "com_comprofiler" && $task == "saveregisters" && $registerForms['cb'] == "yes") {
			$retval = $this->checkUser($firstname,$lastname);
		}
		return $retval;
	}

	public function onUserAfterSave($user, $isnew, $result, $error) {
		$userId	= JArrayHelper::getValue($user, 'id', 0, 'int');
		if ($userId && $result && isset($user['jopensimregister']) && (count($user['jopensimregister']))) {
			if(!$isnew) return TRUE; // Existing users are not needed here
			$firstname	= JArrayHelper::getValue($user['jopensimregister'], 'firstname', null, 'string');
			$lastname	= JArrayHelper::getValue($user['jopensimregister'], 'lastname', null, 'string');
			if(isset($user['jopensimregister']['jopensimavatar'])) {
				$avatar = JArrayHelper::getValue($user['jopensimregister'], 'jopensimavatar', null, 'string');
			} else {
				$avatar = null;
			}

			$option			= JFactory::getApplication()->input->get('option','','method','string');
			$task			= JFactory::getApplication()->input->get('task','','method','string');
			$plgRegisterjOpenSim =& JPluginHelper::getPlugin('user', 'jopensimregister');
			$registerForms = $this->getEnabledForms();

			// Check out, from that form we get data
			$currentForm = "core";

			if($currentForm == "core" || $currentForm == "cb") {
				$newuser = array();

				// seems everything went ok, lets save the user in the grid DB
				// now we need to look into the opensim database
				$this->getOpenSimGridDB();
				$this->getSettings();

				$newuser['firstname'] = $firstname;
				$newuser['lastname'] = $lastname;
				if($avatar) $newuser['avatar'] = $avatar;
				switch($currentForm) {
					case "core":
						$newuser['email']		= $user['email'];
						$newuser['password']	= $user['password_clear'];
					break;
					case "cb":
						$newuser['email']		= $user['email'];
						$newuser['password']	= $user['password'];
					break;
					default: // this should actually never happen
						$newuser['email']		= null;
						$newuser['password']	= null;
					break;
				}
				if($firstname && $lastname) {
					$opensimUID = $this->insertuser($newuser);

					$db =& JFactory::getDBO();
					$query = sprintf("INSERT INTO #__opensim_userrelation (opensimID,joomlaID) VALUES ('%s','%d')",$opensimUID,$user['id']);
					$db->setQuery($query);
					$db->query();
				}
			}
		}
		return TRUE;
	}

	public function onUserBeforeDelete($user) {
		$option			= JFactory::getApplication()->input->get('option','','method','string');
		$view			= JFactory::getApplication()->input->get('view','','method','string');
		$task			= JFactory::getApplication()->input->get('task','','method','string');

		$plgRegisterjOpenSim =& JPluginHelper::getPlugin('user', 'jopensimregister');
		$this->params   	= new JRegistry($plgRegisterjOpenSim->params);
		$deleteOpensim		= $this->params->get('plgJopensimDeleteUser');
		if($deleteOpensim == "1") { // if set, delete the opensim account
			// now we need to look into the opensim database
			$this->getOpenSimGridDB();
			$this->getSettings();
			$opensimID = $this->getOpenSimUID($user['id']);
			$onlinestatus = $this->getonlinestatus($opensimID);
			if($onlinestatus) {
				JFactory::getApplication()->enqueueMessage(JTEXT::_('PLG_JOPENSIMREGISTER_ERROR_USER_ONLINE'),"error");
				$redirect = "index.php?option=com_users&view=users";
				JFactory::getApplication()->redirect($redirect);
			}
		}
		
	}

	public function onUserAfterDelete($user) {
		$plgRegisterjOpenSim	=& JPluginHelper::getPlugin('user', 'jopensimregister');
		$this->params   		= new JRegistry($plgRegisterjOpenSim->params);
		$deleteOpensim			= $this->params->get('plgJopensimDeleteUser');
		$db =& JFactory::getDBO();
		if($deleteOpensim == "1") { // if set, delete the opensim account

			// now we need to look into the opensim database
			$this->getOpenSimGridDB();
			$this->getSettings();



			$opensimID = $this->deleteuser($user['id']);
			// remove stored Offline Messages
			$query = sprintf("DELETE FROM #__opensim_offlinemessages WHERE fromAgentID = '%1\$s' OR toAgentID = '%1\$s'",$opensimID);
			$db->setQuery($query);
			$db->query();
		}


		// delete relation row in jOpenSim component anyway, since this user does not exists anymore
		$query = sprintf("DELETE FROM #__opensim_userrelation WHERE opensimID = '%s'",$opensimID);
		$db->setQuery($query);
		$db->query();

		return TRUE;
	}

	public function getEnabledForms() {
		$plgRegisterjOpenSim	=& JPluginHelper::getPlugin('user', 'jopensimregister');
		$this->params   		= new JRegistry($plgRegisterjOpenSim->params);
		$forms['joomla']		= $this->params->get('plgJopensimRegisterFormJoomla');
		$forms['cb']			= $this->params->get('plgJopensimRegisterFormCB');
		$forms['joomlsocial']	= $this->params->get('plgJopensimRegisterFormJS');
		$forms['test'] = $this->params;
		return $forms;
	}

	public function checkUser($firstname,$lastname) {
		// now we need to look into the opensim database
		$this->getOpenSimGridDB();

		$plgRegisterjOpenSim	=& JPluginHelper::getPlugin('user', 'jopensimregister');
		$this->params   		= new JRegistry($plgRegisterjOpenSim->params);
		$usertype				= $this->params->get('plgJopensimRegisterUser');

		// the opensim fields MUST be both valid when $usertype is required
		if($usertype == "required" && (!$firstname || !$lastname)) {
			$this->returnError(JTEXT::_('PLG_JOPENSIMREGISTER_ERROR_MESSAGE_REQUIRED'));
		}
		// at "optional" creation, the fields must either be both valid or none, not just one of them
		if($usertype == "optional" && (($firstname && !$lastname) || (!$firstname && $lastname))) {
			$this->returnError(JTEXT::_('PLG_JOPENSIMREGISTER_ERROR_MESSAGE_REQUIRED'));
		}
		// in these cases, an opensim account will be created
		if(($usertype == "required" || $usertype == "optional") && $firstname && $lastname) {
			$valid = $this->checkValidName($firstname,$lastname);
			if($valid === FALSE) {
				return FALSE;
			}
		}

		// now everything should be ok, lets return true to create the joomla account and then the opensim account onUserAfterSave
		return TRUE;
	}

	public function getonlinestatus($userid) {
		if(empty($this->_osgrid_db)) $this->getOpenSimGridDB();
		if(!$this->_osgrid_db) return FALSE;
		$opensim	= $this->opensim;
		$query		= $opensim->getOnlineStatusQuery($userid);
		$this->_osgrid_db->setQuery($query);
		$this->_osgrid_db->query();
		$num_rows	= $this->_osgrid_db->getNumRows();
		if($num_rows == 1) return TRUE;
		else return FALSE;
	}

	public function returnError($message) {
		$option		= JFactory::getApplication()->input->get('option','','method','string');
		switch($option) {
			case "com_users":
				$view		= "registration";
				$task		= null;
				$redirect	= "index.php?option=".$option."&view=".$view;
			break;
			case "com_comprofiler":
				$view		= null;
				$task		= "registers";
				$redirect	= "index.php?option=".$option."&task=".$task;
			break;
		}
		JFactory::getApplication()->enqueueMessage($message,"error");
		JFactory::getApplication()->redirect($redirect);
		return FALSE;
	}

	// check the name values if they are valid
	public function checkValidName($firstname,$lastname) {
		$existing = $this->checkUserExists($firstname,$lastname);
		if($existing == TRUE) return FALSE; // This OpenSim User is already existing
		else return $this->allowedName($firstname,$lastname);
	}

	public function checkUserExists($firstname,$lastname) {
		if(empty($this->_osgrid_db)) $this->getOpenSimGridDB();
		if(!$this->_osgrid_db) return FALSE;
		$opensim = $this->opensim;
		$checkquery = $opensim->getCheckQuery($firstname,$lastname);
		$this->_osgrid_db->setQuery($checkquery);
		$this->_osgrid_db->query();
		$existing = $this->_osgrid_db->getNumRows();
		if($existing > 0) {
			return TRUE;
			$this->returnError(PLG_JOPENSIMREGISTER_ERROR_MESSAGE_EXISTING);
		} else {
			return FALSE;
		}
	}

	public function allowedName($firstname,$lastname) {
		// lets have a look for allowed or denied lastnames
		$jopensimsettings = $this->getSettings();

		if(			is_array($jopensimsettings) &&
					array_key_exists("lastnametype",$jopensimsettings) &&
					array_key_exists("lastnames",$jopensimsettings) &&
					$jopensimsettings['lastnametype'] != 0 &&
					is_array($jopensimsettings['lastnames']) && 
					count($jopensimsettings['lastnames']) > 0) {
			if($jopensimsettings['lastnametype'] == "-1" && in_array($lastname,$jopensimsettings['lastnames'])) {
				$message = JText::_(PLG_JOPENSIMREGISTER_ERROR_MESSAGE_DENIED);
				$this->returnError($message);
				$retval = FALSE;
			} elseif ($jopensimsettings['lastnametype'] == "1" && !in_array($lastname,$jopensimsettings['lastnames'])) {
				$message = JText::_(PLG_JOPENSIMREGISTER_ERROR_MESSAGE_ALLOWED);
				$this->returnError($message);
				$retval = FALSE;
			} else {
				$retval = TRUE;
			}
		} else {
			$retval = TRUE;
		}
		return $retval;
	}

	public function insertuser($newuser) {
		if(empty($this->_osgrid_db)) $this->getOpenSimGridDB();
		if(!$this->_osgrid_db) return FALSE;
		if(!isset($newuser['uuid']) || !$newuser['uuid']) $newuser['uuid'] = $this->getUUID();
		$newuser['homeregion'] = $this->_settingsData['jopensim_userhome_region'];
		$newuser['homeposition'] = sprintf("<"."%f,%f,%f".">",$this->_settingsData['jopensim_userhome_x'],$this->_settingsData['jopensim_userhome_y'],$this->_settingsData['jopensim_userhome_z']);
		$newuser['homelookat'] = "<0,0,0>"; // have to figure out once how to set that exact
		$opensim = $this->opensim;
		$newuser['passwordSalt'] = md5(time());
		$newuser['passwordHash'] = md5(md5($newuser['password']).":".$newuser['passwordSalt']);
		$insertquery = $opensim->getInsertUserQuery($newuser);
		$this->_osgrid_db->setQuery($insertquery['user']);
		$retval = $this->_osgrid_db->query();
		$this->_osgrid_db->setQuery($insertquery['auth']);
		$retval = $this->_osgrid_db->query();
		if($this->regionExists($newuser['homeregion'])) { // only add home region if set already
			$this->_osgrid_db->setQuery($insertquery['grid']);
			$retval = $this->_osgrid_db->query();
		}
		$inventoryqueries = $opensim->getinventoryqueries($newuser['uuid']);
		if(is_array($inventoryqueries)) {
			foreach($inventoryqueries AS $query) {
				$this->_osgrid_db->setQuery($query);
				$this->_osgrid_db->query();
			}
		}

		// Lets copy the avatar settings if we need
		if(isset($newuser['avatar']) && $newuser['avatar']) {
			$query = sprintf("INSERT INTO Avatars SELECT '%s' AS PrincipalID, Avatars.`Name`, Avatars.`Value` FROM Avatars WHERE Avatars.PrincipalID = '%s'",
								$newuser['uuid'],
								$newuser['avatar']);
			$this->_osgrid_db->setQuery($query);
			$this->_osgrid_db->query();
			// Only one line needs to be updated:
			$query = sprintf("UPDATE Avatars SET Avatars.`Value` = '%1\$s' WHERE Avatars.PrincipalID = '%1\$s' AND Avatars.`Name` = 'UserID'",
								$newuser['uuid']);
			$this->_osgrid_db->setQuery($query);
			$this->_osgrid_db->query();
		}
		return $newuser['uuid'];
	}

	public function deleteuser($joomlaID) {
		$opensimID = $this->getOpenSimUID($joomlaID);
		if(!$this->_osgrid_db) {
			return FALSE;
		}
		$opensim = $this->opensim;
		$deletequeries = $opensim->getdeletequeries($opensimID);
		if(is_array($deletequeries)) {
			foreach($deletequeries AS $db => $dbquery) {
				foreach($dbquery AS $query) {
					$this->_osgrid_db->setQuery($query);
					$this->_osgrid_db->query();
				}
			}
			return TRUE;
		}
	}

	public function getSettings() {
		$settings				= $this->admin_model->getSettingsData();
		$this->_settingsData	= $settings;
		return $this->_settingsData;
	}

	public function regionExists($regionID) {
		$opensim	= $this->opensim;
		$query		= $opensim->regionExistsQuery($regionID);
		$this->_osgrid_db->setQuery($query);
		$existing	= $this->_osgrid_db->loadResult();
		if($existing == $regionID) return TRUE;
		else return FALSE;
	}

	public function getOpenSimUID($joomlaID) {
		$db			= JFactory::getDBO();
		$query		= sprintf("SELECT
							#__opensim_userrelation.opensimID
						FROM
							#__opensim_userrelation
						WHERE
							#__opensim_userrelation.joomlaID = '%d'",$joomlaID);
		$db->setQuery($query);
		$db->query();
		$num_rows	= $db->getNumRows();
		if($num_rows == 1) return $db->loadResult();
		else return null;
	}

	public function getUUID() {
		$db		= JFactory::getDBO();
		$query	= "SELECT UUID()";
		$db->setQuery($query);
		$uuid	= $db->loadResult();
		return $uuid;
	}

	// Takes the parameters from the component (jOpenSim) and creates the object for the external database
	public function getOpenSimGridDB() {
		$db				= JFactory::getDBO();
		$jOpenSim		= JComponentHelper::getComponent('com_opensim',TRUE);
		$this->params	= new JRegistry($jOpenSim->params);

		$dbhost			= JComponentHelper::getParams('com_opensim')->get('opensimgrid_dbhost');
		$dbuser			= JComponentHelper::getParams('com_opensim')->get('opensimgrid_dbuser');
		$dbpass			= JComponentHelper::getParams('com_opensim')->get('opensimgrid_dbpasswd');
		$dbport			= JComponentHelper::getParams('com_opensim')->get('opensimgrid_dbport');
		$dbname			= JComponentHelper::getParams('com_opensim')->get('opensimgrid_dbname');
		// in case of empty grid params, we take the opensim params
		if(!$dbhost) $dbhost = JComponentHelper::getParams('com_opensim')->get('opensim_dbhost');
		if(!$dbuser) $dbuser = JComponentHelper::getParams('com_opensim')->get('opensim_dbuser');
		if(!$dbpass) $dbpass = JComponentHelper::getParams('com_opensim')->get('opensim_dbpasswd');
		if(!$dbport) $dbport = JComponentHelper::getParams('com_opensim')->get('opensim_dbport');
		if(!$dbname) $dbname = JComponentHelper::getParams('com_opensim')->get('opensim_dbname');
		if(!$dbport) $dbport = "3306";

		require_once(JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_opensim".DIRECTORY_SEPARATOR."includes".DIRECTORY_SEPARATOR."opensim.class.php");
		$this->opensim		= new opensim($dbhost,$dbuser,$dbpass,$dbname,$dbport);
		$this->_osgrid_db	= $this->opensim->connect2osgrid();
		return $this->_osgrid_db;
	}

	public function debugprint($variable,$desc="",$exit=0) { // kept here for backwards compatibility
	}

	public function debuglog($variable,$desc="",$exit=1) { // kept here for backwards compatibility
	}
}