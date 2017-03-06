<?php
// No direct access to this file
defined('_JEXEC') or die;
 
// import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');
 
/**
 * HelloWorld Form Field class for the HelloWorld component
 */
class JFormFieldpayoutstatusselector extends JFormFieldList {
	/**
	* The field type.
	*
	* @var         string
	*/
	protected $type = 'payoutstatusselector';


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
		$value[-2]		= "COM_JOPENSIMPAYPAL_PAYOUTSTATUS_CANCELED";
		$value[-1]		= "COM_JOPENSIMPAYPAL_PAYOUTSTATUS_PENDING";
		$value[0]		= "COM_JOPENSIMPAYPAL_PAYOUTSTATUS_NEW";
		$value[1]		= "COM_JOPENSIMPAYPAL_PAYOUTSTATUS_APPROVED";
		$value[2]		= "COM_JOPENSIMPAYPAL_PAYOUTSTATUS_FINISHED";
		return $value;
	}
}
?>