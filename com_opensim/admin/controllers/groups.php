<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2015 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class OpenSimControllergroups extends OpenSimController {
	public function __construct() {
		parent::__construct();
	}

	public function cancel() {
		$this->setRedirect('index.php?option=com_opensim&view=opensim');
	}

	public function assignOwner() {
		$data = JFactory::getApplication()->input->request->getArray();;
		$model = $this->getModel('groups');
		$model->assignOwner($data['groupID'],$data['OwnerRoleID'],$data['memberID']);
		$this->setRedirect('index.php?option=com_opensim&view=groups',JText::_('JOPENSIM_OKGROUPNEWOWNER'));
	}

	public function deleteGroups() {
		$jinput = JFactory::getApplication()->input;
		$groups2delete	= $jinput->get('checkGroup',array(),'ARRAY');
		$model = $this->getModel('groups');
		$countgroups = $model->deleteGroups($groups2delete);
		$message = JText::sprintf('JOPENSIM_GROUPSDELETED',$countgroups);
		$this->setRedirect('index.php?option=com_opensim&view=groups',$message);
	}
}
?>
