<?php 
require '../vendor/autoload.php';
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Illuminate\Database\Capsule\Manager as Capsule;
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
session_start();
error_reporting(1);
ini_set('max_execution_time', 5000);



$app = new \Slim\App([
'settings' =>[
      'displayErrorDetails' =>true,
 'db'=>[
      'driver' =>'mysql',
      'host' =>'localhost',
      'database' =>'grocerynew',
      'username' =>'root',
      'password' =>'',
      'charset' =>'utf8',   
      'prefix' =>''
      ]
      ]
]);


 
$container = $app->getContainer();
$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($container['settings']['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$container['db'] = function($container) use ($capsule){
	return $capsule;
};

$container['auth'] = function ($container) {
return new Auth;
};
$container['authUser'] = function ($container) {
return new UserAuth;
};
$container['authMerchant'] = function ($container) {
return new MerchantAuth;
};
$container['flash'] = function ($container) {
return new \Slim\Flash\Messages();
};
$container['view'] = function ($container) {
$view = new \Slim\Views\Twig('../templates', [
'cache' => false
]);

$basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');

 $view->addExtension(new Slim\Views\TwigExtension($container['router'], $basePath));
$view->getEnvironment()->addGlobal('auth',[
    
'sessionOut' =>$container->auth->checkSession(),
'sessionOut1' =>$container->auth->check1(),
'allData' =>$container->auth->decoded(),
'categoryLists' =>$container->auth->categoryListApi(),
'socialIcons' =>$container->auth->socialIconApi(),
'page' =>$container->auth->page()
    
]);



$view->getEnvironment()->addGlobal('flash',$container->flash);
return $view;
};
$container['validator']=function($container){
    return new Validator($container);
};

$container['AuthController']=function($container){
    return new AuthController($container);
};

$container['csrf'] = function ($container) {

	$guard = new \Slim\Csrf\Guard;
    $guard->setPersistentTokenMode(true);
    return $guard;
};
$app->add(new ValidationErrorsMiddleware($container));
// $app->add(new CsrfViewMiddleware($container));
// $app->add($container->csrf);





require '../app/routes.php';
$app->run();

