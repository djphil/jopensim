<?php
/*
 * @component jOpenSim Component
 * @copyright Copyright (C) 2018 FoTo50 http://www.jopensim.com/
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */

// No direct access to this file
defined('_JEXEC') or die;
 
// import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');
 
class JFormFieldreceiverselector extends JFormFieldList {
	/**
	* The field type.
	*
	* @var         string
	*/

	protected $type = 'receiverselector';

	/**
	* Method to get a list of options for a list input.
	*/
	public function getOptions() {
//		JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/opensim/models', '');
		$model	= JModelLegacy::getInstance('OpenSim','OpenSimModel',array());
		$osdb	= $model->getOpenSimGridDB();
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		$query->select('DISTINCT(#__opensim_moneytransactions.receiver)');
		$query->from('#__opensim_moneytransactions');
		$db->setQuery((string)$query);
		$sender	= $db->loadColumn();

		$query	= $osdb->getQuery(true);
		$query->select('CONCAT_WS(" ",FirstName,LastName) AS text');
		$query->select('PrincipalID AS value');
		$query->from('UserAccounts');
		$query->order('FirstName,LastName');
		$osdb->setQuery((string)$query);
		$senderlist = $osdb->loadObjectList();

		$options = array();
		if ($senderlist) {
			foreach($senderlist as $senderoption) {
				if(in_array($senderoption->value,$sender)) {
					$zaehler = count($options);
					$options[$zaehler] = new stdClass();
					$options[$zaehler]->value	= $senderoption->value;
					$options[$zaehler]->text	= $senderoption->text;
				}
			}
		}

		return $options;
	}
}
?>