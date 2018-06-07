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

    <div class="form-group" role="dialog">
        <form action="index.php" method="post" id="adminForm" name="adminForm">
            <fieldset>
                <legend><?php echo JText::_('JOPENSIM_LOGINSCREEN_MODIFYPOS'); ?></legend>

                <input type='hidden' name='option' value='com_opensim' />
                <input type='hidden' name='view' value='loginscreen' />
                <input type='hidden' name='task' value='savePos' />
                <input type='hidden' name='posType' value='<?php echo $this->posType; ?>' />
                <input type='hidden' name='posID' value='<?php echo $this->id; ?>' />

                <table class="table table-condensed table-striped table-hover">
                    <colgroup>
                        <col width='150' /><col>
                    </colgroup>
                    <tbody>

                        <?php if($this->posType == "setX"): ?>
                        <tr>
                            <td><?php echo $this->form->getLabel('alignH'); ?></td>
                            <td><?php echo $this->form->getInput('alignH'); ?></td>
                        </tr>
                        <?php else: ?>
                        <tr>
                            <td><?php echo $this->form->getLabel('alignV'); ?></td>
                            <td><?php echo $this->form->getInput('alignV'); ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <td><?php echo $this->form->getLabel('distance'); ?></td>
                            <td><?php echo $this->form->getInput('distance'); ?></td>
                        </tr>
                    </tbody>
                </table>
                <button class="btn btn-success" name="save" type="submit">
                    <span class="icon-checkmark"></span> 
                    <?php echo JText::_('JSAVE'); ?>
                </button>
            </fieldset>
        </form>
    </div>
</div>
