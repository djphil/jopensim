<?php
/**
 * @module      OpenSim Teleport (mod_opensim_teleport)
 * @copyright   Copyright (C) djphil 2017, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 * @creative    CC-BY-NC-SA 4.0
**/

// no direct access
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

function getButtonAlign($params)
{
    if ($params == 1) return "text-right";
    else if ($params == 2) return "text-center";
    return "text-left";
}

function getButtonSize($params)
{
    if ($params == 0) return "btn-default";
    else if ($params == 1) return "btn-mini";
    else if ($params == 2) return "btn-large";
    return "btn-default";
}

function getTeleportUrl($params, $a, $b, $c, $d)
{
    if ($params == 0)
    {
        $a = trim($a);
        $a = str_replace(" ", "%20", $a);
        return "secondlife://".$a."/".$b;
    }

    else if ($params == 1)
    {
        $c = trim($c);
        $c = str_replace(" ", "%20", $c);
        return "secondlife://".$a.":".$b."/".$c."/".$d;
    }

    else if ($params == 2)
    {
        $c = trim($c);
        $c = str_replace(" ", "+", $c);
        return "secondlife://http|!!".$a."|".$b."+".$c;
    }

    else if ($params == 3)
    {
        $c = trim($c);
        $c = str_replace(" ", "%20", $c);
        return "hop://".$a.":".$b."/".$c."/".$d;
    }

    return "#";
}
?>