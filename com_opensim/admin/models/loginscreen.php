<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2017 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die();
/*jimport('joomla.application.component.model');*/
require_once(JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'opensim.php');

class OpenSimModelLoginscreen extends OpenSimModelOpenSim {
	var $_settingsData;
	var $filename = "loginscreen.php";
	var $view = "misc";
	var $_os_db;

	public function __construct() {
		parent::__construct();
	}

	public function getPositions() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select('#__opensim_loginscreen.*');
		$query->from($db->quoteName('#__opensim_loginscreen'));
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

	public function getPosition($id) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select('#__opensim_loginscreen.*');
		$query->from($db->quoteName('#__opensim_loginscreen'));
		$query->where($db->quoteName('#__opensim_loginscreen.id').' = '.$db->quote($id));
		$db->setQuery($query);
		$position = $db->loadAssoc();
		return $position;
	}

	public function getMaxId() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select('MAX('.$db->quoteName('#__opensim_loginscreen.id').')');
		$query->from($db->quoteName('#__opensim_loginscreen'));
		$db->setQuery($query);
		$maxid = $db->loadResult();
		$maxid++;
		return $maxid;
	}

	public function getTable($type = 'loginscreen', $prefix = 'jOpenSimTable', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);
	}

	public function insertPosition() {
		$jrequest	= JFactory::getApplication()->input->request;
		$data		= $jrequest->getArray(array('jform' => array(
						'positionname'	=> 'string',
						'alignH'		=> 'string',
						'posX'			=> 'integer',
						'alignV'		=> 'string',
						'posY'			=> 'integer',
						'zindex'		=> 'integer')));
		$existing	= $this->checkexisting($data['jform']['positionname']);
		if($existing === TRUE) {
			$retval['error'] = JText::sprintf('JOPENSIM_LOGINSCREEN_POSITIONNAME_EXISTING',$data['jform']['positionname']);
			return $retval;
		}
		$db						= JFactory::getDbo();
		$zindex 				= ($data['jform']['zindex']) ? $data['jform']['zindex']:100;
		$newPos					= new stdClass();
		$newPos->positionname	= $data['jform']['positionname'];
		$newPos->alignH			= $data['jform']['alignH'];
		$newPos->posX			= $data['jform']['posX'];
		$newPos->alignV			= $data['jform']['alignV'];
		$newPos->posY			= $data['jform']['posY'];
		$newPos->zindex			= $zindex;
		$result					= $db->insertObject('#__opensim_loginscreen', $newPos);

		if($result !== TRUE) {
			return array('error' => "Error while creating position ".$data['jform']["positionname"]);
		}
		return TRUE;
	}

	public function updatePosition() {
		$jrequest	= JFactory::getApplication()->input->request;
		$data		= $jrequest->getArray(array('jform' => array(
						'id'			=> 'integer',
						'positionname'	=> 'string',
						'alignH'		=> 'string',
						'posX'			=> 'integer',
						'alignV'		=> 'string',
						'posY'			=> 'integer',
						'zindex'		=> 'integer')));
		$existing	= $this->checkexisting($data['jform']['positionname'],$data['jform']['id']);
		if($existing === TRUE) {
			$retval['error'] = JText::sprintf('JOPENSIM_LOGINSCREEN_POSITIONNAME_EXISTING',$data['jform']['positionname']);
			return $retval;
		}
		$db							= JFactory::getDbo();
		$zindex 					= ($data['jform']['zindex']) ? $data['jform']['zindex']:100;
		$updatePos					= new stdClass();
		$updatePos->id				= $data['jform']['id'];
		$updatePos->positionname	= $data['jform']['positionname'];
		$updatePos->alignH			= $data['jform']['alignH'];
		$updatePos->posX			= $data['jform']['posX'];
		$updatePos->alignV			= $data['jform']['alignV'];
		$updatePos->posY			= $data['jform']['posY'];
		$updatePos->zindex			= $zindex;
		$condition					= array("id");
		$result						= $db->updateObject('#__opensim_loginscreen', $updatePos, $condition);

		if($result !== TRUE) {
			return array('error' => "Error while updating position ".$data['jform']["positionname"]);
		}
		return TRUE;
	}

	public function deleteposition($id) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$conditions = array($db->quoteName('id').' = '.$db->quote($id));
		$query->delete($db->quoteName('#__opensim_loginscreen'));
		$query->where($conditions);
		$db->setQuery($query);
		$result = $db->execute();
	}

	public function checkexisting($positionname,$id = null) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select($db->quoteName('#__opensim_loginscreen.id'));
		$query->from($db->quoteName('#__opensim_loginscreen'));
		$query->where($db->quoteName('#__opensim_loginscreen.positionname').' = '.$db->quote($positionname));
		if($id) {
			$query->where($db->quoteName('#__opensim_loginscreen.id').' != '.$db->quote($id));
		}
		$db->setQuery($query);
		$db->execute();
		$existing = $db->getNumRows();
		if($existing > 0) return TRUE;
		else return FALSE;
	}

	public function setPositionVisibility($id,$visibility) {
		$db					= JFactory::getDbo();
		$updatePos			= new stdClass();
		$updatePos->id		= $id;
		$updatePos->active	= $visibility;
		$condition			= array("id");
		$result				= $db->updateObject('#__opensim_loginscreen', $updatePos, $condition);
		
	}

	public function savePosition() {
		$postype	= JFactory::getApplication()->input->request->get('posType');
		$id			= JFactory::getApplication()->input->request->get('posID');
		if($postype	== "setX") {
			$data	= JFactory::getApplication()->input->request->getArray(array('jform' => array('alignH' => 'string', 'distance' => 'int')));
			$align	= $data['jform']['alignH'];
		} else {
			$data	= JFactory::getApplication()->input->request->getArray(array('jform' => array('alignV' => 'string', 'distance' => 'int')));
			$align	= $data['jform']['alignV'];
		}
//		error_log(var_export($data,TRUE));
		$this->savePos($id,$postype,$align,$data['jform']['distance']);
	}

	public function savePos($id,$type,$align,$distance) {
		$debug = func_get_args();
//		error_log(var_export($debug,TRUE));
		$db					= JFactory::getDbo();
		$updatePos			= new stdClass();
		$updatePos->id		= $id;
		if($type == "setX") {
			$updatePos->alignH	= $align;
			$updatePos->posX	= $distance;
		} else {
			$updatePos->alignV	= $align;
			$updatePos->posY	= $distance;
		}
		$condition			= array("id");
		$result				= $db->updateObject('#__opensim_loginscreen', $updatePos, $condition);
	}

	public function save($data) {
		return parent::save($data);
	}

	public function getForm($data = array(), $loadData = true) {
		// Get the form.
		$form = $this->loadForm('com_opensim.loginscreen', 'loginscreen', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		return $form;
	}
}
?>
