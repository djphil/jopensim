<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2018 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

require_once ('css.php');

// Classic view only
if ($this->settingsdata['jopensim_loginscreen_stylebold']) $stylebold = "text-bold";
if ($this->settingsdata['jopensim_loginscreen_styleicon']) $styleicon = TRUE;
?>

<?php if ($this->settingsdata['jopensim_loginscreen_matrix_fx']): ?>
    <div id="matrix"></div>
<?php endif; ?>

<div class='jopensim_loginscreen'>
<?php if ($this->settingsdata['loginscreen_box_gridstatus'] == 1 && (
    $this->settingsdata['loginscreen_show_status'] == 1 || 
    $this->settingsdata['loginscreen_show_regions'] == 1 || 
    $this->settingsdata['loginscreen_show_uniquevisitors'] == 1 || 
    $this->settingsdata['loginscreen_show_totalusers'] == 1 || 
    $this->settingsdata['loginscreen_show_onlinenow'] == 1 
)): ?>

<!-- GRIDSTATUS BOX -->
<div id='jopensim_gridbox' class='welcomebox_gridstatus'>
    <div class='welcomebox_gridstatus_title'>
        <?php if ($styleicon): ?>
            <i class="icon-bars"></i>&nbsp;
        <?php endif; ?>
        <?php echo JText::_('JOPENSIM_LOGINSCREEN_GRIDSTATUS'); ?>
    </div>
    <div class='welcomebox_gridstatus_content'>
        <table class="table-condensed">
        <tbody>
            <?php if($this->settingsdata['loginscreen_show_status'] == 1): ?>
            <tr>
                <td><?php echo JText::_('LABEL_GRIDSTATUS'); ?>:</td>
                <td class='text-right <?php echo $stylebold; ?>'><?php echo $this->gridstatus['statusmsg']; ?></td>
            </tr>
            <?php endif; ?>

            <?php if($this->gridstatus['status'] == "online"): ?>
                <?php if($this->settingsdata['loginscreen_show_regions'] == 1): ?>
                    <tr>
                        <td><?php echo JText::_('LABEL_TOTALREGIONS'); ?>:</td>
                        <td class='text-right <?php echo $stylebold; ?>'><?php echo $this->gridstatus['totalregions']; ?></td>
                    </tr>
                <?php endif; ?>
                
                <?php if($this->settingsdata['loginscreen_show_uniquevisitors'] == 1): ?>
                    <tr>
                        <td><?php echo JText::sprintf('LABEL_LASTXDAYS',$this->gridstatus['days']); ?>:</td>
                        <td class='text-right <?php echo $stylebold; ?>'><?php echo $this->gridstatus['lastonline']; ?></td>
                    </tr>
                <?php endif; ?>
                
                <?php if ($this->settingsdata['loginscreen_show_totalusers'] == 1): ?>
                    <tr>
                        <td><?php echo JText::_('LABEL_TOTALUSERS'); ?>:</td>
                        <td class='text-right <?php echo $stylebold; ?>'><?php echo $this->totalusers; ?></td>
                    </tr>
                <?php endif; ?>
                
                <?php if($this->settingsdata['loginscreen_show_onlinenow'] == 1): ?>
                    <tr>
                        <td><?php echo JText::_('LABEL_ONLINENOW'); ?>:</td>
                        <td class='text-right <?php echo $stylebold; ?>'><?php echo $this->gridstatus['online']; ?></td>
                    </tr>
                <?php endif; ?>
            <?php endif; ?>
        <tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- MESSAGE BOX -->
<?php if ($this->settingsdata['loginscreen_box_message'] == 1): ?>
    <div id='jopensim_messagebox' class='welcomebox_messages'>
        <div class='welcomebox_messages_title'>
        <?php if ($styleicon): ?>
            <i class="icon-info"></i>&nbsp;
        <?php endif; ?>
            <?php echo $this->settingsdata['loginscreen_msgbox_title']; ?>
        </div>
        <div class='welcomebox_messages_content'>
            <?php echo nl2br($this->settingsdata['loginscreen_msgbox_message']); ?>
        </div>
    </div>
<?php endif; ?>

<!-- REGIONS BOX -->
<?php if ($this->settingsdata['loginscreen_box_regions'] == 1): ?>
    <?php if (is_array($this->regions) && count($this->regions) > 0): ?>
        <div id='jopensim_regionbox' class='welcomebox_regions'>
            <div class='welcomebox_regions_title'>
                <?php if ($styleicon): ?>
                    <i class="icon-grid-2"></i>&nbsp;
                <?php endif; ?>
                <?php echo JText::_('JOPENSIM_LOGINSCREEN_REGIONS'); ?>
            </div>

            <div class='welcomebox_regions_content'>
                <table class="table-condensed">
                <thead>
                <tr>
                    <th>
                        <span class="label label-default">
                            <?php echo JText::_('JOPENSIM_LOGINSCREEN_REGIONS_NAME'); ?>:
                        <span>
                    </th>
                    <th><span class="label label-success">Loc X:<span></th>
                    <th><span class="label label-danger">Loc Y:<span></th>
                </tr>
                </thead>

                <tbody>
                <?php foreach($this->regions AS $key => $region): ?>
                <tr>
                    <td>
                        <a class="<?php echo $stylebold; ?>" href="secondlife://<?php echo rawurlencode($region['regionName']); ?>">
                            <?php echo $region['regionName']; ?>
                        </a>
                    </td>
                    <td><?php echo round($region['posX']); ?></td>
                    <td><?php echo round($region['posY']); ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>
</div>
