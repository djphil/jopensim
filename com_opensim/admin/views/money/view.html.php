<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2017 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.view');

class opensimViewmoney extends JViewLegacy {
	public function display($tpl = null) {
		JHTML::_('behavior.modal');
		$document				= JFactory::getDocument();

		$model					= $this->getModel('money');
		$this->settings			= $model->getSettingsData();
		$this->moneysettings	= $model->getMoneySettings();
		$this->opensimdb		= $model->getOpenSimGridDB();
		$this->pagination		= $model->getPagination();
		$this->state			= $this->get('State');

		if(!$this->settings['addons_currency']) {
			JFactory::getApplication()->enqueueMessage(JText::_('JOPENSIM_MONEYADDONDISABLED'),'warning');
			$tpl = "disabled";
		} else {
			$task = JFactory::getApplication()->input->get('task', '', 'method', 'string');

			$moneysettings		= $model->getMoneySettings();
			$this->bankerlist	= $model->getBankerUser();
			$this->balanceAll	= $model->getAllBalances();
			if(!$moneysettings['bankerUID']) {
				JFactory::getApplication()->enqueueMessage(JText::_('JOPENSIM_MONEY_BANKERWARNING'),'error');
			}

			$this->balanceUser	= $model->getAllBalances($moneysettings['bankerUID']);

			$this->sidebar = null;
			switch($task) {
				case "userMoney":
					$this->sidebar	= JHtmlSidebar::render();
					$this->task = $task;
					$this->returnto = "user";
					$tpl = "usermoney";
					$useruuid				= JFactory::getApplication()->input->get( 'uuid', '', 'method', 'string');
					$this->balance			= $model->getBalance($useruuid);
					$this->userdata			= $model->getUserData($useruuid);
				break;
				case "moneyUser":
					$this->sidebar	= JHtmlSidebar::render();
					$this->returnto = "money";
					$tpl = "usermoney";
					$useruuid				= JFactory::getApplication()->input->get( 'uuid', '', 'method', 'string');
					$this->balance			= $model->getBalance($useruuid);
					$this->userdata			= $model->getUserData($useruuid);
				break;
				case "user_customfee":
					$this->returnto = "money";
					$tpl = "customfee";
					$useruuid				= JFactory::getApplication()->input->get( 'uuid', '', 'method', 'string');
					$this->balance			= $model->getBalance($useruuid);
					$this->userdata			= $model->getUserData($useruuid);
					$this->customfees		= $model->getCustomfee($useruuid);
					if(!$this->customfees || !is_array($this->customfees) || !array_key_exists("uploadfee",$this->customfees) || !array_key_exists("groupfee",$this->customfees)) {
						$this->feeaction	= "insert";
						$this->buttonvalue	= JText::_('JOPENSIM_MONEY_USER_CUSTOMFEES_INSERT');
						$this->customfees['uploadfee']	= $this->moneysettings['uploadCharge'];
						$this->customfees['groupfee']	= $this->moneysettings['groupCharge'];
					} else {
						$this->feeaction	= "update";
						$this->buttonvalue	= JText::_('JOPENSIM_MONEY_USER_CUSTOMFEES_UPDATE');
					}
				break;
				case "correctbalance":
					$tpl = "correctbalance";
				break;
				default:
					$this->sidebar	= JHtmlSidebar::render();
					if($this->balanceAll != 0) {
						$warningmessage	 = JText::_('JOPENSIM_MONEY_BALANCEWARNING');
						$warningmessage	.= " <a class='modal' id='jopensimmoney_balancecorrection' href='index.php?option=com_opensim&view=money&task=correctbalance&tmpl=component' rel=\"{handler: 'iframe', size: {x: 500, y: 400}, overlayOpacity: 0.3}\">".JText::_('JOPENSIM_MONEY_BALANCECORRECTION_BUTTON')."</a>";
						JFactory::getApplication()->enqueueMessage($warningmessage,'error');
					}
					// Get data from the model
					$this->transactions 	= $model->getTransactionOpenSimNames($model->getTransactionList());
					$this->sortDirection	= $this->state->get('money_filter_order_Dir');
					$this->sortColumn		= $this->state->get('money_filter_order');
				break;
			}
		}

		$this->setToolbar($tpl);
		parent::display($tpl);
	}

	public function setToolbar($tpl) {
		JToolBarHelper::title(JText::_('JOPENSIM_MONEY'),'32-money');
		$task = JFactory::getApplication()->input->get( 'task', '', 'method', 'string');

		switch($tpl) {
			case "userMoney":
			case "usermoney":
			case "moneyUser":
				JToolBarHelper::save('payMoneyFromBanker','JOPENSIM_MONEY_TRANSFERMONEY');
				JToolBarHelper::cancel('cancelMoneyFromBanker','JCANCEL');
				JToolBarHelper::help("", false, JText::_('JOPENSIM_HELP_USER_MONEY'));
			break;
			default:
				if (JFactory::getUser()->authorise('core.admin', 'com_opensim')) {
					JToolBarHelper::preferences('com_opensim','700','950',JText::_('JOPENSIM_GLOBAL_SETTINGS'));
				}
				JToolBarHelper::help("", false, JText::_('JOPENSIM_HELP_MONEY'));
			break;
		}
	}

	protected function getSortFields() {
		return array(
				'#__opensim_moneytransactions.time' => JText::_('JOPENSIM_MONEY_SORT_TIME'),
				'#__opensim_moneytransactions.sender' => JText::_('JOPENSIM_MONEY_SORT_SENDER'),
				'#__opensim_moneytransactions.receiver' => JText::_('JOPENSIM_MONEY_SORT_RECEIVER'),
				'#__opensim_moneytransactions.amount' => JText::_('JOPENSIM_MONEY_SORT_AMOUNT'),
				'#__opensim_moneytransactions.description' => JText::_('JOPENSIM_MONEY_SORT_DESCRIPTION')
		);
	}

}

?>