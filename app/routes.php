<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
$app->get('/index','AuthController:index')->setName('home');
$app->get('/loginPage','AuthController:loginPage')->setName('loginPage');
$app->get('/hotOffer/{id}','AuthController:hotOffer');
$app->get('/bannerPage/{id}','AuthController:bannerPage');
$app->get('/productPage/{name}/{id}','AuthController:SingleProductPage');
$app->get('/categoryPage/{id}','AuthController:categoryPage');

$app->get('/specialOffer','AuthController:specialOffer');
$app->get('/aboutUs','AuthController:aboutUs');

$app->post('/searchProduct','AuthController:searchproduct');
$app->get('/faq','AuthController:faq');

$app->post('/deleteOrder','AuthController:deleteOrder');

$app->get('/contact','AuthController:contact')->setName('contact');
$app->get('/payment','AuthController:payment')->setName('payment');

$app->post('/contactForm','AuthController:contactForm');
$app->post('/checkout','AuthController:checkout');

$app->post('/paymentPost','AuthController:paymentPost');

$app->post('/login','AuthController:loginPost');
$app->post('/signUp','AuthController:signUpPost');
$app->get('/changePassword','AuthController:changePassword');
$app->get('/myorders','AuthController:myorders')->setName('myorders')->setName('myorder');	
$app->get('/logOut','AuthController:getSignOut')->setName('logOut');	
$app->post('/changePasswordPost','AuthController:changePasswordPost');

$app->post('/postAddress','AuthController:postAddressData');
$app->post('/confirm','AuthController:confirm')->setName('confirm');

$app->group('/admin',function(){
$this->get('/adminpage','AuthController:index1')->setName('home1');

$this->get('/newCategory','AuthController:addNewCategory')->setName('addNewCategory');
$this->get('/newProduct','AuthController:addNewProduct')->setName('addNewProduct');

$this->get('/allCategory','AuthController:allCategory')->setName('categoryList');


$this->get('/allProducts','AuthController:allProducts')->setName('productList');

$this->get('/userlist','AuthController:userlist')->setName('userPage');	
$this->get('/orderList','AuthController:orderList')->setName('orderList');

$this->get('/cartoffer','AuthController:cartoffer')->setName('cartoffer');
$this->post('/addCartOffer','AuthController:addCartOffer');


$this->get('/allSlider','AuthController:allSlider')->setName('allSlider');	
$this->post('/processOrder', 'AuthController:processOrder')->setName('processOrder');

$this->get('/addSlider','AuthController:addSlider');
$this->post('/addSliderPost','AuthController:addSliderPost');

$this->post('/addCategoryPost','AuthController:addCategoryPost');
$this->post('/addProductPost','AuthController:addProductPost');

$this->post('/editCategoryPost','AuthController:editCategoryPost');
$this->post('/editProductPost','AuthController:editProductPost');

$this->post('/editCategory','AuthController:editCategory');
$this->post('/editProduct','AuthController:editProduct');

$this->post('/deleteCategory','AuthController:deleteCategory');
$this->post('/deleteProduct','AuthController:deleteProduct');

$this->get('/auth/signout','AuthController:getSignOut')->setName('auth.signout');	
})->add(new AuthMiddleware($container));

$app->group('/user',function(){
	$this->get('','AuthController:index1')->setName('home1');

	$this->get('/auth/signout','AuthController:getSignOut')->setName('auth.signout');
	
	
})->add(new AuthMiddleware($container));



