<table class="default">
    <caption><?= sprintf('%s - %s', date('d.m.Y', $start), date('d.m.Y', $end)) ?></caption>
    <thead>
        <tr>
            <th width="20%">&nbsp;</th>
            <th width="16%"><?= _('Montag') ?></th>
            <th width="16%"><?= _('Dienstag') ?></th>
            <th width="16%"><?= _('Mittwoch') ?></th>
            <th width="16%"><?= _('Donnerstag') ?></th>
            <th width="16%"><?= _('Freitag') ?></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($types as $index => $name) { $i = $start; ?>
        <tr>
            <td><?= htmlReady($name) ?></td>
            <?php while ($i <= $end) { ?>
            <td>
                <?php if ($data[date('d.m.Y', $i)][$index]) { ?>
                <ul>
                    <?php foreach ($data[date('d.m.Y', $i)][$index] as $entry) { ?>
                        <?= $entry['meal'] ?>
                    <?php } ?>
                </ul>
                <?php } ?>
            </td>
            <?php $i += (24*60*60); } ?>
        </tr>
    <?php } ?>
    </tbody>
</table>