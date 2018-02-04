<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2015 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');


class opensimViewprofile extends JViewLegacy {

	public function display($tpl = null) {
		$model = $this->getModel('profile');
		$this->assetpath = JUri::base(true)."/components/com_opensim/assets/";
		$doc = JFactory::getDocument();
		$doc->addStyleSheet($this->assetpath.'opensim.css');
		$cacheurl		= JPATH_SITE.DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."jopensim".DIRECTORY_SEPARATOR."profiles".DIRECTORY_SEPARATOR;

		$this->Itemid	= JFactory::getApplication()->input->get('Itemid');
		$menu			= JFactory::getApplication()->getMenu();
		$active			= $menu->getActive($this->Itemid);
		if (is_object($active)) {
			$params			= JComponentHelper::getParams('com_opensim');
			$this->pageclass_sfx	= $params->get('pageclass_sfx');
		} else {
			$this->pageclass_sfx	= "";
		}

		$this->settingsdata = $model->getSettingsData();

		$this->uid	= JFactory::getApplication()->input->get( 'uid', '', 'method', 'string');

		$this->wantmask		= $model->profile_wantmask();
		$this->skillsmask	= $model->profile_skillsmask();
		$this->profiledata	= $model->getUserProfile($this->uid);
		if(array_key_exists("error",$this->profiledata)) {
			JFactory::getApplication()->enqueueMessage($this->profiledata['error'],"error");
			$tpl = "nodisplay";
		} else {
			$this->image2nd	= "";
			$this->image1st	= "";
			$this->textureFormat	= $this->settingsdata['getTextureFormat'];
			if($this->settingsdata['profile_images'] == 1 && $this->settingsdata['getTextureEnabled'] == 1) {
				$this->zerouuid = $model->opensim->zerouid;
				if(substr($this->settingsdata['opensim_host'],0,4) != "http") $this->opensimhost = "http://".$this->settingsdata['opensim_host'];
				else $this->opensimhost		= $this->settingsdata['opensim_host'];
				$this->robust_port			= $this->settingsdata['robust_port'];
				if($this->profiledata['image'] && $this->profiledata['image'] != $this->zerouuid) {
					$fileinfo				= @getimagesize($this->opensimhost.":".$this->settingsdata['robust_port']."/CAPS/GetTexture/?texture_id=".$this->profiledata['image']."&format=".$this->textureFormat);
					if($fileinfo !== FALSE) {
						$profileimage2nd= @file_get_contents($this->opensimhost.":".$this->robust_port."/CAPS/GetTexture/?texture_id=".$this->profiledata['image']."&format=".$this->textureFormat);
						if($profileimage2nd) {
							$profile2nd		= $this->uid."_2nd.".$this->textureFormat;
							file_put_contents($cacheurl.$profile2nd,$profileimage2nd);
						}
						$this->image2nd = "<img src='".$this->opensimhost.":".$this->robust_port."/CAPS/GetTexture/?texture_id=".$this->profiledata['image']."&format=".$this->textureFormat."' style='max-width:".$this->settingsdata['profile_images_maxwidth']."px; max-height:".$this->settingsdata['profile_images_maxheight']."px;' />\n";
					}
				}
				if($this->profiledata['firstLifeImage'] && $this->profiledata['firstLifeImage'] != $this->zerouuid) {
					$profileimage1st= @file_get_contents($this->opensimhost.":".$this->robust_port."/CAPS/GetTexture/?texture_id=".$this->profiledata['firstLifeImage']."&format=".$this->textureFormat);
					if($profileimage1st) {
						$profile1st		= $this->uid."_1st.".$this->textureFormat;
						file_put_contents($cacheurl.$profile1st,$profileimage1st);
					}
					$fileinfo				= @getimagesize($this->opensimhost.":".$this->settingsdata['robust_port']."/CAPS/GetTexture/?texture_id=".$this->profiledata['firstLifeImage']."&format=".$this->textureFormat);
					if($fileinfo !== FALSE) $this->image1st = "<img src='".$this->opensimhost.":".$this->robust_port."/CAPS/GetTexture/?texture_id=".$this->profiledata['firstLifeImage']."&format=".$this->textureFormat."' style='max-width:".$this->settingsdata['profile_images_maxwidth']."px; max-height:".$this->settingsdata['profile_images_maxheight']."px;' />\n";
				}
			}
		}

//		$task	= JFactory::getApplication()->input->get( 'task', '', 'method', 'string');
//		switch($task) {
//			default:
//				
//			break;
//		}

		parent::display($tpl);
	}
}
?>