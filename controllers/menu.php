<?php
/**
 * menu.php
 *
 * Shows the current mensa menu.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Thomas Hackl <thomas.hackl@uni-passau.de>
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 */

class MenuController extends AuthenticatedController {

    public function __construct($dispatcher) {
        parent::__construct($dispatcher);
        $this->plugin = $dispatcher->plugin;
        if (Request::isXhr()) {
            $this->set_layout(null);
        }
    }

    public function before_filter(&$action, &$args) {
        $this->set_layout($GLOBALS['template_factory']->open('layouts/base'));
    }

    public function index_action() {
        PageLayout::addStylesheet($this->plugin->getPluginURL().'/assets/stylesheets/style.css');

        $this->info_de =
            'Wegen Corona sind Mensa und Cafeten aktuell geschlossen. Hier gibt es nichts zu sehen... ' .
            'sobald es wieder ein Essensangebot auf dem Campus gibt, wird es hier erscheinen.';
        $this->info_en =
            'The refectory and cafeterias are closed because of the corona virus. Nothing to see here... ' .
            'as soon as meals will be available again, the menu will be shown here.';
    }

    public function settings_action() {
        if (Request::isXhr()) {
            $this->set_layout(null);
        } else {
            $this->set_layout($GLOBALS['template_factory']->open('layouts/base'));
        }
        $this->pricetypes = MensaMenu::getPriceTypes();
        $pricetype = UserConfig::get($GLOBALS['user']->id)->MENSAWIDGET_PRICETYPE ?: 'fullprice';
        $this->pricetype = [
            'name' => $this->pricetypes[$pricetype],
            'value' => $pricetype
        ];
    }

    public function save_settings_action() {
        CSRFProtection::verifyUnsafeRequest();
        $config = UserConfig::get($GLOBALS['user']->id);
        $pricetype = $config->MENSAWIDGET_PRICETYPE ?: 'fullprice';
        $new_pricetype = Request::quoted('pricetype');
        if ($new_pricetype != $pricetype) {
            $config->MENSAWIDGET_PRICETYPE = $new_pricetype;
            if ($config->store('MENSAWIDGET_PRICETYPE', $new_pricetype)) {
                PageLayout::postMessage(MessageBox::success(_('Die Änderungen wurden gespeichert.')));
            } else {
                PageLayout::postMessage(MessageBox::error(_('Die Änderungen konnten nicht gespeichert werden.')));
            }
        }
        if (!Request::isXhr()){
            $this->redirect(URLHelper::getLink('dispatch.php/start'));
        } else {
            $this->render_nothing();
        }
    }

}
