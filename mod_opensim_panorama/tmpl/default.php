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
#jOpenSim_panorama_<?php echo $module->id; ?> #canvas_<?php echo $module->id; ?> {
    max-height: <?php echo $panoramaheight; ?>px;
    <?php if ($panoramastyle == 1): ?>
    max-width: 100%;
    <?php else: ?>
    max-width: <?php echo $panoramawidth; ?>px;
    <?php endif; ?>

    <?php if ($panoramaborder == 1): ?>
    box-sizing: border-box;
    -moz-box-sizing: border-box;
    -webkit-box-sizing: border-box;
    border: <?php echo $panoramasize; ?>px solid <?php echo $panoramacolor; ?>;
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

#jOpenSim_panorama_<?php echo $module->id; ?> {margin-bottom: -40px;}
#panostats_<?php echo $module->id + 1; ?> {width: 80px;}

#panostats_<?php echo $module->id + 1; ?> canvas {
    <?php if ($panoramarounded == 1): ?>
    border-radius: <?php echo $panoramastatsradius; ?>px;
    <?php endif; ?>
    cursor: pointer;
}
</style>

<div id="jOpenSim_panorama_<?php echo $module->id; ?>"></div>

<script>
var camera_<?php echo $module->id; ?>;
var scene_<?php echo $module->id; ?>;
var renderer_<?php echo $module->id; ?>;
var isUserInteracting_<?php echo $module->id; ?> = false;
var onMouseDownMouseX_<?php echo $module->id; ?> = 0;
var onMouseDownMouseY_<?php echo $module->id; ?> = 0;
var lon_<?php echo $module->id; ?> = <?php echo $panoramalon; ?>;
var onMouseDownLon_<?php echo $module->id; ?> = 0;
var lat_<?php echo $module->id; ?> = <?php echo $panoramalat; ?>;
var onMouseDownLat_<?php echo $module->id; ?> = 0;
var phi_<?php echo $module->id; ?> = 0;
var theta_<?php echo $module->id; ?> = 0;
var distortion_<?php echo $module->id; ?> = <?php echo $panoramaradius; ?>;
var fov_<?php echo $module->id; ?> = <?php echo $panoramafov; ?>;
var stats_<?php echo $module->id; ?>;

init_<?php echo $module->id; ?>();
animate_<?php echo $module->id; ?>();

function init_<?php echo $module->id; ?>() {
    var container_<?php echo $module->id; ?>; 
    var mesh_<?php echo $module->id; ?>;
    container_<?php echo $module->id; ?> = document.getElementById('jOpenSim_panorama_<?php echo $module->id; ?>');
    camera_<?php echo $module->id; ?> = new THREE.PerspectiveCamera(fov_<?php echo $module->id; ?>, window.innerWidth / window.innerHeight, 1, 1100);
    camera_<?php echo $module->id; ?>.target = new THREE.Vector3(0, 0, 0);
    scene_<?php echo $module->id; ?> = new THREE.Scene();

    var geometry_<?php echo $module->id; ?> = new THREE.SphereBufferGeometry(500, 60, 40);
    geometry_<?php echo $module->id; ?>.scale(-1, 1, 1);

    var material_<?php echo $module->id; ?> = new THREE.MeshBasicMaterial({
        map: new THREE.TextureLoader().load('<?php echo $panoramaimg; ?>')
    });

    mesh_<?php echo $module->id; ?> = new THREE.Mesh(geometry_<?php echo $module->id; ?>, material_<?php echo $module->id; ?>);
    scene_<?php echo $module->id; ?>.add(mesh_<?php echo $module->id; ?>);
    renderer_<?php echo $module->id; ?> = new THREE.WebGLRenderer();
    renderer_<?php echo $module->id; ?>.setPixelRatio(window.devicePixelRatio);
    renderer_<?php echo $module->id; ?>.setSize(window.innerWidth, window.innerHeight);
    container_<?php echo $module->id; ?>.appendChild(renderer_<?php echo $module->id; ?>.domElement);
    <?php if ($panoramagrabbing == 1): ?>
    renderer_<?php echo $module->id; ?>.domElement.addEventListener('mousedown', onPointerStart_<?php echo $module->id; ?>, false);
    renderer_<?php echo $module->id; ?>.domElement.addEventListener('mousemove', onPointerMove_<?php echo $module->id; ?>, false);
    renderer_<?php echo $module->id; ?>.domElement.addEventListener('mouseup', onPointerUp_<?php echo $module->id; ?>, false);
    renderer_<?php echo $module->id; ?>.domElement.addEventListener('touchstart', onPointerStart_<?php echo $module->id; ?>, false);
    renderer_<?php echo $module->id; ?>.domElement.addEventListener('touchmove', onPointerMove_<?php echo $module->id; ?>, false);
    renderer_<?php echo $module->id; ?>.domElement.addEventListener('touchend', onPointerUp_<?php echo $module->id; ?>, false);
    <?php endif; ?>

    <?php if ($panoramastats == 1): ?>
    stats_<?php echo $module->id; ?> = new Stats();
    stats_<?php echo $module->id; ?>.domElement.id = 'panostats_<?php echo $module->id + 1; ?>';

    // TOP LEFT
    <?php if ($panoramastatspos == "topleft"): ?>
    stats_<?php echo $module->id; ?>.domElement.style.cssText = 'position: relative; top:-<?php echo ($panoramaheight + 5 - $panoramaspacing); ?>px; left:<?php echo $panoramaspacing; ?>px;';
    <?php endif; ?>

    // TOP RIGHT
    <?php if ($panoramastatspos == "topright"): ?>
    stats_<?php echo $module->id; ?>.domElement.style.cssText = 'position: relative; float: right; top:-<?php echo ($panoramaheight + 5 - $panoramaspacing); ?>px; margin-right: <?php echo $panoramaspacing - 1; ?>px;';
    <?php endif; ?>

    // BOTTOM LEFT
    <?php if ($panoramastatspos == "bottomleft"): ?>
    stats_<?php echo $module->id; ?>.domElement.style.cssText = 'position: relative; top:-<?php echo (52 + $panoramaspacing); ?>px; ?>px;left: <?php echo $panoramaspacing; ?>px;';
    <?php endif; ?>

    // BOTTOM RIGHT
    <?php if ($panoramastatspos == "bottomright"): ?>
    stats_<?php echo $module->id; ?>.domElement.style.cssText = 'position: relative; float: right; margin-top: -<?php echo (52 + $panoramaspacing); ?>px; margin-right: <?php echo $panoramaspacing; ?>px;';
    <?php endif; ?>

    container_<?php echo $module->id; ?>.appendChild(stats_<?php echo $module->id; ?>.domElement);
    <?php endif; ?>

    window.addEventListener('resize', onWindowResize_<?php echo $module->id; ?>, false);
    window.addEventListener('resize', resizeCanvasToDisplaySize_<?php echo $module->id; ?>, false);
}

function onWindowResize_<?php echo $module->id; ?>() {
    camera_<?php echo $module->id; ?>.aspect = window.innerWidth / window.innerHeight;
    camera_<?php echo $module->id; ?>.updateProjectionMatrix();
    renderer_<?php echo $module->id; ?>.setSize(window.innerWidth, window.innerHeight);
}

function resizeCanvasToDisplaySize_<?php echo $module->id; ?>(force) {
    const canvas = renderer_<?php echo $module->id; ?>.domElement;
    const width = canvas.clientWidth;
    const height = canvas.clientHeight;

    if (force || canvas.width !== width || canvas.height !== height) {
        renderer_<?php echo $module->id; ?>.setSize(width, height, false);
        camera_<?php echo $module->id; ?>.aspect = width / height;
        camera_<?php echo $module->id; ?>.updateProjectionMatrix();
    }
}

function onPointerStart_<?php echo $module->id; ?>(event) {
    isUserInteracting_<?php echo $module->id; ?> = true;
    var clientX = event.clientX || event.touches[0].clientX;
    var clientY = event.clientY || event.touches[0].clientY;
    onMouseDownMouseX_<?php echo $module->id; ?> = clientX;
    onMouseDownMouseY_<?php echo $module->id; ?> = clientY;
    onMouseDownLon_<?php echo $module->id; ?> = lon_<?php echo $module->id; ?>;
    onMouseDownLat_<?php echo $module->id; ?> = lat_<?php echo $module->id; ?>;
}

function onPointerMove_<?php echo $module->id; ?>(event) {
    if (isUserInteracting_<?php echo $module->id; ?> === true) {
        var clientX = event.clientX || event.touches[0].clientX;
        var clientY = event.clientY || event.touches[0].clientY;
        lon_<?php echo $module->id; ?> = (onMouseDownMouseX_<?php echo $module->id; ?> - clientX) * 0.1 + onMouseDownLon_<?php echo $module->id; ?>;
        lat_<?php echo $module->id; ?> = (clientY - onMouseDownMouseY_<?php echo $module->id; ?>) * 0.1 + onMouseDownLat_<?php echo $module->id; ?>;
    }
}

function onPointerUp_<?php echo $module->id; ?>(event) {
    isUserInteracting_<?php echo $module->id; ?> = false;
}

function animate_<?php echo $module->id; ?>() {
    resizeCanvasToDisplaySize_<?php echo $module->id; ?>();
    resizeCanvasToDisplaySize_<?php echo $module->id; ?>(true);
    requestAnimationFrame(animate_<?php echo $module->id; ?>);

    <?php if ($panoramastats == 1): ?>
    stats_<?php echo $module->id; ?>.update();
    <?php endif; ?>

    update_<?php echo $module->id; ?>();
}

function update_<?php echo $module->id; ?>()
{
    // ANIMATION
    if (isUserInteracting_<?php echo $module->id; ?> === false) {
        <?php if ($panoramaanim == 1): ?>
        lon_<?php echo $module->id; ?> += <?php echo $panoramaspeed; ?>;
        <?php else: ?>
        lon_<?php echo $module->id; ?> += 0.0;
        <?php endif; ?>
    }

    lat_<?php echo $module->id; ?> = Math.max(-89, Math.min(89, lat_<?php echo $module->id; ?>));
    phi_<?php echo $module->id; ?> = THREE.Math.degToRad(90 - lat_<?php echo $module->id; ?>);
    theta_<?php echo $module->id; ?> = THREE.Math.degToRad(lon_<?php echo $module->id; ?>);

    camera_<?php echo $module->id; ?>.target.x = 500 * Math.sin(phi_<?php echo $module->id; ?>) * Math.cos(theta_<?php echo $module->id; ?>);
    camera_<?php echo $module->id; ?>.target.y = 500 * Math.cos(phi_<?php echo $module->id; ?>);
    camera_<?php echo $module->id; ?>.target.z = 500 * Math.sin(phi_<?php echo $module->id; ?>) * Math.sin(theta_<?php echo $module->id; ?>);
    camera_<?php echo $module->id; ?>.lookAt(camera_<?php echo $module->id; ?>.target);

    //  DISTORTION
    <?php if ($panoramadisto == 1): ?>
    camera_<?php echo $module->id; ?>.position.copy(camera_<?php echo $module->id; ?>.target).negate();
    <?php endif; ?>

    renderer_<?php echo $module->id; ?>.domElement.id = 'canvas_<?php echo $module->id; ?>';
    renderer_<?php echo $module->id; ?>.render(scene_<?php echo $module->id; ?>, camera_<?php echo $module->id; ?>);
}
</script>