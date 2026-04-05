<?php
require_once __DIR__ . '/qsj/auth.php';

$_QSJ  = new QsjAuth(require __DIR__ . '/qsj-config.php');
$_USER = $_QSJ->getUser(true);
$_HEAD_SETUP = false;
$_FOOT_SETUP = false;
$_WEBSOCKET_INSERTED = false;

function is_qsj_connected(): bool
{
    global $_USER;
    return $_USER !== null;
}

function get_qsj_username(): bool
{
    global $_USER;
    return $_USER ? $_USER['username'] : '';
}

$files = glob(__DIR__ . '/includes/*.{php}', GLOB_BRACE);
foreach ($files as $file) {
    include_once($file);
}

function cestmoi_setup_head() {
    global $_USER, $_HEAD_SETUP;
    if (!$_USER || $_HEAD_SETUP) return;
    $_HEAD_SETUP = true;

    include_once(__DIR__ . "/includes/styles/head.php");
}

function cestmoi_setup_foot() {
    global $_USER, $_FOOT_SETUP;
    if (!$_USER || $_FOOT_SETUP) return;
    $_FOOT_SETUP = true;

    insert_popup_location();
    insert_achievement_script();
    insert_websocket_connection();
}
