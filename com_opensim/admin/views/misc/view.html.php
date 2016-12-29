<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2015 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// no direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.view');
 
class opensimViewmisc extends JViewLegacy {
	public function display($tpl = null) {
		JHTML::_('behavior.modal');
		JHTML::stylesheet( 'opensim.css', 'administrator/components/com_opensim/assets/' );
		$this->model	= $this->getModel('misc');
		$settings		= $this->model->getSettingsData();
		$task			= JFactory::getApplication()->input->get( 'task', '', 'method', 'string');
		$this->sidebar	= JHtmlSidebar::render();

		$assetinfo = pathinfo(JPATH_COMPONENT_ADMINISTRATOR);
		$assetpath = "components".DIRECTORY_SEPARATOR.$assetinfo['basename'].DIRECTORY_SEPARATOR."assets".DS;
		$this->assignRef('assetpath',$assetpath);

		switch($task) {
			case "addregion":
				$remotehost = $settings['remotehost'];
				$this->assignRef('remotehost', $remotehost);
				$tpl = "addregion";
			break;
			case "sendmessage":
				$tpl = "sendmessage";
			break;
			case "terminals":
				$terminalList = $this->model->getTerminalList(1);
		 		$pagination =& $this->get('Pagination');
				$this->assignRef('pagination', $pagination);
				$this->assignRef('terminalList', $terminalList);
				$tpl = "terminals";
			break;
			case "terminaledit":
				$postdata = JFactory::getApplication()->input->request->getArray();;
				$terminalArray = $postdata['checkTerminal'];
				$terminalKey = $terminalArray[0];
				$terminalData = $this->model->getTerminal($terminalKey);
				$this->assignRef('terminal', $terminalData);
				$tpl = "terminaledit";
			break;
			case "pingTerminal":
				$terminalKey = JFactory::getApplication()->input->get('terminalKey');
				$terminalData = $this->model->getTerminal($terminalKey);
				$pingString = $terminalData['terminalUrl']."?ping=jOpenSim";
				$pingAnswer = @file_get_contents($pingString,FALSE,null,0,13);
				if($pingAnswer == "") $pingAnswer = JText::_('NOPINGANSWER');
				elseif($pingAnswer != "ok, I am here") $pingAnswer = JText::_('UNKNOWNPINGANSWER').": ".$pingAnswer."...";
				$this->assignRef('terminal', $terminalData);
				$this->assignRef('pingAnswer', $pingAnswer);
				$tpl = "terminalping";
			break;
			default:
				if($settings['enableremoteadmin'] == "1") {
					$misclinks['addregion'] = "<a href='index.php?option=com_opensim&view=misc&task=addregion'>".JText::_('JOPENSIM_ADDREGION')."</a>";
					$misclinks['sendmessage'] = "<a href='index.php?option=com_opensim&view=misc&task=sendmessage'>".JText::_('SENDGLOBALMESSAGE')."</a>";
				} else {
					$misclinks['addregion'] = JText::_('JOPENSIM_ADDREGION')." (".JText::_('DISABLED_NOREMOTEADMIN').")";
					$misclinks['sendmessage'] = JText::_('SENDGLOBALMESSAGE')." (".JText::_('DISABLED_NOREMOTEADMIN').")";
				}
				if($settings['addons'] & 8) {
					$misclinks['terminals'] = "<a href='index.php?option=com_opensim&view=misc&task=terminals'>".JText::_('MANAGETERMINALS')."</a>";
				}
			break;
		}
		$this->assignRef( 'misclinks', $misclinks );

		$this->_setToolbar($tpl);
		parent::display($tpl);
	}

	public function _setToolbar($tpl) {
		JToolBarHelper::title(JText::_('JOPENSIM_NAME')." ".JText::_('JOPENSIM_MISC'),'32-misc');
		$task = JFactory::getApplication()->input->get( 'task', '', 'method', 'string');

		switch($tpl) {
			case "addregion":
				JToolBarHelper::save('createregionsend');
				JToolBarHelper::cancel('canceladdregion','JCANCEL');
				JToolBarHelper::help("", false, JText::_('JOPENSIM_HELP_MISC_ADDREGION'));
			break;
			case "sendmessage":
				JToolBarHelper::publish('sendoutmessage');
				JToolBarHelper::cancel('cancelmessage','JCANCEL');
				JToolBarHelper::help("", false, JText::_('JOPENSIM_HELP_MISC_SENDMESSAGE'));
			break;
			case "terminals":
				JToolBarHelper::deleteList(JText::_('DELETETERMINALSURE'),"deleteTerminal",JText::_('DELETETERMINAL'),true,false);
				JToolBarHelper::editList('terminaledit');
				JToolBarHelper::cancel('cancelTerminal','JCANCEL');
			break;
			case "terminaledit":
				JToolBarHelper::save('saveTerminal');
				JToolBarHelper::cancel('terminals');
			break;
			default:
				$os_settings = $this->model->getSettingsData();
				if(isset($os_settings['remoteadmin_enabled']) && $os_settings['remoteadmin_enabled'] == 1) {
					JToolBarHelper::custom("sendmessage","osmisc","opensim",JText::_('SENDMESSAGE2USER'),false,false);
				}
				JToolBarHelper::help("", false, JText::_('JOPENSIM_HELP_MISC'));
			break;
		}
	}
}
?>