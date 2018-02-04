<?php
/*
 * @component jOpenSimPayPal
 * @copyright Copyright (C) 2017 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
jimport('joomla.application.component.modellist');

class jOpenSimPayPalModelPayout extends jOpenSimPayPalModeljOpenSimPayPal {

	protected $searchInFields = array('opensimid','paypalaccount','amount_iwc','amount_rlc');

	public function __construct($config = array()) {
		$config['filter_fields'] = $this->searchInFields;
		parent::__construct($config);
	}

	/**
	* Method to build an SQL query to load the list data.
	*
	* @return      string  An SQL query
	*/
	protected function getListQuery() {
		// Create a new query object.           
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		// Select some fields
		$query->select('#__jopensimpaypal_payoutrequests.id,#__jopensimpaypal_payoutrequests.joomlaid,#__jopensimpaypal_payoutrequests.opensimid,#__jopensimpaypal_payoutrequests.amount_iwc,#__jopensimpaypal_payoutrequests.amount_rlc,#__jopensimpaypal_payoutrequests.xchangerate,#__jopensimpaypal_payoutrequests.transactionfee,#__jopensimpaypal_payoutrequests.paypalaccount,#__jopensimpaypal_payoutrequests.requesttime,#__jopensimpaypal_payoutrequests.status,#__jopensimpaypal_payoutrequests.remarks');
		$query->select('IFNULL(#__jopensimpaypal_currencies.symbol,"'.JText::_('COM_JOPENSIMPAYPAL_UNKNOWNCURRENCY').'") AS symbol');
		$query->select('IFNULL(#__users.username,"'.JText::_('COM_JOPENSIMPAYPAL_NOTAVAILABLE').'") AS joomlausername');
		$query->select('IFNULL(#__opensim_moneybalances.balance,"'.JText::_('COM_JOPENSIMPAYPAL_NOTAVAILABLE').'") AS iwbalance');
		// From the payout table
		$query->from('#__jopensimpaypal_payoutrequests
							LEFT JOIN #__jopensimpaypal_currencies ON #__jopensimpaypal_payoutrequests.currency_rlc = #__jopensimpaypal_currencies.currency
							LEFT JOIN #__users ON #__jopensimpaypal_payoutrequests.joomlaid = #__users.id
							LEFT JOIN #__opensim_moneybalances ON #__jopensimpaypal_payoutrequests.opensimid = #__opensim_moneybalances.user');

		// Filter search // Extra: Search more than one fields and for multiple words
		$regex = str_replace(' ', '|', $this->getState('filter.search'));
		if (!empty($regex)) {
			$regex=' REGEXP '.$db->quote($regex);
			$query->where('('.implode($regex.' OR ',$this->searchInFields).$regex.')');
		}

		// Filter payout status
		$payoutstatus = $db->escape($this->getState('filter.payoutstatus'));
		if ($payoutstatus != "") {
			if($payoutstatus == "unsolved") $query->where('FIND_IN_SET(#__jopensimpaypal_payoutrequests.`status`,"-1,0,1")');
			else $query->where('(#__jopensimpaypal_payoutrequests.status = '.(int)$payoutstatus.')');
		}

		// Filter daterange
		$daterange= $db->escape($this->getState('filter.daterange'));
		if (!empty($daterange)) {
			if($daterange == 999999) $query->where('(#__jopensimpaypal_payoutrequests.requesttime IS NOT NULL)');
			else $query->where('(#__jopensimpaypal_payoutrequests.requesttime >= DATE_SUB(NOW(), INTERVAL '.(int)$daterange.' DAY))');
		}

		$query->order('#__jopensimpaypal_payoutrequests.requesttime DESC');

//		$test = var_export($query,TRUE);
//		JError::raiseWarning(100,$test);

		return $query;
	}

	/**
	* Method to auto-populate the model state.
	*
	* Note. Calling getState in this method will result in recursion.
	*
	* @since       1.6
	*/
	protected function populateState($ordering = null, $direction = null) {
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		//Omit double (white-)spaces and set state
		$this->setState('filter.search', preg_replace('/\s+/',' ', $search));

		//Filter (dropdown) payout status
		$state = $this->getUserStateFromRequest($this->context.'.filter.payoutstatus', 'filter_payoutstatus', '', 'string');
		$this->setState('filter.payoutstatus', $state);

		//Filter (dropdown) company
		$state = $this->getUserStateFromRequest($this->context.'.filter.daterange', 'filter_daterange', '', 'string');
		$this->setState('filter.daterange', $state);

		//Takes care of states: list. limit / start / ordering / direction
		parent::populateState($ordering,$direction);
	}

	public function addTransactions($items) {
		if(!is_array($items)) return FALSE;
		foreach($items AS $key => $item) {
			$paypalaccounts = $this->transactionpaypal($item->joomlaid);
			$items[$key]->transactionpaypal = $paypalaccounts;
			if(count($paypalaccounts) == 0) {
				$items[$key]->paypalstatus = "none";
				$items[$key]->paypalaccount = JHTML::tooltip(JText::_('COM_JOPENSIMPAYPAL_PAYOUT_NOTRANSACTIONFOUND'),JText::_('COM_JOPENSIMPAYPAL_PAYOUT_NOTRANSACTIONFOUND_TITLE'),'',$item->paypalaccount);
			} elseif(in_array($item->paypalaccount,$paypalaccounts)) {
				$items[$key]->paypalstatus = "ok";
			} else {
				$items[$key]->paypalstatus = "other";
				$items[$key]->paypalaccount = JHTML::tooltip(JText::_('COM_JOPENSIMPAYPAL_PAYOUT_OTHERSFOUND'),JText::_('COM_JOPENSIMPAYPAL_PAYOUT_OTHERSFOUND_TITLE'),'',$item->paypalaccount);
			}

			// payout status symbol
			// nothing really something from transactions, but can be added here in one go ;)
			switch($item->status) {
				case -2:
					$items[$key]->payoutsymbol	= "<span class='state expired' title='".JText::_('COM_JOPENSIMPAYPAL_PAYOUTSTATUS_CANCELED')." ".JText::_('COM_JOPENSIMPAYPAL_PAYOUTSTATUS_CHANGE')."'><span class='text'>".JText::_('COM_JOPENSIMPAYPAL_PAYOUTSTATUS_CANCELED')."</span></span>";
					$items[$key]->currentstatus	= JText::_('COM_JOPENSIMPAYPAL_PAYOUTSTATUS_CANCELED');
				break;
				case -1:
					$items[$key]->payoutsymbol = "<span class='state pending' title='".JText::_('COM_JOPENSIMPAYPAL_PAYOUTSTATUS_PENDING')." ".JText::_('COM_JOPENSIMPAYPAL_PAYOUTSTATUS_CHANGE')."'><span class='text'>".JText::_('COM_JOPENSIMPAYPAL_PAYOUTSTATUS_PENDING')."</span></span>";
					$items[$key]->currentstatus	= JText::_('COM_JOPENSIMPAYPAL_PAYOUTSTATUS_PENDING');
				break;
				case 0:
					$items[$key]->payoutsymbol = "<span class='state unpublish' title='".JText::_('COM_JOPENSIMPAYPAL_PAYOUTSTATUS_NEW')." ".JText::_('COM_JOPENSIMPAYPAL_PAYOUTSTATUS_CHANGE')."'><span class='text'>".JText::_('COM_JOPENSIMPAYPAL_PAYOUTSTATUS_NEW')."</span></span>";
					$items[$key]->currentstatus	= JText::_('COM_JOPENSIMPAYPAL_PAYOUTSTATUS_NEW');
				break;
				case 1:
					$items[$key]->payoutsymbol = "<span class='state publish' title='".JText::_('COM_JOPENSIMPAYPAL_PAYOUTSTATUS_APPROVED')." ".JText::_('COM_JOPENSIMPAYPAL_PAYOUTSTATUS_CHANGE')."'><span class='text'>".JText::_('COM_JOPENSIMPAYPAL_PAYOUTSTATUS_APPROVED')."</span></span>";
					$items[$key]->currentstatus	= JText::_('COM_JOPENSIMPAYPAL_PAYOUTSTATUS_APPROVED');
				break;
				case 2:
					$items[$key]->payoutsymbol = "<span class='state icon-16-featured' title='".JText::_('COM_JOPENSIMPAYPAL_PAYOUTSTATUS_FINISHED')." ".JText::_('COM_JOPENSIMPAYPAL_PAYOUTSTATUS_CHANGE')."'><span class='text'>".JText::_('COM_JOPENSIMPAYPAL_PAYOUTSTATUS_FINISHED')."</span></span>";
					$items[$key]->currentstatus	= JText::_('COM_JOPENSIMPAYPAL_PAYOUTSTATUS_FINISHED');
				break;
				default:
					$items[$key]->payoutsymbol = "<span class='state notdefault' title='".JText::_('COM_JOPENSIMPAYPAL_PAYOUTSTATUS_UNKNOWN')." ".JText::_('COM_JOPENSIMPAYPAL_PAYOUTSTATUS_CHANGE')."'><span class='text'>".JText::_('COM_JOPENSIMPAYPAL_PAYOUTSTATUS_UNKNOWN')."</span></span>";
					$items[$key]->currentstatus	= JText::_('COM_JOPENSIMPAYPAL_PAYOUTSTATUS_UNKNOWN');
				break;
			}
		}
		return $items;
	}

	public function payoutSums($items) {
		if(!is_array($items)) return FALSE;
		foreach($items AS $key => $item) {
			$payoutsum = $this->payoutSum($item->joomlaid);
			$items[$key]->payoutsum = $payoutsum;
			if($item->iwbalance == JText::_('COM_JOPENSIMPAYPAL_NOTAVAILABLE')) {
				$items[$key]->payoutstatus = "error";
				$items[$key]->iwbalance = JHTML::tooltip(JText::sprintf('COM_JOPENSIMPAYPAL_PAYOUT_NOBALANCEFOUND',$item->opensimid),JText::_('COM_JOPENSIMPAYPAL_PAYOUT_NOBALANCEFOUND_TITLE'),'',$item->iwbalance);
			} elseif($item->amount_iwc > $item->iwbalance) {
				$items[$key]->payoutstatus = "error";
				$balance = "<span>".strval(number_format($item->iwbalance,0,JText::_('COM_JOPENSIMPAYPAL_SEPERATOR_COMMA'),JText::_('COM_JOPENSIMPAYPAL_SEPERATOR_THOUSAND')))."</span>";
				$items[$key]->iwbalance = JHTML::tooltip(JText::_('COM_JOPENSIMPAYPAL_PAYOUT_INSUFFICIENT'),JText::_('COM_JOPENSIMPAYPAL_PAYOUT_INSUFFICIENT_TITLE'),'',$balance);
//				$items[$key]->iwbalance = "bla";
//				$items[$key]->iwbalance = $balance;
			} elseif($payoutsum > $item->iwbalance) {
				$items[$key]->payoutstatus = "warning";
				$items[$key]->iwbalance = JHTML::tooltip(JText::_('COM_JOPENSIMPAYPAL_PAYOUT_INSUFFICIENTWARNING'),JText::_('COM_JOPENSIMPAYPAL_PAYOUT_INSUFFICIENTWARNING_TITLE'),'',number_format($item->iwbalance,0,JText::_('COM_JOPENSIMPAYPAL_SEPERATOR_COMMA'),JText::_('COM_JOPENSIMPAYPAL_SEPERATOR_THOUSAND')));
			} else {
				$items[$key]->payoutstatus = "ok";
				$items[$key]->iwbalance = number_format($item->iwbalance,0,JText::_('COM_JOPENSIMPAYPAL_SEPERATOR_COMMA'),JText::_('COM_JOPENSIMPAYPAL_SEPERATOR_THOUSAND'));
			}
		}
		return $items;
	}

	public function payoutSum($joomlaid) {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('SUM(#__jopensimpaypal_payoutrequests.amount_iwc)');
		$query->from('#__jopensimpaypal_payoutrequests');
		$query->where('#__jopensimpaypal_payoutrequests.joomlaid = '.(int)$joomlaid);
//		$query->where('#__jopensimpaypal_payoutrequests.status = 0');
		$query->where('FIND_IN_SET(#__jopensimpaypal_payoutrequests.`status`,"-1,0,1")');
		$query->group('#__jopensimpaypal_payoutrequests.joomlaid');
		$db->setQuery((string)$query);
		$payoutsum = $db->loadResult();
		return $payoutsum;
	}

	public function getItemFromID($items,$id) {
		if(!is_array($items)) return FALSE;
		foreach($items AS $item) {
			if($item->id == $id) return $item;
		}
		return FALSE;
	}
}