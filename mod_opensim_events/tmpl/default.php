<?php
/*
 * @module OpenSim Events
 * @copyright Copyright (C) 2016 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

echo "<".$moduleTag ." class='mod_opensim_events_table table-responsive '>\n";

if (is_array($events) && count($events) > 0)
{
    if ($params->get('showextratitle') == 1)
    {
        echo "<div class='well jmoddiv jmodinside ".$moduleclass_sfx."'>\n";
        echo "<".$headerTag." class='".$headerClass."'>".$module->title."</".$headerTag.">\n";
    }
?>

<p class='label label-default'>
    <?php echo ($userid) ? JText::sprintf('MOD_OPENSIM_EVENTS_DATEDESC', $os_events->usertimezone):JText::sprintf('MOD_OPENSIM_EVENTS_DATEDESC_GUESTS', $os_events->usertimezone); ?>
</p>

<!--table-condensed-->
<table class="table table-striped table-condensed table-hover">
<thead>
    <tr>
    <?php if ($params->get('showeventinprogress') == 1 || ($params->get('showmature') == 1 && $params->get('showeventicon') == 1)): ?>
        <th>&nbsp;</th>
    <?php endif; ?>
        <th><?php echo JText::_('MOD_OPENSIM_EVENTS_EVENTNAME'); ?></th>
        <th><?php echo JText::_('MOD_OPENSIM_EVENTS_CATEGORY'); ?></th>
        <th><?php echo JText::_('MOD_OPENSIM_EVENTS_HOSTEDBY'); ?></th>
        <th><?php echo JText::_('MOD_OPENSIM_EVENTS_LOCATION'); ?></th>
        <th><?php echo JText::_('MOD_OPENSIM_EVENTS_DATE'); ?></th>
        <th><?php echo JText::_('MOD_OPENSIM_EVENTS_TIME'); ?></th>
        <th><?php echo JText::_('MOD_OPENSIM_EVENTS_DURATION'); ?></th>
    </tr>
</thead>
<tbody>
    <?php foreach($events AS $event): ?>
    <?php if($params->get('showmature') == 0 && $event['eventflags'] > 0) continue; ?>
    <tr>
        <?php if($params->get('showeventinprogress') == 1 || ($params->get('showmature') == 1 && $params->get('showeventicon') == 1)): ?>
        <td>
            <?php echo ($event['inprogress'] == 1) ? "<span class='icon-lightning' style='color:".$params->get('progressColor')."' title='".JText::_('MOD_OPENSIM_EVENTS_ISINPROGRESS')."'></span>":"&nbsp;"; ?>
            <?php if($params->get('showmature') == 1 && $params->get('showeventicon') == 1): ?>
            <!--<img src='<?php // echo $icons[$event['eventflags']]; ?>' />-->
            <?php echo $icons[$event['eventflags']]; ?>
            <?php endif; ?>
        </td>
        <?php endif; ?>
        <td><?php echo $event['name']; ?></td>
        <td><?php echo $event['categoryname']; ?></td>
        <td><?php echo $event['hostedby']; ?></td>
        <td><a href="secondlife://<?php echo $event['simname']."/".$event['landingpoint']; ?>"><?php echo $event['simname']; ?></a></td>
        <td><?php echo $event['userDate']; ?></td>
        <td><?php echo $event['userTime']; ?></td>
        <td><?php echo $durations[$event['duration']]; ?></td>
    </tr>
    <?php if($params->get('showdesc') == 1): ?>
    <tr>
        <td colspan='<?php echo ($params->get('showeventinprogress') == 1 || ($params->get('showmature') == 1 && $params->get('showeventicon') == 1)) ? "8":"7"; ?>' class='desccol<?php echo $moduleclass_sfx; ?>'>
        <?php echo ($params->get('truncatedesc') > 0 && strlen($event['description']) > $params->get('truncatedesc')) ? nl2br(substr($event['description'],0,$params->get('truncatedesc')))."&#x2026;":nl2br($event['description']);
        ?>
        </td>
    </tr>
    <?php endif; ?>
    <?php endforeach; ?>
</tbody>
</table>

<?php if ($params->get('showextratitle') == 1) {echo "</div>";} ?>
<?php echo "</".$moduleTag .">"; } ?>
