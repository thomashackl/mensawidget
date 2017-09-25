<?php

class InitMensaWidget extends Migration {

    public function up() {
        DBManager::get()->exec("INSERT IGNORE INTO `config` VALUES
            (MD5('MENSAWIDGET_CACHE_LIFETIME'), '', 'MENSAWIDGET_CACHE_LIFETIME', '240', 1, 'integer', 'global', 'mensawidget', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 'Wie viele Minuten sollen die Daten von der STWNO-Homepage lokal vorgehalten werden?', '', '')"
        );
    }

    public function down() {
        DBManager::get()->exec("DELETE FROM `config` WHERE `field`='MENSAWIDGET_CACHE_VALIDITY'");
    }

}
