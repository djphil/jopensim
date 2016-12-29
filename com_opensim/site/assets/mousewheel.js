function catchWheel(dir,vel) {
	if(dir == "Up") {
		if(scalefactor < 256) scalefactor += 10;
	}
	if(dir == "Down") {
		if(scalefactor >= 10) scalefactor -= 10;
	}
	scaleMaps(scalefactor);
}

function resetMap() {
	scalefactor = defaultscalefactor;
	scaleMaps(scalefactor);
	centerMapPos();
}

function zoomMap(zoomDir) {
	if(zoomDir == "in") {
		if(scalefactor <= 246) scalefactor += 10;
	}
	if(zoomDir == "out") {
		if(scalefactor >= 10) scalefactor -= 10;
	}
	scaleMaps(scalefactor);
}

function centerMapPos() {
	var mapcontainer = document.getElementById("mapcontainer");

	var maskWidth  = $("#mapcontainer").width();
	var maskHeight = $("#mapcontainer").height();
	var imgPos     = $("#mapzoom").offset();
	var maskPos  = $("#mapcontainer").offset();
	if(!imgPos) return false;
	var imgPosOffsetTop = imgPos.top;
	var imgPosOffsetLeft = imgPos.left;
	var maskPosOffsetTop = maskPos.top;
	var maskPosOffsetLeft = maskPos.left;

	var cellX = document.getElementById("cellX").value;
	var cellY = document.getElementById("cellY").value;

	var homeX = document.getElementById("homeX").value;
	var homeY = document.getElementById("homeY").value;

	var imgWidth   = cellX*(scalefactor)+(1);
	var imgHeight  = cellY*(scalefactor)+(1);

	var centerHomeOffsetX = homeX*scalefactor;
	var centerHomeOffsetY = homeY*scalefactor;
	
	$("#mapzoom").css({width: imgWidth, height: imgHeight});

	imgOffsetX = (maskWidth - imgWidth)/2 + centerHomeOffsetX;
	imgOffsetY = (maskHeight - imgHeight)/2 + centerHomeOffsetY;
	$("#mapzoom").css({top: imgOffsetY, left: imgOffsetX});
}

function setScalePos() {
	var maskWidth  = $("#mapcontainer").width();
	var maskHeight = $("#mapcontainer").height();
	var maskPos  = $("#mapcontainer").offset();

	var imgWidth  = $("#mapzoom").width();
	var imgHeight = $("#mapzoom").height();
	var imgPos     = $("#mapzoom").offset();

	var imgPosOffsetTop = imgPos.top;
	var imgPosOffsetLeft = imgPos.left;
	var maskPosOffsetTop = maskPos.top;
	var maskPosOffsetLeft = maskPos.left;

	imgOffsetX = maskPosOffsetLeft - imgPosOffsetLeft - (imgWidth - maskWidth)/2;
	imgOffsetY = maskPosOffsetTop - imgPosOffsetTop - (imgHeight - maskHeight)/2;
	$("#mapzoom").css({top: imgOffsetY, left: imgOffsetX});
}

function scaleMaps(scale) {
	if(!document.getElementById("cellX") || !document.getElementById("cellY")) return;

	var imgPos     = $("#mapzoom").offset();
	if(!imgPos) return false;
	var imgPosOffsetTop = imgPos.top;
	var imgPosOffsetLeft = imgPos.left;

	var mapid = 0;
	while(document.getElementById("mapimage_"+mapid)) {
		mapbild = document.getElementById("mapimage_"+mapid);
		mapbild.width = scale;
		mapbild.height = scale;
		mapid++;
	}
	var cellX = document.getElementById("cellX").value;
	var cellY = document.getElementById("cellY").value;
	var imgWidth   = cellX*(scale)+(1);
	var imgHeight  = cellY*(scale)+(1);
	$("#mapzoom").css({width: imgWidth, height: imgHeight});
	var imgPosOffsetTop = imgPos.top;
	var imgPosOffsetLeft = imgPos.left;
	scaleLimits(scale);
	var imgPosOffsetTop = imgPos.top;
	var imgPosOffsetLeft = imgPos.left;
}

function scaleLimits(currentScaling) {
	var tempTop = $("#mapzoom").css('top');
	var tempLeft = $("#mapzoom").css('left');
	$("#mapzoom").css({top: 0, left: 0});

	var cellX = document.getElementById("cellX").value;
	var cellY = document.getElementById("cellY").value;

	var maskWidth  = $("#mapcontainer").width();
	var maskHeight = $("#mapcontainer").height();
	var imgPos     = $("#mapzoom").offset();
	var imgWidth	= cellX * currentScaling;
	var imgHeight	= cellY * currentScaling;

	var x2 = imgPos.left;
	var y2 = imgPos.top;

	if(maskWidth < imgWidth && maskHeight < imgHeight) { // map doesnt fit at all, allow full drag
		setScalePos();
		$("#mapzoom").draggable({ containment: [-20000,-20000,20000,20000] });
		$("#mapzoom").draggable({ revert: false });
	} else if(maskHeight < imgHeight) { // only width fits, lets drag only y-axis
		setScalePos();
		$("#mapzoom").draggable({ containment: [x2,-20000,x2,20000] });
		$("#mapzoom").draggable({ revert: false });
	} else if(maskWidth < imgWidth) { // only height fits, lets drag only x-axis
		setScalePos();
		$("#mapzoom").draggable({ containment: [-20000,y2,20000,y2] });
		$("#mapzoom").draggable({ revert: false });
	} else {
		centerMapPos();
		$("#mapzoom").draggable({ containment: "#mapcontainer" });
		$("#mapzoom").draggable({ revert: true });
	}

	$("#mapzoom").css({top: tempTop, left: tempLeft});
	$("#mapzoom").css({cursor: 'move'});
	$("#mapzoom").draggable({ scroll: false});
	if(initMap == false) {
		centerMapPos();
//		$("#mapzoom").draggable({ drag: function(event,ui) { debugoutput3div(); } });
		initMap = true;
	}
}

var scalefactor;
scaleMaps(scalefactor);