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

class JFormFieldjopensimregionlist extends JFormFieldList {
	/**
	* The field type.
	*
	* @var         string
	*/

	protected $type = 'jopensimregionlist';

	/**
	* Method to get a list of options for a list input.
	*/
	public function getOptions() {
		$opensimgrid_dbhost		= JComponentHelper::getParams('com_opensim')->get('opensimgrid_dbhost');
		$opensimgrid_dbuser		= JComponentHelper::getParams('com_opensim')->get('opensimgrid_dbuser');
		$opensimgrid_dbpasswd	= JComponentHelper::getParams('com_opensim')->get('opensimgrid_dbpasswd');
		$opensimgrid_dbname		= JComponentHelper::getParams('com_opensim')->get('opensimgrid_dbname');
		$opensimgrid_dbport		= JComponentHelper::getParams('com_opensim')->get('opensimgrid_dbport');
		$options = array();
		$options[0] = new stdClass();
		$options[0]->value	= "";
		$options[0]->text	= JText::_('JOPENSIM_USERHOME_SELECTREGION');
		$zaehler = count($options);

		if(!$opensimgrid_dbport) $opensimgrid_dbport = "3306";
		$this->opensim = new opensim($opensimgrid_dbhost,$opensimgrid_dbuser,$opensimgrid_dbpasswd,$opensimgrid_dbname,$opensimgrid_dbport,TRUE);
		
		if(!$opensimgrid_dbhost || !$opensimgrid_dbuser || !$opensimgrid_dbpasswd || !$opensimgrid_dbname) {
			if(!array_key_exists($zaehler,$options) || !is_object($options[$zaehler])) $options[$zaehler] = new stdClass();
			$options[$zaehler]->value	= "";
			$options[$zaehler]->text	= JText::_('JOPENSIM_USERHOME_REGION_NOTCONNECTED');
		} else {
			$regions = $this->getRegions();
			if(count($regions) == 0) {
				if(!array_key_exists($zaehler,$options) || !is_object($options[$zaehler])) $options[$zaehler] = new stdClass();
				$options[$zaehler]->value	= "";
				$options[$zaehler]->text	= JText::_('JOPENSIM_USERHOME_REGION_NOTFOUND');
			} else {
				foreach($regions as $region) {
					$zaehler = count($options);
					$options[$zaehler] = new stdClass();
					$options[$zaehler]->value	= $region['uuid'];
					$options[$zaehler]->text	= $region['regionName'];
				}
			}
		}
		return $options;
	}

	public function getRegions() {
		$db = $this->opensim->_osgrid_db;
		if(is_object($db)) {
			$query = $db->getQuery(true);
			$query->select("regions.*");
			$query->from("regions");
			$query->order("regions.regionName ASC");
			$db->setQuery((string)$query);
			$this->regions = $db->loadAssocList();
			return $this->regions;
		} else {
			return array();
		}
	}
}
?>