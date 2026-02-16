<?php
declare(strict_types=1);

namespace app\middlewares;

use flight\Engine;
use Tracy\Debugger;

class SecurityHeadersMiddleware
{
	protected Engine $app;

	public function __construct(Engine $app)
	{
		$this->app = $app;
	}
	
	public function before(array $params): void
	{
		$nonce = $this->app->get('csp_nonce');

		// development mode to execute Tracy debug bar CSS
		$tracyCssBypass = "'nonce-{$nonce}'";
		if(Debugger::$showBar === true) {
			$tracyCssBypass = ' \'unsafe-inline\'';
		}

		// CSP développement : très permissive pour localhost et ressources externes
		$csp = "default-src 'self' localhost http://localhost:* https:; script-src 'self' 'unsafe-inline' 'unsafe-eval' localhost http://localhost:* https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' localhost http://localhost:* https://cdn.jsdelivr.net https://fonts.googleapis.com {$tracyCssBypass}; font-src 'self' 'unsafe-inline' localhost http://localhost:* https://fonts.gstatic.com https://cdn.jsdelivr.net; img-src 'self' data: https: localhost http://localhost:*;";
		$this->app->response()->header('X-Frame-Options', 'SAMEORIGIN');
		$this->app->response()->header("Content-Security-Policy", $csp);
		$this->app->response()->header('X-XSS-Protection', '1; mode=block');
		$this->app->response()->header('X-Content-Type-Options', 'nosniff');
		$this->app->response()->header('Referrer-Policy', 'strict-no-referrer');
		$this->app->response()->header('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
		$this->app->response()->header('Permissions-Policy', 'geolocation=()');
	}
}