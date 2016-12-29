<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2016 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class modOpenSimAdminHelper {
	public $opensim;
	public $osdb;
	public $addons;

	public function __construct($opensim) {
		$this->opensim	= $opensim;
		$this->osdb		= $this->opensim->_osgrid_db;
		$cparams		= JComponentHelper::getParams('com_opensim');
		$this->addons	= 
			 $cparams->get('addons_messages') + 
			($cparams->get('addons_profile')*2) +
			($cparams->get('addons_groups')*4) +
			($cparams->get('addons_inworldauth')*8) +
			($cparams->get('addons_search')*16) +
			($cparams->get('addons_currency')*32);
	}

	public function getButtons() {
		$button['quickicon']	= $this->renderPlainButton('quickicon_jopensim.php',JText::_('JOPENSIM_GRIDSTATUS'));
		$button['maps']			= $this->renderButton('index.php?option=com_opensim&view=maps','icon-48-os-maps.png',JText::_('JOPENSIM_MAPS'));
		$button['user']			= $this->renderButton('index.php?option=com_opensim&view=user','icon-48-os-user.png',JText::_('JOPENSIM_USER'));

		if(($this->addons &  4) == 4) {
			$button['groups']		= $this->renderButton('index.php?option=com_opensim&view=groups','icon-48-os-group.png',JText::_('JOPENSIM_GROUPS'));
		}
		if(($this->addons &  16) == 16) {
			$button['search']		= $this->renderButton('index.php?option=com_opensim&view=search','icon-48-os-search.png',JText::_('JOPENSIM_SEARCH'));
		}
		if(($this->addons &  32) == 32) {
			$button['money']		= $this->renderButton('index.php?option=com_opensim&view=money','icon-48-money.png',JText::_('JOPENSIM_MONEY'));
		}
		$button['misc']			= $this->renderButton('index.php?option=com_opensim&view=misc','icon-48-os-misc.png',JText::_('JOPENSIM_MISC'));

		$button['addons']		= $this->renderButton('index.php?option=com_opensim&view=addons','icon-48-addonhelp.png',JText::_('JOPENSIM_ADDONS'));

		return $button;
	}

	public function getRecentOnline($limit) {
		$query	= $this->opensim->getUserQueryObject(null,"last_login","DESC");
		$this->osdb->setQuery($query,0,$limit);
        $rows	= $this->osdb->loadObjectList();
        return $rows;
	}

	public function getRecentRegistered($limit) {
		$query	= $this->opensim->getUserQueryObject(null,"created","DESC");
		$this->osdb->setQuery($query,0,$limit);
        $rows	= $this->osdb->loadObjectList();
        return $rows;
	}

	public function getTopGroups($limit) {
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		$query->select("COUNT(DISTINCT(#__opensim_groupmembership.AgentID)) AS members");
		$query->select("#__opensim_group.*");
		$query->from("#__opensim_group");
		$query->join('LEFT',"#__opensim_groupmembership ON #__opensim_group.GroupID = #__opensim_groupmembership.GroupID");
		$query->group("#__opensim_group.GroupID");
		$query->order($db->escape("members DESC"));
		$db->setQuery($query, 0, $limit);
		$rows = $db->loadObjectList();
		return $rows;
	}

	public function renderButton($link,$image,$text) {
		$params = array('title'=>$text, 'border'=>'0', 'width'=>48, 'height'=>48);
		$button  = "<div class='icon-wrapper'>";
		$button .= "<div class='icon'>";
		$button .= sprintf("<a href='%s' class='os_mainscreen'>",$link);
		$button .= JHTML::_('image', 'administrator/components/com_opensim/assets/images/'.$image,$text,$params);
		$button .= sprintf("<span>%s</span></a>",$text);
		$button .= "</div></div>\n";
		return $button;
	}

	public function renderPlainButton($image,$text) {
		$params = array('title'=>$text, 'border'=>'0', 'width'=>48, 'height'=>48);
		$button  = "<div class='icon-wrapper'>";
		$button .= "<div class='icon'><a>";
		$button .= JHTML::_('image', 'administrator/components/com_opensim/assets/'.$image,$text,$params);
		$button .= sprintf("<span>%s</span></a>",$text);
		$button .= "</div></div>\n";
		return $button;
	}
}
