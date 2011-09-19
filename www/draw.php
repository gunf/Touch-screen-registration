<?php

include_once 'settings.php';
include_once 'db.php';
include_once 'lib/checks.php';
require_once 'lib/ean13.php';

function textSize($fontsize, $font, $rotation, $text) {
    $bbox = imagettfbbox($fontsize, $rotation, $font, $text);

    $w = abs($bbox[0]) + abs($bbox[2]);
    $h = abs($bbox[5]) + abs($bbox[1]);
    return array($w, $h);
}

function overflow($a, $b) {
    return ($a[0] > $b[0]) || ($a[1] > $b[1]);
}

function getTextParams($lines, $font, $bounds, $interval, $rotation, $maxFontSize) {
    $fontSize = 0;
    $lineParams = array();
    do {
        $textBox = array(0, 0);
        for ($i = 0; $i < count($lines); $i++) {
            $lineSize = textSize($fontSize, $font, $rotation, $lines[$i]);
            $textBox[0] = max(array($textBox[0], $lineSize[0]));

            $textBox[1] += $lineSize[1] + $interval;
            $k = $rotation == "180" ? -1 : 1;
            $lineParams[$lines[$i]] = array($bounds[0] / 2 - $k * $lineSize[0] / 2, $textBox[1]);
        }
        $fontSize++;
    } while (!overflow($textBox, $bounds) && $fontSize < $maxFontSize);

    return array("lines" => $lineParams, "fontSize" => $fontSize);
}

function drawLayout($img, $form, $params, $font, $color) {
    $text = array();
    foreach ($params["fields"] as $line) {
        $values = array();
        foreach ($line as $field) {
            $values[] = $form[$field];
        }
        $text[] = implode(" ", $values);
    }

    $textRegionSize = array(
        ($params["bounds"][2] - $params["bounds"][0]) - ($params["padding"][2] + $params["padding"][0]),
        ($params["bounds"][3] - $params["bounds"][1]) - ($params["padding"][3] + $params["padding"][1])
    );

    $textParams = getTextParams($text, $font, $textRegionSize, $params["interval"], $params["angle"], $params["fontsize"]);
    foreach ($textParams["lines"] as $line => $pos) {
        imagettftext($img, $textParams["fontSize"], $params["angle"], $pos[0] + $params["padding"][0] + $params["bounds"][0], $pos[1] + $params["padding"][1] + $params["bounds"][1], $color, $font, $line);
    }
}

function drawPng($form, $badgeType) {
    global $settings;

    $params = $settings["badges"][$badgeType];

    if (!$settings["pool_disabled"]) {
        $ean = new Ean13();
        $ean->_barwidth = $params["barcode"]["barwidth"];
        $ean->_barcodeheight = $params["barcode"]["height"];
        $ean->_font = $params["barcode"]["fontsize"];
        $ean = $ean->draw($form["exibition_barcode"]);
    }

    $img = imagecreatefrompng($params["template"]);
    $black = imagecolorallocate($img, 0, 0, 0);

    if (!$params["background"]) {
        $width = imagesx($img);
        $height = imagesy($img);
        $alpha = imagecolorallocate($img, 255, 255, 255);
        imagefilledrectangle($img, 0, 0, $width, $height, $alpha);
    }


    foreach ($params["layout"] as $layout) {
        drawLayout($img, $form, $layout, $params["font"], $black);
    }

    if (!$settings["pool_disabled"]) {
        if (getExibitionBarcode($form["id"]) != 0) {
            $ean = new Ean13();
            $ean->_barwidth = $params["barcode"]["barwidth"];
            $ean->_barcodeheight = $params["barcode"]["height"];
            $ean->_font = $params["barcode"]["fontsize"];
            $ean = $ean->draw($form["exibition_barcode"]);
            imagecopy($img, $ean, $params["barcode"]["position"][0], $params["barcode"]["position"][1], 0, 0, imagesx($ean), imagesy($ean));
        }
    }
    
    imagepng($img, 'file.png');
    system("rundll32 shimgvw.dll ImageView_PrintTo /pt ".realpath("file.png")." \"${settings["printer"]}\" ");
    imagepng($img);
    imagedestroy($img);
}

$visitorId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$badgeType = $settings["badges"]["default"];

if (!visitorExists($visitorId)) {
    print "No such visitor";
    exit;
}

$badgeExists = badgeExists($badgeType);
if (!$badgeExists) {
    print "No such badge";
    exit;
}

header("Content-Type: image/png");
drawPng(getVisitors($visitorId), $badgeType);
?>