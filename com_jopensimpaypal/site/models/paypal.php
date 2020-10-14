<?php
/*
 * @component jOpenSimPayPal
 * @copyright Copyright (C) 2018 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die();
jimport('joomla.application.component.model');

require_once(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'jopensimpaypal.php');

class jOpenSimPayPalModelpaypal extends jOpenSimPayPalModeljOpenSimPayPal {
	public $filename		= "paypal.php";
	public $view			= "paypal";

	public function __construct() {
		parent::__construct();
	}

	public function getDefaultValue() {
		$defaultvalue = JFactory::getApplication()->input->get('defaultvalue', 10, 'INT' );
		return $defaultvalue;
	}

	public function getFormText($type) {
		if($type == "pre") $var = "pre_message";
		else $var = "post_message";
		$formtext = JFactory::getApplication()->input->get($var,"","HTML");
		return $formtext;
	}

	public function checkUserLimits() {
		$user = JFactory::getUser();
		$user->limitDay		= $this->checkUserLimit($user->id,"day");
		$user->limitWeek	= $this->checkUserLimit($user->id,"week");
		$user->limitMonth	= $this->checkUserLimit($user->id,"month");
	}

	public function checkUserLimit($userid,$time) {
		switch($time) {
			case "day":
				$query = sprintf("SELECT SUM(#__jopensimpaypal_transactions.amount_rlc) FROM #__jopensimpaypal_transactions WHERE #__jopensimpaypal_transactions.joomlaid = '%d' AND #__jopensimpaypal_transactions.transactiontime > DATE_SUB(NOW(), INTERVAL 1 DAY)",$userid);
			break;
			case "week":
				$query = sprintf("SELECT SUM(#__jopensimpaypal_transactions.amount_rlc) FROM #__jopensimpaypal_transactions WHERE #__jopensimpaypal_transactions.joomlaid = '%d' AND #__jopensimpaypal_transactions.transactiontime > DATE_SUB(NOW(), INTERVAL 1 WEEK)",$userid);
			break;
			case "month":
				$query = sprintf("SELECT SUM(#__jopensimpaypal_transactions.amount_rlc) FROM #__jopensimpaypal_transactions WHERE #__jopensimpaypal_transactions.joomlaid = '%d' AND #__jopensimpaypal_transactions.transactiontime > DATE_SUB(NOW(), INTERVAL 1 MONTH)",$userid);
			break;
			default:
				return null;
			break;
		}
		$db		= JFactory::getDBO();
		$db->setQuery($query);
		$limit	= $db->loadResult();
		return intval($limit);
	}

	public function __destruct() {
	}
}
?>