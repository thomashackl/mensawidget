<div class="mensawidget-select">
    <div class="mensawidget-weeks">
        <a href="#" onclick="return STUDIP.MensaWidget.showWeek('current')" class="mensawidget-weeklink" data-week="current">
            <span class="mensawidget-weekselect">
                <?= dgettext('mensawidget', 'aktuelle Woche') ?>
            </span>
        </a>
        <a href="#" onclick="return STUDIP.MensaWidget.showWeek('next')" class="mensawidget-weeklink" data-week="next">
            <span class="mensawidget-weekselect">
                <?= dgettext('mensawidget', 'nächste Woche') ?>
            </span>
        </a>
    </div>
    <div class="mensawidget-days">
    <?php foreach ($data as $date => $menu) { ?>
        <a href="#" onclick="return STUDIP.MensaWidget.showMenu('<?= date('dmY', strtotime($date)) ?>')" class="mensawidget-daylink" data-week="<?= strtotime($date) < $lastcurrentweekday ? 'current' : 'next' ?>"<?= $date == $today ? ' data-today="true"' : '' ?>>
            <span id="mensawidget-day<?= date('dmY', strtotime($date)) ?>" class="mensawidget-dayselect<?= $date == $today ? ' mensawidget-today' : '' ?>">
            <?php if ($date == $today) { ?>
                <div class="mensawidget-todaytext">
                <?= dgettext('mensawidget', 'Heute') ?>
                </div>
            <?php } ?>
            <?= date('d.m.', strtotime($date)) ?>
            </span>
        </a>
    <?php } ?>
    </div>
    <div style="clear: both"></div>
</div>
<?php foreach ($data as $date => $menu) { $daydate = date('d.m.Y', strtotime($date)); ?>
<table class="default mensawidget-menu" id="mensawidget-<?= date('dmY', strtotime($date)) ?>">
    <thead>
        <tr>
            <th width="20%"></th>
            <th></th>
            <th></th>
            <th width="20%">
                <?= dgettext('mensawidget', 'Preis') ?>
                <?= tooltipIcon(dgettext('mensawidget', 'Studierende/Beschäftigte/Gäste')) ?>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
    <?php foreach ($types as $index => $name) { $first = true; ?>
        <?php if ($first) { $first = false; ?>
            <td class="mensawidget-type" rowspan="<?= sizeof($data[$daydate][$index]) ?>"><?= htmlReady($name) ?></td>
        <?php } ?>
        <?php foreach ($menu[$index] as $meal) { ?>
            <td><?= htmlReady($meal['meal']) ?></td>
            <td class="mensawidget-kind">
            <?php foreach ($meal['icons'] as $icon) { ?>
                <img src="<?= $icon['icon'] ?>" alt="<?= $icon['title'] ?>" title="<?= $icon['title'] ?>" width="23"/>
            <?php } ?>
            </td>
            <td><?= htmlReady($meal['fullprice']) ?></td>
        </tr>
            <?php } ?>
        <?php } ?>
    </tbody>
</table>
<?php } ?>
