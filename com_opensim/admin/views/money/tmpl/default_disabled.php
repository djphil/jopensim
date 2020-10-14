<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');
?>
<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
<table width='600'>
<tr>
	<td>
	<p>jOpenSimMoney is an extension specific addon for jOpenSim for an easy inworld currency handling in your grid. While the functions for jOpenSimMoney inside jOpenSim are free, it depends on an additional module for OpenSim written in C# which is available for a small annual fee at <a href='http://www.jopensim.com/jopensimmoney.html' target='_blank'>http://www.jopensim.com</a>.</p>
	<p><b>Be sure to <a href='http://www.jopensim.com/downloads1.html' target='_blank'>download the module</a> and test it in DEMO mode before purchasing.</b></p>
	<p>So far, following functions are implemented:</p>
	<ul>
		<li>buy and sell land</li>
		<li>buy and sell objects</li>
		<li>give money</li>
		<li>upload fee</li>
		<li>group creation fee</li>
		<li>group enrollment fee</li>
		<li>support in scripts for &quot;llGiveMoney()&quot;</li>
	</ul>
	<p>Following functions are under construction and planned to be implement in the first release:</p>
	<ul>
		<li>fee for &quot;classified&quot; in profile</li>
		<li>fee for &quot;Show Place in Search&quot; in land options</li>
		<li>cover fee for events</li>
		<li>fee for &quot;partnering&quot;</li>
	</ul>
	<p>Additionally there is another free component available, called <b>jOpenSimPayPal</b> that works only if jOpenSim is installed to handle instant PayPal payments for your inworld currency!</p>
	</td>
</tr>
</table>
</div>