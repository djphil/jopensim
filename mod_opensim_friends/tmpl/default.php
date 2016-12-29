<?php
/*
 * @module OpenSim Friends
 * @copyright Copyright (C) 2015 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// no direct access
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

$nofollowattr = ($nofollow == 1) ? " rel='nofollow'":"";
if ($params->get('stylebold')) $stylebold = "text-bold";
?>

<?php if(count($friendlist) > 0): ?>
<div class='jOpenSim_friends'>
    <table class="table table-striped table-condensed table-hover">
    <tbody>
        <?php if(is_array($friendlist[1])): ?>
        <?php foreach($friendlist[1] AS $friend): ?>
        <tr>
        <?php
        if ($linkprofile == 1)
        {
            $link1 = "<a href='".JURI::root()."index.php?option=com_opensim&view=profile&uid=".$friend['uid']."' class='".$nofollowattr."'>";
            $link2 = "</a>";
        }
        else
        {
            $link1 = "";
            $link2 = "";
        }
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
        if($linkprofile == 1) {
            $link1 = "<a href='".JURI::root()."index.php?option=com_opensim&view=profile&uid=".$friend['uid']."' class='".$nofollowattr."'>";
            $link2 = "</a>";
        } else {
            $link1 = "";
            $link2 = "";
        }
        ?>
            <td><?php echo $link1.$friend['name'].$link2; ?></td>
            <td class="text-right <?php echo $stylebold; ?>"><?php echo $offlineText; ?></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
    </table>
</div>
<?php endif; ?>
