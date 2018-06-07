<?php
/*
 * @module OpenSim Events
 * @copyright Copyright (C) 2018 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

if (!is_array($events) || count($events) == 0) {
	return null;
}

echo "<".$moduleTag ." class='mod_opensim_events_table table-responsive '>\n";

if (is_array($events) && count($events) > 0)
{
	JHTML::_('bootstrap.tooltip');

	if ($params->get('showextratitle') == 1)
    {
        echo "<div class='jmoddiv jmodinside ".$moduleclass_sfx."'>\n";
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
                <!--TODO<img src='<?php // echo $icons[$event['eventflags']]; ?>' />-->
                <?php echo $icons[$event['eventflags']]; ?>
                <?php endif; ?>
            </td>
            <?php endif; ?>

            <?php
            $tooltip  = JText::_('MOD_OPENSIM_EVENTS_CATEGORY').": ".$event['categoryname']."<br />\n";
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

            $link = "";
            if ($params->get('locaslink') == 1)
            {
                $link = "secondlife://".$event['simname']."/".$event['landingpoint'];
            }
            ?>

            <td>
                <?php echo JHTML::tooltip($tooltip,JText::_('MOD_OPENSIM_EVENTS_DESCRIPTION'), '', $event['name'], $link); ?>
            </td>

            <?php if ($params->get('showtpbtn') == 1): ?>
            <td class="text-right">
                <?php if ($params->get('tplocal') == 1): ?>
                <a class="tpbtn btn-tplocal " href="secondlife://<?php echo $event['simname']."/".$event['landingpoint']; ?>">
                    <?php echo JText::_('MOD_OPENSIM_EVENTS_TPLOCAL'); ?>
                </a>
                <?php endif; ?>
                <?php if ($params->get('tphg') == 1): ?>
                <a class="tpbtn btn-tphg" href="secondlife://<?php echo $robustHost.":".$robustPort."/".$event['simname']."/".$event['landingpoint']; ?>">
                    <?php echo JText::_('MOD_OPENSIM_EVENTS_TPHG'); ?>
                </a>
                <?php endif; ?>
                <?php if ($params->get('tphgv3') == 1): ?>
                <a class="tpbtn btn-tphgv3" href="secondlife://http|!!<?php echo $robustHost."|".$robustPort."+".$event['simname']; ?>">
                    <?php echo JText::_('MOD_OPENSIM_EVENTS_TPHGV3'); ?>
                </a>
                <?php endif; ?>
                <?php if ($params->get('tphop') == 1): ?>
                <a class="tpbtn btn-tphop" href="hop://<?php echo $robustHost.":".$robustPort."/".$event['simname']."/".$event['landingpoint']; ?>">
                    <?php echo JText::_('MOD_OPENSIM_EVENTS_TPHOP'); ?>
                </a>
                <?php endif; ?>
                <?php if ($params->get('tplocal') == 0 && $params->get('tphg') == 0 && $params->get('tphgv3') == 0 && $params->get('tphop') == 0): ?>
                    <span class="text-warning"><?php echo JText::_('MOD_OPENSIM_EVENTS_TPMISSING'); ?></span>
                <?php endif; ?>
            </td>
            <?php endif; ?>

        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php if ($params->get('showextratitle') == 1) {echo "</div>";} ?>

<?php } 
else {echo JText::_('MOD_OPENSIM_EVENTS_NOFOUND');}
echo "</".$moduleTag .">"; 
?>
