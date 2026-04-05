<?php

function insert_websocket_connection(): void
{
    global $_USER, $_WEBSOCKET_INSERTED;
    if (!$_USER || $_WEBSOCKET_INSERTED) return;

    $_WEBSOCKET_INSERTED = true;
    include_once(__DIR__ . "/scripts/websocket.php");
?>
<?php
}