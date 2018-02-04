<?php
/*
 * @component jOpenSimPayPal Component
 * @copyright Copyright (C) 2017 FoTo50 https://www.jopensim.com/
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
defined('JPATH_BASE') or die;
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('note');

class JFormFieldjopensimpaypallogfilenote extends JFormFieldNote
{
	protected $type = 'jopensimpaypallogfilenote';
	
	protected function getInput() {
		return '';
	}
	
	protected function getLabel() {
		$logpath		= JComponentHelper::getParams('com_jopensimpaypal')->get('logpath');
		if(substr($logpath,-1) != DIRECTORY_SEPARATOR) $logpath .= DIRECTORY_SEPARATOR;
		$logfile = $logpath."jopensimpaypal.log";
		if(is_file($logfile)) {
			$logfilesize = $this->human_filesize(filesize($logfile));
			$this->element['label']	= JText::_('COM_JOPENSIMPAYPAL_LOGFILENOTE_LABEL');
			$this->element['description'] = JText::sprintf('COM_JOPENSIMPAYPAL_LOGFILENOTE_DESC',$logfilesize);
		} else {
			$this->element['label']	= "";
			$this->element['description'] = "";
		}
		return parent::getLabel();
	}

	protected function human_filesize($bytes, $decimals = 2) {
		$sz = 'BKMGTP';
		$factor = floor((strlen($bytes) - 1) / 3);
		return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
	}
}
