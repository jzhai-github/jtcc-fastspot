<div class="box panel">
    <h1 class="panel-heading"><?php echo lang('seeo_default_template'); ?></h1>

    <div class="panel-body settings seeo">
        <fieldset class="col-group last">
            <div class="setting-field col w-16 last">
                <div class="seeo__instructions field-instruct"><em><?php echo lang('seeo_default_template_description'); ?></em></div>
                <textarea name="template" id="default_template" cols="30" rows="30"><?=htmlspecialchars($template)?></textarea>
            </div>
        </fieldset>
    </div>
</div>
