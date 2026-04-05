<?php
require_once __DIR__ . '/qsj/auth.php';

$_QSJ  = new QsjAuth(require __DIR__ . '/qsj-config.php');
$_USER = $_QSJ->getUser();
$_WEBSOCKET_INSERTED = false;

function is_qsj_connected(): bool
{
    global $_USER;
    return $_USER !== null;
}

function insert_websocket_connection(): void
{
    global $_USER, $_WEBSOCKET_INSERTED;
    if (!$_USER || $_WEBSOCKET_INSERTED) return;

    $_WEBSOCKET_INSERTED = true;
    include_once(__DIR__ . "/includes/scripts/websocket.php");
?>
<?php
}
