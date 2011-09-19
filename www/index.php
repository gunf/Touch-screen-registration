<?php
include 'inc/header.php';
include_once 'db.php';

$noBarcodes = !getFreeBarcodes() ;
?>
<div class="welcome">
    <h1><?= tr("index.welcome.title") ?></h1>
    <h2><?= tr("index.welcome.subtitle") ?></h2>
    <?php if ($noBarcodes && !$settings["pool_disabled"]) { ?>
        <h3 class="error"><?= tr("error.no_barcodes") ?></h3>
    <?php } ?>    
</div>
<a href="barcode.php">
    <button class="left_button mainbutton" <?=$noBarcodes && !$settings["pool_disabled"]?'disabled="disabled"':""?>  >
        <h1><?= tr("index.links.ihaveticket") ?>,</h1>
        <h2><?= tr("index.links.scanbarcode") ?></h2>
    </button>    
</a>

<a href="regform.php">
    <button class="right_button mainbutton" <?=$noBarcodes && !$settings["pool_disabled"]?'disabled="disabled"':""?> >
        <h1><?= tr("index.links.ihavenoticket") ?>,</h1>
        <h2><?= tr("index.links.ineedregister") ?></h2>
    </button>    
</a>

    <div class="law" id="sponsors">
        <center>
            <div class="law_text">
                <?php include 'sponsors.html'; ?>
            </div>
            <button class="big_button"  onclick='$("#sponsors").toggle();'>OK</button>
        </center>
    </div>    
    <div class="sponsors">
        <div class="title"><a href="javascript:void(0);" onclick='$("#sponsors").toggle();'><?= tr("sponsors") ?></a></div>
        <img src="img/logos.png" />
    </div>    
<?php
include 'inc/footer.php';
?>