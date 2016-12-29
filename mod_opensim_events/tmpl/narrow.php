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
	JHTML::_('bootstrap.tooltip');

	if ($params->get('showextratitle') == 1)
    {
        echo "<div class='well jmoddiv jmodinside ".$moduleclass_sfx."'>\n";
        echo "<".$params->get('header_tag').">".$module->title."</".$params->get('header_tag').">\n";
    }
?>

<table class="table table-striped table-condensed table-hover">
<tbody>
    <?php foreach($events AS $event): ?>
    <?php if ($params->get('showmature') == 0 && $event['eventflags'] > 0) continue; ?>
    <tr>
    <?php if($params->get('showeventinprogress') == 1 || ($params->get('showmature') == 1 && $params->get('showeventicon') == 1)): ?>
        <td>
            <?php $progressspacer = ($params->get('showmature') == 1 && $params->get('showeventicon') == 1) ? "":""; ?>
            <?php echo ($event['inprogress'] == 1) ? "<span class='icon-lightning' style='color:".$params->get('progressColor')."' title='".JText::_('MOD_OPENSIM_EVENTS_ISINPROGRESS')."'></span>":$progressspacer; ?>
            
            <?php if ($params->get('showmature') == 1 && $params->get('showeventicon') == 1): ?>
            <!--<img src='<?php // echo $icons[$event['eventflags']]; ?>' />-->
            <?php echo $icons[$event['eventflags']]; ?>
            <?php endif; ?>
        </td>
    <?php endif; ?>

    <?php
    $tooltip = JText::_('MOD_OPENSIM_EVENTS_CATEGORY').": ".$event['categoryname']."<br />\n";
    $tooltip .= JText::_('MOD_OPENSIM_EVENTS_HOSTEDBY').": ".$event['hostedby']."<br />\n";
    $tooltip .= JText::_('MOD_OPENSIM_EVENTS_DATE').": ".$event['userDate']."<br />\n";
    $tooltip .= JText::_('MOD_OPENSIM_EVENTS_TIME').": ".$event['userTime']."<br />\n";
    $tooltip .= JText::_('MOD_OPENSIM_EVENTS_JTIMEZONE').": ".$os_events->usertimezone."<br />\n";
    $tooltip .= JText::_('MOD_OPENSIM_EVENTS_DURATION').": ".$durations[$event['duration']]."<br />\n";
    
    if ($params->get('showdesc') == 1)
    {
        $tooltip .= JText::_('MOD_OPENSIM_EVENTS_DESCRIPTION').": ";
        
        if ($params->get('truncatedesc') > 0 && strlen($event['description']) > $params->get('truncatedesc'))
        {
            $tooltip .= nl2br(substr($event['description'], 0, $params->get('truncatedesc')));
        } 
        
        else
        {
            $tooltip .= $event['description'];
        }
    }
    $link = "secondlife://".$event['simname']."/".$event['landingpoint'];
    ?>
        <td>
            <?php echo JHTML::tooltip($tooltip,JText::_('MOD_OPENSIM_EVENTS_DESCRIPTION'), '', $event['name'], $link); ?>
        </td>
    </tr>
    <?php endforeach; ?>
</tbody>
</table>

<?php if ($params->get('showextratitle') == 1) {echo "</div>";} ?>
<?php echo "</".$moduleTag .">"; } ?>
