<?php
/*
 * @component jOpenSimPayPal
 * @copyright Copyright (C) 2017 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.view');
 
class jOpenSimPayPalViewtransactions extends JViewLegacy {
	public function display($tpl = null) {
		$this->sidebar	= JHtmlSidebar::render();
		$this->assetpath = JUri::base(true)."/components/com_jopensimpaypal/assets/";
		$doc = JFactory::getDocument();
		$doc->addStyleSheet($this->assetpath.'jopensimpaypal.css');


		$model = $this->getModel('transactions');
		// Get data from the model
		$items = $this->get('Items');
		$items = $model->getOpenSimNames($items);
		$pagination = $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JFactory::getApplication()->enqueueMessage(implode('<br />', $errors),'error');
			return false;
		}
		// Assign data to the view
		$this->items		= $items;
		$this->state		= $this->get('State');
		$this->pagination	= $pagination;
		$this->searchterms	= $this->state->get('filter.search');



		$this->setToolbar($tpl);
		parent::display($tpl);
	}

	public function setToolbar($tpl) {
//		$input = JFactory::getApplication()->input;
//		$input->set('hidemainmenu', true);
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