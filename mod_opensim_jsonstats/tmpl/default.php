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
Dilatn = 0;
SimFPS = 0;
PhyFPS = 0;
AgntUp = 0;
RootAg = 0;
ChldAg = 0;
Prims  = 0;
AtvPrm = 0;
AtvScr = 0;
ScrLPS = 0;
PktsIn = 0;
PktOut = 0;
PendDl = 0;
PendUl = 0;
UnackB = 0;
TotlFt = 0;
NetFt  = 0;
PhysFt = 0;
OthrFt = 0;
AgntFt = 0;
ImgsFt = 0;
Memory = 0;
Uptime = "";
Version = "";
FrameDilatn = 0;
// LoggingInUsers = 0;
GeoPrims = 0;
// MeshObjects = 0;
// XEngineThreadCount = 0;
// UtilThreadCount = 0;
// SystemThreadCount = 0;
ProcMem = 0;

setInterval(function() {
    $.getJSON("<?php echo $jsonstatsURI; ?>?callback=?",
    function(data) {
        Dilatn      = data.Dilatn;
        SimFPS      = Math.round(data.SimFPS);
        PhyFPS      = Math.round(data.PhyFPS);
        AgntUp      = data.AgntUp;
        RootAg      = data.RootAg;
        ChldAg      = data.ChldAg;
        Prims       = data.Prims;
        AtvPrm      = data.AtvPrm;
        AtvScr      = data.AtvScr;
        ScrLPS      = data.ScrLPS;
        PktsIn      = data.PktsIn;
        PktOut      = data.PktOut;
        PendDl      = data.PendDl;
        PendUl      = data.PendUl;
        UnackB      = data.UnackB;
        TotlFt      = data.TotlFt;
        NetFt       = data.NetFt;
        PhysFt      = data.PhysFt;
        OthrFt      = data.OthrFt;
        AgntFt      = data.AgntFt;
        ImgsFt      = data.ImgsFt;
        Memory      = Math.round(data.Memory);
        Uptime      = data.Uptime;
        Version     = data.Version;
        FrameDilatn = data.FrameDilatn;
        // LoggingInUsers = data.Logging in Users;
        GeoPrims    = data.GeoPrims;
        // MeshObjects = data.Mesh Objects;
        // XEngineThreadCount = data.XEngine Thread Count;
        // UtilThreadCount = data.Util Thread Count;
        // SystemThreadCount = data.System Thread Count;
        ProcMem     = data.ProcMem;

        // Load in span
        $('#Dilatn').text(Dilatn);
        $('#SimFPS').text(SimFPS);
        $('#PhyFPS').text(PhyFPS);
        $('#AgntUp').text(AgntUp);
        $('#RootAg').text(RootAg);
        $('#ChldAg').text(ChldAg);
        $('#Prims').text(Prims);

        $('#AtvPrm').text(AtvPrm);
        $('#AtvScr').text(AtvScr);
        $('#ScrLPS').text(ScrLPS);
        $('#PktsIn').text(PktsIn);
        $('#PktOut').text(PktOut);
        $('#PendDl').text(PendDl);
        $('#PendUl').text(PendUl);
        $('#UnackB').text(UnackB);
        $('#TotlFt').text(TotlFt);
        $('#NetFt').text(NetFt);
        $('#PhysFt').text(PhysFt);
        $('#OthrFt').text(OthrFt);
        $('#AgntFt').text(AgntFt);
        $('#ImgsFt').text(ImgsFt);
        $('#Memory').text(Memory + " Mb");
        $('#Uptime').text(Uptime);
        $('#Version').text("Version: " + Version);
        $('#FrameDilatn').text(FrameDilatn);

        $('#GeoPrims').text(GeoPrims);
        $('#ProcMem').text(ProcMem);
    })
}, <?php echo $refreshrate; ?>);
</script>

<?php if ($params->get('jsonstatsversion')): ?>
<div id="Version" class="">
    <i class="glyphicon glyphicon-refresh spin"></i>
</div>
<?php endif; ?>

<div class='jOpenSim_jsonstats table-responsive'>
    <table class="table table-striped table-condensed table-hover">
    <thead>
        <tr>
            <th>Name:</th>
            <th class="text-right">Value:</th>
        </tr>
    </thead>
    
    <tbody>
    <tr>
        <td>Dilatn</td>
        <td class="text-right">
            <span id="Dilatn" class="label label-default">
                <i class="glyphicon glyphicon-refresh spin"></i>
            </span>
        </td>
    </tr>

    <tr>
        <td>SimFPS</td>
        <td class="text-right">
            <span id="SimFPS" class="label label-default">
                <i class="glyphicon glyphicon-refresh spin"></i>
            </span>
        </td>
    </tr>

    <tr>
        <td>PhyFPS</td>
        <td class="text-right">
            <span id="PhyFPS" class="label label-default">
                <i class="glyphicon glyphicon-refresh spin"></i>
            </span>
        </td>
    </tr>

    <tr>
        <td>AgntUp</td>
        <td class="text-right">
            <span id="AgntUp" class="label label-default">
                <i class="glyphicon glyphicon-refresh spin"></i>
            </span>
        </td>
    </tr>

    <tr>
        <td>RootAg</td>
        <td class="text-right">
            <span id="RootAg" class="label label-default">
                <i class="glyphicon glyphicon-refresh spin"></i>
            </span>
        </td>
    </tr>

    <tr>
        <td>ChldAg</td>
        <td class="text-right">
            <span id="ChldAg" class="label label-default">
                <i class="glyphicon glyphicon-refresh spin"></i>
            </span>
        </td>
    </tr>

    <tr>
        <td>Prims</td>
        <td class="text-right">
            <span id="Prims" class="label label-default">
                <i class="glyphicon glyphicon-refresh spin"></i>
            </span>
        </td>
    </tr>

    <tr>
        <td>AtvPrm</td>
        <td class="text-right">
            <span id="AtvPrm" class="label label-default">
                <i class="glyphicon glyphicon-refresh spin"></i>
            </span>
        </td>
    </tr>

    <tr>
        <td>AtvScr</td>
        <td class="text-right">
            <span id="AtvScr" class="label label-default">
                <i class="glyphicon glyphicon-refresh spin"></i>
            </span>
        </td>
    </tr>

    <tr>
        <td>ScrLPS</td>
        <td class="text-right">
            <span id="ScrLPS" class="label label-default">
                <i class="glyphicon glyphicon-refresh spin"></i>
            </span>
        </td>
    </tr>

    <tr>
        <td>PktsIn</td>
        <td class="text-right">
            <span id="PktsIn" class="label label-default">
                <i class="glyphicon glyphicon-refresh spin"></i>
            </span>
        </td>
    </tr>

    <tr>
        <td>PktOut</td>
        <td class="text-right">
            <span id="PktOut" class="label label-default">
                <i class="glyphicon glyphicon-refresh spin"></i>
            </span>
        </td>
    </tr>

    <tr>
        <td>PendDl</td>
        <td class="text-right">
            <span id="PendDl" class="label label-default">
                <i class="glyphicon glyphicon-refresh spin"></i>
            </span>
        </td>
    </tr>

    <tr>
        <td>PendUl</td>
        <td class="text-right">
            <span id="PendUl" class="label label-default">
                <i class="glyphicon glyphicon-refresh spin"></i>
            </span>
        </td>
    </tr>

    <tr>
        <td>UnackB</td>
        <td class="text-right">
            <span id="UnackB" class="label label-default">
                <i class="glyphicon glyphicon-refresh spin"></i>
            </span>
        </td>
    </tr>

    <tr>
        <td>TotlFt</td>
        <td class="text-right">
            <span id="TotlFt" class="label label-default">
                <i class="glyphicon glyphicon-refresh spin"></i>
            </span>
        </td>
    </tr>

    <tr>
        <td>NetFt</td>
        <td class="text-right">
            <span id="NetFt" class="label label-default">
                <i class="glyphicon glyphicon-refresh spin"></i>
            </span>
        </td>
    </tr>

    <tr>
        <td>PhysFt</td>
        <td class="text-right">
            <span id="PhysFt" class="label label-default">
                <i class="glyphicon glyphicon-refresh spin"></i>
            </span>
        </td>
    </tr>

    <tr>
        <td>OthrFt</td>
        <td class="text-right">
            <span id="OthrFt" class="label label-default">
                <i class="glyphicon glyphicon-refresh spin"></i>
            </span>
        </td>
    </tr>

    <tr>
        <td>AgntFt</td>
        <td class="text-right">
            <span id="AgntFt" class="label label-default">
                <i class="glyphicon glyphicon-refresh spin"></i>
            </span>
        </td>
    </tr>

    <tr>
        <td>ImgsFt</td>
        <td class="text-right">
            <span id="ImgsFt" class="label label-default">
                <i class="glyphicon glyphicon-refresh spin"></i>
            </span>
        </td>
    </tr>

    <tr>
        <td>Memory</td>
        <td class="text-right">
            <span id="Memory" class="label label-default">
                <i class="glyphicon glyphicon-refresh spin"></i>
            </span>
        </td>
    </tr>

    <tr>
        <td>Uptime</td>
        <td class="text-right">
            <span id="Uptime" class="label label-default">
                <i class="glyphicon glyphicon-refresh spin"></i>
            </span>
        </td>
    </tr>

    <!--
    <tr>
        <td>Version</td>
        <td class="text-right">
            <span id="Version" class="label label-default">
                <i class="glyphicon glyphicon-refresh spin"></i>
            </span>
        </td>
    </tr>
    -->

    <tr>
        <td>FrameDilatn</td>
        <td class="text-right">
            <span id="FrameDilatn" class="label label-default">
                <i class="glyphicon glyphicon-refresh spin"></i>
            </span>
        </td>
    </tr>

    <!--
    // LoggingInUsers = data.Logging in Users;
    -->

    <tr>
        <td>GeoPrims</td>
        <td class="text-right">
            <span id="GeoPrims" class="label label-default">
                <i class="glyphicon glyphicon-refresh spin"></i>
            </span>
        </td>
    </tr>

    <!--
    // MeshObjects = data.Mesh Objects;
    // XEngineThreadCount = data.XEngine Thread Count;
    // UtilThreadCount = data.Util Thread Count;
    // SystemThreadCount = data.System Thread Count;
    -->

    <tr>
        <td>ProcMem</td>
        <td class="text-right">
            <span id="ProcMem" class="label label-default">
                <i class="glyphicon glyphicon-refresh spin"></i>
            </span>
        </td>
    </tr>

    <tbody>
    </table>
</div>