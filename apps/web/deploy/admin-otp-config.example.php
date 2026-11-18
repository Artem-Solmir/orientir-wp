<?php
// Copy to admin-otp-config.php (git-ignored). Same ADMIN_OTP_SECRET as in apps/web/.env
return [
    // Куда уходит код; эту же почту показываем на странице входа (NEXT_PUBLIC_ADMIN_LOGIN_EMAIL).
    'email' => 'mrslay1989@gmail.com',
    // Must match ADMIN_OTP_SECRET in .env (at least 32 characters).
    'secret' => 'ВСТАВЬТЕ_ТОТ_ЖЕ_СЕКРЕТ_ЧТО_В_ENV_МИНИМУМ_32_СИМВОЛА',
    'from' => 'Сайт Ориентир <no-reply@orientir.by>',
    // Отправка через Gmail (SMTP). Пароль — «пароль приложения» из настроек Google (16 символов), не обычный пароль.
    'smtp' => [
        'host' => 'smtp.gmail.com',
        'port' => 465,
        'user' => 'tk.orientir@gmail.com',
        'pass' => 'ПАРОЛЬ_ПРИЛОЖЕНИЯ_GOOGLE',
    ],
    'origins' => ['http://localhost:3000', 'http://127.0.0.1:3000', 'https://orientir.by', 'https://www.orientir.by'],
];
