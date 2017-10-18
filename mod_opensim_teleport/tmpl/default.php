<?php
/**
 * @module      OpenSim Teleport (mod_opensim_teleport)
 * @copyright   Copyright (C) djphil 2017, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 * @creative    CC-BY-NC-SA 4.0
**/

// no direct access
defined('_JEXEC') or die('Direct Access to this location is not allowed.');
?>

<div class='jOpenSim_teleport <?php echo getButtonAlign($params->get('tpbuttonalign')); ?>'>
    <?php if ($params->get('tplocal')): ?>
    <div class="tplocal">
        <?php $url = getTeleportUrl(0, $params->get('regionname'), $params->get('regionlocation'), 0, 0); ?>
        <a class="btn btn-info <?php echo getButtonSize($params->get('tpbuttonsize')); ?>" href="<?php echo $url; ?>">
            <?php if ($params->get('tpicon')): ?>
                <i class="<?php echo $params->get('tpicontext'); ?>"></i><?php echo JText::_('MOD_OPENSIM_TELEPORT_FIELD_TPLOCAL'); ?>
            <?php else: ?>
                <?php echo JText::_('MOD_OPENSIM_TELEPORT_FIELD_TP'); ?> <?php echo JText::_('MOD_OPENSIM_TELEPORT_FIELD_TPLOCAL'); ?>
            <?php endif; ?>
        </a>
    </div>
    <?php endif; ?>

    <?php if ($params->get('tphg')): ?>
    <div class="tphg">
        <?php $url = getTeleportUrl(1, $params->get('hgdomainname'), $params->get('hgdomainport'), $params->get('hgregionname'), $params->get('hgregionlocation')); ?>
        <a class="btn btn-primary <?php echo getButtonSize($params->get('tpbuttonsize')); ?>" href="<?php echo $url; ?>">
            <?php if ($params->get('tpicon')): ?>
                <i class="<?php echo $params->get('tpicontext'); ?>"></i><?php echo JText::_('MOD_OPENSIM_TELEPORT_FIELD_TPHG'); ?>
            <?php else: ?>
                <?php echo JText::_('MOD_OPENSIM_TELEPORT_FIELD_TP'); ?> <?php echo JText::_('MOD_OPENSIM_TELEPORT_FIELD_TPHG'); ?>
            <?php endif; ?>
        </a>
    </div>
    <?php endif; ?>

    <?php if ($params->get('tphgv3')): ?>
    <div class="tphgv3">
        <?php $url = getTeleportUrl(2, $params->get('hgdomainname'), $params->get('hgdomainport'), $params->get('hgregionname'), 0); ?>
        <a class="btn btn-warning <?php echo getButtonSize($params->get('tpbuttonsize')); ?>" href="<?php echo $url; ?>">
            <?php if ($params->get('tpicon')): ?>
                <i class="<?php echo $params->get('tpicontext'); ?>"></i><?php echo JText::_('MOD_OPENSIM_TELEPORT_FIELD_TPHGV3'); ?>
            <?php else: ?>
                <?php echo JText::_('MOD_OPENSIM_TELEPORT_FIELD_TP'); ?> <?php echo JText::_('MOD_OPENSIM_TELEPORT_FIELD_TPHGV3'); ?>
            <?php endif; ?>
        </a>
    </div>
    <?php endif; ?>

    <?php if ($params->get('tphop')): ?>
    <div class="tphop">
        <?php $url = getTeleportUrl(3, $params->get('domainname'), $params->get('domainport'), $params->get('regionname'), $params->get('regionlocation')); ?>
        <a class="btn btn-danger <?php echo getButtonSize($params->get('tpbuttonsize')); ?>" href="<?php echo $url; ?>">
            <?php if ($params->get('tpicon')): ?>
                <i class="<?php echo $params->get('tpicontext'); ?>"></i><?php echo JText::_('MOD_OPENSIM_TELEPORT_FIELD_TPHOP'); ?>
            <?php else: ?>
                <?php echo JText::_('MOD_OPENSIM_TELEPORT_FIELD_TP'); ?> <?php echo JText::_('MOD_OPENSIM_TELEPORT_FIELD_TPHOP'); ?>
            <?php endif; ?>
        </a>
    </div>
    <?php endif; ?>
</div>
