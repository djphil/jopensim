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

<div class='jOpenSim_gridinfos table-responsive'>
    <table class="table table-striped table-condensed table-hover">
    <tbody>
	<?php if ($params->get('simulator')): ?>
        <tr class="simulator">
            <td class='text-left'><?php echo JText::_('MOD_OPENSIM_GRIDINFOS_FIELD_SIMULATOR'); ?>:</td>
            <td class='text-right'><?php echo getSimulatorName($params->get('simulatortext')); ?></td>
        </tr>
	<?php endif; ?>

	<?php if ($params->get('version')): ?>
        <tr class="version">
            <td class='text-left'><?php echo JText::_('MOD_OPENSIM_GRIDINFOS_FIELD_VERSION'); ?>:</td>
            <td class='text-right'><span class="label label-info"><?php echo $params->get('versiontext'); ?></span></td>
        </tr>
	<?php endif; ?>

	<?php if ($params->get('mode')): ?>
        <tr class="mode">
            <td class='text-left'><?php echo JText::_('MOD_OPENSIM_GRIDINFOS_FIELD_MODE'); ?>:</td>
            <td class='text-right'><?php echo getSimulatorMode($params->get('modetext')); ?></td>
        </tr>
	<?php endif; ?>

	<?php if ($params->get('access')): ?>
        <tr class="access">
            <td class='text-left'><?php echo JText::_('MOD_OPENSIM_GRIDINFOS_FIELD_ACCESS'); ?>:</td>
            <td class='text-right'><?php echo getSimulatorAccess($params->get('accesstext')); ?></td>
        </tr>
	<?php endif; ?>

	<?php if ($params->get('rating')): ?>
        <tr class="rating">
            <td class='text-left'><?php echo JText::_('MOD_OPENSIM_GRIDINFOS_FIELD_RATING'); ?>:</td>
            <td class='text-right'><?php echo getSimulatorRating($params->get('ratingtext')); ?></td>
        </tr>
	<?php endif; ?>
 
	<?php if ($params->get('hypergrid')): ?>
        <tr class="hypergrid">
            <td class='text-left'><?php echo JText::_('MOD_OPENSIM_GRIDINFOS_FIELD_HYPERGRID'); ?>:</td>
            <td class='text-right'><?php echo getSimulatorHypergrid($params->get('hypergridtext')); ?></td>
        </tr>
	<?php endif; ?>

	<?php if ($params->get('voice')): ?>
        <tr class="voice">
            <td class='text-left'><?php echo JText::_('MOD_OPENSIM_GRIDINFOS_FIELD_VOICE'); ?>:</td>
            <td class='text-right'><?php echo getSimulatorVoice($params->get('voicetext')); ?></td>
        </tr>
	<?php endif; ?>

	<?php if ($params->get('money')): ?>
        <tr class="money">
            <td class='text-left'><?php echo JText::_('MOD_OPENSIM_GRIDINFOS_FIELD_MONEY'); ?>:</td>
            <td class='text-right'><?php echo getSimulatorMoney($params->get('moneytext')); ?></td>
        </tr>
	<?php endif; ?>

	<?php if ($params->get('physics')): ?>
        <tr class="physics">
            <td class='text-left'><?php echo JText::_('MOD_OPENSIM_GRIDINFOS_FIELD_PHYSICS'); ?>:</td>
            <td class='text-right'><?php echo getSimulatorPhysicsEngine($params->get('physicstext')); ?></td>
        </tr>
	<?php endif; ?>

	<?php if ($params->get('scripts')): ?>
        <tr class="scripts">
            <td class='text-left'><?php echo JText::_('MOD_OPENSIM_GRIDINFOS_FIELD_SCRIPTS'); ?>:</td>
            <td class='text-right'><?php echo getSimulatorScriptsEngine($params->get('scriptstext')); ?></td>
        </tr>
	<?php endif; ?>
    </tbody>
    </table>
</div>
