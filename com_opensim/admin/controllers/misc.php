<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
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
		if($data['radminsystem'] == "multiple") {
			$simulator = $model->getSimulator($data['simulator']);
			if($simulator) {
				$remotehost 	= $this->getHostFrom($data['simulator']);
				$remoteport 	= ($simulator['radminport']) ? $simulator['radminport']:$this->getPortFrom($data['simulator']);
				$remotepasswd	= ($simulator['radminpwd']) ? $simulator['radminpwd']:$settings['remotepasswd'];
			} else {
				$remotehost 	= $this->getHostFrom($data['simulator']);
				$remoteport 	= $this->getPortFrom($data['simulator']);
				$remotepasswd	= $settings['remotepasswd'];
			}
		} else {
			$remotehost 		= $settings['remotehost'];
			$remoteport 		= $settings['remoteport'];
			$remotepasswd		= $settings['remotepasswd'];
		}
		$opensim->RemoteAdmin($remotehost,$remoteport,$remotepasswd);
		$command = "admin_broadcast";
		$params = array('message' => $message);
		$returnvalue = $opensim->SendCommand($command,$params);
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
		$this->setRedirect('index.php?option=com_opensim&view=misc&task=sendmessage');
	}

	public function createregionsend() {
		$data				= JFactory::getApplication()->input->request->getArray();;
		$region_name		= $data['region_name'];
		$listen_ip			= $data['listen_ip'];
		$listen_port		= $data['listen_port'];
//		$external_address	= $data['external_address'];
		$region_x			= intval($data['region_x']);
		$region_y			= intval($data['region_y']);
		$public				= $data['public'];
		$voice_enabled		= $data['voice'];
		$estate_name		= $data['estate_name'];
		$persist			= 'true';

		$model				= $this->getModel('misc');
		$settings			= $model->getSettingsData();
		$opensim			= $model->opensim;

		if($data['radminsystem'] == "multiple") {
			$simulator = $model->getSimulator($data['simulator']);
			if($simulator) {
				$remotehost 		= $this->getHostFrom($data['simulator']);
				$external_address	= $remotehost;
				$remoteport 		= ($simulator['radminport']) ? $simulator['radminport']:$this->getPortFrom($data['simulator']);
				$remotepasswd		= ($simulator['radminpwd']) ? $simulator['radminpwd']:$settings['remotepasswd'];
			} else {
				$remotehost 		= $this->getHostFrom($data['simulator']);
				$remoteport 		= $this->getPortFrom($data['simulator']);
				$remotepasswd		= $settings['remotepasswd'];
				$external_address	= $remotehost;
			}
		} else {
			$remotehost 			= $settings['remotehost'];
			$remoteport 			= $settings['remoteport'];
			$remotepasswd			= $settings['remotepasswd'];
			$external_address	= $remotehost;
		}
		$opensim->RemoteAdmin($remotehost,$remoteport,$remotepasswd);
		$command		= "admin_create_region";
		$params			= array('region_name' => $region_name,
							'listen_ip' => $listen_ip,
							'listen_port' => $listen_port,
							'external_address' => $external_address,
							'region_x' => $region_x,
							'region_y' => $region_y,
							'public' => $public,
							'enable_voice' => $voice_enabled,
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
		if($settings['jopensim_maps_visibility'] == 0) { // new region must be set to invisible
			if(array_key_exists("region_id",$returnvalue)) {
				$regionuuid		= $returnvalue['region_id'];
				$mapmodel		= $this->getModel('map');
				$mapmodel->setVisible($regionuuid,1);
			}
		}
		$this->setRedirect('index.php?option=com_opensim&view=misc');
	}

	// Remote Admin Get Opensim Version
	public function getopensimulatorversion() {
		$data = JFactory::getApplication()->input->request->getArray();;
		$model = $this->getModel('misc');
		$settings = $model->getSettingsData();
		$opensim = $model->opensim;

		if($data['radminsystem'] == "multiple") {
			$simulator = $model->getSimulator($data['simulator']);
			if($simulator) {
				$remotehost 	= $this->getHostFrom($data['simulator']);
				$remoteport 	= ($simulator['radminport']) ? $simulator['radminport']:$this->getPortFrom($data['simulator']);
				$remotepasswd	= ($simulator['radminpwd']) ? $simulator['radminpwd']:$settings['remotepasswd'];
			} else {
				$remotehost 	= $this->getHostFrom($data['simulator']);
				$remoteport 	= $this->getPortFrom($data['simulator']);
				$remotepasswd	= $settings['remotepasswd'];
			}
		} else {
			$remotehost 		= $settings['remotehost'];
			$remoteport 		= $settings['remoteport'];
			$remotepasswd		= $settings['remotepasswd'];
		}
		$opensim->RemoteAdmin($remotehost,$remoteport,$remotepasswd);

		$command = "admin_get_opensim_version";
		$params = array('');
		$returnvalue = $opensim->SendCommand($command, $params);

		if (!is_array($returnvalue))
		{
			JFactory::getApplication()->enqueueMessage(JText::_('REMOTEADMIN_NORESPONSE'), "error");
		}
		
		else if (array_key_exists("error", $returnvalue) && $returnvalue['error'])
		{
			$message = JText::_('REMOTEADMIN_ERROR').": ".$returnvalue['error'];
			JFactory::getApplication()->enqueueMessage($message, "error");
		}
		
		else
		{
			$messages = array();
			$message = JText::_('REMOTEADMIN_RESPONDED').": ";

			foreach($returnvalue AS $key => $val)
			{
				$messages[] = $key."=".$val;
			}

			$message .= implode("<br />", $messages)."";
			JFactory::getApplication()->enqueueMessage($message, "message");
		}

		$this->setRedirect('index.php?option=com_opensim&view=misc&task=getopensimversion');
	}

	public function getHostFrom($url) {
		return parse_url($url, PHP_URL_HOST);
	}

	public function getPortFrom($url) {
		return parse_url($url, PHP_URL_PORT);
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

	public function saveSimulators() {
		$model = $this->getModel('misc');
		$model->saveSimulators();
		$this->setRedirect('index.php?option=com_opensim&view=misc');
	}

	public function saveSimulatorOrderAjax() {
		$model = $this->getModel('misc');
		$model->saveSimulatorOrder();
	}

	public function cancelSimulators() {
		$this->setRedirect('index.php?option=com_opensim&view=misc');
	}

	public function deleteSimulator() {
		$model = $this->getModel('misc');
		$model->removeSimulators();
		$this->setRedirect('index.php?option=com_opensim&view=misc&task=managesimulators');
	}
}
?>
