<?php
/**
 * @module      OpenSim Gridinfos (mod_opensim_gridinfos)
 * @copyright   Copyright (C) djphil 2016, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 * @creative    CC-BY-NC-SA 4.0
**/

// no direct access
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

function getSimulatorName($params)
{
    if ($params == 0) return "<span class='label label-info'>OpenSim</span>";
    else if ($params == 1) return "<span class='label label-info'>Diva</span>";
    else if ($params == 3) return "<span class='label label-info'>Moze</span>";
    else if ($params == 4) return "<span class='label label-info'>Aurora-Sim</span>";
    else if ($params == 5) return "<span class='label label-info'>WhiteCore-Sim</span>";
    return "<span class='label label-warning'>".JText::_('MOD_OPENSIM_GRIDINFOS_FIELD_UNKNOW')."</span>";
}

function getSimulatorMode($params)
{
    if ($params == 0) return "<span class='label label-info'>".JText::_('MOD_OPENSIM_GRIDINFOS_FIELD_STANDALONE')."</span>";
    else if ($params == 1) return "<span class='label label-info'>".JText::_('MOD_OPENSIM_GRIDINFOS_FIELD_GRID')."</span>";
    return "<span class='label label-warning'>".JText::_('MOD_OPENSIM_GRIDINFOS_FIELD_UNKNOW')."</span>";
}

function getSimulatorAccess($params)
{
    if ($params == 0) return "<span class='label label-success'>".JText::_('MOD_OPENSIM_GRIDINFOS_FIELD_PUBLIC')."</span>";
    else if ($params == 1) return "<span class='label label-danger'>".JText::_('MOD_OPENSIM_GRIDINFOS_FIELD_PRIVATE')."</span>";
    return "<span class='label label-warning'>".JText::_('MOD_OPENSIM_GRIDINFOS_FIELD_UNKNOW')."</span>";
}

function getSimulatorRating($params)
{
    if ($params == 0) return "<span class='label label-info'>".JText::_('MOD_OPENSIM_GRIDINFOS_FIELD_GENERAL')."</span>";
    else if ($params == 1) return "<span class='label label-warning'>".JText::_('MOD_OPENSIM_GRIDINFOS_FIELD_MATURE')."</span>";
    else if ($params == 2) return "<span class='label label-danger'>".JText::_('MOD_OPENSIM_GRIDINFOS_FIELD_ADULT')."</span>";
    return "<span class='label label-warning'>".JText::_('MOD_OPENSIM_GRIDINFOS_FIELD_UNKNOW')."</span>";
}

function getSimulatorHypergrid($params)
{
    if ($params == 0) return "<span class='label label-danger'>".JText::_('MOD_OPENSIM_GRIDINFOS_FIELD_DISABLE')."</span>";
    else if ($params == 1) return "<span class='label label-success'>".JText::_('MOD_OPENSIM_GRIDINFOS_FIELD_ENABLE')."</span>";
    return "<span class='label label-warning'>".JText::_('MOD_OPENSIM_GRIDINFOS_FIELD_UNKNOW')."</span>";
}

function getSimulatorVoice($params)
{
    if ($params == 0) return "<span class='label label-danger'>".JText::_('MOD_OPENSIM_GRIDINFOS_FIELD_DISABLE')."</span>";
    else if ($params == 1) return "<span class='label label-success'>".JText::_('MOD_OPENSIM_GRIDINFOS_FIELD_ENABLE')."</span>";
    return "<span class='label label-warning'>".JText::_('MOD_OPENSIM_GRIDINFOS_FIELD_UNKNOW')."</span>";
}

function getSimulatorMoney($params)
{
    if ($params == 0) return "<span class='label label-danger'>".JText::_('MOD_OPENSIM_GRIDINFOS_FIELD_DISABLE')."</span>";
    else if ($params == 1) return "<span class='label label-success'>".JText::_('MOD_OPENSIM_GRIDINFOS_FIELD_ENABLE')."</span>";
    return "<span class='label label-warning'>".JText::_('MOD_OPENSIM_GRIDINFOS_FIELD_UNKNOW')."</span>";
}

function getSimulatorPhysicsEngine($params)
{
    if ($params == 0) return "<span class='label label-info'>Ode</span>";
    else if ($params == 1) return "<span class='label label-info'>ubOde</span>";
    else if ($params == 2) return "<span class='label label-info'>Bulletsim</span>";
    else if ($params == 3) return "<span class='label label-info'>PhysX</span>";
    else if ($params == 4) return "<span class='label label-info'>Ninja</span>";
    else if ($params == 5) return "<span class='label label-info'>Custom</span>";
    return "<span class='label label-warning'>".JText::_('MOD_OPENSIM_GRIDINFOS_FIELD_UNKNOW')."</span>";
}

function getSimulatorScriptsEngine($params)
{
    if ($params == 0) return "<span class='label label-info'>XEngine</span>";
    else if ($params == 1) return "<span class='label label-info'>Custom</span>";
    return "<span class='label label-warning'>".JText::_('MOD_OPENSIM_GRIDINFOS_FIELD_UNKNOW')."</span>";
}
?>
