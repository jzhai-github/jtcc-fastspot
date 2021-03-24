<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>

<script type="text/javascript">
$(document).ready(function() {

    var modal_log = $('<div />').dialog({
        autoOpen: false,
        modal: true,
        title: 'Log',
        width: 500
    });

    $('.toggle-note').hover(
        function() { $(this).find('a.edit').css('display', 'inline-block'); },
        function() { $(this).find('a.edit').hide(); }
    );

    // $('.toggle-note div').toggle(
    //  function() { $(this).css({ 'line-height':'1.5em', 'white-space':'normal', 'overflow':'show' }); },
    //  function() { $(this).css({ 'line-height':'1em', 'white-space':'pre', 'overflow':'hidden' }); }
    // );

    $('.toggle-note a').click(function() {
        $(this).parent().hide().parent().find('.form-note').show();
        return false;
    });

    $('.form-note .buttons a').click(function() {
        $(this).parent().parent().parent().parent().hide().parent().find('.toggle-note').show();
        return false;
    });

    $('.link-log').click(function() {
        // feedback to user through cursor change
        $('body').css('cursor', 'wait');

        // get page
        $.get(this.href, function( data ) {
            // feedback to user through cursor change
            $('body').css('cursor', 'default');

            // load modal box
            modal_log.html(data);
            modal_log.dialog('open');
        });

        return false;
    });

});

function backupNow(type) {
    if(type == 'full') $.get('<?=$backup_url_full?>');
    else if(type == 'diff') $.get('<?=$backup_url_diff?>');

    // if(type == 'full') $.get('<?=$backup_url_full?>', function(r) { console.log(r); });
    // else if(type == 'diff') $.get('<?=$backup_url_diff?>', function(r) { console.log(r); });

    $('body').append('<div id="safeharbor-overlay">&nbsp;</div><div id="safeharbor-loading"><?=lang("backup_loading")?></div>');

    setTimeout(function() { window.location.reload() }, 2000);
    return false;
}

// The following JS thanks to Low Schutte
var SHL  = new Object();


SHL.ReplaceLog = function(){

    // EE's modal
    var $modal = $('.modal-log-replace .box');

    // Set max-height
    $modal.css({
        maxHeight: ($(window).height() - 80 - 55) + 'px',
        overflow: 'auto',
        padding: '10px 10px 0',
    });

    // Links that trigger the modal
    $('.replace-log').on('click', function(event){
        $modal.html('<p style="text-align:center">Loading</p>');
        $modal.load(this.href);
    });

};

$(SHL.ReplaceLog);


</script>

<style type="text/css">
.toggle-note { position:relative; overflow:hidden; zoom:1.0; }
.toggle-note div { float:left; margin-right: 40px; line-height:1em; overflow:hidden; text-overflow:ellipsis; white-space:initial; cursor:pointer; }
.toggle-note a.edit { display: none; font-size: 10px; padding: 1px 5px; margin-left: 10px; }
.toggle-note a.add-note { display:inline-block; position:relative; }
.form-note { display:none; }
.textfield { padding-bottom:7px; }
.buttons { overflow:hidden; zoom:1.0; }
.buttons a:hover { text-decoration:none; }
table td { white-space: initial; }
.btn.btn-warning { background-color: #eab45d; border:0; }
.btn.btn-warning:hover { background-color: #ff282c; border:0; }
#safeharbor-overlay { position: fixed; z-index:10; top:0; left:0; right:0; bottom:0; background-color:#000; opacity:0.5; }
#safeharbor-loading { position: fixed; z-index:11; top:50%; left:50%; width: 300px; height:21px; margin:-40px 0 0 -150px; border-radius:5px; background-color:#fff; font: normal normal 18px Helvetica,Arial,Sans-Serif; color: gray; font-weight: 400; text-align: center; padding:30px; }
</style>
<div class="box">
    <?=form_open($base_url.'settings', 'class="tbl-ctrls"')?>
        <fieldset class="tbl-search right">
            <button type="button" class="btn tn action" onclick="backupNow('full')"><?=lang('backup_full_now')?></button>
            <button type="button" class="btn tn action" onclick="backupNow('diff')"><?=lang('backup_differential_now')?></button>
        </fieldset>
    <?=form_close();?>
        <h1><?=lang('safeharbor_module_name')?></h1>
        <?=ee('CP/Alert')->get('moblogs-table')?>

        <div class="tbl-ctrls">
            <?php $this->embed('ee:_shared/table', $table); ?>
        </div>

</div>
