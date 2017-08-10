<?php
/**
 * MensaMenu.php
 *
 * Model class for the current mensa plan.
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

class MensaMenu {

    public static function getWeekPlan($week) {
        $weekplan = array();
        $curryear = intval(date('Y'));
        $cachefile = $GLOBALS['CACHING_FILECACHE_PATH'] . '/mensa-' . $curryear . '-' . $week . '.csv';
        // Try to get cached data if file exists and is still valid.
        if (!file_exists($cachefile) ||
                (filemtime($cachefile) <= mktime() - (Config::get()->MENSAWIDGET_CACHE_LIFETIME*60))) {
            // Fetch CSV with data from STWNO homepage.
            $file = file_get_contents('http://www.stwno.de/infomax/daten-extern/csv/UNI-P/' . intval($week) . '.csv?t=' . mktime());
            if (@file_put_contents($cachefile, $file)) {
                $handle = fopen($cachefile, 'r');
            } else {
                $handle = fopen('http://www.stwno.de/infomax/daten-extern/csv/UNI-P/' . intval($week) . '.csv?t=' . mktime(), 'r');
            }
        } else {
            $handle = fopen($cachefile, 'r');
        }
        // Mensa menu, ordered by date
        $data = array();
        $types = array();
        $icon_mapping = self::getIconMapping();
        if ($handle) {
            // Get first and last days to consider for current week.
            $weekplan['start'] = strtotime(date(datetime::ISO8601, strtotime(date('Y').'W'.$week)));
            $weekplan['end'] = strtotime(date(datetime::ISO8601, strtotime(date('Y').'W'.$week.'-5')));
            $weekplan['mtime'] = filemtime($cachefile);
            $i = 0;
            while ($string = fgets($handle)) {
                $current = str_getcsv(html_entity_decode($string), ';');
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
                        'day' => $current[0],
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

    public static function getPriceTypes() {
        return array(
            'fullprice' => dgettext('mensawidget', 'Studierende/Beschäftige/Gäste'),
            'student' => dgettext('mensawidget', 'Studierende'),
            'employee' => dgettext('mensawidget', 'Beschäftigte'),
            'guest' => dgettext('mensawidget', 'Gäste')
        );
    }

    private static function getIconMapping()  {
        // Map icons to different kinds of meal (vegetarian, pork, fish etc.)
        return array(
            'A' => array(
                'icon' => '/assets/images/alkohol.png',
                'title' => dgettext('mensawidget', 'Alkohol')
            ),
            'B' => array(
                'icon' => '/assets/images/eu_organic.png',
                'title' => dgettext('mensawidget', 'DE-ÖKO-006 mit ausschließlich biologisch erzeugten Rohstoffen')
            ),
            'F' => array(
                'icon' => '/assets/images/fisch.png',
                'title' => dgettext('mensawidget', 'Fisch')
            ),
            'G' => array(
                'icon' => '/assets/images/gefluegel.png',
                'title' => dgettext('mensawidget', 'Geflügel')
            ),
            'L' => array(
                'icon' => '/assets/images/lamm.png',
                'title' => dgettext('mensawidget', 'Lamm')
            ),
            'MV' => array(
                'icon' => '/assets/images/mensa_vital.png',
                'title' => dgettext('mensawidget', 'Mensa Vital')
            ),
            'MSC' => array(
                'icon' => '/assets/images/msc.png',
                'title' => dgettext('mensawidget', 'zertifizierte nachhaltige Fischerei (MSC-C-53400)')
            ),
            'R' => array(
                'icon' => '/assets/images/rind.png',
                'title' => dgettext('mensawidget', 'Rind')
            ),
            'S' => array(
                'icon' => '/assets/images/schwein.png',
                'title' => dgettext('mensawidget', 'Schwein')
            ),
            'V' => array(
                'icon' => '/assets/images/vegetarisch.png',
                'title' => dgettext('mensawidget', 'Vegetarisch')
            ),
            'VG' => array(
                'icon' => '/assets/images/vegan.png',
                'title' => dgettext('mensawidget', 'Vegan')
            ),
            'W' => array(
                'icon' => '/assets/images/wild.png',
                'title' => dgettext('mensawidget', 'Wild')
            )
        );
    }

}
