<?php

namespace hypeJunction\Post;

use Elgg\EntityPermissionsException;
use Elgg\Hook;
use Elgg\Http\ResponseBuilder;
use Elgg\Request;

class DeleteCoverAction {

	/**
	 * Delete cover
	 *
	 * @param Request $request Request
	 *
	 * @return ResponseBuilder
	 * @throws EntityPermissionsException
	 */
	public function __invoke(Request $request) {

		$entity = $request->getEntityParam();

		if (!$entity || !$entity->canEdit()) {
			throw new EntityPermissionsException();
		}

		if ($entity->deleteIcon('cover')) {
			return elgg_ok_response('', elgg_echo('post:cover:delete:success'));
		}

		return elgg_error_response(elgg_echo('post:cover:delete:error'));
	}
}