<?php

include_once 'db.php';
include_once 'settings.php';

function visitorExists($visitorId) {
    $visitorId = intval($visitorId);
    if ($visitorId == 0) {
        return false;
    }
    $visitor = getVisitors($visitorId);
    return (count($visitor) > 0);
}

function badgeExists($badgeType) {
    global $settings;

    if (array_key_exists($badgeType, $settings["badges"]) && is_array($settings["badges"][$badgeType])) {
        return "settings";
    }
    if (file_exists("badges/$badgeType.json")) {
        return "file";
    }

    return false;
}

function validateRegform($data, $fields, $mandatory) {
    $form = array();
    foreach ($fields as $field) {
        if (in_array($field, $mandatory) && (!isset($data[$field]) || (!is_array($data[$field]) && trim($data[$field]) == ""))) {
            return false;
        }
        $form[$field] = is_array($data[$field]) ? $data[$field] : trim($data[$field]);
    }
    $form["preregister"] = false;
    $form["ticket_printed"] = true;

    return $form;
}

?>
