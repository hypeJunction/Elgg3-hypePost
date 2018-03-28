<?php

namespace hypeJunction\Post;

use Elgg\BadRequestException;
use Elgg\Http\ResponseBuilder;
use Elgg\Request;
use Exception;

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

			$svc = $request->elgg()->{'posts.model'};
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

			$data = [
				'entity' => $entity,
				'forward_url' => $forward_url,
			];

			$name = $entity->getDisplayName() ? : elgg_echo("item:$entity->type:$entity->subtype");
			$message = $request->elgg()->echo('success:post:save', [$name]);

			return elgg_ok_response($data, $message, $forward_url);
		} catch (Exception $e) {
			return elgg_error_response(
				$e->getMessage(),
				REFERER,
				$e->getCode() ? : ELGG_HTTP_INTERNAL_SERVER_ERROR
			);
		}
	}
}
