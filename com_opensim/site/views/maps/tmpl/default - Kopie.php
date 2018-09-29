<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2015 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// No direct access
 
defined('_JEXEC') or die('Restricted access'); ?>
<script type="text/javascript">
jQuery(function($) {
    $('div.mapzoom')
        .bind('mousewheel', function(event, delta) {
            var dir = delta > 0 ? 'Up' : 'Down',
                vel = Math.abs(delta);
            catchWheel(dir,vel);
            return false;
        });
});
</script>
<style type="text/css">
	a.tip2 {
  position: relative;
  text-decoration: none;
}
img.tip2 span {display: none;}
img.tip2:hover span {
  display: block;
  position: absolute; 
  padding: .5em;
  content: attr(title);
  min-width: 120px;
  text-align: center;
  width: auto;
  height: auto;
  white-space: nowrap;
  top: -32px;
  background: rgba(0,0,0,.8);
  -moz-border-radius:10px;
  -webkit-border-radius:10px;
  border-radius:10px;    
  color: #fff;
  font-size: .86em;
}
img.tip2:hover span:after {
  position: absolute;
  display: block;
  content: "";  
  border-color: rgba(0,0,0,.8) transparent transparent transparent;
  border-style: solid;
  border-width: 10px;
  height:0;
  width:0;
  position:absolute;
  bottom: -16px;
  left:1em;
}
	</style>
<h1><?php echo JText::_('MAP_TITLE'); ?></h1>
<?php echo $this->maptable; ?>
<input type='hidden' name='cellX' id='cellX' value='<?php echo $this->cellX; ?>' />
<input type='hidden' name='cellY' id='cellY' value='<?php echo $this->cellY; ?>' />
<input type='hidden' name='homeX' id='homeX' value='<?php echo $this->settingsdata['mapcenter_offsetX']; ?>' />
<input type='hidden' name='homeY' id='homeY' value='<?php echo $this->settingsdata['mapcenter_offsetY']; ?>' />
<script type="text/javascript">
var defaultscalefactor = <?php echo $this->settingsdata['map_defaultsize']; ?>;
var scalefactor = defaultscalefactor;
var initMap = false;
var map_minsize = <?php echo $this->settingsdata['map_minsize']; ?>;
var map_maxsize = <?php echo $this->settingsdata['map_maxsize']; ?>;
var map_zoomstep = <?php echo $this->settingsdata['map_zoomstep']; ?>;
var delta;
$(function() {
	scaleMaps(scalefactor);
});

// centerMapPos();
</script>
<input style='position: relative; top:-25px; left:10px;' type='image' src='<?php echo $this->assetpath; ?>images/home.png' style='background:none;' id='homebutton' onClick='centerMapPos();' />
<input type='button' style='position: relative; top:-27px; left:15px;' id='resetbutton' value='reset' onClick='resetMap();' />
<input type='button' style='position: relative; top:-27px; left:15px;' id='resetbutton' value='+' onClick='zoomMap("in");' />
<input type='button' style='position: relative; top:-27px; left:15px;' id='resetbutton' value='-' onClick='zoomMap("out");' />
