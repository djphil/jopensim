<?php
/*
 * @module OpenSim Friends
 * @copyright Copyright (C) 2017 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// no direct access
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

$nofollowattr = ($nofollow == 1) ? " rel='nofollow'":"";
$stylebold = ($params->get('stylebold')) ? "text-bold":"";
?>

<?php if(count($friendlist) > 0): ?>
<?php if($useaccordion == 1): ?>
<style type="text/css">
	#jOpenSim_friends_container {
		max-height:<?php echo $accordioninit; ?>px;
	}

	#jOpenSimFriendsToggler.jOSFactive {
		color:red;
	}

</style>

<script type="text/javascript">
jQuery(document).ready(function(){
	var tableHeight = jQuery("#jOpenSimFriendsTable").height();
	if(tableHeight < <?php echo $accordioninit; ?>) {
		jQuery("#jOpenSimFriendsToggler").hide();
		jQuery("#jOpenSimFriendsShowMore").hide();
		jQuery("#jOpenSim_friends_container").attr('height',tableHeight);
	} else {
//		jQuery("#jOpenSim_friends_container").hide();
		jQuery("#jOpenSimFriendsToggler").click(function(){
//			alert(tableHeight);
			var currentAttrValue = jQuery("#jOpenSim_friends_container").attr('mystatus');
//			alert(currentAttrValue);
			if(currentAttrValue == "collapsed") {
				document.getElementById("jOpenSimFriendsToggler").innerHTML = "<span class='<?php echo $accordionupclass; ?>'> </span>";
				document.getElementById("jOpenSimFriendsShowMore").innerHTML = "<?php echo JText::_('MOD_OPENSIM_FRIENDS_SHOWLESS'); ?>";
				jQuery("#jOpenSim_friends_container").attr('mystatus','ellapsed');
				jQuery("#jOpenSim_friends_container").animate({minHeight:tableHeight+'px',maxHeight:tableHeight+'px'},<?php echo $accordiontime; ?>,"swing");
			} else {
				document.getElementById("jOpenSimFriendsToggler").innerHTML = "<span class='<?php echo $accordiondownclass; ?>'> </span>";
				document.getElementById("jOpenSimFriendsShowMore").innerHTML = "<?php echo JText::_('MOD_OPENSIM_FRIENDS_SHOWMORE'); ?>";
				jQuery("#jOpenSim_friends_container").attr('mystatus','collapsed');
				jQuery("#jOpenSim_friends_container").animate({minHeight:'<?php echo $accordioninit; ?>px',maxHeight:'<?php echo $accordioninit; ?>px'},<?php echo $accordiontime; ?>,"swing");
			}
//			jQuery("#jOpenSim_friends_container").slideDown(1000,"linear");
//			jQuery("#jOpenSim_friends_container").slideToggle("slow");
//			jQuery("#jOpenSim_friends_container").toggleClass("jOSF_collapsed jOSF_ellapsed");
			jQuery("#jOpenSimFriendsToggler").toggleClass("jOSFactive");
			return false;
		});
		jQuery("#jOpenSimFriendsShowMore").click(function(){
			var currentAttrValue = jQuery("#jOpenSim_friends_container").attr('mystatus');
			if(currentAttrValue == "collapsed") {
				document.getElementById("jOpenSimFriendsToggler").innerHTML = "<span class='<?php echo $accordionupclass; ?>'> </span>";
				document.getElementById("jOpenSimFriendsShowMore").innerHTML = "<?php echo JText::_('MOD_OPENSIM_FRIENDS_SHOWLESS'); ?>";
				jQuery("#jOpenSim_friends_container").attr('mystatus','ellapsed');
				jQuery("#jOpenSim_friends_container").animate({minHeight:tableHeight+'px',maxHeight:tableHeight+'px'},<?php echo $accordiontime; ?>,"swing");
			} else {
				document.getElementById("jOpenSimFriendsToggler").innerHTML = "<span class='<?php echo $accordiondownclass; ?>'> </span>";
				document.getElementById("jOpenSimFriendsShowMore").innerHTML = "<?php echo JText::_('MOD_OPENSIM_FRIENDS_SHOWMORE'); ?>";
				jQuery("#jOpenSim_friends_container").attr('mystatus','collapsed');
				jQuery("#jOpenSim_friends_container").animate({minHeight:'<?php echo $accordioninit; ?>px',maxHeight:'<?php echo $accordioninit; ?>px'},<?php echo $accordiontime; ?>,"swing");
			}
			jQuery("#jOpenSimFriendsToggler").toggleClass("jOSFactive");
			return false;
		});
	}
});

</script>
<?php endif; ?>

<div id='jOpenSim_friends' class='jOpenSim_friends'>
<?php if($useaccordion == 1): ?>
	<div id='jOpenSimFriendsTogglerOuter'>
		<div id='jOpenSimFriendsTogglerInner'>
			<button id="jOpenSimFriendsToggler" class="accordion"><span class='<?php echo $accordiondownclass; ?>'> </span></button>
		</div>
	</div>
	<div id='jOpenSim_friends_container' class='jOSF_collapsed' style='overflow:hidden;' mystatus="collapsed">
<?php else: ?>
	<div id='jOpenSim_friends_container'>
<?php endif; ?>
    <table id="jOpenSimFriendsTable" class="table table-striped table-condensed table-hover">
    <tbody>
        <?php if(is_array($friendlist[1])): ?>
        <?php foreach($friendlist[1] AS $friend): ?>
        <tr>
        <?php
        if ($linkprofile == 1 && $friend['sourcegrid'] == "local") {
        	$friendurl	= JRoute::_("&option=com_opensim&view=profile&uid=".$friend['uid']."&Itemid=".$itemid);
            $link1 = "<a href='".$friendurl."'".$nofollowattr.">";
            $link2 = "</a>";
        } else {
            $link1 = "";
            $link2 = "";
        }
        if($friend['sourcegrid'] == "hg") $friend['name'] = "<span title='".$friend['nametitle']."'>".$friend['name']."</span>";
        ?>
            <td class="text-left"><?php echo $link1.$friend['name'].$link2; ?></td>
            <td class="text-right <?php echo $stylebold; ?>"><?php echo $onlineText; ?></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
        <?php if (is_array($friendlist[0])): ?>
        <?php foreach($friendlist[0] AS $friend): ?>
        <tr>
        <?php
        if($linkprofile == 1 && $friend['sourcegrid'] == "local") {
        	$friendurl	= JRoute::_("&option=com_opensim&view=profile&uid=".$friend['uid']."&Itemid=".$itemid);
            $link1 = "<a href='".$friendurl."'".$nofollowattr.">";
            $link2 = "</a>";
        } else {
            $link1 = "";
            $link2 = "";
        }
        if($friend['sourcegrid'] == "hg") $friend['name'] = "<span title='".$friend['nametitle']."'>".$friend['name']."</span>";
        ?>
            <td><?php echo $link1.$friend['name'].$link2; ?></td>
            <td class="text-right <?php echo $stylebold; ?>"><?php echo $offlineText; ?></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
    </table>
	</div>
<?php if($useaccordion == 1): ?>
	<div id='jOpenSimFriendsShowMore' class='oldStyle'><?php echo JText::_('MOD_OPENSIM_FRIENDS_SHOWMORE'); ?></div>
<?php endif; ?>
</div>


<?php endif; ?>
