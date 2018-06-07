<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2018 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');


class opensimViewShowcase extends JViewLegacy {

	public function display($tpl = null) {
		$model			= $this->getModel();
		$mapmodel		= $this->getModel('maps');
		$regionmodel	= $this->getModel('regions');

		$app					= JFactory::getApplication(); // Access the Application Object
		$menu					= $app->getMenu(); // Load the JMenuSite Object
		$active					= $menu->getActive(); // Load the Active Menu Item as an stdClass Object
		$this->Itemid			= JFactory::getApplication()->input->get('Itemid');
		$task					= JFactory::getApplication()->input->get('task');

		$this->pagetitle		= $active->params->get('jopensim_showcase_title');
		$this->titleformat		= $active->params->get('jopensim_showcase_titleformat','h1');
		$this->showregions		= $active->params->get('jopensim_showcase_regions');
		$this->regionintro		= $active->params->get('jopensim_showcase_regionintro');
		$this->showclassified	= $active->params->get('jopensim_showcase_classified');
		$this->matureclassified	= $active->params->get('jopensim_showcase_matureclassified',0);
		$this->hideunavailable	= $active->params->get('jopensim_showcase_classifiedhideunavailable',0);
		$this->classifiedintro	= $active->params->get('jopensim_showcase_classifiedintro');
		$this->imagesize		= $active->params->get('jopensim_showcase_imagesize',150);
		$this->mainlink			= $active->params->get('jopensim_showcase_mainlink');
		$this->link_local		= $active->params->get('jopensim_showcaselink_local');
		$this->link_hg			= $active->params->get('jopensim_showcaselink_hg');
		$this->link_hgv3		= $active->params->get('jopensim_showcaselink_hgv3');
		$this->link_hop			= $active->params->get('jopensim_showcaselink_hop');
		if(!$this->pagetitle) $this->pagetitle = JText::_('JOPENSIM_SHOWCASE');

		$params					= JComponentHelper::getParams('com_opensim');
		$this->host				= $params->get('opensim_host');
		$this->port				= $params->get('robust_port');
		$this->classifiedimages	= $params->get('classified_images');
		$this->classifiedX		= $params->get('classified_images_maxwidth',512);
		$this->classifiedY		= $params->get('classified_images_maxheight',512);
		$this->tmpl				= JFactory::getApplication()->input->get('tmpl');

		$this->settingsdata 	= $model->getSettingsData();
		$this->textureFormat	= $this->settingsdata['getTextureFormat'];

		$assetinfo				= pathinfo(JPATH_COMPONENT_SITE);
		$assetpath				= "components/".$assetinfo['basename']."/assets/";
		$asseturl				= "components/".$assetinfo['basename']."/assets/regionimage.php?uuid=";

		$this->assetpath = JUri::base(true)."/components/com_opensim/assets/";
		$doc = JFactory::getDocument();
		$doc->addStyleSheet($this->assetpath.'opensim.css');

		switch($task) {
			case "detailinfo":
				$this->classifieduuid		= JFactory::getApplication()->input->get('id');
				$classified					= $model->getClassifieds($this->classifieduuid,$this->matureclassified);
				$this->classified			= $classified[0];
				$this->classified['creator']= $model->opensim->getUserName($this->classified['creatoruuid'],"full");
//				$assetimage					= "<img class='img-thumbnail' src='%1\$s' width='%3\$d' height='%3\$d' alt='%2\$s' title='%2\$s' />";
				$assetimage					= "<img class='img-thumbnail' src='%1\$s' style='max-width:%3\$dpx;max-height:%4\$dpx' alt='%2\$s' title='%2\$s' />";
				$this->classified['image']	= sprintf($assetimage,$this->classified['imageurl'],$this->classified['name'],$this->classifiedX,$this->classifiedY);
				$tpl						= "classified_detail";
			break;
			default:
				// show regions in showcase
				if($this->showregions) {
					$cacheurl		= JPATH_SITE.DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."jopensim".DIRECTORY_SEPARATOR."regions".DIRECTORY_SEPARATOR;

					$this->tmpl		= JFactory::getApplication()->input->get('tmpl');

					$this->regions	= array();
					$allregions		= $regionmodel->getRegions();
					$regionimage	= "<img class='img-thumbnail' src='%1\$s%2\$s' width='%4\$d' height='%4\$d' alt='%3\$s' title='%3\$s' />";
					$cachepath			= JPATH_SITE.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'jopensim'.DIRECTORY_SEPARATOR.'regions'.DIRECTORY_SEPARATOR;
					$cacheurl			= JURI::root()."images/jopensim/regions/";

					foreach($allregions AS $region) {
						$regioninfo = $mapmodel->getMapInfo($region['uuid']);
						if(array_key_exists("guide",$regioninfo) && $regioninfo['guide'] == 1) {
							$mapcounter	= count($this->regions);
							$this->regions[$mapcounter] = $region;
							if(is_file($cachepath.$region['uuid'].".jpg")) {
								$mapimage = sprintf($regionimage,$cacheurl.$region['uuid'].".jpg","",$region['regionName'],$this->imagesize);
							} else {
								$params = sprintf("%1\$s&mapserver=%2\$s&mapport=%3\$s&scale=150",str_replace("-","",$region['uuid']),$region['serverIP'],$region['serverHttpPort']);
								$mapimage = sprintf($regionimage,$asseturl,$params,$region['regionName'],$this->imagesize);
							}
							$this->regions[$mapcounter]['regionlink']	= "";
							$this->regions[$mapcounter]['linklocal']	= "secondlife:/"."/".$region['regionName'];
							$this->regions[$mapcounter]['linkhg']	= "secondlife:/"."/".$this->host.":".$this->port.":".$region['regionName'];
							$this->regions[$mapcounter]['linkhgv3']	= "secondlife:/"."/http|!!".$this->host."|".$this->port."+".str_replace(" ","+",$region['regionName']);
							$this->regions[$mapcounter]['linkhop']	= "hop:/"."/".$this->host.":".$this->port.":".$region['regionName'];
							if($this->mainlink != "no") {
								switch ($this->mainlink) {
									case "web":
										if($regioninfo['articleId'] > 0) {
											$this->regions[$mapcounter]['regionlink'] = "./index.php?option=com_content&view=article&id=".$regioninfo['articleId']."&Itemid=".$this->Itemid;
										}
									break;
									case "local":
										$this->regions[$mapcounter]['regionlink'] = $this->regions[$mapcounter]['linklocal'];
									break;
									case "hg":
										$this->regions[$mapcounter]['regionlink'] = $this->regions[$mapcounter]['linkhg'];
									break;
									case "hgv3":
										$this->regions[$mapcounter]['regionlink'] = $this->regions[$mapcounter]['linkhgv3'];
									break;
									case "hop":
										$this->regions[$mapcounter]['regionlink'] = $this->regions[$mapcounter]['linkhop'];
									break;
								}
							}
							$this->regions[$mapcounter]['mapimage'] = $mapimage;
						}
					}
				}

				if($this->showclassified) {
					$this->classifieds	= $model->getClassifieds(null,$this->matureclassified);
					if(is_array($this->classifieds) && count($this->classifieds) > 0) {
						$assetimage	= "<img class='img-thumbnail' src='%1\$s' width='%3\$d' height='%3\$d' alt='%2\$s' title='%2\$s' />";
						foreach($this->classifieds AS $key => $classified) {
							if(!$classified['linklocal'] && $this->hideunavailable) { // this location is currently not available, dont show it at all
								unset($this->classifieds[$key]);
								continue;
							}
							$this->classifieds[$key]['image'] = sprintf($assetimage,$classified['imageurl'],$this->classifieds[$key]['name'],$this->imagesize);
							if($this->mainlink != "no") {
								switch ($this->mainlink) {
									case "web":
										$this->classifieds[$key]['mainlink'] = "./index.php?option=com_opensim&view=showcase&task=detailinfo&id=".$this->classifieds[$key]['classifieduuid']."&Itemid=".$this->Itemid;
									break;
									case "local":
										$this->classifieds[$key]['mainlink'] = $this->classifieds[$key]['linklocal'];
									break;
									case "hg":
										$this->classifieds[$key]['mainlink'] = $this->classifieds[$key]['linkhg'];
									break;
									case "hgv3":
										$this->classifieds[$key]['mainlink'] = $this->classifieds[$key]['linkhgv3'];
									break;
									case "hop":
										$this->classifieds[$key]['mainlink'] = $this->classifieds[$key]['linkhop'];
									break;
								}
							}
						}
					}
				}
			break;
		}

		parent::display($tpl);
	}
}
?>