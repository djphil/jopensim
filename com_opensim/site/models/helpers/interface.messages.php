<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die();

// require_once(JPATH_COMPONENT_SITE.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'opensim.php');

class opensimModelInterfaceMessages extends opensimModelInterface {

	public $debug	= FALSE;

	public function __construct() {
		parent::__construct();
		if($this->jdebug['messages']) $this->debug	= TRUE;
	}

	public function initMethods() { // empty this this method to avoid endless loops
		return;
	}

	public function initAddons() { // empty this this method to avoid endless loops
		return;
	}

	public function SaveMessage($data) {
		if($this->debug === TRUE) $this->debuglog($data,"data for ".__FUNCTION__);
		$retval = "";

		$message = $this->opensim->parseOSxml($data['input']);
		if($this->debug === TRUE) $this->debuglog($message,__FUNCTION__);
		$searchstr = "?".">";
		$start = strpos($data['input'], $searchstr);
		if($this->debug === TRUE) $this->debuglog("\nstart:\n".$start,__FUNCTION__);
		if ($start != -1) {
			$start+=2;
			$msg = substr($data['input'], $start);
			if($this->debug === TRUE) $this->debuglog("\nmsg:\n".$msg,__FUNCTION__);
			try {
				$db = JFactory::getDBO();

				$newmessage	= new stdClass();
				$newmessage->imSessionID	= $message["imSessionID"];
				$newmessage->fromAgentID	= $message["fromAgentID"];
				$newmessage->fromAgentName	= $message["fromAgentName"];
				$newmessage->toAgentID		= $message["toAgentID"];
				$newmessage->fromGroup		= $message["fromGroup"];
				$newmessage->message		= $msg;
				$newmessage->remoteip		= $data["remoteip"];
				$newmessage->sent			= date("Y-m-d H:i:s");

				$result = $db->insertObject('#__opensim_offlinemessages', $newmessage);

				$retval = "<?x"."ml version=\"1.0\" encoding=\"utf-8\"?><boolean>true</boolean>";

				// no need to check in components settings for offline messages since we would not be here if disabled
				$usersettings = $this->opensim->getUserSettings($message['toAgentID']);
				if($usersettings['im2email'] == 1) { // Send Email to "toAgentID" only if set in user settings
					$config		= JFactory::getConfig();
					if($message['fromGroup'] == "true") {
						$groupname	= $this->getGroupName($message['fromAgentID']);
						$subject	= JText::sprintf(JOPENSIM_GROUPIM2MAILSUBJECT,$groupname,$config->get('fromname'))." (".$message['fromAgentName'].")";
						$dash		= strpos($message['message'],"|");
						if($dash) {
							$message['message'] = JText::_('JOPENSIM_GROUPNOTICE_SUBJECT').str_replace("|","\n\n",substr($message['message'],0,($dash+1))).substr($message['message'],($dash+1));
						}
					} else {
						$subject	= JText::_('IM2MAILSUBJECT')." ".$config->get('fromname')." (".$message['fromAgentName'].")";
					}
					$userdata	= $this->opensim->getUserData($message['toAgentID']);
					$mailer		= JFactory::getMailer();
					$sender		= array($config->get('mailfrom'),$config->get('fromname'));
					$body		= JText::_('IMFROM').": ".$message['fromAgentName']."\n\n".$message['message'];
					$mailer->setSender($sender);
					$mailer->addRecipient($userdata['email']);
					$mailer->setSubject($subject);
					$mailer->setBody($body);
					$mailer->Send();
				}
			} catch (Exception $e) {
				$errormsg = $e->getMessage();
				if($this->debug === TRUE) $this->debuglog("error: ".$errormsg,__FUNCTION__);
				$retval = "<?x"."ml version=\"1.0\" encoding=\"utf-8\"?><boolean>false</boolean>";
			}
		} else {
			if($this->debug === TRUE) $this->debuglog("\nno message found in request",__FUNCTION__);
			$retval = "<?x"."ml version=\"1.0\" encoding=\"utf-8\"?><boolean>false</boolean>";
		}
		if($this->debug === TRUE) $this->debuglog("Response for saveMessage:\n\n".$retval,__FUNCTION__);
		return $retval;
	}

	public function RetrieveMessages($data) {
		if($this->debug === TRUE) $this->debuglog("Offline Messages [RetrieveMessages] fired from ".$data['remoteip']." at line ".__LINE__." in ".__FILE__,__FUNCTION__);

		$message	= $this->opensim->parseOSxml($data['input']);
		if($this->debug === TRUE) $this->debuglog($message,"Parsed Message");
		$guid		= $message['Guid'];
		$db			= JFactory::getDBO();
		$query	= $db->getQuery(true);



		$query
			->select($db->quoteName('#__opensim_offlinemessages.message'))
			->from($db->quoteName('#__opensim_offlinemessages'))
			->where($db->quoteName('#__opensim_offlinemessages.toAgentID')." = ".$db->quote($guid))
			->order($db->quoteName('#__opensim_offlinemessages.sent'));
		$db->setQuery($query);
		$messages	= $db->loadRowList();
		$retval		= "<?x"."ml version=\"1.0\" encoding=\"utf-8\"?><ArrayOfGridInstantMessage xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\">";
		foreach($messages AS $message) {
			if($this->debug === TRUE) $this->debuglog($message[0],"Single read Message");
			$retval .= $message[0];
		}
		$retval .= "</ArrayOfGridInstantMessage>";

		$query = $db->getQuery(true);
		$conditions = array(
			$db->quoteName('toAgentID').' = '.$db->quote($guid)
		);
		$query->delete($db->quoteName('#__opensim_offlinemessages'));
		$query->where($conditions);
		$db->setQuery($query);
		$db->execute();

		if($this->debug === TRUE) $this->debuglog("Response for retrieveMessages:\n\n".$retval,__FUNCTION__);
		return $retval;
	}

	public function getGroupName($groupid) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select($db->quoteName('#__opensim_group.Name'));
		$query->from($db->quoteName('#__opensim_group'));
		$query->where($db->quoteName('#__opensim_group.GroupID').' = '.$db->quote($groupid));

		$db->setQuery($query);
		$db->execute();
		$foundgroup = $db->getNumRows();
		if($foundgroup == 1) {
			$groupdata = $db->loadAssoc();
			return $groupdata['Name'];
		} else {
			return JText::_('JOPENSIM_UNKNOWNGROUP');
		}
	}
}
?>