<?php
include_once 'settings.php';
?>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />        
        <script src="js/jquery.min.js" type="text/javascript"></script>
        <link rel="stylesheet" type="text/css" href="css/style.css" />
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <script type="text/javascript" src="js/keyboard/vk_loader.js?vk_layout=us&vk_skin=air_mid" ></script>
    </head>
    <body <?= isset($simple) ? "" : 'class="stripes"' ?>>
        <?php
        if (isset($simple)) {
            $currentLanguage = $settings["languages"]["default"];
        } else {
            ?>
            <div class="flags">
                <?php
                foreach ($languages as $code => $params) {
                    if ($code != "default") {
                        if ($currentLanguage == $code) {
                            ?>
                            <img class="chosen" src="<?= $params["icon"] ?>" alt="<?= $params["name"] ?>" />
                            <?php
                        } else {
                            ?>
                            <a href="?lang=<?= $code ?>">
                                <img src="<?= $params["icon"] ?>" alt="<?= $params["name"] ?>" />
                            </a>
                            <?php
                        }
                    }
                }
            }
            ?>
        </div>
        <?php if ($admin) { ?> 
            [<a href="index.php"><?= tr("index.name") ?></a>, <a href="admin.php"><?= tr("admin.admin"); ?></a>, <a href="auth.php?action=logout"><?= tr("auth.logout"); ?></a>]
        <?php } ?>