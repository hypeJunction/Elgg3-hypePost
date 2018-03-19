<?php

return [
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
