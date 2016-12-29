<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2015 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class OpenSimControllersearch extends OpenSimController {
	public function __construct() {
		parent::__construct();
		$model = $this->getModel('settings');
	}

	public function cancel() {
		$this->setRedirect('index.php?option=com_opensim&view=opensim');
	}

	public function applysearch() {
		$data = JFactory::getApplication()->input->request->getArray();;
		$model = $this->getModel('search');
		$model->saveOptions($data);
		$this->setRedirect('index.php?option=com_opensim&view=search&task=applyok',JText::_('SETTINGSSAVEDOK'));
	}

	public function savesearch() {
		$data = JFactory::getApplication()->input->request->getArray();;
		$model = $this->getModel('search');
		$model->saveOptions($data);
		$this->setRedirect('index.php?option=com_opensim&view=opensim&task=saveok',JText::_('SETTINGSSAVEDOK'));
	}

	public function removehost() {
		$data = JFactory::getApplication()->input->request->getArray();

		$model = $this->getModel('search');
		$model->removehost($data);
		$this->setRedirect('index.php?option=com_opensim&view=search&task=viewregisteredhosts&tmpl=component',JText::_('JOPENSIM_SEARCH_HOSTREMOVED'));
	}

	public function rebuildallhosts() {
		$model = $this->getModel('search');
		$model->rebuildAll();
		$this->setRedirect('index.php?option=com_opensim&view=search',JText::_('JOPENSIM_SEARCH_ALLHOSTREBUILD_OK'));
	}

	public function rebuildhost() {
		$data = JFactory::getApplication()->input->request->getArray();

		if(array_key_exists("host",$data) && array_key_exists("port",$data)) {
			$model = $this->getModel('search');
			$model->rebuildHost($data['host'],$data['port']);
			$this->setRedirect('index.php?option=com_opensim&view=search&task=viewregisteredhosts&tmpl=component',JText::_('JOPENSIM_SEARCH_HOSTREBUILD_OK'));
		} else {
			$this->setRedirect('index.php?option=com_opensim&view=search&task=viewregisteredhosts&tmpl=component',JText::_('JOPENSIM_SEARCH_HOSTREBUILD_ERROR'),'error');
		}
	}

	public function purgedata() {
		$data = JFactory::getApplication()->input->request->getArray();

		if(array_key_exists("searchdata",$data)) {
			$model = $this->getModel('search');
			$model->purgedata($data['searchdata']);
			$this->setRedirect('index.php?option=com_opensim&view=search',JText::_('JOPENSIM_SEARCH_DATAPURGED'));
		} else {
			$this->setRedirect('index.php?option=com_opensim&view=search',JText::_('JOPENSIM_SEARCH_DATAPURGE_ERROR'),"error");
		}
	}
}
?>
