<?php
/*
 * @component jOpenSimPayPal
 * @copyright Copyright (C) 2017 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
jimport('joomla.application.component.modellist');
/**
 * jOpenSimPayPal Transactions Model
 */
class jOpenSimPayPalModelTransactions extends jOpenSimPayPalModeljOpenSimPayPal {

	protected $searchInFields = array('payer_email','payer_firstname','payer_lastname','opensimid','amount_rlc','amount_iwc');

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
		$query->select('#__jopensimpaypal_transactions.id,#__jopensimpaypal_transactions.payer_email,#__jopensimpaypal_transactions.amount_rlc,#__jopensimpaypal_transactions.amount_iwc,#__jopensimpaypal_transactions.opensimid');
		$query->select('#__jopensimpaypal_transactions.transactiontime,#__jopensimpaypal_transactions.iwbalance,#__jopensimpaypal_transactions.mc_fee AS paypalfee');
		$query->select('CONCAT_WS(" ",#__jopensimpaypal_transactions.payer_firstname,#__jopensimpaypal_transactions.payer_lastname) AS name');
		$query->select('IFNULL(#__jopensimpaypal_currencies.symbol,"?") AS symbol');
		$query->select('IFNULL(#__users.username,"'.JText::_('COM_JOPENSIMPAYPAL_NOTAVAILABLE').'") AS joomlausername');
		// From the hello table
		$query->from('#__jopensimpaypal_transactions
							LEFT JOIN #__jopensimpaypal_currencies ON #__jopensimpaypal_transactions.currencyname = #__jopensimpaypal_currencies.currency
							LEFT JOIN #__users ON #__jopensimpaypal_transactions.joomlaid = #__users.id');

		// Filter search // Extra: Search more than one fields and for multiple words
		$regex = str_replace(' ', '|', $this->getState('filter.search'));
		if (!empty($regex)) {
			$regex=' REGEXP '.$db->quote($regex);
			$query->where('('.implode($regex.' OR ',$this->searchInFields).$regex.')');
		}

		// Filter payment status
		$paymentstatus = $db->escape($this->getState('filter.paymentstatus'));
		if (!empty($paymentstatus)) {
			$query->where('(#__jopensimpaypal_transactions.payment_type = '.$db->quote($paymentstatus).')');
		}

		// Filter daterange
		$daterange = $db->escape($this->getState('filter.daterange'));
		if (!empty($daterange)) {
//			if($daterange == 999999) $query->where('(#__jopensimpaypal_transactions.transactiontime IS NOT NULL)');
			$query->where('(#__jopensimpaypal_transactions.transactiontime >= DATE_SUB(NOW(), INTERVAL '.(int)$daterange.' DAY))');
		}

		$query->order('#__jopensimpaypal_transactions.transactiontime DESC');

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

		//Filter (dropdown) payment status
		$state = $this->getUserStateFromRequest($this->context.'.filter.paymentstatus', 'filter_paymentstatus', '', 'string');
		$this->setState('filter.paymentstatus', $state);

		//Filter (dropdown) daterange
		$state = $this->getUserStateFromRequest($this->context.'.filter.daterange', 'filter_daterange', '', 'string');
		$this->setState('filter.daterange', $state);

		//Takes care of states: list. limit / start / ordering / direction
		parent::populateState($ordering,$direction);
	}


}