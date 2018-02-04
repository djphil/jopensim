<?php
/**
 * @module OpenSim Balance
 * @copyright Copyright (C) 2017 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
**/

// no direct access
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

class ModOpenSimBalanceHelper {
    public $showbalance;
    public $balancealign;
    public $balanceposition;
    public $balanceintable;
    public $showbalanceword;
    public $showcurrency;
    public $currencyposition;
    public $showbuylink;
    public $buylink;
    public $showselllink;
    public $selllink;
    public $showdisplaylink;
    public $displaylink;
    public $currencytype;
    public $linkalign;
    public $currencytexttype;
    public $currencytextbold;
    public $currencybadgestyle;
    public $currencylabelstyle;
    public $buttonblock;
    public $showicons;
    public $iconsonly;
    public $iconposition;
    public $linktype;
    public $showseparator;
    public $separator;
    public $separatorbold;
    public $buttonsize;
    public $buybuttonstyle;
    public $sellbuttonstyle;
    public $buyiconclass;
    public $selliconclass;
    public $displaybuttonstyle;
    public $displayiconclass;

    public function __construct($params) {
        $this->showbalance          = $params->get('showbalance');
        $this->balancealign         = $params->get('balancealign');
        $this->balanceposition      = $params->get('balanceposition');
        $this->balanceintable       = $params->get('balanceintable');
        $this->showbalanceword      = $params->get('showbalanceword');
        $this->showcurrency         = $params->get('showcurrency');
        $this->currencyposition     = $params->get('currencyposition','after');
        $this->showbuylink          = $params->get('showbuylink');
        $this->buylink              = $params->get('buylink');
        $this->showselllink         = $params->get('showselllink');
        $this->selllink             = $params->get('selllink');
        $this->showdisplaylink      = $params->get('showdisplaylink');
        $this->displaylink          = $params->get('displaylink');
        $this->currencytype         = $params->get('currencytype');
        $this->linkalign            = $params->get('linkalign');
        $this->currencytexttype     = $params->get('currencytexttype');
        $this->currencytextbold     = $params->get('currencytextbold');
        $this->currencybadgestyle   = $params->get('currencybadgestyle');
        $this->currencylabelstyle   = $params->get('currencylabelstyle');
        $this->buttonblock          = $params->get('buttonblock');
        $this->showicons            = $params->get('showicons');
        $this->iconsonly            = $params->get('iconsonly');
        $this->iconsonlytooltip     = $params->get('iconsonlytooltip');
        $this->iconposition         = $params->get('iconposition');
        $this->linktype             = $params->get('linktype');
        $this->showseparator        = $params->get('showseparator');
        $this->separator            = $params->get('separator');
        $this->separatorbold        = $params->get('separatorbold');
        $this->buttonsize           = $params->get('buttonsize');
        $this->buybuttonstyle       = $params->get('buybuttonstyle');
        $this->sellbuttonstyle      = $params->get('sellbuttonstyle');
        $this->displaybuttonstyle   = $params->get('displaybuttonstyle');
        $this->buyiconclass         = $params->get('buyiconclass');
        $this->selliconclass        = $params->get('selliconclass');
        $this->displayiconclass     = $params->get('displayiconclass');

        // Force option if needed
        // if ($this->showbalanceword) $this->showcurrency = true;
        if (!$this->showicons) $this->iconsonly = false;
    }

    public function getUserBalance($joomlaid) {
        $opensimID = $this->getUUID($joomlaid);
        if (!$opensimID) return FALSE;
        else return $this->getBalance($opensimID);
    }

    public function getUUID($joomlaid) {
        $db = JFactory::getDBO();
        $query	= $db->getQuery(true);

        $query->select($db->quoteName('#__opensim_userrelation.opensimID'));
        $query->from($db->quoteName('#__opensim_userrelation'));
        $query->where($db->quoteName('#__opensim_userrelation.joomlaID')." = ".$db->quote($joomlaid));

        $db->setQuery($query);
        $db->execute();
        $num_rows = $db->getNumRows();

        if ($db->getNumRows() == 1) {
            return $db->loadResult();
        } else {
            return null;
        }
    }

    public function getBalance($opensimID) {
        $db = JFactory::getDBO();
        $query	= $db->getQuery(true);

        $query->select($db->quoteName('#__opensim_moneybalances.balance'));
        $query->from($db->quoteName('#__opensim_moneybalances'));
        $query->where($db->quoteName('#__opensim_moneybalances.user')." = ".$db->quote($opensimID));

        $db->setQuery($query);
        $db->execute();
        $num_rows = $db->getNumRows();

        if ($db->getNumRows() == 1) {
            return $db->loadResult();
        } else {
            return null;
        }
    }
} // end ModOpenSimBalanceHelper
?>
