<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2018 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

JHTML::_('bootstrap.tooltip');
$rowclass = "even";

// NEW: Add event maturity icons
// JOPENSIM_EVENT_MATURITY

$icon = array();
$icon[0] = '<span class="label label-default">G</span>'; 
$icon[1] = '<span class="label label-info">M</span>';    
$icon[2] = '<span class="label label-danger">A</span>';
// TODO: add choice (icon or img)
// $basepath = JURI::base( true ).'/modules/mod_opensim_events/assets/';
// $basepath."icon_event.png";
// $basepath."icon_event_mature.png";
// $basepath."icon_event_adult.png";
?>

<h1><?php echo JText::_('JOPENSIM_EVENTLIST'); ?></h1>
<p class="label label-info"><?php echo JText::sprintf('JOPENSIM_EVENTS_TIMEZONEDISPLAY', $this->usertimezone); ?></p>

<table class="table table-striped table-hover">
    <tr>
        <th><?php echo JText::_('JOPENSIM_EVENT_NAME'); ?></th>
        <th><?php echo JText::_('JOPENSIM_EVENTCATEGORY'); ?></th>
        <th><?php echo JText::_('JOPENSIM_EVENTTYPE'); ?></th>
        <th><?php echo JText::_('JOPENSIM_EVENT_RUNBY'); ?></th>
        <th><?php echo JText::_('JOPENSIM_EVENT_LOCATION'); ?></th>
        <th><?php echo JText::_('JOPENSIM_EVENT_DATE'); ?></th>
        <th><?php echo JText::_('JOPENSIM_EVENT_TIME'); ?></th>
        <th><?php echo JText::_('JOPENSIM_EVENT_DURATION'); ?></th>
        <?php if($event['editflag'] == 1): ?>
        <th>&nbsp;</th>
        <?php endif; ?>
    </tr>
    <?php if (is_array($this->eventlist['events']) && count($this->eventlist['events']) > 0): ?>
    <?php
    foreach($this->eventlist['events'] AS $event) {
        if ($rowclass == "even") $rowclass = "odd";
        else $rowclass = "even";
    ?>
    <tr>
        <td><?php echo JHTML::tooltip($event['description'], JText::_('JOPENSIM_EVENT_DESCRIPTION'). ":", '', $event['name']); ?></td>
        <td><?php echo $event['categoryname']; ?></td>
        <td><?php echo $icon[$event['eventflags']]; ?></td>
        <td><?php echo $event['ownername']; ?></td>
        <td><?php echo ($event['surl']) ? "<a href='secondlife://".$event['surl']."'>".$event['simname']."</a>":$event['simname']; ?></td>
        <td><?php echo $event['userdate']; ?></td>
        <td><?php echo $event['usertime']; ?></td>
        <td><?php echo $this->duration[$event['duration']]; ?></td>

        <?php if($event['editflag'] == 1): ?>
        <td>
            <a class='btn btn-danger btn-small pull-right' 
               href='<?php echo JRoute::_('&option=com_opensim&view=events&layout=eventlist&task=deleteevent&eventid='.$event['eventid']); ?>' 
               onClick='return confirm("<?php echo JText::_('JOPENSIM_EVENT_DELETE_SURE'); ?>");' 
               alt='<?php echo JText::_('JOPENSIM_EVENT_DELETE'); ?>' 
               title='<?php echo JText::_('JOPENSIM_EVENT_DELETE'); ?>'><strong>X</strong>
            </a>
        </td>
        <?php endif; ?>
    </tr>
    <?php } ?>

    <?php else: ?>
    <tr>
        <td colspan='8'><?php echo JText::_('JOPENSIM_EMPTYEVENTLIST'); ?></td>
    </tr>
    <?php endif; ?>
</table>