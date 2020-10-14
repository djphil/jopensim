<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
?>
<?php if($this->retval): ?>
<h3>Functionblock: <?php echo $this->functionblock; ?></h3>
Result:
<pre>
<?php var_dump($this->retval); ?>
</pre>
<?php
$response	= xmlrpc_encode($this->retval);
$response	= str_replace("<","&lt;",$response);
$response	= str_replace(">","&gt;",$response);
?>
response:<br />
<pre>
<?php var_dump($response); ?>
</pre>

<?php else: ?>
no response for <?php echo $this->test; ?> :(<br />
<?php endif; ?>

_REQUEST:<br />
<pre>
<?php var_dump($_REQUEST); ?>
</pre>

Settings Debug:<br />
<pre>
<?php var_dump($this->jdebug); ?>
</pre>

