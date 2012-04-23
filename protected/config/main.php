<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Truth Or Dare',
        'sourceLanguage'=>'EN_US',
        'language'=>'EN_US',
        'timeZone' => 'Asia/Hong_Kong',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
                'application.helpers.*',
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'eric',
		 	// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
		),
		
	),

	// application components
	'components'=>array(
                'messages'=>array('class'=>'CDbMessageSource',),
                'behaviors' => array('ApplicationConfigBehavior'),
		'user'=>array(
                  // There you go, use our 'extended' version
                  'class'=>'application.components.EWebUser',
                  // enable cookie-based authentication
                  'allowAutoLogin'=>true,
                  'autoUpdateFlash' => false, // add this line to disable the flash counter
                  'defaultReturnUrl'=>array('user/myPage'),
                ), 
                'file'=>array(
                    'class'=>'application.extensions.file.CFile',
                ),
                'image'=>array(
                  'class'=>'application.extensions.image.CImageComponent',
                    // GD or ImageMagick
                    'driver'=>'GD',
                    // ImageMagick setup path
                    'params'=>array('directory'=>'/opt/local/bin'),
                ),

		// uncomment the following to enable URLs in path-format
		/*
		'urlManager'=>array(
			'urlFormat'=>'path',
			'rules'=>array(
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		*/
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=truthordare',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => 'eric',
			'charset' => 'utf8',
                        'enableProfiling' => true, //ADDED FOR DEBUG
                        'enableParamLogging' => true //ADDED FOR DEBUG
		),
		'errorHandler'=>array(
			// use 'site/error' action to display errors
            'errorAction'=>'site/error',
        ),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					//'class'=>'CFileLogRoute', //AVANT QUE J'INSTALLE L'EXTENSION YII DEBUGGER
					'levels'=>'error, warning',
                                        'class'=>'ext.yii-debug-toolbar.YiiDebugToolbarRoute', // EXTENSION YII DEBUG
                                        'ipFilters'=>array('127.0.0.1'),//RAJOUTE EGALEMENT MANUELLEMENT
				),
				// uncomment the following to show log messages on web pages
				
				array(
					'class'=>'CWebLogRoute',
				),
				
			),
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'ehochedez@hotmail.com',
	),
);