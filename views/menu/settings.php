<form class="studip_form" action="<?= PluginEngine::getURL('MensaWidget/menu/save_settings') ?>" method="post">
    <label class="caption">
        <?= dgettext('mensawidget', 'Welche Preise sollen angezeigt werden?') ?>
        <select name="pricetype">
            <?php foreach ($pricetypes as $type => $name) { ?>
            <option value="<?= $type ?>"<?= $pricetype['value'] == $type ? ' selected' : ''?>><?= htmlReady($name) ?></option>
            <?php } ?>
        </select>
    </label>
    <?= CSRFProtection::tokenTag() ?>
    <div class="submit_wrapper" data-dialog-buttons>
        <?= Studip\Button::createAccept(dgettext('mensawidget', 'Speichern'), 'submit', array('data-dialog-button' => '')) ?>
        <?= Studip\LinkButton::createCancel(dgettext('mensawidget', 'Abbrechen'), URLHelper::getLink('dispatch.php/start'), array('data-dialog-button' => '', 'data-dialog' => 'close')) ?>
    </div>
</form>