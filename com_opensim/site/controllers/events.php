<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2016 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class OpenSimControllerevents extends OpenSimController {
	public function __construct() {
		parent::__construct();
		$model = $this->getModel('events');
	}

	public function insertevent() {
		$model = $this->getModel('events');
		$opensim = $model->opensim;

		$app		= JFactory::getApplication();
		$Itemid		= $app->input->get('Itemid');

		$data['eventname']		= trim($app->input->get('eventname'));
		$data['eventdate']		= trim($app->input->get('eventdate','','RAW'));
		$data['eventtime']		= trim($app->input->get('eventtime','','RAW'));
		$data['eventtimezone']	= trim($app->input->get('eventtimezone','','RAW'));
		$data['eventduration']	= trim($app->input->get('eventduration'));
		$data['eventlocation']	= trim($app->input->get('eventlocation'));
		$data['eventcategory']	= trim($app->input->get('eventcategory'));
		$data['covercharge']	= trim($app->input->get('covercharge'));
		$data['description']	= trim($app->input->get('description','','RAW'));
		$data['eventflags']		= trim($app->input->get('eventflags'));
		$data['parceluuid']		= trim($app->input->get('eventlocation'));
//		echo "<pre>\n";
//		print_r($data);
//		echo "</pre>\n";
//		exit;
		$retval = $model->insertEvent($data);
		if($retval['error'] > 0) {
			$message	= "";
			if($retval['error'] & 1) $message .= JText::_(ERROR_NOEVENTNAME);
			if($retval['error'] & 2) $message .= JText::_(ERROR_NOEVENTDATE);
			if($retval['error'] & 4) $message .= JText::_(ERROR_NOEVENTUSER);
			$layout		= "submitevent";
			$type		= "Error";
			$addvalues	=	"&task=inserterror".
							"&eventname=".$data['eventname'].
							"&eventdate=".$data['eventdate'].
							"&eventtime=".$data['eventtime'].
							"&eventtimezone=".$data['eventtimezone'].
							"&eventduration=".$data['eventduration'].
							"&eventlocation=".$data['eventlocation'].
							"&eventcategory=".$data['eventcategory'].
							"&covercharge=".$data['covercharge'].
							"&description=".$data['description'].
							"&eventflags=".$data['eventflags'].
							"&Itemid=".$Itemid;
		} else {
			$type		= "message";
			$message	= JText::_(OK_EVENTCREATED);
			$layout		= "eventlist";
			$addvalues	= "";
		}
		$redirect = "index.php?option=com_opensim&view=events&layout=".$layout.$addvalues;
		$this->setRedirect($redirect,$message,$type);
	}

	public function updateevent() {
		$type		= "message";
		$message	= JText::_(TODO);
		$redirect	= "index.php?option=com_opensim&view=events&layout=eventlist";
		$this->setRedirect($redirect,$message,$type);
	}

	public function deleteevent() {
		$eventid	= JFactory::getApplication()->input->get('eventid');
		$model		= $this->getModel('events');
		$retval		= $model->deleteEvent($eventid);
		if($retval['error'] > 0) {
			if($retval['error'] & 4) $message .= JText::_(ERROR_NOEVENTUSER);
			$layout		= "eventlist";
			$type		= "Error";
			$message	= "";
		} else {
			$type		= "message";
			$message	= JText::_(OK_EVENTDELETED);
			$layout		= "eventlist";
		}
		$redirect = "index.php?option=com_opensim&view=events&layout=".$layout;
		$this->setRedirect($redirect,$message,$type);
	}
}
?>
