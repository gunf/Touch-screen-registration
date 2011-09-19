<?php

include 'db.php';

if (!isset($_GET['type'])) {
    print "Unknown mode";
    exit;
}

function arrayToCSV($array, $fields, $separator = ";") {
    $fieldsSlashed = array(implode("$separator ", $fields));

    foreach ($array as $item) {
        $slashed = array();
        foreach ($fields as $field) {
            $slashed[] = str_replace("$separator", "\$separator", ($item[$field]));
        }
        $fieldsSlashed[] = implode("$separator ", $slashed);
    }
    return implode("\n", $fieldsSlashed) . "\n";
}

function exportCSV($table) {
    global $visitorFields;

    switch ($table) {
        case "visitors":
            $visitors = getVisitors();
            $csv = arrayToCSV($visitors, $visitorFields);
            break;
        case "barcodes":
            $barcodes = getBarcodes();
            $csv = arrayToCSV($barcodes, array("visitor_barcode", "exibition_barcode"));
            break;

        case "exibitions":
            $exibitions = getExibitions();
            $csv = arrayToCSV($exibitions, array("exibition_id", "title_ru", "title_en", "active"));
            break;

        case "members":
            $members = getMembers();
            $csv = arrayToCSV($members, array("exibition_id", "visitor_barcode"));
            break;
    }
    header("Content-Type: text/csv");
    header("Content-Disposition:attachment;filename=$table.csv");
    header("Content-Length: " . strlen($csv));
    return $csv;
}

$type = $_GET['type'];

switch ($type) {
    case "countries":
        $countries = getCountries($_GET['term']);
        $countryJson = array();
        foreach ($countries as $id => $name) {
            $townJson[] = array(
                "label" => $name[$currentLanguage],
                "value" => $id,
            );
        }
        print json_encode($townJson);
        break;

    case "towns":
        $towns = getTowns($_GET['country'], $_GET['term'], 8);
        $townJson = array();

        foreach ($towns as $id => $name) {
            $townJson[] = array(
                "label" => $name[$currentLanguage],
                "value" => $id,
            );
        }
        print json_encode($townJson);
        break;

    case "csv":
        $csv = exportCSV($_GET["table"]);
        print $csv;
        break;

    case "barcode_check":
        if (isset($_GET["barcode"]) && intval($_GET["barcode"])) {
            $barcode = getIdForBarcode($_GET["barcode"]);
            if ($barcode) {
                $visitor = getVisitors($barcode);
                print json_encode(array("exists" => count($visitor) > 0, "visitor" => $visitor));
            } else {
                print json_encode(array("exists" => false));
            }
        } else {
            print json_encode(array("exists" => false));
        }
}
?>