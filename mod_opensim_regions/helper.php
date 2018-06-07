<?php
/**
 * @module      OpenSim Gridstatus (mod_opensim_regions)
 * @copyright   Copyright (C) 2018 FoTo50 http://www.jopensim.com
 * @license     GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
**/

// no direct access
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

class ModOpenSimRegionsHelper {
	public $opensim;
	public $_osgrid_db;
	public $showhiddenregions;

	public function __construct($params) {
		$this->showhiddenregions	= $params->get('showhiddenregions');
		$this->namemaxlength		= $params->get('maxnamelength',0);
		$this->getComponentParameter();
		$this->initOpenSim();                       // generate the opensim object
		$this->_osgrid_db = $this->getOsGridDB();   // load the external DB in an object
		$this->model = new opensimModelOpensim();
	}

	public function initOpenSim() {
		$this->opensim = new opensim($this->connectparams['osgriddbhost'],$this->connectparams['osgriddbuser'],$this->connectparams['osgriddbpasswd'],$this->connectparams['osgriddbname'],$this->connectparams['osgriddbport']);
	}

	public function getComponentParameter() {
		$params = JComponentHelper::getParams('com_opensim');
		$parameter['osgriddbhost']      = $params->get('opensimgrid_dbhost');
		$parameter['osgriddbuser']      = $params->get('opensimgrid_dbuser');
		$parameter['osgriddbpasswd']    = $params->get('opensimgrid_dbpasswd');
		$parameter['osgriddbname']      = $params->get('opensimgrid_dbname');
		$parameter['osgriddbport']      = $params->get('opensimgrid_dbport');
		$this->connectparams            = $parameter;
	}

	public function getOsGridDB() {
		return $this->opensim->_osgrid_db;
	}

	public function getRegions() {
		$regions			= $this->model->getData();
		$this->settingsdata	= $this->model->getSettingsData();

		if(intval($this->showhiddenregions) == 0) {
			$regionarray	= $this->model->removehidden($regions['regions']);
		} else {
			$regionarray	= $regions['regions'];
		}
		if($this->namemaxlength > 0) {
			foreach($regionarray AS $key => $region) {
				if(strlen($region['regionName']) > $this->namemaxlength) {
					$regionarray[$key]['displayName'] = substr($region['regionName'],0,$this->namemaxlength)."&#x2026;";
				} else {
					$regionarray[$key]['displayName'] = $region['regionName'];
				}
			}
		} else {
			foreach($regionarray AS $key => $region) $regionarray[$key]['displayName'] = $region['regionName'];
		}
		return $regionarray;
	}



} // end ModOpenSimRegionsHelper
?>
