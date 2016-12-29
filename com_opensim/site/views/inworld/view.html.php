<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2015 FoTo50 http://www.jopensim.com
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// no direct access

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');
jimport( 'joomla.html.toolbar.button.standard' );
jimport( 'joomla.html.parameter' );

class opensimViewinworld extends JViewLegacy {
	public function display($tpl = null) {
		$this->assetpath = JUri::base(true)."/components/com_opensim/assets/";
		$doc = JFactory::getDocument();
		$doc->addStyleSheet($this->assetpath.'opensim.css');
		$doc->addStyleSheet($this->assetpath.'opensim.override.css');

		$this->Itemid = JFactory::getApplication()->input->get('Itemid');
		$menu	= JFactory::getApplication()->getMenu();
		$active	= $menu->getActive($this->Itemid);
		if (is_object($active)) {
			$params					= &JComponentHelper::getParams('com_opensim');
			$this->pageclass_sfx	= $params->get('pageclass_sfx');
		} else {
			$this->pageclass_sfx	= "";
		}

		$model = $this->getModel('inworld');
		$this->settings = $model->getSettingsData();
		$task = JFactory::getApplication()->input->get('task','','method','string');
		$user = JFactory::getUser();
		if($user->guest) {
			$tpl = "needlogin";
		} elseif(!$model->_osgrid_db) {
			$tpl = "error";
		} else {
			$created = $model->opensimIsCreated();
			if(!$created) {
				$model->cleanIdents(); // delete Idents
				$inworldIdent = $model->opensimGetInworldIdent();
				if($inworldIdent) {
					$tpl = "identdisplay";
					$this->assignRef('identstring',$inworldIdent);
					$command = JText::_('TERMINALCOMMANDTEXT');
					if($this->settings['addons_terminalchannel'] > 0) $typechannel = "/".$this->settings['addons_terminalchannel']." ";
					else $typechannel = "";
					$identcommand = $typechannel."identify ".$inworldIdent;
					$this->identcommand = $identcommand;
					$terminalList = $model->getTerminalList();
					$this->terminalList = $terminalList;
				} else {
					$tpl = "create";

					// how is the last name controled?
					switch($this->settings['lastnametype']) {
						case 1: // only allow from the list
							$lastnamefield = "<select name='lastname' id='lastname'>\n";
							$lastnames = explode("\n",$this->settings['lastnamelist']);
							if(count($lastnames) > 0) {
								foreach($lastnames AS $lastname) {
									$lastnamefield .= "\t<option value='".trim($lastname)."'>".trim($lastname)."</option>\n";
								}
							}
							$lastnamefield .= "</select>\n";
						break;
						case 0: // no control at all
						case -1: // allow everything but not from the list (the validation has to be done later)
							$lastnamefield = "<input type='text' name='lastname' id='lastname' />";
						break;
					}
					$this->assignRef('lastnamefield',$lastnamefield);
				}
			} else {
				$this->topbar = $this->generateTopbar($task);
				$this->osdata = $model->getUserData($created);
				if(!$this->osdata['im2email']) $this->osdata['im2email'] = 0;
				switch($task) {
					case "welcome":
						$tpl = "welcome";
						$this->welcometitle = JText::_('WELCOME_DEFAULTTITLE');
						$this->welcometext = JText::_('WELCOME_DEFAULTTEXT');
					break;
					case "messages":
						$this->messages = $model->messagelist($this->osdata['uuid']);
						$tpl = "messages";
					break;
					case "messagedetail":
						$imSessionID	= JFactory::getApplication()->input->get('imSessionID','','','string');
						$fromAgentID	= JFactory::getApplication()->input->get('fromAgentID','','','string');
						$messagedetails	= $model->messagedetail($imSessionID,$fromAgentID);
						$fromAgentName	= $messagedetails['fromAgentName'];
						unset($messagedetails['fromAgentName']);
						$this->assignRef('messagedetails',$messagedetails);
						$this->assignRef('fromAgentName',$fromAgentName);
						$tpl = "messagedetail";
					break;
					case "profile":
						$this->wantmask		= $model->profile_wantmask();
						$this->skillsmask	= $model->profile_skillsmask();
						$this->profiledata	= $model->getprofile($this->osdata['uuid']);
						$this->friendlist	= $model->getUserFriends($this->osdata['uuid']);

						$this->profiledata['image2nd']	= "&nbsp;";
						$this->profiledata['image1st']	= "&nbsp;";
						$this->textureFormat			= $this->settings['getTextureFormat'];
						if($this->settings['profile_images'] == 1 && $this->settings['getTextureEnabled'] == 1) {
							$this->zerouuid = $model->opensim->zerouid;
							if(substr($this->settings['opensim_host'],0,7) != "http://") $this->opensimhost = "http://".$this->settings['opensim_host'];
							else $this->opensimhost		= $this->settings['opensim_host'];
							$this->robust_port			= $this->settings['robust_port'];
							if($this->profiledata['image'] && $this->profiledata['image'] != $this->zerouuid) {
								$this->profiledata['image2nd'] = "<img src='".$this->opensimhost.":".$this->robust_port."/CAPS/GetTexture/?texture_id=".$this->profiledata['image']."&format=".$this->textureFormat."' style='max-width:".$this->settings['profile_images_maxwidth']."px; max-height:".$this->settings['profile_images_maxheight']."px;' />\n";
							}
							if($this->profiledata['firstLifeImage'] && $this->profiledata['firstLifeImage'] != $this->zerouuid) {
								$this->profiledata['image1st'] = "<img src='".$this->opensimhost.":".$this->robust_port."/CAPS/GetTexture/?texture_id=".$this->profiledata['firstLifeImage']."&format=".$this->textureFormat."' style='max-width:".$this->settings['profile_images_maxwidth']."px; max-height:".$this->settings['profile_images_maxheight']."px;' />\n";
							}
						}

						if($this->profiledata['partner']) {
							$newtask			= "divorce";
							$partnerimage		= "divorce.png";
							$partnerimgtitle	= JText::_('JOPENSIM_PROFILE_PARTNER_DIVORCE');
							$modalwidth			= 300;
							$modalheight		= 200;
						} else {
							$partnerrequest		= $model->partnerrequeststatus();
							switch($partnerrequest['status']) {
								case "sent":
									$newtask			= "partnercancel";
									$partnerimage		= "partnering_waiting.png";
									$partnername		= $model->getOpenSimName($partnerrequest['partneruuid']);
									$partnerimgtitle	= JText::sprintf('JOPENSIM_PROFILE_PARTNER_REQUEST_SENT',$partnername);
									$modalwidth			= 300;
									$modalheight		= 200;
								break;
								case "received":
									$newtask			= "partnerrequest";
									$partnerimage		= "partnering_request.png";
									$partnername		= $model->getOpenSimName($partnerrequest['partneruuid']);
									$partnerimgtitle	= JText::sprintf('JOPENSIM_PROFILE_PARTNER_REQUEST_RECEIVED',$partnername);
									$modalwidth			= 300;
									$modalheight		= 200;
								break;
								default:
									$newtask			= "partner";
									$partnerimage		= "partnering.png";
									$partnerimgtitle	= JText::_('JOPENSIM_PROFILE_PARTNER_REQUEST');
									$modalwidth			= 300;
									$modalheight		= 500;
								break;
							}
						}
						$this->newtask			= $newtask;
						$this->modalwidth		= $modalwidth;
						$this->modalheight		= $modalheight;
						$this->partnerimage		= $partnerimage;
						$this->partnerimgtitle	= $partnerimgtitle;
						$tpl = "profile";
					break;
					case "partner":
						$profiledata	= $model->getprofile($this->osdata['uuid']);
						$friendlist		= $model->getUserFriends($this->osdata['uuid']);
						if(is_array($friendlist) && count($friendlist) > 0) {
							$friends = array();
							foreach($friendlist AS $key => $friend) {
								$count						= count($friends);
								$friends[$count]['name']	= $model->getOpenSimName($friend['friendid']);
								$friends[$count]['uuid']	= $friend['friendid'];
								$friends[$count]['profile']	= $model->getprofile($friend['friendid']);
								$friends[$count]['ignore']	= $model->getIgnoreStatus($friend['friendid']);
							}
							sort($friends);
						}
						$this->assignRef('friends',$friends);
						$this->assignRef('osdata',$osdata);
						$tpl = "partner";
					break;
					case "divorce":
						$profiledata	= $model->getprofile($this->osdata['uuid']);
						$partnername	= $profiledata['partnername'];
						$partneruuid	= $profiledata['partner'];
						$divorcetext	= JText::sprintf('JOPENSIM_PROFILE_PARTNER_DIVORCE_SURE',$partnername);
						$this->assignRef('divorcetext',$divorcetext);
						$this->assignRef('partneruuid',$partneruuid);
						$tpl			= "partnerdivorce";
					break;
					case "partnercancel":
						$partnerrequest	= $model->partnerrequeststatus();
						if(!is_array($partnerrequest) || !array_key_exists("partneruuid",$partnerrequest)) $partnername = "error";
						else $partnername = $model->getOpenSimName($partnerrequest['partneruuid']);
						$parntercancel	= JText::sprintf('JOPENSIM_PROFILE_PARTNER_REQUEST_CANCEL',$partnername);
						$this->assignRef('partneruuid',$partnerrequest['partneruuid']);
						$this->assignRef('parntercanceltext',$parntercancel);
						$tpl			= "partnercancel";
					break;
					case "partnerrequest":
						$partnerrequest	= $model->partnerrequeststatus();
						if(!is_array($partnerrequest) || !array_key_exists("partneruuid",$partnerrequest)) $partnername = "error";
						else $partnername = $model->getOpenSimName($partnerrequest['partneruuid']);
						$partneraccept	= JText::sprintf('JOPENSIM_PROFILE_PARTNER_REQUEST_ACCEPT',$partnername);
						$this->assignRef('partneruuid',$partnerrequest['partneruuid']);
						$this->assignRef('partneraccepttext',$partneraccept);
						$tpl			= "partnerrequest";
					break;
					case "groups":
						$grouplist = $model->groupmemberships($this->osdata['uuid']);
						$this->assignRef('grouplist',$grouplist);
						$tpl = "groups";
					break;
					case "groupdetail":
						JHTML::stylesheet( 'opensim_modal.css', 'components/com_opensim/assets/' );
						$groupid = JFactory::getApplication()->input->get('groupid','','','string');
						$grouplist = $model->groupmemberships($this->osdata['uuid'],$groupid);
						$this->assignRef('grouplist',$grouplist[0]);
						$this->assignRef('grouplist1',$grouplist);
						$tpl = "groupdetail";
					break;
					case "groupnotices":
						JHTML::stylesheet( 'opensim_modal.css', 'components/com_opensim/assets/' );
						$groupid = JFactory::getApplication()->input->get('groupid','','','string');
						$grouplist = $model->groupmemberships($this->osdata['uuid'],$groupid);
						if($grouplist[0]['acceptnotices'] == 1 && $grouplist[0]['power']['power_receivenotice'] == 1 && $grouplist[0]['hasnotices'] > 0) $noticelist = $model->getnotices($groupid);
						else $noticelist = array();
						$this->assignRef('grouplist',$grouplist[0]);
						$this->assignRef('noticelist',$noticelist);
						$tpl = "groupnotices";
					break;
					case "groupmembers":
						JHTML::stylesheet( 'opensim_modal.css', 'components/com_opensim/assets/' );
						$groupid = JFactory::getApplication()->input->get('groupid','','','string');
						$memberlist = $model->memberlist($groupid);
						$grouplist = $model->groupmemberships($this->osdata['uuid'],$groupid);
						$power = $model->group_power($this->osdata['uuid'],$groupid);
						$this->assignRef('memberlist',$memberlist);
						$this->assignRef('power',$power);
						$this->assignRef('grouplist',$grouplist[0]);
						$tpl = "groupmembers";
					break;
					case "money":
						$balance		= $model->getBalance($this->osdata['uuid']);
						$currencyname	= $model->getCurrencyName();
						$jinput			= JFactory::getApplication()->input;
						$range			= $jinput->get('range', '30', 'INTEGER');
						$transactions	= $model->transactionlist($this->osdata['uuid'],$range);
						$this->assignRef('balance',$balance);
						$this->assignRef('currencyname',$currencyname);
						$this->assignRef('range',$range);
						$this->assignRef('transactions',$transactions);
						// check if jOpenSimMoney is installed
						$jopensimpaypalfolder = JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_jopensimpaypal";
						if(is_dir($jopensimpaypalfolder)) {
							$paypallink = "<a href='".JRoute::_("index.php?option=com_jopensimpaypal&view=paypal&Itemid=".$this->Itemid."&returnto=jopensim")."'>".JText::_('JOPENSIM_MONEY_BUYPAYPAL')."</a>";
							$params = &JComponentHelper::getParams('com_jopensimpaypal');
							$payout = $params->get('currencyratesell');
							$minbuy = $params->get('minbuy');
							if($minbuy > 0) $minbalance = $payout * $minbuy;
							else $minbalance = $payout;
							$jopensimpaypal = TRUE;
						} else {
							$paypallink = "";
							$payout = 0;
							$minbalance = -1;
							$jopensimpaypal = FALSE;
						}
						if($jopensimpaypal === TRUE && $payout > 0 && $balance >= $minbalance) {
							$payoutlink = "<a href='".JRoute::_("index.php?option=com_jopensimpaypal&view=payout&Itemid=".$this->Itemid."&returnto=jopensim")."'>".JText::_('JOPENSIM_MONEY_PAYOUTREQUEST')."</a>";
						} else {
							$payoutlink = "";
						}
						$this->assignRef('paypallink',$paypallink);
						$this->assignRef('jopensimmoney',$jopensimmoney);
						$this->assignRef('payout',$payout);
						$this->assignRef('payoutlink',$payoutlink);
						$tpl = "money";
					break;
					default:
						// how is the last name controled?
						switch($this->settings['lastnametype']) {
							case 1: // only allow from the list
								$lastnamefield = "<select name='lastname' id='lastname' class='".$this->pageclass_sfx."'>\n";
								$lastnames = explode("\n",trim($this->settings['lastnamelist']));
								$lastnames[] = $this->osdata['lastname']; // ensure that the own lastname is also in the list
								foreach($lastnames AS $key => $lastname) $lastnames[$key] = trim($lastname); // just to ensure different linebreaks between Win and Linux
								$lastnames = array_unique($lastnames); // no duplicate values ( ... own lastname ... )
								if(count($lastnames) > 0) {
									foreach($lastnames AS $lastname) {
										$lastnamefield .= "\t<option value='".$lastname."'";
										if($lastname == $this->osdata['lastname']) $lastnamefield .= " selected='selected'";
										$lastnamefield .= ">".$lastname."</option>\n";
									}
								}
								$lastnamefield .= "</select>\n";
							break;
							case 0: // no control at all
							case -1: // allow everything but not from the list (the check has to be done later)
								$lastnamefield = "<input type='text' name='lastname' id='lastname' value='".$this->osdata['lastname']."' class='".$this->pageclass_sfx."' />";
							break;
						}

						$timezone_identifiers = DateTimeZone::listIdentifiers();
						$this->assignRef('timezones',$timezone_identifiers);
						if(!array_key_exists('eventtimedefault',$this->settings) || !$this->settings['eventtimedefault']) $this->settings['eventtimedefault'] = "UTC";
						if(!array_key_exists('timezone',$this->osdata) || !$this->osdata['timezone']) $this->osdata['timezone'] = $this->settings['eventtimedefault'];


						$this->assignRef('lastnamefield',$lastnamefield);
						$tpl = "display";
					break;
				}
			}
		}
		$this->assignRef('userid',$created);
		$this->assignRef('user',$user);

		parent::display($tpl);
	}

	public function generateTopbar($task) {
		if(!$task) $task = "default";
		$settings	= JText::_('JOPENSIM_SETTINGS');
		$messages	= JText::_('JOPENSIM_MESSAGES');
		$profile	= JText::_('JOPENSIM_PROFILE');
		$groups		= JText::_('JOPENSIM_GROUPS');
		$money		= JText::_('JOPENSIM_MONEY');
		$topbar = "<div><div class='contentsubheading_table".$this->pageclass_sfx."'>";
		$topbar .= "<div class='contentsubheading".$this->pageclass_sfx."'>";
		$topbar .= ($task == "default")  ? $settings:"<a href='".JRoute::_('index.php?option=com_opensim&view=inworld&task=default&Itemid='.$this->Itemid)."' class='".$this->pageclass_sfx."'>".$settings."</a>";
		$topbar .= "</div>";
		if($this->settings['addons_messages'] == 1){
			$topbar .= "<div class='contentsubheading".$this->pageclass_sfx."'>";
			$topbar .= ($task == "messages") ? $messages:"<a href='".JRoute::_('index.php?option=com_opensim&view=inworld&task=messages&Itemid='.$this->Itemid)."' class='".$this->pageclass_sfx."'>".$messages."</a>";
			$topbar .= "</div>";
		}
		if($this->settings['addons_profile'] == 1){
			$topbar .= "<div class='contentsubheading".$this->pageclass_sfx."'>";
			$topbar .= ($task == "profile")   ? $profile:"<a href='index.php?option=com_opensim&view=inworld&task=profile&Itemid=".$this->Itemid."' class='".$this->pageclass_sfx."'>".$profile."</a>";
			$topbar .= "</div>";
		}
		if($this->settings['addons_groups'] == 1){
			$topbar .= "<div class='contentsubheading".$this->pageclass_sfx."'>";
			$topbar .= ($task == "groups")   ? $groups:"<a href='index.php?option=com_opensim&view=inworld&task=groups&Itemid=".$this->Itemid."' class='".$this->pageclass_sfx."'>".$groups."</a>";
			$topbar .= "</div>";
		}
		if($this->settings['addons_currency'] == 1){
			$topbar .= "<div class='contentsubheading".$this->pageclass_sfx."'>";
			$topbar .= ($task == "money")   ? $money:"<a href='index.php?option=com_opensim&view=inworld&task=money&Itemid=".$this->Itemid."' class='".$this->pageclass_sfx."'>".$money."</a>";
			$topbar .= "</div>";
		}
		$topbar .= "</div></div>";
		return $topbar;
	}
}
