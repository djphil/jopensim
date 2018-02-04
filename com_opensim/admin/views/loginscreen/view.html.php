<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2017 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.view');

if(!defined('LOGINSCREEN_HELP_LINK')) define("LOGINSCREEN_HELP_LINK","http://wiki.jopensim.com/index.php/jOpenSim_HowTo_LoginscreenAttachModule");

 
class opensimViewLoginscreen extends JViewLegacy {
	public function display($tpl = null) {
		error_log("loginscreen view ".__LINE__.": REQUEST = ".var_export($_REQUEST,true));
		JHTML::_('behavior.modal');
		$this->sidebar	= null;
		$jinput			= JFactory::getApplication()->input;
		$task			= $jinput->get( 'task','','method','string');
		$model			= $this->getModel('loginscreen');

		$this->form	= $this->get('Form');
		$this->item	= $this->get('Item');

		$document		= JFactory::getDocument();
		$document->addStyleSheet(JURI::root(true).'/media/jui/css/icomoon.css');

		$this->settings	= $model->getSettingsData();

		$this->sidebar				= JHtmlSidebar::render();
		if($this->settings['loginscreen_layout'] == "classic") {
			$task	= "disabled";
		}
//		error_log("loginscreen view ".__LINE__.": this->sidebar = ".$this->sidebar);
		switch($task) {
			default:
				$this->positionlist	= $model->getPositions();
				if(!is_array($this->positionlist) || count($this->positionlist) == 0) $tpl = "empty";
				$this->setToolbar($tpl);
			break;
			case "newposition":
				$tpl	= "addnew";
				$maxid	= $model->getMaxId();
				$hint	= "jopensim_loginscreen_".$maxid;
				$this->form->setFieldAttribute('positionname','hint',$hint);
				$this->form->setValue('zindex',null,100);
				$this->setToolbar($tpl);
			break;
			case "editposition":
				$id	= $jinput->get('checkPosition');
				$positiondata	= $model->getPosition($id[0]);
				$this->form	= $model->getForm($positiondata);
				$this->form->setValue('id',null,$positiondata['id']);
				$this->form->setValue('positionname',null,$positiondata['positionname']);
				$this->form->setValue('alignH',null,$positiondata['alignH']);
				$this->form->setValue('alignV',null,$positiondata['alignV']);
				$this->form->setValue('posX',null,$positiondata['posX']);
				$this->form->setValue('posY',null,$positiondata['posY']);
				$this->form->setValue('zindex',null,$positiondata['zindex']);
				$tpl	= "editposition";
				$this->sidebar				= JHtmlSidebar::render();
				$this->setToolbar($tpl);
			break;
			case "setX":
			case "setY":
				$tpl			= "position";
				$this->posType 	= $task;
				$this->id		= $jinput->get('id');
				$this->position	= $model->getPosition($this->id);
				if($task == "setX") {
					$this->form->setValue('alignH',null,$this->position['alignH']);
					$this->form->setValue('distance',null,$this->position['posX']);
				} else {
					$this->form->setValue('alignV',null,$this->position['alignV']);
					$this->form->setValue('distance',null,$this->position['posY']);
				}
			break;
			case "disabled":
				$tpl = "disabled";
				$this->setToolbar($tpl);
			break;
		}
		parent::display($tpl);
	}

	public function setToolbar($tpl) {
		JToolBarHelper::title(JText::_('JOPENSIM_NAME')." ".JText::_('JOPENSIM_LOGINSCREEN'),'32-jopensim');
		switch($tpl) {
			default:
				JToolBarHelper::addNew("newposition",JText::_('JOPENSIM_LOGINSCREEN_ADDNEWPOS'));
				JToolBarHelper::editList("editposition",JText::_('JTOOLBAR_EDIT'));
				JToolBarHelper::deleteList(JText::_('JOPENSIM_LOGINSCREEN_DELETEPOSSURE'),"deleteposition",JText::_('JTOOLBAR_DELETE'));
				if (JFactory::getUser()->authorise('core.admin', 'com_opensim')) {
					JToolBarHelper::preferences('com_opensim','700','950',JText::_('JOPENSIM_GLOBAL_SETTINGS'));
				}
				JToolBarHelper::help("", false, JText::_('JOPENSIM_HELP'));
			break;
			case "addnew":
				JToolBarHelper::save('insertposition');
				JToolBarHelper::cancel('cancelinsert','JCANCEL');
				JToolBarHelper::help("", false, JText::_('JOPENSIM_HELP'));
			break;
			case "editposition":
				JToolBarHelper::save('updateposition');
				JToolBarHelper::cancel('cancelinsert','JCANCEL');
				JToolBarHelper::help("", false, JText::_('JOPENSIM_HELP'));
			break;
			case "empty":
				JToolBarHelper::addNew("newposition",JText::_('JOPENSIM_LOGINSCREEN_ADDNEWPOS'));
				if (JFactory::getUser()->authorise('core.admin', 'com_opensim')) {
					JToolBarHelper::preferences('com_opensim','700','950',JText::_('JOPENSIM_GLOBAL_SETTINGS'));
				}
				JToolBarHelper::help("", false, JText::_('JOPENSIM_HELP'));
			break;
			case "disabled":
				if (JFactory::getUser()->authorise('core.admin', 'com_opensim')) {
					JToolBarHelper::preferences('com_opensim','700','950',JText::_('JOPENSIM_GLOBAL_SETTINGS'));
				}
				JToolBarHelper::help("", false, JText::_('JOPENSIM_HELP'));
			break;
		}
	}

}
?>