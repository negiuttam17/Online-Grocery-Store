<?php
class CsrfViewMiddleware extends Middleware
{
	public function __invoke($request,$response,$next)
	{
				$this->container->view->getEnvironment()->addGlobal('csrf',[
					'field' =>'
						<input type ="hidden" id="csrf_name" name="'.$this->container->csrf->getTokenNameKey() .'" value="' . $this->container->csrf->getTokenName()  .'">
						<input type ="hidden" id="csrf_value" name="'.$this->container->csrf->getTokenValueKey() .'" value="' . $this->container->csrf->getTokenValue()  .'">
					'
				]);
				
		$response= $next($request,$response);
		return $response;
	}
}