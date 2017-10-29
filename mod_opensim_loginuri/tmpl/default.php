<?php
/**
 * @module      OpenSim LoginURI (mod_opensim_loginuri)
 * @copyright   Copyright (C) djphil 2017, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 * @creative    CC-BY-NC-SA 4.0
**/

// no direct access
defined('_JEXEC') or die('Direct Access to this location is not allowed.');
?>

<div class='jOpenSim_loginuri table-responsive'>
    <table class="table table-striped table-condensed table-hover">
    <tbody>
	<?php if ($params->get('loginuri')): ?>
        <?php if ($params->get('loginurisubtitle')): ?>
            <tr class="loginuri">
                <td class='text-left'><strong><?php echo JText::_('MOD_OPENSIM_LOGINURI_FIELD_URI_TEXT_LABEL'); ?>:</strong></td>
            </tr>
        <?php endif; ?>
        <tr class="loginuri">
            <?php $url = getLoginURI($params->get('domainname'), $params->get('domainport'), $params->get('loginurissl')); ?>
            <?php if ($params->get('loginuriclick')): ?>
                <td class='text-left'><a target="_blank" href="<?php echo $url; ?>"><?php echo $url; ?></a></td>
            <?php else: ?>
                <td class='text-left'><?php echo $url; ?></td>
            <?php endif; ?>
        </tr>
	<?php endif; ?>

	<?php if ($params->get('hgloginuri')): ?>
        <?php if ($params->get('hgloginurisubtitle')): ?>
            <tr class="hgloginuri">
                <td class='text-left'><strong><?php echo JText::_('MOD_OPENSIM_LOGINURI_FIELD_HGURI_TEXT_LABEL'); ?>:</strong></td>
            </tr>
        <?php endif; ?>
        <tr class="hgloginuri">
            <?php $url = getLoginURI($params->get('hgdomainname'), $params->get('hgdomainport'), $params->get('hgloginurissl')); ?>
            <?php if ($params->get('hgloginuriclick')): ?>
                <td class='text-left'><a target="_blank" href="<?php echo $url; ?>"><?php echo $url; ?></a></td>
            <?php else: ?>
                <td class='text-left'><?php echo $url; ?></td>
            <?php endif; ?>
        </tr>
	<?php endif; ?>
    </tbody>
    </table>
</div>
