<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2017 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// no direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.view');
 
class opensimViewsearch extends JViewLegacy {
	public function display($tpl = null) {
		JHTML::_('behavior.modal');
		$document		= JFactory::getDocument();
		$document->addStyleSheet(JURI::base(true).'/components/com_opensim/assets/opensim.css');
		$this->sidebar	= null;
		$model			= $this->getModel('search');
		$this->settings	= $model->getSettingsData();
		$searchoptions	= $model->getoptions();
		$searchsort		= $model->getoptions("customsort");
		$task			= JFactory::getApplication()->input->get( 'task', '', 'method', 'string');

		if(!$this->settings['addons_search']) {
			JFactory::getApplication()->enqueueMessage(JText::_('JOPENSIM_SEARCHADDONDISABLED'),'warning');
		}

		$assetinfo = pathinfo(JPATH_COMPONENT_ADMINISTRATOR);
		$assetpath = "components".DIRECTORY_SEPARATOR.$assetinfo['basename'].DIRECTORY_SEPARATOR."assets".DS;
		$this->assetpath		= $assetpath;
		$this->searchoptions	= $searchoptions;
		$this->searchsort		= $searchsort;

		switch($task) {
			case "viewregisteredhosts":
				$tpl = "registeredhosts";
				$this->registeredHosts = $model->getRegisteredHosts();
			break;
			case "viewsearchdata":
				$this->zerouuid				= $model->opensim->zerouid;
				$this->searchcategory		= array();
				$this->searchcategory[0]	= JText::_('JOPENSIM_SEARCH_CAT0');
				$this->searchcategory[1]	= JText::_('JOPENSIM_SEARCH_CAT1');
				$this->searchcategory[3]	= JText::_('JOPENSIM_SEARCH_CAT3');
				$this->searchcategory[4]	= JText::_('JOPENSIM_SEARCH_CAT4');
				$this->searchcategory[5]	= JText::_('JOPENSIM_SEARCH_CAT5');
				$this->searchcategory[6]	= JText::_('JOPENSIM_SEARCH_CAT6');
				$this->searchcategory[7]	= JText::_('JOPENSIM_SEARCH_CAT7');
				$this->searchcategory[8]	= JText::_('JOPENSIM_SEARCH_CAT8');
				$this->searchcategory[9]	= JText::_('JOPENSIM_SEARCH_CAT9');
				$this->searchcategory[10]	= JText::_('JOPENSIM_SEARCH_CAT10');
				$this->searchcategory[11]	= JText::_('JOPENSIM_SEARCH_CAT11');
				$this->searchcategory[13]	= JText::_('JOPENSIM_SEARCH_CAT13');
				$this->searchcategory[14]	= JText::_('JOPENSIM_SEARCH_CAT14');

				$searchdata = JFactory::getApplication()->input->get( 'searchdata', '', 'method', 'string');
				switch($searchdata) {
					case "objects":
						$this->data	= $model->getSearchdata('objects');
						$tpl		= "objects";
					break;
					case "parcels":
						$this->data	= $model->getSearchdata('parcels');
						$tpl		= "parcels";
					break;
					case "parcelsales":
						$this->data	= $model->getSearchdata('parcelsales');
						$tpl		= "parcelsales";
					break;
					case "classifieds":
						$this->data	= $model->getSearchdata('classifieds');
						$tpl		= "classifieds";
					break;
					case "events":
						$this->data	= $model->getSearchdata('events');
						$tpl		= "events";
					break;
					case "regions":
						$this->data	= $model->getSearchdata('regions');
						$tpl		= "regions";
					break;
					default:
						JFactory::getApplication()->enqueueMessage(JText::_('JOPENSIM_SEARCH_UNKNOWNERROR'),'warning');
						$tpl		= "unknown";
					break;
				}
			break;
			default:
				$this->sidebar	= JHtmlSidebar::render();
				$this->searchcount = $model->countSearchContent();
				$this->registeredHosts = $model->getRegisteredHosts();
			break;
		}

		$this->_setToolbar($tpl);
		parent::display($tpl);
	}

	public function _setToolbar($tpl) {
		JToolBarHelper::title(JText::_('JOPENSIM_NAME')." ".JText::_('JOPENSIM_SEARCH'),'32-search');

		switch($tpl) {
			default:
				JToolBarHelper::apply('applysearch');	
				JToolBarHelper::save('savesearch');
				JToolBarHelper::cancel('cancel','JCANCEL');
			break;
		}
		if (JFactory::getUser()->authorise('core.admin', 'com_opensim')) {
			JToolBarHelper::preferences('com_opensim','700','950',JText::_('JOPENSIM_GLOBAL_SETTINGS'));
		}
		JToolBarHelper::help("", false, JText::_('JOPENSIM_HELP_SEARCH'));
	}
}

?>