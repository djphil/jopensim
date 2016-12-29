<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2015 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
?>

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
            
			<div class='jopensim_useredittable'>
			    <div class='jopensim_useredittable_tr'>
				    <div class='jopensim_useredittable_td1'>
					    Region Name:
					</div>
				    <div class='jopensim_useredittable_td2'>
					    <input type='text' maxlength='25' name="region_name" />
					</div>
				</div>
				
				<div class='jopensim_useredittable_tr'>
				    <div class='jopensim_useredittable_td1'>Listen IP:</div>
    					<div class='jopensim_useredittable_td2'>
						    <input type='text' maxlength='25' name="listen_ip" value="0.0.0.0" />
						</div>
					</div>
				<div class='jopensim_useredittable_tr'>
    				<div class='jopensim_useredittable_td1'>
					    Listen Port:
					</div>
					<div class='jopensim_useredittable_td2'>
					    <input type='text' maxlength='25' name="listen_port" value="9000" />
					</div>
				</div>
				<div class='jopensim_useredittable_tr'>
				    <div class='jopensim_useredittable_td1'>
					    External Adress:
					</div>
					<div class='jopensim_useredittable_td2'>
					    <input type='text' maxlength='25' name="external_address" value="<?php echo $this->remotehost; ?>" />
					</div>
				</div>
				<div class='jopensim_useredittable_tr'>
				    <div class='jopensim_useredittable_td1'>
    					Region X:
					</div>
					<div class='jopensim_useredittable_td2'>
					    <input type='text' maxlength='25' name="region_x" value="1000" />
					</div>
				</div>
				<div class='jopensim_useredittable_tr'>
    				<div class='jopensim_useredittable_td1'>
					    Region Y:
					</div>
					<div class='jopensim_useredittable_td2'>
				        <input type='text' maxlength='25' name="region_y" value="1000" />
				    </div>
			    </div>
			    <div class='jopensim_useredittable_tr'>
			        <div class='jopensim_useredittable_td1'>
				        Public Region ?
				    </div>
			        <div class='jopensim_useredittable_td2'>
			            <select name="public">
				            <option value="false">No</option>
					        <option value="true" selected='selected'>Yes</option>
				        </select>
			        </div>
		        </div>
			    <div class='jopensim_useredittable_tr'>
				    <div class='jopensim_useredittable_td1'>
					    Voice ?
					</div>
					<div class='jopensim_useredittable_td2'>
					    <select name="voice">
						    <option value="false" selected='selected'>No</option>
							<option value="true">Yes</option>
						</select>
					</div>
				</div>
				<div class='jopensim_useredittable_tr'>
    				<div class='jopensim_useredittable_td1'>
					    Estate Name:
					</div>
				    <div class='jopensim_useredittable_td2'>
					    <input type='text' maxlength='64' name="estate_name" value="" />
					</div>
				</div>
			</div>
			<button type='submit' class='btn btn-default btn-success' />
		        <span class='icon-checkmark'></span> <?php echo JText::_('JOPENSIM_ADDREGION'); ?>
		    </button>
	    </legend>
    </form>
</div>
</div>
