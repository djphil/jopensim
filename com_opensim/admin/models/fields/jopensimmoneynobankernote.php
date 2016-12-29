<?php
/*
 * @component jOpenSim Component
 * @copyright Copyright (C) 2016 FoTo50 http://www.jopensim.com/
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
defined('JPATH_BASE') or die;
jimport('joomla.form.helper');
jimport('joomla.application.component.helper');
JFormHelper::loadFieldClass('note');
require_once(JPATH_ROOT.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_opensim".DIRECTORY_SEPARATOR."includes".DIRECTORY_SEPARATOR."opensim.class.php");

class JFormFieldjopensimmoneynobankernote extends JFormFieldNote
{
	protected $type = 'jopensimmoneynobankernote';
	protected $bankerquery;
	protected $opensim;
	
	protected function getInput() {
		return '';
	}
	
	protected function getLabel() {
		$opensimgrid_dbhost		= JComponentHelper::getParams('com_opensim')->get('opensimgrid_dbhost');
		$opensimgrid_dbuser		= JComponentHelper::getParams('com_opensim')->get('opensimgrid_dbuser');
		$opensimgrid_dbpasswd	= JComponentHelper::getParams('com_opensim')->get('opensimgrid_dbpasswd');
		$opensimgrid_dbname		= JComponentHelper::getParams('com_opensim')->get('opensimgrid_dbname');
		$opensimgrid_dbport		= JComponentHelper::getParams('com_opensim')->get('opensimgrid_dbport');
		if(!$opensimgrid_dbhost || !$opensimgrid_dbuser || !$opensimgrid_dbpasswd || !$opensimgrid_dbname) {
			$this->element['label']			= JText::_('JOPENSIM_MONEY_NOBANKER_LABEL');
			$this->element['description']	= JText::_('JOPENSIM_ERROR_NODBSETTING');
		} else {
			if(!$opensimgrid_dbport) $opensimgrid_dbport = "3306";
			$this->opensim = new opensim($opensimgrid_dbhost,$opensimgrid_dbuser,$opensimgrid_dbpasswd,$opensimgrid_dbname,$opensimgrid_dbport,TRUE);
			$bankeruser = $this->getUserDataList();
			if(count($bankeruser) == 0) {
				$this->element['label']			= JText::_('JOPENSIM_MONEY_NOBANKER_LABEL');
				$this->element['description']	= JText::_('JOPENSIM_MONEY_NOBANKER_DESC');
			} else {
				$this->element['label']			= "";
				$this->element['description']	= "";
			}
		}
		return parent::getLabel();
	}

	public function getUserDataList() {
		$filter['UserLevel'] = -2;
		$this->bankerquery = $this->opensim->getUserQuery($filter,null,null,1);
		try {
			$db = $this->opensim->_osgrid_db;
			$db->setQuery($this->bankerquery);
			$this->banker = $db->loadAssocList();
			return $this->banker;
		} catch (Exception $e) {
			return array();
		}
	}
}
?>