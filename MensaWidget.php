<?php
/*
 * MensaWidget.php - Shows current mensa menu.
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

require_once('bootstrap.php');
require_once('controllers/menu.php');
require_once('models/MensaMenu.php');

class MensaWidget extends StudIPPlugin implements PortalPlugin {

    public function getPluginName() {
        return dgettext('mensawidget', 'Mensaplan');
    }

    function getPortalTemplate() {
        if ($GLOBALS['user']->id != 'nobody') {
            $trails_root = $this->getPluginPath();
            $dispatcher = new Trails_Dispatcher($this->getPluginPath(), "plugins.php", 'index');
            $controller = new MenuController($dispatcher);
            $controller->plugin = $this;

            $response = $controller->relay('menu/index');
            $template = $GLOBALS['template_factory']->open('shared/string');
            $template->content = $response->body;

            return $template;
        }
        return NULL;
    }

}
