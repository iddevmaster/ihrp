<?php

return [
    'adminEmail' => 'admin@example.com',
    'adminName' => 'ศูนย์จริยธรรมการวิจัยในมนุษย์ สวรส',
    'sendMailCmd' => 'php /var/www/html/ihrp/yii email-queue/send-mail',
//    'dateFormat' => 'd/m/Y',
//    'dateFormatJs' => 'DD/MM/YYYY',
    'alertCheckInterval' => 5 * 60 * 1000,
    'fileEncryptionKey' => getenv('FILE_ENCRYPTION_KEY') ?: '8e19d211a2a7711502bcf0b9fc2adcd464b834bddcd9ba9e69fec59457d149dd',
    'user.passwordResetTokenExpire' => 15 * 60 * 1000,
    'i18nSuffixes' => [
        'th' => '',
        'en' => '_eng',
    ]
];
