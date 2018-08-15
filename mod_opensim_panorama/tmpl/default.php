<?php
/**
 * @module      OpenSim Panorama (mod_opensim_panorama)
 * @copyright   Copyright (C) djphil 2018, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 * @creative    CC-BY-NC-SA 4.0
**/

// no direct access
defined('_JEXEC') or die('Direct Access to this location is not allowed.');
?>

<style>
#jOpenSim_panorama canvas {
	max-height: <?php echo $panoramaheight; ?>px;
	<?php if ($panoramastyle == 1): ?>
	max-width: 100%;
	<?php else: ?>
	max-width: <?php echo $panoramawidth; ?>px;
	<?php endif; ?>

	<?php if ($panoramaborder == 1): ?>
	border: 1px solid <?php echo $panoramacolor; ?>;
	<?php endif; ?>
	
	<?php if ($panoramarounded == 1): ?>
	border-radius: <?php echo $panoramaradius; ?>px;
	<?php endif; ?>

	<?php if ($panoramagrabbing == 1): ?>
	cursor: <?php echo $panoramagrabstyle; ?>;
	<?php else: ?>
	cursor: default;
	<?php endif; ?>
}
</style>

<div id="jOpenSim_panorama"></div>

<script>
var camera, scene, renderer;
var isUserInteracting = false;
var onMouseDownMouseX = 0;
var onMouseDownMouseY = 0;
var lon = <?php echo $panoramalon; ?>;
var onMouseDownLon = 0;
var lat = <?php echo $panoramalat; ?>;
var onMouseDownLat = 0;
var phi = 0;
var theta = 0;
var distortion = <?php echo $panoramaradius; ?>;
var fov = <?php echo $panoramafov; ?>;

init();
animate();

function init() {
	var container, mesh;
	container = document.getElementById('jOpenSim_panorama');
	camera = new THREE.PerspectiveCamera(fov, window.innerWidth / window.innerHeight, 1, 1100);
	camera.target = new THREE.Vector3(0, 0, 0);
	scene = new THREE.Scene();

	var geometry = new THREE.SphereBufferGeometry(500, 60, 40);
	geometry.scale(-1, 1, 1);

	var material = new THREE.MeshBasicMaterial({
		map: new THREE.TextureLoader().load('<?php echo $panoramaimg; ?>')
	});

	mesh = new THREE.Mesh(geometry, material);
	scene.add(mesh);
	renderer = new THREE.WebGLRenderer();
	renderer.setPixelRatio(window.devicePixelRatio);
	renderer.setSize(window.innerWidth, window.innerHeight);
	container.appendChild(renderer.domElement);
	<?php if ($panoramagrabbing == 1): ?>
	renderer.domElement.addEventListener('mousedown', onPointerStart, false);
	renderer.domElement.addEventListener('mousemove', onPointerMove, false);
	renderer.domElement.addEventListener('mouseup', onPointerUp, false);
	renderer.domElement.addEventListener('touchstart', onPointerStart, false);
	renderer.domElement.addEventListener('touchmove', onPointerMove, false);
	renderer.domElement.addEventListener('touchend', onPointerUp, false);
	<?php endif; ?>
	window.addEventListener( 'resize', onWindowResize, false );
}

function onWindowResize() {
	camera.aspect = window.innerWidth / window.innerHeight;
	camera.updateProjectionMatrix();
	renderer.setSize( window.innerWidth, window.innerHeight );
}

function onPointerStart( event ) {
	isUserInteracting = true;
	var clientX = event.clientX || event.touches[0].clientX;
	var clientY = event.clientY || event.touches[0].clientY;
	onMouseDownMouseX = clientX;
	onMouseDownMouseY = clientY;
	onMouseDownLon = lon;
	onMouseDownLat = lat;
}

function onPointerMove( event ) {
	if ( isUserInteracting === true ) {
		var clientX = event.clientX || event.touches[0].clientX;
		var clientY = event.clientY || event.touches[0].clientY;
		lon = ( onMouseDownMouseX - clientX ) * 0.1 + onMouseDownLon;
		lat = ( clientY - onMouseDownMouseY ) * 0.1 + onMouseDownLat;
	}
}

function onPointerUp( event ) {
	isUserInteracting = false;
}

function animate() {
	requestAnimationFrame( animate );
	update();
}

function update()
{
	// Animation
	if (isUserInteracting === false) {
		<?php if ($panoramaanim == 1): ?>
		lon += <?php echo $panoramaspeed; ?>;
		<?php else: ?>
		lon += 0.0;
		<?php endif; ?>
	}

	lat = Math.max(-89, Math.min(89, lat));
	phi = THREE.Math.degToRad(90 - lat);
	theta = THREE.Math.degToRad(lon);

	camera.target.x = 500 * Math.sin(phi) * Math.cos(theta);
	camera.target.y = 500 * Math.cos(phi);
	camera.target.z = 500 * Math.sin(phi) * Math.sin(theta);
	camera.lookAt(camera.target);

	//  Distortion
	<?php if ($panoramadisto == 1): ?>
		camera.position.copy(camera.target).negate();
	<?php endif; ?>

	renderer.render(scene, camera);
}
</script>