<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2015 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class opensimViewaddons extends JViewLegacy {
	public function display($tpl = null) {
		JHTML::stylesheet( 'opensim.css', 'administrator/components/com_opensim/assets/' );

		$this->sidebar	= JHtmlSidebar::render();
		$model			= $this->getModel('addons');
		$settings		= $model->getSettingsData();
		$task			= JFactory::getApplication()->input->get('task');
		$warningtext	= "";
		switch($task) {
			case "robustini":
				if($settings['addons_groups'] != 1) {
					$return			= '&return=' . urlencode(base64_encode("index.php?option=com_opensim&view=addons&task=robustini"));
					$warningtext	= "<span class='jopensim_addonwarning'>; For this, the addon &quot;Search&quot; must be enabled in the <a href='index.php?option=com_config&view=component&component=com_opensim&path=".$return."'>global configuration</a>!</span>\n";
				}
				$infotext = "<span class='jopensim_infotitle'>Robust.ini:</span>\n\n[Const]\n\n\tjOpensimURL = \"".substr(JURI::root(),0,-1)."\"\n\n\n; for the login screen:\n[GridInfoService]\n\n\twelcome = \${Const|jOpensimURL}/index.php?option=com_opensim\n";
			break;
			case "opensimini":
				$infotext = "<span class='jopensim_infotitle'>OpenSim.ini:</span>\n\n[Const]\n\n\tjOpensimURL = \"".substr(JURI::root(),0,-1)."\"\n\n";
			break;
			case "gettexture":
				if($settings['getTextureEnabled'] != 1) {
					$return			= '&return=' . urlencode(base64_encode("index.php?option=com_opensim&view=addons&task=gettexture"));
					$warningtext	= "<span class='jopensim_addonwarning'>; getTexture must be enabled in the <a href='index.php?option=com_config&view=component&component=com_opensim&path=".$return."'>global configuration</a>!</span>\n";
				}
				$infotext = $warningtext."; getTexture is a new Feature of OpenSim, allowing jOpenSim to display\n; textures directly on the website (e.g. profile images, group insignias, classified pics, etc...)\n\n<span class='jopensim_infotitle'>Robust.ini:</span>\n\n[Const]\n\n\tBaseURL = \"my.grid.server.url\" ; <-- this must match the value of ".JText::_('JOPENSIM_HOST')." in the global configuration\n\tPublicPort = \"xxxx\" ; <-- this must match the value of ".JText::_('JOPENSIM_ROBUST_PPORT')." in global configuration\n\n\n";
				$infotext .= "[ServiceList]\n\n\tGetTextureConnector = \"\${Const|PublicPort}/OpenSim.Capabilities.Handlers.dll:GetTextureServerConnector\"\n\n\n";
				$infotext .= "[CapsService]\n\n\tAssetService = \"OpenSim.Services.AssetService.dll:AssetService\"\n\n\n";
				$infotext .= "; and dont forget that this public port must be accessible from outside (firewall?) ;)\n\n";
			break;
			case "ominfo":
				if($settings['addons_messages'] != 1) {
					$return			= '&return=' . urlencode(base64_encode("index.php?option=com_opensim&view=addons&task=ominfo"));
					$warningtext	= "<span class='jopensim_addonwarning'>; The addon &quot;".JText::_('JOPENSIM_ADDONS_MESSAGES')."&quot; must be enabled in the <a href='index.php?option=com_config&view=component&component=com_opensim&path=".$return."'>global configuration</a>!</span>\n\n";
				}
				$infotext = $warningtext."<span class='jopensim_infotitle'>OpenSim.ini:</span>\n\n[Messaging]\n\n\tOfflineMessageModule = OfflineMessageModule\n\tOfflineMessageURL = \${Const|jOpensimURL}/components/com_opensim/interface.php\n\tMuteListModule = MuteListModule\n\tMuteListURL = \${Const|jOpensimURL}/components/com_opensim/interface.php\n\n\t; Optional:\n\tForwardOfflineGroupMessages = true\n";
			break;
			case "pinfo":
				if($settings['addons_profile'] != 1) {
					$return			= '&return=' . urlencode(base64_encode("index.php?option=com_opensim&view=addons&task=pinfo"));
					$warningtext	= "<span class='jopensim_addonwarning'>; The addon &quot;".JText::_('JOPENSIM_ADDONS_PROFILE')."&quot; must be enabled in the <a href='index.php?option=com_config&view=component&component=com_opensim&path=".$return."'>global configuration</a>!</span>\n\n";
				}
				$infotext = $warningtext."<span class='jopensim_infotitle'>config-include/GridCommon.ini</span> (Grid Mode)\nor\n<span class='jopensim_infotitle'>config-include/StandaloneCommon.ini</span> (Standalone Mode)\n\n[Profile]\n\n\tProfileURL = \${Const|jOpensimURL}/components/com_opensim/interface.php\n\tModule = \"jOpenSimProfile\"\n\n\t; Optional:\n\tDebug = true\n\n...and copy:\n".JPATH_COMPONENT_ADMINISTRATOR."/opensim_modules/jOpenSim.Profile.dll\nto your opensim/bin folder";
			break; 
			case "ginfo":
				if($settings['addons_groups'] != 1) {
					$return			= '&return=' . urlencode(base64_encode("index.php?option=com_opensim&view=addons&task=ginfo"));
					$warningtext	= "<span class='jopensim_addonwarning'>; The addon &quot;".JText::_('JOPENSIM_ADDONS_GROUPS')."&quot; must be enabled in the <a href='index.php?option=com_config&view=component&component=com_opensim&path=".$return."'>global configuration</a>!</span>\n\n";
				}
				$infotext = $warningtext."<span class='jopensim_infotitle'>OpenSim.ini:</span>\n\n[Groups]\n\n\tEnabled = true\n\tModule = GroupsModule\n\tServicesConnectorModule = XmlRpcGroupsServicesConnector\n\tGroupsServerURI = \"\${Const|jOpensimURL}/components/com_opensim/interface.php\"\n\n\t; These values must match your settings in \"Global Configuration\"!\n\tXmlRpcServiceReadKey = 1234\n\tXmlRpcServiceWriteKey = 4321\n\n\t; Optional:\n\tMessagingEnabled = true\n\tMessagingModule = GroupsMessagingModule\n\tNoticesEnabled = true\n\tDebugEnabled = false\n";
			break;
			case "sinfo":
				if($settings['addons_search'] != 1) {
					$return			= '&return=' . urlencode(base64_encode("index.php?option=com_opensim&view=addons&task=sinfo"));
					$warningtext	= "<span class='jopensim_addonwarning'>; The addon &quot;".JText::_('JOPENSIM_ADDONS_SEARCH')."&quot; must be enabled in the <a href='index.php?option=com_config&view=component&component=com_opensim&path=".$return."'>global configuration</a>!</span>\n\n";
				}
				$infotext = $warningtext."<span class='jopensim_infotitle'>Robust.ini:</span>\n\n[LoginService]\n\n\tSearchURL = \"\${Const|jOpensimURL}/index.php?option=com_opensim&view=inworldsearch&task=viewersearch&tmpl=component&\"\n\n\n\n";
				$infotext .= "<span class='jopensim_infotitle'>OpenSim.ini:</span>\n\n[DataSnapshot]\n\n\tindex_sims = true\n\tdata_services=\"\${Const|jOpensimURL}/components/com_opensim/registersearch.php\"\n\n\t; Optional (if you want to provide events, this is required to collect ALL parcel data):\n\tdata_exposure = all";
				$infotext .="\n\n[Search]\n\n\tSearchURL = \${Const|jOpensimURL}/components/com_opensim/interface.php\n\n\t; Optional:\n\tsearchPeople = false ; in case your people search returns double results\n\tDebugMode = true\n\n...and copy:\n".JPATH_COMPONENT_ADMINISTRATOR."/opensim_modules/jOpenSim.Search.dll\nto your opensim/bin folder";
			break;
			case "iaiinfo":
				$infotext = "Copy:\n".JPATH_COMPONENT_ADMINISTRATOR."/lsl-scripts/jOpenSimTerminal.lsl\ninto a prim inside your OpenSimulator and see:\n<a href='http://help.jopensim.com/index.php?keyref=addon_inworldident' target='_blank'>http://help.jopensim.com/index.php?keyref=addon_inworldident</a> for more information.\n";
			break;
			case "minfo":
				if($settings['addons_currency'] != 1) {
					$return			= '&return=' . urlencode(base64_encode("index.php?option=com_opensim&view=addons&task=minfo"));
					$warningtext	= "<span class='jopensim_addonwarning'>; The addon &quot;".JText::_('JOPENSIM_MONEY')."&quot; must be enabled in the <a href='index.php?option=com_config&view=component&component=com_opensim&path=".$return."'>global configuration</a>!</span>\n\n";
				}
				$infotext = $warningtext."<span class='jopensim_infotitle'><i>Demoversion:</i>\n\nOpenSim.ini:</span>\n\n[Economy]\n\n\tEconomyModule = jOpenSimMoneyModule\n\tCurrencyURL = \"\${Const|jOpensimURL}/components/com_opensim/currency.php\"\n\n\t; Optional:\n\tDebugMode = \"1\"\n\tPay2myself = true\n\tPayPopup = true\n\tPayPopupMsgSender = \"You paid {0} to {1}\"\n\tPayPopupMsgReceiver = \"You received {0} from {1}\"\n\n";
				$infotext .="\n\n<span class='jopensim_infotitle'>Robust.ini:</span>\n\n[GridInfoService]\n\n\teconomy = \${Const|jOpensimURL}/components/com_opensim/\n\n; Optional:\n[LoginService]\n\tCurrency = \"jO\$\"\n";
				$infotext .="\n\n... and download OpenSim.Joomla.Money.dll from http://www.jopensim.com and copy it to the bin folder of your OpenSim.\n";
				$infotext .="\n\n<span class='jopensim_infotitle'><i>Full Version:</i></span>\n\nAfter purchasing a license for the full version at http://www.jopensim.com you will receive email with further information.\n";
			break;
			default:
				$infotext = JText::sprintf('JOPENSIM_ADDONS_MAIN',"<i class='icon-info' title='addon information' alt='addon information'></i>");
			break;
		}

		$settings	= $model->_settingsData;
		$this->assignRef('infotext',$infotext);
		$this->assignRef('addons',$settings['addons']);

		$this->_setToolbar($tpl);
		parent::display($tpl);
	}

	public function _setToolbar($tpl) {
		JToolBarHelper::title(JText::_('JOPENSIM_NAME')." ".JText::_('JOPENSIM_ADDONHELP'),'32-addonhelp');
		switch($tpl) {
			default:
				JToolBarHelper::help("", false, JText::_('JOPENSIM_HELP_ADDON'));
			break;
		}
	}
}

?>
