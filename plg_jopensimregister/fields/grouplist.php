<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2017 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('JPATH_PLATFORM') or die;

if(!defined('DS')) define("DS",DIRECTORY_SEPARATOR);

JFormHelper::loadFieldClass('list');

class JFormFieldGrouplist extends JFormFieldList {
	protected $type = 'Grouplist';
	protected $opensim;

	protected function getOptions() {
		$groups = array();
		$db = JFactory::getDBO();
		$query	= $db->getQuery(true);
		$query->select($db->quoteName('#__opensim_group.GroupID'));
		$query->select($db->quoteName('#__opensim_group.Name'));
		$query->from($db->quoteName('#__opensim_group'));
		$query->order($db->quoteName('#__opensim_group.Name'));

		$db->setQuery($query);

		$groupdata = $db->loadAssocList();
		foreach($groupdata AS $groupresult) {
			$results[$groupresult['GroupID']] = $groupresult;
		}

		$options = array();

		$tmp = JHtml::_('select.option', "-1", JText::_('PLG_JOPENSIMREGISTER_NOGROUP'), 'value', 'text');
		$tmp->title = JText::_('PLG_JOPENSIMREGISTER_NOGROUP');

		// Add the option object to the result set.
		$options[] = $tmp;

		foreach ($groupdata as $option) {

			// Create a new option object based on the <option /> element.
			$tmp = JHtml::_('select.option', (string) $option['GroupID'], trim((string) $option['Name']), 'value', 'text');
			$tmp->title = $option['Name'];

			// Add the option object to the result set.
			$options[] = $tmp;
		}

		reset($options);

		return $options;
	}
}