<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2017 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class OpenSimControllerMaps extends OpenSimController {
	public function __construct() {
		parent::__construct();
		$this->model	= $this->getModel('map');
		$view			= $this->getView( 'maps', 'html' );
		$view->setModel($this->getModel('regions'),true);
		$view->setModel($this->model);
	}

	public function savedefault() {
		$this->model->savedefault();
		$this->setRedirect( 'index.php?option=com_opensim&view=maps&task=applyok',JText::_('REGION_SAVED'));
	}

	public function savemanual() {
		$this->model->savemanual();
		$this->setRedirect('index.php?option=com_opensim&view=maps&task=applyok',JText::_('REGION_SAVED'));
	}

	public function setDefaultRegion() {
		$data = JFactory::getApplication()->input->request->getArray();
		$selectedRegion = (is_array($data['selectedRegion'])) ? $data['selectedRegion'][0]: $data['selectedRegion'];
		$this->setRedirect('index.php?option=com_opensim&view=maps&task=selectdefault&region='.$selectedRegion);
	}

	public function apply_regionsettings() {
		$data = JFactory::getApplication()->input->request->getArray();
		$this->model->setMapInfo($data);
		$this->setRedirect('index.php?option=com_opensim&view=maps&task=editinfo&selectedRegion='.$data['regionUUID'],JText::_('REGIONINFO_SAVED'));
	}

	public function save_regionsettings() {
		$data = JFactory::getApplication()->input->request->getArray();
		$this->model->setMapInfo($data);
		$this->setRedirect('index.php?option=com_opensim&view=maps',JText::_('REGIONINFO_SAVED'));
	}

	public function removemaparticle() {
		$data = JFactory::getApplication()->input->request->getArray();

		$this->model->removeMapArticle($data['regionUUID']);
		$this->setRedirect('index.php?option=com_opensim&view=maps&task=editinfo&selectedRegion='.$data['regionUUID'],JText::_('REGIONARTICLE_REMOVED'));
	}

	public function setRegionVisible() {
		$data = JFactory::getApplication()->input->request->getArray();
		$region = $data['region'];
		$this->model->setVisible($region,0);
		$this->setRedirect('index.php?option=com_opensim&view=maps');
	}

	public function setRegionInvisible() {
		$data = JFactory::getApplication()->input->request->getArray();
		$region = $data['region'];
		$this->model->setVisible($region,1);
		$this->setRedirect('index.php?option=com_opensim&view=maps');
	}

	public function setRegionUnpublic() {
		$data = JFactory::getApplication()->input->request->getArray();
		$region = $data['region'];
		$this->model->setPublic($region,0);
		$this->setRedirect('index.php?option=com_opensim&view=maps');
	}

	public function setRegionPublic() {
		$data = JFactory::getApplication()->input->request->getArray();
		$region = $data['region'];
		$this->model->setPublic($region,1);
		$this->setRedirect('index.php?option=com_opensim&view=maps');
	}

	public function setRegionGuideShow() {
		$data = JFactory::getApplication()->input->request->getArray();
		$region = $data['region'];
		$this->model->setGuide($region,1);
		$this->setRedirect('index.php?option=com_opensim&view=maps');
	}

	public function setRegionGuideHide() {
		$data = JFactory::getApplication()->input->request->getArray();
		$region = $data['region'];
		$this->model->setGuide($region,0);
		$this->setRedirect('index.php?option=com_opensim&view=maps');
	}

	public function save_mapconfig() {
		$data = JFactory::getApplication()->input->request->getArray();
		$retval = $this->model->updateMapconfig($data);
		if($retval === TRUE) {
			$type = "message";
			$message = JText::_('JOPENSIM_SAVE_MAPCONFIG_OK');
		} else {
			$type = "error";
			$message = JText::_('JOPENSIM_SAVE_MAPCONFIG_ERROR');
		}
		$this->setRedirect('index.php?option=com_opensim&view=maps',$message,$type);
	}

	public function apply_mapconfig() {
		$data = JFactory::getApplication()->input->request->getArray();
		$retval = $this->model->updateMapconfig($data);
		if($retval === TRUE) {
			$type = "message";
			$message = JText::_('JOPENSIM_SAVE_MAPCONFIG_OK');
		} else {
			$type = "error";
			$message = JText::_('JOPENSIM_SAVE_MAPCONFIG_ERROR');
		}
		$this->setRedirect('index.php?option=com_opensim&view=maps&task=mapconfig',$message,$type);
	}

	public function maprefresh() {
		$selectedRegions = JFactory::getApplication()->input->get('selectedRegion');
		$message	= "";
		$type		= "";
		if(is_array($selectedRegions)) {
			foreach($selectedRegions AS $selectedRegion) {
				$this->model->refreshMap($selectedRegion);
			}
			if(count($selectedRegions) == 1) $message = JText::_('JOPENSIM_MAPREFRESH1_OK');
			else $message = JText::sprintf('JOPENSIM_MAPREFRESH_OK',count($selectedRegions));
		} else {
			$type		= "warning";
			$message	= JText::_('JOPENSIM_MAPREFRESH_ERROR');
		}
		$this->setRedirect( 'index.php?option=com_opensim&view=maps',$message,$type);
	}

	public function removeCacheImage() {
		$data = JFactory::getApplication()->input->request->getArray();

		$folder = $this->model->checkCacheFolder();
		if(is_file($folder['path'].DIRECTORY_SEPARATOR.$data['img'])) {
			unlink($folder['path'].DIRECTORY_SEPARATOR.$data['img']);
		}
		$this->setRedirect( 'index.php?option=com_opensim&view=maps');
	}

	public function cancel($key = NULL) {
		$this->setRedirect( 'index.php?option=com_opensim&view=maps');
	}

	public function display($cachable = false, $urlparams = array()) {
		$view	= $this->getView( 'maps', 'html' );
		$view->setModel($this->getModel('regions'),true);
		$view->setModel($this->model);
		jimport('joomla.application.component.helper');
		$addons = JComponentHelper::getParams('com_opensim')->get('addons');
		$this->jopensimmenue($addons,"maps");
		$view->display();
	}


}
?>
