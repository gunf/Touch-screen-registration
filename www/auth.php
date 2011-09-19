<?php
include_once 'settings.php';

if ($admin) {
    header("Location: admin.php");
    exit;
}

include_once 'inc/header.php';
?>
<form method="POST" action="auth.php">
    <div class="central barcode">
        <div class="item">
            <center>
                <input type="password" name="password" class="input"/>
            </center>
        </div>    
        <div class="item">
            <center>
                <button type="submit" class="big_button"><?= tr("auth.enter") ?></button>
            </center>
        </div>
    </div>
</form>

<?php
include 'inc/footer.php';
?>