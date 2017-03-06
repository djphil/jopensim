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

class jopensimpaypalViewtransactionlist extends JViewLegacy {
	public function display($tpl = null) {
		JHTML::stylesheet( 'jopensimpaypal.css', 'components/com_jopensimpaypal/assets/' );
		JHTML::script('jopensimpaypal.js','components/com_jopensimpaypal/assets/');

		$model = $this->getModel('transactionlist');
		$cparams = $model->getParam("all");
		$this->currencyname = $cparams['currency'];

		$this->itemid = JRequest::getVar('Itemid');

		$user =& JFactory::getUser();
		if($user->guest) {
			JError::raiseWarning(100,JText::_('COM_JOPENSIMPAYPAL_ERROR_LOGINFIRST'));
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