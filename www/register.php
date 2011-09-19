<?php

include 'db.php';
include 'lib/checks.php';

$fields = array("lastname", "firstname", "patronymic",
    "post", "company", "country", "town",
    "address", "postcode", "email", "website",
    "exibitions", "phone", "fax", "postcode");

$badgeType = $settings["badges"]["default"];
$form = validateRegform($_POST, $fields, $settings["regform"]["mandatory"]);

$error = "";
$id = null;

if ($form) {
    $id = addVisitor($form);
    if ($id !== FALSE) {
        if (assignBarcode($id)){
            header("Location: print.php?id=$id");
            exit;
        } else {
            $error = "error.regform.barcode_err";
        }
    } else {
        $error = "error.regform.register_err";
    }
} else {
    $error = "error.regform.form_err";
}

if ($error != "" && $id != null) {
    include_once 'inc/header.php';
    print tr($error);
    include_once 'inc/footer.php';
} else {
    header ("Location: print.php?type=".$settings['badges']['default']."&id=$id");
    exit;
}
?>
