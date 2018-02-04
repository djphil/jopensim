<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2017 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die();

require_once(JPATH_COMPONENT_SITE.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'opensim.php');

class opensimModelShowcase extends OpenSimModelOpenSim {

	var $_data;
	var $_data_ext;
	var $_regiondata = null;
	var $_settingsData;
	var $filename = "showcase.php";
	var $view = "showcase";
	var $_os_db;
	var $_osgrid_db;
	var $_db;

	public function __construct() {
		parent::__construct();
		$this->getSettingsData();
		$this->_os_db = $this->getOpenSimDB();
		$this->_osgrid_db = $this->getOpenSimGridDB();
	}

	public function getClassifieds($classifiedid = null) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select($db->quoteName('#__opensim_userclassifieds').".*");
		$query->from($db->quoteName('#__opensim_userclassifieds'));
		if($this->_settingsData['classified_hide'] > 0) {
			$query->where("FROM_UNIXTIME(".$db->quoteName('#__opensim_userclassifieds.creationdate').") + ".$this->_settingsData['classified_hide']." > NOW()");
		}
		if($classifiedid) {
			$query->where($db->quoteName('#__opensim_userclassifieds.classifieduuid')." = ".$db->quote($classifiedid));
		}
//		error_log("sort by: ".$this->_settingsData['classified_sort']);
//		error_log("sorting: ".$this->_settingsData['classified_order']);
		$query->order($db->quoteName('#__opensim_userclassifieds.'.$this->_settingsData['classified_sort'])." ".$this->_settingsData['classified_order']);
		$db->setQuery($query);
		$db->execute();
		$this->classifieds = $db->loadAssocList();
		if(is_array($this->classifieds) && count($this->classifieds) > 0) {
			$this->host				= $this->_settingsData['opensim_host'];
			$this->port				= $this->_settingsData['robust_port'];
			$this->textureFormat	= $this->_settingsData['getTextureFormat'];
			$assetimage		= "<img class='img-thumbnail' src='%1\$s' width='%3\$d' height='%3\$d' alt='%2\$s' title='%2\$s' />";
			$suchmuster		= '/([0-9\.]*), ([0-9\.]*), ([0-9\.]*)/';
			$texturehost 	= (substr($this->host,0,4) != "http") ? "http://".$this->host:$this->host;
			$cacheurl		= JPATH_SITE.DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."jopensim".DIRECTORY_SEPARATOR."classifieds".DIRECTORY_SEPARATOR;
			$assetinfo		= pathinfo(JPATH_COMPONENT_SITE);
			$assetpath		= "components/".$assetinfo['basename']."/assets/";
			$asseturl		= "components/".$assetinfo['basename']."/assets/regionimage.php?uuid=";

			foreach($this->classifieds AS $key => $classified) {
				preg_match($suchmuster,$classified['posglobal'],$treffer);
				$this->classifieds[$key]['globalpos']['posX']	= $treffer[1];
				$this->classifieds[$key]['globalpos']['posY']	= $treffer[2];
				$this->classifieds[$key]['globalpos']['posZ']	= $treffer[3];
				$this->classifieds[$key]['localpos']			= $this->opensim->globalPosition2regionPosition($this->classifieds[$key]['globalpos']);
				if($classified['snapshotuuid'] != $this->opensim->zerouid) {
					if(!is_file($cacheurl.$classified['snapshotuuid'].".".$this->textureFormat)) {
						$getTexture = $texturehost.":".$this->port."/CAPS/GetTexture/?texture_id=".$classified['snapshotuuid']."&format=".$this->textureFormat;
						$fileinfo	= @getimagesize($getTexture);
						if($fileinfo !== FALSE) {
							$classifiedimage= @file_get_contents($getTexture);
							if($classifiedimage) {
								$classifiedimagename	= $classified['snapshotuuid'].".".$this->textureFormat;
								file_put_contents($cacheurl.$classifiedimagename,$classifiedimage);
								$classifiedimageurl		= JURI::root()."images/jopensim/classifieds/".$classifiedimagename;
							}
						} else { // add an error image
							$classifiedimageurl = JURI::root().$assetpath."images/noregion.jpg";
						}
					} else {
						$classifiedimageurl = JURI::root()."images/jopensim/classifieds/".$classified['snapshotuuid'].".".$this->textureFormat;
					}
				} else { // add a dummy image
					$classifiedimageurl = JURI::root().$assetpath."images/default_land_picture.jpg";
				}
				$this->classifieds[$key]['imageurl'] = $classifiedimageurl;
				if(!is_array($this->classifieds[$key]['localpos']) ||
				   !array_key_exists('regionname',$this->classifieds[$key]['localpos']) ||
				   !array_key_exists('posX',$this->classifieds[$key]['localpos']) ||
				   !array_key_exists('posY',$this->classifieds[$key]['localpos']) ||
				   !array_key_exists('posZ',$this->classifieds[$key]['localpos'])) {
						$this->classifieds[$key]['linklocal']	= null;
						$this->classifieds[$key]['linkhg']		= null;
						$this->classifieds[$key]['linkhgv3']	= null;
						$this->classifieds[$key]['linkhop']		= null;
				} else {
					$this->classifieds[$key]['linklocal']	= $this->tplink($this->classifieds[$key]['localpos'],"local",$this->host,$this->port);
					$this->classifieds[$key]['linkhg']		= $this->tplink($this->classifieds[$key]['localpos'],"hg",$this->host,$this->port);
					$this->classifieds[$key]['linkhgv3']	= $this->tplink($this->classifieds[$key]['localpos'],"hgv3",$this->host,$this->port);
					$this->classifieds[$key]['linkhop']		= $this->tplink($this->classifieds[$key]['localpos'],"hop",$this->host,$this->port);
				}
			}
			return $this->classifieds;




			
		}
	}

	public function tplink($data,$type,$host,$port) {
		switch($type) {
			case "local":
				$link = "secondlife:/"."/".$data['regionname']."/".$data['posX']."/".$data['posY']."/".$data['posZ'];
			break;
			case "hg":
				$link = "secondlife:/"."/".$host.":".$port.":".$data['regionname']."/".$data['posX']."/".$data['posY']."/".$data['posZ'];
			break;
			case "hgv3":
				$link = "secondlife:/"."/http|!!".$host."|".$port."+".str_replace(" ","+",$data['regionname'])."/".$data['posX']."/".$data['posY']."/".$data['posZ'];
			break;
			case "hop":
				$link = "hop:/"."/".$host.":".$port.":".$data['regionname']."/".$data['posX']."/".$data['posY']."/".$data['posZ'];
			break;
			default:
				$link = null;
			break;
		}
		return $link;
	}
}
?>
