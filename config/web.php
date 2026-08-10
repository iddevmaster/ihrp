<?php

@ini_set('upload_max_size', '64M');
@ini_set('post_max_size', '64M');
@ini_set('max_execution_time', '300');

use kartik\datecontrol\Module;
use yii\helpers\FormatConverter;

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'ihrp',
    'name' => 'IHRP EC Online Submission',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'languagesDispatcher', 'devicedetect'],
    'language' => 'th-TH',
    'sourceLanguage' => 'th-TH',
    'timeZone' => 'Asia/Bangkok',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'components' => [
        'fileEncryptor' => [
            'class' => 'app\components\FileEncryptor',
            'encryptionKey' => $params['fileEncryptionKey'],
        ],
        'jwt' => [
            'class' => \sizeg\jwt\Jwt::class,
            'key' => 'transport-portal-secret',
            // You have to configure ValidationData informing all claims you want to validate the token.
            'jwtValidationData' => \app\components\JwtValidationData::class,
        ],
        'devicedetect' => [
		'class' => 'alexandernst\devicedetect\DeviceDetect'
	],
        'languagesDispatcher' => [
            'class' => 'cetver\LanguagesDispatcher\Component',
            'languages' => ['th', 'en'],
            /*
              or
              'languages' => function () {
              return \app\models\Language::find()->select('code')->column();
              },
             */
            // Order is important
            'handlers' => [
                    [
                    // Detects a language from the query parameter.
                    'class' => 'cetver\LanguagesDispatcher\handlers\QueryParamHandler',
                    'request' => 'request', // optional, the Request component ID.
                    'queryParam' => 'language' // optional, the query parameter name that contains a language.
                ],
                    [
                    // Detects a language from the session.
                    // Writes a language to the session, regardless of what handler detected it.
                    'class' => 'cetver\LanguagesDispatcher\handlers\SessionHandler',
                    'session' => 'session', // optional, the Session component ID.
                    'key' => 'language' // optional, the session key that contains a language.
                ],
                    [
                    // Detects a language from the cookie.
                    // Writes a language to the cookie, regardless of what handler detected it.
                    'class' => 'cetver\LanguagesDispatcher\handlers\CookieHandler',
                    'request' => 'request', // optional, the Request component ID.
                    'response' => 'response', // optional, the Response component ID.
                    'cookieConfig' => [// optional, the Cookie component configuration.
                        'class' => 'yii\web\Cookie',
                        'name' => 'language',
                        'domain' => '',
                        'expire' => strtotime('+1 year'),
                        'path' => '/',
                        'secure' => true | false, // depends on wRequest::$isSecureConnection
                        'httpOnly' => true,
                    ]
                ],
                    [
                    // Detects a language from an authenticated user.
                    // Writes a language to an authenticated user, regardless of what handler detected it.
                    // Note: The property "identityClass" of the "User" component must be an instance of "\yii\db\ActiveRecord"
                    'class' => 'cetver\LanguagesDispatcher\handlers\UserHandler',
                    'user' => 'user', // optional, the User component ID.
                    'languageAttribute' => 'language_code' // optional, an attribute that contains a language.
                ],
                    [
                    // Detects a language from the "Accept-Language" header.
                    'class' => 'cetver\LanguagesDispatcher\handlers\AcceptLanguageHeaderHandler',
                    'request' => 'request', // optional, the Request component ID.
                ],
                    [
                    // Detects a language from the "language" property.
                    'class' => 'cetver\LanguagesDispatcher\handlers\DefaultLanguageHandler',
                    'language' => 'en' // the default language.
                /*
                  or
                  'language' => function () {
                  return \app\models\Language::find()
                  ->select('code')
                  ->where(['is_default' => true])
                  ->createCommand()
                  ->queryScalar();
                  },
                 */
                ]
            ],
        ],
        'util' => [
            'class' => 'app\components\Util',
        ],
        'session' => [
            'name' => 'IHRPSESSID',
//            'savePath' => __DIR__ . '/../tmp',
        ],
        'thaiFormatter' => [
            'class' => 'dixonsatit\thaiYearFormatter\ThaiYearFormatter',
            'dateFormat' => 'php:d/m/Y',
            'datetimeFormat' => 'php:d/m/Y H:i',
            'timeZone' => 'UTC',
        ],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
//            'dateFormat' => 'php:d/m/Y',
            'dateFormat' => 'php:d/m/Y',
            'datetimeFormat' => 'php:d/m/Y H:i',
            'timeFormat' => 'php:H:i',
//            'timeZone' => 'Asia/Bangkok'
            'timeZone' => 'UTC',
            'nullDisplay' => '',
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '9a7QGx5jq6ZRYWTVdGugAtfwDwFQA9oJHatyai',
            'baseUrl' => '',
            'csrfParam' => '_ihrpCSRF',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'class' => 'app\rbac\RbacUser',
            'superUserRole' => 'ผูดูแลระบบ',
            'identityClass' => 'app\models\User',
//            'enableAutoLogin' => true,
            'loginUrl' => ['site/login'],
            'identityCookie' => [
                'name' => '_ihrpIdentity',
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.gmail.com',
//                'username' => 'echrkku@gmail.com',
//                'password' => 'zmlsofiqtkzkkclf',
                'username' => 'ecihrpsubmission@gmail.com',
                'password' => 'nlfavcyigxntrlav',
                'authMode' => 'login',
                'port' => '587',
                'encryption' => 'tls',
                'StreamOptions' => [
                    'ssl' => [
                        'allow_self_signed' => TRUE,
                        'verify_peer' => FALSE,
                    ]
                ]
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                    [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            //'enablePrettyUrl' => true,
            //'showScriptName' => false,
            //'baseUrl' => '/echr',
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => [
                        'api/submission',
                    ],
                    'only' => ['document-status', 'committee-assessment'],
                    'extraPatterns' => [
                        'GET document-status' => 'document-status',
                        'GET committee-assessment' => 'committee-assessment',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => [
                        'api/meeting',
                    ],
                    'only' => ['calendar'],
                    'extraPatterns' => [
                        'GET calendar' => 'calendar',
                    ],
                ],
            ],
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'assetManager' => [
            'appendTimestamp' => true,
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'sourcePath' => null, // do not publish the bundle
                    'js' => [
                        '/remark/global/vendor/jquery/jquery.js',
                    ]
                ],
                'yii\bootstrap\BootstrapAsset' => [
                    'depends' => ['yii\web\JqueryAsset'],
                    'sourcePath' => null, // do not publish the bundle
                    'js' => [
                        '/remark/global/vendor/bootstrap/bootstrap.js',
                    ],
                    'css' => [
                        '/remark/global/css/bootstrap.css',
                    ]
                ],
                'yii\bootstrap\BootstrapPluginAsset' => FALSE,
            ],
        ],
    ],
    'modules' => [
        'audit' => [
            'class' => 'bedezign\yii2\audit\Audit',
            'ignoreActions' => ['*'],
            'maxAge' => 30,
//            'panels' => [
////                'audit/request',
////                'audit/error',
//                'audit/trail',
//            ],
        ],
        'rbac' => [
            'class' => 'johnitvn\rbacplus\Module',
        ],
        'redactor' => 'yii\redactor\RedactorModule',
        'gridview' => [
            'class' => '\kartik\grid\Module'
        ],
        'datecontrol' => [
            'class' => '\kartik\datecontrol\Module',
            // format settings for displaying each date attribute (ICU format example)
//            'displaySettings' => [
//                Module::FORMAT_DATE => 'php:d/m/Y',
//                Module::FORMAT_TIME => 'php:H:i:s',
//                Module::FORMAT_DATETIME => 'php:d/m/Y H:i',
//            ],
            // format settings for saving each date attribute (PHP format example)
            'saveSettings' => [
                Module::FORMAT_DATE => 'php:Y-m-d', // saves as unix timestamp
                Module::FORMAT_TIME => 'php:H:i:s',
                Module::FORMAT_DATETIME => 'php:Y-m-d H:i:s',
            ],
            // set your display timezone
            'displayTimezone' => 'Asia/Bangkok',
            // set your timezone for date saved to db
            'saveTimezone' => 'Asia/bangkok',
            // automatically use kartik\widgets for each of the above formats
            'autoWidget' => true,
            // default settings for each widget from kartik\widgets used when autoWidget is true
            'autoWidgetSettings' => [
                Module::FORMAT_DATE => ['type' => kartik\widgets\DatePicker::TYPE_COMPONENT_APPEND, 'pluginOptions' => ['autoclose' => true]], // example
                Module::FORMAT_DATETIME => ['type' => \kartik\datetime\DateTimePicker::TYPE_COMPONENT_APPEND, 'pluginOptions' => ['autoclose' => true]], // setup if needed
                Module::FORMAT_TIME => [], // setup if needed
            ],
        // custom widget settings that will be used to render the date input instead of kartik\widgets,
        // this will be used when autoWidget is set to false at module or widget level.
//            'widgetSettings' => [
//                Module::FORMAT_DATE => [
//                    'class' => 'yii\jui\DatePicker', // example
//                    'options' => [
//                        'dateFormat' => 'php:d/M/Y',
//                        'options' => ['class' => 'form-control'],
//                        '_container' => ['class' => 'empty'],
//                    ]
//                ]
//            ]
        // other settings
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
            // uncomment the following to add your IP if you are not connecting from localhost.
            //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
            // uncomment the following to add your IP if you are not connecting from localhost.
            //'allowedIPs' => ['127.0.0.1', '::1'],
        'allowedIPs' => ['*'],
        'generators' => [//here
            'ajaxcrud' => [// generator name
                'class' => 'johnitvn\ajaxcrud\generators\Generator', //'yii\gii\generators\crud\Generator', // generator class
                'templates' => [//setting for out templates
                    'finedayCrud' => '@app/generators/ajaxcrud/default', // template name => path to template
                ]
            ]
        ],
    ];
}

return $config;
