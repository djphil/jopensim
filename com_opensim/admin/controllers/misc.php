<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2015 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class OpenSimControllermisc extends OpenSimController {
	public function __construct() {
		parent::__construct();
	}

	public function sendoutmessage() {
		$data = JFactory::getApplication()->input->request->getArray();;
		$message = $data['message'];
		$model = $this->getModel('misc');
		$settings = $model->getSettingsData();
		$opensim = $model->opensim;
		$opensim->RemoteAdmin($settings['remotehost'],$settings['remoteport'],$settings['remotepasswd']);
		$command = "admin_broadcast";
		$params = array('message' => $message);
		$returnvalue = $opensim->SendCommand($command,$params);
		$debug = var_export($returnvalue,TRUE);
		if(!is_array($returnvalue)) {
			JFactory::getApplication()->enqueueMessage(JText::_('REMOTEADMIN_NORESPONSE'),"error");
		} elseif(array_key_exists("error",$returnvalue) && $returnvalue['error']) {
			$message = JText::_('REMOTEADMIN_ERROR').": ".$returnvalue['error'];
			JFactory::getApplication()->enqueueMessage($message,"error");
		} else {
			$messages = array();
			$message = JText::_('MESSAGESENT')." (".JText::_('REMOTEADMIN_RESPONDED').": ";
			foreach($returnvalue AS $key => $val) {
				$messages[] = $key."=".$val;
			}
			$message .= implode(",",$messages).")";
			JFactory::getApplication()->enqueueMessage($message,"message");
		}
		$this->setRedirect('index.php?option=com_opensim&view=misc');
	}

	public function createregionsend() {
		$data				= JFactory::getApplication()->input->request->getArray();;
		$region_name		= $data['region_name'];
		$listen_ip			= $data['listen_ip'];
		$listen_port		= $data['listen_port'];
		$external_address	= $data['external_address'];
		$region_x			= intval($data['region_x']);
		$region_y			= intval($data['region_y']);
		$public				= $data['public'];
		$voice_enabled		= $data['voice'];
		$estate_name		= $data['estate_name'];
		$persist			= 'true';

		$model				= $this->getModel('misc');
		$settings			= $model->getSettingsData();
		$opensim			= $model->opensim;

		$opensim->RemoteAdmin($settings['remotehost'],$settings['remoteport'],$settings['remotepasswd']);
		$command		= "admin_create_region";
		$params			= array('region_name' => $region_name,
							'listen_ip' => $listen_ip,
							'listen_port' => $listen_port,
							'external_address' => $external_address,
							'region_x' => $region_x,
							'region_y' => $region_y,
							'public' => $public,
							'enable_voice' => $voice,
							'persist' => $persist,
							'region_file' => 'jOpenSim.ini',
							'estate_name' => $estate_name);
		$returnvalue	= $opensim->SendCommand($command,$params);
		if(!is_array($returnvalue)) {
			JFactory::getApplication()->enqueueMessage(JText::_('REMOTEADMIN_NORESPONSE'),"error");
		} elseif(array_key_exists("error",$returnvalue) && $returnvalue['error']) {
			$message	= JText::_('REMOTEADMIN_ERROR').": ".$returnvalue['error'];
			JFactory::getApplication()->enqueueMessage($message,"error");
		} else {
			$messages	= array();
			$message	= JText::_('MESSAGESENT')." (".JText::_('REMOTEADMIN_RESPONDED').": ";
			foreach($returnvalue AS $key => $val) {
				$messages[] = $key."=".$val;
			}
			$message .= implode(",",$messages).")";
			JFactory::getApplication()->enqueueMessage($message,"message");
		}
		$this->setRedirect('index.php?option=com_opensim&view=misc');
	}
	public function savewelcomemessage() {
		$model = $this->getModel('misc');
		$model->updateWelcome();
		$this->setRedirect('index.php?option=com_opensim&view=misc',JText::_('WELCOMEUPDATED'));
	}

	public function removewelcome() {
		$model = $this->getModel('misc');
		$model->removeWelcome();
		$this->setRedirect('index.php?option=com_opensim&view=misc',JText::_('WELCOMEUPDATED'));
	}

	public function toggleTerminal() {
		$terminalKey = JFactory::getApplication()->input->get('terminalKey');
		$model = $this->getModel('misc');
		$model->toggleTerminal($terminalKey);
		$this->setRedirect('index.php?option=com_opensim&view=misc&task=terminals');
	}

	public function saveTerminalStatic() {
		$terminalKey = JFactory::getApplication()->input->get('terminalKey');
		$staticValue = JFactory::getApplication()->input->get('staticValue');
		$model = $this->getModel('misc');
		$model->saveTerminalStatic($terminalKey,$staticValue);
		$this->setRedirect('index.php?option=com_opensim&view=misc&task=terminals',JText::_('AUTOUPDATED'));
	}

	public function saveTerminal() {
		$postdata = JFactory::getApplication()->input->request->getArray();;
		$model = $this->getModel('misc');
		$model->saveTerminal($postdata);
		$this->setRedirect('index.php?option=com_opensim&view=misc&task=terminals',JText::_('TERMINALUPDATED'));
	}

	public function deleteTerminal() {
		$terminalArray = JFactory::getApplication()->input->get('checkTerminal');
		$model = $this->getModel('misc');
		$model->removeTerminal($terminalArray);
		$this->setRedirect('index.php?option=com_opensim&view=misc&task=terminals',JText::_('TERMINALREMOVED'));
	}
}
?>
