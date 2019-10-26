<?php
use Slim\Views\Twig as View;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Respect\Validation\Validator as v;
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;


class AuthController extends Controller
{

	public function editAbout($request,$response,$args)
	{

		$capsule = new Capsule;
		$users = Capsule::table('newtable')->where('id', '=', $args['id'])->get();
		return $this->view->render($response,'admin/editAbout.html',["users"=>$users]);
	}
	public function editAboutUsForm($request,$response)
	{
		
		$id = $request->getParam('id');		
			Capsule::table('newtable')
            ->where('id', $id)
            ->update(['FeedType' =>  $request->getParam('feedType'), 'Message' =>  $request->getParam('message')]);
        return $response->withRedirect($this->router->pathFor('aboutUs')); 
    }
    public function deleteAbout($request,$response,$args)
	    {
	    	Capsule::table('newtable')->where('id', '=', $args['id'])->delete();
	    	return $response->withRedirect($this->router->pathFor('aboutUs'));    	 
	    }
    
	public function aboutUs($request ,$response)
		{
			$aboutUS = $this->auth->aboutPageApi();

			
   			return $this->view->render($response,'about.html',["aboutUS"=>$aboutUS]);
		}

		public function processOrder($request, $response)
		{
			$orders = Capsule::table('orders')->join('users', 'orders.User_Id', '=', 'users.User_Id')->select('orders.*','users.Name','users.Email')->where('order_id',"=",$request->getParam('processOrder'))->get();

			return $this->view->render($response,'admin/processOrder.html',["orders"=>$orders]);
		}

		public function confirm($request,$response)
		{
			// $value = $request->input('status');
			// var_dump($val);
			// die();
			// Capsule::table('orders')->where('order_id',"=",$request->getParam('confirm')->update('status'=",$request->getParam('status')));
			//  $e = Capsule::table('product')->where('product_name',"=",$key)->decrement('stock',$value);
		}
	
	public function specialOffer($request,$response)
	{
		$productPage = Capsule::table('product')
            ->join('category', 'product.category_id', '=', 'category.id')          
            ->where('product.special','=', 1)->select('product.*','category.slug','category.category_name')
            ->get();
         return $this->view->render($response,'product_page.html',["productPage"=>$productPage]);
	}

	public function index($request ,$response)
		{
			$banners = $this->auth->bannerApi();
			$hotOffers = $this->auth->hotOfferApi();
			$categoryList = $this->auth->categoryListApi();
			$socialIcon = $this->auth->socialIconApi();			
			return $this->view->render($response,'index.html',["banners"=>$banners,"hotOffers"=>$hotOffers,"categoryLists"=> $categoryList,"socialIcons"=>$socialIcon]);
		}


	
	
public function contact($request,$response)
{
	return $this->view->render($response,'contact.html',["banners"=>$banners,"hotOffers"=>$hotOffers]);
 
}


	public function bannerPage($request,$response,$args)
	{
		$bannerPages = $this->auth->bannerPageApi($args['id']);
		return $this->view->render($response,'single_page_products.html',["bannerPages"=>$bannerPages]);
	}


	public function hotOffer($request,$response,$args)
	{
		$hotOffers = $this->auth->hotOfferSinglePage($args['id']);

		return $this->view->render($response,'single_page_hot_offer.html',["hotOffers"=>$hotOffers]);


	}


	public function categoryPage($request,$response,$args)
	{
		$productPage = Capsule::table('product')
            ->join('category', 'product.category_id', '=', 'category.id')          
            ->where('category.slug','=',$args[id])->select('product.*','category.slug','category.category_name')
            ->get();
		// var_dump($productPage);
		// die();
        return $this->view->render($response,'product_page.html',["productPage"=>$productPage]);
    }

    public function SingleProductPage($request,$response,$args)
    {
    	$productPage = $this->auth->productPageApi($args['name'],$args['id']);
    	return $this->view->render($response,'single_page_products.html',["productPage"=>$productPage]);
    }


    public function contactForm($request,$response)
    {
    	$this->auth->contactFormApi(
						$request->getParam('Name'),
						$request->getParam('Email'),
						$request->getParam('Telephone'),
						$request->getParam('Subject'),
						$request->getParam('Message')
					);
    	$this->flash->addMessage('success','Message has been sent');
    	return $response->withRedirect($this->router->pathFor('contact'));
    }

    public function changePassword($request,$response,$args)
	{
		return $this->view->render($response,'changePassword.html');
		
	}

	public function changePasswordPost($request,$response,$args)
	{
		// $Validation = $this->Validator->validate($request,[
		// 				'password'=> v::nowhitwspace()->notEmpty(),
		// 				'Newpassword'=>v:: nowhitwspace()->notEmpty(),
		// 			]);
		// if($Validation->failed()){
		// return $response->withRedirect($this->router->pathFor('changePassword'));	
		// }
		$this->auth->changePasswordApi(
						$request->getParam('Email'),
						$request->getParam('Password'),
						$request->getParam('Newpassword')
					);
    	$this->flash->addMessage('success','Password Change Successfully');
    	return $response->withRedirect($this->router->pathFor('loginPage'));
	}

	public function searchproduct($request,$response)
	{
		$productPage = $this->auth->getSearchProducts($request->getParam('Product'));
		if(is_null($productPage))
		{
			$this->flash->addMessage('No such product available');
            return $response->withRedirect($this->router->pathFor('index'));
        }
        else
        {
			return $this->view->render($response,'product_page.html',["productPage"=>$productPage]);
		}
	}

	
    public function faq($request,$response)
    {
    	$faq = $this->auth->faqApi();    
    	return $this->view->render($response,'faq.html',["faqs"=>$faq]);
    }


	public function loginPage($request ,$response)
		{
			 $this->auth->logout();
			return $this->view->render($response,'login.html');
		}
	public function loginPost($request,$response)
		{
			$auth = $this->auth->loginPostApi(
					$request->getParam('Email'),
					$request->getParam('Password')
			);	

            if(!$auth){                	
            	$this->flash->addMessage('error','Wrong username or password!!!');
            	$this->auth->logout();
            	return $response->withRedirect($this->router->pathFor('loginPage'));
            }
            else
            {            	
            	if ($_SESSION['isAdmin'] == 0) {
            		$this->flash->addMessage('success','You are successfully login in');
            		return $response->withRedirect($this->router->pathFor('home'));
            	}
            	else{
            		$this->flash->addMessage('success','You are successfully login to Admin Portal...');
            		return $response->withRedirect($this->router->pathFor('home1'));
            	}
            	
            }					
		}
	public function signUpPost($request,$response)
		{
			$auth = $this->auth->signUpPostApi(
					$request->getParam('Username'),
					$request->getParam('Password'),
					$request->getParam('Email'),
					$request->getParam('Address'),
					$request->getParam('Phone')
				);
			$this->flash->addMessage('success','New user successfully added!!!');
			return $response->withRedirect($this->router->pathFor('loginPage'));

							
		}

	public function getSignOut($request,$response)
		{
			$this->auth->logout();
			$this->flash->addMessage('global','You are successfully logout');
			return $response->withRedirect($this->router->pathFor('loginPage'));
		}


                      /*Admin  API list */

public function index1($request ,$response)
		{	
			return $this->view->render($response,'admin/adminPage.html');
		}

public function orderList($request,$response)
{
	$orderLists = Capsule::table('orders')->join('users', 'orders.User_Id', '=', 'users.User_Id')->select('orders.*','users.Name','users.Email')->where('status',"!=",'Fulfilled')->get();

	return $this->view->render($response,'admin/orderList.html',["orderLists"=>$orderLists]);
	

}

public function cartoffer($request,$response)
{
	return $this->view->render($response, 'admin/cartoffer.html');
	

}

public function addCartOffer($request,$response)
{
	$cartoffer = capsule::table('cartoffer')->updateOrInsert(
		[	'amount_min' => $request->getParam('cartamount') ,
			'amount_discount' => $request->getParam('%discount'),			
			'percent_discount' => $request->getParam('amountdiscount')			 
		]);

	return $this->view->render($response, 'admin/cartoffer.html');

}

public function myorders($request,$response)
{
	$orders = $this->auth->paymentApi($_SESSION['userId']);
	return $this->view->render($response,'myorder.html',["orders"=>$orders]);
	// $orderLists = Capsule::table('orders')->join('users', 'orders.User_Id', '=', 'users.User_Id')->select('orders.*','users.Name','users.Email')->where('orders.User_id','=',$_SESSION['userId'])->get();
	
	// return $this->view->render($response,'admin/orderList.html',["orderLists"=>$orderLists]);
	

}

public function allSlider($request,$response)
{
	$allSliders = Capsule::table('slider')->get();
	return $this->view->render($response,'admin/allSlider.html',["allSliders"=>$allSliders]);
}

public function addSlider($request,$response)
	{
		return $this->view->render($response,'admin/addSlider.html');
	}

public function addSliderPost($request,$response)
{

	$checkforpics = json_encode($_FILES['files']['error']);	
	$move =  "C:/xampp/htdocs/Srilakshmi/public/asset/slider/";
	foreach ($_FILES["files"]["tmp_name"] as $key => $value)
		{
			$tmp_name[$key] = $_FILES["files"]["tmp_name"][$key];

			$mimeType = $_FILES["files"]["type"][$key];
			if($mimeType == "image/gif"){
				$fileExtension = ".png";
			}elseif($mimeType == "image/png"){
				$fileExtension = ".png";
			}elseif($mimeType == "image/jpeg" or $mimeType == "image/jpg"){
				$fileExtension = ".png";
			}	

			$name[$key] = $move .basename($request->getParam('slider_name').$fileExtension);
			move_uploaded_file($tmp_name[$key], $name[$key]);
		}
	
	$sliders = Capsule::table('slider')->updateOrInsert(
		[	'name' => $request->getParam('slider_name') ,
			'description' => $request->getParam('slider_text'),			
			'image' => $request->getParam('slider_name').$fileExtension			 
		]
    );
	return $response->withRedirect($this->router->pathFor('home1'));
}

public function userlist($request,$response)
{
	$userlist = Capsule::table('users')->where('users.isAdmin','=',0)->get();
	return $this->view->render($response,'admin/userlist.html',["userlists"=>$userlist]);
}
	public function addCategoryPost($request,$response)
{
	$categorylists = Capsule::table('category')->updateOrInsert(
		['category_name' => $request->getParam('name') ],
        ['slug' => $request->getParam('slug')]
    );
    return $response->withRedirect($this->router->pathFor('categoryList'));
}
	
public function addProductPost($request,$response)
{

	$checkforpics = json_encode($_FILES['files']['error']);	
	$move =  "C:/xampp/htdocs/Srilakshmi/public/asset/products/";
	foreach ($_FILES["files"]["tmp_name"] as $key => $value)
		{
			$tmp_name[$key] = $_FILES["files"]["tmp_name"][$key];

			$mimeType = $_FILES["files"]["type"][$key];
			if($mimeType == "image/gif"){
						$fileExtension = ".gif";
					}elseif($mimeType == "image/png"){
						$fileExtension = ".png";
					}elseif($mimeType == "image/jpeg" or $mimeType == "image/jpg"){
						$fileExtension = ".jpg";
					}	

			$name[$key] = $move .basename($request->getParam('product_name').$fileExtension);
			move_uploaded_file($tmp_name[$key], $name[$key]);
		}
	
	$categorylists = Capsule::table('product')->updateOrInsert(
		[	'product_name' => $request->getParam('product_name') ,
			'brand_name' => $request->getParam('brand_name'),
			'quantity' => $request->getParam('quantity'),
			'stock' => $request->getParam('stock'),
			'price' => $request->getParam('price'),
			'offer_price' => $request->getParam('offer_price'),
			'description' => $request->getParam('description'),
			'category_id' => $request->getParam('category'),
			'tag' => $request->getParam('tag'),
			'rating' => $request->getParam('rating'),
			'special' => $request->getParam('special'),
			'image' => $request->getParam('product_name').$fileExtension
			 
		]
    );
	return $response->withRedirect($this->router->pathFor('productList'));
}

public function editCategoryPost($request,$response)
{

	$categorylists = Capsule::table('category')->where('id', $request->getParam('id'))->update(
		['category_name' => $request->getParam('name') ,'slug' => $request->getParam('slug') ]
       
    );
	return $response->withRedirect($this->router->pathFor('categoryList'));
}

public function editProductPost($request,$response)
{

	$productLists = Capsule::table('product')->where('id', $request->getParam('id'))->update(
		[	'product_name' => $request->getParam('product_name') ,
			'brand_name' => $request->getParam('brand_name'),
			'quantity' => $request->getParam('quantity'),
			'stock' => $request->getParam('stock'),
			'price' => $request->getParam('price'),
			'offer_price' => $request->getParam('offer_price'),
			'description' => $request->getParam('description'),
			'tag' => $request->getParam('tag'),
			'rating' => $request->getParam('rating'),
			'special' => $request->getParam('special')
		]
       
    );
	return $response->withRedirect($this->router->pathFor('productList'));
}



public function editProduct($request,$response)
{
	$editProduct = json_decode($request->getParam('editProduct'));
	
	return $this->view->render($response,'admin/editProduct.html',["editProduct"=>$editProduct]);
}


public function editCategory($request,$response)
{
	$editCategory = json_decode($request->getParam('editCategory'));
	
	return $this->view->render($response,'admin/editCategory.html',["editCategory"=>$editCategory]);
}

public function deleteCategory($request,$response)
{	
	Capsule::table('category')->where('id', '=', $request->getParam('deleteCategory'))->delete();
	return $response->withRedirect($this->router->pathFor('categoryList'));
}

public function deleteProduct($request,$response)
{	
	Capsule::table('product')->where('id', '=', $request->getParam('deleteProduct'))->delete();
	return $response->withRedirect($this->router->pathFor('productList'));
}

public function allCategory($request,$response)
{
	$categoryList = Capsule::table('category')->get();
	return $this->view->render($response,'admin/categoryList.html',["categoryLists"=>$categoryList]);
}


public function addNewCategory($request,$response)
{
	return $this->view->render($response,'admin/addCategory.html');	
}

public function addNewProduct($request,$response)
{
	$categoryList = Capsule::table('category')->get();
	return $this->view->render($response,'admin/addProduct.html',["categoryLists"=>$categoryList]);	
}

public function allProducts($request,$response)
{
	$productList = Capsule::table('product')->get();
	return $this->view->render($response,'admin/product.html',["productLists"=>$productList]);
}

public function checkout($request,$response)
	{
		$checkout = $this->auth->checkoutApi(
			$request->getParam('business'),
			$request->getParam('quantity'),
			$request->getParam('discount_amount'),
			$request->getParam('item_name'),
			$request->getParam('amount'),
			$request->getParam('total'));
		
		return $this->view->render($response,'checkout.html',["checkout"=>$checkout]);	 
	}

// public function postAddressData($request,$response)
// {
// 	$OrderedDate = Carbon::now();
// 	$order = array(
// 		'address' => $request->getParam('town') ,
//         'username' => $request->getParam('name'),
//         'contact' => $request->getParam('mobile'),
//         'zip' => $request->getParam('postcode'),
//         'city' => $request->getParam('town'),
//         'OrderedDate' => $OrderedDate->toDateTimeString()
//     );

// 	$orderid = Capsule::table('orders')->insertGetId($order);
// 	$checkout = $this->auth->checkoutApi($request->getParam('business'),$request->getParam('quantity'),$request->getParam('discount_amount'),$request->getParam('item_name'),$request->getParam('amount'),$request->getParam('total'));
// 	var_dump($checkout);
// 	die();
//     return $response->withRedirect($this->router->pathFor('payment'));	 
// }	

public function paymentPost($request,$response)
{
	

	if ($_SESSION['userId']) {
		$categorylists = Capsule::table('orders')->updateOrInsert(
		[
			'name' => $request->getParam('name'),
			'product_item' => $request->getParam('item_name'),
			'quantity' => $request->getParam('quantity'),
			'product_amount' => $request->getParam('item_amount'),
			'total' => $request->getParam('total'),
			'number' => $request->getParam('number'),
			'landmark' => $request->getParam('landmark'),
			'address' => $request->getParam('address'),
			'town' => $request->getParam('town'),
			'User_Id' => $_SESSION['userId']
			
		]
	);
		$b = json_decode($request->getParam('item_name'));
		$a = json_decode($request->getParam('quantity'));
		 $c = array_combine($b,$a);
		 $d=array_map('intval', $c);
		 	foreach ($d as $key => $value) {
			 $e = Capsule::table('product')->where('product_name',"=",$key)->decrement('stock',$value);
		 }
		
	   $this->flash->addMessage('success','Your Order has been received and is now being processed. Your order details are shown below for your refernce: ');
	   
    	return $response->withRedirect($this->router->pathFor('payment'));
	}
	else
	{
		$this->flash->addMessage('error','Please login to confirm your order.');

    	return $response->withRedirect($this->router->pathFor('loginPage'));
	}

}

public function postAddressData($request,$response)
{
	$OrderedDate = Carbon::now();
	$order = array(
		'address' => $request->getParam('town') ,
        'username' => $request->getParam('name'),
        'contact' => $request->getParam('mobile'),
        'zip' => $request->getParam('postcode'),
        'city' => $request->getParam('town'),
        'OrderedDate' => $OrderedDate->toDateTimeString()
    );

	$orderid = Capsule::table('orders')->insertGetId($order);
	$checkout = $this->auth->checkoutApi($request->getParam('business'),$request->getParam('quantity'),$request->getParam('discount_amount'),$request->getParam('item_name'),$request->getParam('amount'),$request->getParam('total'));
	var_dump($checkout);
	die();
    return $response->withRedirect($this->router->pathFor('payment'));	 
}	

// public function payment($request,$response,$args)
// {
// 	$name= $request->getParam('product');
// 	return $this->view->render($response,'payment.html');	 
// }

public function payment($request,$response)
{
	$payments = $this->auth->paymentApi($_SESSION['userId']);
	return $this->view->render($response,'payment.html',["payments"=>$payments]);
}

public function deleteOrder($request,$response)
{	
	$orderDelete = Capsule::table('orders')->where('order_id', '=', $request->getParam('deleteOrder'))->delete();
	return $response->withRedirect($this->router->pathFor('myorder'));
}

}
?>
