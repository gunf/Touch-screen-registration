<?php
include 'settings.php';
include 'lib/pager.php';

if (!$admin) {
    header("Location: auth.php");
    exit;
}

$simple = true;

include 'inc/header.php';
include 'db.php';

if (isset($_GET["action"]) && $_GET["action"] == "delete" &&
        isset($_GET["id"]) && intval($_GET["id"]) > 0) {
    $error = removeVisitor(intval($id));
}

$tableHeader = array("visitor_barcode", "exibition_barcode", "firstname", "lastname", "patronymic",
    "post", "company", "country_name", "town_name",
    "address", "postcode", "email", "website",
    "exibitions", "preregister", "ticket_printed");

$barcodesPerPage = 10;
$visitorsPerPage = 10;
$barcodesCurrent = 0;
$visitorsCurrent = 0;

if (isset($_GET["barcodes_per_page"]) && intval($_GET["barcodes_per_page"]) > 0) {
    $barcodesPerPage = intval($_GET["barcodes_per_page"]);
}

if (isset($_GET["visitors_per_page"]) && intval($_GET["visitors_per_page"]) > 0) {
    $visitorsPerPage = intval($_GET["visitors_per_page"]);
}

if (isset($_GET["barcodes_current"]) && intval($_GET["barcodes_current"]) > 0) {
    $barcodesCurrent = intval($_GET["barcodes_current"]);
}

if (isset($_GET["visitors_current"]) && intval($_GET["visitors_current"]) > 0) {
    $visitorsCurrent = intval($_GET["visitors_current"]);
}

$visitorsCount = getVisitorsCount();
$barcodesCount = getBarcodesCount();

$visitorsPager = new Pager("visitors", $visitorsCount, $visitorsCurrent, $visitorsPerPage);
$barcodesPager = new Pager("barcodes", getBarcodesCount(), $barcodesCurrent, $barcodesPerPage);

$visitors = getVisitors(null, $visitorsPager->getPaging());
$barcodes = getBarcodes($barcodesPager->getPaging());

$exibitions = getExibitions();
?>
<div class="admin_nest">
    <ul>
        <?php if (isset($error)) { ?>
            <div class="error">
                <?= $error ?>
            </div>
        <?php } ?>
        <li>
            <h1 class="admin_header" onclick='$("#users").toggle();'><?= tr("admin.users") ?>(<?= $visitorsCount ?>)</h1>
            <div class="users" id="users">
                <?= $visitorsPager->getHtml("pager", $barcodesPager->getParams()) ?>
                <table>
                    <thead>
                        <tr>
                            <?php foreach ($tableHeader as $field) { ?>
                                <th><?= tr("admin.table_header.$field") ?></th>
                            <? } ?>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (count($visitors) > 0) {
                            foreach ($visitors as $visitor) {
                                ?>
                                <tr>
                                    <?php foreach ($tableHeader as $field) { ?>
                                        <td><?= $visitor[$field] ?></td>
                                    <?php } ?>
                                    <td>
                                        <a href="draw.php?id=<?= $visitor['id'] ?>"><?= tr("admin.plastic") ?></a><br/>
                                        <a href="draw.php?id=<?= $visitor['id'] ?>"><?= tr("admin.paper") ?></a><br/>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td colspan="<?= count($tableHeader) + 1 ?>" class="span">
                                    <?= tr("admin.no_records") ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </li>
        <li>
            <h1 class="admin_header" onclick='$("#barcodes").toggle();'><?= tr("admin.barcodes") ?>(<?= $barcodesCount ?>)</h1>
            <div class="users" id="barcodes">
                <?= $barcodesPager->getHtml("pager", $visitorsPager->getParams()) ?>
                <table>
                    <thead>
                    <th><?= tr("admin.table_header.barcode") ?></th>
                    <th><?= tr("admin.table_header.visitor") ?></th>
                    </thead>
                    <tbody>
                        <?php
                        if (count($barcodes) > 0) {
                            foreach ($barcodes as $barcode) {
                                ?>
                                <tr>
                                    <td><?= $barcode["exibition_barcode"] ?></td><td><?= $barcode["lastname"] ?> <?= $barcode["firstname"] ?> <?= $barcode["patronymic"] ?></td>
                                </tr>
                                <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td colspan="<?= count($tableHeader) + 1 ?>" class="span">
                                    <?= tr("admin.no_records") ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </li>
        <li>
            <h1 class="admin_header" onclick='$("#exibitions").toggle();'><?= tr("admin.exibitions") ?>(<?= count($exibitions) ?>)</h1>
            <ol id="exibitions">
                <?php
                if (count($exibitions) > 0) {
                    foreach ($exibitions as $id => $exibition) {
                        ?>
                        <li <?= ($exibition["active"] ? "" : 'class="inactive" ') ?>>
                            (<?= $exibition["members"] ?>)
                            <?= $exibition["title_ru"] ?></li>
                    <?php }
                } ?>
            </ol>            
        </li>
        <li>
            <h1  class="admin_header" onclick='$("#import_export").toggle();'><?= tr("admin.import_export") ?></h1>
            <ul id="import_export">
                <li>
                    <?= tr("admin.export") ?>: 
                    <a href="export.php?type=csv&table=visitors"><?= tr("admin.users") ?></a>,
                    <a href="export.php?type=csv&table=barcodes"><?= tr("admin.barcodes") ?></a>,
                    <a href="export.php?type=csv&table=exibitions"><?= tr("admin.exibitions") ?></a>,
                    <a href="export.php?type=csv&table=members"><?= tr("admin.members") ?></a>
                </li>
                <li><h3><?= tr("admin.load_from_csv") ?></h3>
                    <form method="POST" action="import.php" enctype="multipart/form-data">
                        <input type="checkbox" name="charset" value="cp1251" id="cp1251" checked="checked" /><label for="cp1251"><?=tr("admin.import.cp1251")?></label></br>
                        <input type="radio" name="import_type" value="visitors" id="import_visitors" /><label for="import_visitors"><?= tr("admin.import.visitors") ?></label><br/>
                        <input type="radio" name="import_type" value="individuals" id="import_individuals" /><label for="import_individuals"><?= tr("admin.import.individuals") ?></label><br/>
                        <input type="radio" name="import_type" value="barcodes" id="import_barcodes" /><label for="import_barcodes"><?= tr("admin.import.barcodes") ?></label><br/>
                        <input type="radio" name="import_type" value="exibitions" id="import_exibitions" /><label for="import_exibitions"><?= tr("admin.import.exibitions") ?></label><br/>
                        <input type="radio" name="import_type" value="members" id="import_members" /><label for="import_members"><?= tr("admin.import.members") ?></label><br/>
                        <input type="file" name="csv"/>
                        <input type="submit" value="OK" />
                    </form>
                </li>
            </ul>
    </ul>
</li>
</div>
<?php
include 'inc/footer.php';
?>