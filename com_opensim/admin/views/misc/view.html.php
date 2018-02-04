<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2017 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// no direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.view');
 
class opensimViewmisc extends JViewLegacy {
	public function display($tpl = null) {
		JHTML::_('behavior.modal');
		$document			= JFactory::getDocument();
		$document->addStyleSheet(JURI::base(true).'/components/com_opensim/assets/opensim.css');
		$this->model		= $this->getModel('misc');
		$settings			= $this->model->getSettingsData();
		$task				= JFactory::getApplication()->input->get( 'task', '', 'method', 'string');
		$this->sidebar		= JHtmlSidebar::render();

		$assetinfo			= pathinfo(JPATH_COMPONENT_ADMINISTRATOR);
		$assetpath			= "components".DIRECTORY_SEPARATOR.$assetinfo['basename'].DIRECTORY_SEPARATOR."assets".DS;
		$this->assetpath	= $assetpath;
		$misclinks			= array();

		switch($task) {
			case "getopensimversion":
				// $remotehost			= $settings['remotehost'];
				// $this->remotehost	= $remotehost;
				$tpl				= "getopensimversion";
			break;
			case "addregion":
				$remotehost			= $settings['remotehost'];
				$this->remotehost	= $remotehost;
				$tpl				= "addregion";
			break;
			case "sendmessage":
				$tpl				= "sendmessage";
			break;
			case "terminals":
				$terminalList		= $this->model->getTerminalList(1);
		 		$pagination			=& $this->get('Pagination');
				$this->pagination	= $pagination;
				$this->terminalList	= $terminalList;
				$tpl				= "terminals";
			break;
			case "terminaledit":
				$postdata			= JFactory::getApplication()->input->request->getArray();;
				$terminalArray		= $postdata['checkTerminal'];
				$terminalKey		= $terminalArray[0];
				$terminalData		= $this->model->getTerminal($terminalKey);
				$this->terminal		= $terminalData;
				$tpl				= "terminaledit";
			break;
			case "pingTerminal":
				$terminalKey		= JFactory::getApplication()->input->get('terminalKey');
				$terminalData		= $this->model->getTerminal($terminalKey);
				$pingString			= $terminalData['terminalUrl']."?ping=jOpenSim";
				$pingAnswer			= @file_get_contents($pingString,FALSE,null,0,13);
				if($pingAnswer == "") $pingAnswer = JText::_('NOPINGANSWER');
				elseif($pingAnswer != "ok, I am here") $pingAnswer = JText::_('UNKNOWNPINGANSWER').": ".$pingAnswer."...";
				$this->terminal		= $terminalData;
				$this->pingAnswer	= $pingAnswer;
				$tpl				= "terminalping";
			break;
			default:
				if ($settings['enableremoteadmin'] == "1") { 
                    $misclinks['addregion']         = "<a href='index.php?option=com_opensim&view=misc&task=addregion'>".JText::_('JOPENSIM_ADDREGION')."</a>";
					$misclinks['sendmessage']       = "<a href='index.php?option=com_opensim&view=misc&task=sendmessage'>".JText::_('SENDGLOBALMESSAGE')."</a>";
                    $misclinks['getopensimversion'] = "<a href='index.php?option=com_opensim&view=misc&task=getopensimversion'>".JText::_('GETOPENSIMVERSION')."</a>";
                } else {
                    $misclinks['addregion']         = JText::_('JOPENSIM_ADDREGION')." (".JText::_('DISABLED_NOREMOTEADMIN').")";
					$misclinks['sendmessage']       = JText::_('SENDGLOBALMESSAGE')." (".JText::_('DISABLED_NOREMOTEADMIN').")";
                    $misclinks['getopensimversion'] = JText::_('GETOPENSIMVERSION')." (".JText::_('DISABLED_NOREMOTEADMIN').")";
				}
				if($settings['addons'] & 8) {
					$misclinks['terminals']		= "<a href='index.php?option=com_opensim&view=misc&task=terminals'>".JText::_('MANAGETERMINALS')."</a>";
				}
			break;
		}
		$this->misclinks			= $misclinks;

		$this->_setToolbar($tpl);
		parent::display($tpl);
	}

	public function _setToolbar($tpl) {
		JToolBarHelper::title(JText::_('JOPENSIM_NAME')." ".JText::_('JOPENSIM_MISC'),'32-misc');
		$task = JFactory::getApplication()->input->get( 'task', '', 'method', 'string');

		switch($tpl) {
			case "getopensimversion":
                // JToolBarHelper::publish('getopensimulatorversion');
				JToolBarHelper::cancel('canceladdregion','JCANCEL');
				JToolBarHelper::help("", false, JText::_('JOPENSIM_HELP_MISC_ADDREGION'));
			break;
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
				if (JFactory::getUser()->authorise('core.admin', 'com_opensim')) {
					JToolBarHelper::preferences('com_opensim','700','950',JText::_('JOPENSIM_GLOBAL_SETTINGS'));
				}
				JToolBarHelper::help("", false, JText::_('JOPENSIM_HELP_MISC'));
			break;
		}
	}
}
?>