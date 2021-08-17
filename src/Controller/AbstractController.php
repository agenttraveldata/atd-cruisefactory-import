<?php


namespace ATD\CruiseFactory\Controller;


use ATD\CruiseFactory\Feed\Feed;
use WP_REST_Server;

abstract class AbstractController {
	protected Feed $feed;
	protected string $apiNamespace = 'atd/cfi/v1';
	protected string $apiEndpointPrefix;
	protected array $routes = [];

	public function setFeed( Feed $feed ) {
		$this->feed = $feed;
	}

	public function getFeed(): Feed {
		return $this->feed;
	}

	public function getApiEndpointPrefix(): string {
		return $this->apiEndpointPrefix ?? '';
	}

	public function getRoutes(): array {
		return $this->routes;
	}

	protected function addRoute( string $endpoint, callable $callback, bool $requiresAuth = false, string $method = WP_REST_Server::READABLE ): self {
		if ( $requiresAuth ) {
			if ( ! is_user_logged_in() ) {
				return $this;
			}

			$role = reset( $GLOBALS['current_user']->roles );
			if ( ! $role || ! $GLOBALS['wp_roles']->roles[ $role ]['capabilities']['edit_posts'] ) {
				return $this;
			}
		}

		register_rest_route( $this->apiNamespace, $this->getApiEndpointPrefix() . $endpoint, [
			'methods'             => $method,
			'callback'            => $callback,
			'args'                => [],
			'permission_callback' => function () {
				return true;
			}
		] );

		return $this;
	}
}