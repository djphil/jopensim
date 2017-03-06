<?php
// No direct access to this file
defined('_JEXEC') or die;
 
// import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');
 
/**
 * HelloWorld Form Field class for the HelloWorld component
 */
class JFormFieldcurrencyselector extends JFormFieldList {
	/**
	* The field type.
	*
	* @var         string
	*/
	protected $type = 'currencyselector';

	/**
	* Method to get a list of options for a list input.
	*
	* @return      array           An array of JHtml options.
	*/
	protected function getOptions($preselected = null) {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('currency');
		$query->from('#__jopensimpaypal_currencies');
		$query->order('reihe, currency');
		$db->setQuery((string)$query);
		$messages = $db->loadObjectList();
		$options = array();
		if ($messages) {
			foreach($messages as $message) {
				if($message->currency == $preselected) $options[] = JHtml::_('select.option', $message->currency, $message->currency, TRUE);
				else $options[] = JHtml::_('select.option', $message->currency, $message->currency);
			}
		}
		$options = array_merge(parent::getOptions(), $options);
		return $options;
	}
}
?>