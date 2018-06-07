<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2015 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die();
/*jimport('joomla.application.component.model');*/
require_once(JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'opensim.php');

class OpenSimModelMisc extends OpenSimModelOpenSim {
	public $_settingsData;
	public $filename = "misc.php";
	public $view = "misc";
	public $_os_db;
	public $currentsimulators	= array();
	public $connectedsimulators	= array();

	public function __construct() {
		parent::__construct();
		global $mainframe, $option;
		$this->getSettingsData();
	}

	public function toggleTerminal($terminalKey) {
		$db		= JFactory::getDBO();
		$query	= sprintf("SELECT active FROM #__opensim_terminals WHERE terminalKey = '%s'",$terminalKey);
		$db->setQuery($query);
		$active = $db->loadResult();
		if($active == 1) {
			$query = sprintf("UPDATE #__opensim_terminals SET active = '0' WHERE terminalKey = '%s'",$terminalKey);
		} else {
			$query = sprintf("UPDATE #__opensim_terminals SET active = '1' WHERE terminalKey = '%s'",$terminalKey);
		}
		$db->setQuery($query);
		$db->execute();
	}

	public function getTerminal($terminalKey) {
		$db		= JFactory::getDBO();
		$query	= sprintf("SELECT * FROM #__opensim_terminals WHERE terminalKey = '%s'",$terminalKey);
		$db->setQuery($query);
		$terminal = $db->loadAssoc();
		return $terminal;
	}

	public function saveTerminalStatic($terminalKey,$staticValue) {
		$db		= JFactory::getDBO();
		$query	= sprintf("UPDATE #__opensim_terminals SET staticLocation = '%d' WHERE terminalKey = '%s'",$staticValue,$terminalKey);
		$db->setQuery($query);
		$db->execute();
		return TRUE;
	}

	public function saveTerminal($data) {
		if($data['staticLocation'] == 1) $staticLocation = 0;
		else $staticLocation = 1;
		$db		= JFactory::getDBO();
		$query	= sprintf("UPDATE #__opensim_terminals SET
								terminalName = '%s',
								terminalDescription = '%s',
								location_x = '%d',
								location_y = '%d',
								location_z = '%d',
								staticLocation = '%d',
								active = '%d'
							WHERE
								terminalKey = '%s'",
				$data['terminalName'],
				$data['terminalDescription'],
				$data['location_x'],
				$data['location_y'],
				$data['location_z'],
				$staticLocation,
				$data['active'],
				$data['terminalKey']);
		$db->setQuery($query);
		$db->execute();
		return TRUE;
	}

	public function removeTerminal($terminalArray) {
		if(!is_array($terminalArray) || count($terminalArray) == 0) return FALSE;
		$db = JFactory::getDBO();
		foreach($terminalArray AS $terminalKey) {
			$query = sprintf("DELETE FROM #__opensim_terminals WHERE terminalKey = '%s'",$terminalKey);
			$db->setQuery($query);
			$db->execute();
		}
		return TRUE;
	}

	public function getconnectedsimulators() {
		$this->connectedsimulators = $this->opensim->opensimGetConnectedSimulators();
		if(is_array($this->connectedsimulators) && count($this->connectedsimulators) > 0) {
			foreach($this->connectedsimulators AS $connectedsimulator) {
				$this->currentsimulators[] = $connectedsimulator['serverURI'];
			}
		}
		return $this->connectedsimulators;
	}

	public function populateSimulators() {
		$connectedsimulators = $this->getconnectedsimulators();
		$db = JFactory::getDBO();
		if(is_array($connectedsimulators)) {
			foreach($connectedsimulators AS $connectedsimulator) {
				$query = $db->getQuery(true);
				$query->select($db->quoteName('#__opensim_simulators.simulator'));
				$query->from($db->quoteName('#__opensim_simulators'));
				$query->where($db->quoteName('#__opensim_simulators.simulator')." = ".$db->quote($connectedsimulator["serverURI"]));
				$db->setQuery($query);
				$db->execute();
				$found = $db->getNumRows();
				if($found == 0) {
					$newSimulator				= new stdClass();
					$newSimulator->simulator	= $connectedsimulator["serverURI"];
					$newSimulator->ordering		= 999;
					$result = $db->insertObject('#__opensim_simulators', $newSimulator);
				}
			}
		}
	}

	public function getSimulators() {
		if(count($this->currentsimulators) == 0) $this->getconnectedsimulators();
		$this->populateSimulators();
		$db			= JFactory::getDBO();
		$query		= "SELECT #__opensim_simulators.* FROM #__opensim_simulators ORDER BY #__opensim_simulators.ordering";
		$db->setQuery($query);
		$simulators	= $db->loadAssocList();
		if(is_array($simulators) && count($simulators) > 0) {
			foreach($simulators AS $key => $simulator) {
				foreach($this->connectedsimulators AS $connectedsimulator) {
					if($connectedsimulator['serverURI'] == $simulator['simulator']) {
						$simulators[$key]['connected']	= TRUE;
						$simulators[$key]['regions']	= $connectedsimulator['regions'];
						break;
					} else {
						$simulators[$key]['connected']	= FALSE;
						$simulators[$key]['regions']	= "";
					}
				}
			}
		}
		return $simulators;
	}

	public function getSimulator($simulator) {
		$db			= JFactory::getDBO();
		$query		= "SELECT #__opensim_simulators.* FROM #__opensim_simulators WHERE #__opensim_simulators.simulator = ".$db->quote($simulator);
		$db->setQuery($query);
		$db->execute();
		$foundsimulator = $db->getNumRows();
		if($foundsimulator == 1) {
			$simulator	= $db->loadAssoc();
		} else {
			$simulator = FALSE;
		}
		return $simulator;
	}

	public function saveSimulators() {
		$simulatorarray		= JFactory::getApplication()->input->get('simulator','','raw');
		$aliasarray			= JFactory::getApplication()->input->get('alias','','raw');
		$radminportarray	= JFactory::getApplication()->input->get('radminport','','raw');
		$radminpwdarray		= JFactory::getApplication()->input->get('radminpwd','','raw');
		if(is_array($simulatorarray) && count($simulatorarray) > 0) {
			$db				= JFactory::getDbo();
			foreach($simulatorarray AS $key => $simulator) {
				$updateSim	= new stdClass();
				$updateSim->simulator	= $simulator;
				$updateSim->alias		= (array_key_exists($key,$aliasarray) && $aliasarray[$key]) ? $aliasarray[$key]:null;
				$updateSim->radminport	= (array_key_exists($key,$radminportarray) && $radminportarray[$key]) ? $radminportarray[$key]:null;
				$updateSim->radminpwd	= (array_key_exists($key,$radminpwdarray) && $radminpwdarray[$key]) ? $radminpwdarray[$key]:null;
				$condition				= array("simulator");
				$db->updateObject('#__opensim_simulators', $updateSim, $condition,true);
			}
		}
	}

	public function saveSimulatorOrder() {
		$order	= JFactory::getApplication()->input->get('order');
		$simulatorarray	= JFactory::getApplication()->input->get('simulator','','raw');
		$counter = 0;
		$db				= JFactory::getDbo();
		foreach($simulatorarray AS $simulator) {
			$updateSim	= new stdClass();
			$updateSim->simulator	= $simulator;
			$updateSim->ordering	= $order[$counter];
			$condition				= array("simulator");
			$db->updateObject('#__opensim_simulators', $updateSim, $condition,true);
			$counter++;
		}
	}

	public function removeSimulators() {
		$cid	= JFactory::getApplication()->input->get('cid','','raw');
		if(is_array($cid) && count($cid) > 0) {
			$db	= JFactory::getDbo();
			foreach($cid AS $removesim) {
				$query = $db->getQuery(true);
				$condition = array($db->quoteName('simulator').' = '.$db->quote($removesim));
				$query->delete($db->quoteName('#__opensim_simulators'));
				$query->where($condition);
				$db->setQuery($query);
				$db->execute();
			}
		}
	}
}
?>