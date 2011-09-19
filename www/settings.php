<?php

session_start();

$settings = array(
    "db" => array(
        "hostname" => "localhost",
        "database" => "primexpo",
        "username" => "primexpo",
        "password" => "primexpo",
        "imports" => array(
            "visitors", "exibitions", "members", "barcodes", "individuals"
        ),
    ),
    "printer" => "printer name",
    "pool_disabled" => true,
    "badges" => array(
        "default" => "paper",
        "plastic" => array(
            "layout" => array(
                "normal" => array(
                    "fields" => array(
                        array("lastname", "firstname", "patronymic"),
                        array("post", "company")
                    ),
                    "fontsize" => 40,
                    "bounds" => array(450, 1540, 1900, 1890),
                    "padding" => array(100, 100, 100, 100),
                    "interval" => 30,
                    "angle" => 0
                )
            ),
            "font" => "img/fonts/LiberationSans-Regular.ttf",
            "barcode" => array(
                "position" => array(800, 2150),
                "height" => 150,
                "barwidth" => 8,
                "fontsize" => 30
            ),
            "template" => "img/badges/plastic.png",
            "background" => true,
            "type" => "image/png"
        ),
        "paper" => array(
            "font" => "img/fonts/LiberationSans-Regular.ttf",
            "layout" => array(
                "normal" => array(
                    "fields" => array(
                        array("lastname", "firstname", "patronymic"),
                        array("post", "company")
                    ),
                    "fontsize" => 1000,
                    "bounds" => array(0, 1500, 1500, 2000),
                    "padding" => array(100, 100, 100, 100),
                    "interval" => 40,
                    "angle" => 0,
                ),
                "rotated" => array(
                    "fields" => array(
                        array("lastname", "firstname", "patronymic"),
                        array("post", "company"),
                    ),
                    "bounds" => array(0, 300, 1500, 770),
                    "padding" => array(110, 100, 100, 90),
                    "fontsize" => 70,
                    "interval" => 30,
                    "angle" => 180
                ),
            ),
            "font" => "img/fonts/LiberationSans-Regular.ttf",
            "barcode" => array(
                "position" => array(50, 2050),
                "height" => 150,
                "barwidth" => 3,
                "fontsize" => 14,
            ),
            "template" => "img/badges/paper.png",
            "background" => true,
            "type" => "image/png",
        )
    ),
    "admin" => array(
        "password" => "123456"
    ),
    "regform" => array(
        "fields" => array(
            array("lastname", "firstname", "patronymic"),
            array("company", "post", "email", "website"),
            array("country", "town", "postcode", "address", "phone", "fax")
        ),
        "mandatory" => array("firstname", "lastname", "patronymic", "post",
            "company", "address", "postcode", "house", "email"),
        "checkme" => array("firstname", "lastname", "patronymic", "company", "post")
    ),
    "first_countries" => array("Россия", "Украина", "Беларусь", "Гваделупа"),
    "languages" => array(
        "default" => "ru",
        "ru" => array(
            "enabled" => true,
            "icon" => "img/flags/ru.png",
            "name" => "Русский",
            "filename" => "lang/lang_ru.properties",
        ),
        "en" => array(
            "enabled" => true,
            "icon" => "img/flags/uk.png",
            "name" => "English",
            "filename" => "lang/lang_en.properties"
        )
    )
);

//Языки
if (isset($_GET["lang"])) {
    $currentLanguage = $_GET["lang"];
    $_SESSION["lang"] = $currentLanguage;
} elseif (isset($_SESSION["lang"])) {
    $currentLanguage = $_SESSION["lang"];
}

//Авторизация
$password = isset($_POST['password']) ? $_POST['password'] : NULL;

if ($password == $settings["admin"]["password"]) {
    $_SESSION["auth"] = true;
};

if (isset($_GET['action']) && $_GET['action'] == "logout") {
    unset($_SESSION["auth"]);
}

$admin = isset($_SESSION["auth"]);

session_write_close();

if (!isset($currentLanguage)) {
    $currentLanguage = "ru";
}

if (!isset($settings["languages"][$currentLanguage]) ||
        !$settings["languages"][$currentLanguage]["enabled"]) {
    $currentLanguage = "ru";
}

$languages = array();
foreach ($settings["languages"] as $lang => $params) {
    if ($params["enabled"]) {
        $languages[$lang] = $params;
    }
}

$translation = array();
include_once("lang/$currentLanguage.php");

function tr($msg) {
    global $translation, $currentLanguage;

    return array_key_exists($msg, $translation) ? $translation[$msg] : "--$msg not found in $currentLanguage--";
}

//База данных
$db = new mysqli($settings["db"]["hostname"], $settings["db"]["username"], $settings["db"]["password"], $settings["db"]["database"]);

?>
