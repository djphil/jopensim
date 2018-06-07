<?php
/*
 * @module OpenSim Regions
 * @copyright Copyright (C) 2018 FoTo50 http://www.jopensim.com
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// no direct access
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

//echo "<pre>\n";
//print_r($regions);
//echo "</pre>\n";



?>

<?php if (is_array($regions) && count($regions) > 0): ?>
<div class='jOpenSim_regions'<?php if($params->get('maxheight',0) > 0) echo ' style="max-height:'.$params->get('maxheight').'px; overflow-y:auto; overflow-x:hidden; padding-right:20px;"'; ?>>
    <table class="<?php echo $tableclass; ?>">
    <thead>
    <tr>
        <th>
            <span class="label label-default">
                <?php echo JText::_('MOD_OPENSIM_REGIONS_NAME'); ?>:
            <span>
        </th>
        <th><span class="label label-danger">Loc X:<span></th>
        <th><span class="label label-success">Loc Y:<span></th>
    </tr>
    </thead>

    <tbody>
    <?php foreach($regions AS $key => $region): ?>
    <tr>
        <td>
            <a href="secondlife://<?php echo rawurlencode($region['regionName']); ?>" title="<?php echo $region['regionName']; ?>">
                <?php echo $region['displayName']; ?>
            </a>
        </td>
        <td><?php echo round($region['posX']); ?></td>
        <td><?php echo round($region['posY']); ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
    </table>
</div>
<?php endif; ?>