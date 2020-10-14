<?php
/*
 * @component jOpenSimPayPal
 * @copyright Copyright (C) 2018 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.view');
 
class jOpenSimPayPalViewjOpenSimPayPal extends JViewLegacy {
	public function display($tpl = null) {
		$this->debug = null;
		JHTML::_('behavior.modal');
		$this->sidebar	= null;
		$this->sidebar	= JHtmlSidebar::render();
		$doc = JFactory::getDocument();
		$this->assetpath = JUri::base(true)."/components/com_jopensimpaypal/assets/";
		$doc->addStyleSheet($this->assetpath.'jopensimpaypal.css');

		$assetpath = JUri::base(true)."/components/com_opensim/assets/";
		$doc->addStyleSheet($assetpath.'opensim.css');
		$doc->addStyleSheet($assetpath.'quickiconstyle.css?v=2.6.8');

		$model = $this->getModel('jopensimpaypal');
		$model->checkParams();
		$this->params = $model->getParams();
		$this->user = JFactory::getUser();
		$this->newtransactions	= $model->newTransactions($this->user->lastvisitDate);
		$this->newpayouts		= $model->newPayouts($this->user->lastvisitDate);
		$this->unsolvedpayouts	= $model->unsolvedPayouts();

		$this->adminbuttons['transactions']	= $this->renderButton('index.php?option=com_jopensimpaypal&view=transactions','transactions.png',JText::_('COM_JOPENSIMPAYPAL_MENU_TRANSACTIONS'));
		$this->adminbuttons['payout']		= $this->renderButton('index.php?option=com_jopensimpaypal&view=payout','payout.png',JText::_('COM_JOPENSIMPAYPAL_MENU_PAYOUT'));

		$this->jopensimpaypalVersion = $this->getjOpenSimPayPalVersion();

		$this->setToolbar($tpl);
		parent::display($tpl);
	}

	public function renderButton($link,$image,$text) {
		$params = array('title'=>$text, 'border'=>'0');
		$button  = "<div class='icon-wrapper'>";
		$button .= "<div class='icon'>";
		$button .= sprintf("<a href='%s' class='os_mainscreen'>",$link);
		$button .= JHTML::_('image', 'administrator/components/com_jopensimpaypal/assets/images/'.$image,$text,$params);
		$button .= sprintf("<span>%s</span></a>",$text);
		$button .= "</div></div>\n";
		return $button;
	}

	public function getjOpenSimPayPalVersion()
	{
		jimport( 'joomla.filesystem.folder' );
		jimport( 'joomla.filesystem.file' );
		
		$xmlitems = array();
		
		$file = JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_jopensimpaypal'.DIRECTORY_SEPARATOR.'jopensimpaypal.xml';
		if (JFile::exists($file)) {
			if ($data = JInstaller::parseXMLInstallFile($file)) {
//				$this->debug = var_export($data,TRUE);
				foreach($data as $key => $value) {
					$xmlitems[$key] = $value;
				}
				if (isset($xmlitems['version']) && $xmlitems['version'] != '' ) {
					return $xmlitems['version'];
				} else {
					return 'Not defined!';
				}
			}
		} else {
			return 'Can not get jDownloads version number!';
		}
	}

	public function setToolbar($tpl) {
		JToolBarHelper::title(JText::_('COM_JOPENSIMPAYPAL_CONTROL_PANEL'),'jopensimpaypal');
		if (JFactory::getUser()->authorise('core.admin', 'com_jopensimpaypal')) {
			JToolBarHelper::preferences('com_jopensimpaypal','700','950',JText::_('COM_JOPENSIMPAYPAL_OPTIONS'));
		}
		JToolBarHelper::help("", false, JText::_('JOPENSIM_HELP'));
	}
}
?>