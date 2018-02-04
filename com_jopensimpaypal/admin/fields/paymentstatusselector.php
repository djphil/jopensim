<?php
/*
 * @component jOpenSimPayPal
 * @copyright Copyright (C) 2017 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die;
 
// import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');
 
class JFormFieldpaymentstatusselector extends JFormFieldList {
	/**
	* The field type.
	*
	* @var         string
	*/
	protected $type = 'paymentstatusselector';

	/**
	* Method to get a list of options for a list input.
	*
	* @return      array           An array of JHtml options.
	*/
	public function getOptions($preselected = null) {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('DISTINCT(#__jopensimpaypal_transactions.payment_type) payment_type');
		$query->from('#__jopensimpaypal_transactions');
		$query->where('#__jopensimpaypal_transactions.payment_type IS NOT NULL');
		$query->order('#__jopensimpaypal_transactions.payment_type');
		$db->setQuery((string)$query);
		$paymentstatus = $db->loadObjectList();
		$options = array();
		if ($paymentstatus) {
			foreach($paymentstatus as $status) {
				$zaehler = count($options);
				$options[$zaehler] = new stdClass();
				$options[$zaehler]->value	= $status->payment_type;
				$options[$zaehler]->text	= $status->payment_type;
			}
		}
		return $options;
	}
}
?>