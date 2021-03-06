<article>
    <header class="mensawidget-select">
        <div class="mensawidget-weeks">
            <a href="" onclick="return STUDIP.MensaWidget.showWeek('current')" class="mensawidget-weeklink" data-week="current">
                <span class="mensawidget-weekselect">
                    <?= dgettext('mensawidget', 'aktuelle Woche') ?>
                </span>
            </a>
            <a href="" onclick="return STUDIP.MensaWidget.showWeek('next')" class="mensawidget-weeklink" data-week="next">
                <span class="mensawidget-weekselect">
                    <?= dgettext('mensawidget', 'nächste Woche') ?>
                </span>
            </a>
        </div>
        <div class="mensawidget-days">
            <?php foreach ($data as $date => $menu) : ?>
                <a href="#<?= date('dmY', strtotime($date)) ?>" onclick="return STUDIP.MensaWidget.showMenu('<?= date('dmY', strtotime($date)) ?>')" class="mensawidget-daylink mensawidget-shown" data-week="<?= strtotime($date) < $lastcurrentweekday ? 'current' : 'next' ?>"<?= $date == $today ? ' data-today="true"' : '' ?>>
                <span id="mensawidget-day<?= date('dmY', strtotime($date)) ?>" class="mensawidget-dayselect<?= $date == $today ? ' mensawidget-today' : '' ?>">
                <?php if ($date == $today) : ?>
                    <div class="mensawidget-todaytext">
                        <?= dgettext('mensawidget', 'Heute') ?>
                    </div>
                <?php endif ?>
                    <?= date('d.m.', strtotime($date)) ?>
                </span>
                </a>
            <?php endforeach ?>
        </div>
        <div style="clear: both"></div>
    </header>
    <?php foreach ($data as $date => $menu) : $daydate = date('d.m.Y', strtotime($date)); ?>
    <section class="mensawidget-menu mensawidget-hidden" id="mensawidget-<?= date('dmY', strtotime($date)) ?>">
        <a name="<?= date('dmY', strtotime($date)) ?>"></a>
        <table class="default">
            <thead>
            <tr>
                <th width="20%"></th>
                <th></th>
                <th></th>
                <th class="mensawidget-price">
                    <?= dgettext('mensawidget', 'Preis') ?>
                    <?= tooltipIcon($pricetype['name']) ?>
                </th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <?php foreach ($types as $index => $name) : $first = true; ?>
                    <?php if (count($data[$daydate][$index]) > 0) : ?>
                        <?php if ($first) : $first = false; ?>
                            <td class="mensawidget-type" rowspan="<?= count($data[$daydate][$index]) ?>"><?= htmlReady($name) ?></td>
                        <?php endif ?>
                        <?php foreach ($menu[$index] as $meal) : ?>
                            <td><?= htmlReady($meal['meal']) ?></td>
                            <td class="mensawidget-kind">
                            <?php foreach ($meal['icons'] as $icon) : ?>
                                <img src="<?= $controller->plugin->getPluginURL().$icon['icon'] ?>" alt="<?= $icon['title'] ?>" title="<?= $icon['title'] ?>"/>
                            <?php endforeach ?>
                            </td>
                            <td class="mensawidget-price"><?= htmlReady($meal[$pricetype['value']]) ?></td>
                        </tr>
                        <?php endforeach ?>
                    <?php endif ?>
                <?php endforeach ?>
            </tbody>
        </table>
    </section>
    <?php endforeach ?>
    <footer class="mensawidget-info">
        <?= formatReady(sprintf(dgettext('mensawidget', 'Daten von %s (Stand: %s)'), 'http://www.stwno.de/joomla/de/gastronomie/speiseplan/uni-passau', date('d.m.Y, H:i', $mtime))) ?>
    </footer>
</article>
