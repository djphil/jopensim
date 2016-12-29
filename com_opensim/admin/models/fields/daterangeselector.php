<?php
/*
 * @component jOpenSim Component
 * @copyright Copyright (C) 2016 FoTo50 http://www.jopensim.com/
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */

// No direct access to this file
defined('_JEXEC') or die;
 
// import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');
 
/**
 * Form Field class for jOpenSim
 */
class JFormFielddaterangeselector extends JFormFieldList {
	/**
	* The field type.
	*
	* @var         string
	*/
	protected $type = 'daterangeselector';


	/**
	* Method to get a list of options for a list input.
	*
	* @return      array           An array of JHtml options.
	*/
	public function getOptions() {
		$values = $this->getValues();
		$options = array();
		foreach($values as $value => $text) {
			$zaehler = count($options);
			$options[$zaehler] = new stdClass();
			$options[$zaehler]->value	= $value;
			$options[$zaehler]->text	= JText::_($text);
		}
		return $options;
	}

	public function getValues() {
		$values = array();
		$value[1]		= "JOPENSIM_MONEY_DATERANGE_1";
		$value[7]		= "JOPENSIM_MONEY_DATERANGE_7";
		$value[30]		= "JOPENSIM_MONEY_DATERANGE_30";
		$value[60]		= "JOPENSIM_MONEY_DATERANGE_60";
		$value[365]		= "JOPENSIM_MONEY_DATERANGE_365";
		return $value;
	}
}
?>