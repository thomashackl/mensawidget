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
        if (Studip\ENV == 'development') {
            $js = $this->plugin->getPluginURL() . '/assets/javascripts/mensawidget.js';
            $css = $this->plugin->getPluginURL() . '/assets/stylesheets/style.css';
        } else {
            $js = $this->plugin->getPluginURL().'/assets/javascripts/mensawidget.min.js';
            $css = $this->plugin->getPluginURL().'/assets/stylesheets/style.min.css';
        }
        PageLayout::addStylesheet($css);
        PageLayout::addScript($js);
        $currweek = date('W');
        $currweekplan = MensaMenu::getWeekPlan($currweek);
        $nextweek = date('W', time()+(7*24*60*60));
        $nextweekplan = MensaMenu::getWeekPlan($nextweek);
        if ($currweekplan) {
            if (is_array($currweekplan['datemap']) && is_array($nextweekplan['datemap'])) {
                $this->days = $currweekplan['datemap'] + $nextweekplan['datemap'];
                $this->today = date('d.m.Y');
                $this->data = $currweekplan['data'] + $nextweekplan['data'];
                $this->types = array_merge($currweekplan['types'], $nextweekplan['types']);
                $this->lastcurrentweekday = strtotime('next sunday', strtotime('yesterday'));
                $pricetypes = MensaMenu::getPriceTypes();
                $pricetype = UserConfig::get($GLOBALS['user']->id)->MENSAWIDGET_PRICETYPE ?: 'fullprice';
                $this->pricetype = [
                    'name' => $pricetypes[$pricetype],
                    'value' => $pricetype
                ];
                $this->mtime = $currweekplan['mtime'];
            } else {
                $this->error = MessageBox::info(
                    dgettext('mensawidget',
                        'Fehler in den vom STWNO übertragenen Daten. Bitte versuchen Sie es später wieder.'));
            }
        } else {
            $this->error = MessageBox::info(
                dgettext('mensawidget', 'Kein Speiseplan für die aktuelle Woche gefunden.'));
        }
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
