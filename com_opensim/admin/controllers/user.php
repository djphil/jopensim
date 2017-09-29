<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2017 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class OpenSimControlleruser extends OpenSimController {
	public function __construct() {
		parent::__construct();
	}

	public function addUser() {
		$this->setRedirect('index.php?option=com_opensim&view=user&task=newuser','');
	}

	public function canceladduser() {
		$this->setRedirect('index.php?option=com_opensim&view=user','');
	}

	public function saveuseredit() {
		$model = $this->getModel('user');
		$opensim =& $model->opensim;

		$data = JFactory::getApplication()->input->request->getArray();

		if(!array_key_exists("jopensim_usersetting_flag3",$data)) $data['jopensim_usersetting_flag3']	= 0;
		if(!array_key_exists("jopensim_usersetting_flag4",$data)) $data['jopensim_usersetting_flag4']	= 0;
		if(!array_key_exists("jopensim_usersetting_flag5",$data)) $data['jopensim_usersetting_flag5']	= 0;
		if(!array_key_exists("jopensim_usersetting_flag9",$data)) $data['jopensim_usersetting_flag9']	= 0;
		if(!array_key_exists("jopensim_usersetting_flag10",$data)) $data['jopensim_usersetting_flag10']	= 0;
		if(!array_key_exists("jopensim_usersetting_flag11",$data)) $data['jopensim_usersetting_flag11']	= 0;
		if(!array_key_exists("jopensim_usersetting_flag12",$data)) $data['jopensim_usersetting_flag12']	= 0;

		$data['PrincipalID']= trim($data['userid']);
		$data['FirstName']	= trim($data['firstname']);
		$data['LastName']	= trim($data['lastname']);
		$data['Email']		= trim($data['email']);
		$data['UserLevel']	= trim($data['UserLevel']);
		$data['UserTitle']	= trim($data['usertitle']);
		$data['UserFlags']	= trim($data['jopensim_usersetting_flag3'])
							+ trim($data['jopensim_usersetting_flag4'])
							+ trim($data['jopensim_usersetting_flag5'])
							+ trim($data['jopensim_usersetting_flag9'])
							+ trim($data['jopensim_usersetting_flag10'])
							+ trim($data['jopensim_usersetting_flag11'])
							+ trim($data['jopensim_usersetting_flag12']);
		$pwd1				= trim($data['pwd1']);
		$pwd2				= trim($data['pwd2']);

		if($model->checkUserExists($data['FirstName'],$data['LastName'],$data['PrincipalID']) !== FALSE) { // check if user already exists
			$type = "error";
			$message = JText::_('ERROR_USEREXISTS');
			$redirect = "index.php?option=com_opensim&view=user&task=edituser&checkUser[0]=".$data['PrincipalID']."&firstname=".$data['FirstName']."&lastname=".$data['LastName']."&email=".$data['Email']."&UserLevel=".$data['UserLevel'];
		} elseif($pwd1 != $pwd2) {
			$type = "error";
			$message = JText::_('ERROR_PWDMISMATCH');
			$redirect = "index.php?option=com_opensim&view=user&task=edituser&checkUser[0]=".$data['PrincipalID']."&firstname=".$data['FirstName']."&lastname=".$data['LastName']."&email=".$data['Email']."&UserLevel=".$data['UserLevel'];
		} elseif(!$data['FirstName']) {
			$type = "error";
			$message = JText::_('ERROR_NOFIRSTNAME');
			$redirect = "index.php?option=com_opensim&view=user&task=edituser&checkUser[0]=".$data['PrincipalID']."&firstname=".$data['FirstName']."&lastname=".$data['LastName']."&email=".$data['Email']."&UserLevel=".$data['UserLevel'];
		} elseif(!$data['LastName']) {
			$type = "error";
			$message = JText::_('ERROR_NOLASTNAME');
			$redirect = "index.php?option=com_opensim&view=user&task=edituser&checkUser[0]=".$data['PrincipalID']."&firstname=".$data['FirstName']."&lastname=".$data['LastName']."&email=".$data['Email']."&UserLevel=".$data['UserLevel'];
		} elseif(!$data['Email']) {
			$type = "error";
			$message = JText::_('ERROR_NOEMAIL');
			$redirect = "index.php?option=com_opensim&view=user&task=edituser&checkUser[0]=".$data['PrincipalID']."&firstname=".$data['FirstName']."&lastname=".$data['LastName']."&email=".$data['Email']."&UserLevel=".$data['UserLevel'];
		} else {
			$pregmail = "/^.{1,}@.{2,}\..{2,63}\$/";
			preg_match($pregmail, $data['Email'], $treffer); // Emailadresse auf Gültigkeit prüfen
			if($treffer[0] != $data['Email'] || !isset($treffer[0])) { // validate Email format
				$type = "error";
				$message = JText::_('ERROR_INVALIDEMAIL')." bla ".$data['Email'];
				$redirect = "index.php?option=com_opensim&view=user&task=edituser&checkUser[0]=".$data['PrincipalID']."&firstname=".$data['FirstName']."&lastname=".$data['LastName']."&email=".$data['Email'];
			} else {
				if($pwd1) $data['password'] = $pwd1;
				$result = $model->updateuser($data);
				if($result === TRUE) {
					$type = "message";
					$message = JText::_('OK_USERUPDATE');
				} else {
				$type = "error";
				$message = JText::_('ERROR_USERUPDATE');
				}
				$redirect = "index.php?option=com_opensim&view=user";
			}
		}
		if($type != "error" && $data['UserLevel'] == -3) {
			$model->checkAvatarProfileImage($data['PrincipalID']);
		}
		$this->setRedirect($redirect,$message,$type);
	}

	public function insertuser() {
		$model = $this->getModel('user');
		$opensim =& $model->opensim;

		$firstname	= trim(JFactory::getApplication()->input->get('firstname'));
		$lastname	= trim(JFactory::getApplication()->input->get('lastname'));
		$email		= trim(JFactory::getApplication()->input->get('email','','STRING'));
		$pwd1		= trim(JFactory::getApplication()->input->get('pwd1'));
		$pwd2		= trim(JFactory::getApplication()->input->get('pwd2'));

		if($model->checkUserExists($firstname,$lastname)) { // check if user already exists
			$type = "error";
			$message = JText::_('ERROR_USEREXISTS');
			$redirect = "index.php?option=com_opensim&view=user&task=newuser&firstname=".$firstname."&lastname=".$lastname."&email=".$email;
		} elseif($pwd1 != $pwd2) {
			$type = "error";
			$message = JText::_('ERROR_PWDMISMATCH');
			$redirect = "index.php?option=com_opensim&view=user&task=newuser&firstname=".$firstname."&lastname=".$lastname."&email=".$email;
		} elseif(!$pwd1) {
			$type = "error";
			$message = JText::_('ERROR_NOPWD');
			$redirect = "index.php?option=com_opensim&view=user&task=newuser&firstname=".$firstname."&lastname=".$lastname."&email=".$email;
		} elseif(!$firstname) {
			$type = "error";
			$message = JText::_('ERROR_NOFIRSTNAME');
			$redirect = "index.php?option=com_opensim&view=user&task=newuser&firstname=".$firstname."&lastname=".$lastname."&email=".$email;
		} elseif(!$lastname) {
			$type = "error";
			$message = JText::_('ERROR_NOLASTNAME');
			$redirect = "index.php?option=com_opensim&view=user&task=newuser&firstname=".$firstname."&lastname=".$lastname."&email=".$email;
		} elseif(!$email) {
			$type = "error";
			$message = JText::_('ERROR_NOEMAIL');
			$redirect = "index.php?option=com_opensim&view=user&task=newuser&firstname=".$firstname."&lastname=".$lastname."&email=".$email;
		} else {
			$pregmail = "/^.{1,}@.{2,}\..{2,63}\$/";
			preg_match($pregmail, $email, $treffer); // Emailadresse auf Gültigkeit prüfen
			if($treffer[0] != $email || !isset($treffer[0])) { // validate Email format
				$type = "error";
				$message = JText::_('ERROR_INVALIDEMAIL');
				$redirect = "index.php?option=com_opensim&view=user&task=newuser&firstname=".$firstname."&lastname=".$lastname."&email=".$email;
			} else {
				$newuser['firstname']	= $firstname;
				$newuser['lastname']	= $lastname;
				$newuser['email']		= $email;
				$newuser['password']	= $pwd1;
				$newuser['uuid'] = $model->getUUID();
				$result = $model->insertuser($newuser);
				$type = "message";
				$message = JText::_('OK_NEWUSER');
				$redirect = "index.php?option=com_opensim&view=user";
			}
		}
		$this->setRedirect($redirect,$message,$type);
	}

	public function deleteuser() {
		$data = JFactory::getApplication()->input->request->getArray();;
		$model = $this->getModel('user');
		if(is_array($data['checkUser'])) {
			$counter = 0;
			foreach($data['checkUser'] AS $userid) {
				$deleted = $model->deleteUser($userid);
				if($deleted == TRUE) $counter++;
			}
			if($counter == count($data['checkUser'])) {
				$type = "message";
				$message = JText::_('OK_DELETEUSER');
			} else {
				$type = "notice";
				$message = JText::sprintf('OK_DELETEXUSER',$counter);
			}
			$redirect = "index.php?option=com_opensim&view=user";
		} else {
			$type = "error";
			$message = JText::_('ERROR_DELETEUSER');
			$redirect = "index.php?option=com_opensim&view=user";
		}
		$this->setRedirect($redirect,$message,$type);
	}

	public function applyuserrelation() {
		$model = $this->getModel('user');
		$data = JFactory::getApplication()->input->request->getArray();;
		if($data['joomlauser'] == 0) {
			$message = JText::_('OK_RELATIONNOCHANGE');
		}elseif($data['joomlauser'] == -1) {
			$deleted = $model->userrelation($data['userid'],999,"delete");
			$message = JText::_('OK_RELATIONDELETED');
		} else {
			switch($data['relationmethod']) {
				case "insert":
					$inserted = $model->userrelation($data['userid'],$data['joomlauser'],"insert");
					if($inserted) $message = JText::_('OK_RELATIONINSERTED');
					else $message = JText::_('OK_RELATIONNOCHANGE');
				break;
				case "update":
					$updated = $model->userrelation($data['userid'],$data['joomlauser'],"update");
					if($updated) $message = JText::_('OK_RELATIONUPDATED');
					else $message = JText::_('OK_RELATIONNOCHANGE');
				break;
			}
		}
		$type = "message";
		$redirect = "index.php?option=com_opensim&view=user";
		$this->setRedirect($redirect,$message,$type);
	}

	public function setUserOffline() {
		$userid = JFactory::getApplication()->input->get('userid');
		$model = $this->getModel('user');
		$model->setUserOffline($userid);
		$this->setRedirect("index.php?option=com_opensim&view=user",JText::_('OK_USETSETOFFLINE'));
	}

	public function repairUserStatus() {
		$model = $this->getModel('user');
		$model->repairUserStatus();
		$this->setRedirect("index.php?option=com_opensim&view=user",JText::_('OK_REPAIRUSERSTATUS'));
	}

	public function userMoney() {
		$model = $this->getModel('user');
		if($model->moneyEnabled === TRUE) {
			$data = JFactory::getApplication()->input->request->getArray();;
			$uuid = $data['checkUser'][0];
			$redirect = "index.php?option=com_opensim&view=money&task=userMoney&uuid=".$uuid."&test=".$data['checkUser'][0];
			$this->setRedirect($redirect);
		} else {
			$type = "error";
			$message = JText::_('JOPENSIM_ERROR_MONEYDISABLED');
			$redirect = "index.php?option=com_opensim&view=user";
			$this->setRedirect($redirect,$message,$type);
		}
	}
}
?>
