<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2015 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class OpenSimControllerMoney extends OpenSimController {

	public function __construct($config = array()) {
		parent::__construct($config);
	}

	public function cancel($key = NULL) {
		$this->setRedirect('index.php?option=com_opensim&view=opensim');
	}

	public function savemoneysettings() {
		$this->save();
	}

	public function save($key = NULL, $urlVar = NULL) {
		$model = $this->getModel('money');
		$model->storeMoneySettings();
		$this->setRedirect('index.php?option=com_opensim&view=money',JText::_('SETTINGSSAVEDOK'));
	}

	public function apply_settings() {
		$model = $this->getModel('money');
		$model->storeMoneySettings();
		$this->setRedirect('index.php?option=com_opensim&view=money',JText::_('SETTINGSSAVEDOK'));
	}

	public function cancelMoneySettings() {
		$this->cancel();
	}

	public function cancelMoneyFromBanker() {
		$returnto = JFactory::getApplication()->input->get('returnto');
		$this->setRedirect('index.php?option=com_opensim&view='.$returnto);
	}

	public function balancecorrection() {
		$model = $this->getModel('money');
		$model->balanceCorrection();
		$this->setRedirect('index.php?option=com_opensim&view=money');
	}

	public function resetmoney() {
		$model = $this->getModel('money');
		$settings = $model->getMoneySettings();
		$banker = $settings['bankerUID'];
		$positives = $model->getPositiveBalances($banker);
		if(is_array($positives) && count($positives) > 0) {
			foreach($positives AS $data) {
				if($model->opensimCreated($data['user']) === FALSE) { // this user does not exist in OpenSim anymore, lets remove it
					$model->removeUserBalance($data['user']);
				} else {
					$parameter['senderID']		= $data['user'];
					$parameter['receiverID']	= $banker;
					$parameter['amount']		= $data['balance'];
					$parameter['description']	= "jOpenSim MoneyReset!";
					$model->TransferMoney($parameter);
				}
			}
		}
		$bankerbalance = $model->getBalance($banker);
		$model->setBalance($banker,($bankerbalance * -1));
		$type = "message";
		$message = JText::_('JOPENSIM_MONEY_RESET_MESSAGE');
		$this->setRedirect('index.php?option=com_opensim&view=money',$message,$type);
	}

	public function setCustomfee() {
		$model			= $this->getModel('money');
		$uuid			= trim(JFactory::getApplication()->input->get('uuid'));
		$uploadfee		= trim(JFactory::getApplication()->input->get('jopensim_money_customfee_upload'));
		$groupfee		= trim(JFactory::getApplication()->input->get('jopensim_money_customfee_groupcreation'));
		$affected		= $model->setCustomfee($uuid,$uploadfee,$groupfee);
		if($affected > 0) {
			$type		= "message";
			$message	= JText::_('JOPENSIM_MONEY_USER_CUSTOMFEES_OKMSG');
		} else {
			$type		= "";
			$message	= "";
		}
		$this->setRedirect('index.php?option=com_opensim&view=money&task=userMoney&uuid='.$uuid,$message,$type);
	}

	public function removeCustomFee() {
		$model	= $this->getModel('money');
		$uuid	= trim(JFactory::getApplication()->input->get('uuid'));
		$affected		= $model->removeCustomFee($uuid);
		if($affected > 0) {
			$type		= "message";
			$message	= JText::_('JOPENSIM_MONEY_USER_CUSTOMFEES_REMOVEOKMSG');
		} else {
			$type		= "message";
//			$message	= "";
			$message	= $affected;
		}
		$this->setRedirect('index.php?option=com_opensim&view=money&task=userMoney&uuid='.$uuid,$message,$type);
	}
}
?>