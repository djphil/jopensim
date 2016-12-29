<?php
/**
* @version $Id: mod_jdownloads_admin_stats.php v3.2
* @package mod_jdownloads_admin_stats
* @copyright (C) 2016 Arno Betz
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author Arno Betz http://www.jDownloads.com
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.html.html');

    // fix for Joomla 3 missing CSS tabs when creating tabs
	$document = JFactory::getDocument();
	$document->addStyleDeclaration('
		dl.tabs {
           float:left;
           margin:10px 0 -1px 0;
           z-index:50;
        }
		dl.tabs dt {
            float:left;
            padding:4px 10px;
            border:1px solid #ccc;margin-left:3px;
            background:#F9F9F9;
            color:#666;
        }
		dl.tabs dt.open {
            background:#fff;
            border-bottom:1px solid #f9f9f9;
            z-index:100;
            color:#000;
        }
		div.current {
            clear:both;
            border:1px solid #ccc;
            padding:10px 10px;
            background:#fff;
        }
		dl.tabs h3 {
            font-size:12px;
            line-height:12px;
            margin:4px;
        }
        ');
        
    // Import Joomla! tabs
    jimport('joomla.html.pane');

?>

<div class="clr"></div>

<?php echo JHtml::_('tabs.start'); ?>

<?php if($params->get('view_quicklinks', 1)): ?>
    <?php echo JHtml::_('tabs.panel', JText::_('MOD_JOPENSIM_ADMIN_QUICKLINKS'), 'latestItemsTab'); ?>
	<table class="adminlist table table-striped">
	<tbody>
	<tr>
		<td>
		<div class="btn-inline" id="jOpenSimQuickIcons">
		<?php
		if (is_array($buttons) && count($buttons) > 0) {
			foreach($buttons AS $button) echo "<div class='icon-wrapper '><div class='icon'>".$button."</div></div>\n";
		}
		?>
		<?php if($params->get('view_helplinks', 1)): ?>
		<div style="float:left;margin-left:20px;">
		<table>
		<thead>
		<tr>
			<th class="title" style="padding-top:0px;"><?php echo JText::_('MOD_JOPENSIM_ADMIN_HELPLINKS'); ?></th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td><i class="icon icon-arrow-right-4"></i><a class="hasTooltip" href="http://wiki.jopensim.com" title="jOpenSim Wiki" target="_blank">jOpenSim Wiki</a></td>
		</tr>
		<tr>
			<td><i class="icon icon-arrow-right-4"></i><a class="hasTooltip" href="http://www.jopensim.com/forum/index.html" title="jOpenSim Forum" target="_blank">jOpenSim Forum</a></td>
		</tr>
		</tbody>
		</table>
		</div>
		<?php endif; ?>
		</div>
		</td>
	</tr>
	</tbody>
	</table>
<?php endif; ?>

<?php if($params->get('view_recentonline', 1)): ?>
    <?php echo JHtml::_('tabs.panel', JText::_('MOD_JOPENSIM_ADMIN_RECENTUSERSONLINE'), 'popularItemsTab'); ?>
	<table class="adminlist table table-striped">
	<thead>
	<tr>
		<th class="title"><?php echo JText::_('MOD_JOPENSIM_ADMIN_USER'); ?></th>
		<th class="title"><?php echo JText::_('MOD_JOPENSIM_ADMIN_LASTLOGIN'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach($recentonline_items as $recentonline_item): ?>
	<tr>
		<td><?php echo $recentonline_item->firstname." ".$recentonline_item->lastname; ?></td>
		<td><?php echo JFactory::getDate($recentonline_item->last_login); ?></td>
	</tr>
	<?php endforeach; ?>
	</tbody>
	</table>
<?php endif; ?>

<?php if($params->get('view_recentregistered', 1)): ?>
    <?php echo JHtml::_('tabs.panel', JText::_('MOD_JOPENSIM_ADMIN_RECENTUSERSREGISTERED'), 'popularItemsTab'); ?>
	<table class="adminlist table table-striped">
	<thead>
	<tr>
		<th class="title"><?php echo JText::_('MOD_JOPENSIM_ADMIN_USER'); ?></th>
		<th class="title"><?php echo JText::_('MOD_JOPENSIM_ADMIN_CREATED'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach($recentregistered_items as $recentregistered_item): ?>
	<tr>
		<td><?php echo $recentregistered_item->firstname." ".$recentregistered_item->lastname; ?></td>
		<td><?php echo JFactory::getDate($recentregistered_item->created); ?></td>
	</tr>
	<?php endforeach; ?>
	</tbody>
	</table>
<?php endif; ?>

<?php if($params->get('view_topgroups', 1)): ?>
    <?php echo JHtml::_('tabs.panel', JText::_('MOD_JOPENSIM_ADMIN_TOPGROUPS'), 'featuredItemsTab'); ?>
	<table class="adminlist table table-striped">
	<thead>
	<tr>
		<th class="title"><?php echo JText::_('MOD_JOPENSIM_ADMIN_GROUP'); ?></th>
		<th class="title"><?php echo JText::_('MOD_JOPENSIM_ADMIN_GROUPMEMBERS'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach($topgroup_items as $topgroup_item): ?>
	<tr>
		<td><?php echo $topgroup_item->Name; ?></td>
		<td><?php echo $topgroup_item->members; ?></td>
	</tr>
	<?php endforeach; ?>
	</tbody>
	</table>
<?php endif; ?>

<?php echo JHtml::_('tabs.end'); ?>
