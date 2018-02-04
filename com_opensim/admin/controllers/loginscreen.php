<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2017 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class OpenSimControllerLoginscreen extends OpenSimController {
	public function __construct() {
		$this->model	= $this->getModel('loginscreen');
		parent::__construct();
	}

	public function insertposition() {
		$this->model	= $this->getModel('loginscreen');
		$result = $this->model->insertPosition();
		if(is_array($result) && array_key_exists("error",$result)) {
			$type = "error";
			$message = $result['error'];
			$this->setRedirect('index.php?option=com_opensim&view=loginscreen&task=newposition',$message,$type);
		} else {
			$type = "message";
			$message = JText::_('JOPENSIM_LOGINSCREEN_SAVEPOS_OK');
			$this->setRedirect('index.php?option=com_opensim&view=loginscreen',$message,$type);
		}
	}

	public function setPosInvisible() {
		$this->model	= $this->getModel('loginscreen');
		$posID = JFactory::getApplication()->input->request->get('id');
		$this->model->setPositionVisibility($posID,0);
		$this->setRedirect('index.php?option=com_opensim&view=loginscreen');
	}

	public function setPosVisible() {
		$this->model	= $this->getModel('loginscreen');
		$posID = JFactory::getApplication()->input->request->get('id');
		$this->model->setPositionVisibility($posID,1);
		$this->setRedirect('index.php?option=com_opensim&view=loginscreen');
	}

	public function savePos() {
		$this->model	= $this->getModel('loginscreen');
		$this->model->savePosition();
		$this->setRedirect('index.php?option=com_opensim&view=loginscreen');
	}

	public function editposition() {
		$jinput			= JFactory::getApplication()->input;
		$boxchecked	= $jinput->get('boxchecked');
//		error_log("loginscreen controller ".__LINE__.": boxchecked = ".$boxchecked);
		if($boxchecked != 1) {
			$application = JFactory::getApplication();
			$application->enqueueMessage(JText::_('JOPENSIM_LOGINSCREEN_EDITPOS_ONLY1'), 'error');
			$this->setRedirect('index.php?option=com_opensim&view=loginscreen');
		} else {
			$this->model	= $this->getModel('loginscreen');
			$view	= $this->getView('loginscreen','html');
			$view->setModel($this->model);
			$this->display();
		}
	}

	public function updateposition() {
		$this->model	= $this->getModel('loginscreen');
		$result			= $this->model->updatePosition();
		if(is_array($result) && array_key_exists("error",$result)) {
			$jrequest	= JFactory::getApplication()->input->request;
			$data		= $jrequest->getArray(array('jform' => array('id' => 'integer')));
			$id			= $data['jform']['id'];
			$type = "error";
			$message = $result['error'];
			$this->setRedirect('index.php?option=com_opensim&view=loginscreen&task=editposition&boxchecked=1&checkPosition[0]='.$id,$message,$type);
		} else {
			$type = "message";
			$message = JText::_('JOPENSIM_LOGINSCREEN_SAVEPOS_OK');
			$this->setRedirect('index.php?option=com_opensim&view=loginscreen',$message,$type);
		}
	}

	public function deleteposition() {
		$deletepositions = JFactory::getApplication()->input->request->get('checkPosition');
		if(is_array($deletepositions) && count($deletepositions) > 0) {
			$this->model	= $this->getModel('loginscreen');
			foreach($deletepositions AS $deleteposition) {
				$this->model->deleteposition($deleteposition);
			}
		}
		$this->setRedirect('index.php?option=com_opensim&view=loginscreen');
	}
}
?>
