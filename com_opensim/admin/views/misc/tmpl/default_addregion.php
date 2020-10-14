<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
?>

<div class="jopensim-adminpanel">
<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>

<div id="j-main-container" class="span10">
<div class="form-group">
    <form action="index.php" method="post" id="adminForm" name="adminForm">
        <fieldset>
            <legend><?php echo JText::_('JOPENSIM_ADDREGION'); ?></legend>
            <input type="hidden" name="option" value="com_opensim" />
            <input type="hidden" name="view" value="misc" />
            <input type="hidden" name="task" value="createregionsend" />
            <?php if($this->settings['remoteadminsystem'] == "multiple"): ?>
            <input type="hidden" name="radminsystem" value="multiple" />
            <?php if(is_array($this->simulators) && count($this->simulators) > 0): ?>
            <?php echo JText::_('JOPENSIM_REMOTEADMINSYSTEM_MULTIPLE_SENDTO'); ?>:&nbsp;<select name='simulator'>
            <?php foreach($this->simulators AS $simulator): ?>
            	<option value='<?php echo $simulator['simulator']; ?>' title='<?php echo $simulator['regions']; ?>'><?php echo ($simulator['alias']) ? $simulator['alias']:$simulator['simulator']; ?></option>
            <?php endforeach; ?>
	        </select>
            <?php else: ?>
            <input type="hidden" name="simulator" value="" />
            <?php echo JText::_('JOPENSIM_REMOTEADMINSYSTEM_MULTIPLE_ERROR_NOSIMULATORS'); ?>
            <?php endif; ?>
            <?php else: ?>
            <input type="hidden" name="radminsystem" value="single" />
            <?php endif; ?>
            <table class="table table-condensed table-striped table-hover">
                <colgroup>
                    <col width='150' /><col>
                </colgroup>

                <tbody>
                    <tr>
                        <td>Region Name:</td>
                        <td><input type='text' maxlength='25' name="region_name" placeHolder="jOpenSim"/></td>
                    </tr>
                    <tr>
                        <td>Listen IP:</td>
                        <td><input type='text' maxlength='25' name="listen_ip" placeHolder="0.0.0.0" /></td>
                    </tr>
                    <tr>
                        <td>Listen Port:</td>
                        <td><input type='text' maxlength='25' name="listen_port" placeHolder="9000" /></td>
                    </tr>
                    <tr>
                        <td>Region X:</td>
                        <td><input type='text' maxlength='25' name="region_x" placeHolder="1000" /></td>
                    </tr>
                    <tr>
                        <td>Region Y:</td>
                        <td><input type='text' maxlength='25' name="region_y" placeHolder="1000" /></td>
                    </tr>
                    <tr>
                        <td>Public Region ?</td>
                        <td> 
                            <select name="public">
                                <option value="false">No</option>
                                <option value="true" selected='selected'>Yes</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Voice ?</td>
                        <td>
                            <select name="voice">
                                <option value="false" selected='selected'>No</option>
                                <option value="true">Yes</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Estate Name:</td>
                        <td><input type='text' maxlength='64' name="estate_name" placeHolder="My Estate" /></td>
                    </tr>
                </tbody>
            </table>
            
            <button type='submit' class='btn btn-default btn-success' />
            <span class='icon-checkmark'></span> <?php echo JText::_('JOPENSIM_ADDREGION'); ?>
            </button>
	    </fieldset>
    </form>
</div>
</div>
</div>
