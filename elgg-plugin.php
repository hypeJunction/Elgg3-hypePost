<?php

return [
	'plugin' => [
		'name' => 'hypePost',
		'description' => 'Utility plugin for quick prototyping of content posts',
		'version' => '5.0.0',
		'dependencies' => [
			'hypeajax' => [
				'must_be_active' => false,
			],
			'hypetime' => [
				'must_be_active' => false,
			],
			'hypescraper' => [
				'must_be_active' => false,
			],
		],
	],

	'bootstrap' => \hypeJunction\Post\Bootstrap::class,

	'actions' => [
		'cover/delete' => [
			'controller' => \hypeJunction\Post\DeleteCoverAction::class,
		],
		'post/save' => [
			'controller' => \hypeJunction\Post\SavePostAction::class,
		],
	],

	'routes' => [
		'view:post' => [
			'path' => '/post/view/{guid}',
			'resource' => 'post/view',
			'public' => true,
		],
	],

	'view_extensions' => [
		'elgg.css' => [
			'post/styles.css' => [],
		],
		'elements/forms.css' => [
			'forms/validation.css' => [],
			'input/range.css' => [],
		],
	],

	'events' => [
		'fields' => [
			'object' => [
				\hypeJunction\Post\AddProfileModulesField::class => [],
				\hypeJunction\Post\SetObjectFields::class => [],
			],
			'group' => [
				\hypeJunction\Post\AddProfileModulesField::class => [],
			],
			'user' => [
				\hypeJunction\Post\AddProfileModulesField::class => [],
			],
		],
		'entity:cover:sizes' => [
			'all' => [
				\hypeJunction\Post\DefineCoverSizes::class => [],
			],
		],
		'register' => [
			'menu:social' => [
				\hypeJunction\Post\SocialMenu::class => [],
			],
			'menu:entity' => [
				\hypeJunction\Post\EntityMenu::class => [],
			],
		],
		'adapter:entity' => [
			'all' => [
				\hypeJunction\Post\PopulateExportData::class => [],
			],
		],
		'update' => [
			'object' => [
				\hypeJunction\Post\SaveEditHistory::class => [],
			],
		],
	],
];
