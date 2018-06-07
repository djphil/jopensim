<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2018 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
?>
<h1><?php echo JText::_('JOPENSIM_AUTHORIZE_HGTITLE'); ?></h1>
<form action='index.php' name='confirmHGage' method='post'>
<input type='hidden' name='option' value='com_opensim' />
<input type='hidden' name='view' value='auth' />
<input type='hidden' name='task' value='confirmHG' />
<input type='hidden' name='hguser' value='<?php echo $this->hguser; ?>' />
<p><?php echo JText::sprintf('JOPENSIM_AUTHORIZE_HGUSERMESSAGE',$this->minage); ?></p>
<input type='submit' name='confirmage' value='<?php echo JText::_('JYES'); ?>' />
<input type='submit' name='confirmage' value='<?php echo JText::_('JNO'); ?>' />
</form>