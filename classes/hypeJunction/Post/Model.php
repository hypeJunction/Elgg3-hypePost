<?php

namespace hypeJunction\Post;

use Elgg\EntityNotFoundException;
use Elgg\EntityPermissionsException;
use Elgg\HttpException;
use Elgg\Request;
use ElggEntity;
use ElggObject;
use hypeJunction\ValidationException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Model {

	/**
	 * @var Post
	 */
	protected $post;

	/**
	 * Constructor
	 *
	 * @param Post $post
	 */
	public function __construct(Post $post) {
		$this->post = $post;
	}

	/**
	 * Get entity fields
	 *
	 * @param ElggEntity $entity Entity
	 * @param array      $vars   Form vars
	 *
	 * @return array
	 */
	public function getFields(ElggEntity $entity, array $vars = []) {

		$fields['title'] = [
			'#type' => 'text',
			'#section' => 'content',
			'#input' => function (Request $request) {
				return elgg_get_title_input();
			},
			'#profile' => false,
			'required' => true,
			'#priority' => 100,
		];

		$fields['description'] = [
			'#type' => 'longtext',
			'rows' => 3,
			'#section' => 'content',
			'#profile' => false,
			'required' => true,
			'#priority' => 100,
		];

		$fields['excerpt'] = [
			'#type' => 'text',
			'maxlength' => 200,
			'#section' => 'content',
			'#profile' => false,
		];

		$fields['icon'] = [
			'#type' => 'file',
			'#section' => 'sidebar',
			'name' => 'icon',
			'#input' => function (Request $request) {
				$files = elgg_get_uploaded_files('icon');
				if (empty($files)) {
					return null;
				}

				return array_shift($files);
			},
			'#validate' => function ($value, $field) {
				$required = elgg_extract('required', $field);
				$label = elgg_extract('#label', $field);

				if ($required) {
					if ((!$value instanceof UploadedFile)) {
						throw new ValidationException(elgg_echo('error:field:required', [$label]));
					}

					if (!$value->isValid()) {
						throw new ValidationException(elgg_echo('error:field:invalid_file', [
							$label,
							elgg_get_friendly_upload_error($value->getError()),
						]));
					}
				}
			},
			'#getter' => function (ElggEntity $entity) {
				$icon = $entity->getIcon('master');

				return $icon->exists() ? $icon : null;
			},
			'#setter' => function (ElggEntity $entity) {
				return $entity->saveIconFromUploadedFile('icon');
			},
			'#priority' => 400,
			'#profile' => false,
			'#visibility' => function (\ElggEntity $entity){
				$params = [
					'entity' => $entity,
				];

				return elgg()->hooks->trigger(
					'uses:icon',
					"$entity->type:$entity->subtype",
					$params,
					false
				);
			},
		];

		$fields['cover'] = [
			'#type' => 'post/cover',
			'#section' => 'sidebar',
			'#input' => function (Request $request) {
				$files = elgg_get_uploaded_files('cover');
				$cover = $request->getParam('cover', []);

				return [
					'file' => elgg_extract('file', $files),
					'url' => elgg_extract('url', $cover),
				];
			},
			'#validate' => function ($value, $params) {
				$required = elgg_extract('required', $params);
				$label = elgg_extract('#label', $params);

				if ($required) {
					if ((!$value['file'] instanceof UploadedFile) && empty($value['url'])) {
						throw new ValidationException(elgg_echo('error:field:required', [$label]));
					}
				}
			},
			'#getter' => function (ElggEntity $entity) {
				$svc = elgg()->{'posts.post'};
				/* @var $svc \hypeJunction\Post\Post */

				$cover = $svc->getCover($entity);

				if ($cover->getCoverUrl()) {
					return $cover;
				}
			},
			'#setter' => function (ElggEntity $entity, $value) {
				$file = elgg_extract('file', $value);
				$url = elgg_extract('url', $value);

				if ($file instanceof UploadedFile && $file->isValid()) {
					$tmp_filename = time() . $file->getClientOriginalName();
					$tmp = new \ElggFile();
					$tmp->owner_guid = $entity->guid;
					$tmp->setFilename("tmp/$tmp_filename");
					$tmp->open('write');
					$tmp->close();

					copy($file->getPathname(), $tmp->getFilenameOnFilestore());

					$entity->saveIconFromElggFile($tmp, 'cover');

					$tmp->delete();
				} else if ($url) {
					$bytes = file_get_contents($url);

					if (!empty($bytes)) {
						$tmp = new \ElggFile();
						$tmp->owner_guid = $entity->guid;
						$tmp->setFilename("tmp/" . pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_BASENAME));

						$tmp->open('write');
						$tmp->write($bytes);
						$tmp->close();

						$entity->saveIconFromElggFile($tmp, 'cover');

						$tmp->delete();
					}
				}
			},
			'#priority' => 400,
			'#profile' => false,
			'#visibility' => function (\ElggEntity $entity){
				$params = [
					'entity' => $entity,
				];

				return elgg()->hooks->trigger(
					'uses:cover',
					"$entity->type:$entity->subtype",
					$params,
					false
				);
			},
		];


		$fields['tags'] = [
			'#type' => 'tags',
			'#setter' => function (ElggEntity $entity, $value) {
				if (!is_string($value)) {
					$value = string_to_tag_array($value);
				}
				$entity->tags = $value;
			},
			'#section' => 'content',
			'#profile' => false,
			'#visibility' => function (\ElggEntity $entity){
				$params = [
					'entity' => $entity,
				];

				return elgg()->hooks->trigger(
					'uses:tags',
					"$entity->type:$entity->subtype",
					$params,
					true
				);
			},
		];

		$fields['access_id'] = [
			'#type' => 'access',
			'#section' => 'sidebar',
			'required' => true,
			'#getter' => function (ElggEntity $entity) {
				if ($entity->guid) {
					return $entity->access_id;
				}

				return get_default_access();
			},
			'#priority' => 100,
		];

		$fields['disable_comments'] = [
			'#type' => 'select',
			'#section' => 'sidebar',
			'options_values' => [
				0 => elgg_echo('enable'),
				1 => elgg_echo('disable'),
			],
			'#input' => function (Request $request) {
				return (bool) $request->getParam('disable_comments');
			},
			'#getter' => function (ElggEntity $entity) {
				if (isset($entity->disable_comments)) {
					return $entity->disable_comments;
				}

				return null;
			},
			'#priority' => 300,
		];

		$params = [
			'entity' => $entity,
			'vars' => $vars,
		];

		$fields = elgg_trigger_plugin_hook('fields', "$entity->type", $params, $fields);
		$fields = elgg_trigger_plugin_hook('fields', "$entity->type:$entity->subtype", $params, $fields);

		$fields['type'] = [
			'#type' => 'hidden',
		];

		$fields['subtype'] = [
			'#type' => 'hidden',
		];

		$fields['guid'] = [
			'#type' => 'hidden',
		];

		if (!isset($fields['container_guid'])) {
			$fields['container_guid'] = [
				'#type' => 'hidden',
			];
		}

		$fields['_hash'] = [
			'#type' => 'hidden',
			'#getter' => function (ElggEntity $entity) {
				return elgg_build_hmac([
					'guid' => (int) $entity->guid,
					'type' => $entity->type,
					'subtype' => $entity->subtype,
				])->getToken();
			},
		];

		if (!isset($fields['cancel'])) {
			$fields['cancel'] = [
				'#type' => 'post/cancel',
				'#section' => 'actions',
				'#label' => false,
			];
		}

		if (!isset($fields['submit'])) {
			$fields['submit'] = [
				'#type' => 'submit',
				'#section' => 'actions',
				'#label' => false,
				'value' => function (ElggEntity $entity) {
					return $entity->guid ? elgg_echo('update') : elgg_echo('save');
				},
			];
		}

		foreach ($fields as $key => $field) {
			if (!isset($field['name']) && is_string($key)) {
				$field['name'] = $key;
			}
			$fields[$key] = $field;
		}

		return $fields;
	}

	/**
	 * Get profile fields
	 *
	 * @param ElggEntity $entity Entity
	 * @param array      $vars   View vars
	 *
	 * @return array
	 */
	public function getProfileFields(ElggEntity $entity, array $vars = []) {
		$fields = $this->getFields($entity, $vars);

		$params = $vars;
		$params['entity'] = $entity;

		$fields = elgg_trigger_plugin_hook('fields:profile', "$entity->type", $params, $fields);
		$fields = elgg_trigger_plugin_hook('fields:profile', "$entity->type:$entity->subtype", $params, $fields);

		$ignored = array_merge(\ElggEntity::$primary_attr_names, [
			'title',
			'description',
			'tags',
			'timezone',
			'submit',
			'_hash',
		]);

		$fields = array_filter($fields, function ($e) use ($entity, $ignored) {
			$name = elgg_extract('name', $e);
			if (!$name) {
				return false;
			}

			if (in_array($name, $ignored)) {
				return false;
			}

			$output = elgg_extract('#profile', $e);
			if ($output instanceof \Closure) {
				$output = $output($entity, $e);
			}

			if ($output === false) {
				return false;
			}

			return true;
		});

		return $this->normalizeFields($entity, $fields);
	}

	/**
	 * Prepare form vars
	 *
	 * @param ElggEntity $entity Entity
	 * @param array      $vars   Default vars
	 *
	 * @return array
	 */
	public function getFormVars(ElggEntity $entity, array $vars = []) {

		if ($entity->container_guid) {
			$container = $entity->getContainerEntity();
		} else {
			$container = elgg_extract('container', $vars);
			if (!$container) {
				$container_guid = elgg_extract('container_guid', $vars);
				$container = get_entity($container_guid);
			}
		}

		$defaults = [
			'title' => '',
			'description' => '',
			'access_id' => get_default_access(),
		];

		$fields = $this->getFields($entity, $vars);

		foreach ($fields as $key => $field) {
			$name = elgg_extract('name', $field, $key);
			if (isset($defaults[$name])) {
				continue;
			}
			$defaults[$name] = '';
		}

		$sticky = elgg_get_sticky_values("edit:$entity->type:$entity->subtype");
		elgg_clear_sticky_form("edit:$entity->type:$entity->subtype");

		foreach ($defaults as $key => $value) {
			$vars[$key] = elgg_extract($key, $sticky);
		}

		foreach ($fields as $key => $field) {
			$name = elgg_extract('name', $field);
			if (!$name) {
				continue;
			}

			if (isset($vars[$name])) {
				$field['value'] = $vars[$name];
			}

			$fields[$key] = $field;
		}

		$vars['fields'] = $this->normalizeFields($entity, $fields);
		$vars['entity'] = $entity;
		$vars['guid'] = $entity->guid;
		$vars['container'] = $container;
		$vars['container_guid'] = $container->guid;

		return $vars;
	}

	/**
	 * Normalize fields
	 *
	 * @param ElggEntity $entity Entity
	 * @param array      $fields Fields
	 *
	 * @return array
	 */
	public function normalizeFields(ElggEntity $entity, array $fields = []) {

		$make_label = function ($suffix, $strict = true) use ($entity) {
			$type = $entity->type;
			$subtype = $entity->subtype;
			$keys = [
				"field:$type:$subtype:$suffix",
				"field:$type:$suffix",
				"field:$suffix",
				"$suffix",
			];

			foreach ($keys as $key) {
				if (elgg_language_key_exists($key)) {
					return elgg_echo($key);
				}
			}

			if (!$strict) {
				return elgg_echo("field:$type:$subtype:$suffix");
			}

			return null;
		};

		foreach ($fields as $key => $field) {
			if (isset($field['#visibility'])) {
				$visible = elgg_extract('#visibility', $field);
				if ($visible instanceof \Closure) {
					$visible = $visible($entity, $field);
				}

				if ($visible === false) {
					unset($fields[$key]);
					continue;
				}
			}

			$name = elgg_extract('name', $field);
			$value = elgg_extract('value', $field);

			if (!isset($value)) {
				$getter = elgg_extract('#getter', $field);
				if ($getter instanceof \Closure) {
					$value = $getter($entity, $field);
				} else {
					$value = $entity->$name;
				}
			}

			$field['value'] = $value;
			$field['entity'] = $entity;

			foreach ($field as $prop => $val) {
				if (strpos($prop, '#') === 0) {
					continue;
				}

				if ($val instanceof \Closure) {
					$field[$prop] = $val($entity, $field);
				}
			}

			if (!isset($field['#label'])) {
				$field['#label'] = $make_label($name, false);
			}
			if (!isset($field['#help'])) {
				$field['#help'] = $make_label("$name:help");
			}
			if (!isset($field['placeholder'])) {
				$field['#placeholder'] = $make_label("$name:placeholder");
			}

			$width = elgg_extract('#width', $field, 6);
			if ($width == '6') {
				$widget_class = 'elgg-col-1of1';
			} else if ($width == 4) {
				$widget_class = 'elgg-col-2of3';
			} else if ($width == 3) {
				$widget_class = 'elgg-col-1of2';
			} else if ($width == 2) {
				$widget_class = 'elgg-col-1of3';
			} else {
				$widget_class = "elgg-col-{$width}of6";
			}

			$field['#class'] = elgg_extract_class($field, ['elgg-col', $widget_class], '#class');

			if (empty($field['#section'])) {
				$field['#section'] = 'content';
			}

			if ($field['#section'] === 'sidebar') {
				$field['#view'] = 'post/input/field';
			}

			$fields[$key] = $field;
		}

		uasort($fields, function ($f1, $f2) {
			$p1 = (int) elgg_extract('#priority', $f1, 500);
			$p2 = (int) elgg_extract('#priority', $f2, 500);
			if ($p1 === $p2) {
				return 0;
			}

			return $p1 < $p2 ? -1 : 1;
		});

		return $fields;
	}

	/**
	 * Accept save action values
	 *
	 * @param $request Request
	 *
	 * @return ElggEntity|false
	 * @throws EntityPermissionsException
	 * @throws HttpException
	 * @throws EntityNotFoundException
	 */
	public function save(Request $request) {

		$guid = (int) $request->getParam('guid');
		$type = $request->getParam('type');
		$subtype = $request->getParam('subtype');

		elgg_make_sticky_form("edit:$type:$subtype");

		$user = elgg_get_logged_in_user_entity();

		if ($guid) {
			$entity = get_entity($guid);
			if (!$entity) {
				throw new EntityNotFoundException();
			}

			if (!$entity->canEdit()) {
				throw new EntityPermissionsException();
			}
			$container = $entity->getContainerEntity();

			$add_to_river = false;
		} else {
			$container_guid = $request->getParam('container_guid');
			if (!$container_guid) {
				$container_guid = $user->guid;
			}

			$container = get_entity($container_guid);
			if (!$container || !$container->canWriteToContainer(0, $type, $subtype)) {
				throw new EntityPermissionsException();
			}

			$add_to_river = true;

			$class = elgg_get_entity_class($type, $subtype) ? : ElggObject::class;

			$entity = new $class();

			if (!$entity instanceof ElggEntity) {
				throw new HttpException("$class must implement " . ElggEntity::class);
			}

			$entity->owner_guid = $user->guid;
			$entity->container_guid = $container_guid;
		}

		$access_id = $request->getParam('access_id');
		if (!isset($access_id)) {
			if ($container instanceof \ElggGroup) {
				$access_id = $container->group_acl;
			} else {
				$access_id = get_default_access($user);
			}
		}

		$entity->access_id = $access_id;

		$fields = $this->normalizeFields($entity, $this->getFields($entity));

		$fields = array_filter($fields, function ($e) {
			$name = elgg_extract('name', $e);
			if (!$name) {
				return false;
			}
			if (in_array($name, ElggEntity::$primary_attr_names)) {
				return false;
			}

			return true;
		});

		$errors = [];

		foreach ($fields as $key => $field) {
			$name = elgg_extract('name', $field);

			$input = elgg_extract('#input', $field);
			if ($input instanceof \Closure) {
				$value = $input($request, $field);
			} else {
				$value = get_input($name);
			}

			if (!isset($value)) {
				// Field is not present
				continue;
			}

			try {
				$validator = elgg_extract('#validate', $field);
				$label = elgg_extract('#label', $field);
				if ($validator instanceof \Closure) {
					$valid = $validator($value, $field);
					if ($valid === false) {
						throw new ValidationException("error:field:invalid", [$label]);
					}
				} else {
					$required = elgg_extract('required', $field);
					if ($required && empty($value) && $value !== '0') {
						throw new ValidationException(elgg_echo("error:field:required", [$label]));
					}
				}
			} catch (ValidationException $ex) {
				$errors[] = $ex->getMessage();
				continue;
			}

			$field['value'] = $value;

			$fields[$key] = $field;
		}

		if ($errors) {
			throw new HttpException(implode("\r\n", $errors));
		}

		if (!$entity->save()) {
			return false;
		}

		foreach ($fields as $field) {
			$name = elgg_extract('name', $field);
			$value = elgg_extract('value', $field);

			$setter = elgg_extract('#setter', $field);
			if ($setter instanceof \Closure) {
				$setter($entity, $value, $field);
			} else {
				$entity->$name = $value;
			}
		}

		$entity->setVolatileData('add_to_river', $add_to_river);

		if (!$entity->save()) {
			return false;
		}

		if (!isset($entity->published_status)) {
			$entity->published_status = 'published';
			elgg_trigger_event('publish', 'object', $entity);
		}

		elgg_clear_sticky_form("edit:$entity->type:$entity->subtype");

		return $entity;
	}
}
