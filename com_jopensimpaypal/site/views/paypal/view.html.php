<?php
/*
 * @component jOpenSimPayPal
 * @copyright Copyright (C) 2018 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class jopensimpaypalViewpaypal extends JViewLegacy {
	public function display($tpl = null) {
		$this->assetpath = JUri::base(true)."/components/com_jopensimpaypal/assets/";
		$doc = JFactory::getDocument();
		$doc->addStyleSheet($this->assetpath.'jopensimpaypal.css');
		$doc->addScript($this->assetpath.'jopensimpaypal.js');

		$model			= $this->getModel('paypal');
		$cparams		= $model->getParam("all");

		$this->itemid	= JFactory::getApplication()->input->get('Itemid');
		$task 			= JFactory::getApplication()->input->get('task');

		switch($task) {
			case "cancel":
				$this->canceltext	= $cparams['cancel_message'];
				$tpl = "cancel";
			break;
			case "success":
				$this->successtext	= $cparams['success_message'];
				$tpl = "success";
			break;
			default:
				$this->inworldlink	= "index.php?option=com_opensim&view=inworld&Itemid=".$this->itemid;
				$this->createlink	= JText::sprintf('COM_JOPENSIMPAYPAL_ERROR_NEEDRELATION_Q1',$this->inworldlink);

				$user = JFactory::getUser();
				if($user->guest) {
					JFactory::getApplication()->enqueueMessage(JText::_('COM_JOPENSIMPAYPAL_ERROR_LOGINFIRST'),'warning');
					$tpl = "needlogin";
				} else {
					$opensimUID = $model->getUUID($user->id);
					if(!$opensimUID) {
						JFactory::getApplication()->enqueueMessage(JText::_('COM_JOPENSIMPAYPAL_ERROR_NEEDRELATION'),'warning');
						$tpl = "needrelation";
					} else {
						$user->UUID = $opensimUID;
						$model->checkUserLimits();
					}
				}


				$this->user				= $user;
				$this->userUUID			= $user->UUID;

				$transactionfee 		= intval($cparams['transactionfee']);
				$this->cparams			= $cparams;
				$this->pretext			= $cparams['pre_message'];
				$this->posttext			= $cparams['post_message'];

				$defaultvalue			= $model->getDefaultValue();
				$this->defaultvalueRL	= $defaultvalue;
				$iwcurrency				= $defaultvalue * $cparams['currencyratebuy'];
				$this->defaultvalueIW	= $iwcurrency;

				if($cparams['hasfee']) {
					if($cparams['transactionfeetype'] == "percent") {
						$transactionfee = $defaultvalue / 100 * $cparams['transactionfee'];
						$total = $defaultvalue + $transactionfee;
					} else {
						$transactionfee = $cparams['transactionfee'];
						$total = $defaultvalue + $transactionfee;
					}
				} else {
					$total = $defaultvalue;
				}
				$this->total			= round($total,2);
				$this->transactionfee	= $transactionfee;

				if(intval($cparams['userlimit_day']) > 0 && $user->limitDay >= $cparams['userlimit_day']) {
					$tpl = "limit_day";
				} elseif(intval($cparams['userlimit_week']) > 0 && $user->limitWeek >= $cparams['userlimit_week']) {
					$tpl = "limit_week";
				} elseif(intval($cparams['userlimit_month']) > 0 && $user->limitMonth >= $cparams['userlimit_month']) {
					$tpl = "limit_month";
				}

				$this->notifyurl	= JURI::base()."index.php?option=com_jopensimpaypal&view=notify&tmpl=component";
				$this->successurl	= JURI::base()."index.php?option=com_jopensimpaypal&view=paypal&task=success&Itemid=".$this->itemid;
				$this->cancelurl	= JURI::base()."index.php?option=com_jopensimpaypal&view=paypal&task=cancel&Itemid=".$this->itemid;
			break;
		}

		parent::display($tpl);
	}
}
?>