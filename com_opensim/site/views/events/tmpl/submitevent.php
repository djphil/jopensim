<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2018 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
?>

<!--<?php // echo $this->pageclass_sfx; ?>-->
<h1><?php echo $this->formtitle; ?></h1>

<?php if (is_array($this->landoption) && count($this->landoption) > 0) { ?>

<form class="form" name='eventform' action='index.php' method='post'>
<input type='hidden' name='option' value='com_opensim' />
<input type='hidden' name='view' value='events' />
<input type='hidden' name='task' value='<?php echo $this->formaction; ?>' />
<input type='hidden' name='uuid' value='<?php echo $this->osdata['uuid']; ?>' />
<input type='hidden' name='Itemid' value='<?php echo $this->Itemid; ?>' />

<table class="table table-striped table-hover">
<tr>
    <td><label for='eventname'><?php echo JText::_('JOPENSIM_EVENT_NAME'); ?>:</label></td>
    <td><input type='text' name='eventname' id='eventname' value='<?php echo $this->eventdata['name']; ?>' size='50' /></td>
</tr>

<tr>
    <td><label for='eventdate'><?php echo JText::_('JOPENSIM_EVENT_DATE'); ?>:</label></td>
    <td><input type='text' name='eventdate' id='eventdate' value='<?php echo $this->eventdata['eventdate']; ?>' size='15' /></td>
</tr>

<tr>
    <td><label for='eventtime'><?php echo JText::_('JOPENSIM_EVENT_TIME'); ?>:</label></td>
    <td>
        <select name='eventtime' id='eventtime'>
        <?php
        for ($i=0; $i<24;$i++) {
            $full = str_pad($i,2,"0",STR_PAD_LEFT).":00";
            $half = str_pad($i,2,"0",STR_PAD_LEFT).":30";
            if($full == $this->eventdata['eventtime']) $fullselected = " selected='selected'";
            else $fullselected = "";
            if($half == $this->eventdata['eventtime']) $halfselected = " selected='selected'";
            else $halfselected = "";
            echo "\t\t<option value='".$full."'".$fullselected.">".$full."</option>\n";
            echo "\t\t<option value='".$half."'".$halfselected.">".$half."</option>\n";
        }
        ?>
        </select>
    </td>
</tr>

<tr>
    <td><label for='eventtimezone'><?php echo JText::_('JOPENSIM_TIMEZONE'); ?>:</label></td>
    <td>
    <select name='eventtimezone' id='eventtimezone' >
<?php if(is_array($this->timezones) && count($this->timezones) > 0): ?>
<?php foreach($this->timezones AS $timezone): ?>
        <option value='<?php echo $timezone; ?>' <?php echo ($timezone == $this->osdata['timezone']) ? " selected='selected'":""; ?>><?php echo $timezone; ?></option>
<?php endforeach; ?>
<?php else: ?>
        <option value='???'><?php echo JText::_('JOPENSIM_TIMEZONE_ERROR'); ?></option>
<?php endif; ?>
    </select>
    </td>
</tr>
<tr>
    <td><label for='eventduration'><?php echo JText::_('JOPENSIM_EVENT_DURATION'); ?>:</label></td>
    <td>
    <select name='eventduration' id='eventduration' >
<?php if (is_array($this->duration) && count($this->duration) > 0): ?>
<?php foreach($this->duration AS $duration => $durationdescription): ?>
        <option value='<?php echo $duration; ?>' <?php echo ($duration == $this->eventdata['duration']) ? " selected='selected'":""; ?>><?php echo $durationdescription; ?></option>
<?php endforeach; ?>
<?php endif; ?>	
    </select>
    </td>
</tr>

<tr>
    <td><label for='eventlocation'><?php echo JText::_('JOPENSIM_EVENT_LOCATION'); ?>:</label></td>
    <td>
    <select name='eventlocation' id='eventlocation'>
<?php
if (is_array($this->landoption) && count($this->landoption) > 0) {
    foreach($this->landoption AS $land) echo "\t\t".$land;
} else {
    echo "\t<option value='?' disabled='disabled'>".JText::_('JOPENSIM_EVENTNOLANDFOUND')."</option>\n";
}
?>
    </select>
    </td>
</tr>
<tr>
    <td><label for='eventcategory'><?php echo JText::_('JOPENSIM_EVENTCATEGORY'); ?>:</label></td>
    <td>
        <select name='eventcategory' id='eventcategory'>
        <?php
        if (is_array($this->eventcategories) && count($this->eventcategories) > 0) {
            foreach($this->eventcategories AS $category => $categorydescription) {
                echo "\t\t<option value='".$category."'";
                echo ($this->eventdata['category'] == $category) ? " selected='selected'":"";
                echo ">".$categorydescription."</option>\n";
            }
        } else {
            echo "\t<option value='?' disabled='disabled'>".JText::_('JOPENSIM_EVENTNOCATEGORYFOUND')."</option>\n";
        }
        ?>
        </select>
    </td>
</tr>

<?php if ($this->currencyenabled): ?>
<tr>
    <td><label for='covercharge'><?php echo JText::_('JOPENSIM_EVENTCOVERCHARGE'); ?>:</label></td>
    <td><input type='text' name='covercharge' id='covercharge' value='<?php echo $this->eventdata['covercharge']; ?>' size='7' /></td>
</tr>
<?php endif; ?>

<tr>
    <td><label for='description'><?php echo JText::_('JOPENSIM_EVENT_DESCRIPTION'); ?>:</label></td>
    <td><textarea name='description' id='description' cols='40' rows='7'><?php echo $this->eventdata['description']; ?></textarea></td>
</tr>

<tr>
    <td><label for='eventflags'><?php echo JText::_('JOPENSIM_EVENTTYPE'); ?>:</label></td>
    <td>
        <div id="eventflags" class="form-inline">
            <label class="radio-inline" title="<?php echo JText::_('JOPENSIM_EVENTTYPE_PG_DESC'); ?>">
                <input type="radio" name="eventflags" value='0'> <span class="label label-default"><?php echo JText::_('JOPENSIM_EVENTTYPE_PG'); ?></span>
            </label> 
            <?php if($this->listmatureevents > 0): ?>
            <label class="radio-inline" title="<?php echo JText::_('JOPENSIM_EVENTTYPE_MATURE_DESC'); ?>">
                <input type="radio" name="eventflags" value='1'> <span class="label label-info"><?php echo JText::_('JOPENSIM_EVENTTYPE_MATURE'); ?></span>
            </label>
            <label class="radio-inline" title="<?php echo JText::_('JOPENSIM_EVENTTYPE_ADULT_DESC'); ?>">
                <input type="radio" name="eventflags" value='2'> <span class="label label-danger"><?php echo JText::_('JOPENSIM_EVENTTYPE_ADULT'); ?></span>
            </label>
            <?php endif; ?>
        </div>     
    </td>
</tr>
<tr>
    <td colspan='2'>
        <!-- 
            Temporary removed, need to be fixed in next release
            <span class="icon-save"></span>
        -->
        <button type='submit' class="btn btn-primary"/>
            <?php echo JText::_('JOPENSIM_SAVEEVENT'); ?>
        </button>
    </td>
</tr>
</table>
</form>
<!-- NEED ONE ALERT FIRST IF THE USER IS NOT CONNECTED TO THE WEBSITE -->
<?php
} else {
    // no valid land for events found :( give out some error message
	// Temporary removed, need to be fixed in next release
	// <span class='icon-notification'></span>

	echo "<p class='alert alert-error'>".JText::_('JOPENSIM_EVENTNOLANDFOUND')."</p>\n";
	echo "<p>".JText::_('JOPENSIM_EVENT_VALIDLAND_DETAIL')."</p>\n";
	echo "<ol>\n";
	echo "\t<li>".JText::_('JOPENSIM_EVENT_VALIDLAND1')."</li>\n";
	echo "\t<li>".JText::_('JOPENSIM_EVENT_VALIDLAND2')."</li>\n";
	echo "\t<li>".JText::_('JOPENSIM_EVENT_VALIDLAND3')."</li>\n";
	echo "</ol>\n";
}
?>