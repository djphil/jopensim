<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') or die();
/*jimport('joomla.application.component.model');*/
require_once(JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'opensim.php');

class OpenSimModelGroups extends OpenSimModelOpenSim {

	var $_data;
	var $filename = "groups.php";
	var $view = "groups";

	public function __construct() {
		parent::__construct();

		$input = JFactory::getApplication()->input;

		$app		= JFactory::getApplication();
		$limitstart	= $app->getUserStateFromRequest( 'groups_limitstart', 'limitstart', 0, 'int' );
		$limit		= $app->getUserStateFromRequest( 'global.list.limit', 'limit', $app->getCfg('list_limit'), 'int' );
		$orderby	= $app->getUserStateFromRequest( 'groups_filter_order', 'filter_order', '#__opensim_group.Name', 'STR' );
		$orderdir	= $app->getUserStateFromRequest( 'groups_filter_order_Dir', 'filter_order_Dir', 'asc', 'STR' );
		$search		= $app->getUserStateFromRequest( 'groups_filter_search', 'filter_search', '', 'STR' );

		$this->setState('limit', $input->get('limit',$limit,'INT'));
		$this->setState('groups_limitstart', $input->get('limitstart',$limitstart,'INT'));
		$this->setState('groups_filter_order', $input->get('filter_order',$orderby,'STR'));
		$this->setState('groups_filter_order_Dir', $input->get('filter_order_Dir',$orderdir,'STR'));
		$this->setState('groups_filter_search', $input->get('filter_search',$search,'STR'));
	}

	public function getGroupDetails($groupID = null) {
		$opensim = $this->opensim;
		$db		= JFactory::getDBO();
		$query = "SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));";
		$db->setQuery($query);
		$db->execute();
		$query	= $db->getQuery(true);
		$query->select("COUNT(DISTINCT(#__opensim_grouprolemembership.AgentID)) AS owners");
		$query->select("COUNT(DISTINCT(#__opensim_groupmembership.AgentID)) AS members");
		$query->select("#__opensim_group.*");
		$query->from("#__opensim_group");
		$query->join('LEFT',"#__opensim_grouprolemembership ON #__opensim_group.GroupID = #__opensim_grouprolemembership.GroupID AND #__opensim_group.OwnerRoleID = #__opensim_grouprolemembership.RoleID");
		$query->join('LEFT',"#__opensim_groupmembership ON #__opensim_group.GroupID = #__opensim_groupmembership.GroupID");
		$query->group("#__opensim_group.GroupID");
		if($groupID) $query->where($db->quoteName("#__opensim_group.GroupID")." = '".$groupID."'");
		elseif($this->getState('groups_filter_search')) {
			$search = "%".$db->escape($this->getState('groups_filter_search'),TRUE)."%";
			$searchfields = $this->getSearchFields();
			if(is_array($searchfields) && count($searchfields) > 0) {
				$where = array();
				foreach($searchfields AS $searchfield) $where[] = $db->quoteName($searchfield)." LIKE ".$db->quote($search,FALSE);
				$query->where(implode(" OR ",$where));
			}
		}
		$query->setLimit($this->getGroupState('limit'),$this->getGroupState('groups_limitstart'));
		$order	= $this->getState('groups_filter_order','#__opensim_group.Name');
		$sort	= $this->getState('groups_filter_order_Dir');
		$query->order($db->escape($order." ".$sort));

		$db->setQuery($query);
		$groupList = $db->loadAssocList();
		// get the Founders Name
		if(is_array($groupList)) {
			foreach($groupList AS $key => $group) {
				$founderdata = $opensim->getUserData($group['FounderID']);
				if(!isset($founderdata['firstname']) && !isset($founderdata['lastname'])) {
					$groupList[$key]['FounderName'] = JText::_('JOPENSIMUNKNOWN');
				} else {
					if(!isset($founderdata['firstname'])) $founderdata['firstname'] = JText::_('JOPENSIMUNKNOWN');
					if(!isset($founderdata['lastname'])) $founderdata['lastname'] = JText::_('JOPENSIMUNKNOWN');
					$groupList[$key]['FounderName'] = $founderdata['firstname']." ".$founderdata['lastname'];
				}
			}
		} else {
			$groupList = array(); // no groups, return an empty array
		}
		return $groupList;
	}

	public function getTotal() {
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		$query->select("#__opensim_group.*");
		$query->from("#__opensim_group");
		if($this->getState('groups_filter_search')) {
			$search = $db->quoteName($this->getState('groups_filter_search'));
			$searchfields = $this->getSearchFields();
			if(is_array($searchfields) && count($searchfields) > 0) {
				$where = array();
				foreach($searchfields AS $searchfield) $where[] = $searchfield." LIKE '%".$search."%'";
				$query->where(implode(" OR ",$where));
			}
		}
		$db->setQuery($query);
		$db->execute();
		return $db->getNumRows();
	}

	public function getGroupDetails_old($groupID = null) {
		$opensim = $this->opensim;
		$db = JFactory::getDBO();
		if($groupID) $groupWhere = sprintf("WHERE #__opensim_group.GroupID = '%s'",$groupID);
		else $groupWhere = "";
		$query = sprintf("SELECT
							COUNT(DISTINCT(#__opensim_grouprolemembership.AgentID)) AS owners,
							COUNT(DISTINCT(#__opensim_groupmembership.AgentID)) AS members,
							#__opensim_group.*
						FROM
							#__opensim_group
								LEFT JOIN #__opensim_grouprolemembership ON #__opensim_group.GroupID = #__opensim_grouprolemembership.GroupID AND #__opensim_group.OwnerRoleID = #__opensim_grouprolemembership.RoleID
								LEFT JOIN #__opensim_groupmembership ON #__opensim_group.GroupID = #__opensim_groupmembership.GroupID
						%s
						GROUP BY
							#__opensim_group.GroupID
						ORDER BY
							#__opensim_group.`Name` ASC",$groupWhere);
		$db->setQuery($query);
		$groupList = $db->loadAssocList();
		// get the Founders Name
		if(is_array($groupList)) {
			foreach($groupList AS $key => $group) {
				$founderdata = $opensim->getUserData($group['FounderID']);
				if(!isset($founderdata['firstname'])) $founderdata['firstname'] = JText::_('JOPENSIMUNKNOWN');
				if(!isset($founderdata['lastname'])) $founderdata['lastname'] = JText::_('JOPENSIMUNKNOWN');
				$groupList[$key]['FounderName'] = $founderdata['firstname']." ".$founderdata['lastname'];
			}
		} else {
			$groupList = array(); // no groups, return an empty array
		}
		return $groupList;
	}

	public function getGroupMembers($groupID) {
		$opensim	= $this->opensim;
		$db			= JFactory::getDBO();
		$query		= sprintf("SELECT * FROM #__opensim_groupmembership WHERE GroupID = '%s'",$groupID);
		$db->setQuery($query);
		$memberList = $db->loadAssocList();
		// get the Founders Name
		foreach($memberList AS $key => $member) {
			$memberdata = $opensim->getUserData($member['AgentID']);
			if(count($memberdata) > 0) {
				$memberList[$key]['memberdebug'] = $memberdata;
				$memberList[$key]['MemberName'] = $memberdata['firstname']." ".$memberdata['lastname'];
			} else {
				// invalid user?
				unset($memberList[$key]);
			}
		}
		return $memberList;
	}

	public function assignOwner($groupID,$roleID,$memberID) {
		$db		= JFactory::getDBO();
		$query	= sprintf("INSERT INTO #__opensim_grouprolemembership (GroupID,RoleID,AgentID) VALUES ('%s','%s','%s')",$groupID,$roleID,$memberID);
		$db->setQuery($query);
		$db->execute();
		return $db->getAffectedRows();
	}

	public function deleteGroups($groupArray) { // deletes all groups in $groupArray
		if(!is_array($groupArray) || count($groupArray) == 0) return 0; // no groups to delete?
		$count = 0;
		foreach($groupArray AS $group) {
			$count += $this->deleteGroup($group);
		}
		return $count;
	}

	public function deleteGroup($group) { // deletes the single group $group and returns the affectedRows at success
		$db		= JFactory::getDBO();
		$query	= sprintf("DELETE FROM #__opensim_grouprolemembership WHERE GroupID = '%s'",$group);
		$db->setQuery($query);
		$db->execute();
		$query = sprintf("DELETE FROM #__opensim_grouprole WHERE GroupID = '%s'",$group);
		$db->setQuery($query);
		$db->execute();
		$query = sprintf("DELETE FROM #__opensim_groupnotice WHERE GroupID = '%s'",$group);
		$db->setQuery($query);
		$db->execute();
		$query = sprintf("DELETE FROM #__opensim_groupmembership WHERE GroupID = '%s'",$group);
		$db->setQuery($query);
		$db->execute();
		$query = sprintf("DELETE FROM #__opensim_groupinvite WHERE GroupID = '%s'",$group);
		$db->setQuery($query);
		$db->execute();
		$query = sprintf("UPDATE #__opensim_groupactive SET #__opensim_groupactive.ActiveGroupID = '00000000-0000-0000-0000-000000000000' WHERE #__opensim_groupactive.ActiveGroupID = '%s'",$group);
		$db->setQuery($query);
		$db->execute();
		$query = sprintf("DELETE FROM #__opensim_group WHERE GroupID = '%s'",$group);
		$db->setQuery($query);
		$db->execute();
		return $db->getAffectedRows();
	}

	public function getPagination() {
		jimport('joomla.html.pagination');
		$this->_pagination = new JPagination($this->getTotal(), $this->getState('groups_limitstart'), $this->getState('limit') );
		return $this->_pagination;
	}

	public function getGroupState($state) {
		return $this->getState($state);
	}

	public function getSearchFields() {
		return array(	'#__opensim_group.Name',
						'#__opensim_group.Charter');
	}

	public function populateState() {
		$filter_order = JFactory::getApplication()->input->get('filter_order');
		$filter_order_Dir = JFactory::getApplication()->input->get('filter_order_Dir');
		$this->setState('filter_order', $filter_order);
		$this->setState('filter_order_Dir', $filter_order_Dir);
		parent::populateState();
	}
}
?>
