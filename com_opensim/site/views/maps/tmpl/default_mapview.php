<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2017 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access'); // No direct access
?>

<style>
.jgridmap {
    width:<?php echo $this->settingsdata['jopensim_maps_width'].$this->settingsdata['jopensim_maps_width_style']; ?>;
    height:<?php echo $this->settingsdata['jopensim_maps_width'].$this->settingsdata['jopensim_maps_width_style']; ?>;
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
div#map-canvas {
    background-image:url("<?php echo $this->settingsdata['jopensim_maps_water']; ?>");
    /*background-size:cover;*/
    /*background-size:contain;*/
    background-size:<?php echo $this->backgroundsize; ?>;
    background-repeat:<?php echo $this->backgroundrepeat; ?>;
    background-position: center; 
}
</style>

<?php if ($this->showpageheading === TRUE) : ?>
<div class="page-header<?php echo $this->pageclass_sfx; ?>">
	<h1 class="<?php echo $this->pageclass_sfx; ?>"> <?php echo $this->escape($this->pageheading); ?> </h1>
</div>
<?php endif; ?>

<div id="content" class="jgridmap">
    <div class="jgridmap" id="map-canvas"></div>
    <div class='jGoogleLink' id='jGoogleLink'>
        <a href='https://maps.google.com' target='_blank'></a>
    </div>
</div>

<script>
var showCoords = "<?php echo ($this->settingsdata['jopensim_maps_showcoords'] == 1) ? "true":"false"; ?>";
var mapCentreNames = ["<?php echo $this->settingsdata['jopensim_maps_homename']; ?>"];
var jCopyright = "<div class='label label-default'><?php echo $this->settingsdata['jopensim_maps_copyright']; ?></div>";

// primary map centre location (x)
var xlocations = {"world1": <?php echo $this->jmapX; ?>, };

// primary map centre location (y)
var ylocations = {"world1": <?php echo $this->jmapY; ?>, };

// ## This is especially useful for large regions e.g. varregions ##
// if required: default is zero
// primary offset (number of tiles) SE from centre (x)
var xoffsets = {"world1": <?php echo $this->jmapXoffset; ?>, };

// ## This is especially useful for large regions e.g. varregions ##
// if required: default is zero
// primary offset (number of tiles) SE from centre (y)
var yoffsets = {"world1": <?php echo $this->jmapYoffset; ?>, };

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
var jArticleIcon	= "<?php echo $this->settingsdata['jopensim_maps_link2article_icon']; ?>";
var jTeleportLink	= "<?php echo $this->settingsdata['jopensim_maps_showteleport']; ?>";
var jMapWater		= "<?php echo $this->settingsdata['jopensim_maps_water']; ?>";

var infoBubble = new InfoBubble({
	borderRadius: 3,
	arrowSize: 10,
    // color: '#000',
	backgroundColor: '<?php echo $this->settingsdata['jopensim_maps_bubble_color']; ?>'
});

// Mozilla, Netscape, Firefox
if (window.addEventListener) {window.addEventListener("load", load(), false);}
else {window.attachEvent("onload", load());} // IE
</script>
