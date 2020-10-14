<?php
/*
 * @component jOpenSimPayPal
 * @copyright Copyright (C) 2017 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class jopensimpaypalViewtransactionlist extends JViewLegacy {
	public function display($tpl = null) {
		$this->assetpath	= JUri::base(true)."/components/com_jopensimpaypal/assets/";
		$doc				= JFactory::getDocument();
		$doc->addStyleSheet($this->assetpath.'jopensimpaypal.css');
		$doc->addScript($this->assetpath.'jopensimpaypal.js');

		$model				= $this->getModel('transactionlist');
		$cparams			= $model->getParam("all");
		$this->currencyname	= $cparams['currency'];

		$this->itemid		= JFactory::getApplication()->input->get('Itemid');

		$user = JFactory::getUser();
		if($user->guest) {
			JFactory::getApplication()->enqueueMessage(JText::_('COM_JOPENSIMPAYPAL_ERROR_LOGINFIRST'),'warning');
			$tpl = "needlogin";
		} else {
			$opensimUID = $model->getUUID($user->id);
			$user->UUID = $opensimUID;

			$this->paypalList = $model->paypalList($user->id);
			$this->payoutList = $model->payoutList($user->id);
			$this->payoutList = $model->addPayOutImage($this->payoutList);

		}

		$this->revokeimage = JHtml::_('image','components/com_jopensimpaypal/assets/images/trash.png',JText::_('COM_JOPENSIMPAYPAL_PAYOUT_REVOKE'),array('title'=>JText::_('COM_JOPENSIMPAYPAL_PAYOUT_REVOKE')));
		$this->assignRef('user',$user);
		$this->assignRef('userUUID',$user->UUID);

		parent::display($tpl);
	}
}
?>