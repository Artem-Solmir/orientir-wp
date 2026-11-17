<?php
// Copy to admin-otp-config.php (git-ignored). Same ADMIN_OTP_SECRET as in apps/web/.env
return [
    // Куда уходит код; эту же почту показываем на странице входа (NEXT_PUBLIC_ADMIN_LOGIN_EMAIL).
    'email' => 'tk.orientir@gmail.com',
    // Must match ADMIN_OTP_SECRET in .env (at least 32 characters).
    'secret' => 'change-me-to-the-same-secret-as-in-env-file',
    'from' => 'Сайт Ориентир <no-reply@orientir.by>',
    'origins' => ['http://localhost:3000', 'http://127.0.0.1:3000', 'https://orientir.by', 'https://www.orientir.by'],
];
