<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');


class opensimViewAuth extends JViewLegacy {

	public function display($tpl = null) {
		$model				= $this->getModel();
		$settings			= $model->getSettingsData();
		if($settings['addons_authorize'] == 0) { // Addon is disabled, no check, lets send TRUE
			$this->message = JText::_('JOPENSIM_AUTHORIZE_DISABLED');
			parent::display($tpl);
			exit;
		}
		$this->auth_link	= $settings['auth_link'];
		$menu				= $menu = JFactory::getApplication()->getMenu();
		$item				= $menu->getItem($this->auth_link);
		$authlink			= new JURI($item->link);
		$authlink->setVar('Itemid', $this->auth_link);
		$this->authlink		= JUri::base().$item->route;

		$test	= JFactory::getApplication()->input->get('test');
		if($test == "debug") {
			$userid		= JFactory::getApplication()->input->get('userid');
			$regionuuid	= JFactory::getApplication()->input->get('regionuuid');
			$regionname	= JFactory::getApplication()->input->get('regionname');
			$firstname	= JFactory::getApplication()->input->get('firstname');
			$lastname	= JFactory::getApplication()->input->get('lastname');
		} else {
			$input		= file_get_contents("php://input");
			$xml		= new SimpleXMLElement($input);
			$userid		= (string)$xml->ID;
			$regionuuid	= (string)$xml->RegionID;
			$regionname	= (string)$xml->RegionName;
			$firstname	= (string)$xml->FirstName;
			$lastname	= (string)$xml->SurName;
		}

		$useriscreated	= $model->opensim->isCreated($userid);

		if($useriscreated === FALSE) { // here we probably have some HG user
//		if(!$lastname || substr($lastname,0,1) == "@") { // here we probably have some HG user
			$this->avatarname	= $firstname;
			$this->ishguser		= TRUE;
		} else {
			$this->avatarname	= $firstname." ".$lastname;
			$this->ishguser		= FALSE;
		}
		$this->regionname	= $regionname;

		$regionrating		= $model->opensim->getRegionRating($regionuuid);
		if($regionrating == "adult" || $regionrating == "mature") {
			$userageverified = $model->ageVerify($userid,$this->ishguser);
			if($userageverified === FALSE) {
				if($this->ishguser === FALSE) { // Denied message for local users
					$this->message = JText::sprintf('JOPENSIM_AUTHORIZE_DENIED_MESSAGE',$this->avatarname,$this->regionname,$this->authlink);
				} else {
					if($settings['addons_authorizehg'] == 0) { // Denied message for HG users (authHG disabled)
						$this->message		= JText::_('JOPENSIM_AUTHORIZEHG_DISABLED_MESSAGE');
					} else { // Denied message for HG users (authHG enabled)
						$this->authhglink	= JUri::base().'index.php?option=com_opensim&view=auth&task=hguserconfirm&uuid='.$userid;
						$this->message		= JText::sprintf('JOPENSIM_AUTHORIZEHG_DENIED_MESSAGE',$this->avatarname,$this->regionname,$this->authhglink);
					}
				}
				$tpl = "noauth";
			} else {
				$this->message = JText::sprintf('JOPENSIM_AUTHORIZE_APROOVED_MESSAGE',$this->avatarname,$this->regionname);
			}
		} else {
			$this->message = JText::sprintf('JOPENSIM_AUTHORIZE_PGREGION',$regionname);
		}
		parent::display($tpl);
	}
}
?>