<?php

include_once 'settings.php';

$visitorFields = array("visitor_barcode", "firstname", "lastname", "patronymic",
    "post", "company", "country", "town",
    "address", "postcode", "email", "website",
    "exibitions", "preregister", "ticket_printed");

function getVisitorsCount() {
    global $db;
    $result = mysqli_query($db, "SELECT COUNT(*) FROM visitor");
    $array = mysqli_fetch_array($result);

    return $array[0];
}

function assignExibitions($visitorId, $exibitions) {
    global $db;

    $result = mysqli_query($db, "INSERT INTO exibition_membership (exibition_id, visitor_barcode) 
                                 SELECT exibition_id, $visitorId
                                 FROM exibition 
                                 WHERE active AND exibition_id IN (" . implode(", ", $exibitions) . ")
                                 ON DUPLICATE KEY UPDATE exibition_id = VALUES(exibition_id)");

    return $db->error;
}

function getIdForBarcode($visitorId) {
    global $db;
    $result = mysqli_query($db, "SELECT id FROM visitor WHERE visitor_barcode = '$visitorId'");
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row["id"];
    }
    return 0;
}

function getVisitors($visitorId = null, $paging = null) {
    global $db;

    $visitors = array();
    if ($visitorId === null || is_numeric($visitorId)) {

        $result = mysqli_query($db, "SELECT v.id, v.visitor_barcode, v.firstname, v.lastname, v.patronymic,   
                        v.post, v.company, v.country, v.town, v.phone, v.fax,
                        v.address, v.postcode, v.email, v.website,     
                        v.preregister, v.ticket_printed ,
                        v.town AS town_name, v.country AS country_name,
                        b.exibition_barcode AS exibition_barcode,
                        GROUP_CONCAT(ex.title_ru) AS exibitions
                 FROM visitor v
                 LEFT JOIN exibition_barcode b ON b.visitor_id = v.id
                 LEFT JOIN exibition_membership em ON em.visitor_barcode = v.visitor_barcode
                 LEFT JOIN exibition ex ON ex.exibition_id = em.exibition_id 
                 " .
                ($visitorId != null ? "WHERE v.id = $visitorId" : "") . "
                  GROUP BY v.visitor_barcode " .
                ($paging != null ? "LIMIT ${paging[0]}, ${paging[1]}" : ""));
        print $db->error;
        if ($visitorId != null) {
            $visitors = mysqli_fetch_assoc($result);
        } else {
            while ($row = mysqli_fetch_assoc($result)) {
                $visitors[$row["visitor_barcode"]] = $row;
            }
        }
    }
    return $visitors;
}

function getBarcodesCount() {
    global $db;
    $result = mysqli_query($db, "SELECT COUNT(*) FROM exibition_barcode");
    $array = mysqli_fetch_array($result);

    return $array[0];
}

function getMembers() {
    global $db;

    $result = mysqli_query($db, "SELECT * FROM exibition_membership");
    $members = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $members[] = $row;
    }

    return $members;
}

function getBarcodes($paging = null) {
    global $db;

    $result = mysqli_query($db, "SELECT b.exibition_barcode, v.lastname, v.firstname, v.patronymic, v.visitor_barcode
                                 FROM exibition_barcode b
                                 LEFT JOIN visitor v ON v.id = b.visitor_id
                                 ORDER BY b.exibition_barcode desc " .
            ($paging != null ? "LIMIT ${paging[0]}, ${paging[1]}" : ""));
    $barcodes = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $barcodes[] = $row;
    }

    return $barcodes;
}

function getCountries($term = null) {
    global $db;

    $term = htmlspecialchars($term);
    $countries = array();
    $result = mysqli_query($db, "SELECT id_country, countryname_ru, countryname_en
                                 FROM country " .
            ($term == null ? "" : " WHERE (countryname_ru LIKE '$term%' OR countryname_en LIKE '$term%') "));

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $countries[$row['id_country']] = array("ru" => $row['countryname_ru'], "en" => $row['countryname_en']);
        }
        return $countries;
    }

    return array();
}

function getTowns($country, $term, $limit = null) {
    global $db;
    $towns = array();
    $country = intval($country);
    $term = htmlspecialchars($term);
    if ($country != 0) {
        $result = mysqli_query($db, "SELECT id_country as country, townname_ru, townname_en, id_town
                 FROM town WHERE id_country=$country AND (townname_ru LIKE '$term%' OR townname_en LIKE '$term%') " .
                ($limit != null ? " LIMIT 0,$limit " : ""));
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $towns[$row['id_town']] = array("ru" => $row['townname_ru'], "en" => $row['townname_en']);
            }
        }
    }
    return $towns;
}

function getTownAndCountry($town, $country) {
    global $db;

    $town = intval($town);
    if ($town != 0) {
        $result = mysqli_query($db, "SELECT t.name AS town_name, c.name AS country_name 
                 FROM towns t 
                 INNER JOIN countries c ON t.country_id = c.id 
                 WHERE t.id=$town AND t.country_id=$country");
        if ($result) {
            return mysqli_fetch_assoc($result);
        }
    }
}

function assignBarcode($visitorId) {
    global $db;

    if (is_numeric($visitorId)) {
        $result = mysqli_query($db, "UPDATE exibition_barcode
                       SET visitor_id = $visitorId
                       WHERE visitor_id IS NULL
                       LIMIT 1;");
        return $result ? 1 : 0;
    }
}

function getFreeBarcodes() {
    global $db;

    $result = $db->query("SELECT COUNT(*) AS amount FROM exibition_barcode WHERE visitor_id IS NULL");
    $result = mysqli_fetch_assoc($result);
    return $result["amount"];
}

function getExibitionBarcode($visitorId) {
    global $db;

    if (is_numeric($visitorId)) {
        $result = mysqli_query($db, "SELECT exibition_barcode FROM exibition_barcode WHERE visitor_id=$visitorId");
        $barcode = mysqli_fetch_assoc($result);
        return ($barcode != 0 ? $barcode["exibition_barcode"] : 0);
    }
    return 0;
}

function nextBarcode() {
    global $db;

    $result = mysqli_query($db, "SELECT MAX(visitor_barcode) + 1 AS barcode FROM visitor");

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        if ($nextId < "1000000000000") {
            $nextId += 1000000000000;
        }
    }
    return $nextId;
}

function addVisitorsFromArray ($visitors){
    global $db;

    $statement = $db->prepare(
            "INSERT INTO visitor (
                     visitor_barcode, firstname, lastname, patronymic,    
                     post, company, country, town, 
                     address, postcode, email, website,      
                     preregister, ticket_printed, phone, fax) 
                     VALUES (?, ?, ?, ?, 
                             ?, ?, ?, ?, 
                             ?, ?, ?, ?,
                             ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE 
                     visitor_barcode = VALUES(visitor_barcode),
                     firstname = VALUES(firstname),
                     lastname = VALUES(lastname),
                     patronymic = VALUES(patronymic),    
                     post = VALUES(post),
                     company = VALUES(company),
                     country  = VALUES(country),
                     town = VALUES(town), 
                     address  = VALUES(address),
                     postcode  = VALUES(postcode),
                     email = VALUES(email),
                     website = VALUES(website),      
                     preregister = VALUES(preregister),
                     ticket_printed = VALUES(ticket_printed),
                     phone = VALUES(phone),
                     fax = VALUES(fax);");    
    
    foreach ($visitors as $form) {
        $id = isset($form["visitor_barcode"]) && is_numeric($form["visitor_barcode"]) ? $form["visitor_barcode"] : null;
        $statement->bind_param("sssssssssssiiiss", $id, $form["firstname"], $form["lastname"], $form["patronymic"], $form["post"], $form["company"], $form["country"], $form["town"], $form["address"], $form["postcode"], $form["email"], $form["website"], $form["preregister"], $form["ticket_printed"], $form["phone"], $form["fax"]);
        $statement->execute();
    }
}

function addVisitor($form) {
    global $db;

    $id = isset($form["visitor_barcode"]) && is_numeric($form["visitor_barcode"]) ?
            $form["visitor_barcode"] : null;

    if (isset($form["town_id"]) && isset($form["country_id"])) {
        $townAndCountry = getTownAndCountry($form["town_id"], $form["country_id"]);
        $town = $townAndCountry["town_name"];
        $country = $townAndCountry["country_name"];
    } else {
        $town = $form["town"];
        $country = $form["country"];
    }

    $statement = $db->prepare(
            "INSERT INTO visitor (
                     visitor_barcode, firstname, lastname, patronymic,    
                     post, company, country, town, 
                     address, postcode, email, website,      
                     preregister, ticket_printed, phone, fax) 
                     VALUES (?, ?, ?, ?, 
                             ?, ?, ?, ?, 
                             ?, ?, ?, ?,
                             ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE 
                     visitor_barcode = VALUES(visitor_barcode),
                     firstname = VALUES(firstname),
                     lastname = VALUES(lastname),
                     patronymic = VALUES(patronymic),    
                     post = VALUES(post),
                     company = VALUES(company),
                     country  = VALUES(country),
                     town = VALUES(town), 
                     address  = VALUES(address),
                     postcode  = VALUES(postcode),
                     email = VALUES(email),
                     website = VALUES(website),      
                     preregister = VALUES(preregister),
                     ticket_printed = VALUES(ticket_printed),
                     phone = VALUES(phone),
                     fax = VALUES(fax);");

    $statement->bind_param("sssssssssssiiiss", $id, $form["firstname"], $form["lastname"], $form["patronymic"], $form["post"], $form["company"], $country, $town, $form["address"], $form["postcode"], $form["email"], $form["website"], $form["preregister"], $form["ticket_printed"], $form["phone"], $form["fax"]);
    $statement->execute();

    if ($db->error) {
        return FALSE;
    }

    $insertId = $db->insert_id;

    if (isset($form["exibitions"]) && is_array($form["exibitions"]) && count($form["exibitions"] > 0)) {
        assignExibitions($insertId, $form["exibitions"]);
    }

    if ($db->error) {
        return FALSE;
    }

    return $insertId;
}

function getCountriesByName($names) {
    global $db;

    $countries = array();

    $nameStr = implode("', '", $names);
    $result = mysqli_query($db, "SELECT id, name FROM countries WHERE name IN ('$nameStr');");
    print ($db->error);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $countries[$row["name"]] = $row["id"];
        }
    }

    $result = array();
    while ($country = array_shift($names)) {
        $result[$countries[$country]] = $country;
    }

    return $result;
}

function setFromBarcode($id) {
    global $db;

    if (is_numeric($id)) {
        if (!assignBarcode($id)) {
            return 0;
        }
        mysqli_query($db, "UPDATE visitor SET ticket_printed = true, preregister = 1 WHERE visitor_barcode = '$id'");
        print $db->error;
        return $db->error;
    }

    return false;
}

function removeVisitor($id) {
    global $db;

    mysqli_query($db, "DELETE FROM visitor WHERE visitor_barcode = '$id'");

    return $db->error;
}

function explode_escaped($delimiter, $string) {
    $string = str_replace('\\' . $delimiter, urlencode($delimiter), $string);
    return array_map('urldecode', explode($delimiter, $string));
}

function addBarcode($barcode) {
    global $db;

    if (isset($barcode["exibition_barcode"]) && is_numeric($barcode["exibition_barcode"])) {
        $visitorBarcode = "NULL";

        if (isset($barcode["visitor_barcode"]) && is_numeric($barcode["visitor_barcode"])) {
            $barcode = "'$barcode'";
        }

        $result = mysqli_query($db, "INSERT INTO exibition_barcode (exibition_barcode, visitor_id) VALUES ('" . $barcode["exibition_barcode"] . "', " . $visitorBarcode . ")");
        if (!$result) {
            return 0;
        }
    }
    return $db->insert_id;
}

function addExibition($exibition) {
    global $db;

    $statement = $db->prepare("INSERT INTO exibition (exibition_id, title_ru, title_en, active) VALUES (?, ?, ?, ?)
                               ON DUPLICATE KEY UPDATE title_ru = VALUES(title_ru), title_en=VALUES(title_en), active = VALUES(active);");


    if ($statement) {
        $statement->bind_param("issi", $exibition["exibition_id"], $exibition["title_ru"], $exibition["title_en"], $exibition["active"]);
        $statement->execute();
    }

    if ($db->error) {
        return 0;
    }

    return $db->insert_id;
}

function addMembership($exibition) {
    global $db;

    if (is_numeric($exibition["visitor_barcode"]) && is_numeric($exibition["exibition_id"])) {
        $result = mysqli_query($db, "INSERT INTO exibition_membership (exibition_id, visitor_barcode) VALUES(${exibition["exibition_id"]}, '${exibition["visitor_barcode"]}')");
    }

    if ($db->error) {
        return 0;
    }
    return $db->insert_id;
}

function getExibitions($onlyActive = null) {
    global $db;

    $result = mysqli_query($db, "SELECT COUNT(exm.exibition_id) AS members, ex.exibition_id, ex.title_ru, ex.title_en, ex.active
                                 FROM exibition ex
                                 LEFT JOIN exibition_membership exm ON exm.exibition_id = ex.exibition_id " .
            ($onlyActive ? "WHERE ex.active = TRUE " : "") . "
                                 GROUP BY ex.exibition_id
                                 ORDER BY ex.active DESC, ex.title_ru ");
    $exibitions = array();
    print $db->error;
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $exibitions[$row["exibition_id"]] = $row;
        }
    }

    return $exibitions;
}

?>