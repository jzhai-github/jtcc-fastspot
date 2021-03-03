<style>
.status span {
    padding: 4px 10px;
    border-radius: 3px;
    border: 2px solid blue;
    box-shadow: 0 1px 1px 0 rgba(60,64,67,.08), 0 1px 3px 1px rgba(60,64,67,.16)
}
.missing span {
    border-color: #e08480;
}
.partial span {
    border-color: #fc0;
}
.full span {
    border: 0;
    background-color: #aaffd2;
}
</style>
<div class="box panel">
    <!-- Header: Channel and the Dropdown -->

    <div class="tbl-ctrls">
        <h1 class="panel-heading">Audit Entries - <?= $channel['channel_title'] ?></h1>

    <?php if (empty($channel['entries']) || count($channel['entries'])) : ?>
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
                    <td><a href="<?php echo $flux->cpUrl('publish', 'edit', array('entry_id' => $audit->entry_id)); ?>"><?php echo $audit->entry['title']; ?></a></td>
                    <td class="status <?php echo $audit->map['standard']['class']; ?>"><span><?php echo $audit->map['standard']['count'] . ' / ' . $audit->map['standard']['total']; ?></span></td>
                    <td class="status <?php echo $audit->map['open_graph']['class']; ?>"><span><?php echo $audit->map['open_graph']['count'] . ' / ' . $audit->map['open_graph']['total']; ?></span></td>
                    <td class="status <?php echo $audit->map['twitter']['class']; ?>"><span><?php echo $audit->map['twitter']['count'] . ' / ' . $audit->map['twitter']['total']; ?></span></td>
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
