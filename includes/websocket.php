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

function send_websocket_notification($content): void
{
    global $_USER, $_ACHIEVEMENT_NOTIFICATIONS;
    if (!$_USER) return;
    $msg = json_encode($content);
    @file_get_contents(
        "http://127.0.0.1:6442/push",
        false,
        stream_context_create(['http' => [
            'method' => 'POST',
            'header' => "Content-Type: text/plain\r\n",
            'content' => "cestmoi/". $_USER["username"] .":" . $msg
        ]])
    );

    if (strlen($msg) > 0)
        $_ACHIEVEMENT_NOTIFICATIONS .= "\ncestmoi_handleMsg(JSON.parse(`". trim($msg) . "`));\n";
}
