<?php if (!$_USER) return; ?>
<script>
    function genId() {
        return Math.random().toString(36).slice(2, 9) + Date.now().toString(36);
    }

    let clientID = genId();
    let cestmoi_ws = null,
        cestmoi_pingIv = null;

    function connectWS(cb) {
        console.log("connected")
        if (cestmoi_ws) {
            try {
                cestmoi_ws.close();
            } catch (e) {}
        }
        clearInterval(cestmoi_pingIv);
        cestmoi_ws = new WebSocket('wss://magictintin.fr/ws');

        cestmoi_ws.onopen = () => {
            ping();
            cestmoi_pingIv = setInterval(ping, 30000); // keep-alive < 100s Cloudflare timeout
            if (cb) cb();
        };

        cestmoi_ws.onmessage = e => {
            const body = e.data;
            if (body === 'ping') return;
            try {
                const msg = JSON.parse(body);
                // console.log(msg)
                if (msg.from === clientID) return; // ignore own echo
                handleMsg(msg);
            } catch (err) {}
        };

        cestmoi_ws.onclose = () => {
            clearInterval(cestmoi_pingIv);
            setTimeout(() => connectWS(), 3500);
        };

        cestmoi_ws.onerror = () => {};
    }

    function ping() {
        if (cestmoi_ws?.readyState === 1) {
            cestmoi_ws.send(`cestmoi/<?php echo $_USER["username"] ?>:ping`);
            // console.log("ping sent");
        }
    }

    function send(type, data = {}) {
        if (cestmoi_ws?.readyState === 1)
            cestmoi_ws.send(`cestmoi/<?php echo $_USER["username"] ?>:${JSON.stringify({ type, from: clientID, ...data })}`);
    }

    function handleMsg(msg) {
        switch (msg.type) {
            case 'new_achievement':
                console.log(`New achievement! (${msg.name})`);
                add_new_achievement_notification(msg.name, msg.icon)
                break;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Connect to the websocket
        connectWS();
    });
</script>