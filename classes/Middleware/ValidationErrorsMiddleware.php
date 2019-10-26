<?php
class ValidationErrorsMiddleware extends Middleware
{
	public $errors;
	public function __invoke($request,$response,$next)
	{
		
		
		$response =$next($request,$response);
		return $response;
	}
}