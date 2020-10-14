<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.html.html');
?>
<div class="jopensim-adminpanel">

<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
    <div class="form-group">
        <form action="index.php" method="post" id="adminForm" name="adminForm">
            <fieldset>
                <legend><?php echo JText::_('JOPENSIM_LOGINSCREEN_ADDNEWPOS'); ?></legend>

                <input type='hidden' name='option' value='com_opensim' />
                <input type='hidden' name='view' value='loginscreen' />
                <input type='hidden' name='task' value='list' />
                <input type='hidden' name='boxchecked' value='0' />

                <table class="table table-condensed table-striped table-hover">
                    <colgroup>
                        <col width='150' /><col>
                    </colgroup>
                    <tbody>
                        <tr>
                            <td><?php echo $this->form->getLabel('id'); ?></td>
                            <td><?php echo $this->form->getInput('id'); ?></td>
                        </tr>
                        <tr>
                            <td><?php echo $this->form->getLabel('positionname'); ?></td>
                            <td><?php echo $this->form->getInput('positionname'); ?></td>
                        </tr>
                        <tr>
                            <td><?php echo $this->form->getLabel('alignH'); ?></td>
                            <td><?php echo $this->form->getInput('alignH'); ?></td>
                        </tr>
                        <tr>
                            <td><?php echo $this->form->getLabel('posX'); ?></td>
                            <td><?php echo $this->form->getInput('posX'); ?></td>
                        </tr>
                        <tr>
                            <td><?php echo $this->form->getLabel('alignV'); ?></td>
                            <td><?php echo $this->form->getInput('alignV'); ?></td>
                        </tr>
                        <tr>
                            <td><?php echo $this->form->getLabel('posY'); ?></td>
                            <td><?php echo $this->form->getInput('posY'); ?></td>
                        </tr>
                        <tr>
                            <td><?php echo $this->form->getLabel('zindex'); ?></td>
                            <td><?php echo $this->form->getInput('zindex'); ?></td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
        </form>
    </div>
</div>
</div>