<?php

namespace hypeJunction\Post;

use Elgg\BadRequestException;
use Elgg\Http\ResponseBuilder;
use Elgg\Request;
use Exception;
use Psr\Log\LogLevel;

class SavePostAction {

	/**
	 * Save a post
	 *
	 * @param Request $request Request
	 *
	 * @return ResponseBuilder
	 */
	public function __invoke(Request $request) {

		$guid = (int) $request->getParam('guid');
		$type = $request->getParam('type');
		$subtype = $request->getParam('subtype');
		$hash = $request->getParam('_hash');

		try {
			$hmac = elgg_build_hmac([
				'guid' => $guid,
				'type' => $type,
				'subtype' => $subtype,
			]);

			if (!$hmac->matchesToken($hash)) {
				$msg = $request->elgg()->echo('error:hmac');
				throw new BadRequestException($msg);
			}

			$svc = \hypeJunction\Post\Model::instance();
			/* @var $svc \hypeJunction\Post\Model */

			$entity = $svc->save($request);

			if (!$entity) {
				return elgg_error_response($request->elgg()->echo('error:post:save'));
			}

			$forward_url = $entity->getURL();
			if (!$forward_url) {
				if ($entity->getContainerEntity() instanceof \ElggGroup) {
					$forward_url = elgg_generate_entity_url($entity, 'collection', 'group');
				} else {
					$forward_url = elgg_generate_entity_url($entity, 'collection', 'owner');
				}
			}

			if (!$forward_url) {
				$forward_url = '';
			}

			$hook_params = [
				'context' => $request->getParam('_context'),
				'request' => $request,
				'entity' => $entity,
			];

			$forward_url = elgg_trigger_plugin_hook('post:forward', "$type:$subtype", $hook_params, $forward_url);

			$data = [
				'entity' => $entity,
				'forward_url' => $forward_url,
			];

			$name = $entity->getDisplayName() ? : elgg_echo("item:$entity->type:$entity->subtype");

			$success_keys = [
				"success:$entity->type:$entity->subtype:save",
				"success:$entity->type:save",
				'success:post:save'
			];

			foreach ($success_keys as $key) {
				if (elgg_language_key_exists($key)) {
					$message = $request->elgg()->echo($key, [$name]);
					break;
				}
			}

			return elgg_ok_response($data, $message, $forward_url);
		} catch (Exception $e) {
			elgg_log($e, LogLevel::ERROR);

			return elgg_error_response(
				$e->getMessage(),
				REFERER,
				$e->getCode() ? : ELGG_HTTP_INTERNAL_SERVER_ERROR
			);
		}
	}
}
