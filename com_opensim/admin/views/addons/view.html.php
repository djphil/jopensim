<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class opensimViewaddons extends JViewLegacy {
	public function display($tpl = null) {

		$document		= JFactory::getDocument();
//		$document->addStyleSheet(JURI::base(true).'/components/com_opensim/assets/opensim.css', array('version' => '0.3.1.4'));
//		JHTML::stylesheet( 'opensim.css', 'administrator/components/com_opensim/assets/' );

		$this->sidebar	= JHtmlSidebar::render();
		$model			= $this->getModel('addons');
		$settings		= $model->getSettingsData();
		$task			= JFactory::getApplication()->input->get('task');
		$warningtext	= "";
		switch($task) {
			case "robustini":
				if($settings['addons_groups'] != 1) {
					$return			= '&return=' . urlencode(base64_encode("index.php?option=com_opensim&view=addons&task=robustini"));
					$warningtext	= "<span class='text-alert'>; For this, the addon &quot;Search&quot; must be enabled in the <a href='index.php?option=com_config&view=component&component=com_opensim&path=".$return."'>global configuration</a>!</span>\n";
				}
				$infotext = "<span class='jopensim_infotitle'>Robust.ini:</span>\n\n[Const]\n\n\tjOpensimURL = \"".substr(JURI::root(),0,-1)."\"\n\n\n; for the login screen:\n[GridInfoService]\n\n\twelcome = \${Const|jOpensimURL}/index.php?option=com_opensim\n";
			break;
			case "opensimini":
				$infotext = "<span class='jopensim_infotitle'>OpenSim.ini:</span>\n\n[Const]\n\n\tjOpensimURL = \"".substr(JURI::root(),0,-1)."\"\n\n";
			break;
			case "gettexture":
				if($settings['getTextureEnabled'] != 1) {
					$return			= '&return=' . urlencode(base64_encode("index.php?option=com_opensim&view=addons&task=gettexture"));
					$warningtext	= "<span class='text-alert'>; getTexture must be enabled in the <a href='index.php?option=com_config&view=component&component=com_opensim&path=".$return."'>global configuration</a>!</span>\n";
				}
				$infotext = $warningtext."; getTexture is a new Feature of OpenSim, allowing jOpenSim to display\n; textures directly on the website (e.g. profile images, group insignias, classified pics, etc...)\n\n<span class='jopensim_infotitle'>Robust.ini:</span>\n\n[Const]\n\n\tBaseURL = \"my.grid.server.url\" ; <-- this must match the value of ".JText::_('JOPENSIM_HOST')." in the global configuration\n\tPublicPort = \"xxxx\" ; <-- this must match the value of ".JText::_('JOPENSIM_ROBUST_PPORT')." in global configuration\n\n\n";
				$infotext .= "[ServiceList]\n\n\tGetTextureConnector = \"\${Const|PublicPort}/OpenSim.Capabilities.Handlers.dll:GetTextureServerConnector\"\n\n\n";
				$infotext .= "[CapsService]\n\n\tAssetService = \"OpenSim.Services.AssetService.dll:AssetService\"\n\n\n";
				$infotext .= "; and dont forget that this public port must be accessible from outside (firewall?) ;)\n\n";
			break;
			case "ominfo":
				if($settings['addons_messages'] != 1) {
					$return			= '&return=' . urlencode(base64_encode("index.php?option=com_opensim&view=addons&task=ominfo"));
					$warningtext	= "<span class='text-alert'>; The addon &quot;".JText::_('JOPENSIM_ADDONS_MESSAGES')."&quot; must be enabled in the <a href='index.php?option=com_config&view=component&component=com_opensim&path=".$return."'>global configuration</a>!</span>\n\n";
				}
				$infotext = $warningtext."<span class='jopensim_infotitle'>OpenSim.ini:</span>\n\n[Messaging]\n\n\tOfflineMessageModule = OfflineMessageModule\n\tOfflineMessageURL = \${Const|jOpensimURL}/index.php?option=com_opensim&view=interface&messaging=\n\tMuteListModule = MuteListModule\n\tMuteListURL = \${Const|jOpensimURL}/index.php?option=com_opensim&view=interface&messaging=\n\n\t; Optional:\n\tForwardOfflineGroupMessages = true\n";
			break;
			case "pinfo":
				if($settings['addons_profile'] != 1) {
					$return			= '&return=' . urlencode(base64_encode("index.php?option=com_opensim&view=addons&task=pinfo"));
					$warningtext	= "<span class='text-alert'>; The addon &quot;".JText::_('JOPENSIM_ADDONS_PROFILE')."&quot; must be enabled in the <a href='index.php?option=com_config&view=component&component=com_opensim&path=".$return."'>global configuration</a>!</span>\n\n";
				}
				$infotext = $warningtext."<span class='jopensim_infotitle'>Robust.ini:</span>\n\n[ServiceList]\n\n\t;this one MUST be commented out:\n\t; UserProfilesServiceConnector = \"\${Const|PublicPort}/OpenSim.Server.Handlers.dll:UserProfilesConnector\"\n\n[UserProfilesService]\n\n\tEnabled = false\n\n\n<span class='jopensim_infotitle'>OpenSim.ini:</span>\n\n[UserProfiles]\n\n\t;this one MUST be commented out:\n\t; ProfileServiceURL = \${Const|BaseURL}:\${Const|PublicPort}\n\n\n<span class='jopensim_infotitle'>config-include/GridCommon.ini</span> (Grid Mode)\nor\n<span class='jopensim_infotitle'>config-include/StandaloneCommon.ini</span> (Standalone Mode)\n\n[Profile]\n\n\tProfileURL = \${Const|jOpensimURL}/index.php?option=com_opensim&view=interface\n\tModule = \"jOpenSimProfile\"\n\n\t; Optional:\n\tDebug = true\n\n...and copy:\n".JPATH_COMPONENT_ADMINISTRATOR."/opensim_modules/jOpenSim.Profile.dll\nto your opensim/bin folder";
			break; 
			case "ginfo":
				if($settings['addons_groups'] != 1) {
					$return			= '&return=' . urlencode(base64_encode("index.php?option=com_opensim&view=addons&task=ginfo"));
					$warningtext	= "<span class='text-alert'>; The addon &quot;".JText::_('JOPENSIM_ADDONS_GROUPS')."&quot; must be enabled in the <a href='index.php?option=com_config&view=component&component=com_opensim&path=".$return."'>global configuration</a>!</span>\n\n";
				}
				$infotext = $warningtext."<span class='jopensim_infotitle'>OpenSim.ini:</span>\n\n[Groups]\n\n\tEnabled = true\n\tModule = GroupsModule\n\tServicesConnectorModule = XmlRpcGroupsServicesConnector\n\tGroupsServerURI = \"\${Const|jOpensimURL}/index.php?option=com_opensim&view=interface\"\n\n\t; These values must match your settings in \"Global Configuration\"!\n\tXmlRpcServiceReadKey = 1234\n\tXmlRpcServiceWriteKey = 4321\n\n\t; Optional:\n\tMessagingEnabled = true\n\tMessagingModule = GroupsMessagingModule\n\tNoticesEnabled = true\n\tDebugEnabled = false\n";
			break;
			case "sinfo":
				if($settings['addons_search'] != 1) {
					$return			= '&return=' . urlencode(base64_encode("index.php?option=com_opensim&view=addons&task=sinfo"));
					$warningtext	= "<span class='text-alert'>; The addon &quot;".JText::_('JOPENSIM_ADDONS_SEARCH')."&quot; must be enabled in the <a href='index.php?option=com_config&view=component&component=com_opensim&path=".$return."'>global configuration</a>!</span>\n\n";
				}
				$infotext = $warningtext."<span class='jopensim_infotitle'>Robust.ini:</span>\n\n[LoginService]\n\n\tSearchURL = \"\${Const|jOpensimURL}/index.php?option=com_opensim&view=inworldsearch&task=viewersearch&tmpl=component&\"\n\n\n\n";
				$infotext .= "<span class='jopensim_infotitle'>OpenSim.ini:</span>\n\n[DataSnapshot]\n\n\tindex_sims = true\n\t\n\t;This line is not needed anymore and SHOULD BE REMOVED/COMMENTED OUT since it might cause error lines in the console with the new dll module\n\t;data_services=\"\${Const|jOpensimURL}/components/com_opensim/registersearch.php\"\n\n\t; Optional (if you want to provide events, this is required to collect ALL parcel data):\n\tdata_exposure = all";
				$infotext .="\n\n[Search]\n\n\tSearchURL = \${Const|jOpensimURL}/index.php?option=com_opensim&view=interface\n\n\t; Optional:\n\tsearchPeople = false ; in case your people search returns double results\n\tDebugMode = true\n\n...and copy:\n".JPATH_COMPONENT_ADMINISTRATOR."/opensim_modules/jOpenSim.Search.dll\nto your opensim/bin folder";
			break;
			case "ainfo":
				if($settings['addons_authorize'] != 1) {
					$return			= '&return=' . urlencode(base64_encode("index.php?option=com_opensim&view=addons&task=ainfo"));
					$warningtext	= "<span class='text-alert'>; The addon &quot;".JText::_('JOPENSIM_ADDONS_AUTHORIZE')."&quot; must be enabled in the <a href='index.php?option=com_config&view=component&component=com_opensim&path=".$return."'>global configuration</a>!</span>\n\n";
				}
				$infotext = $warningtext."<span class='jopensim_infotitle'>config-include/GridCommon.ini</span> (Grid Mode)\nor\n<span class='jopensim_infotitle'>config-include/StandaloneCommon.ini</span> (Standalone Mode)\n\n[Modules]\n\n\tAuthorizationServices = \"RemoteAuthorizationServicesConnector\"\n\n[AuthorizationService]\n\tAuthorizationServerURI = \"\${Const|jOpensimURL}/index.php?option=com_opensim&view=auth&format=xml&tmpl=component\"";
			break;
			case "mapinfo":
				if($settings['jopensim_maps_varregions'] != 1) {
					$return			= '&return=' . urlencode(base64_encode("index.php?option=com_opensim&view=addons&task=mapinfo"));
					$warningtext	= "<span class='text-alert'>; The addon &quot;".JText::_('JOPENSIM_MAPS_VARREGIONS')."&quot; must be enabled in the <a href='index.php?option=com_config&view=component&component=com_opensim&path=".$return."'>global configuration</a>!</span>\n\n";
				}
				$infotext = "<span class='jopensim_infotitle'>Robust.ini:</span>\n\n[LoginService]\n\n\tMapTileURL = \"\${Const|BaseURL}:\${Const|PublicPort}/\";";
			break;
			case "dinfo":
				$infotext = "<span class='jopensim_infotitle'>Robust.ini:</span>\n\n[LoginService]\n\n\tDestinationGuide = \"\${Const|jOpensimURL}/index.php?option=com_opensim&view=guide&tmpl=component\"";
			break;
			case "iaiinfo":
				$infotext = "Copy:\n".JPATH_COMPONENT_ADMINISTRATOR."/lsl-scripts/jOpenSimTerminal.lsl\ninto a prim inside your OpenSimulator and see:\n<a href='http://help.jopensim.com/index.php?keyref=addon_inworldident' target='_blank'>http://help.jopensim.com/index.php?keyref=addon_inworldident</a> for more information.\n";
			break;
			case "minfo":
				if($settings['addons_currency'] != 1) {
					$return			= '&return=' . urlencode(base64_encode("index.php?option=com_opensim&view=addons&task=minfo"));
					$warningtext	= "<span class='text-alert'>; The addon &quot;".JText::_('JOPENSIM_MONEY')."&quot; must be enabled in the <a href='index.php?option=com_config&view=component&component=com_opensim&path=".$return."'>global configuration</a>!</span>\n\n";
				}
				$infotext = $warningtext."<span class='jopensim_infotitle'><i>Demoversion:</i>\n\nOpenSim.ini:</span>\n\n[Economy]\n\n\tEconomyModule = jOpenSimMoneyModule\n\tCurrencyURL = \"\${Const|jOpensimURL}/index.php?option=com_opensim&view=interface\"\n\n\t; Optional:\n\tDebugMode = \"1\"\n\tPay2myself = true\n\tPayPopup = true\n\tPayPopupMsgSender = \"You paid {0} to {1}\"\n\tPayPopupMsgReceiver = \"You received {0} from {1}\"\n\n";
				$infotext .="\n\n<span class='jopensim_infotitle'>Robust.ini:</span>\n\n[GridInfoService]\n\n\teconomy = \${Const|jOpensimURL}/components/com_opensim/\n\n; Optional:\n[LoginService]\n\tCurrency = \"jO\$\"\n";
				$infotext .="\n\n... and download OpenSim.Joomla.Money.dll from http://www.jopensim.com and copy it to the bin folder of your OpenSim.\n";
				$infotext .="\n\n<span class='jopensim_infotitle'><i>Full Version:</i></span>\n\nAfter purchasing a license for the full version at http://www.jopensim.com you will receive email with further information.\n";
			break;
            // Remote Admin
			case "rainfo":
				if($settings['enableremoteadmin'] == 1) {
					$return			= '&return=' . urlencode(base64_encode("index.php?option=com_opensim&view=addons&task=rainfo"));
					$warningtext	= "<span class='text-alert'>; The addon &quot;".JText::_('JOPENSIM_REMOTE_ADMIN')."&quot; must be enabled in the <a href='index.php?option=com_config&view=component&component=com_opensim&path=".$return."'>global configuration</a>!</span>\n\n";
				}
				$infotext = $warningtext."<span class='jopensim_infotitle'>OpenSim.ini:</span>\n\n[RemoteAdmin]\n\tenabled = true\n\tport = xxxx\n\taccess_password = xxxx\n\tenabled_methods = admin_broadcast|admin_create_region|admin_get_opensim_version\n\n\t; Optional:\n\tregion_file_template = \"{0}x{1}-{2}.ini\"\n";
			break;
			default:
				$infotext = JText::sprintf('JOPENSIM_ADDONS_MAIN',"<i class='icon-info' title='addon information' alt='addon information'></i>");
			break;
		}

		$settings		= $model->_settingsData;
		$this->infotext	= $infotext;
		$this->addons	= $settings['addons'];

		$this->_setToolbar($tpl);
		parent::display($tpl);
	}

	public function _setToolbar($tpl) {
		JToolBarHelper::title(JText::_('JOPENSIM_NAME')." ".JText::_('JOPENSIM_ADDONHELP'),'32-addonhelp');
		switch($tpl) {
			default:
				if (JFactory::getUser()->authorise('core.admin', 'com_opensim')) {
					JToolBarHelper::preferences('com_opensim','700','950',JText::_('JOPENSIM_GLOBAL_SETTINGS'));
				}
				JToolBarHelper::help("", false, JText::_('JOPENSIM_HELP_ADDON'));
			break;
		}
	}
}

?>
