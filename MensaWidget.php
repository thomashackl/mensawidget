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

class MensaWidget extends StudIPPlugin implements PortalPlugin {
    public function getPluginName() {
        return _('Mensaplan');
    }

    function getPortalTemplate() {
        if (Studip\ENV == 'development') {
            $js = $this->getPluginURL() . '/assets/javascripts/mensawidget.js';
            $css = $this->getPluginURL() . '/assets/stylesheets/style.css';
        } else {
            $js = $this->getPluginURL().'/assets/javascripts/mensawidget.min.js';
            $css = $this->getPluginURL().'/assets/stylesheets/style.min.css';
        }
        PageLayout::addStylesheet($css);
        PageLayout::addScript($js);
        $currweek = intval(date('W'));
        $currweekplan = $this->getWeekPlan($currweek);
        $nextweek = intval(date('W', mktime()+(7*24*60*60)));
        $nextweekplan = $this->getWeekPlan($nextweek);
        // Open template.
        $tfac = new Flexi_TemplateFactory(__DIR__.'/templates');
        $template = $tfac->open('daymenu');
        if ($currweekplan) {
            $template->days = $currweekplan['datemap'] + $nextweekplan['datemap'];
            $template->today = date('d.m.Y');
            $template->data = $currweekplan['data'] + $nextweekplan['data'];
            $template->types = array_merge($currweekplan['types'], $nextweekplan['types']);
            $template->lastcurrentweekday = strtotime('next saturday', strtotime('yesterday'));
        } else {
            $template->error = MessageBox::info(
                dgettext('mensawidget', 'Kein Speiseplan für die aktuelle Woche gefunden.'));
        }
        return $template;
    }

    private function getWeekPlan($week) {
        $weekplan = array();
        $curryear = intval(date('Y'));
        $cachefile = $GLOBALS['CACHING_FILECACHE_PATH'] . '/mensa-' . $curryear . '-' . $week . '.csv';
        // Try to get cached data if file exists and is still valid.
        if (!file_exists($cachefile) ||
            (filemtime($cachefile) <= mktime() - (24))
        ) {
            // Fetch CSV with data from STWNO homepage.
            $file = file_get_contents('http://www.stwno.de/infomax/daten-extern/csv/UNI-P/' . ($week - 1) . '.csv?t=' . mktime());
            if (@file_put_contents($cachefile, $file)) {
                $handle = fopen($cachefile, 'r');
            } else {
                $handle = fopen('http://www.stwno.de/infomax/daten-extern/csv/UNI-P/' . ($week - 1) . '.csv?t=' . mktime(), 'r');
            }
        } else {
            $handle = fopen($cachefile, 'r');
        }
        // Mensa menu, ordered by date
        $data = array();
        $types = array();
        $icon_mapping = $this->getIconMapping();
        if ($handle) {
            // Get first and last days to consider for current week.
            $weekplan['start'] = strtotime(date(datetime::ISO8601, strtotime(date('Y').'W'.$week)));
            $weekplan['end'] = strtotime(date(datetime::ISO8601, strtotime(date('Y').'W'.$week.'5')));
            $i = 0;
            while ($current = fgetcsv($handle, 5000, ';')) {
                // Skip first line as it contains only descriptional headers.
                if ($i > 0) {
                    // Map meal type to some textual representation.
                    switch (substr($current[2], 0, 1)) {
                        case 'S':
                            $index = 'soup';
                            $typename = dgettext('mensawidget', 'Suppe');
                            break;
                        case 'H':
                            $index = 'main';
                            $typename = dgettext('mensawidget', 'Hauptgericht');
                            break;
                        case 'B':
                            $index = 'side';
                            $typename = dgettext('mensawidget', 'Beilage');
                            break;
                        case 'N':
                            $index = 'dessert';
                            $typename = dgettext('mensawidget', 'Nachspeise');
                            break;
                        default:
                            $index = 'unknown';
                            $typename = 'unbekannt';
                    }
                    $icons = array();
                    // Add icons for meal type.
                    foreach (explode(',', $current[4]) as $k) {
                        if ($icon_mapping[trim($k)]) {
                            $icons[] = $icon_mapping[trim($k)];
                        }
                    }
                    // Add complete record to weekplan.
                    $weekplan['data'][$current[0]][$index][] = array(
                        'day' => $day,
                        'meal' => $current[3],
                        'kind' => $current[4],
                        'fullprice' => $current[5],
                        'student' => $current[6],
                        'employee' => $current[7],
                        'guest' => $current[8],
                        'icons' => $icons
                    );
                    $weekplan['types'][$index] = $typename;
                    $weekplan['datemap'][$current[0]] = $current[1];
                }
                $i++;
            }
            return $weekplan;
        }
    }

    private function getIconMapping()  {
        // Map icons to different kinds of meal (vegetarian, pork, fish etc.)
        return array(
            'A' => array(
                'icon' => $this->getPluginURL() . '/assets/images/alkohol.gif',
                'title' => dgettext('mensawidget', 'Alkohol')
            ),
            'B' => array(
                'icon' => $this->getPluginURL() . '/assets/images/eu_organic.jpg',
                'title' => dgettext('mensawidget', 'Bio-Gericht')
            ),
            'F' => array(
                'icon' => $this->getPluginURL() . '/assets/images/fisch.jpg',
                'title' => dgettext('mensawidget', 'Fisch')
            ),
            'G' => array(
                'icon' => $this->getPluginURL() . '/assets/images/gefluegel.jpg',
                'title' => dgettext('mensawidget', 'Geflügel')
            ),
            'MV' => array(
                'icon' => $this->getPluginURL() . '/assets/images/mensa_vital.jpg',
                'title' => dgettext('mensawidget', 'mensaVital')
            ),
            'MSC' => array(
                'icon' => $this->getPluginURL() . '/assets/images/msc.png',
                'title' => dgettext('mensawidget', 'zertifiziert Marine Stewardship Council MSC')
            ),
            'R' => array(
                'icon' => $this->getPluginURL() . '/assets/images/rind.jpg',
                'title' => dgettext('mensawidget', 'Rind')
            ),
            'S' => array(
                'icon' => $this->getPluginURL() . '/assets/images/schwein.jpg',
                'title' => dgettext('mensawidget', 'Schwein')
            ),
            'V' => array(
                'icon' => $this->getPluginURL() . '/assets/images/vegetarisch.jpg',
                'title' => dgettext('mensawidget', 'Vegetarisch')
            ),
            'VG' => array(
                'icon' => $this->getPluginURL() . '/assets/images/vegan.jpg',
                'title' => dgettext('mensawidget', 'Vegan')
            )
        );
    }

}