<?php
/*
 * @component jOpenSimPayPal
 * @copyright Copyright (C) 2017 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.view');
 
class jOpenSimPayPalViewpayout extends JViewLegacy {
	public function display($tpl = null) {
		$this->sidebar	= JHtmlSidebar::render();

		$this->assetpath = JUri::base(true)."/components/com_jopensimpaypal/assets/";
		$doc = JFactory::getDocument();
		$doc->addStyleSheet($this->assetpath.'jopensimpaypal.css');

		$model = $this->getModel('payout');

		// Get data from the model
		$items	= $this->get('Items');
		$items	= $model->addTransactions($items);
		$items	= $model->payoutSums($items);
		$items	= $model->getOpenSimNames($items);

		$this->state		= $this->get('State');

		$task	= JRequest::getVar( 'task', '', 'method', 'string');

		switch($task) {
			case "changestatus":
				$payoutid	= JRequest::getInt('payoutID');
				$this->item	= $model->getItemFromID($items,$payoutid);
				$tpl = "changestatus";
			break;
			default:
				$pagination = $this->get('Pagination');
		
				// Check for errors.
				if (count($errors = $this->get('Errors'))) {
					JError::raiseError(500, implode('<br />', $errors));
					return false;
				}
				// Assign data to the view
				$this->items		= $items;
				$this->pagination	= $pagination;
				$this->searchterms	= $this->state->get('filter.search');

				$this->setToolbar($tpl);
			break;
		}

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