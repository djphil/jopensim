<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2018 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


require_once JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_content'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'route.php';
jimport('joomla.application.categories');

jimport( 'joomla.application.component.view');
JLoader::register('jOpenSimHelper', JPATH_COMPONENT.'/helpers/jopensimhelper.php');

class opensimViewmaps extends JViewLegacy {

	public function display($tpl = null) {
		$document			= JFactory::getDocument();
		$document->addStyleSheet(JURI::base(true).'/components/com_opensim/assets/opensim.css');
		$document->addStyleSheet(JURI::base(true).'/components/com_opensim/assets/font-awesome.css');
		$queueMessage 		= array();
		$this->ismapcache	= null;
		$this->sidebar		= JHtmlSidebar::render();
		$this->canDo		= jOpenSimHelper::getActions();

		$curl = extension_loaded('curl');
		if(!$curl) JFactory::getApplication()->enqueueMessage(JText::_('JOPENSIM_MAP_CURLREQUIRED'),"warning");

		$regionmodel			= $this->getModel('regions');
		$this->pagination		= $regionmodel->getPagination();
		$this->limit			= $regionmodel->getRegionState('limit');
		$this->limitstart		= $regionmodel->getRegionState('regions_limitstart');
		$model				    = $this->getModel('map');
		$model->regionstart		= $this->limitstart;
		$model->regionlimit		= $this->limit;
		$this->regionrating		= array('13' => '<span class="label label-default" title="'.JText::_('JOPENSIM_GENERAL').'">G</span>',
										'21' => '<span class="label label-info" title="'.JText::_('JOPENSIM_MATURE').'">M</span>',
										'42' => '<span class="label label-danger" style="background:#D32C46;" title="'.JText::_('JOPENSIM_ADULT').'">A</span>');

		if(!$model->_osgrid_db) {
			JFactory::getApplication()->enqueueMessage(JText::sprintf('ERROR_NOSIMDB',JText::_('OPENSIMGRIDDB')),"error");
			$errormsg		= "<br />\n".JText::_('ERROR_NOREGION')."<br />\n".JText::_('ERRORQUESTION1')."<br />\n".JText::_('ERRORQUESTION2')."<br />\n";
			$this->errormsg	= $errormsg;
			$tpl			= "nodb";
		} else {
			$this->regionmodel		= $regionmodel;
			$this->filterForm		= $this->get('FilterForm','regions');
			$this->activeFilters	= $this->get('ActiveFilters','regions');
	
			$this->state			= $this->get('State','regions');
			$this->items			= $this->get('Items');
	
			$this->sortDirection	= $regionmodel->getRegionState('regions_filter_order_Dir');
			$this->sortColumn		= $regionmodel->getRegionState('regions_filter_order');
//			error_log("limit: ".$this->limit);
//			error_log("limitstart: ".$this->limitstart);
	
			$this->cacheimages		= $model->getCacheImages();
	
			$mapfolder = $model->checkCacheFolder();
			$this->mapfolder	= $mapfolder;
	
			if($mapfolder['existing'] == FALSE) {
				$foldercreated = $model->createImageFolder();
				if($foldercreated == FALSE) {
					$queueMessage['warning'][] = JText::_('JOPENSIM_MAPCACHE_UNWRITEABLE');
					$ismapcache = FALSE;
				} else {
					$ismapcache = TRUE;
				}
			} else {
				if($mapfolder['writeable'] == FALSE) {
					$queueMessage['warning'][] = JText::_('JOPENSIM_MAPCACHE_UNWRITEABLE');
					$ismapcache = FALSE;
				} else {
					$ismapcache = TRUE;
				}
			}
			$this->ismapcache	= $ismapcache;
	
			$settingsdata = $model->getSettingsData();
			$this->defaultregion	= $settingsdata['jopensim_userhome_region'];
			$this->settings			= $settingsdata;
			if(!$model->_osgrid_db) {
				$queueMessage['error'][] = JText::sprintf('ERROR_NOSIMDB',JText::_('OPENSIMGRIDDB'));
			}
	
			$assetinfo			= pathinfo(JPATH_COMPONENT_ADMINISTRATOR);
			$assetpath			= "components/".$assetinfo['basename']."/assets/";
			$asseturl			= "components/".$assetinfo['basename']."/assets/regionimage.php?uuid=";
			$this->assetpath	= $assetpath;
			$cachepath			= JPATH_SITE.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'jopensim'.DIRECTORY_SEPARATOR.'regions'.DIRECTORY_SEPARATOR;
			$cacheurl			= JURI::root()."images/jopensim/regions/";
	
			$this->cachepath	= $cachepath;
			$this->mapserver	= $settingsdata['oshost'];
			$this->mapport		= $settingsdata['osport'];
			$this->settingsdata	= $settingsdata;
	
			$task				= JFactory::getApplication()->input->get('task','','method','string');
			$this->task			= $task;
	
			if(count($queueMessage) > 0) {
				foreach($queueMessage AS $type => $messages) {
					if(is_array($messages) && count($messages) > 0) {
						foreach($messages AS $message) {
							JFactory::getApplication()->enqueueMessage($message,$type);
						}
					}
				}
			}
	
			$regionimage	= "<img class='img-thumbnail' src='%1\$s%2\$s' width='%4\$d' height='%4\$d' alt='%3\$s' title='%3\$s' />";
			if($this->canDo->get('core.edit')) {
				$regionlink		= "<a href='index.php?option=com_opensim&view=maps&task=selectdefault&region=%1\$s'>%2\$s</a>";
			} else {
				$regionlink		= "%2\$s";
			}
	
			switch($task) {
				case "selectdefault":
					$ueberschrift = JText::_('MAPSDEFAULT');
					$data = JFactory::getApplication()->input->request->getArray();
					$region = $data['region'];
					$this->region		= $region;
					$regiondata			= $model->getRegionDetails($region);
					$this->regiondata	= $regiondata;
					if(array_key_exists("jopensim_userhome_region",$settingsdata) && $settingsdata['jopensim_userhome_region'] == $data['region']) {
						$this->locX = sprintf("%d",$settingsdata['jopensim_userhome_x']);
						$this->locY = sprintf("%d",$settingsdata['jopensim_userhome_y']);
						$this->locZ = sprintf("%d",$settingsdata['jopensim_userhome_z']);
						if(array_key_exists("sizeX",$regiondata) && $regiondata['sizeX'] > 256) $locX = $this->locX / $regiondata['sizeX'] * 256;
						else $locX	= $this->locX;
						if(array_key_exists("sizeY",$regiondata) && $regiondata['sizeY'] > 256) $locY = $this->locY / $regiondata['sizeY'] * 256;
						else $locY	= $this->locY;
						$this->imgAddLink = "&defaultX=".$locX."&defaultY=".$locY;
					} else {
						$this->imgAddLink = "";
					}
					$tpl				= "selectregion";
				break;
				case "editinfo":
					$articles = $model->getArticles();
					$ueberschrift = JText::_('MAPEDIT');
					$data = JFactory::getApplication()->input->request->getArray();
					if(is_array($data['selectedRegion'])) $selectedRegion = $data['selectedRegion'][0];
					else $selectedRegion = $data['selectedRegion'];
					$mapinfo = $model->getMapInfo($selectedRegion);
					$mapdetails = $model->getRegionDetails($selectedRegion);
					$contentTitle = $model->getContentTitleFromId($mapinfo['articleId']);
					$this->data			= $data;
					$this->contentTitle	= $contentTitle;
					$this->mapinfo		= $mapinfo;
					$this->articles		= $articles;
					if($ismapcache == TRUE && is_file($cachepath.$mapdetails['uuid'].".jpg")) {
						$mapdetails['image'] = sprintf($regionimage,$cacheurl.$mapdetails['uuid'].".jpg","",$mapdetails['regionName'],256);
					} else {
						$params = sprintf("%1\$s&mapserver=%2\$s&mapport=%3\$s&scale=128",$mapdetails['maplink'],$mapdetails['serverIP'],$mapdetails['serverHttpPort']);
						$mapdetails['image'] = sprintf($regionimage,$asseturl,$params,$mapdetails['regionName'],256);
					}
					$this->mapdetails	= $mapdetails;
	
					//Get button
					$linkg = 'index.php?option=com_content&view=articles&layout=modal&tmpl=component&function=jOpenSimSelectArticle';
					JHTML::_('behavior.modal', 'a.modal-button');
					$selectArticle = new JObject();
					$selectArticle->set('modal', true);
					$selectArticle->set('link', $linkg);
					$selectArticle->set('text', JText::_('SELECT_ARTICLE'));
					$selectArticle->set('name', 'image');
					$selectArticle->set('modalname', 'modal-button');
					$selectArticle->set('options', "{handler: 'iframe', size: {x: 640, y: 360}}");
					// - - - - - - - - - - - - - - - - 
					$this->selectArticle	= $selectArticle;
					$tpl					= "mapedit";
				break;
				case "mapconfig":
					$tpl					= "mapconfig";
				break;
				default:
					$ueberschrift		= JText::_('JOPENSIM_MAPSMANAGEMENT');
					$filter				= JFactory::getApplication()->input->get('search','','method','string');
					$this->debugquery	= $regionmodel->debugquery;
					$regions			= $regionmodel->getRegions();
					$mapquery			= $model->mapquery;
					$this->mapquery		= $mapquery;
					$this->unusedImages = $regionmodel->unused;
	
					if(is_array($regions) && count($regions) > 0) {
						foreach($regions AS $key => $region) {
							$mapinfo = $model->getMapInfo($region['uuid']);
							$regions[$key]['articleId']		= $mapinfo['articleId'];
							$regions[$key]['articleTitle']	= $mapinfo['articleTitle'];
							$regions[$key]['hidemap']		= $mapinfo['hidemap'];
							$regions[$key]['public']		= $mapinfo['public'];
							$regions[$key]['guide']			= $mapinfo['guide'];
							$regions[$key]['toggletask']	= ($regions[$key]['hidemap'] == 1) ? "setRegionVisible":"setRegionInvisible";
							$regions[$key]['visible']		= ($regions[$key]['hidemap'] == 1) ? 0:1;
	
							$ismapcache = TRUE;
							if($ismapcache == TRUE && is_file($cachepath.$region['uuid'].".jpg")) {
								$mapimage = sprintf($regionimage,$cacheurl.$region['uuid'].".jpg","",$region['regionName'],48);
							} else {
								$params = sprintf("%1\$s&mapserver=%2\$s&mapport=%3\$s&scale=128",str_replace("-","",$region['uuid']),$region['serverIP'],$region['serverHttpPort']);
								$mapimage = sprintf($regionimage,$asseturl,$params,$region['regionName'],48);
							}
							$ownerdata = $model->getUserData($region['owner_uuid']);
							$regions[$key]['owner'] = $ownerdata['name'];
							$regions[$key]['image'] = sprintf($regionlink,$region['uuid'],$mapimage);
							$regions[$key]['posX']	= $regions[$key]['locX'] / 256;
							$regions[$key]['posY']	= $regions[$key]['locY'] / 256;
						}
					} else {
						$regions = array();
						JFactory::getApplication()->enqueueMessage(JText::_('JOPENSIM_NOREGIONS'),'warning');
					}
					$this->regions = $regions;
	
					$this->filter = JFactory::getApplication()->input->get('search');
				break;
			}
			$this->ueberschrift		= $ueberschrift;
		}
		$this->_setToolbar($tpl);

		parent::display($tpl);
	}

	public function _setToolbar($tpl) {
		JToolBarHelper::title(JText::_('JOPENSIM_NAME')." ".JText::_('JOPENSIM_MAPS'),'32-gridmap');

		$task	= JFactory::getApplication()->input->get('task','','method','string');
		switch($task) {
			case "selectdefault":
				JToolBarHelper::cancel('cancel','JCANCEL');
				JToolBarHelper::help("", false, JText::_('JOPENSIM_HELP_MAPS_DEFAULT'));
			break;
			case "mapview":
				JToolBarHelper::cancel('maps','JCANCEL');
			break;
			case "editinfo":
				JToolBarHelper::save('save_regionsettings');
				JToolBarHelper::apply('apply_regionsettings');	
				JToolBarHelper::cancel('cancel','JCANCEL');
				JToolBarHelper::help("", false, JText::_('JOPENSIM_HELP_MAPS_EDIT'));
			break;
			default:
				if($this->canDo->get('core.edit')) {
					JToolBarHelper::makeDefault('setDefaultRegion');
					JToolBarHelper::editList('editinfo');
					if($this->ismapcache) {
						JToolBarHelper::custom("maprefresh","maprefresh","maprefresh2",JText::_('JOPENSIM_REFRESHMAP'),true,false);
					}
				}
				if (JFactory::getUser()->authorise('core.admin', 'com_opensim')) {
					JToolBarHelper::preferences('com_opensim','700','950',JText::_('JOPENSIM_GLOBAL_SETTINGS'));
				}
				JToolBarHelper::help("", false, JText::_('JOPENSIM_HELP_MAPS'));
			break;
		}
	}

	protected function getSortFields() {
		return array(
				'regions.regionName' => JText::_('JOPENSIM_REGION_NAME'),
				'regions.uuid' => JText::_('JOPENSIM_REGION_UUID'),
				'regions.locX' => JText::_('JOPENSIM_REGION_POSITION_X'),
				'regions.locY' => JText::_('JOPENSIM_REGION_POSITION_Y')
		);
	}
}
?>