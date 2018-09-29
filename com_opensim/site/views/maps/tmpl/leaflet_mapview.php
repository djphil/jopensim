<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2018 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access'); // No direct access
?>
<style>
.jopensimleafletmap {
    width:<?php echo $this->settingsdata['jopensim_maps_width'].$this->settingsdata['jopensim_maps_width_style']; ?>;
    height:<?php echo $this->settingsdata['jopensim_maps_height'].$this->settingsdata['jopensim_maps_height_style']; ?>;
}

.jmapregionname,
.jopensim_map_coords,
.leaflet-popup-close-button,
.leaflet-popup-content {
    color:<?php echo $this->settingsdata['jopensim_maps_bubble_textcolor']; ?> !important;
}
a.jopensim_articlelink:link,
a.jopensim_articlelink:visited {
    color:<?php echo $this->settingsdata['jopensim_maps_bubble_linkcolor']; ?>;
}
.leaflet-container {
    background-image:url("<?php echo $this->settingsdata['jopensim_maps_water']; ?>");
    /*background-size:cover;*/
    /*background-size:contain;*/
    background-size:<?php echo $this->backgroundsize; ?>;
    background-repeat:<?php echo $this->backgroundrepeat; ?>;
    background-position: center; 
}

.leaflet-popup-content-wrapper,
.leaflet-popup-tip {
	background-color: <?php echo $this->settingsdata['jopensim_maps_bubble_color']; ?>;
}

.leaflet-control-layers-toggle {
    background-image: url(<?php echo $this->layerBackground; ?>) !important;
}

a.jopensimmapsearch_classified::before {
	font-family: 'IcoMoon';
	content: "\63";
	padding-right:3px;
}

a.jopensimmapsearch_region:before {
	font-family: 'IcoMoon';
	content: "\58";
	padding-right:3px;
}

a.jopensimmapsearch_event:before {
	font-family: 'IcoMoon';
	content: "\43";
	padding-right:3px;
}



a.tpbtn:link {
	color:#fff;
}
</style>

<div id="map" class="jopensimleafletmap"></div>

<script>

function onMapClick(e) {
	regionX = Math.floor(e.latlng.lat);
	regionY = Math.floor(e.latlng.lng);
	regionTPx = Math.floor((e.latlng.lat - regionX)*256);
	regionTPy = Math.floor((e.latlng.lng - regionY)*256);
	if(regionAddX[regionX+"-"+regionY]) regionTPx += regionAddX[regionX+"-"+regionY];
	if(regionAddY[regionX+"-"+regionY]) regionTPy += regionAddY[regionX+"-"+regionY];
	if(regionname[regionX+"-"+regionY]) {
		if(showTP) {
			tpLinks = getTPlinks(regionname[regionX+"-"+regionY],regionTPx,regionTPy,0);
			tpLocal	= getTPbutton(tpLinks.tplocal,"local");
			tpHG	= getTPbutton(tpLinks.tphg,"hg");
			tpHGV3	= getTPbutton(tpLinks.tphgv3,"hgv3");
			tpHOP	= getTPbutton(tpLinks.tphop,"hop");
//			alert(tpHG);
			regionTPlink = "<br /><div class='jopensim_showcase'>"+tpLocal+tpHG+tpHGV3+tpHOP+"</div>";
//			regionTPlink = "<br /><a href='secondlife://"+regionname[regionX+"-"+regionY]+"/"+regionTPx+"/"+regionTPy+"' class='btn btn-primary jopensim_map_tplink'><i class='icon-ok'></i><?php echo JText::_('JOPENSIM_MAPS_TELEPORT'); ?></a>";
		} else {
			regionTPlink = "";
		}
		if(showCoords) {
			coordString = "<br /><span class='jopensim_map_coords'><?php echo JText::_('JOPENSIM_MAPS_COORDS'); ?> <span class=\"label label-danger\" id=\"locX\">X:</span> "+regionY+" <span class=\"label label-success\">Y:</span> "+regionX+'</span>';
		} else {
			coordString = "";
		}
		popupstring = "<span class=jmapregionname>"+regionname[regionX+"-"+regionY]+"</span><br />"+coordString+regionTPlink;

		popup
			.setLatLng(e.latlng)
			.setContent(popupstring);

		map.openPopup(popup);
	} else {
		// do eventually something on water??? ;)
		// popupstring = "Click on:<br />x: "+regionX+"<br />y: "+regionY+"<br />No region here ;)";
	}
}

function getTPlinks(regionname,posX,posY,posZ) {
	var maphost	= "<?php echo $this->host; ?>";
	var mapport	= "<?php echo $this->port; ?>";
	linklocal	= "secondlife:/"+"/"+regionname+"/"+posX+"/"+posY+"/"+posZ;
	linkhg		= "secondlife:/"+"/"+maphost+":"+mapport+":"+regionname+"/"+posX+"/"+posY+"/"+posZ;
	linkhgv3	= "secondlife:/"+"/http|!!"+maphost+"|"+mapport+"+"+regionname.replace(/ /g,"+")+"/"+posX+"/"+posY+"/"+posZ;
	linkhop		= "hop:/"+"/"+maphost+":"+mapport+":"+regionname+"/"+posX+"/"+posY+"/"+posZ;
	tpLinks		= {
		tplocal:	linklocal,
		tphg:		linkhg,
		tphgv3:		linkhgv3,
		tphop:		linkhop
	};
	return tpLinks;
}

function getTPbutton(tplink,linktype) {
	switch(linktype) {
		case "hg":
			return "<div class='tphg'><a class='tpbtn btn-hg' href='"+tplink+"'>HG</a></div>";
		break;
		case "hgv3":
			return "<div class='tphgv3'><a class='tpbtn btn-hgv3' href='"+tplink+"'>HGV3</a></div>";
		break;
		case "hop":
			return "<div class='tphop'><a class='tpbtn btn-tphop' href='"+tplink+"'>HOP</a></div>";
		break;
		default:
			return "<div class='tplocal'><a class='tpbtn btn-local' href='"+tplink+"'>Local</a></div>";
		break;
	}
}
<?php if(is_array($this->regions) && count($this->regions) > 0): ?>
var regionname = {
<?php
$regionAddX		= array();
$regionAddY		= array();
$regionImage	= array();

// parse regions as JS arrays

// 1. the name
foreach($this->regions AS $region) {
	if(!isset($maxnorth) || $maxnorth < ($region['locY'] / 256)) $maxnorth = $region['locY'] / 256;
	if(!isset($maxsouth) || $maxsouth > ($region['locY'] / 256)) $maxsouth = $region['locY'] / 256;
	if(!isset($maxwest) || $maxwest > ($region['locX'] / 256)) $maxwest = $region['locX'] / 256;
	if(!isset($maxeast) || $maxeast < ($region['locX'] / 256)) $maxeast = $region['locX'] / 256;
	if($region['sizeX'] > 256) {
		$sizeMultiplyer = $region['sizeX'] / 256;
		for($x = 0; $x < $sizeMultiplyer; $x++) {
			for($y = 0; $y < $sizeMultiplyer; $y++) {
				$coordstring = (($region['locY'] / 256)+$y)."-".(($region['locX'] / 256)+$x);
				echo "\t\"".$coordstring."\":\"".$region['regionName']."\",\n";
				if($x > 0) $regionAddX[$coordstring] = $x * 256;
				if($y > 0) $regionAddY[$coordstring] = $y * 256;
				$regionImage[$coordstring] = JUri::base(true)."/images/jopensim/regions/varregions/map-1-".(($region['locX'] / 256)+$x)."-".(($region['locY'] / 256)+$y)."-objects.jpg";
			}
		}
	} else {
		$coordstring = ($region['locY'] / 256)."-".($region['locX'] / 256);
		echo "\t\"".$coordstring."\":\"".$region['regionName']."\",\n";
		$regionImage[$coordstring] = JUri::base(true)."/images/jopensim/regions/".$region['uuid'].".jpg";
	}
	
}
?>
	"xxx-xxx":"end_dummy"
};
<?php else: ?>
<?php endif; ?>
// 2. the region image
var regionImage = {
<?php
if(count($regionImage) > 0) {
	foreach($regionImage AS $key => $val) echo "\t\"".$key."\":\"".$val."\",\n";
}
?>
	"xxx-xxx":"end_dummy"
}
// adding x-coords for varregions to tp links?
var regionAddX = {
<?php
if(count($regionAddX) > 0) {
	foreach($regionAddX AS $key => $val) echo "\t\"".$key."\":".$val.",\n";
}
?>
	"xxx-xxx":"end_dummy"
}

// adding y-coords for varregions to tp links?
var regionAddY = {
<?php
if(count($regionAddY) > 0) {
	foreach($regionAddY AS $key => $val) echo "\t\"".$key."\":".$val.",\n";
}
?>
	"xxx-xxx":"end_dummy"
}

// max dimensions of map
var maxnorth	= <?php echo $maxnorth; ?>;
var maxsouth	= <?php echo $maxsouth; ?>;
var maxwest		= <?php echo $maxwest; ?>;
var maxeast		= <?php echo $maxeast; ?>;

// settings
var zoomStart	= <?php echo $this->zoomStart; ?>;
var mouseZoom	= <?php echo $this->mousezoom; ?>;
var xstart		= <?php echo ($this->jmapX + 0.5 + $this->jmapXoffset); ?>;
var ystart		= <?php echo ($this->jmapY + 0.5 + $this->jmapYoffset); ?>;
var mapxoffset	= <?php echo $this->jmapXoffset; ?>;
var mapyoffset	= <?php echo $this->jmapYoffset; ?>;
var jCopyright	= "<?php echo $this->settingsdata['jopensim_maps_copyright']; ?>";
var showTP		= <?php echo ($this->jopensim_maps_showteleport == 1) ? "true":"false"; ?>;
var showCoords	= <?php echo ($this->jopensim_maps_showcoords == 1) ? "true":"false"; ?>;
var searchPlaceholder		= "<?php echo JText::_('JOPENSIM_MAP_SEARCHTEXT'); ?>";

var classifiedsMarker		= <?php echo $this->markerClassified; ?>;
var classifiedMarkerDummy	= '<?php echo $this->iconDummy; ?>';
<?php if($this->markerClassified && is_array($this->classifieds) && count($this->classifieds) > 0): ?>
var classifiedCatNames		= [
								'<?php echo JText::_('JOPENSIM_MARKERS_CLASSIFIED'); ?>',
								'<?php echo JText::_('JOPENSIM_MARKERS_CLASSIFIED_1'); ?>',
								'<?php echo JText::_('JOPENSIM_MARKERS_CLASSIFIED_2'); ?>',
								'<?php echo JText::_('JOPENSIM_MARKERS_CLASSIFIED_3'); ?>',
								'<?php echo JText::_('JOPENSIM_MARKERS_CLASSIFIED_4'); ?>',
								'<?php echo JText::_('JOPENSIM_MARKERS_CLASSIFIED_5'); ?>',
								'<?php echo JText::_('JOPENSIM_MARKERS_CLASSIFIED_6'); ?>',
								'<?php echo JText::_('JOPENSIM_MARKERS_CLASSIFIED_7'); ?>',
								'<?php echo JText::_('JOPENSIM_MARKERS_CLASSIFIED_8'); ?>',
								'<?php echo JText::_('JOPENSIM_MARKERS_CLASSIFIED_9'); ?>'
								];
var classifiedMarkerIcon1	= '<?php echo $this->iconClassifiedShopping; ?>';
var classifiedMarkerIcon2	= '<?php echo $this->iconClassifiedLandrental; ?>';
var classifiedMarkerIcon3	= '<?php echo $this->iconClassifiedPropRental; ?>';
var classifiedMarkerIcon4	= '<?php echo $this->iconClassifiedAttraction; ?>';
var classifiedMarkerIcon5	= '<?php echo $this->iconClassifiedProducts; ?>';
var classifiedMarkerIcon6	= '<?php echo $this->iconClassifiedEmployment; ?>';
var classifiedMarkerIcon7	= '<?php echo $this->iconClassifiedWanted; ?>';
var classifiedMarkerIcon8	= '<?php echo $this->iconClassifiedService; ?>';
var classifiedMarkerIcon9	= '<?php echo $this->iconClassifiedPersonal; ?>';
var classifiedMarker = {
<?php
// Thank you very much Javascript not to support multidimensional arrays :(
// this object contains the position for the marker
foreach($this->classifieds AS $classified) {
	echo "\t\"".$classified['name']."\": [".$classified['markerY'].", ".$classified['markerX']."],\n";
}
?>
	"xxx-xxx":"end_dummy"
}
var classifiedCat = {
<?php
// this object contains the category for the marker
foreach($this->classifieds AS $classified) {
	echo "\t\"".$classified['name']."\": ".$classified['category'].",\n";
}
?>
	"xxx-xxx":"end_dummy"
}
var classifiedDesc = {
<?php
// this object contains the description for the marker popup
foreach($this->classifieds AS $classified) {
	echo "\t\"".$classified['name']."\": \"".str_replace("\n","<br />",str_replace("\"","&quot;",$classified['description']))."\",\n";
}
?>
	"xxx-xxx":"end_dummy"
}

var classifiedTpLocal = {
<?php
// this object contains the local tp link for the marker popup
foreach($this->classifieds AS $classified) {
	echo "\t\"".$classified['name']."\": \"".str_replace("\"","&quot;",$classified['linklocal'])."\",\n";
}
?>
	"xxx-xxx":"end_dummy"
}
var classifiedTpHG = {
<?php
// this object contains the hg tp link for the marker popup
foreach($this->classifieds AS $classified) {
	echo "\t\"".$classified['name']."\": \"".str_replace("\"","&quot;",$classified['linkhg'])."\",\n";
}
?>
	"xxx-xxx":"end_dummy"
}
var classifiedTpHGv3 = {
<?php
// this object contains the hg v3 tp link for the marker popup
foreach($this->classifieds AS $classified) {
	echo "\t\"".$classified['name']."\": \"".str_replace("\"","&quot;",$classified['linkhgv3'])."\",\n";
}
?>
	"xxx-xxx":"end_dummy"
}
var classifiedTpHop = {
<?php
// this object contains the hop tp link for the marker popup
foreach($this->classifieds AS $classified) {
	echo "\t\"".$classified['name']."\": \"".str_replace("\"","&quot;",$classified['linkhop'])."\",\n";
}
?>
	"xxx-xxx":"end_dummy"
}


<?php endif; ?>
var eventsMarker = <?php echo $this->markerEvents; ?>;
var eventMarkerIcon = '<?php echo $this->iconEvents; ?>';
var eventMartkerTimeText = "<?php echo JText::_('JOPENSIM_MARKERS_EVENTS_STARTING'); ?>";
var eventMartkerDurationText = "<?php echo JText::_('JOPENSIM_MARKERS_EVENTS_DURATION'); ?>";
<?php if($this->markerEvents && is_array($this->events) && count($this->events) > 0): ?>

var eventMarker = {
<?php
// this object contains the position for the marker
foreach($this->events AS $event) {
	echo "\t\"".$event['eventid']."\": [".$event['markerY'].", ".$event['markerX']."],\n";
}
?>
	"xxx-xxx":"end_dummy"
}

var eventMarkerName = {
<?php
// this object contains the name for the marker
foreach($this->events AS $event) {
	echo "\t\"".$event['eventid']."\": \"".str_replace("\"","&quot;",$event['name'])."\",\n";
}
?>
	"xxx-xxx":"end_dummy"
}

var eventMarkerDesc = {
<?php
// this object contains the description for the marker
foreach($this->events AS $event) {
	echo "\t\"".$event['eventid']."\": \"".str_replace("\"","&quot;",$event['description'])."\",\n";
}
?>
	"xxx-xxx":"end_dummy"
}
var eventMarkerTime = {
<?php
// this object contains the starting time for the marker
foreach($this->events AS $event) {
	echo "\t\"".$event['eventid']."\": \"".$event['userdatetime']."\",\n";
}
?>
	"xxx-xxx":"end_dummy"
}
var eventMarkerDuration = {
<?php
// this object contains the duration for the marker
foreach($this->events AS $event) {
	echo "\t\"".$event['eventid']."\": \"".$event['duration']."\",\n";
}
?>
	"xxx-xxx":"end_dummy"
}

var eventTpPos = {
<?php
// this object contains the duration for the marker
foreach($this->events AS $event) {
	echo "\t\"".$event['eventid']."\": \"".$event['surl']."\",\n";
}
?>
	"xxx-xxx":"end_dummy"
}
<?php endif; ?>









if (window.addEventListener) {window.addEventListener("load", initmap(), false);} // Mozilla, Netscape, Firefox
else {window.attachEvent("onload", initmap());} // IE


</script>
<!--
<pre>
<?php
//echo "maxnorth: ".$maxnorth."\n";
//echo "maxsouth: ".$maxsouth."\n";
//echo "maxwest: ".$maxwest."\n";
//echo "maxeast: ".$maxeast."\n";
//var_dump($this->events);
?>
</pre>
-->
