<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2015 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');

class JFormFieldLastnamelist extends JFormFieldList {
	protected	$type = 'Lastnamelist';
	public		$lastnamelist;

	public function __construct() {
		parent::__construct();
	}

	protected function getOptions() {
		$params			= &JComponentHelper::getParams('com_opensim');
		$nameliststring	= $params->get('lastnamelist',"error_no_lastname");
		$namelist = explode("\n",$nameliststring);
		foreach($namelist AS $key => $val) {
			if(!trim($val)) unset($namelist[$key]);
			else $namelist[$key] = trim($val);
		}

		// Initialize variables.
		$options = array();
		

		foreach ($namelist as $option) {

			// Create a new option object based on the <option /> element.
			$tmp = JHtml::_(
				'select.option', (string) $option, trim((string) $option), 'value', 'text'
			);

			// Add the option object to the result set.
			$options[] = $tmp;
		}

		reset($options);

		return $options;
	}

}