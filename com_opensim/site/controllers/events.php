<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
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

		$data['eventname']		= trim($app->input->get('eventname','','RAW'));
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
		$retval = $model->insertEvent($data);
		if(array_key_exists("error",$retval) && $retval['error'] > 0) {
			$message	= "";
			if($retval['error'] & 1) {
				$message .= JText::_('ERROR_NOEVENTNAME');
//				$data['eventname'] = "jopensimeventerror";
			}
			if($retval['error'] & 2) $message .= JText::_('ERROR_NOEVENTDATE');
			if($retval['error'] & 4) $message .= JText::_('ERROR_NOEVENTUSER');
			$layout		= "submitevent";
			$type		= "Error";
			$addvalues	=	"&task=inserterror";

			// set the values into the session to refill form
			$session = JFactory::getSession();
			$session->set('jopensim_error_eventname', $data['eventname']);
			$session->set('jopensim_error_eventdate', $data['eventdate']);
			$session->set('jopensim_error_eventtime', $data['eventtime']);
			$session->set('jopensim_error_eventtimezone', $data['eventtimezone']);
			$session->set('jopensim_error_eventduration', $data['eventduration']);
			$session->set('jopensim_error_eventlocation', $data['eventlocation']);
			$session->set('jopensim_error_eventcategory', $data['eventcategory']);
			$session->set('jopensim_error_covercharge', $data['covercharge']);
			$session->set('jopensim_error_description', $data['description']);
			$session->set('jopensim_error_eventflags', $data['eventflags']);
		} else {
			$type		= "message";
			$message	= JText::_('OK_EVENTCREATED');
			$layout		= "eventlist";
			$addvalues	= "";
		}
		$redirect = JRoute::_('&option=com_opensim&view=events&layout='.$layout.$addvalues);
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
		if(array_key_exists("error",$retval) && $retval['error'] > 0) {
			if($retval['error'] & 4) $message .= JText::_('ERROR_NOEVENTUSER');
			$layout		= "eventlist";
			$type		= "Error";
			$message	= "";
		} else {
			$type		= "message";
			$message	= JText::_('OK_EVENTDELETED');
			$layout		= "eventlist";
		}
		$redirect = "index.php?option=com_opensim&view=events&layout=".$layout;
		$this->setRedirect($redirect,$message,$type);
	}
}
?>
