<style>
td.title {
    max-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
}
.status span, #legend span {
    padding: 4px 5px;
    font-size: 12px;
    border-radius: 3px;
    box-shadow: 0 1px 1px 0 rgba(60,64,67,.08), 0 1px 3px 1px rgba(60,64,67,.16)
    }
.missing span, span.n {
    border: 2px solid #e08480;
    }
.partial span {
    border: 2px solid #fc0;
    }
.full span, span.y {
    border: 2px solid #aaffd2;
    background-color: #aaffd2;
    }
#legend {
    margin-bottom: 20px;
    }
#legend div.col span {
    display: inline-block;
    padding: 0px 3px;
    width: 10px;
    text-align: center;
    margin-right: 3px;
    }
#legend div.col div {
    height: 24px;
    margin-left: 10px;
    }
#legend .key {
    margin-left: 0 !important;
    margin-bottom: 0px;
    font-weight: bold;
    border: none;
    }
body[data-ee-version^="6."] #legend div.col span {
    width: 19px;
}
body[data-ee-version^="6."] #legend div.col div {
    height: 28px;
}
</style>
<div class="box panel">
    <!-- Header: Channel and the Dropdown -->

    <div class="panel-heading title-bar form-btns form-btns-top">
        <h1>Audit Entries - <?= $channel['channel_title'] ?></h1>
        <div class="title-bar__extra-tools">
            <button class="btn" type="button" onclick="$('#legend').toggle();">Legend</button>
        </div>
    </div>

    <div class="panel-body tbl-ctrls">
        <div id="legend" class="col-group" style="display:none;">
            <div class="col w-1">&nbsp;</div>
            <div class="col w-5">
                <div class="key">Standard Meta</div>
                <div><span class="y">T</span> Title</div>
                <div><span class="y">D</span> Description</div>
                <div><span class="y">K</span> Keywords</div>
                <div><span class="y">C</span> Canonical URL</div>
                <div><span class="y">R</span> Robots</div>
                <div><span class="y">A</span> Author</div>
            </div>
            <div class="col w-5">
                <div class="key">Open Graph</div>
                <div><span class="y">T</span> Title</div>
                <div><span class="y">D</span> Description</div>
                <div><span class="y">I</span> Image</div>
                <div><span class="y">T</span> Type</div>
                <div><span class="y">U</span> URL</div>
            </div>
            <div class="col w-5">
                <div class="key">Twitter</div>
                <div><span class="y">T</span> Title</div>
                <div><span class="y">D</span> Description</div>
                <div><span class="y">C</span> Content Type</div>
                <div><span class="y">I</span> Image</div>
            </div>
        </div>

    <?php if (empty($channel['entries']) || count($channel['entries']) === 0) : ?>
        <div class="txt-wrap">
            <em>There are no entries in this channel yet.</em>
        </div>
    <?php else : ?>
        <table cellspacing="0">
            <thead>
                <tr>
                    <th>Entry ID</th>
                    <th>Entry Title</th>
                    <th>Standard Meta</th>
                    <th>Open Graph</th>
                    <th>Twitter</th>
                </tr>
            </thead>
            <tbody class='seeo-entry-table'>
            <?php foreach ($channel['audit'] as $audit) : ?>
                <tr>
                    <td><a href="<?php echo $flux->cpUrl('publish', 'edit', array('entry_id' => $audit->entry_id)); ?>"><?php echo $audit->entry_id; ?></a></td>
                    <td class="title"><a href="<?php echo $flux->cpUrl('publish', 'edit', array('entry_id' => $audit->entry_id)); ?>"><?php echo $audit->entry['title']; ?></a></td>
                    <td class="status 1<?php echo $audit->map['standard']['class']; ?>">
                    <?php
                    foreach ($audit->map['standard']['fields'] as $field) {
                        echo '<span class="', (!empty($audit->entry[$field]) ? 'y' : 'n'), '">', strtoupper(substr(str_replace('meta_', '', $field), 0, 1)), '</span>', "\n";
                    }
                    ?>
                    </td>
                    <td class="status 1<?php echo $audit->map['open_graph']['class']; ?>">
                    <?php
                    foreach ($audit->map['open_graph']['fields'] as $field) {
                        echo '<span class="', (!empty($audit->entry[$field]) ? 'y' : 'n'), '">', strtoupper(substr(str_replace('og_', '', $field), 0, 1)), '</span>', "\n";
                    }
                    ?>
                    </td>
                    <td class="status 1<?php echo $audit->map['twitter']['class']; ?>">
                    <?php
                    foreach ($audit->map['twitter']['fields'] as $field) {
                        echo '<span class="', (!empty($audit->entry[$field]) ? 'y' : 'n'), '">', strtoupper(substr(str_replace('twitter_', '', $field), 0, 1)), '</span>', "\n";
                    }
                    ?>
                    </td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>

        <div class="paginate">
            <?=$pagination?>
        </div>
    <?php endif; ?>
    </div>
</div>

<?php
// I know these functions down here are kinda ghetto, but I'm trying to keep the html a little cleaner.
function firstInRow($itemNumber)
{
    return (($itemNumber % 3) == 0);
}

function lastInRow($itemNumber)
{
    return (($itemNumber % 3) == 2);
}

function lastRowTds($itemNumber, $total)
{
    $columns = (2-($itemNumber % 3));

    // if there are no remaining columns, return
    if (! $columns) {
        return null;
    }

    // if we are on the last one, print stuff out
    if ($total === ($itemNumber+1)) {
        foreach (range(1, $columns) as $index) {
            echo '<td></td>';
        }
        echo '</tr>';
    }
}
?>
