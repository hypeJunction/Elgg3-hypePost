<?php

$plugin_root = __DIR__;
$root = dirname(dirname($plugin_root));
$alt_root = dirname(dirname(dirname($root)));

if (file_exists("$plugin_root/vendor/autoload.php")) {
	$path = $plugin_root;
} else if (file_exists("$root/vendor/autoload.php")) {
	$path = $root;
} else {
	$path = $alt_root;
}

return [
	'views' => [
		'default' => [
			'parsley.js' => $path . '/vendor/bower-asset/parsleyjs/dist/parsley.min.js',
		]
	],
	'actions' => [
		'cover/delete' => [
			'controller' => \hypeJunction\Post\DeleteCoverAction::class,
		],
	],
	'routes' => [
		'view:post' => [
			'path' => '/post/view/{guid}',
			'resource' => 'post/view',
			'public' => true,
		],
	],
];
