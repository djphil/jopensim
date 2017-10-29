<?php
/**
 * @module      OpenSim jSonStats (mod_opensim_jsonstats)
 * @copyright   Copyright (C) djphil 2017, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 * @creative    CC-BY-NC-SA 4.0
**/

// no direct access
defined('_JEXEC') or die('Direct Access to this location is not allowed.');
?>

<script src="<?php echo $assetpath?>jquery.min.js"></script>
<script>
setInterval(function() {
    $.getJSON("<?php echo $jsonstatsURI; ?>?callback=?",
    function(data) {
        $(".buffer").remove();
        var items = [];
        $.each(data, function(key, val) {
            if (key == "Version") $('#Version').text(data.Version);
            else items.push("<tr><td>" + key + ":</td><td class='text-right' id='" + key + "'><span class='label label-info'>" + val + "</span></td></tr>");
        });
        $("<tbody/>", {
            "class": "buffer",
            html: items.join("")
        }).appendTo(".table-jsonstats");
    })
}, <?php echo $refreshrate; ?>);
</script>

<div class="buffer"><i class="glyphicon glyphicon-refresh spin"></i> loading ...</div>

<?php if ($params->get('jsonstatsversion')): ?>
<div id="Version" class="text-left"></div>
<?php endif; ?>

<div class='jOpenSim_jsonstats table-responsive'>
<table class="table table-striped table-condensed table-hover table-jsonstats">
    <thead>
        <tr>
            <th>Name:</th>
            <th class="text-right">Value:</th>
        </tr>
    </thead>
</table>
</div>
