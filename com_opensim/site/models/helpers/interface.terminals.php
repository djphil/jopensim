<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die();

// require_once(JPATH_COMPONENT_SITE.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'opensim.php');

class opensimModelInterfaceTerminals extends opensimModelInterface {

	public $debug	= FALSE;

	public function __construct() {
		parent::__construct();
		if($this->jdebug['terminal']) $this->debug	= TRUE;
	}

	public function initMethods() { // empty this this method to avoid endless loops
		return;
	}

	public function initAddons() { // empty this this method to avoid endless loops
		return;
	}

	public function identifyTerminal($data) {
		$db = JFactory::getDBO();
		// first clean up old ident requests
		if($this->settings['addons_identminutes'] > 0) {
			$query = sprintf("DELETE FROM #__opensim_inworldident WHERE created < DATE_SUB(NOW(), INTERVAL %d MINUTE)",$this->settings['addons_identminutes']);
			$db->setQuery($query);
			$db->execute();
		}
		// first check if uuid has already a joomla relation
		$query = sprintf("SELECT joomlaID FROM #__opensim_userrelation WHERE opensimID = '%s'",$data['identKey']);
		$db->setQuery($query);
		$db->execute();
		$num_rows = $db->getNumRows();
		if($num_rows > 0) {
			$retval = JText::_('INWORLDALREADYIDENTIFIED');
		} else {
			// no relation existing, check if some inworld ident is prepared
			$query = sprintf("SELECT joomlaID FROM #__opensim_inworldident WHERE inworldIdent = '%s'",$data['identString']);
			$db->setQuery($query);
			$db->execute();
			$num_rows = $db->getNumRows();
			if($num_rows > 0) {
				$joomlaId = $db->loadResult();
				// this should actually never happen, but better check this as well:
				$query = sprintf("SELECT opensimID FROM #__opensim_userrelation WHERE joomlaID = '%d'",$joomlaId);
				$db->setQuery($query);
				$db->execute();
				$num_rows = $db->getNumRows();
				if($num_rows > 0) { // something went completely wrong here, hope this causes ppl to report a bug
					$retval = "Error while double check joomlaID ".$joomlaId." (could be a bug)! Please contact the Gridmanager and/or FoTo50 at the support forum at http://www.jopensim.com";
				} else { // Everything ok, inworld account identified
					// Update the relation table
					$query = sprintf("INSERT INTO #__opensim_userrelation (joomlaID,opensimID) VALUES ('%1\$d','%2\$s')",$joomlaId,$data['identKey']);
					$db->setQuery($query);
					$db->execute();
					// and delete from the ident table
					$query = sprintf("DELETE FROM #__opensim_inworldident WHERE inworldIdent = '%s'",$data['identString']);
					$db->setQuery($query);
					$db->execute();
					$retval = JText::_('IDENTIFYINWORLDSUCCESS');
				}
			} else { // no inworld ident found, give a proper message
				$retval = JText::_('IDENTIFYINWORLDFAILED');
			}
		}
		if($this->debug === TRUE) $this->debuglog("Response for ".__FUNCTION__.":\n\n".$retval);
		return $retval;
	}

	public function registerTerminal($remoteip) {
		$terminalDescription	= JFactory::getApplication()->input->get('terminalDescription','','string');
		$terminalUrl			= JFactory::getApplication()->input->get('myurl','','raw');
		$regionString			= $_SERVER['HTTP_X_SECONDLIFE_REGION'];
		$locationString			= $_SERVER['HTTP_X_SECONDLIFE_LOCAL_POSITION'];
		$terminalKey			= $_SERVER['HTTP_X_SECONDLIFE_OBJECT_KEY'];
		$terminalName			= $_SERVER['HTTP_X_SECONDLIFE_OBJECT_NAME'];
		$region_suchmuster		= "/([^\(])*/";
		preg_match($region_suchmuster,$regionString,$treffer);
		$region = trim($treffer[0]);
		$location_suchmuster	= "/[^\d]*([\d\.]*)[^\d]*([\d\.]*)[^\d]*([\d\.]*)/";
		preg_match_all($location_suchmuster,$locationString,$treffer,PREG_SET_ORDER);
		$location_x = $this->roundoff($treffer[0][1],0);
		$location_y = $this->roundoff($treffer[0][2],0);
		$location_z = $this->roundoff($treffer[0][3],0);
		$db = JFactory::getDBO();
		$query = sprintf("SELECT staticLocation FROM #__opensim_terminals WHERE terminalKey = '%s'",$terminalKey);
		$db->setQuery($query);
		$db->execute();
		$num_rows = $db->getNumRows();
		if($num_rows > 0) {
			$terminalstatic = $db->loadResult();
			if($terminalstatic == "1") {
				$retval = "Terminal found, but not updated due to static setting!";
			} else {
				$query = sprintf("UPDATE #__opensim_terminals SET
									terminalName = '%s',
									terminalDescription = '%s',
									terminalUrl = '%s',
									region = '%s',
									location_x = '%d',
									location_y = '%d',
									location_z = '%d'
								WHERE
									terminalKey = '%s'",
					$terminalName,
					$terminalDescription,
					$terminalUrl,
					$region,
					$location_x,
					$location_y,
					$location_z,
					$terminalKey);
				$db->setQuery($query);
				$db->execute();
				$retval = "Terminal found and sucessfully updated!";
			}
		} else {
			$query = sprintf("INSERT INTO #__opensim_terminals
									(terminalName,terminalDescription,terminalKey,terminalUrl,region,location_x,location_y,location_z)
								VALUES
									('%s','%s','%s','%s','%s','%d','%d','%d')",
					$terminalName,
					$terminalDescription,
					$terminalKey,
					$terminalUrl,
					$region,
					$location_x,
					$location_y,
					$location_z);
			$db->setQuery($query);
			$db->execute();
			$retval = "Terminal sucessfully registered!";
		}
		if($this->debug === TRUE) $this->debuglog("Response for ".__FUNCTION__.":\n\n".$retval,__FUNCTION__);
		return $retval;
	}

	public function setStateTerminal($remoteip) {
		if($this->debug === TRUE) $this->debuglog("Terminal setState fired from ".$remoteip." at line ".__LINE__." in ".__FILE__,__FUNCTION__);
		$terminalState	= JFactory::getApplication()->input->get('state');
		$terminalKey	= $_SERVER['HTTP_X_SECONDLIFE_OBJECT_KEY'];
		$query			= sprintf("UPDATE #__opensim_terminals SET active = '%d' WHERE terminalKey = '%s'",$terminalState,$terminalKey);
		$db				= JFactory::getDBO();
		$db->setQuery($query);
		$db->execute();
		if($terminalState == 1) $retval = JText::_('TERMINALSETVISIBLE');
		else $retval = JText::_('TERMINALSETINVISIBLE');
		if($this->debug === TRUE) $this->debuglog("Response for ".__FUNCTION__.":\n\n".$retval,__FUNCTION__);
		return $retval;
	}

}
?>