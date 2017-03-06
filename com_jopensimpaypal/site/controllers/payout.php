<?php
/*
 * @package Joomla 2.5
 * @copyright Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html, see LICENSE.php
 *
 * @component jOpenSimPayPal
 * @copyright Copyright (C) 2013 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// No direct access

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
		$itemid	= JRequest::getVar('Itemid');
		$return = JRequest::getString('returnto');
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