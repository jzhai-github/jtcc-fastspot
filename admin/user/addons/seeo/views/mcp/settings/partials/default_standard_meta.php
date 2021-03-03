<div class="box panel">
    <h1 class="panel-heading"><?=lang('seeo_default_standard_meta')?></h1>

    <div class="panel-body settings seeo">

        <fieldset class="col-group">
        <fieldset class="col-group">
            <div class="setting-txt col w-4">
                <label for="divider"><?=lang('seeo_title_divider')?></label>
            </div>
            <div class="setting-field col w-12 last">
                <select name="divider" id="divider">
                    <option value="-" <?php echo ($divider == '-' ? 'selected="selected"' : ''); ?>>-</option>
                    <option value="|" <?php echo ($divider == '|' ? 'selected="selected"' : ''); ?>>|</option>
                    <option value="||" <?php echo ($divider == '||' ? 'selected="selected"' : ''); ?>>||</option>
                    <option value="|:|" <?php echo ($divider == '|:|' ? 'selected="selected"' : ''); ?>>|:|</option>
                    <option value="::" <?php echo ($divider == '::' ? 'selected="selected"' : ''); ?>>::</option>
                    <option value="/" <?php echo ($divider == '/' ? 'selected="selected"' : ''); ?>>/</option>
                    <option value="//" <?php echo ($divider == '//' ? 'selected="selected"' : ''); ?>>//</option>
                </select><br />
                <span class="seeo__instructions field-instruct"><em><?=lang('seeo_title_divider_instructions')?></em></span>
            </div>
        </fieldset>

            <div class="setting-txt col w-4">
                <label for="default_title"><?=lang('seeo_default_site_title')?></label>
            </div>
            <div class="setting-field col w-12 last">
                <input type="text" name="title" id="default_title" value="<?=htmlspecialchars($title)?>" /><br />
                <span class="seeo__instructions field-instruct"><em><?=lang('seeo_default_site_title_instructions')?></em></span>
            </div>
        </fieldset>

        <fieldset class="col-group last">
            <div class="setting-txt col w-4">
                <label for="default_title_prefix"><?=lang('seeo_default_title_prefix')?></label>
            </div>
            <div class="setting-field col w-12 last">
                <input type="text" name="title_prefix" id="default_title_prefix" value="<?=htmlspecialchars($title_prefix)?>" /><br />
                <span class="seeo__instructions field-instruct"><em><?=lang('seeo_default_title_prefix_instructions')?></em></span>
            </div>
        </fieldset>

        <fieldset class="col-group">
            <div class="setting-txt col w-4">
                <label for="default_title_suffix"><?=lang('seeo_default_title_suffix')?></label>
            </div>
            <div class="setting-field col w-12 last">
                <input type="text" name="title_suffix" id="default_title_suffix" value="<?=htmlspecialchars($title_suffix)?>" /><br />
                <span class="seeo__instructions field-instruct"><em><?=lang('seeo_default_title_suffix_instructions')?></em></span>
            </div>
        </fieldset>

        <fieldset class="col-group">
            <div class="setting-txt col w-4">
                <label for="default_description"><?=lang('seeo_default_description')?></label>
                <span class="seeo__instructions field-instruct"><em><?=lang('seeo_default_description_instructions')?></em></span>
            </div>
            <div class="setting-field col w-12 last">
                <textarea name="description" id="default_description" cols="30" rows="3"><?=htmlspecialchars($description)?></textarea>
            </div>
        </fieldset>

        <fieldset class="col-group">
            <div class="setting-txt col w-4">
                <label for="default_keywords"><?=lang('seeo_default_keywords')?></label>
            </div>
            <div class="setting-field col w-12 last">
                <input type="text" name="keywords" id="default_keywords" value="<?=htmlspecialchars($keywords)?>"/>
            </div>
        </fieldset>

        <fieldset class="col-group">
            <div class="setting-txt col w-4">
                <label for="default_author"><?=lang('seeo_default_author')?></label>
            </div>
            <div class="setting-field col w-12 last">
                <input type="text" name="author" id="default_author" value="<?=htmlspecialchars($author)?>"/>
            </div>
        </fieldset>

    </div>
</div>
