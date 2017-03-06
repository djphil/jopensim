<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2017 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('JPATH_PLATFORM') or die;

if(!defined('DS')) define("DS",DIRECTORY_SEPARATOR);

JFormHelper::loadFieldClass('list');

class JFormFieldAvatarlist extends JFormFieldList {
	protected $type = 'Avatarlist';
	protected $opensim;

	protected function getOptions() {
		$avatars = array();
		if(is_file(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_opensim'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'opensim.class.php')) {
			require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_opensim'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'opensim.class.php');
			$this->initOpenSim();
			if(is_object($this->opensim)) {
				$filter['usertable_field_UserLevel'] = "-3";
				$userquery = $this->opensim->getUserListQuery($filter,"exact");
				$db = $this->getOSdb();
				$db->setQuery($userquery);
				$userlist = $db->loadAssocList();
				if(is_array($userlist) && count($userlist) > 0) {
					foreach($userlist AS $user) {
						$zaehler = count($avatars);
						$avatars[$zaehler]['value'] = $user['PrincipalID'];
						if($user['UserTitle']) $avatars[$zaehler]['text'] = $user['UserTitle'];
						else $avatars[$zaehler]['text'] = $user['FirstName']." ".$user['LastName'];
					}
				} else {
					$avatars[0]['value'] = "0";
					$avatars[0]['text'] = JText::_('PLG_JOPENSIMREGISTER_ERROR_NOAVATARS');;
				}
			} else {
				$avatars[0]['value'] = "-1";
				$avatars[0]['text'] = JText::_('PLG_JOPENSIMREGISTER_ERRORINIT');
			}


		} else {

			$avatars[0]['value'] = "-1";
			$avatars[0]['text'] = JText::_('PLG_JOPENSIMREGISTER_ERRORDETECT');

		}
		// Initialize variables.
		$options = array();
		

		foreach ($avatars as $option) {

			// Create a new option object based on the <option /> element.
			$tmp = JHtml::_(
				'select.option', (string) $option['value'].":".$option['text'], trim((string) $option['text']), 'value', 'text'
			);
			$tmp->title = $option['value'];

			// Set some option attributes.
			if(array_key_exists("class",$option)) $tmp->class = (string) $option['class']." hastip";
			else $tmp->class = "hastip";

			// Set some JavaScript option attributes.
			if(array_key_exists("onclick",$option)) $tmp->onclick = (string) $option['onclick'];

			// Add the option object to the result set.
			$options[] = $tmp;
		}

		reset($options);

		return $options;
	}

	public function initOpenSim() {
		$params			= &JComponentHelper::getParams('com_opensim');
		$this->params	= $params;

		$osdbhost		= ($params->get('opensimgrid_dbhost'))   ? $params->get('opensimgrid_dbhost'):$params->get('opensim_dbhost');
		$osdbuser		= ($params->get('opensimgrid_dbuser'))   ? $params->get('opensimgrid_dbuser'):$params->get('opensim_dbuser');
		$osdbpasswd		= ($params->get('opensimgrid_dbpasswd')) ? $params->get('opensimgrid_dbpasswd'):$params->get('opensim_dbpasswd');
		$osdbname		= ($params->get('opensimgrid_dbname'))   ? $params->get('opensimgrid_dbname'):$params->get('opensim_dbname');
		$osdbport		= ($params->get('opensimgrid_dbport'))   ? $params->get('opensimgrid_dbport'):$params->get('opensim_dbport');
		$this->opensim	= new opensim($osdbhost,$osdbuser,$osdbpasswd,$osdbname,$osdbport);
	}

	public function getOSdb() {
		return $this->opensim->_osgrid_db;
	}
}