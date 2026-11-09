<?php
// Copy to send-config.php (git-ignored) and fill in. build:static copies it next to send.php.
return [
    'to' => 'tk.orientir@gmail.com',
    // Must be an address on the site's own domain, otherwise mail servers treat the message as spoofed.
    'from' => 'Сайт Ориентир <no-reply@orientir.by>',
    // Origins allowed to post the form.
    'origins' => ['https://orientir.by', 'https://www.orientir.by'],
];
