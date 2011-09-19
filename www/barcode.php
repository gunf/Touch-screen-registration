<?php
include_once 'inc/header.php';
include_once 'settings.php';
include_once 'db.php';

$exibitions = getExibitions();
?>
<script type="text/javascript" src="js/kbdSetup.js"></script>
<script type="text/javascript">   
    var keyboardShown = false;
    
    $(function(){
        $("#barcode").focus();
        
        function checkBarcode(){
            var barcode = $("#barcode").val() ;
            
            if (barcode.length == 13){
                $.ajax({
                    url:  "export.php",
                    data: {
                        barcode: barcode,
                        type:"barcode_check"
                    },
                    dataType:'json',
                    success: function(data){
                        if (data.exists) {
                            $("#keyboard").hide();
                            $("#notfound").hide();
                            $("#barcode_input").hide();
                            $("#visitor_confirm").show();
                            $("#fio").text(data.visitor.lastname + " " + 
                                data.visitor.firstname + " " +
                                data.visitor.patronymic);
                                       
                            $("#company").text(data.visitor.company);
                            $("#do_print").attr("href", "print.php?type=<?= $settings["badges"]["default"] ?>&id="+data.visitor.id);
                        } else {
                            $("#notfound").show();
                            if (!keyboardShown) {
                                kbdFor(document.getElementById("barcode"), "bottom");
                                $("#form").addClass("with_keyboard");
                                keyboardShown = true;
                            }
                        }
                    }
                });
            } else {
                if (barcode.length > 13){
                    $("#barcode").val(barcode.substr(0, 13));
                } else {
                    $("#notfound").hide();
                }
            }
        }
        
        $("#barcode").keyup (checkBarcode);
        setInterval(checkBarcode, 200);
        
        $("#barcode").click(function(){
            kbdFor(document.getElementById("barcode"), "bottom");
            $("#form").addClass("with_keyboard");
        });
    })
</script>
<div class="barcode">
    <div class="logos">
        <?php foreach ($exibitions as $id => $exibition) { ?>
            <img src="img/exibition_logos/<?= $id ?>.png" />
        <?php } ?>
    </div>
    <div class="form" id="form">
        <div id="barcode_input">
            <h1><?= tr("barcode.ticket_to_scanner"); ?>.</h1>
            <h2 id="notfound" class="notice"><?= tr("barcode.not_found"); ?>.</h2>
            <input type="text" id="barcode" name="barcode" maxlength="13" class="input"/>
            <a href="index.php"><button class="big_button"><?= tr("cancel") ?></button></a>
        </div>
        <div id="visitor_confirm">
            <h1><?= tr("barcode.thanks") ?></h1>
            <h2><?= tr("barcode.isthatyou") ?></h2>
            <h3 class="notice" id="fio"></h3>
            <h3 class="notice" id="company"></h3>
            <center>
                <div class="confirm_buttons">
                    <a id="do_print" href="print.php"><button class="big_button"><?= tr("barcode.yes") ?></button></a>
                    <a href="regform.php"><button class="big_button"><?= tr("barcode.no") ?></button></a>
                </div>
                <center>
                    </div>
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