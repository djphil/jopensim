<?php
/**
 * @module      OpenSim Gridinfos (mod_opensim_gridinfos)
 * @copyright   Copyright (C) djphil 2016, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 * @creative    CC-BY-NC-SA 4.0
**/

// no direct access
defined('_JEXEC') or die('Direct Access to this location is not allowed.');
?>

<div class='jOpenSim_gridsinfos'>
	<?php if ($params->get('simulator')): ?>
    <div class="simulator">
        <p class="text-left"><?php echo JText::_('MOD_OPENSIM_GRIDINFOS_FIELD_SIMULATOR'); ?>: 
            <span class="pull-right"><?php echo getSimulatorName($params->get('simulatortext')); ?></span>
        </p>
    </div>
	<?php endif; ?>

	<?php if ($params->get('version')): ?>
    <div class="version">
        <p class="text-left"><?php echo JText::_('MOD_OPENSIM_GRIDINFOS_FIELD_VERSION'); ?>: 
            <span class="label label-info pull-right"><?php echo $params->get('versiontext'); ?></span>
        </p>
    </div>
	<?php endif; ?>

	<?php if ($params->get('mode')): ?>
    <div class="gridmode">
        <p class="text-left"><?php echo JText::_('MOD_OPENSIM_GRIDINFOS_FIELD_MODE'); ?>: 
            <span class="pull-right"><?php echo getSimulatorMode($params->get('modetext')); ?></span>
        </p>
    </div>
	<?php endif; ?>

	<?php if ($params->get('access')): ?>
    <div class="access">
        <p class="text-left"><?php echo JText::_('MOD_OPENSIM_GRIDINFOS_FIELD_ACCESS'); ?>: 
            <span class="pull-right"><?php echo getSimulatorAccess($params->get('accesstext')); ?></span>
        </p>
    </div>
	<?php endif; ?>

	<?php if ($params->get('rating')): ?>
    <div class="rating">
        <p class="text-left"><?php echo JText::_('MOD_OPENSIM_GRIDINFOS_FIELD_RATING'); ?>: 
            <span class="pull-right"><?php echo getSimulatorAccess($params->get('ratingtext')); ?></span>
        </p>
    </div>
	<?php endif; ?>

	<?php if ($params->get('hypergrid')): ?>
    <div class="hypergrid">
        <p class="text-left"><?php echo JText::_('MOD_OPENSIM_GRIDINFOS_FIELD_HYPERGRID'); ?>: 
            <span class="pull-right"><?php echo getSimulatorHypergrid($params->get('hypergridtext')); ?></span>
        </p>
    </div>
	<?php endif; ?>

	<?php if ($params->get('voice')): ?>
    <div class="voice">
        <p class="text-left"><?php echo JText::_('MOD_OPENSIM_GRIDINFOS_FIELD_VOICE'); ?>: 
            <span class="pull-right"><?php echo getSimulatorVoice($params->get('voicetext')); ?></span>
        </p>
    </div>
	<?php endif; ?>

	<?php if ($params->get('money')): ?>
    <div class="money">
        <p class="text-left"><?php echo JText::_('MOD_OPENSIM_GRIDINFOS_FIELD_MONEY'); ?>: 
            <span class="pull-right"><?php echo getSimulatorMoney($params->get('moneytext')); ?></span>
        </p>
    </div>
	<?php endif; ?>

	<?php if ($params->get('physics')): ?>
    <div class="physics">
        <p class="text-left"><?php echo JText::_('MOD_OPENSIM_GRIDINFOS_FIELD_PHYSICS'); ?>: 
            <span class="pull-right"><?php echo getSimulatorPhysicsEngine($params->get('physicstext')); ?></span>
        </p>
    </div>
	<?php endif; ?>

    <?php if ($params->get('scripts')): ?>
    <div class="scripts">
        <p class="text-left"><?php echo JText::_('MOD_OPENSIM_GRIDINFOS_FIELD_SCRIPTS'); ?>: 
            <span class="pull-right"><?php echo getSimulatorScriptsEngine($params->get('scriptstext')); ?></span>
        </p>
    </div>
    <?php endif; ?>
</div>
