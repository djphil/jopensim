<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// no direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.view');
JLoader::register('jOpenSimHelper', JPATH_COMPONENT.'/helpers/jopensimhelper.php');
JTable::addIncludePath(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'tables');
 
class opensimViewmisc extends JViewLegacy {
	public function display($tpl = null) {
		JHTML::_('behavior.modal');
//		$this->state		= $this->get('State');
		$document			= JFactory::getDocument();
		$this->model		= $this->getModel('misc');
		$this->settings			= $this->model->getSettingsData();
		if($this->settings['remoteadminsystem'] == "multiple") {
			$this->connectedsimulators = $this->model->getconnectedsimulators();
		} else {
			$this->connectedsimulators = null;
		}
		$task				= JFactory::getApplication()->input->get( 'task', '', 'method', 'string');
		$this->sidebar		= JHtmlSidebar::render();
		$this->canDo		= jOpenSimHelper::getActions();

		$assetinfo			= pathinfo(JPATH_COMPONENT_ADMINISTRATOR);
		$assetpath			= "components".DIRECTORY_SEPARATOR.$assetinfo['basename'].DIRECTORY_SEPARATOR."assets".DS;
		$this->assetpath	= $assetpath;
		$misclinks			= array();

		switch($task) {
			case "getopensimversion":
				$this->simulators	= $this->model->getSimulators();
				foreach($this->simulators AS $key => $simulator) {
					if($simulator['connected'] === FALSE) unset($this->simulators[$key]);
				}
				$tpl				= "getopensimversion";
			break;
			case "addregion":
				$remotehost			= $this->settings['remotehost'];
				$this->remotehost	= $remotehost;
				$this->simulators	= $this->model->getSimulators();
				foreach($this->simulators AS $key => $simulator) {
					if($simulator['connected'] === FALSE) unset($this->simulators[$key]);
				}
				$tpl				= "addregion";
			break;
			case "sendmessage":
				$this->simulators	= $this->model->getSimulators();
				foreach($this->simulators AS $key => $simulator) {
					if($simulator['connected'] === FALSE) unset($this->simulators[$key]);
				}
				$tpl				= "sendmessage";
			break;
			case "terminals":
				$terminalList		= $this->model->getTerminalList(1);
		 		$pagination			= $this->get('Pagination');
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
			case"managesimulators":
				$this->simulators	= $this->model->getSimulators();
				$tpl				= "managesimulators";
			break;
			default:
				$document->addStyleSheet(JURI::base(true).'/components/com_opensim/assets/quickiconstyle.css?v=2.6.8');
				if($this->canDo->get('core.remoteadmin')) {
					if ($this->settings['enableremoteadmin'] == "1") {
	                    $misclinks['addregion']       = $this->renderButton('index.php?option=com_opensim&view=misc&task=addregion','icon-48-region.png',JText::_('JOPENSIM_ADDREGION'));
//	                    $misclinks['addregion']         = "<a href='index.php?option=com_opensim&view=misc&task=addregion'>".JText::_('JOPENSIM_ADDREGION')."</a>";
	                    $misclinks['sendmessage']       = $this->renderButton('index.php?option=com_opensim&view=misc&task=sendmessage','icon-48-sendmessage.png',JText::_('JOPENSIM_SENDMESSAGE'),JText::_('JOPENSIM_SENDMESSAGE_DESC'));
//						$misclinks['sendmessage']       = "<a href='index.php?option=com_opensim&view=misc&task=sendmessage'>".JText::_('SENDGLOBALMESSAGE')."</a>";
	                    $misclinks['getopensimversion'] = $this->renderButton('index.php?option=com_opensim&view=misc&task=getopensimversion','icon-48-os-osversion.png',JText::_('GETOPENSIMVERSION'),JText::_('GETOPENSIMVERSION_DESC'));
//	                    $misclinks['getopensimversion'] = "<a href='index.php?option=com_opensim&view=misc&task=getopensimversion'>".JText::_('GETOPENSIMVERSION')."</a>";
	                } else {
	                    $misclinks['addregion']         = JText::_('JOPENSIM_ADDREGION')." (".JText::_('DISABLED_NOREMOTEADMIN').")";
						$misclinks['sendmessage']       = JText::_('SENDGLOBALMESSAGE')." (".JText::_('DISABLED_NOREMOTEADMIN').")";
	                    $misclinks['getopensimversion'] = JText::_('GETOPENSIMVERSION')." (".JText::_('DISABLED_NOREMOTEADMIN').")";
					}
				}
				if($this->settings['addons'] & 8) {
                    $misclinks['terminals']       = $this->renderButton('index.php?option=com_opensim&view=misc&task=terminals','icon-48-os-terminal.png',JText::_('JOPENSIM_TERMINALS'),JText::_('JOPENSIM_TERMINALS_DESC'));
//					$misclinks['terminals']		= "<a href='index.php?option=com_opensim&view=misc&task=terminals'>".JText::_('MANAGETERMINALS')."</a>";
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
				if($this->canDo->get('core.simulators')) {
					JToolbarHelper::apply('managesimulators','JOPENSIM_SIMULATORS');
				}
				JToolBarHelper::help("", false, JText::_('JOPENSIM_HELP_MISC_ADDREGION'));
			break;
			case "addregion":
				JToolBarHelper::addnew('createregionsend','JOPENSIM_ADDREGION');
				JToolBarHelper::cancel('canceladdregion','JCANCEL');
				if($this->canDo->get('core.simulators')) {
					JToolbarHelper::apply('managesimulators','JOPENSIM_SIMULATORS');
				}
				JToolBarHelper::help("", false, JText::_('JOPENSIM_HELP_MISC_ADDREGION'));
			break;
			case "sendmessage":
				JToolBarHelper::publish('sendoutmessage');
				JToolBarHelper::cancel('cancelmessage','JCANCEL');
				if($this->canDo->get('core.simulators')) {
					JToolbarHelper::apply('managesimulators','JOPENSIM_SIMULATORS');
				}
				JToolBarHelper::help("", false, JText::_('JOPENSIM_HELP_MISC_SENDMESSAGE'));
			break;
			case "terminals":
				if($this->canDo->get('core.delete')) {
					JToolBarHelper::deleteList(JText::_('DELETETERMINALSURE'),"deleteTerminal",JText::_('DELETETERMINAL'),true,false);
				}
				if($this->canDo->get('core.edit')) {
					JToolBarHelper::editList('terminaledit');
				}
				JToolBarHelper::cancel('cancelTerminal','JCANCEL');
			break;
			case "terminaledit":
				if($this->canDo->get('core.edit')) {
					JToolBarHelper::save('saveTerminal');
				}
				JToolBarHelper::cancel('terminals');
			break;
			case "managesimulators":
				if($this->canDo->get('core.edit')) {
					JToolBarHelper::save('saveSimulators');
				}
				JToolBarHelper::cancel('cancelSimulators','JCANCEL');
				if($this->canDo->get('core.delete')) {
					JToolBarHelper::deleteList(JText::_('JOPENSIM_SIMULATORS_DELETESURE'),"deleteSimulator",JText::_('JOPENSIM_SIMULATORS_DELETE'),true,true);
				}
			break;
			default:
				if($this->canDo->get('core.simulators')) {
					JToolbarHelper::apply('managesimulators','JOPENSIM_SIMULATORS');
				}
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

	public function getHostFrom($url) {
		return parse_url($url, PHP_URL_HOST);
	}

	public function getPortFrom($url) {
		return parse_url($url, PHP_URL_PORT);
	}

	public function renderButton($link,$image,$text,$title = "") {
		if(!$title) $title = $text;
		$params = array('title'=>$title, 'border'=>'0');
		$button  = "<div class='icon-wrapper'>";
		$button .= "<div class='icon'>";
		$button .= sprintf("<a href='%s' class='os_mainscreen'>",$link);
		$button .= JHTML::_('image', 'administrator/components/com_opensim/assets/images/'.$image,$title,$params);
		$button .= sprintf("<span>%s</span></a>",$text);
		$button .= "</div></div>\n";
		return $button;
	}
}
?>