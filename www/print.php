<?php
include 'settings.php';
include 'lib/checks.php';

$badgeType = $settings["badges"]["default"];
$visitorId = isset($_GET['id']) ? intval($_GET['id']) : null;

if (!badgeExists($badgeType) || !visitorExists($visitorId)) {
    header("Location: index.php");
    exit;
}

$barcode = getExibitionBarcode($visitorId);
if ($barcode !== FALSE && !$settings["pool_disabled"]) {
    $barcode = assignBarcode($visitorId);
}

$visitor = getVisitors($visitorId);
if ($visitor["visitor_barcode"] != NULL) {
    setFromBarcode($visitor["visitor_barcode"]);
}
?>

<html>
    <head>
        <link rel="stylesheet" type="text/css" href="css/badges.css" />
        <script type="text/javascript">
            setTimeout(function(){
                window.location.href = "index.php";
            }, 5000);
        </script>
    </head>
    <?php if ($barcode || $settings["pool_disabled"]) { ?>
        <body>
        <center>
            <h1><?= tr("print.take_your_badge") ?></h1>
            <img class="<?= $badgeType ?>" src="draw.php?id=<?= $visitorId ?>"/>
        </center>
    </body>
<?php } else { ?>
    <body>
        <?= tr("error.no_barcodes"); ?>
    </body>
<?php } ?>
</html>
