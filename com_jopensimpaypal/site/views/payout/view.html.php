<?php
/*
 * @package Joomla 2.5
 * @copyright Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html, see LICENSE.php
 *
 * @component jOpenSimPayPal
 * @copyright Copyright (C) 2013 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.view');
 
class jopensimpaypalViewpayout extends JViewLegacy {
	public function display($tpl = null) {
		$this->assetpath = JUri::base(true)."/components/com_jopensimpaypal/assets/";
		$doc = JFactory::getDocument();
		$doc->addStyleSheet($this->assetpath.'jopensimpaypal.css');
		$doc->addScript($this->assetpath.'jopensimpaypal.js');

		$model			= $this->getModel('payout');
		$cparams		= $model->getParam("all");

		$this->itemid	= JRequest::getVar('Itemid');
		$task 			= JRequest::getVar('task');
		$this->returnto	= JRequest::getString('returnto');
		switch($task) {
			default:
				$inworldlink = "index.php?option=com_opensim&view=inworld&Itemid=".$this->itemid;
				$createlink		= JText::sprintf('COM_JOPENSIMPAYPAL_ERROR_NEEDRELATION_Q1',$inworldlink);
				$this->assignRef('inworldlink',$inworldlink);
				$this->assignRef('createlink',$createlink);
		
				$user =& JFactory::getUser();
				if($user->guest) {
					JError::raiseWarning(100,JText::_('COM_JOPENSIMPAYPAL_ERROR_LOGINFIRST'));
					$tpl		= "needlogin";
					$balance	= 0;
				} else {
					$opensimUID = $model->getUUID($user->id);
					if(!$opensimUID) {
						JError::raiseWarning(100,JText::_('COM_JOPENSIMPAYPAL_ERROR_NEEDRELATION'));
						$tpl		= "needrelation";
						$balance	= 0;
					} else {
						$user->UUID = $opensimUID;
						$balance	= $model->getBalance($opensimUID);
					}
				}
		
				$this->assignRef('user',$user);
				$this->assignRef('userUUID',$user->UUID);
				$this->assignRef('balance',$balance);

				$this->assignRef('cparams',$cparams);

				$this->assignRef('pretext',$cparams['sell_pre_message']);

				$this->assignRef('posttext',$cparams['sell_post_message']);

				$defaultvalue = 0;
				$this->assignRef('defaultvalueRL',$defaultvalue);
				$iwcurrency		= $defaultvalue * $cparams['currencyratebuy'];
				$this->assignRef('defaultvalueIW',$iwcurrency);

				if($cparams['hasfee']) {
					if($cparams['transactionfeetype'] == "percent") {
						$transactionfee = 0;
					} else {
						$transactionfee = $cparams['transactionfee'];
					}
				} else {
					$transactionfee = 0;
				}
				$this->assignRef('transactionfee',$transactionfee);
			break;
		}

		parent::display($tpl);
	}
}
?>