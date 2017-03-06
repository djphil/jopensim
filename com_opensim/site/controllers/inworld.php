<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2017 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class OpenSimControllerinworld extends OpenSimController {
	public function __construct() {
		parent::__construct();
		$model = $this->getModel('inworld');
	}

	public function create() {
		$this->insertuser();
	}

	public function insertuser() {
		$model = $this->getModel('inworld');

		$firstname	= trim(JFactory::getApplication()->input->get('firstname'));
		$lastname	= trim(JFactory::getApplication()->input->get('lastname'));
		$email		= trim(JFactory::getApplication()->input->get('email','','STRING'));
		$pwd1		= trim(JFactory::getApplication()->input->get('pwd1'));
		$pwd2		= trim(JFactory::getApplication()->input->get('pwd2'));

		if($model->checkUserExists($firstname,$lastname)) { // check if user already exists
			$type = "error";
			$message = JText::_('ERROR_USEREXISTS');
			$redirect = "index.php?option=com_opensim&view=inworld&firstname=".$firstname."&lastname=".$lastname."&email=".$email;
		} elseif(!$model->checkLastnameAllowed($lastname)) { // control allowed last names
			$type = "error";
			$message = JText::_('ERROR_LASTNAMENOTALLOWED')." (".$lastname.")";
			$redirect = "index.php?option=com_opensim&view=inworld&firstname=".$firstname."&lastname=".$lastname."&email=".$email;
		} elseif($pwd1 != $pwd2) {
			$type = "error";
			$message = JText::_('ERROR_PWDMISMATCH');
			$redirect = "index.php?option=com_opensim&view=inworld&firstname=".$firstname."&lastname=".$lastname."&email=".$email;
		} elseif(!$pwd1) {
			$type = "error";
			$message = JText::_('ERROR_NOPWD');
			$redirect = "index.php?option=com_opensim&view=inworld&firstname=".$firstname."&lastname=".$lastname."&email=".$email;
		} elseif(!$firstname) {
			$type = "error";
			$message = JText::_('ERROR_NOFIRSTNAME');
			$redirect = "index.php?option=com_opensim&view=inworld&firstname=".$firstname."&lastname=".$lastname."&email=".$email;
		} elseif(!$lastname) {
			$type = "error";
			$message = JText::_('ERROR_NOLASTNAME');
			$redirect = "index.php?option=com_opensim&view=inworld&firstname=".$firstname."&lastname=".$lastname."&email=".$email;
		} elseif(!$email) {
			$type = "error";
			$message = JText::_('ERROR_NOEMAIL');
			$redirect = "index.php?option=com_opensim&view=inworld&firstname=".$firstname."&lastname=".$lastname."&email=".$email;
		} else {
			$pregmail = "/^.{1,}@.{2,}\..{2,63}\$/";
			preg_match($pregmail, $email, $treffer); // Emailadresse auf Gültigkeit prüfen
			if($treffer[0] != $email || !isset($treffer[0])) { // validate Email format
				$type = "error";
				$message = JText::_('ERROR_INVALIDEMAIL');
				$redirect = "index.php?option=com_opensim&view=inworld&firstname=".$firstname."&lastname=".$lastname."&email=".$email;
			} else {
				$newuser['firstname']	= $firstname;
				$newuser['lastname']	= $lastname;
				$newuser['email']		= $email;
				$newuser['password']	= $pwd1;
				$newuser['uuid'] = $model->getUUID();
				$result = $model->insertuser($newuser);
				$model->setUserRelation($newuser['uuid']);
				$type = "message";
				$message = JText::_(OK_NEWUSER);
				$settings = $model->getSettingsData();
				if($settings['welcomecontent']) {
					$redirect = "index.php?option=com_content&view=article&id=".$settings['welcomecontent'];
				} else {
					$redirect = "index.php?option=com_opensim&view=inworld&task=welcome";
				}
			}
		}
		$this->setRedirect($redirect,$message,$type);
	}

	public function update() {
		$data	= JFactory::getApplication()->input->request->getArray();
//		$data	= JFactory::getApplication()->input->get('post');
		$task	= $task = JFactory::getApplication()->input->get('task','','method','string');
		$itemid	= JFactory::getApplication()->input->get('Itemid');
		$model = $this->getModel('inworld');
		switch($task) {
			default:
				$newtask = "default";
		}

		$response = $model->updateuser($data);
		if(is_array($response) && $response[0] == "error") { // something went wrong :(
			array_shift($response); // dont need the first one
			$errormessages = implode(", ",$response);
			$type = "Error";
			$message = JText::_('ERROR_USERUPDATE').": ".$errormessages;
		} elseif(is_array($response) && $response[0] == "ok") { // everything ok :)
			$type = "Message";
			$message = JText::_('OK_USERUPDATE').": ".$response[1];
		} else { // that should never happen ... :|
			$type = "Notice";
			$message = JText::_('NOTICE_USERUPDATE');
		}
		$redirect = "index.php?option=com_opensim&view=inworld&task=".$newtask."&Itemid=".$itemid;
		$this->setRedirect($redirect,$message,$type);
	}

	public function updateprofile() {
		$data	= JFactory::getApplication()->input->request->getArray();
//		$data	= JFactory::getApplication()->input->get('post');
		$task	= JFactory::getApplication()->input->get('task','','method','string');
		$itemid	= JFactory::getApplication()->input->get('Itemid');
		$model = $this->getModel('inworld');
		switch($task) {
			default:
				$newtask = "profile";
		}
		$model->updateprofile($data);
		$message = JText::_('JOPENSIM_OK_PROFILEUPDATE');
		$type = "Message";
		$redirect = "index.php?option=com_opensim&view=inworld&task=".$newtask."&Itemid=".$itemid;
		$this->setRedirect($redirect,$message,$type);
	}

	public function leavegroup() {
		$groupid = JFactory::getApplication()->input->get('groupid');
		$itemid	= JFactory::getApplication()->input->get('Itemid');
		$model = $this->getModel('inworld');
		$groupleft = $model->leaveGroup($groupid);

		if($groupleft === TRUE) {
			$message = JText::_('OK_LEAVEGROUP');
			$type = "Message";
		} else {
			$message = JText::_('ERROR_LEAVEGROUP');
			$type = "Error";
		}
		$redirect = "index.php?option=com_opensim&view=inworld&task=groups&Itemid=".$itemid;
		$this->setRedirect($redirect,$message,$type);
	}

	public function ejectgroup() {
		$groupid = JFactory::getApplication()->input->get('groupid');
		$ejectid = JFactory::getApplication()->input->get('ejectid');
		$itemid	= JFactory::getApplication()->input->get('Itemid');
		$model = $this->getModel('inworld');
		$groupleft = $model->ejectFromGroup($groupid,$ejectid);

		if($groupleft === TRUE) {
			$message = JText::_('OK_EJECTGROUP');
			$type = "Message";
		} else {
			$message = JText::_('ERROR_EJECTGROUP');
			$type = "Error";
		}
		$redirect = "index.php?option=com_opensim&view=inworld&task=groups&Itemid=".$itemid;
		$this->setRedirect($redirect,$message,$type);
	}

	public function createIdent() {
		$itemid	= JFactory::getApplication()->input->get('Itemid');
		$model = $this->getModel('inworld');
		$model->opensimSetInworldIdent();

		$redirect = "index.php?option=com_opensim&view=inworld&Itemid=".$itemid;
		$this->setRedirect($redirect);
	}

	public function cancelIdent() {
		$itemid	= JFactory::getApplication()->input->get('Itemid');
		$model = $this->getModel('inworld');
		$model->opensimCancelInworldIdent();

		$redirect = "index.php?option=com_opensim&view=inworld&Itemid=".$itemid;
		$this->setRedirect($redirect,$message,$type);
	}

	public function divorcesure() {
		$model		= $this->getModel('inworld');
		$data		= JFactory::getApplication()->input->request->getArray(array());
		$divorce	= $model->divorcefrom($data['partneruuid']);
		if($divorce === TRUE) {
			$partnername	= $model->getOpenSimName($data['partneruuid']);
			$type			= "Message";
			$message		= JText::sprintf('JOPENSIM_PROFILE_PARTNER_DIVORCE_OK',$partnername);
		} else {
			$message		= JText::_('JOPENSIM_PROFILE_PARTNER_DIVORCE_ERROR');
			$type			= "Error";
		}
		$redirect	= "index.php?option=com_opensim&view=inworld&task=profile&Itemid=".$data['Itemid'];
		$this->setRedirect($redirect,$message,$type);
	}

	public function sendpartnerrequest() {
		$model		= $this->getModel('inworld');
		$data		= JFactory::getApplication()->input->request->getArray(array());

		$sendrequest= $model->sendpartnerrequest($data['frienduuid']);
		if($sendrequest === TRUE) {
			$partnername	= $model->getOpenSimName($data['frienduuid']);
			$type			= "Message";
			$message		= JText::sprintf('JOPENSIM_PROFILE_PARTNER_REQUEST_OK',$partnername);
		} else {
			$message		= JText::_('JOPENSIM_PROFILE_PARTNER_REQUEST_ERROR');
			$type			= "Error";
		}

		$redirect	= "index.php?option=com_opensim&view=inworld&task=profile&Itemid=".$data['Itemid'];
		$this->setRedirect($redirect,$message,$type);
	}

	public function parnercancelsure() {
		$model	= $this->getModel('inworld');
		$data	= JFactory::getApplication()->input->request->getArray(array());

		$cancelrequest= $model->deletepartnerrequest($data['partneruuid']);
		if($cancelrequest === TRUE) {
			$type		= "Message";
			$message	= JText::_('JOPENSIM_PROFILE_PARTNER_REQUEST_CANCELED_OK');
		} else {
			$type		= "Error";
			$message	= JText::_('JOPENSIM_PROFILE_PARTNER_REQUEST_CANCELED_ERROR');
		}

		$redirect	= "index.php?option=com_opensim&view=inworld&task=profile&Itemid=".$data['Itemid'];
		$this->setRedirect($redirect,$message,$type);
	}

	public function partnerrequesthandler() {
		$model		= $this->getModel('inworld');
		$data		= JFactory::getApplication()->input->request->getArray(array());

		if(!array_key_exists("accept",$data) || !array_key_exists("partneruuid",$data)) {
			$message		= JText::_('JOPENSIM_PROFILE_PARTNER_REQUEST_HANDLE_ERROR');
			$type			= "Error";
		} else {
			switch($data['accept']) {
				case "yes":
					$yes				= $model->partnership($data['partneruuid']);
					if($yes === TRUE) {
						$partnername	= $model->getOpenSimName($data['partneruuid']);
						$type			= "Message";
						$message		= JText::sprintf('JOPENSIM_PROFILE_PARTNER_OK',$partnername);
					} else {
						$type			= "Error";
						$message		= JText::_('JOPENSIM_PROFILE_PARTNER_ERROR');
					}
				break;
				case "no":
					$no					= $model->denypartnerrequest($data['partneruuid']);
					if($no === TRUE) {
						$type			= "Message";
						$message		= JText::_('JOPENSIM_PROFILE_PARTNER_REQUEST_DENY_OK');
					} else {
						$type			= "Error";
						$message		= JText::_('JOPENSIM_PROFILE_PARTNER_REQUEST_HANDLE_ERROR');
					}
				break;
				case "never":
					$ignore				= $model->ignorepartnerrequest($data['partneruuid']);
					if($ignore === TRUE) {
						$type			= "Message";
						$message		= JText::_('JOPENSIM_PROFILE_PARTNER_REQUEST_IGNORE_OK');
					} else {
						$type			= "Error";
						$message		= JText::_('JOPENSIM_PROFILE_PARTNER_REQUEST_HANDLE_ERROR');
					}
				break;
			}
		}
		$redirect	= "index.php?option=com_opensim&view=inworld&task=profile&Itemid=".$data['Itemid'];
		$this->setRedirect($redirect,$message,$type);
	}

	public function cancelpartnerignore() {
		$model		= $this->getModel('inworld');
		$data		= JFactory::getApplication()->input->request->getArray(array());
		$model->denypartnerrequest($data['frienduuid']);
		$redirect	= "index.php?option=com_opensim&view=inworld&task=profile&Itemid=".$data['Itemid'];
		$this->setRedirect($redirect);
	}
}
?>