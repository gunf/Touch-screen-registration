<?php
include_once 'inc/header.php';
include_once 'db.php';
include_once 'settings.php';

$exibitions = getExibitions(true);
?>
<script type="text/javascript">
    var errorMessages = {
<?php foreach ($settings["regform"]["fields"] as $name) { ?>
    <?= $name ?>:'<?= tr("error.regform.$name") ?>',
<?php } ?>
        required: '<?= tr("error.regform.required") ?>'
    }
</script>
<script type="text/javascript" src="js/jquery.validate.min.js"></script>
<script type="text/javascript" src="js/regform.js"></script>
<script type="text/javascript" src="js/kbdSetup.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.15.custom.min.js"></script>
<script type="text/javascript" src="js/jquery.multiselect.min.js"></script>
<script type="text/javascript" src="js/jquery.ui.autocomplete.js"></script>
<script type="text/javascript" src="js/combobox.js"></script>

<link rel="stylesheet" type="text/css" href="css/smoothness/jquery-ui-1.8.15.custom.css" />
<link rel="stylesheet" type="text/css" href="css/jquery.multiselect.css" />

<script type="text/javascript">
    var previousCountry = "" ;
    var previousTown = "";

    
    $(function(){
        $("#exibitions").multiselect({
            checkAllText:    '<?= tr("regform.multiselect.select_all") ?>',
            uncheckAllText:  '<?= tr("regform.multiselect.unselect_all") ?>',
            noneSelectedText:'<?= tr("regform.multiselect.please_select") ?>',
            selectedText:    '<?= tr("regform.multiselect.selected") ?>',
            selectedList: 3,
            minWidth: 300,
            position: {at: "top", my: 'bottom'}
        }).addClass("input");

    });
    
    
</script>
<div class="register">
    <div class="logos">
        <?php foreach ($exibitions as $id => $exibition) { ?>
            <img src="img/exibition_logos/<?= $id ?>.png" />
        <?php } ?>
    </div>
    <div class="form">
        <h1><?= tr("regform.pleasefill") ?></h1>
        <form name="register" id="regform" method="POST" action="register.php">
            <input type="hidden" name="exibition_id" value="1" />

            <?php foreach ($settings["regform"]["fields"] as $group) { ?>
                <div class="group">
                    <?php
                    foreach ($group as $name) {
                        if ($name == "country") {
                            ?>
                            <div class="item">
                                <div class="name">
                                    <div class="mandatory">*</div>                                                            
                                    <label for="country"><?= tr("regform.country") ?>:</label>
                                </div>
                                <div class="value">
                                    <div class="autocomplete_host">
                                        <input name="country" type="text" id="country" class="input required" />
                                    </div>
                                </div>
                            </div>           
                        <?php } elseif ($name == "town") { ?>
                            <div class="autocomplete_host">
                                <div class="item">
                                    <div class="name">
                                        <div class="mandatory">*</div>                                        
                                        <label for="town"><?= tr("regform.town") ?>:</label>
                                    </div>
                                    <div class="value">
                                        <input type="text" name="town" id="town" class="input required" />
                                    </div>
                                </div>    
                            </div>
                        <?php } else { ?>
                            <div class="item">
                                <div class="name">
                                    <?php
                                    $mandatory = in_array($name, $settings["regform"]["mandatory"]);
                                    if ($mandatory) {
                                        ?>
                                        <div class="mandatory">*</div>
                                    <?php } ?>                                    
                                    <label for="<?= $name ?>"><?= tr("regform.$name") ?>:</label>
                                </div>
                                <div class="value">
                                    <input type="text" name="<?= $name ?>" id="<?= $name ?>" class="input <?= $mandatory ? "required" : "" ?> <?= $name ?>" />                        
                                </div>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
            <?php } ?>
            <div class="item">
                <div class="name">
                    <div class="mandatory">*</div>
                    <label for="exibitions"><?= tr("regform.exibitions") ?>:</label>
                </div>
                <div class="value">
                    <select id="exibitions" multiple="multiple" name="exibitions[]">
                        <?php foreach ($exibitions as $id => $exibition) { ?>
                            <option value="<?= $id ?>"><?= $exibition["title_" . $currentLanguage] ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="item submits">
                <button class="big_button"  onclick='window.location.href="index.php"; return false;'><?=tr("cancel")?></button>
                <button type="submit" id="submit" class="big_button"><?= tr("regform.register") ?></button>
            </div>
    </div>
</form>
<div class="agree">
    <label for="agree"><?= tr("regform.agreement") ?> <a href="javascript:void(0);" onclick='$("#law").toggle();'><?= tr("regform.law") ?></a></label>
</div>
<div class="law" id="law">
    <center>
        <div class="law_text">
            <?php include 'law.html'; ?>
        </div>
        <button class="big_button"  onclick='$("#law").toggle();'>OK</button>
    </center>
</div>
    <div class="law" id="sponsors">
        <center>
            <div class="law_text">
                <?php include 'sponsors.html'; ?>
            </div>
            <button class="big_button"  onclick='$("#sponsors").toggle();'>OK</button>
        </center>
    </div>    
    <div class="sponsors">
        <div class="title"><a href="javascript:void(0);" onclick='$("#sponsors").toggle();'><?= tr("sponsors") ?></a></div>
        <img src="img/logos.png" />
    </div>    
</div>
<?php
include 'inc/footer.php';
?>