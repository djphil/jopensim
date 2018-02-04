<?php
/*
 * @component jOpenSimPayPal
 * @copyright Copyright (C) 2017 FoTo50 https://www.jopensim.com/
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

//		JHTML::stylesheet( 'jopensimpaypal.css', 'components/com_jopensimpaypal/assets/' );
//		JHTML::script('jopensimpaypal.js','components/com_jopensimpaypal/assets/');

		$model = $this->getModel('paypal');
		$cparams = $model->getParam("all");

		$itemid = JRequest::getVar('Itemid');
		$this->assignRef('Itemid',$itemid);

		$task = JRequest::getVar('task');
		switch($task) {
			case "cancel":
				$this->assignRef('canceltext',$cparams['cancel_message']);
				$tpl = "cancel";
			break;
			case "success":
				$this->assignRef('successtext',$cparams['success_message']);
				$tpl = "success";
			break;
			default:
				$inworldlink = "index.php?option=com_opensim&view=inworld&Itemid=".$itemid;
				$createlink		= JText::sprintf('COM_JOPENSIMPAYPAL_ERROR_NEEDRELATION_Q1',$inworldlink);
				$this->assignRef('inworldlink',$inworldlink);
				$this->assignRef('createlink',$createlink);

				$user =& JFactory::getUser();
				if($user->guest) {
					JError::raiseWarning(100,JText::_('COM_JOPENSIMPAYPAL_ERROR_LOGINFIRST'));
					$tpl = "needlogin";
				} else {
					$opensimUID = $model->getUUID($user->id);
					if(!$opensimUID) {
						JError::raiseWarning(100,JText::_('COM_JOPENSIMPAYPAL_ERROR_NEEDRELATION'));
						$tpl = "needrelation";
					} else {
						$user->UUID = $opensimUID;
						$model->checkUserLimits();
					}
				}


				$this->assignRef('user',$user);
				$this->assignRef('userUUID',$user->UUID);

				$transactionfee = intval($cparams['transactionfee']);
				$this->assignRef('transactionfee',$transactionfee);

				$this->assignRef('cparams',$cparams);

				$this->assignRef('pretext',$cparams['pre_message']);

				$this->assignRef('posttext',$cparams['post_message']);

				$defaultvalue = $model->getDefaultValue();
				$this->assignRef('defaultvalueRL',$defaultvalue);
				$iwcurrency		= $defaultvalue * $cparams['currencyratebuy'];
				$this->assignRef('defaultvalueIW',$iwcurrency);

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
				$total = round($total,2);
				$this->assignRef('transactionfee',$transactionfee);
				$this->assignRef('total',$total);

				if(intval($cparams['userlimit_day']) > 0 && $user->limitDay >= $cparams['userlimit_day']) {
					$tpl = "limit_day";
				} elseif(intval($cparams['userlimit_week']) > 0 && $user->limitWeek >= $cparams['userlimit_week']) {
					$tpl = "limit_week";
				} elseif(intval($cparams['userlimit_month']) > 0 && $user->limitMonth >= $cparams['userlimit_month']) {
					$tpl = "limit_month";
				}

				$notifyurl	= JURI::base()."components/com_jopensimpaypal/jnotify.php";
				$this->assignRef('notifyurl',$notifyurl);
				$successurl	= JURI::base()."index.php?option=com_jopensimpaypal&view=paypal&task=success&Itemid=".$itemid;
				$this->assignRef('successurl',$successurl);
				$cancelurl	= JURI::base()."index.php?option=com_jopensimpaypal&view=paypal&task=cancel&Itemid=".$itemid;
				$this->assignRef('cancelurl',$cancelurl);
			break;
		}

		parent::display($tpl);
	}
}
?>