<?php if (!$_USER) return; ?>
<script>
    function genId() {
        return Math.random().toString(36).slice(2, 9) + Date.now().toString(36);
    }

    let clientID = genId();
    let ws = null,
        pingIv = null;

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

    document.addEventListener('DOMContentLoaded', function() {
        // Connect to the websocket
        connectWS();
    });
</script>