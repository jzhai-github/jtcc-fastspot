<div class="box panel">
    <h1 class="panel-heading"><?=lang('seeo_file_manager')?></h1>

    <div class="panel-body settings seeo">
        <fieldset class="col-group last">
            <div class="setting-txt col w-10">
                <label for="seeo_use_assets"><?=lang('seeo_default_file_manager')?></label>
                <span class="seeo__instructions field-instruct"><em><?=lang('seeo_default_file_manager_instructions')?></em></span>
            </div>
            <div class="setting-field col w-6 last">
                <label class="seeo__radio-label" for="file_manager">
                    <input type="radio" id="file_manager" name="file_manager" value="default"
                        <?php if ($file_manager == 'default') : ?>
                            checked="checked"
                        <?php endif; ?>
                        />
                    <?=lang('seeo_default_file_manager')?>
                </label>
                <label class="seeo__radio-label" for="seeo_assets_file_manager">
                    <input type="radio" id="seeo_assets_file_manager" name="file_manager" value="assets"
                        <?php if ($file_manager == 'assets') : ?>
                            checked="checked"
                        <?php endif; ?>
                        />
                    EEHarbor Assets
                </label>
            </div>
        </fieldset>
    </div>
</div>
