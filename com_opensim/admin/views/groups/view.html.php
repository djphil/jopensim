<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2017 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class opensimViewgroups extends JViewLegacy {
	public function display($tpl = null) {
		JHTML::_('behavior.modal');
		$document		= JFactory::getDocument();
		$document->addStyleSheet(JURI::base(true).'/components/com_opensim/assets/opensim.css');

		$model			= $this->getModel('groups');
		$settingsdata	= $model->getSettingsData();

		$this->pagination		= $model->getPagination();
		$this->state			= $this->get('State');
		$this->sortDirection	= $model->getGroupState('groups_filter_order_Dir');
		$this->sortColumn		= $model->getGroupState('groups_filter_order');
		$this->limit			= $model->getGroupState('limit');
		$this->limitstart		= $model->getGroupState('groups_limitstart');

		$jOpenSim = JComponentHelper::getComponent('com_opensim',TRUE);
		$this->params	= new JRegistry($jOpenSim->params);

		$assetinfo = pathinfo(JPATH_COMPONENT_ADMINISTRATOR);
		$assetpath = "components".DIRECTORY_SEPARATOR.$assetinfo['basename'].DIRECTORY_SEPARATOR."assets".DS;
		$this->assetpath	= $assetpath;
		$settingsdata['groupaddon'] = $settingsdata['addons'] & 4;
		if(!$settingsdata['groupaddon']) {
			JFactory::getApplication()->enqueueMessage(JText::_('JOPENSIM_GROUPADDONDISABLED'),'warning');
		}

		$task = JFactory::getApplication()->input->get( 'task', '', 'method', 'string');

		$this->ueberschrift = JText::_('JOPENSIM_ADDONS_GROUPS');

		$this->sidebar = null;

		switch($task) {
			default:
				$this->sidebar		= JHtmlSidebar::render();
				$this->groupList	= $model->getGroupDetails();
				$this->zerouuid		= $model->opensim->zerouid;
				$this->settings		= $settingsdata;
			break;
			case "charta":
				$groupID = JFactory::getApplication()->input->get('groupID','','method','string');
				$groupDetails = $model->getGroupDetails($groupID);
				$this->charta = $groupDetails[0]['Charter'];
				$tpl = "charta";
			break;
			case "grouprepair":
				$groupID			= JFactory::getApplication()->input->get('groupID','','method','string');
				$groupDetails		= $model->getGroupDetails($groupID);
				$this->groupDetails	= $groupDetails[0];
				$this->groupMembers	= $model->getGroupMembers($groupID);
				$tpl = "repair";
			break;
		}

		$this->_setToolbar($tpl);
		parent::display($tpl);
	}

	public function _setToolbar($tpl) {
		JToolBarHelper::title(JText::_('JOPENSIM_NAME')." ".JText::_('JOPENSIM_ADDONS_GROUPS'),'32-groups');
		switch($tpl) {
			default:
				JToolBarHelper::deleteList(JText::_('JOPENSIM_DELETEGROUPSSURE'),"deleteGroups",JText::_('JOPENSIM_DELETEGROUPS'),true,false);
				JToolBarHelper::help("", false, JText::_('JOPENSIM_HELP_GROUPS'));
			break;
		}
	}

	protected function getSortFields() {
		return array(
				'#__opensim_group.Name' => JText::_('JOPENSIM_GROUPNAME'),
				'#__opensim_group.FounderID' => JText::_('JOPENSIM_GROUPFOUNDER_SEARCH'),
				'COUNT(DISTINCT(#__opensim_grouprolemembership.AgentID))' => JText::_('JOPENSIM_GROUPOWNERS'),
				'COUNT(DISTINCT(#__opensim_groupmembership.AgentID))' => JText::_('JOPENSIM_GROUPMEMBERS')
		);
	}
}

?>