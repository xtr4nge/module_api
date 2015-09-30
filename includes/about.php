<b>API</b>
<br><br>
<b>Parameters</b>
<br>
api: The actual parameter to perform actions using the API.
<br>
token: Authentication token. You can get/change the token from the config page. If the session is already established, the TOKEN is not required.

<br><br>
<b>using CURL</b>
<div style="font-family: monospace">
HTTP:8000
<br>curl -sS "http://{IP}:8000/modules/api/includes/ws_action.php?token={TOKEN}&api=/config/io/action"
<br>HTTPS:8443
<br>curl -sS "https://{IP}:8443/modules/api/includes/ws_action.php?token={TOKEN}&api=/config/io/action" --insecure
</div>

<br><br>
<b>CONFIG Interfaces</b>
<div style="font-family: monospace">
GET: /config/io/{in|out}/iface
<br>
SET: /config/io/{in|out}/iface/{value}
<br>
GET: /config/io/{in|out}/type
<br>
SET: /config/io/in/type/{value}
<br>
GET: /config/io/{in|out}/ip
<br>
SET: /config/io/in/ip/{value}
<br>
GET: /config/io/{in|out}/mask
<br>
SET: /config/io/{in|out}/mask/{value}
<br>
GET: /config/io/{in|out}/gw
<br>
SET: /config/io/{in|out}/gw/{value}
<br><br>
Example (SET): https://{IP}:8443/modules/api/includes/ws_action.php?token={TOKEN}&api=/config/io/in/ip/10.0.0.1
</div>

<br><br> <b>CONFIG Action Interface</b> (sniff|inject)
<div style="font-family: monospace">
GET: /config/inout/action
<br>
SET: /config/inout/action/{value}
<br><br>
Example (SET): https://{IP}:8443/modules/api/includes/ws_action.php?token={TOKEN}&api=/config/inout/action/wlan0
</div>

<br><br> <b>MODULES</b>
<div style="font-family: monospace">
GET: /module
<br>
GET: /module/{module-name}
<br>
SET: /module/{module-name}/{start|stop}
<br><br>
Example (START): https://{IP}:8443/modules/api/includes/ws_action.php?token={TOKEN}&api=/module/fruityproxy/start
</div>