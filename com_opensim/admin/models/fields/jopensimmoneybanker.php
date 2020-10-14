<?php
/*
 * @component jOpenSim Component
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */

// No direct access to this file
defined('_JEXEC') or die;
 
// import the list field type
jimport('joomla.form.helper');
jimport('joomla.application.component.helper');
JFormHelper::loadFieldClass('list');
require_once(JPATH_ROOT.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_opensim".DIRECTORY_SEPARATOR."includes".DIRECTORY_SEPARATOR."opensim.class.php");

class JFormFieldjopensimmoneybanker extends JFormFieldList {
	/**
	* The field type.
	*
	* @var         string
	*/

	protected $type = 'jopensimmoneybanker';

	/**
	* Method to get a list of options for a list input.
	*/
	public function getOptions() {
		$opensimgrid_dbhost		= JComponentHelper::getParams('com_opensim')->get('opensimgrid_dbhost');
		$opensimgrid_dbuser		= JComponentHelper::getParams('com_opensim')->get('opensimgrid_dbuser');
		$opensimgrid_dbpasswd	= JComponentHelper::getParams('com_opensim')->get('opensimgrid_dbpasswd');
		$opensimgrid_dbname		= JComponentHelper::getParams('com_opensim')->get('opensimgrid_dbname');
		$opensimgrid_dbport		= JComponentHelper::getParams('com_opensim')->get('opensimgrid_dbport');
		if(!$opensimgrid_dbhost || !$opensimgrid_dbuser || !$opensimgrid_dbpasswd || !$opensimgrid_dbname) {
			$this->element['label']			= "";
			$this->element['description']	= "";
			return null;
		} else {
			if(!$opensimgrid_dbport) $opensimgrid_dbport = "3306";
			$this->opensim = new opensim($opensimgrid_dbhost,$opensimgrid_dbuser,$opensimgrid_dbpasswd,$opensimgrid_dbname,$opensimgrid_dbport,TRUE);
			$bankerusers = $this->getUserDataList();
			if(count($bankerusers) == 0) {
				$this->element['label']			= "";
				$this->element['description']	= "";
				return null;
			} else {
				$options = array();
				if (count($bankerusers) > 0) {
					foreach($bankerusers as $bankeruser) {
						$zaehler = count($options);
						$options[$zaehler] = new stdClass();
						$options[$zaehler]->value	= $bankeruser['userid'];
						$options[$zaehler]->text	= $bankeruser['firstname']." ".$bankeruser['lastname'];
					}
				}
			}
		}
		return $options;
	}

	public function getUserDataList() {
		$filter['UserLevel'] = -2;
		$this->bankerquery = $this->opensim->getUserQuery($filter,null,null,1);
		try {
			$db = $this->opensim->_osgrid_db;
			if(is_object($db)) {
				$db->setQuery($this->bankerquery);
				$this->banker = $db->loadAssocList();
				return $this->banker;
			} else {
				return array();
			}
		} catch (Exception $e) {
			return array();
		}
	}
}
?>