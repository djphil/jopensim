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
		$tpl		= "hguser";
		$model		= $this->getModel();
		$settings	= $model->getSettingsData();
		if($settings['addons_authorize'] == 0 || $settings['addons_authorizehg'] == 0) { // Addon is disabled, no check, lets send TRUE
			$tpl = "hgdisabled";
			$this->message = JText::_('JOPENSIM_AUTHORIZE_DISABLED');
			parent::display($tpl);
		} else {
			$task	= JFactory::getApplication()->input->get('task');
			switch($task) {
				case "confirmresponse":
					$response	= JFactory::getApplication()->input->get('response');
					if($response == "no") $this->message	= JText::_('JOPENSIM_AUTHORIZE_HGRESPONSENO');
					else $message	= $this->message = JText::_('JOPENSIM_AUTHORIZE_HGRESPONSEYES');
					$tpl = "response";
				break;
				case "hguserconfirm":
				default:
					$this->hguser	= JFactory::getApplication()->input->get('uuid');
					$this->minage	= $model->_settingsData['auth_minage'];
					$iscreated = $model->getHGuser($this->hguser);
					if(!$iscreated) $tpl = "error";
				break;
			}
			parent::display($tpl);
		}
	}
}
?>