<?php
/**
 * @module      OpenSim LoginURI (mod_opensim_loginuri)
 * @copyright   Copyright (C) djphil 2016, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 * @creative    CC-BY-NC-SA 4.0
**/

// no direct access
defined('_JEXEC') or die('Direct Access to this location is not allowed.');
?>

<div class='jOpenSim_loginuri'>
	<?php if ($params->get('loginuri')): ?>
        <?php if ($params->get('loginurisubtitle')): ?>
            <div class="loginuri">
                <p class="text-left"><strong><?php echo JText::_('MOD_OPENSIM_LOGINURI_FIELD_URI_TEXT_LABEL'); ?>:</strong></p>
            </div>
        <?php endif; ?>
        <div class="loginuri">
            <?php $url = getLoginURI($params->get('domainname'), $params->get('domainport'), $params->get('loginurissl')); ?>
            <?php if ($params->get('loginuriclick')): ?>
                <p class="text-left"><a target="_blank" href="<?php echo $url; ?>"><?php echo $url; ?></a></p>
            <?php else: ?>
                <p class="text-left"><?php echo $url; ?></p>
            <?php endif; ?>
        </div>
	<?php endif; ?>

	<?php if ($params->get('hgloginuri')): ?>
        <?php if ($params->get('hgloginurisubtitle')): ?>
            <div class="hgloginuri">
                <p class="text-left"><strong><?php echo JText::_('MOD_OPENSIM_LOGINURI_FIELD_HGURI_TEXT_LABEL'); ?>:</strong></p>
            </div>
        <?php endif; ?>
        <div class="hgloginuri">
            <?php $url = getLoginURI($params->get('hgdomainname'), $params->get('hgdomainport'), $params->get('hgloginurissl')); ?>
            <?php if ($params->get('hgloginuriclick')): ?>
                <p class="text-left"><a target="_blank" href="<?php echo $url; ?>"><?php echo $url; ?></a></p>
            <?php else: ?>
                <p class="text-left"><?php echo $url; ?></p>
            <?php endif; ?>
        </div>
	<?php endif; ?>
</div>
