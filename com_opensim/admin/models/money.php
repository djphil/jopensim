<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2017 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
 
defined('_JEXEC') or die();
/*jimport('joomla.application.component.model');*/

// import the Joomla modellist library
jimport('joomla.application.component.modellist');

require_once(JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'opensim.php');

class OpenSimModelMoney extends OpenSimModelOpenSim {
	public $_moneySettingsData;
	public $filename		= "money.php";
	public $view			= "money";

	public function __construct($config = array()) {
		parent::__construct($config);

		$input = JFactory::getApplication()->input;

		$app		= JFactory::getApplication();
		$limitstart	= $app->getUserStateFromRequest( 'money_limitstart', 'limitstart', 0, 'int' );
		$limit		= $app->getUserStateFromRequest( 'global.list.limit', 'limit', $app->getCfg('list_limit'), 'int' );
		$orderby	= $app->getUserStateFromRequest( 'money_filter_order', 'mfilter_order', '#__opensim_moneytransactions.time', 'STR' );
		$orderdir	= $app->getUserStateFromRequest( 'money_filter_order_Dir', 'mfilter_order_Dir', 'desc', 'STR' );
		$search		= $app->getUserStateFromRequest( 'money_filter_search', 'filter_search', '', 'STR' );

		$sender		= $app->getUserStateFromRequest( 'money_filter_sender','filter_sender','', 'STR');
		$receiver	= $app->getUserStateFromRequest( 'money_filter_receiver','filter_receiver','', 'STR');
		$orname		= $app->getUserStateFromRequest( 'money_filter_orname','filter_orname','', 'STR');
		$daterange	= $app->getUserStateFromRequest( 'money_filter_daterange','filter_daterange',0, 'int');

		$this->setState('limit', $input->get('limit',$limit,'INT'));
		$this->setState('money_limitstart', $input->get('limitstart',$limitstart,'INT'));
		$this->setState('money_filter_order', $input->get('filter_order',$orderby,'STR'));
		$this->setState('money_filter_order_Dir', $input->get('filter_order_Dir',$orderdir,'STR'));
		$this->setState('money_filter_search', $input->get('filter_search',$search,'STR'));

		$this->setState('money_filter_sender', $input->get('filter_sender',$sender,'STR'));
		$this->setState('money_filter_receiver', $input->get('filter_receiver',$receiver,'STR'));
		$this->setState('money_filter_orname', $input->get('filter_orname',$orname,'STR'));
		$this->setState('money_filter_daterange', $input->get('filter_daterange',$daterange,'int'));
	}

	protected function getListQuery() {
		$settings	= $this->getSettingsData();
		$db			= JFactory::getDBO();
		$query		= $db->getQuery(true);
		$query->select('#__opensim_moneytransactions.*');
		$query->from('#__opensim_moneytransactions');
		if(($settings['jopensimmoney_zerolines'] & 2) == 0) $query->where('#__opensim_moneytransactions.amount > 0');

		// Filter sender, receiver and ornames
		$sender		= $db->escape($this->getState('money_filter_sender'));
		$receiver	= $db->escape($this->getState('money_filter_receiver'));
		$orname		= $db->escape($this->getState('money_filter_orname'));
		if($orname == "or") {
			if(!empty($sender) && !empty($receiver)) {
				$query->where('(#__opensim_moneytransactions.sender = '.$db->quote($sender).' OR #__opensim_moneytransactions.receiver = '.$db->quote($receiver).')');
			} elseif(!empty($sender) && empty($receiver)) {
				$query->where('(#__opensim_moneytransactions.sender = '.$db->quote($sender).' OR #__opensim_moneytransactions.receiver = '.$db->quote($sender).')');
			} elseif(empty($sender) && !empty($receiver)) {
				$query->where('(#__opensim_moneytransactions.sender = '.$db->quote($receiver).' OR #__opensim_moneytransactions.receiver = '.$db->quote($receiver).')');
			}
		} else {
			if (!empty($sender)) {
				$query->where('#__opensim_moneytransactions.sender = '.$db->quote($sender));
			}
			if (!empty($receiver)) {
				$query->where('#__opensim_moneytransactions.receiver = '.$db->quote($receiver));
			}
		}

		// Filter daterange
		$daterange = $db->escape($this->getState('money_filter_daterange'));
		if ($daterange > 0) {
			$query->where('#__opensim_moneytransactions.`time` >= UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL '.(int)$daterange.' DAY))');
		}

		// regular Search Filter
		$search = $db->escape($this->getState('money_filter_search'));
		if($search) {
			$nameuuids = $this->convertSearch2UUID($search);
			if(is_array($nameuuids) && count($nameuuids) > 0) {
				foreach($nameuuids AS $nameuuid) {
					$sendersearch[]		= "#__opensim_moneytransactions.sender = '".$nameuuid."'";
					$receiversearch[]	= "#__opensim_moneytransactions.receiver = '".$nameuuid."'";
				}
				$sender		= implode(" OR ",$sendersearch);
				$receiver	= implode(" OR ",$receiversearch);
				$query->where("(#__opensim_moneytransactions.description LIKE '%".$db->escape($search)."%' OR ".$sender." OR ".$receiver.")");
			} else {
				$query->where("#__opensim_moneytransactions.description LIKE '%".$db->escape($search)."%'");
			}
		}

		$query->setLimit($this->getState('limit'),$this->getState('money_limitstart'));

		$orderby	= $db->escape($this->getState('money_filter_order'));
		$orderdir	= $db->escape($this->getState('money_filter_order_Dir'));
		$query->order($orderby." ".$orderdir);

		return $query;
	}

	public function getTransactionList() {
		$db		= JFactory::getDBO();
		$query	= $this->getListQuery();
		$db->setQuery($query);
		$transactions = $db->loadAssocList();
		return $transactions;
	}

	public function getBankerUser() {
		$filter['UserLevel']	= -2;
		$query					= $this->opensim->getUserQueryObject($filter,null,null,1);
		$db						= $this->opensim->_osgrid_db;
		$db->setQuery($query);
		$bankeruser = $db->loadAssocList();
		return $bankeruser;
	}

	public function getBalance($uuid) {
		if($this->_moneySettingsData['startBalance'] > 0) $init = $this->_moneySettingsData['startBalance'];
		else $init = 0;
		$this->balanceExists($uuid,$init); // see if $uuid exists and if not, create a balance line for it
		$query					= sprintf("SELECT balance FROM #__opensim_moneybalances WHERE `user` = '%s'",$uuid);
		$db						= JFactory::getDBO();
		$db->setQuery($query);
		$db->query();
		$userbalance			= $db->loadAssoc();
		$balance				= $userbalance['balance'];
		return $balance;
	}

	public function setBalance($uuid,$amount) {
		$this->balanceExists($uuid); // see if $uuid exists and if not, create a zero balance line for it
		$query	= sprintf("UPDATE #__opensim_moneybalances SET balance = balance + %d WHERE `user`= '%s'",$amount,$uuid);
		$db		= JFactory::getDBO();
		$db->setQuery($query);
		$db->query();
	}

	public function balanceExists($uuid,$amount = 0) { // if this $uuid does not exist yet, it will create a Balance with $amount for it
		$query	= sprintf("SELECT balance FROM #__opensim_moneybalances WHERE `user` = '%s'",$uuid);
		$db		= JFactory::getDBO();
		$db->setQuery($query);
		$db->query();
		$num_rows = $db->getNumRows();
		if($num_rows == 0) {
			$query = sprintf("INSERT INTO #__opensim_moneybalances (`user`,`balance`) VALUES ('%s','0')",$uuid);
			$db->setQuery($query);
			$db->query();

			$parameter['senderID']		= $this->_moneySettingsData['bankerUID'];
			$parameter['receiverID']	= $uuid;
			$parameter['amount']		= $amount;
			$parameter['description']	= JTEXT::_('JOPENSIM_MONEY_STARTBALANCE');

			$this->TransferMoney($parameter);
		}
	}

	public function getAllBalances($banker = null) {
		if(!$banker) $query = "SELECT SUM(#__opensim_moneybalances.balance) AS allbalances FROM #__opensim_moneybalances";
		else $query = sprintf("SELECT SUM(#__opensim_moneybalances.balance) AS allbalances FROM #__opensim_moneybalances WHERE user != '%s'",$banker);
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$summe = $db->loadResult();
		return $summe;
	}

	public function getPositiveBalances($banker = null) {
		if(!$banker) $query = "SELECT #__opensim_moneybalances.balance, #__opensim_moneybalances.user FROM #__opensim_moneybalances WHERE #__opensim_moneybalances.balance > 0";
		else $query = sprintf("SELECT #__opensim_moneybalances.balance, #__opensim_moneybalances.user FROM #__opensim_moneybalances WHERE #__opensim_moneybalances.balance > 0 AND #__opensim_moneybalances.user != '%s'",$banker);
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$positives = $db->loadAssocList();
		return $positives;
	}

	public function removeUserBalance($uuid) {
		$query = sprintf("DELETE FROM #__opensim_moneybalances WHERE #__opensim_moneybalances.user = '%s'",$uuid);
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$db->query();
	}

	public function balanceCorrection() {
		$banker		= JFactory::getApplication()->input->get('bankerUID');
		$balance	= JFactory::getApplication()->input->get('correctionvalue');
		$balance	*= -1;

		$query	= sprintf("UPDATE #__opensim_moneybalances SET #__opensim_moneybalances.balance = #__opensim_moneybalances.balance + '%d' WHERE #__opensim_moneybalances.user = '%s'",$balance,$banker);
		$db		= JFactory::getDBO();
		$db->setQuery($query);
		$db->query();
	}

	public function setBankerBalance() {
		$banker		= JFactory::getApplication()->input->get('bankerUID');
		$balance	= JFactory::getApplication()->input->get('bankerbalance');
		$balance	*= -1;

		$query	= sprintf("UPDATE #__opensim_moneybalances SET #__opensim_moneybalances.balance = '%d' WHERE #__opensim_moneybalances.user = '%s'",$balance,$banker);
		$db		= JFactory::getDBO();
		$db->setQuery($query);
		$db->query();
	}

	public function getCustomfee($uuid) {
		$query	= sprintf("SELECT #__opensim_money_customfees.uploadfee, #__opensim_money_customfees.groupfee FROM #__opensim_money_customfees WHERE #__opensim_money_customfees.PrincipalID = '%s'",$uuid);
		$db		= JFactory::getDBO();
		$db->setQuery($query);
		$customfees = $db->loadAssoc();
		return $customfees;
	}

	public function setCustomfee($uuid,$uploadfee,$groupfee) {
		$query = sprintf("INSERT INTO #__opensim_money_customfees (PrincipalID,uploadfee,groupfee) VALUES ('%1\$s','%2\$d','%3\$d')
							ON DUPLICATE KEY UPDATE uploadfee = '%2\$d', groupfee = '%3\$d'",
			$uuid,
			$uploadfee,
			$groupfee);
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$db->query();
		return $db->getAffectedRows();
	}

	public function removeCustomFee($uuid) {
		$query = sprintf("DELETE FROM #__opensim_money_customfees WHERE #__opensim_money_customfees.PrincipalID = '%s'",$uuid);
		$db =& JFactory::getDBO();
		$db->setQuery($query);
		$db->query();
		return $db->getAffectedRows();
	}

	public function populateState($ordering = null, $direction = null) {
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		//Filter (dropdown) sender
		$state = $this->getUserStateFromRequest($this->context.'.filter.sender', 'filter_sender', '', 'string');
		$this->setState('filter.sender', $state);

		//Filter (dropdown) receiver
		$state = $this->getUserStateFromRequest($this->context.'.filter.receiver', 'filter_receiver', '', 'string');
		$this->setState('filter.receiver', $state);

		//Filter (checkbox) senderORreceiver
		$state = $this->getUserStateFromRequest($this->context.'.filter.orname', 'filter_orname', '', 'string');
		$this->setState('filter.orname', $state);

		//Filter (dropdown) daterange
		$state = $this->getUserStateFromRequest($this->context.'.filter.daterange', 'filter_daterange', '', 'string');
		$this->setState('filter.daterange', $state);

		//Takes care of states: list. limit / start / ordering / direction
		parent::populateState($ordering,$direction);
	}

	public function getTotal() {
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		$query->select("#__opensim_moneytransactions.*");
		$query->from("#__opensim_moneytransactions");

		// regular Search Filter
		$search = $db->escape($this->getState('money_filter_search'));
		if($search) {
			$nameuuids = $this->convertSearch2UUID($search);
			if(is_array($nameuuids) && count($nameuuids) > 0) {
				foreach($nameuuids AS $nameuuid) {
					$sendersearch[]		= "#__opensim_moneytransactions.sender = '".$nameuuid."'";
					$receiversearch[]	= "#__opensim_moneytransactions.receiver = '".$nameuuid."'";
				}
				$sender		= implode(" OR ",$sendersearch);
				$receiver	= implode(" OR ",$receiversearch);
				$query->where("(#__opensim_moneytransactions.description LIKE '%".$db->escape($search)."%' OR ".$sender." OR ".$receiver.")");
			} else {
				$query->where("#__opensim_moneytransactions.description LIKE '%".$db->escape($search)."%'");
			}
		}

		// Filter sender, receiver and ornames
		$sender		= $db->escape($this->getState('money_filter_sender'));
		$receiver	= $db->escape($this->getState('money_filter_receiver'));
		$orname		= $db->escape($this->getState('money_filter_orname'));

		if($orname == "or") {
			if(!empty($sender) && !empty($receiver)) {
				$query->where('(#__opensim_moneytransactions.sender = '.$db->quote($sender).' OR #__opensim_moneytransactions.receiver = '.$db->quote($receiver).')');
			} elseif(!empty($sender) && empty($receiver)) {
				$query->where('(#__opensim_moneytransactions.sender = '.$db->quote($sender).' OR #__opensim_moneytransactions.receiver = '.$db->quote($sender).')');
			} elseif(empty($sender) && !empty($receiver)) {
				$query->where('(#__opensim_moneytransactions.sender = '.$db->quote($receiver).' OR #__opensim_moneytransactions.receiver = '.$db->quote($receiver).')');
			}
		} else {
			if (!empty($sender)) {
				$query->where('#__opensim_moneytransactions.sender = '.$db->quote($sender));
			}
			if (!empty($receiver)) {
				$query->where('#__opensim_moneytransactions.receiver = '.$db->quote($receiver));
			}
		}

		// Filter daterange
		$daterange = $db->escape($this->getState('money_filter_daterange'));
		if ($daterange > 0) {
			$query->where('#__opensim_moneytransactions.`time` >= UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL '.(int)$daterange.' DAY))');
		}

		$db->setQuery($query);
		$db->execute();
		return $db->getNumRows();
	}

	public function convertSearch2UUID($searchterm) {
		$query	= $this->opensim->getUserQueryObject($searchterm);
		$db		= $this->opensim->_osgrid_db;
		$db->setQuery($query);
		$uuids	= $db->loadColumn(2);
		if($this->_moneySettingsData['bankerName']) {
			if(stristr($this->_moneySettingsData['bankerName'],$searchterm)) $uuids[] = $this->_moneySettingsData['bankerUID'];
		}
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		$query->select('#__opensim_group.GroupID');
		$query->from('#__opensim_group');
		$query->where('#__opensim_group.Name LIKE '.$db->quote('%'.$db->escape($searchterm).'%'));
		$db->setQuery($query);
		$groups	= $db->loadColumn();

		$alluuids = array_merge($uuids,$groups);

		return $alluuids;
	}

	public function getSearchFields() {
		return array(	'#__opensim_moneytransactions.sender',
						'#__opensim_moneytransactions.receiver',
						'#__opensim_moneytransactions.description');
	}

	public function getPagination() {
		jimport('joomla.html.pagination');
		$this->_pagination = new JPagination($this->getTotal(), $this->getState('money_limitstart'), $this->getState('limit') );
		return $this->_pagination;
	}

}
?>