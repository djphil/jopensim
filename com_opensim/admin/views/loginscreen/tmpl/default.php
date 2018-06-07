<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2018 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.html.html');
?>

<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
    <form action="index.php" method="post" id="adminForm" name="adminForm">
        <input type='hidden' name='option' value='com_opensim' />
        <input type='hidden' name='view' value='loginscreen' />
        <input type='hidden' name='task' value='list' />
        <input type='hidden' name='boxchecked' value='0' />

        <table class="table table-striped table-hover adminlist">
            <thead>
                <tr>
                    <th width="5"><?php echo JText::_('Num'); ?></th>
                    <th class='title' width='10'>&nbsp;</td>
                    <th class='title'><?php echo JText::_('JOPENSIM_LOGINSCREEN_POSITIONNAME'); ?></th>
                    <th class='title'><?php echo JText::_('JOPENSIM_LOGINSCREEN_POSITIONMODULES'); ?></th>
                    <th class='title'><?php echo JText::_('JOPENSIM_LOGINSCREEN_POSITIONX'); ?></th>
                    <th class='title'><?php echo JText::_('JOPENSIM_LOGINSCREEN_POSITIONY'); ?></th>
                    <th class='title'><?php echo JText::_('JOPENSIM_LOGINSCREEN_POSITIONACTIVE'); ?></th>
                    <th class='title'><?php echo JText::_('JOPENSIM_LOGINSCREEN_POSITIONZINDEX'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 0;
                $modulecount = 0;
                foreach($this->positionlist AS $position) {
                    $i++;
                ?>
                <tr>
                    <td valign="top"><?php echo $i; ?></td>
                    <td valign="top"><input type='checkbox' name='checkPosition[]' id='position_<?php echo $position['id']; ?>' value='<?php echo $position['id']; ?>' onClick='Joomla.isChecked(this.checked);' style='margin-top:0px;margin-right:5px;' /></td>
                    <td valign="top"><?php echo $position['positionname']; ?></td>
                    <td valign="top">
                    <?php if(is_array($position['modules']) && count($position['modules']) > 0): ?>
                    <?php foreach($position['modules'] AS $module): ?>
                    <?php $modulecount++; ?>
                    <div>
                    <a href="index.php?option=com_modules&task=module.edit&id=<?php echo $module['id']; ?>" title="<?php echo JText::_('JOPENSIM_LOGINSCREEN_POSITIONMODULE_ID').": ".$module['id']."\n".JText::_('JOPENSIM_LOGINSCREEN_POSITIONMODULE_TYPE').": ".$module['module']; ?>">
                    <?php echo $module['title']; ?>
                    <?php if($module['published'] == 0) echo '<span class="jopensimmoduleunpublished">('.JText::_('JOPENSIM_LOGINSCREEN_POSITIONMODULE_UNPUBLISHED').")</span>"; ?>
                    <?php if($module['published'] == -2) echo '<span class="jopensimmoduleunpublished">('.JText::_('JOPENSIM_LOGINSCREEN_POSITIONMODULE_TRASHED').")</span>"; ?>
                    </a>
                    </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <a href="index.php?option=com_modules"><?php echo JText::_('JOPENSIM_LOGINSCREEN_POSITIONMODULE_SET'); ?></a>
                    <?php endif; ?>
                    </td>
                    <td valign="top">
                    <a class="modal" href="index.php?option=com_opensim&view=loginscreen&task=setX&id=<?php echo $position['id']; ?>&tmpl=component" title="<?php echo JText::_('JOPENSIM_LOGINSCREEN_POSITIONMODULE_SETPOSX'); ?>">
                    <?php echo $position['alignH']." - ".$position['posX']."px"; ?>
                    </a>
                    </td>
                    <td valign="top">
                    <a class="modal" href="index.php?option=com_opensim&view=loginscreen&task=setY&id=<?php echo $position['id']; ?>&tmpl=component" title="<?php echo JText::_('JOPENSIM_LOGINSCREEN_POSITIONMODULE_SETPOSY'); ?>">
                    <?php echo $position['alignV']." - ".$position['posY']."px"; ?>
                    </a>
                    </td>
                    <td valign="top">
                    <?php if(count($position['modules']) == 0): ?>
                    <div class="btn btn-micro btn-disabled" title="<?php echo JText::_('JOPENSIM_LOGINSCREEN_POSITIONMODULE_SELECT1ST'); ?>"><span class="icon-eye-blocked"></span></div>
                    <?php elseif($position['active'] == 1): ?>
                    <a class="btn btn-micro" href="index.php?option=com_opensim&view=loginscreen&task=setPosInvisible&id=<?php echo $position['id']; ?>"><span class="icon-publish"></span></a>
                    <?php else: ?>
                    <a class="btn btn-micro" href="index.php?option=com_opensim&view=loginscreen&task=setPosVisible&id=<?php echo $position['id']; ?>"><span class="icon-unpublish"></span></a>
                    <?php endif; ?>
                    </td>
                    <td valign="top"><?php echo $position['zindex']; ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <a class="btn btn-primary" href="<?php echo JURI::root(); ?>index.php?option=com_opensim" target="_blank">
            <span class="icon-eye"></span> 
            <?php echo JText::_('JOPENSIM_LOGINSCREEN_PREVIEW'); ?>
            </a>
        <?php if($modulecount == 0): ?>
            <?php echo JText::sprintf('LOGINSCREEN_HELP_HINT',LOGINSCREEN_HELP_LINK); ?>
        <?php endif; ?>
    </form>
</div>