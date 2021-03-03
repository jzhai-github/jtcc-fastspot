<table cellspacing="0">
    <thead>
        <tr>
            <th><a href="<?= $flux->cpUrl('publish', 'edit', array('entry_id' => $audit->entry['entry_id']))?>"><?= $audit->entry['title'] ?></a></th>
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody class='seeo-entry-table'>
        <?php foreach ($audit->items as $itemNumber => $audit_item) : ?>
        <?php if (firstInRow($itemNumber)) : ?> <tr class='seeo-table-row'> <?php endif;?>
            <td><span class="<?= $audit_item->badgeClass ?> seeo-table-item-badge seeo-tooltip">
                <?= $audit_item->shortname ?>
                <?php if ($audit_item->valid) : ?>
                    <span class="seeo-tooltiptext " style="display: block;">
                        <div class="seeo-tooltiptext-inner ">
                            <h3><?= $audit_item->shortname ?></h3>
                            <p><?= $audit_item->value ?></p>
                        </div>
                    </span>
                <?php endif; ?>
            </span></td>
            <?= lastRowTds($itemNumber, count($audit->items)) ?>
        <?php if (lastInRow($itemNumber)) : ?> </tr> <?php endif;?>
        <?php endforeach; ?>
    </tbody>
</table>