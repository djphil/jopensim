<?php
/*
 * @module OpenSim Balance (mod_opensim_balance)
 * @copyright Copyright (C) 2017 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
?>

<?php
/* HTML START HERE */
echo '<div class="mod_opensim_balance">';

// BALANCE ON TOP 
if ($os_balance->showbalance && $os_balance->balanceposition == "ontop")
{
    if ($os_balance->balanceintable)
    {
        echo '<table class="table table-striped table-condensed table-hover">';
        echo '<tbody><tr><td>';
    }

    if ($os_balance->showbalanceword)
    {
        echo '<span class="mod_opensim_balance_balanceword">'.JText::_('MOD_OPENSIM_BALANCE_BALANCEWORD').': </span>';
        if ($os_balance->balancealign == "right")
            $currency_class .= " pull-right";
    }

    if (!$os_balance->showbalanceword) {echo '<center>';}
        echo '<span class="mod_opensim_balance_currency '.$currency_class.'">';
    if ($os_balance->showcurrency && $os_balance->currencyposition == "before")
        echo '<span class="mod_opensim_balance_currency"> '.$currency.' </span>';
    echo '<span class="mod_opensim_balance_value"> '.$userbalance.'</span>';
    if ($os_balance->showcurrency && $os_balance->currencyposition == "after")
        echo '<span class="mod_opensim_balance_currency"> '.$currency.'</span>';
    echo '</span>';
    if (!$os_balance->showbalanceword) echo '</center>';;
    if ($os_balance->showbalanceword) echo '<div class="clearfix"></div>';
    if ($os_balance->balanceintable) echo '</td></tr></tbody></table>';
    echo '<div class="spacer"></div>';
}

// LINKS
if ($os_balance->showbuylink || $os_balance->showselllink || $os_balance->showdisplaylink)
{
    if ($os_balance->linkalign == 1) echo '<div class="mod_opensim_balance_links text-right">';
    else if ($os_balance->linkalign == 2) echo '<div class="mod_opensim_balance_links text-center">';
    else echo '<div class="mod_opensim_balance_links text-left">';
}

// ICON BEFORE
if (!$os_balance->iconsonly && $os_balance->iconposition == "before")
{
    // Link 1 only - Link 2 only - Link 3 only
    if ($os_balance->showbuylink && !$os_balance->showselllink && !$os_balance->showdisplaylink)
        echo '<a href="'.$buylink.'" class="'.$buybuttonstyle.' '.$buttons_class.'">'.$buyicon.$mod_opensim_balance_buy.'</a>';
    if (!$os_balance->showbuylink && $os_balance->showselllink && !$os_balance->showdisplaylink)
        echo '<a href="'.$buylink.'" class="'.$buybuttonstyle.' '.$buttons_class.'">'.$buyicon.$mod_opensim_balance_buy.'</a>';
    if (!$os_balance->showbuylink && !$os_balance->showselllink && $os_balance->showdisplaylink)
        echo '<a href="'.$displaylink.'" class="'.$displaybuttonstyle.' '.$buttons_class.'">'.$displayicon.$mod_opensim_balance_display.'</a>';

    // Link 1 2 or 2 1
    if ($os_balance->showbuylink && $os_balance->showselllink && !$os_balance->showdisplaylink) {
        echo '<a href="'.$buylink.'" class="'.$buybuttonstyle.' '.$buttons_class.'">'.$buyicon.$mod_opensim_balance_buy.'</a>';
        echo '<span class="'.$separator_class.'">'.$separator."</span>";
        echo '<a href="'.$selllink.'" class="'.$sellbuttonstyle.' '.$buttons_class.'">'.$sellicon.$mod_opensim_balance_sell.'</a>';
    }

    // Link 1 3 or 3 1
    if ($os_balance->showbuylink && !$os_balance->showselllink && $os_balance->showdisplaylink)
    {
        echo '<a href="'.$buylink.'" class="'.$buybuttonstyle.' '.$buttons_class.'">'.$buyicon.$mod_opensim_balance_buy.'</a>';
        echo '<span class="'.$separator_class.'">'.$separator."</span>";
        echo '<a href="'.$displaylink.'" class="'.$displaybuttonstyle.' '.$buttons_class.'">'.$displayicon.$mod_opensim_balance_display.'</a>';
    }

    // Link 2 3 or 3 2
    if (!$os_balance->showbuylink && $os_balance->showselllink && $os_balance->showdisplaylink)
    {
        echo '<a href="'.$selllink.'" class="'.$sellbuttonstyle.' '.$buttons_class.'">'.$sellicon.$mod_opensim_balance_sell.'</a>';
        echo '<span class="'.$separator_class.'">'.$separator."</span>";
        echo '<a href="'.$displaylink.'" class="'.$displaybuttonstyle.' '.$buttons_class.'">'.$displayicon.$mod_opensim_balance_display.'</a>';
    }

    // Link 1 2 3 or 3 2 1
    if ($os_balance->showbuylink && $os_balance->showselllink && $os_balance->showdisplaylink)
    {
        echo '<a href="'.$buylink.'" class="'.$buybuttonstyle.' '.$buttons_class.'">'.$buyicon.$mod_opensim_balance_buy.'</a>';
        echo '<span class="'.$separator_class.'">'.$separator."</span>";
        echo '<a href="'.$selllink.'" class="'.$sellbuttonstyle.' '.$buttons_class.'">'.$sellicon.$mod_opensim_balance_sell.'</a>';
        echo '<span class="'.$separator_class.'">'.$separator."</span>";
        echo '<a href="'.$displaylink.'" class="'.$displaybuttonstyle.' '.$buttons_class.'">'.$displayicon.$mod_opensim_balance_display.'</a>';
    }
}

// ICON AFTER
if ($os_balance->iconposition == "after")
{
    // Link 1 only - Link 2 only - Link 3 only
    if ($os_balance->showbuylink && !$os_balance->showselllink && !$os_balance->showdisplaylink)
        echo '<a href="'.$buylink.'" class="'.$buybuttonstyle.' '.$buttons_class.'">'.$mod_opensim_balance_buy.$buyicon.'</a>';
    if (!$os_balance->showbuylink && $os_balance->showselllink && !$os_balance->showdisplaylink)
        echo '<a href="'.$buylink.'" class="'.$buybuttonstyle.' '.$buttons_class.'">'.$mod_opensim_balance_buy.$buyicon.'</a>';
    if (!$os_balance->showbuylink && !$os_balance->showselllink && $os_balance->showdisplaylink)
        echo '<a href="'.$displaylink.'" class="'.$displaybuttonstyle.' '.$buttons_class.'">'.$mod_opensim_balance_display.$displayicon.'</a>';

    // Link 1 2 or 2 1
    if ($os_balance->showbuylink && $os_balance->showselllink && !$os_balance->showdisplaylink) {
        echo '<a href="'.$buylink.'" class="'.$buybuttonstyle.' '.$buttons_class.'">'.$mod_opensim_balance_buy.$buyicon.'</a>';
        echo '<span class="'.$separator_class.'">'.$separator."</span>";
        echo '<a href="'.$selllink.'" class="'.$sellbuttonstyle.' '.$buttons_class.'">'.$mod_opensim_balance_sell.$sellicon.'</a>';
    }

    // Link 1 3 or 3 1
    if ($os_balance->showbuylink && !$os_balance->showselllink && $os_balance->showdisplaylink)
    {
        echo '<a href="'.$buylink.'" class="'.$buybuttonstyle.' '.$buttons_class.'">'.$mod_opensim_balance_buy.$buyicon.'</a>';
        echo '<span class="'.$separator_class.'">'.$separator."</span>";
        echo '<a href="'.$displaylink.'" class="'.$displaybuttonstyle.' '.$buttons_class.'">'.$mod_opensim_balance_display.$displayicon.'</a>';
    }

    // Link 2 3 or 3 2
    if (!$os_balance->showbuylink && $os_balance->showselllink && $os_balance->showdisplaylink)
    {
        echo '<a href="'.$selllink.'" class="'.$sellbuttonstyle.' '.$buttons_class.'">'.$mod_opensim_balance_sell.$sellicon.'</a>';
        echo '<span class="'.$separator_class.'">'.$separator."</span>";
        echo '<a href="'.$displaylink.'" class="'.$displaybuttonstyle.' '.$buttons_class.'">'.$mod_opensim_balance_display.$displayicon.'</a>';
    }

    // Link 1 2 3 or 3 2 1
    if ($os_balance->showbuylink && $os_balance->showselllink && $os_balance->showdisplaylink)
    {
        echo '<a href="'.$buylink.'" class="'.$buybuttonstyle.' '.$buttons_class.'">'.$mod_opensim_balance_buy.$buyicon.'</a>';
        echo '<span class="'.$separator_class.'">'.$separator."</span>";
        echo '<a href="'.$selllink.'" class="'.$sellbuttonstyle.' '.$buttons_class.'">'.$mod_opensim_balance_sell.$sellicon.'</a>';
        echo '<span class="'.$separator_class.'">'.$separator."</span>";
        echo '<a href="'.$displaylink.'" class="'.$displaybuttonstyle.' '.$buttons_class.'">'.$mod_opensim_balance_display.$displayicon.'</a>';
    }
}

// ICONS ONLY
if ($os_balance->showicons && $os_balance->iconsonly)
{
    $buyicon_tooltip = "";
    $sellicon_tooltip = "";
    $displayicon_tooltip = "";

    $buyicon = str_replace("&nbsp;", '', $buyicon);
    $sellicon = str_replace("&nbsp;", '', $sellicon);
    $displayicon = str_replace("&nbsp;", '', $displayicon);

    if ($os_balance->iconsonlytooltip)
    {
        $buttons_class .= " hasTooltip";
        $buyicon_tooltip = JText::_('MOD_OPENSIM_BALANCE_BUY');
        $sellicon_tooltip = JText::_('MOD_OPENSIM_BALANCE_SELL');
        $displayicon_tooltip = JText::_('MOD_OPENSIM_BALANCE_DISPLAY');
    }

    // Link 1 only - Link 2 only - Link 3 only
    if ($os_balance->showbuylink && !$os_balance->showselllink && !$os_balance->showdisplaylink)
        echo '<a href="'.$buylink.'" class="'.$buybuttonstyle.' '.$buttons_class.'" title="'.$buyicon_tooltip.'">'.$buyicon.'</a>';
    if (!$os_balance->showbuylink && $os_balance->showselllink && !$os_balance->showdisplaylink)
        echo '<a href="'.$buylink.'" class="'.$buybuttonstyle.' '.$buttons_class.'" title="'.$buyicon_tooltip.'">'.$buyicon.'</a>';
    if (!$os_balance->showbuylink && !$os_balance->showselllink && $os_balance->showdisplaylink)
        echo '<a href="'.$displaylink.'" class="'.$displaybuttonstyle.' '.$buttons_class.'" title="'.$displayicon_tooltip.'">'.$displayicon.'</a>';

    // Link 1 2 or 2 1
    if ($os_balance->showbuylink && $os_balance->showselllink && !$os_balance->showdisplaylink) {
        echo '<a href="'.$buylink.'" class="'.$buybuttonstyle.' '.$buttons_class.'" title="'.$buyicon_tooltip.'">'.$buyicon.'</a>';
        echo '<span class="'.$separator_class.'">'.$separator."</span>";
        echo '<a href="'.$selllink.'" class="'.$sellbuttonstyle.' '.$buttons_class.'" title="'.$sellicon_tooltip.'">'.$sellicon.'</a>';
    }

    // Link 1 3 or 3 1
    if ($os_balance->showbuylink && !$os_balance->showselllink && $os_balance->showdisplaylink)
    {
        echo '<a href="'.$buylink.'" class="'.$buybuttonstyle.' '.$buttons_class.'" title="'.$buyicon_tooltip.'">'.$buyicon.'</a>';
        echo '<span class="'.$separator_class.'">'.$separator."</span>";
        echo '<a href="'.$displaylink.'" class="'.$displaybuttonstyle.' '.$buttons_class.'" title="'.$displayicon_tooltip.'">'.$displayicon.'</a>';
    }

    // Link 2 3 or 3 2
    if (!$os_balance->showbuylink && $os_balance->showselllink && $os_balance->showdisplaylink)
    {
        echo '<a href="'.$selllink.'" class="'.$sellbuttonstyle.' '.$buttons_class.'" title="'.$sellicon_tooltip.'">'.$sellicon.'</a>';
        echo '<span class="'.$separator_class.'">'.$separator."</span>";
        echo '<a href="'.$displaylink.'" class="'.$displaybuttonstyle.' '.$buttons_class.'" title="'.$displayicon_tooltip.'">'.$displayicon.'</a>';
    }

    // Link 1 2 3 or 3 2 1
    if ($os_balance->showbuylink && $os_balance->showselllink && $os_balance->showdisplaylink)
    {
        echo '<a href="'.$buylink.'" class="'.$buybuttonstyle.' '.$buttons_class.'" title="'.$buyicon_tooltip.'">'.$buyicon.'</a>';
        echo '<span class="'.$separator_class.'">'.$separator."</span>";
        echo '<a href="'.$selllink.'" class="'.$sellbuttonstyle.' '.$buttons_class.'" title="'.$sellicon_tooltip.'">'.$sellicon.'</a>';
        echo '<span class="'.$separator_class.'">'.$separator."</span>";
        echo '<a href="'.$displaylink.'" class="'.$displaybuttonstyle.' '.$buttons_class.'" title="'.$displayicon_tooltip.'">'.$displayicon.'</a>';
    }
}

if ($os_balance->showbuylink || $os_balance->showselllink || $os_balance->showdisplaylink) {echo '</div>';}

// BALANCE ON BOTTOM
if ($os_balance->showbalance && $os_balance->balanceposition == "onbottom")
{
    echo '<div class="spacer"></div>';

    if ($os_balance->balanceintable)
    {
        echo '<table class="table table-striped table-condensed table-hover">';
        echo '<tbody><tr><td>';
    }

    if ($os_balance->showbalanceword)
    {
        
        echo '<span class="mod_opensim_balance_balanceword">'.JText::_('MOD_OPENSIM_BALANCE_BALANCEWORD').': </span>';
        $currency_class .= " pull-right";
    }

    if (!$os_balance->showbalanceword) {echo '<center>';}
        echo '<span class="mod_opensim_balance_currency '.$currency_class.'">';
    if ($os_balance->showcurrency && $os_balance->currencyposition == "before")
        echo '<span class="mod_opensim_balance_currency"> '.$currency.' </span>';
    echo '<span class="mod_opensim_balance_value"> '.$userbalance.'</span>';
    if ($os_balance->showcurrency && $os_balance->currencyposition == "after")
        echo '<span class="mod_opensim_balance_currency"> '.$currency.'</span>';
    echo '</span>';
    if (!$os_balance->showbalanceword) echo '</center>';;
    if ($os_balance->showbalanceword) echo '<div class="clearfix"></div>';
    if ($os_balance->balanceintable) echo '</td></tr></tbody></table>';
}

echo '</div>';
