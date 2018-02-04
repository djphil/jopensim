<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2017 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class opensimViewuser extends JViewLegacy {
	public function display($tpl = null) {
		$document				= JFactory::getDocument();
		$document->addStyleSheet(JURI::base(true).'/components/com_opensim/assets/opensim.css');

		$model = $this->getModel('user');
		$this->sidebar			= JHtmlSidebar::render();
		$this->pagination		= $model->getPagination();
		$this->state			= $this->get('State');
		$this->sortDirection	= $model->getUserState('users_filter_order_Dir');
		$this->sortColumn		= $model->getUserState('users_filter_order');
		$this->limit			= $model->getUserState('limit');
		$this->limitstart		= $model->getUserState('users_limitstart');
		$zusatztext				= "";

		if(!$model->_osgrid_db) {
			JFactory::getApplication()->enqueueMessage(JText::sprintf('ERROR_NOSIMDB',JText::_('OPENSIMGRIDDB')),"error");
			$ueberschrift	= JText::_('USERMANAGEMENT');
			$errormsg		= "<br />\n".JText::_('ERROR_NOUSER')."<br />\n".JText::_('ERRORQUESTION1')."<br />\n".JText::_('ERRORQUESTION2')."<br />\n";
			$this->errormsg	= $errormsg;
			$tpl			= "nodb";
		} else {
			$task = JFactory::getApplication()->input->get( 'task', '', 'method', 'string');
			switch($task) {
				case "newuser":
					JFactory::getApplication()->input->set('hidemainmenu', 1);
					$ueberschrift			= JText::_('JOPENSIM_NEWUSER');
					$tpl					= "newuser";
	
					$firstname				= JFactory::getApplication()->input->get('firstname');
					$lastname				= JFactory::getApplication()->input->get('lastname');
					$email					= JFactory::getApplication()->input->get('email');
					$this->firstname		= $firstname;
					$this->lastname			= $lastname;
					$this->email			= $email;
				break;
				case "edituser":
					$ueberschrift			= JText::_('JOPENSIM_EDITUSER');
					$data					= JFactory::getApplication()->input->request->getArray();
					$userid					= $data['checkUser'];
					$userparams				= $model->getUserParams();
					JFactory::getApplication()->input->set('hidemainmenu', 1);
					$userdata				= $model->getUserData($userid[0]);
					$userlevellist			= $model->getUserLevels();
					$this->userdata			= $userdata;
					$this->userlevellist	= $userlevellist;
					$this->userid			= $userdata['uuid'];
					$this->firstname		= $userdata['firstname'];
					$this->lastname			= $userdata['lastname'];
					$this->email			= $userdata['email'];
					$this->userlevel		= $userdata['userlevel'];
					$this->userparams		= $userparams;
					$tpl					= "edituser";
					$usertitle				= JHTML::tooltip(JText::_('JOPENSIM_USERSETTING_TITLE_DESC'),JText::_('JOPENSIM_USERSETTING_TITLE'),'',JText::_('JOPENSIM_USERSETTING_TITLE'));
					$this->usertitle		= $usertitle;
				break;
				case "attachUser":
					$ueberschrift			= JText::_('USERMANAGEMENT');
					$postdata				= JFactory::getApplication()->input->request->getArray();;
					$userid					= $postdata['checkUser'][0];
					$this->userid			= $userid;
					$opensim_userdata		= $model->getUserData($postdata['checkUser'][0]);
					$this->opensim_userdata	= $opensim_userdata;
					$relation				= $model->getUserRelation($postdata['checkUser'][0]);
					if($relation[0]) $relationmethod = "update";
					else $relationmethod = "insert";
					$this->relationmethod	= $relationmethod;
					$this->relation			= $relation[0];
					$joomlalist				= $model->getJoomlaRelationList($postdata['checkUser'][0]);
					
					$this->joomlalist		= $joomlalist;
					$tpl					= "attachuser";
				break;
				case "applyok":
					$zusatztext				= " <div class='com_opensim_okmsg'>".JText::_('SETTINGSSAVEDOK')."</div>";
				default:
					$ueberschrift			= JText::_('USERMANAGEMENT');
					$model->getUserDataList();
					$this->UserQueryObject	= $model->UserQueryObject;
					$this->UserQueryObject->setLimit($model->getUserState('limit'),$model->getUserState('users_limitstart'));
					$model->_osgrid_db->setQuery($this->UserQueryObject);
					try {
						$this->users		= $this->usertestlist = $model->_osgrid_db->loadAssocList();
			 			$users				= $this->get('Data');
						$this->avatarusers	= $model->repopulateavatars();
					} catch(Exception $e) {
			 			$users				= array();
						JFactory::getApplication()->enqueueMessage(JText::_('JOPENSIM_NOUSER'),'warning');
						$tpl				= "nouser";
					}
					$filter					= JFactory::getApplication()->input->get('search');
			 		$this->filter			= $filter;
			 		$pagination				=& $this->get('Pagination');
					$this->pagination		= $pagination;
				break;
			}
		}

		$this->ueberschrift					= $ueberschrift;
 		$this->zusatztext					= $zusatztext;

		$this->_setToolbar($tpl);
		parent::display($tpl);
	}

	public function _setToolbar($tpl) {
		JToolBarHelper::title(JText::_('JOPENSIM_NAME')." ".JText::_('USERMANAGEMENT'),'32-user');
		switch($tpl) {
			case "newuser":
				JToolBarHelper::save('insertuser');
				JToolBarHelper::cancel('canceladduser','JCANCEL');
				JToolBarHelper::help("", false, JText::_('JOPENSIM_HELP_USER_ADD'));
			break;
			case "attachuser":
				JToolBarHelper::save('applyuserrelation');
				JToolBarHelper::cancel('cancelapplyuserrelation','JCANCEL');
				JToolBarHelper::help("", false, JText::_('JOPENSIM_HELP_USER_RELATION'));
			break;
			case "edituser":
				JToolBarHelper::save('saveuseredit');
				JToolBarHelper::cancel('canceluseredit','JCANCEL');
				JToolBarHelper::help("", false, JText::_('JOPENSIM_HELP_USER_EDIT'));
			break;
			default:
				JToolBarHelper::deleteList(JText::_('DELETEUSERSURE'),"deleteuser",JText::_('DELETEUSER'));
				$model = $this->getModel('user');
				$os_settings = $model->getSettingsData();
				JToolBarHelper::addNew("newuser",JText::_('ADDNEWUSER'));
				JToolBarHelper::editList("edituser",JText::_('JOPENSIM_EDITUSER'));
				JToolBarHelper::custom("attachUser","joomla2opensim","opensim",JText::_('ATTACHJOOMLA2OPENSIM'),true,false);
				JToolBarHelper::custom("repairUserStatus","userrepair","opensim",JText::_('REPAIRUSERSTATUS'),false,false);
				if($model->moneyEnabled === TRUE) {
					JToolBarHelper::custom("userMoney","usermoney","opensim",JText::_('JOPENSIMUSERMONEY'),true,false);
				}
				if (JFactory::getUser()->authorise('core.admin', 'com_opensim')) {
					JToolBarHelper::preferences('com_opensim','700','950',JText::_('JOPENSIM_GLOBAL_SETTINGS'));
				}
				JToolBarHelper::help("", false, JText::_('JOPENSIM_HELP_USER'));
			break;
		}
	}

	protected function getSortFields() {
		return array(
				'UserAccounts.PrincipalID' => JText::_('JOPENSIM_USER_PRINCIPALID'),
				'UserAccounts.FirstName' => JText::_('JOPENSIM_USER_FIRSTNAME'),
				'UserAccounts.LastName' => JText::_('JOPENSIM_USER_LASTNAME'),
				'UserAccounts.Email' => JText::_('JOPENSIM_USER_EMAIL'),
				'UserAccounts.Created' => JText::_('JOPENSIM_USER_CREATED'),
				'GridUser.Online' => JText::_('JOPENSIM_USER_ONLINESTATUS'),
				'GridUser.Login' => JText::_('JOPENSIM_USER_LOGIN'),
		);
	}
}

?>