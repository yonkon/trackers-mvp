<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Trackers-MVP',
  'sourceLanguage' => 'en_US',
  'language' => 'ru',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
    'application.modules.user.UserModule',
    'application.modules.user.models.*',
    'application.modules.user.components.*',
    'application.modules.rights.*',
    'application.modules.rights.components.*',
    'application.extensions.IpGeoBase.*',
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool

		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'103103103',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
		),
    'user'=>array(
      'tableUsers' => 'tbl_users',
      'tableProfiles' => 'tbl_profiles',
      'tableProfileFields' => 'tbl_profiles_fields',
      'captcha' => array(),
      'hash' => 'md5',

      # send activation email
      'sendActivationMail' => true,

      # allow access for non-activated users
      'loginNotActiv' => false,

      # activate user on registration (only sendActivationMail = false)
      'activeAfterRegister' => false,

      # automatically login from registration
      'autoLogin' => true,

      # registration path
      'registrationUrl' => array('/user/registration'),

      # recovery password path
      'recoveryUrl' => array('/user/recovery'),

      # login form path
      'loginUrl' => array('/login'),

      # page after login
      'returnUrl' => array('/panel'),

      # page after logout
      'returnLogoutUrl' => array('//'),
    ),
    'rights'=>array(
      'superuserName'=>'Admin', // Name of the role with super user privileges.
      'authenticatedName'=>'Authenticated',  // Name of the authenticated user role.
      'userIdColumn'=>'id', // Name of the user id column in the database.
      'userNameColumn'=>'username',  // Name of the user name column in the database.
      'enableBizRule'=>true,  // Whether to enable authorization item business rules.
      'enableBizRuleData'=>true,   // Whether to enable data for business rules.
      'displayDescription'=>true,  // Whether to use item description instead of name.
      'flashSuccessKey'=>'RightsSuccess', // Key to use for setting success flash messages.
      'flashErrorKey'=>'RightsError', // Key to use for setting error flash messages.

      'baseUrl'=>'/rights', // Base URL for Rights. Change if module is nested.
      'layout'=>'rights.views.layouts.main',  // Layout to use for displaying Rights.
      'appLayout'=>'application.views.layouts.main', // Application layout.
      'cssFile'=>'rights.css', // Style sheet file to use for Rights.
      'install'=>false,  // Whether to enable installer.
      'debug'=>false,
    ),

	),

	// application components
	'components'=>array(

    'user'=>array(
      'class'=>'RWebUser',
      // enable cookie-based authentication
      'allowAutoLogin'=>true,
      'loginUrl'=>array('/login'),
      'returnUrl'=>array('/panel'),

    ),
    'authManager'=>array(
      'class'=>'RDbAuthManager',
      'connectionID'=>'db',
      'defaultRoles'=>array('Authenticated', 'Guest'),
      'itemTable'=>'authitem',
      'itemChildTable'=>'authitemchild',
      'assignmentTable'=>'authassignment',
      'rightsTable'=>'rights',
    ),

		// uncomment the following to enable URLs in path-format

		'urlManager'=>array(
			'urlFormat'=>'path',
      'showScriptName'=>false,
      'caseSensitive'=>false,
			'rules'=>array(
//        'index' => 'site/index',
        'login' => 'user/login',
        'logout' => 'user/logout',
        'user'=>'/user/user',
        'profile'=>'/user/profile',
        'panel'=>'/panel/index',
        'panel/<action:\w+>'=>'/panel/<action>',
				'<controller:\w+>/<id:\d+>'=>'<controller>/view/<id>',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
				'<action:\w+>'=>'site/<action>',
			),
		),


		// database settings are configured in database.php
		'db'=>require(dirname(__FILE__).'/database.php'),

		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>YII_DEBUG ? null : 'site/error',
		),

		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
				// uncomment the following to show log messages on web pages

				array(
					'class'=>'CWebLogRoute',
				),

			),
		),

    'IpGeoBase' => array(
      'class' => 'IpGeoBase',
      'useLocalDB' => true,
    ),

	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'yonkon.ru@gmail.com',
	),
);
