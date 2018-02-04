<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2017 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.view');
 
class opensimViewopensim extends JViewLegacy {
	public function display($tpl = null) {
		JHTML::_('behavior.modal');
		$this->sidebar	= null;
		$jinput			= JFactory::getApplication()->input;
		$task			= JFactory::getApplication()->input->get( 'task','','method','string');
		$model			= $this->getModel('opensim');

		$this->settings	= $model->getSettingsData();

		// Ensure that the current banker has a balance row
		if($this->settings['addons_currency'] == 1 && $this->settings['jopensimmoneybanker']) $model->balanceExists($this->settings['jopensimmoneybanker']);

		switch($task) {
			case "editcss":
				$this->csswritable			= FALSE;
				$this->sidebar				= JHtmlSidebar::render();
				if (!JFactory::getUser()->authorise('core.admin', 'com_opensim')) {
					$tpl					= "noaccess";
					$this->setToolbar("csserror");
				} else {
					$tpl					= "editcss";
					$cssfile				= $model->frontendCSS();
					$this->cssfile			= $cssfile;
					if (JFile::exists($cssfile))  {
						if(is_writable($cssfile)) {
							$this->csswritable = TRUE;
							$cssmsg			= "<span style='color:#009900'>".JText::_('JOPENSIM_CSSWRITEABLE')."</span>";
							$this->setToolbar($tpl);
						} else {
							$cssmsg			= "";
							JFactory::getApplication()->enqueueMessage(JText::_('JOPENSIM_CSSNOTWRITEABLE')."<br /><span style='color:#CC0000'>".JText::sprintf('JOPENSIM_CSSNOTWRITEABLE_DESC',$cssfile)."</span>", 'warning');
							$this->setToolbar("csserror");
						}
						$csscontent			= file_get_contents($cssfile);
						$this->cssmsg		= $cssmsg;
						$this->csscontent	= $csscontent;
					} else {
						JFactory::getApplication()->enqueueMessage(JText::_('JOPENSIM_CSSNOTFOUND'), 'error');
					}
				}
			break;
			case "gettexture":
				$tpl = "gettexture";
				if(substr($this->settings['opensim_host'],0,4) != "http") $this->opensimhost = "http://".$this->settings['opensim_host'];
				else $this->opensimhost		= $this->settings['opensim_host'];
				$this->robust_port			= $this->settings['robust_port'];
				$this->getTextureEnabled	= $this->settings['getTextureEnabled'];
				$this->textureFormat		= $this->settings['getTextureFormat'];
				$this->textureID			= $jinput->get('textureid',$model->opensim->zerouid,'STR');
				$this->zeroUUID				= $model->opensim->zerouid;
				$this->fileinfo				= @getimagesize($this->opensimhost.":".$this->settings['robust_port']."/CAPS/GetTexture/?texture_id=".$this->textureID."&format=".$this->textureFormat);
			break;
			case"importsettings":
				$this->sidebar				= JHtmlSidebar::render();
				$this->pagetitle			= JText::_('JOPENSIM_IMPORTSETTINGSTITLE');
				$this->pagenote				= JText::_('JOPENSIM_IMPORTSETTINGSNOTE');
				$tpl = "importsettings";
				$this->setToolbar($tpl);
			break;
			default:
				$this->sidebar				= JHtmlSidebar::render();
				$document					= JFactory::getDocument();
				$document->addStyleSheet(JURI::base(true).'/components/com_opensim/assets/quickiconstyle.css?v=2.6.8');
				$version					= $model->getVersion();
				$recentversion				= $model->checkversion();
				$settings					= $model->_settingsData;
				$this->addons				= $settings['addons'];
				$document->addStyleSheet(JURI::root(true).'/media/jui/css/icomoon.css');
				$params 					= JComponentHelper::getParams('com_opensim');
				$message					= JText::_('JOPENSIM_PLS_CHOOSE_OPTION');
				$this->version				= $version;
				$this->message				= $message;
				$this->recentversion		= $recentversion;
				$button['quickicon']		= $this->renderPlainButton('quickicon_jopensim.php',JText::_('JOPENSIM_GRIDSTATUS'));
				$button['maps']				= $this->renderButton('index.php?option=com_opensim&view=maps','icon-48-os-maps.png',JText::_('JOPENSIM_MAPS'));
				$button['user']				= $this->renderButton('index.php?option=com_opensim&view=user','icon-48-os-user.png',JText::_('JOPENSIM_USER'));
				if(($settings['addons'] &  4) == 4) {
					$button['groups']		= $this->renderButton('index.php?option=com_opensim&view=groups','icon-48-os-group.png',JText::_('JOPENSIM_GROUPS'));
				}
				if(($settings['addons'] &  16) == 16) {
					$button['search']		= $this->renderButton('index.php?option=com_opensim&view=search','icon-48-os-search.png',JText::_('JOPENSIM_SEARCH'));
				}
				if(($settings['addons'] &  32) == 32) {
					$button['money']		= $this->renderButton('index.php?option=com_opensim&view=money','icon-48-money.png',JText::_('JOPENSIM_MONEY'));
				}
				$button['misc']				= $this->renderButton('index.php?option=com_opensim&view=misc','icon-48-os-misc.png',JText::_('JOPENSIM_MISC'));
				$button['addons']			= $this->renderButton('index.php?option=com_opensim&view=addons','icon-48-addonhelp.png',JText::_('JOPENSIM_ADDONS'));
				$this->adminbuttons			= $button;
				$jOpenSim					= JComponentHelper::getComponent('com_opensim',TRUE);
				$this->params				= new JRegistry($jOpenSim->params);
				$this->setToolbar($tpl);
			break;
		}
		parent::display($tpl);
	}

	public function setToolbar($tpl) {
		JToolBarHelper::title(JText::_('JOPENSIM_CONTROL_PANEL'),'32-jopensim');
		switch($tpl) {
			case "editcss":
				JToolBarHelper::apply('applycss');
				JToolBarHelper::save('savecss');
				JToolBarHelper::cancel();
				JToolBarHelper::help("", false, JText::_('JOPENSIM_HELP_EDITCSS'));
			break;
			case "csserror":
				JToolBarHelper::cancel();
				JToolBarHelper::help("", false, JText::_('JOPENSIM_HELP_EDITCSS'));
			break;
			default:
				if (JFactory::getUser()->authorise('core.admin', 'com_opensim')) {
					JToolBarHelper::preferences('com_opensim','700','950',JText::_('JOPENSIM_GLOBAL_SETTINGS'));
					JToolBarHelper::editList('editcss','JOPENSIM_EDITCSS');
				}
				JToolBarHelper::help("", false, JText::_('JOPENSIM_HELP'));
			break;
		}
	}

	public function renderButton($link,$image,$text) {
		$params = array('title'=>$text, 'border'=>'0');
		$button  = "<div class='icon-wrapper'>";
		$button .= "<div class='icon'>";
		$button .= sprintf("<a href='%s' class='os_mainscreen'>",$link);
		$button .= JHTML::_('image', 'administrator/components/com_opensim/assets/images/'.$image,$text,$params);
		$button .= sprintf("<span>%s</span></a>",$text);
		$button .= "</div></div>\n";
		return $button;
	}
	public function renderPlainButton($image,$text) {
		$params = array('title'=>$text, 'border'=>'0');
		$button  = "<div class='icon-wrapper'>";
		$button .= "<div class='icon'><a>";
		$button .= JHTML::_('image', 'administrator/components/com_opensim/assets/'.$image,$text,$params);
		$button .= sprintf("<span>%s</span></a>",$text);
		$button .= "</div></div>\n";
		return $button;
	}
}
?>