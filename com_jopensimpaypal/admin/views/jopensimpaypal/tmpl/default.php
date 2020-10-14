<?php
/*
 * @component jOpenSimPayPal
 * @copyright Copyright (C) 2017 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access'); 
?>

<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
<div id="jopensim" class="jopensim-cpanel">
    <section class="content-block" role="main">
        <div class="row-fluid">
            <div class="span7">
                <div class="well well-small">
                    <div class="module-title nav-header">
						<?php echo JText::_('COM_JOPENSIMPAYPAL_WELCOME'); ?>
					</div>

					<hr class="hr-condensed">

					<div id="cpanel">
						<div class="btn-inline" id="jOpenSimQuickIcons">
							<?php if (is_array($this->adminbuttons) && count($this->adminbuttons) > 0) {
								foreach($this->adminbuttons AS $button)
									echo "<div class='icon-wrapper '><div class='icon'>".$button."</div></div>\n";
							} ?>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>

                <div class="well well-small">
	                <div class="module-title nav-header">
						<?php echo JText::_('COM_JOPENSIMPAYPAL_TODO'); ?>
					</div>

					<hr class="hr-condensed">

					<div id="cpanel">
						<form action="index.php" method="post" name="adminForm">
							<input type='hidden' name='option' value='com_jopensimpaypal' />
							<input type='hidden' name='view' value='jopensimpaypal' />
							<input type='hidden' name='task' value='' />
						</form>	
						<?php if ($this->newtransactions > 0 || $this->newpayouts > 0 || (is_array($this->unsolvedpayouts) && count($this->unsolvedpayouts) > 0)): ?>
						<ul class='jopensimpaypal_overviewlist'>
							<?php if ($this->newtransactions > 0): ?>
							<li class='jopensimpaypal_overviewlistitem'>
								<a href='index.php?option=com_jopensimpaypal&view=transactions'>
									<?php echo JText::sprintf('COM_JOPENSIMPAYPAL_NEWTRANSACTIONS',"<span class='jopensimpaypal_overview_transactionnumber'>".$this->newtransactions."</span>"); ?>
								</a>
							</li>
							<?php endif; ?>
						
							<?php if($this->newpayouts > 0): ?>
							<li class='jopensimpaypal_overviewlistitem'>
								<a href='index.php?option=com_jopensimpaypal&view=payout'>
									<?php echo JText::sprintf('COM_JOPENSIMPAYPAL_NEWPAYOUTS',"<span class='jopensimpaypal_overview_payoutnumber'>".$this->newpayouts."</span>"); ?>
								</a>
							</li>
							<?php endif; ?>
							
							<?php if (is_array($this->unsolvedpayouts) && count($this->unsolvedpayouts) > 0): ?>
							<li class='jopensimpaypal_overviewlistitem'>
								<a href='index.php?option=com_jopensimpaypal&view=payout&filter.payoutstatus=unsolved'>
									<?php echo JText::_('COM_JOPENSIMPAYPAL_UNSOLVEDPAYOUTS'); ?>:
								</a>
							</li>
							<ul class='jopensimpaypal_overviewlist2'>
								<?php foreach($this->unsolvedpayouts AS $unsolved): ?>
								<li class='jopensimpaypal_overviewlistitem2'>
									<a href='index.php?option=com_jopensimpaypal&view=payout&filter.payoutstatus=<?php echo $unsolved->filter; ?>'>
										<?php echo "<span class='jopensimpaypal_overview_unsolvednumber'>".$unsolved->anzahl."</span> ".JText::_($unsolved->payoutstatus); ?>
									</a>
								</li>
								<?php endforeach; ?>
							</ul>
							<?php endif; ?>
						</ul>
						<?php else: ?>
						<?php echo JText::_('COM_JOPENSIMPAYPAL_NOTHINGTODO'); ?>
						<?php endif; ?>
					</div>
				</div>

				<div class="well well-small">
					<div class="module-title nav-header "><?php echo JText::_('COM_JOPENSIMPAYPAL_GETHELP'); ?></div>
					<hr class="hr-condensed">
					<ul class="">
						<li><i class="icon icon-question"></i><a class="hasTooltip" href="http://wiki.jopensim.com/index.php/jOpenSimPayPal" title="Wiki" target="_blank"><?php echo JText::_('COM_JOPENSIMPAYPAL_FAQ'); ?>: Wiki</a></li>
						<li><i class="icon icon-question"></i><a class="hasTooltip" href="https://www.jopensim.com/forum/index.html" title="Forum" target="_blank"><?php echo JText::_('COM_JOPENSIMPAYPAL_FAQ'); ?>: Forum</a></li>
						<li><i class="icon icon-question"></i><a class="hasTooltip" href="http://mantis.jopensim.com" title="Mantis" target="_blank"><?php echo JText::_('COM_JOPENSIMPAYPAL_FAQ'); ?>: Mantis</a></li>
					</ul>
					<div class="clearfix"></div>
				</div>
			</div>

			<div class="span5">
				<div class="well well-small">
					<div class="center"> 
						<img src="components/com_opensim/assets/images/jOpenSim.png"/>
					</div>
						
					<dl class="dl-horizontal">
					<hr class="hr-condensed">
					<dt><?php echo JText::_('Version');?>:</dt>
					<dd><?php echo $this->jopensimpaypalVersion ;?></dd>
					<hr class="hr-condensed">
						
					<dt><?php echo JText::_('OS');?>:</dt>
					<dd>Running on <?php if(PHP_INT_SIZE == 4) echo "32Bit"; elseif(PHP_INT_SIZE == 8) echo "64Bit"; else echo "unknown (".PHP_INT_SIZE.")"; ?> System</dd>
					<hr class="hr-condensed">
						
					<dt><?php echo JText::_('License');?>:</dt>
					<dd>General Public License <a href="http://www.gnu.org/licenses/gpl-2.0.html" target="_blank">GPLv2</a></dd>
					<hr class="hr-condensed">
					
					<dt>More Info:</dt>
					<dd><a href="https://www.jopensim.com" target="_blank">www.jopensim.com</a></dd>
					<hr class="hr-condensed">
					</dl>
				</div>
			</div>
		</div>
	</section>


	</div>
	</div>
</div>
