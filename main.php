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

function insert(): void
{
    global $_USER, $_WEBSOCKET_INSERTED;
    if (!$_USER || $_WEBSOCKET_INSERTED) return;

    $_WEBSOCKET_INSERTED = true;
?>
    <script>
        function genId() {
            return Math.random().toString(36).slice(2, 9) + Date.now().toString(36);
        }

        let clientID = genId();

        function connectWS(cb) {
            console.log("connected")
            if (ws) {
                try {
                    ws.close();
                } catch (e) {}
            }
            clearInterval(pingIv);
            ws = new WebSocket('wss://magictintin.fr/ws');

            ws.onopen = () => {
                ping();
                pingIv = setInterval(ping, 30000); // keep-alive < 100s Cloudflare timeout
                if (cb) cb();
            };

            ws.onmessage = e => {
                const body = e.data;
                if (body === 'ping') return;
                try {
                    const msg = JSON.parse(body);
                    // console.log(msg)
                    if (msg.from === clientID) return; // ignore own echo
                    handleMsg(msg);
                } catch (err) {}
            };

            ws.onclose = () => {
                clearInterval(pingIv);
                setTimeout(() => connectWS(), 3500);
            };

            ws.onerror = () => {};
        }

        function ping() {
            if (ws?.readyState === 1) {
                ws.send(`cestmoi/<?php echo $_USER["username"] ?>:ping`);
                // console.log("ping sent");
            }
        }

        function send(type, data = {}) {
            if (ws?.readyState === 1)
                ws.send(`cestmoi/<?php echo $_USER["username"] ?>:${JSON.stringify({ type, from: clientID, ...data })}`);
        }

        function handleMsg(msg) {
            switch (msg.type) {
                case 'new':
                    console.log("new");
                    break;
            }
        }
    </script>
<?php
}
