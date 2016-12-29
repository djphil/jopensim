<?php
/*
 * @module OpenSim Gridstatus
 * @copyright Copyright (C) 2015 FoTo50 http://www.jopensim.com
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// no direct access
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

if ($params->get('stylebold')) $stylebold = "text-bold";
?>

<?php if ($values['gridboxlines'] > 0): ?>
    <div class='jOpenSim_gridstatus'>
        <table class="table table-striped table-condensed table-hover">
        <tbody>
            <?php if ($values['gridboxlines'] & 1): ?>
            <tr>
                <td class='text-left'><?php echo JText::_('MOD_OPENSIM_GRIDSTATUS_FRONT_GRIDSTATUS'); ?>:</td>
                <td class='text-right <?php echo $stylebold; ?>'><?php echo $values['statusmsg']; ?></td>
            </tr>
            <?php endif; ?>

            <?php if ($values['status'] == "online"): ?>
            <?php if ($values['gridboxlines'] & 2): ?>
            <tr>
                <td class='text-left'><?php echo JText::_('MOD_OPENSIM_GRIDSTATUS_FRONT_REGIONS'); ?>:</td>
                <td class='text-right <?php echo $stylebold; ?>'><?php echo $values['totalregions']; ?></td>
            </tr>
            <?php endif; ?>

            <?php if ($values['gridboxlines'] & 4): ?>
            <tr>
                <td class='text-left'><?php echo JText::sprintf('MOD_OPENSIM_GRIDSTATUS_FRONT_LASTXDAYS',$values['days']); ?>:</td>
                <td class='text-right <?php echo $stylebold; ?>'><?php echo $values['lastonline']; ?></td>
            </tr>
            <?php endif; ?>

            <?php if ($values['gridboxlines'] & 16): ?>
            <tr>
                <td class='text-left'><?php echo JText::_('MOD_OPENSIM_GRIDSTATUS_FRONT_TOTALUSERS'); ?>:</td>
                <td class='text-right <?php echo $stylebold; ?>'><?php echo $values['totalusers']; ?></td>
            </tr>
            <?php endif; ?>

            <?php if ($values['gridboxlines'] & 8): ?>
            <tr>
                <td class='text-left'><?php echo JText::_('MOD_OPENSIM_GRIDSTATUS_FRONT_ONLINENOW'); ?>:</td>
                <td class='text-right <?php echo $stylebold; ?>'><?php echo $values['online']; ?></td>
            </tr>
            <?php endif; ?>
            <?php endif; ?>
        </tbody>
        </table>
    </div>
<?php endif; ?>
