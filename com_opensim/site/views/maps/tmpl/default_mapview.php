<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2015 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// No direct access
 
defined('_JEXEC') or die('Restricted access');

?>
<style>
	.jgridmap {
		width:<?php echo $this->settingsdata['jopensim_maps_width'].$this->settingsdata['jopensim_maps_width_style']; ?>;
		height:<?php echo $this->settingsdata['jopensim_maps_width'].$this->settingsdata['jopensim_maps_width_style']; ?>;
	}
	#jGoogleLink {
		/*border:#ff0 1px dotted;*/
		display:inline-block;
		width:70px;
		height:40px;
		position:relative;
		top:-26px;
	}
	#jGoogleLink a {
		display:block;
		text-decoration:none;
	}
	table.regioninfo span#name,
	table.regioninfo span#loc {
		color:<?php echo $this->settingsdata['jopensim_maps_bubble_textcolor']; ?>;
	}
	a.jopensim_articlelink:link,
	a.jopensim_articlelink:visited,
	a.jopensim_tplink:link,
	a.jopensim_tplink:visited {
		color:<?php echo $this->settingsdata['jopensim_maps_bubble_linkcolor']; ?>;
	}
</style>
<?php if ($this->showpageheading === TRUE) : ?>
<div class="page-header<?php echo $this->pageclass_sfx; ?>">
	<h1 class="<?php echo $this->pageclass_sfx; ?>"> <?php echo $this->escape($this->pageheading); ?> </h1>
</div>
<?php endif; ?>
<div id="content" class='jgridmap'>
	<table class='jgridmap'>
	<tr>
		<td>
		<div class='jgridmap' id="map-canvas"></div>
		<div class='jGoogleLink' id='jGoogleLink'><a href='https://maps.google.com' target='_blank'>&nbsp;</td></div>
		</td>
	</tr>
	</table>
</div>
<script>
var showCoords = "<?php echo ($this->settingsdata['jopensim_maps_showcoords'] == 1) ? "true":"false"; ?>";
var mapCentreNames = ["<?php echo $this->settingsdata['jopensim_maps_homename']; ?>"];
var jCopyright = "<div class='label label-default'><?php echo $this->settingsdata['jopensim_maps_copyright']; ?></div>";

var xlocations = {
	"world1": <?php echo $this->jmapX; ?>, // primary map centre location (x)
};

var ylocations = {
	"world1": <?php echo $this->jmapY; ?>, // primary map centre location (y)
};

// ## This is especially useful for large regions e.g. varregions ##
var xoffsets = { // if required: default is zero
  "world1": <?php echo $this->jmapXoffset; ?>, // primary offset (number of tiles) SE from centre (x)
};

// ## This is especially useful for large regions e.g. varregions ##
var yoffsets = { // if required: default is zero
  "world1": <?php echo $this->jmapYoffset; ?>, // primary offset (number of tiles) SE from centre (y)
};

var xstart		= <?php echo $this->jmapX; ?>;
var ystart		= <?php echo $this->jmapY; ?>;
var mapxoffset	= <?php echo $this->jmapXoffset; ?>;
var mapyoffset	= <?php echo $this->jmapYoffset; ?>;

var jgridmapwidth = "<?php echo $this->settingsdata['jopensim_maps_width'].$this->settingsdata['jopensim_maps_width_style']; ?>";
var jgridmapheight = "<?php echo $this->settingsdata['jopensim_maps_height'].$this->settingsdata['jopensim_maps_height_style']; ?>";

// initial zoom level (make sure 5 <= zoomStart <= 9): for small grids, try 8; for large grids, try 6
var zoomStart = <?php echo $this->settingsdata['jopensim_maps_zoomstart']; ?>;
var zoomStarts = {
  "world1": <?php echo $this->settingsdata['jopensim_maps_zoomstart']; ?>, // primary zoom start level
};

var jUrlBase		= "<?php echo JUri::base(true); ?>";
var jArticleLink	= "<?php echo $this->settingsdata['jopensim_maps_link2article']; ?>";
var jTeleportLink	= "<?php echo $this->settingsdata['jopensim_maps_showteleport']; ?>";
var jMapWater		= "<?php echo $this->settingsdata['jopensim_maps_water']; ?>";

var infoBubble = new InfoBubble({
	borderRadius: 5,
	arrowSize: 10,
//	color: '#000',
	backgroundColor: '<?php echo $this->settingsdata['jopensim_maps_bubble_color']; ?>'
});



if(window.addEventListener){ // Mozilla, Netscape, Firefox
	window.addEventListener("load", load(), false);
} else { // IE
	window.attachEvent("onload", load());
}

</script>
