<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
?>

<p><?php echo $this->partneraccepttext; ?></p>

<p>
    <a class="btn btn default btn-success" href='index.php?option=com_opensim&view=inworld&task=partnerrequesthandler&accept=yes&partneruuid=<?php echo $this->partneruuid; ?>&Itemid=<?php echo $this->Itemid; ?>' target='_parent'>
        <?php echo JText::_('JOPENSIM_PROFILE_PARTNER_REQUEST_ACCEPT_YES'); ?>
    </a>
</p>
<p>
    <a class="btn btn default btn-warning" href='Javascript:window.parent.SqueezeBox.close();'>
        <?php echo JText::_('JOPENSIM_PROFILE_PARTNER_REQUEST_ACCEPT_MAYBE'); ?>
    </a>
</p>
<p>
    <a class="btn btn default btn-warning" href='index.php?option=com_opensim&view=inworld&task=partnerrequesthandler&accept=no&partneruuid=<?php echo $this->partneruuid; ?>&Itemid=<?php echo $this->Itemid; ?>' target='_parent'>
        <?php echo JText::_('JOPENSIM_PROFILE_PARTNER_REQUEST_ACCEPT_NO'); ?>
    </a>
</p>
<p>
    <a class="btn btn default btn-danger" href='index.php?option=com_opensim&view=inworld&task=partnerrequesthandler&accept=never&partneruuid=<?php echo $this->partneruuid; ?>&Itemid=<?php echo $this->Itemid; ?>' target='_parent'>
        <?php echo JText::_('JOPENSIM_PROFILE_PARTNER_REQUEST_ACCEPT_NEVER'); ?>
    </a>
</p>

<!--
<button type="button" onclick="window.parent.SqueezeBox.close();"><?php // echo JText::_('JOPENSIM_PROFILE_PARTNER_DIVORCE_NO'); ?></button>
<input type='submit' class='button art-button' value='<?php // echo JText::_('JOPENSIM_PROFILE_PARTNER_DIVORCE_NO'); ?>' onClick='window.parent.SqueezeBox.close();' />
-->