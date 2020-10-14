<?php
/*
 * @component jOpenSimPayPal
 * @copyright Copyright (C) 2017 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die();
jimport('joomla.application.component.model');

require_once(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'jopensimpaypal.php');

class jOpenSimPayPalModeltransactionlist extends jOpenSimPayPalModeljOpenSimPayPal {
	public $filename		= "transactionlist.php";
	public $view			= "transactionlist";

	public function __construct() {
		parent::__construct();
	}

	public function paypalList($joomlaid) {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('#__jopensimpaypal_transactions.*');
		$query->from('#__jopensimpaypal_transactions');
		$query->order('#__jopensimpaypal_transactions.transactiontime DESC');
		$query->where('#__jopensimpaypal_transactions.joomlaid = '.(int)$joomlaid);
		$db->setQuery((string)$query);
		$paypal = $db->loadObjectList();
		return $paypal;
	}

	public function payoutList($joomlaid) {
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		$query->select('#__jopensimpaypal_payoutrequests.*');
		$query->select('IFNULL(ELT(#__jopensimpaypal_payoutrequests.`status`+3,"COM_JOPENSIMPAYPAL_PAYOUTSTATUS_CANCELED","COM_JOPENSIMPAYPAL_PAYOUTSTATUS_PENDING","COM_JOPENSIMPAYPAL_PAYOUTSTATUS_NEW","COM_JOPENSIMPAYPAL_PAYOUTSTATUS_APPROVED","COM_JOPENSIMPAYPAL_PAYOUTSTATUS_FINISHED"),"COM_JOPENSIMPAYPAL_PAYOUTSTATUS_UNKNOWN") AS payoutstatus');
		$query->select('IFNULL(ELT(#__jopensimpaypal_payoutrequests.`status`+3,"canceled.png","pending.png","new.png","approved.png","finished.png"),"unknown.png") AS payoutsymbol');
		$query->from('#__jopensimpaypal_payoutrequests');
		$query->order('#__jopensimpaypal_payoutrequests.requesttime DESC');
		$query->where('#__jopensimpaypal_payoutrequests.joomlaid = '.(int)$joomlaid);
		$db->setQuery((string)$query);
		$payout = $db->loadObjectList();
		return $payout;
	}

	public function addPayOutImage($payoutlist) {
		if(!is_array($payoutlist)) return FALSE;
		if(count($payoutlist) == 0) return $payoutlist;
		foreach($payoutlist AS $key => $payout) {
			$payoutlist[$key]->statusimage = JHtml::_('image','components/com_jopensimpaypal/assets/images/'.$payout->payoutsymbol,JText::_($payout->payoutstatus),array('title'=>JText::_($payout->payoutstatus)));
			$tooltip = array("text"=>"", "title"=>"");
			if($payout->status < 0 && $payout->remarks) {
				$tooltip['text'] = str_replace("\n","<br />",$payout->remarks);
			}
			if($payout->lastchange) {
				$tooltip['title'] = JText::sprintf('COM_JOPENSIMPAYPAL_PAYOUTSTATUS_LASTCHANGE',$payout->lastchange);
			}
			if($tooltip['text'] || $tooltip['title']) {
				$payoutlist[$key]->statusimage = JHTML::tooltip($tooltip['text'],$tooltip['title'],'',$payoutlist[$key]->statusimage);
			}

		}
		return $payoutlist;
	}

	public function revokePayOut() {
		$payoutid	= JFactory::getApplication()->input->get('id');
		$user		= JFactory::getUser();
		return $this->deletePayout($payoutid,$user->id);
	}

	protected function deletePayout($payoutid,$userid) {
		$result = FALSE;
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$conditions = array(
			'id = '.$db->quote($payoutid),
			'joomlaid = '.$db->quote($userid));
		$query->delete($db->quoteName('#__jopensimpaypal_payoutrequests'));
		$query->where($conditions);
		$db->setQuery($query);
		try {
			$result = $db->query();
		} catch (Exception $e) {
//			JFactory::getApplication()->enqueueMessage(JText::_('COM_JOPENSIMPAYPAL_PAYOUT_REVOKE_ERROR'),'error');
		}
		return $result;
	}

	public function __destruct() {
	}
}
?>