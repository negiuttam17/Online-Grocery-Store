<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class AuthMiddleware extends Middleware
{
	public function __invoke($request,$response,$next)
	{
				
		if (!$this->container->auth->check1()) {
			$this->container->flash->addMessage('error','please sign in');
			return $response->withRedirect($this->container->router->pathFor('loginPage'));
		}

		$response= $next($request,$response);
		return $response;
				

	}
}