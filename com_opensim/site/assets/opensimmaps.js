// Copyright 2012-14 donated to OSGrid.org, under BSD licence, see http://forge.opensimulator.org/gf/project/opensimwi/ for details.
// This code updates the Google Maps module found there from API v2 to API v3, but can be used without the OpenSim Web Interface. It also
// adds new functionality e.g. new hypergrid teleport formats and multiple map centres and/or multiple grids. It also has a draggable marker.

// Copyright 2014 donated to OSGrid.org added new features such as support for large regions e.g. varregions in OpenSim/WhiteCore/Aurora, 
// offsets from map centre, zoom levels for each  map centre, automatic copyright start/end years, better home centering per map centre.

// ########## GRID SPECIFIC VARIABLES, CHANGE THESE AS REQUIRED ##########


// TO DO Language translation
var region_coordinates = "Coordinates";
var region_uuid = "Region UUID";
var region_location = "Location";
var region_home = "Home";
var region_home_click = "Click to set the map to Home";
var viewer_restriction = "Viewer may restrict login within SE 256x256 corner of larger regions in OpenSim";


var defaultMap = 'world1'; // must be in the lists below, usually the first

// Note that the index labels, e.g. "world1", "world2" etc MUST be the same in each section where they appear,
// so it is recommended that you use this indexing pattern, e.g. add "world3", "world4" etc as necessary.
// Note that you will need local copies of the maptiles or be able to make (soft) links to those in [opensim]/bin/maptiles
// but if you use local copies you will need to update them manually or otherwise. See also the filename setting below.


var copyrightNotices = [ // these may be different for each map, e.g. if for multiple worlds
  "jOpenSimWorld", // primary copyright notice
//  "Chili", // secondary copyright notice
//  "jOpenSimWorld2",
  // ... add more lines as required, separated by commas
];

var hgdomains = { // these may be different for each map, e.g. if for multiple worlds
//  "world1": "opensim.example.com", // primary hypergrid domain
//  "world2": "otherworld.example.com", // secondary hypergrid domain
  // .. add more lines as required, separated by commas
};

var hgports = { // these may be different for each map, e.g. if for multiple worlds
//  "world1": "8002", // primary hypergrid port
//  "world2": "80", // secondary hypergrid port
  // ... add more lines as required, separated by commas
};

var port80 = 1; // Where default port 80 is specified, include explicitly in link (boolean).

var copyrightStartYear = 0;
var copyrightEndYear = -1; // 0 = current year, -1 = no end year

// ########## DON'T USUALLY CHANGE THIS ##########
// This setting determines the names of the jpg files. They can be the UUIDs of the regions, or the format 
// used in [opensim]/bin/maptiles or, though not sure you'd want this, UUIDs with dashes removed. This is left 
// in to enable compatibility with the v2 code but it is better to use the proper UUID format with dashes retained.

var filenames = "uuid"; // default is "opensim", otherwise use "uuid" or "uuid-no-dashes"

var showUUID = "false"; // Default is "false", setting to "true" will show the region UUID in the infoWindow


// ########## ONLY SOFTWARE DEVELOPERS BELOW THIS LINE ##########

// ##### VARIABLES #####

// ## Set up variables for region location ##
var xjump;
var yjump;
var __items;

// ## Set up variables for region size ##
var sizeX;
var sizeY;
var xsizes = new Array();
var ysizes = new Array();

// ## Set up variables for the map ##
var map;

var latLng = new google.maps.LatLng(11, 11);

var mapTypes = new Array();
var mapTypesCount;

// ## Set up variables for the map overlay ##
var groundOverlayOptions = {map: map, clickable: true, opacity: 1.0};
var layer = new Array();
var layerCount = 0;

// ## Set up options for the marker ##
var markerTitle = "Location";

var marker = new google.maps.Marker({
  position: latLng,
  title: markerTitle,
  map: map,
  draggable: true,
  animation: google.maps.Animation.DROP,
});

var infoWindow = new google.maps.InfoWindow;
var copyrights = {}, id;
var copyrightNode;

// ##### MAP TYPES #####

// ## Set options for the standard map type ##
function plainMapType(name) {
  this.tileSize = new google.maps.Size(192, 192);
  this.maxZoom = 9;
  this.minZoom = 5;
  this.name = name;
  this.alt = "Change map to "+name;
}

// ## Set up div for the standard map type ##
plainMapType.prototype.getTile = function(coord, zoom, ownerDocument) {
  var div = ownerDocument.createElement('div');
  //div.innerHTML = coord;
  div.style.width = this.tileSize.width + 'px';
  div.style.height = this.tileSize.height + 'px';
  div.style.fontSize = '10';
  div.style.borderStyle = 'none';
  div.style.borderWidth = '0px';
  // div.style.borderColor = '#AAAAAA';
  return div;
};

// ## Set up options for the grid lines map type ##
// THIS MAP TYPE IS NOT CURRENTLY USED BECAUSE THE GRID DOESN'T MATCH THE TILES :-(
// Also need to make the map type selectable in the user settings.
function coordMapType(name) {
  this.tileSize = new google.maps.Size(192, 192);
  this.maxZoom = 9;
  this.minZoom = 5;
  this.name = name;
  this.alt = 'Tile coordinate custom map type';
}

// ## Set up div for the grid lines map type ##
// THIS MAP TYPE IS NOT CURRENTLY USED BECAUSE THE GRID DOESN'T MATCH THE TILES :-(
// Also need to make the map type selectable in the user settings.
coordMapType.prototype.getTile = function(coord, zoom, ownerDocument) {
  var div = ownerDocument.createElement('div');
  div.innerHTML = "(" + (coord.x - 180 + xstart) + "," + (-coord.y + 160 + ystart) + ")";
  div.style.width = this.tileSize.width + 'px';
  div.style.height = this.tileSize.height + 'px';
  div.style.fontSize = '12';
  div.style.borderStyle = 'none';
  div.style.borderWidth = '1px';
  // div.style.borderColor = '#AAAAAA';
  return div;
};

// ##### MAIN FUNCTION #####

function load() {

  // #### Initialise map ####

  // ## Set up div for tiles - but since we use overlays, consider replacing this entirely? ##
  var div = document.getElementById("map-canvas");
  if(div==null){return;}
//  if(window.innerWidth){
//    div.style.width  = (window.innerWidth - 20) + "px";
//    div.style.height = (window.innerHeight - 30) + "px";
//    div.style.backgroundImage = 'url(water.jpg)';
//  }else{
//    div.style.width  = (document.documentElement.clientWidth - 20) + "px";
//    div.style.height = (document.documentElement.clientHeight - 30) + "px";
//    // ## This may not be the best method but it works. Consider using the proper image method. ##
//    div.style.backgroundImage = 'url(water.jpg)';
//  }

    div.style.width  = jgridmapwidth;
    div.style.height = jgridmapheight;
//    div.style.min-height = "300px";
//    div.style.backgroundImage = 'url(' + jMapWater + ')';

  // ## This is legacy v2 code left here for development information only: consider ##
  // ## removing when image method complete (see previous comment). ##
  // var tilelayers = [new GTileLayer(copyCollection, 5, 9)];
  // tilelayers[0].getTileUrl = function CustomGetTileUrl(a,b){
  //    return "default.jpg";
  //}

  // ## use the index labels in xlocations to create the list of world centres for maps ##
  mapTypesCount = 0;
		for (key in xlocations) {
			++mapTypesCount;
			mapTypes.push(key);
		}

  // ## Set options for map ##
  var mapOptions = {
    zoom: zoomStart,
    center: latLng,
    streetViewControl: false,
    //mapTypeId: google.maps.MapTypeId.ROADMAP, // can be useful for testing
    mapTypeControlOptions: {
      //mapTypeIds: ['world1', 'world2', google.maps.MapTypeId.ROADMAP] // for reference, old hard-coded mapTypeIds
      mapTypeIds: mapTypes
    }
  };

  // ## Initialise map ##
  map = new google.maps.Map(document.getElementById('map-canvas'), 
      mapOptions);
    // Fix for background image (01/01/2017)
    // keep the next div transparent in order to can see the custom background image
	var nextdiv = div.firstChild;
	nextdiv.style.backgroundColor = "transparent";

//	alert("xoffset: "+-mapxoffset*184+"\nmapyoffset: "+mapyoffset*184);
    map.panBy(-mapxoffset*184,mapyoffset*184); // Not sure why 184 pixels is right but it is!

  // ## New method of setting mapTypeIds from user settings ##
  for (i = 0; i < mapTypesCount; ++i) {
    map.mapTypes.set(mapTypes[i], new plainMapType(mapCentreNames[i]));
  }
  map.setMapTypeId(defaultMap);

  // #### Fetch region data on initialise ####
  var request = getHTTPObject();
  if(request){
    request.onreadystatechange = function(){
      parseMapResponse(request,map);
    };
    request.open("GET", jUrlBase+"/components/com_opensim/map.php", true);
    request.send(null);
  }

  // ## Listener for re-initialising map on map type change ##
  // This resets xstart, ystart and re-draws the tiles for a different map
  google.maps.event.addListener(map, 'maptypeid_changed', function(event) {
    infoBubble.close(); marker.setMap(null); // this removes the marker and infoWindow for the old world centre
    for (i = 0; i < layerCount + 1; i++) // this loop removes the old overlay tiles
    {
      if (layer[i] != undefined) {
        layer[i].setMap(null)
      }
    }
    layerCount = 0; // restarts the count of overlay tiles
    xstart = xlocations[map.getMapTypeId()]; // gets the x location for the tiles
    ystart = ylocations[map.getMapTypeId()]; // gets the y location for the tiles
    zoomStart = zoomStarts[map.getMapTypeId()]; // fetches the zoom start level for the map type
    map.setZoom(zoomStart); // sets the zoom start level

    // ## Offset the centre from xlocation, ylocation ##
    var xoffset = xoffsets[map.getMapTypeId()];
    var yoffset = yoffsets[map.getMapTypeId()];
    map.setCenter(latLng);
//	alert("xoffset: "+-xoffset*184+"\nyoffset: "+yoffset*184);
    map.panBy(-xoffset*184,yoffset*184); // Not sure why 184 pixels is right but it is!

    request = getHTTPObject();
    if(request){
      request.onreadystatechange = function(){
        parseMapResponse(request,map);
      };
      request.open("GET",jUrlBase+"/components/com_opensim/map.php",false);
      request.send(null);
    }
  });

  // #### Create the div to hold the control and call the HomeControl() to create control ####
  var homeControlDiv = document.createElement('div');
  var homeControl = new HomeControl(homeControlDiv, map);
  
  homeControlDiv.index = 1;
  map.controls[google.maps.ControlPosition.TOP_RIGHT].push(homeControlDiv);

  // #### Listeners for map events ####

  // ## Listener for map click event ##
  google.maps.event.addListener(map, 'click', function(event) {
    var clickLatLng = event.latLng;
    var x = clickLatLng.lng();
    var y = clickLatLng.lat();
    //if(overlay){return;}
    //var x = point.lng();
    // ## New code for getting varregion sizes ##
    sizeX = ysizes[map.getMapTypeId()]; // gets the x size for the tiles
    sizeY = ysizes[map.getMapTypeId()]; // gets the y size for the tiles
    // ## End of new code ##
    xjump = Math.round(256 * (x - (x | 0)));
    // ## Purpose of next line unclear?  ##
    if(x < 0) x--;		
    var str = x.toString();		
    str = str.substring(0, str.indexOf(".", 0));
    x = xstart - 10 + parseInt(str);
    //var y = point.lat();			
    yjump = Math.round(256 * (y - (y | 0)));
    // ## Purpose of next line unclear?  ##
    if(y < 0) y--;	
    str = y.toString();
    str = str.substring(0, str.indexOf(".", 0));
    y = ystart - 10 + parseInt(str);
    // ## New code for enabling varregions ##
    var xbigger = (x-xstart);
    var ybigger = (y-ystart);
    var xtilebigger = (sizeX/256)-1;
    var ytilebigger = (sizeY/256)-1;
    if (xbigger > 0 && xbigger <= xtilebigger) {
      xjump = xjump + (xbigger*256);
      x = xstart;
    }
    if (ybigger > 0 && ybigger <= ytilebigger) {
      yjump = yjump + (ybigger*256);
      y = ystart;
    }
    // ## End of new code ##
    if(isOutOfBounds(x,y)){return;}
    //show info popup if a region exists
    var content = getRegionInfos(x,y);
//	alert("content: "+content);
    if(content!=""){
      placeMarker(clickLatLng);
      infoBubble.setContent(content);
//	  if (infoBubble.isOpen()) infoBubble.close();
	  infoBubble.open(map, marker);
//	  var div = document.createElement('DIV');
//	  div.innerHTML = content;
//	  infoBubble.addTab(null, div);
      
//      infoWindow.close();
//      alert(content);
//      infoWindow.setContent(content);
//      infoWindow.open(map, marker);
    }
    else{
      infoBubble.close();
      marker.setMap(null);
    }						 		
  });

  // ## Placeholder for zoom event listener, currently unused ##
  //google.maps.event.addListener(map, 'zoom_changed', function() {
    //map.setMap(null);
    //var showStreetViewControl = map.getMapTypeId() != 'coordinate';
    //map.setOptions({'streetViewControl': showStreetViewControl});
  //});

  // #### Copyright notices ####
  // ## Create div for showing copyrights ##
  copyrightNode = document.createElement('div');
  copyrightNode.id = 'copyright-control';
  copyrightNode.style.fontSize = '11px';
  copyrightNode.style.fontFamily = 'Arial, sans-serif';
  copyrightNode.style.margin = '0 2px 2px 0';
  copyrightNode.style.whiteSpace = 'nowrap';
  copyrightNode.index = 0;
  map.controls[google.maps.ControlPosition.BOTTOM_RIGHT].push(copyrightNode);

//  loadCopyrightCollections(mapTypesCount); // Copyright collections

  // ## Listener for map type change to update copyrights ##
  google.maps.event.addListener(map, 'idle', updateCopyrights);
  google.maps.event.addListener(map, 'maptypeid_changed', updateCopyrights);

// ## end of function load() ##
}

// ##### OTHER FUNCTIONS #####

function placeMarker(location) {
  //var infoWindow = new google.maps.InfoWindow;
  if (marker == undefined){
    // ## This should never be called and is here just in case! ##
    marker = new google.maps.Marker({
      position: location,
      title: 'Location',
      map: map,
      draggable: true,
      animation: google.maps.Animation.DROP,
    });
  }
  else{
    marker.setPosition(location);
    marker.setMap(map);
  }

  // ## Listener to remove marker when dragged ##
  google.maps.event.addListenerOnce(marker, 'dragstart', function() {
    infoBubble.close();
  });

  // ## THIS GLUES THE MARKER TO THE MOUSE: DON'T USE HERE (LEFT IN FOR INFO!) ##
  //google.maps.event.addListenerOnce(marker, 'drag', function() {
     // <code />
  //});

  // ## Listener to re-create marker when drag released ##
  google.maps.event.addListenerOnce(marker, 'dragend', function(event) {
    google.maps.event.trigger(map, 'click', event);
  });

  // ## Listener to centre map on marker if it is clicked ##
  google.maps.event.addListenerOnce(marker, 'click', function(event) {
    var latLng = marker.getPosition(); // returns LatLng object
    map.setCenter(latLng); // setCenter takes a LatLng object
  });

  // ## Listener to remove marker if infoWindow is manually closed, for neatness ##
  google.maps.event.addListenerOnce(infoBubble, 'closeclick', function(event) {
    marker.setMap(null);
  });
}

// #### Function to parse region data and create map overlays ####
function parseMapResponse(request,map){	
  if(request.readyState == 4){
    if(request.status == 200 || request.status == 304){			
      var data=parseIEBug(request);
      var root=data.getElementsByTagName('Map')[0];
      if(root==null){return;}
      __items=root.getElementsByTagName("Grid");	
      //console.log(root); // FOR TESTING [opensim]/app/google_map/data/map.php ONLY
      if(__items==null){return;}
      for(var i=0;i<__items.length;i++){
        if(__items[i].nodeType == 1){
          var xmluuid=__items[i].getElementsByTagName("Uuid")[0].firstChild.nodeValue;
          var xmlregionname=__items[i].getElementsByTagName("RegionName")[0].firstChild.nodeValue;
          var xmllocX=__items[i].getElementsByTagName("LocX")[0].firstChild.nodeValue;
          var xmllocY=__items[i].getElementsByTagName("LocY")[0].firstChild.nodeValue;
          // ## Rewritten code for getting varregion sizes ##
          for (key in xlocations) {
            if (xlocations[key] == xmllocX && ylocations[key] == xmllocY) {
              xsizes[key] = __items[i].getElementsByTagName("SizeX")[0].firstChild.nodeValue;
              ysizes[key] = __items[i].getElementsByTagName("SizeY")[0].firstChild.nodeValue;
            }
          }
          sizeX = __items[i].getElementsByTagName("SizeX")[0].firstChild.nodeValue; 
          sizeY = __items[i].getElementsByTagName("SizeY")[0].firstChild.nodeValue;
          // ## End of new code - following old line left for information, moved below ##
          //var opensimFilename = 'map-1-' + xmllocX + '-' + xmllocY + '-objects'+ '.jpg';
          xmllocX=xmllocX - xstart + 10;
          xmllocY=xmllocY - ystart + 10;
          // ## New code for initialising varregions on map ##
          for (var x=(sizeX/256)-1;x>=0;x--){ // has to be backwards to finish on SE corner
            for (var y=(sizeX/256)-1;y>=0;y--){ // has to be backwards to finish on SE corner
              var xmllocXx = parseInt(xmllocX) + x - 10 + xstart; var xmllocYy = parseInt(xmllocY) + y - 10 + ystart; // messy hack needs tidying later
              var opensimFilename = 'map-1-' + xmllocXx + '-' + xmllocYy + '-objects'+ '.jpg';
              // ## Old lines left for information ##
              //var boundaries = new google.maps.LatLngBounds(
                //new google.maps.LatLng(xmllocY,xmllocX),
                //new google.maps.LatLng(xmllocY+1,xmllocX+1));
              var boundaries = new google.maps.LatLngBounds(
                new google.maps.LatLng(xmllocY+y,xmllocX+x),
                new google.maps.LatLng(xmllocY+y+1,xmllocX+x+1));
              if (filenames == "uuid-no-dashes") { // This is kept to enable compatibility with v2 code using UUIDs without dashes
                var rx=new RegExp("(-)", "g");
                xmluuid = xmluuid.replace(rx,"");
              }
              var groundOverlayOptions = {map: map, clickable: true, opacity: 1};
              layerCount ++;
              var groundoverlay;

              if (filenames == "uuid" || filenames == "uuid-no-dashes") { // Use UUID format for jpg names
                //layer[layerCount] = new google.maps.GroundOverlay('data/regions/' + xmluuid + '.jpg', boundaries, groundOverlayOptions);
                if(sizeX > 256) {
                	groundoverlay = jUrlBase+'/images/jopensim/regions/' + xmluuid + '.jpg';
//                	groundoverlay = jUrlBase+'/images/jopensim/regions/varregions/map-1-' + xmllocXx + '-' + xmllocYy + '-objects'+ '.jpg';
                } else {
                	groundoverlay = jUrlBase+'/images/jopensim/regions/' + xmluuid + '.jpg';
                }
              }
              else if (filenames == "opensim") { // Use default opensim naming pattern for jpg names
                //layer[layerCount] = new google.maps.GroundOverlay('data/regions/' + opensimFilename, boundaries, groundOverlayOptions);
                groundoverlay = jUrlBase+'/images/jopensim/regions/' + opensimFilename;
              }
              layer[layerCount] = new google.maps.GroundOverlay(groundoverlay, boundaries, groundOverlayOptions);
              layer[layerCount].setMap(map);
              // ## Listener to divert click on map overlay tiles to map click (otherwise blocked) ##
              google.maps.event.addListener(layer[layerCount], 'click', function(event) {
                google.maps.event.trigger(map, 'click', event);
              });
              // ## End of rewritten code ##
            }
          }
        }
      }	
    }
  }
}

// #### Function to return information for infoWindow ####
function getRegionInfos(x, y)
{
    //	alert("x: "+x+"\ny: "+y);
    //	x = x - mapxoffset;
    //	y = y - mapyoffset;
    //	alert("x: "+x+"\ny: "+y);

	if (__items == null) {return;}
	
    var response = "";
	
    for (var i = 0; i < __items.length; i++)
    {
        if (__items[i].nodeType == 1)
        {
            var xmllocX = __items[i].getElementsByTagName("LocX")[0].firstChild.nodeValue;
			var xmllocY = __items[i].getElementsByTagName("LocY")[0].firstChild.nodeValue;			
			
            if (xmllocX == x && xmllocY == y)
            {
				var articleID		= __items[i].getElementsByTagName("articleID")[0].firstChild.nodeValue;
				var xmluuid			= __items[i].getElementsByTagName("Uuid")[0].firstChild.nodeValue;
				var xmlregionname	= __items[i].getElementsByTagName("RegionName")[0].firstChild.nodeValue;

				// #### These two lines from the old code visually remove dashes from UUIDs: seems unnecessary. ####
				// var rx = new RegExp("(-)", "g");
				// xmluuid = xmluuid.replace(rx,"");

				response = "";
				response += "<table class='regioninfo'>";

				if (articleID > 0 && jArticleLink == "1") {
                    link1 = "<a class=\"jopensim_articlelink\" href='" + jUrlBase + "/index.php?option=com_content&view=article&id="+articleID+"'>";
					if (jArticleIcon == "1") link2 = "&nbsp;<span class='icon-link'> </span></a>";
					else link2 = "</a>";
				} else {
					link1 = "";
					link2 = "";
				}
				
				response += "<thead><tr><th>";
				response += "<span id='name'>" + link1 + "<strong>" + xmlregionname + "</strong>" + link2 + "</span>";
				response += "</th></tr></thead>";
				
				if (showUUID == "true") {
				    marker.setTitle(region_uuid + ":\n" +xmluuid+ "\nLocation: " +xmlregionname+ "/" +xjump+ "/" +yjump+ "/");
				} else {
				    marker.setTitle(region_location + ": " +xmlregionname+ "/" +xjump+ "/" +yjump+ "/");
				}

				if (showCoords == "true")
                {
				    response += "<tr><td>";
				    response += region_coordinates + ": ";
                    response += "<span class='label label-danger' id='locX'>X:</span> <strong>" +xmllocX+ "</strong> <span class='label label-success'>Y: </span> <strong>"+xmllocY+"</strong>";
					response += "</td></tr>";
                }

				var portnumber = hgports[map.getMapTypeId()];
				var portstring = "";
				var portstring2 = "";
				
                if (port80 == 1)
                {
                    portstring = ":" + portnumber; 
                    portstring2 = "|" + portnumber;
                }
				
                if (jTeleportLink == 1)
                {
					response += "<tr><td>";
					response += "<a class=\"btn btn-default btn-primary\" href=\"secondlife://" + xmlregionname + "/" + xjump + "/" + yjump + "/\">";
					response += "<i class='icon-ok'></i> Teleport";
					response += "</a>";
					response += "</td></tr>";
					
					if (xjump > 255 || yjump > 255) {
						response += "</table>";
						response += "<table class='regioninfo'>";
						response += "<tr><td>";
						response += viewer_restriction;
						response += "</td></tr>";
						response += "</table>";
					}
				}
				response += "</table>";
			}
		}	
	}	
	return response;
}

// #### Function to prevent click outside preset bounds - not vital but kept from v2 code ####
function isOutOfBounds(x,y){
    // x = x - mapxoffset;
    // y = y - mapyoffset;
	if (x < xstart - 30 || x > xstart + 30){return true;}
	if (y < ystart - 30 || y > ystart + 30){return true;}
	return false;
}

// #### Function to fix IE bug, kept from v2 code - still required? ####
function parseIEBug(request){	
	if (document.implementation && document.implementation.createDocument){
		xmlDoc = request.responseXML;
	} else if (window.ActiveXObject){
		var testandoAppend = document.createElement('xml');
		testandoAppend.setAttribute('innerHTML',request.responseText);
		testandoAppend.setAttribute('id','_formjAjaxRetornoXML');
		document.body.appendChild(testandoAppend);
		document.getElementById('_formjAjaxRetornoXML').innerHTML = request.responseText;
		xmlDoc = document.getElementById('_formjAjaxRetornoXML');
	}
	return xmlDoc;
}

// #### Function to get HTTP object ####
function getHTTPObject(){
	var xhr = false;
	if(window.XMLHttpRequest){
		var xhr = new XMLHttpRequest();
	} else if(window.ActiveXObject) {
		try {
			var xhr = new ActiveXObject("Msxml2.XMLHTTP");
		}catch(e){
			try {
				var xhr = new ActiveXObject("Microsoft.XMLHTTP");
			} catch(e){xhr=false;}
		}
	}
	return xhr;
}

// #### Function to update copyrights if map type changed ####
function updateCopyrights() {
	var notice = '';
	var collection = copyrights[map.getMapTypeId()];
	var bounds = map.getBounds();
	var zoom = map.getZoom();
//  if (collection && bounds && zoom) {
//    notice = collection.getCopyrightNotice(bounds, zoom);
//  }
	copyrightNode.innerHTML = jCopyright;
}

// #### Function to load copyright collections for custom map types ####
function loadCopyrightCollections(mapTypesCount) {
	var collection = new Array();
	if (copyrightStartYear == 0){
		copyrightStartYear = String(new Date().getFullYear());
	}
	var copyrightStartYearString = "";
	if (copyrightStartYear != -1) {
		copyrightStartYearString = copyrightStartYear.toString();
	}
	if (copyrightEndYear == 0){
		copyrightEndYear = String(new Date().getFullYear()).substr(2)
	}
	var copyrightEndYearString = "";
	if (copyrightEndYear != -1) {
		copyrightEndYearString = "-"+copyrightEndYear.toString();
	}
	for (i = 0; i < mapTypesCount; ++i) {
		//map.mapTypes.set(mapTypes[i], new plainMapType(mapCentreNames[i]));
		mapCopy = "";
		if(copyrightNotices[i]) mapCopy = "Map data &copy;";
		else {
			copyrightStartYearString = "";
			copyrightEndYearString = "";
		}
		collection[i] = new CopyrightCollection(mapCopy+copyrightStartYearString+copyrightEndYearString);
		collection[i].addCopyright(new Copyright(   
			1,
			new google.maps.LatLngBounds(
				new google.maps.LatLng(-90, -179), new google.maps.LatLng(90, 180)),
				0,
				copyrightNotices[i]));  
		copyrights[mapTypes[i]] = collection[i];
	}
}

// #### Function to set up home control ####
function HomeControl(controlDiv, map) {
	// ## Set CSS styles for the DIV containing the control ##
	// Setting padding to 5 px will offset the control
	// from the edge of the map.
	controlDiv.style.padding = '5px';

	// ## Set CSS for the control border ##
	var controlUI = document.createElement('div');
	controlUI.style.backgroundColor = 'white';
	controlUI.style.borderStyle = 'solid';
	controlUI.style.borderWidth = '2px';
	controlUI.style.cursor = 'pointer';
	controlUI.style.textAlign = 'center';
	controlUI.title = region_home_click;
    // controlUI.title = 'Click to set the map to Home';
	controlDiv.appendChild(controlUI);

	// ## Set CSS for the control interior ##
	var controlText = document.createElement('div');
	controlText.style.fontFamily = 'Arial,sans-serif';
	controlText.style.fontSize = '11px';
	controlText.style.paddingLeft = '4px';
	controlText.style.paddingRight = '4px';
	// controlText.innerHTML = '<strong>'+region_home+'<strong>';
    controlText.innerHTML = '<strong>Home<strong>';
	controlUI.appendChild(controlText);

	// ## Set up the click event listeners ##
	google.maps.event.addDomListener(controlUI, 'click', function() {
		// ## New code to offset the centre from xlocation, ylocation ##
		var xoffset = xoffsets[map.getMapTypeId()];
		var yoffset = yoffsets[map.getMapTypeId()];
		map.setCenter(latLng);
//	alert("xoffset: "+-xoffset*184+"\nyoffset: "+yoffset*184);
		map.panBy(-xoffset*184,yoffset*184); // Not sure why 184 pixels is right but it is!
	});
}
