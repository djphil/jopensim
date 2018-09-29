var map;
var popup = L.popup();

function initmap() {

	var southWest = L.latLng((maxsouth-2), (maxwest+2)),
		northEast = L.latLng((maxnorth+2), (maxeast-2)),
		maxbounds = L.latLngBounds(southWest, northEast);

	map = L.map('map', {
		crs: L.CRS.Simple,
		center: [ystart, xstart],
		zoom: zoomStart,
		maxZoom: 9
//		maxBounds: maxbounds
	});

	if(mouseZoom == 0) map.scrollWheelZoom.disable();

	var jopensimMap = L.layerGroup();

	var jopensim_markers	= L.markerClusterGroup({ chunkedLoading: true, animateAddingMarkers: true, maxClusterRadius: 50, showCoverageOnHover: false });

	var jopensim_regions	= L.featureGroup.subGroup(jopensimMap);

	// lets handle markers if there are any
	var jopensimicon = L.Icon.extend({
		options: {
			iconSize:     [33, 44],
			iconAnchor:   [17, 43],
			popupAnchor:  [0, -44]
		}
	});
	
	var jopensimregionicon = L.Icon.extend({
		options: {
			iconSize:     [2, 2],
			iconAnchor:   [1, 1],
			popupAnchor:  [0, 0],
			iconUrl: classifiedMarkerDummy
		}
	});

	

	Object.keys(regionImage).forEach(function(mapkey,mapindex) {
		mapcoords = mapkey.split("-");
		if(mapcoords.length == 2 && mapcoords[0] != "xxx") {
			bounds = [[parseInt(mapcoords[0]),parseInt(mapcoords[1])],[parseInt(mapcoords[0])+1,parseInt(mapcoords[1])+1]];
			mapname = regionImage[mapkey];
			regionName	= regionname[mapkey]
			var image = L.imageOverlay(mapname, bounds, {title: mapname, className: "jopensimregion", interactive: true, zIndex:1}).addTo(map);
			if(!regionAddX[mapkey] && !regionAddY[mapkey]) {
				var regionlatlng = L.latLng(parseInt(mapcoords[0])+0.5, parseInt(mapcoords[1])+0.5);
//				L.marker(regionlatlng, {title: regionName, className:"region"}).addTo(jopensim_regions);
//				L.marker(regionlatlng, {title: regionName, icon: jopensimregionicon, className:"region"}).addTo(jopensim_regions);
//				L.circle(regionlatlng, {radius: 10, color: 'blue', weight: 1, title: regionName, className:"region"}).addTo(jopensim_regions);
				L.circleMarker(regionlatlng, {radius: 1, color: 'transparent', weight: 1, title: regionName, className:"region"}).addTo(jopensim_regions);
			}
			var image = L.imageOverlay(mapname, bounds, {title: mapname, className: "jopensimregion", interactive: true, zIndex:1}).addTo(map);
		}
	});
	jopensim_regions.addTo(jopensimMap);

	if(classifiedsMarker) {
		var classifieds		= new Array();
		classifieds[0]		= L.featureGroup.subGroup(jopensim_markers);
		classifieds[1]		= L.featureGroup.subGroup(classifieds[0]); // shopping
		classifieds[2]		= L.featureGroup.subGroup(classifieds[0]); // land rental
		classifieds[3]		= L.featureGroup.subGroup(classifieds[0]); // property rental
		classifieds[4]		= L.featureGroup.subGroup(classifieds[0]); // special attraction
		classifieds[5]		= L.featureGroup.subGroup(classifieds[0]); // new products
		classifieds[6]		= L.featureGroup.subGroup(classifieds[0]); // employment
		classifieds[7]		= L.featureGroup.subGroup(classifieds[0]); // wanted
		classifieds[8]		= L.featureGroup.subGroup(classifieds[0]); // service
		classifieds[9]		= L.featureGroup.subGroup(classifieds[0]); // personal
//		var classifiedCats	= new Array();
//		classifiedCats[0] = 1;
		classifiedCats = [1,0,0,0,0,0,0,0,0,0];
		var iconClassified1	= new jopensimicon({iconUrl: classifiedMarkerIcon1})
		var iconClassified2	= new jopensimicon({iconUrl: classifiedMarkerIcon2})
		var iconClassified3	= new jopensimicon({iconUrl: classifiedMarkerIcon3})
		var iconClassified4	= new jopensimicon({iconUrl: classifiedMarkerIcon4})
		var iconClassified5	= new jopensimicon({iconUrl: classifiedMarkerIcon5})
		var iconClassified6	= new jopensimicon({iconUrl: classifiedMarkerIcon6})
		var iconClassified7	= new jopensimicon({iconUrl: classifiedMarkerIcon7})
		var iconClassified8	= new jopensimicon({iconUrl: classifiedMarkerIcon8})
		var iconClassified9	= new jopensimicon({iconUrl: classifiedMarkerIcon9})
		Object.keys(classifiedMarker).forEach(function(classifiedkey,classifiedindex) {
			if(classifiedkey != "xxx-xxx") {
				if(showTP) {
					tpLocal			= getTPclassified(classifiedkey,"local");
					tpHG			= getTPclassified(classifiedkey,"hg");
					tpHGv3			= getTPclassified(classifiedkey,"hgv3");
					tpHop			= getTPclassified(classifiedkey,"hop");
					popupContent	= "<span class='jmappopupname'>"+classifiedkey+"</span><br />"+classifiedDesc[classifiedkey]+"<br /><div class='jopensim_showcase'>"+tpLocal+tpHG+tpHGv3+tpHop+"</div>";
				} else {
					popupContent	= "<span class='jmappopupname'>"+classifiedkey+"</span><br />"+classifiedDesc[classifiedkey];
				}
				switch(classifiedCat[classifiedkey]) {
					case 1: // shopping
//						alert("shopping+");
						var marker = L.marker(classifiedMarker[classifiedkey], {title: classifiedkey, icon: iconClassified1, className:"classified"}).bindPopup(popupContent).addTo(classifieds[1]);
						classifiedCats[1]++;
					break;
					case 2: // land rental
						var marker = L.marker(classifiedMarker[classifiedkey], {title: classifiedkey, icon: iconClassified2, className:"classified"}).bindPopup(popupContent).addTo(classifieds[2]);
						classifiedCats[2]++;
					break;
					case 3: // property rental
						var marker = L.marker(classifiedMarker[classifiedkey], {title: classifiedkey, icon: iconClassified3, className:"classified"}).bindPopup(popupContent).addTo(classifieds[3]);
						classifiedCats[3]++;
					break;
					case 4: // special attraction
						var marker = L.marker(classifiedMarker[classifiedkey], {title: classifiedkey, icon: iconClassified4, className:"classified"}).bindPopup(popupContent).addTo(classifieds[4]);
						classifiedCats[4]++;
					break;
					case 5: // new products
						var marker = L.marker(classifiedMarker[classifiedkey], {title: classifiedkey, icon: iconClassified5, className:"classified"}).bindPopup(popupContent).addTo(classifieds[5]);
						classifiedCats[5]++;
					break;
					case 6: // employment
						var marker = L.marker(classifiedMarker[classifiedkey], {title: classifiedkey, icon: iconClassified6, className:"classified"}).bindPopup(popupContent).addTo(classifieds[6]);
						classifiedCats[6]++;
					break;
					case 7: // wanted
						var marker = L.marker(classifiedMarker[classifiedkey], {title: classifiedkey, icon: iconClassified7, className:"classified"}).bindPopup(popupContent).addTo(classifieds[7]);
						classifiedCats[7]++;
					break;
					case 8: // service
						var marker = L.marker(classifiedMarker[classifiedkey], {title: classifiedkey, icon: iconClassified8, className:"classified"}).bindPopup(popupContent).addTo(classifieds[8]);
						classifiedCats[8]++;
					break;
					case 9: // personal
						var marker = L.marker(classifiedMarker[classifiedkey], {title: classifiedkey, icon: iconClassified9, className:"classified"}).bindPopup(popupContent).addTo(classifieds[9]);
						classifiedCats[9]++;
					break;
				}
			}
		});
	}

	var events = L.featureGroup.subGroup(jopensim_markers);

	if(eventsMarker) {
		var iconEvent	= new jopensimicon({iconUrl: eventMarkerIcon})
		Object.keys(eventMarker).forEach(function(eventkey,eventindex) {
			if(eventkey != "xxx-xxx") {
				popupContent	= "<span class='jmappopupname'>"+eventMarkerName[eventkey]+"</span><br />"+eventMartkerTimeText+": "+eventMarkerTime[eventkey]+"<br />"+eventMartkerDurationText+": "+eventMarkerDuration[eventkey]+"<br />"+eventMarkerDesc[eventkey];
				if(showTP) {
					popupContent = popupContent+"<br />"+getTPevent(eventkey);
				}
				var marker = L.marker(eventMarker[eventkey], {title: eventMarkerName[eventkey], icon: iconEvent, className:"event"}).bindPopup(popupContent).addTo(events);
			}
		});
	}

	if(classifiedsMarker || eventsMarker) {
		jopensim_markers.addTo(jopensimMap);
	}

	if(classifiedsMarker) {
		classifieds[0].addTo(map);
	}

	var overlayMaps = {};
	if(classifiedsMarker) {
		overlayMaps[classifiedCatNames[0]] = classifieds[0];
		for(i=1;i<classifiedCats.length;i++) {
			if(classifiedCats[i] > 0) {
				overlayMaps["<span class='jsubgroup'>"+classifiedCatNames[i]+"</span>"] = classifieds[i];
				classifieds[i].addTo(map);
			}
		}
	}
	if(eventsMarker) {
		overlayMaps["Events"] = events;
		events.addTo(map);
	}

	if(classifiedsMarker || eventsMarker) {
		L.control.layers(null,overlayMaps).addTo(map);
	}

	jopensimMap.addTo(map);

	L.easyButton('icon-contract-2', function(btn, map){
		var maphome = [1000,1000];
		map.setView(maphome,7);
	}).addTo(map);

	var controlSearch = new L.Control.Search({
		position:'topright',		
		layer: jopensimMap,
		initial: false,
		zoom: 12,
		marker: false,
		buildTip: jCustomTip,
		textPlaceholder:searchPlaceholder
	});
	map.addControl( controlSearch );

	if(jCopyright) {
		map.attributionControl.setPrefix(jCopyright+' | Powered by <a href="https://leafletjs.com/" target="_blank">Leaflet</a> and <a href="https://www.jopensim.com" target="_blank">jOpenSim</a>');
	} else {
		map.attributionControl.setPrefix('Powered by <a href="https://leafletjs.com/" target="_blank">Leaflet</a> and <a href="https://www.jopensim.com" target="_blank">jOpenSim</a>');
	}

	map.on('click', onMapClick);
}

function jCustomTip(text,val) {
//	alert("text: "+text+"\nval: "+val.layer.options.className);
	return '<a href="#" class="jopensimmapsearch_'+val.layer.options.className+'" title="'+val.layer.options.className+'">'+text+'</a>';
//	return '<a href="#">'+text+'<em style="background:'+text+'; width:14px;height:14px;float:right"></em></a>';
}

function getTPclassified(classifiedKey,linktype) {
	switch(linktype) {
		case "hg":
			return "<div class='tphg'><a class='tpbtn btn-hg' href='"+classifiedTpHG[classifiedKey]+"'>HG</a></div>";
		break;
		case "hgv3":
			return "<div class='tphgv3'><a class='tpbtn btn-hgv3' href='"+classifiedTpHGv3[classifiedKey]+"'>HGV3</a></div>";
		break;
		case "hop":
			return "<div class='tphop'><a class='tpbtn btn-tphop' href='"+classifiedTpHop[classifiedKey]+"'>HOP</a></div>";
		break;
		default:
			return "<div class='tplocal'><a class='tpbtn btn-local' href='"+classifiedTpLocal[classifiedKey]+"'>Local</a></div>";
		break;
	}
}

function getTPevent(eventKey) { // currently only local TP in event data
	eventLocation	= eventTpPos[eventKey].split("/");
	eventTPlinks	= getTPlinks(eventLocation[0],eventLocation[1],eventLocation[2],eventLocation[3]);
	tpLocal	= getTPbutton(eventTPlinks.tplocal,"local");
	tpHG	= getTPbutton(eventTPlinks.tphg,"hg");
	tpHGV3	= getTPbutton(eventTPlinks.tphgv3,"hgv3");
	tpHOP	= getTPbutton(eventTPlinks.tphop,"hop");
	eventTPlink = "<div class='jopensim_showcase'>"+tpLocal+tpHG+tpHGV3+tpHOP+"</div>";
	return eventTPlink;
}