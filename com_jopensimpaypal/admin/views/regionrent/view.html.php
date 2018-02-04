<?php
/*
 * @component jOpenSimPayPal
 * @copyright Copyright (C) 2017 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.view');
 
class jOpenSimPayPalViewregionrent extends JViewLegacy {
	public function display($tpl = null) {
		JHTML::stylesheet( 'jopensimpaypal.css', 'administrator/components/com_jopensimpaypal/assets/' );
		$this->setToolbar($tpl);
		parent::display($tpl);
	}

	public function setToolbar($tpl) {
		switch($tpl) {
			case "editcss":
				JToolBarHelper::apply('applycss');
				JToolBarHelper::save('savecss');
				JToolBarHelper::cancel();
			break;
			case "csserror":
				JToolBarHelper::cancel();
			break;
			default:
				JToolBarHelper::title(JText::_('COM_JOPENSIMPAYPAL_CONTROL_PANEL'),'jopensimpaypal');
				if (JFactory::getUser()->authorise('core.admin', 'com_jopensimpaypal')) {
					JToolBarHelper::preferences('com_jopensimpaypal','700','950',JText::_('COM_JOPENSIMPAYPAL_OPTIONS'));
//					JToolBarHelper::custom('editcss', 'css.png', 'css_f2.png', 'JOPENSIM_EDITCSS',FALSE);
				}
				JToolBarHelper::help("", false, JText::_('JOPENSIM_HELP'));
			break;
		}
	}
}
?>