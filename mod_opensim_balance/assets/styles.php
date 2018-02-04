<?php
/**
 * @module      OpenSim balance (mod_opensim_balance)
 * @copyright   Copyright (C) djphil 2017, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 * @creative    CC-BY-NC-SA 4.0
**/

// no direct access
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

$currency_class = "";
$buttons_class = "";
$separator_class = "";
$separator = "";
$buybuttonstyle = "";
$buyicon_class = "";
$buyicon = "";
$sellbuttonstyle = "";
$sellicon_class = "";
$sellicon = "";
$displaybuttonstyle = "";
$displayicon_class = "";
$displayicon = "";

// CURRENCY STYLES
if ($os_balance->currencytype == 1) {
         if ($os_balance->currencybadgestyle == 1) $currency_class = "badge badge-primary";
    else if ($os_balance->currencybadgestyle == 2) $currency_class = "badge badge-secondary";
    else if ($os_balance->currencybadgestyle == 3) $currency_class = "badge badge-info";
    else if ($os_balance->currencybadgestyle == 4) $currency_class = "badge badge-success";
    else if ($os_balance->currencybadgestyle == 5) $currency_class = "badge badge-warning";
    else if ($os_balance->currencybadgestyle == 6) $currency_class = "badge badge-danger";
    else if ($os_balance->currencybadgestyle == 7) $currency_class = "badge badge-inverse";
    else $currency_class = "badge badge-default";
}

else if ($os_balance->currencytype == 2) {
         if ($os_balance->currencylabelstyle == 1) $currency_class = "label label-primary";
    else if ($os_balance->currencylabelstyle == 2) $currency_class = "label label-secondary";
    else if ($os_balance->currencylabelstyle == 3) $currency_class = "label label-info";
    else if ($os_balance->currencylabelstyle == 4) $currency_class = "label label-success";
    else if ($os_balance->currencylabelstyle == 5) $currency_class = "label label-warning";
    else if ($os_balance->currencylabelstyle == 6) $currency_class = "label label-danger";
    else if ($os_balance->currencylabelstyle == 7) $currency_class = "label label-inverse";
    else $currency_class = "label label-default";
}
                    
else {
         if ($os_balance->currencytexttype == 1) $currency_class = "text-primary";
    else if ($os_balance->currencytexttype == 2) $currency_class = "text-secondary";
    else if ($os_balance->currencytexttype == 3) $currency_class = "text-info";
    else if ($os_balance->currencytexttype == 4) $currency_class = "text-success";
    else if ($os_balance->currencytexttype == 5) $currency_class = "text-warning";
    else if ($os_balance->currencytexttype == 6) $currency_class = "text-danger";
    else if ($os_balance->currencytexttype == 7) $currency_class = "text-muted";
    else if ($os_balance->currencytexttype == 8) $currency_class = "text-inverse";
    else $currency_class = "";
}

if ($os_balance->currencytextbold) $currency_class .= " text-bold";

// LINK TYPE
if ($os_balance->linktype == 1) $buttons_class = "btn btn-link";
else if ($os_balance->linktype == 2) $buttons_class = "btn btn-default";
else if ($os_balance->linktype == 3) $buttons_class = "disabled";

// SEPARATORS
if ($os_balance->showseparator) {
    if ($os_balance->separator) $separator = " ".$os_balance->separator." ";
    else $separator = " ";
    if ($os_balance->separatorbold) $separator_class = " text-bold";
    else $separator_class = "";
}
else {$separator = " "; $separator_class = "";}

// BUTTON STYLES
if ($os_balance->linktype == 2) {
         if ($os_balance->buybuttonstyle == 1) $buybuttonstyle = "btn btn-primary";
    else if ($os_balance->buybuttonstyle == 2) $buybuttonstyle = "btn btn-secondary";
    else if ($os_balance->buybuttonstyle == 3) $buybuttonstyle = "btn btn-info";
    else if ($os_balance->buybuttonstyle == 4) $buybuttonstyle = "btn btn-success";
    else if ($os_balance->buybuttonstyle == 5) $buybuttonstyle = "btn btn-warning";
    else if ($os_balance->buybuttonstyle == 6) $buybuttonstyle = "btn btn-danger";
    else if ($os_balance->buybuttonstyle == 7) $buybuttonstyle = "btn btn-inverse";
    else $buybuttonstyle .= "btn btn-default";

         if ($os_balance->sellbuttonstyle == 1) $sellbuttonstyle = "btn btn-primary";
    else if ($os_balance->sellbuttonstyle == 2) $sellbuttonstyle = "btn btn-secondary";
    else if ($os_balance->sellbuttonstyle == 3) $sellbuttonstyle = "btn btn-info";
    else if ($os_balance->sellbuttonstyle == 4) $sellbuttonstyle = "btn btn-success";
    else if ($os_balance->sellbuttonstyle == 5) $sellbuttonstyle = "btn btn-warning";
    else if ($os_balance->sellbuttonstyle == 6) $sellbuttonstyle = "btn btn-danger";
    else if ($os_balance->sellbuttonstyle == 7) $sellbuttonstyle = "btn btn-inverse";
    else $sellbuttonstyle .= "btn btn-default";

         if ($os_balance->displaybuttonstyle == 1) $displaybuttonstyle = "btn btn-primary";
    else if ($os_balance->displaybuttonstyle == 2) $displaybuttonstyle = "btn btn-secondary";
    else if ($os_balance->displaybuttonstyle == 3) $displaybuttonstyle = "btn btn-info";
    else if ($os_balance->displaybuttonstyle == 4) $displaybuttonstyle = "btn btn-success";
    else if ($os_balance->displaybuttonstyle == 5) $displaybuttonstyle = "btn btn-warning";
    else if ($os_balance->displaybuttonstyle == 6) $displaybuttonstyle = "btn btn-danger";
    else if ($os_balance->displaybuttonstyle == 7) $displaybuttonstyle = "btn btn-inverse";
    else $displaybuttonstyle .= "btn btn-default";
}

// BUTTONS SIZE
if ($os_balance->buttonsize == 1) $buttons_class .= " btn-mini btn-sm";
else if ($os_balance->buttonsize == 2) $buttons_class .= " btn-large btn-lg";

// BUTTONS TYPE
if ($os_balance->buttonblock == 1) $buttons_class .= " btn-block";

if ($os_balance->buttonsize == 1)
{
    $mod_opensim_balance_buy     = strtoupper(JText::_('MOD_OPENSIM_BALANCE_BUY'));
    $mod_opensim_balance_sell    = strtoupper(JText::_('MOD_OPENSIM_BALANCE_SELL'));
    $mod_opensim_balance_display = strtoupper(JText::_('MOD_OPENSIM_BALANCE_DISPLAY'));
}

else {
    $mod_opensim_balance_buy     = JText::_('MOD_OPENSIM_BALANCE_BUY');
    $mod_opensim_balance_sell    = JText::_('MOD_OPENSIM_BALANCE_SELL');
    $mod_opensim_balance_display = JText::_('MOD_OPENSIM_BALANCE_DISPLAY');
}

// ICONES
if ($os_balance->showicons)
{
    $buyicon_class = $os_balance->buyiconclass;
    if ($os_balance->iconposition === "after")
        $buyicon = '&nbsp;<span class="'.$buyicon_class.'"></span>';
    else $buyicon = '<span class="'.$buyicon_class.'"></span>&nbsp;';

    $sellicon_class = $os_balance->selliconclass;

    if ($os_balance->iconposition === "after")
        $sellicon = '&nbsp;<span class="'.$sellicon_class.'"></span>';
    else $sellicon = '<span class="'.$sellicon_class.'"></span>&nbsp;';

    $displayicon_class = $os_balance->displayiconclass;
    if ($os_balance->iconposition === "after")
        $displayicon = '&nbsp;<span class="'.$displayicon_class.'"></span>';
    else $displayicon = '<span class="'.$displayicon_class.'"></span>&nbsp;';
}
?>
