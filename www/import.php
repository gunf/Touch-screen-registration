<?php

include_once 'db.php';
include_once 'settings.php';

$importTable = isset($_POST["import_type"]) ? $_POST["import_type"] : "";
$csvFilename = isset($_FILES["csv"]["tmp_name"]) ? $_FILES["csv"]["tmp_name"] : "";


$defaultFields = array(
    "visitors" => array("year", "regdate", "fio", "company", "post", "phone", "email", "news", "country", "town", "address", "postcode", "visitor_barcode"),
    "individuals" => array("a", "b", "email", "regdate", "firstname", "lastname", "patronymic", "company", "post", "country", "postcode", "town", "address", "phone", "fax", "visitor_barcode"),
    "exibitions" => array("exibition_id", "title_ru", " title_en", " active"),
    "barcodes" => array("exibition_barcode", "visitor_barcode"),
    "members" => array("exibition_id", "visitor_barcode")
);

function addToTable($table, $item) {
    switch ($table) {
        case "individuals":
            return addVisitor($item);
        case "visitors":
            return addVisitor($item);
        case "barcodes":
            return addBarcode($item);
        case "exibitions":
            return addExibition($item);
        case "members":
            return addMembership($item);
    }
}

function readCSV($fname, $table, $separator = ";", $fields = null, $isCp1251 = false) {

    $items = array();

    $file = fopen($fname, "r");
    if ($fields == null) {
        $fields = explode_escaped($separator, fgets($file));
    }

    $errors = array();
    $lineNumber = 0;

    while (($values = fgetcsv($file, 1000, ";")) !== FALSE) {
        if ($isCp1251) {
            for ($i = 0; $i < count($values); $i++) {
                $values[$i] = mb_convert_encoding($values[$i], "UTF-8", "CP1251");
            }
        }

        $item = array();
        for ($i = 0; $i < count($fields); $i++) {
            $fieldName = trim($fields[$i]);

            if ($fieldName == "fio") {
                $fioExploded = preg_split("/[\s]+/", $values[$i]);
                $item["lastname"] = $fioExploded[0];
                $item["firstname"] = $fioExploded[1];
                $item["patronymic"] = $fioExploded[2];
            } else {
                $item[$fieldName] = $values[$i];
            }
        }

        $items[] = $item;
    }
    fclose($file);


    if ($table == "visitors" || $table == "individuals") {
        addVisitorsFromArray($items);
    } else {
        foreach ($items as $item) {
            $status = addToTable($table, $item);
            if (!$status) {
                $errors[$lineNumber] = array(
                    "line" => $values,
                    "item" => $item
                );
            }
        }
    }

    return $errors;
}

$importStatus = array();

if ($csvFilename != "" && in_array($importTable, $settings["db"]["imports"])) {
    if (array_key_exists($importTable, $defaultFields)) {
        $importStatus = readCSV($csvFilename, $importTable, ";", $defaultFields[$importTable], isset($_POST["charset"]) && $_POST["charset"] == "cp1251");
    } else {
        $importStatus = readCSV($csvFilename, $importTable);
    }
}

if (count($importStatus) == 0) {
    header("Location: admin.php");
    exit;
} else {
    print "При имортировании возникли некоторые ошибки:";
    print "<pre>" . print_r($importStatus, true) . "</pre>";
}
?>