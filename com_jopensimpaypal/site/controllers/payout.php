<?php
/*
 * @component jOpenSimPayPal
 * @copyright Copyright (C) 2018 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

class jOpenSimPayPalControllerpayout extends jOpenSimPayPalController {
	public function __construct() {
		parent::__construct();
	}

	public function payout() {
		$model = $this->getModel('payout');
		$retval = $model->savePayoutRequest();
		if($retval === TRUE) {
			$type		= "message";
			$message	= JText::_('COM_JOPENSIMPAYPAL_PAYOUTOK');
		} else {
			$type		= "error";
			$message	= JText::_('COM_JOPENSIMPAYPAL_PAYOUTERROR').var_export($retval,TRUE);
		}
		$itemid	= JFactory::getApplication()->input->get('Itemid');
		$return = JFactory::getApplication()->input->get('returnto');
		switch($return) {
			case "jopensim":
				$redirect = "index.php?option=com_opensim&view=inworld&task=money&Itemid=".$itemid;
			break;
			default:
				$redirect = "index.php?option=com_jopensimpaypal&view=transactionlist&noadd=1&Itemid=".$itemid;
			break;
		}
		$this->setRedirect($redirect,$message,$type);
	}
}
?>