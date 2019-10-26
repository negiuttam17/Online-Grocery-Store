<?php
use Slim\Views\Twig as View;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

global $global;

use Illuminate\Database\Capsule\Manager as Capsule;

class Auth
{


	
	public function signUpPostApi($username,$password,$email,$address,$phone)
		{
			$user = User::create([
				'Email'=>$email,
				'Name'=>$username,
				'Address'=>$address,
				'Mobile'=>$phone,
				'Password'=> password_hash($password, PASSWORD_DEFAULT)
			]);
		}

public function checkoutApi($item_id,$item_number,$discount_amount,$item_name,$amount,$total)
	{
		$myObj1->item_id  = $item_id;
		$myObj1->item_number  = $item_number;
		$myObj1->discount_amount  =$discount_amount;
		$myObj1->item_name  =$item_name;	
		$myObj1->amount  =$amount;	
		$myObj1->total  =$total;	
		$myJSON = (object)($myObj1);
		return ($myObj1);
	}

	public function loginPostApi($email,$password)
		{
			$user = User::where('Email',$email)->first();
			$_SESSION['isAdmin'] = $user->isAdmin;
			$_SESSION['userId'] = $user->User_Id;
			if (!$user) {
				return false;
			}

			if(password_verify($password, $user->Password)){
				$_SESSION['user'] = $user->isAdmin;
				
				return true;
			}	
			return false;		             
		}
	public function paymentApi($id)
	{
		$payments = Capsule::table('orders')->where('User_Id', '=', $id )->get();		
		
		return $payments;
	}
	
	public function  bannerApi()
	{
		$banner = Capsule::table('banners')->get();

		return $banner;
	}

	public function  hotOfferApi()
	{
		$hotOffer = Capsule::table('product')
            ->join('category', 'product.category_id', '=', 'category.id')          
            ->where('product.special','=', 2)->select('product.*','category.slug','category.category_name')
            ->get();
		return $hotOffer;
	}

	public function hotOfferSinglePage($id)
	{
		$hotOffer = Capsule::table('product')->find($id);
		return $hotOffer;

	}

	public function changePasswordApi($email,$password,$newpassword)
	{
		 $capsule = new Capsule;
			Capsule::table('users')
						->where('Email',$email)
			->update(
			     ['Password'=>password_hash($newpassword, PASSWORD_DEFAULT),]
			    );
	}
	public function getSearchProducts($productname)
	{
		$products = Capsule::table('product')
            ->join('category', 'product.category_id', '=', 'category.id')          
            ->where('product.product_name','like', '%'.$productname.'%')->select('product.*','category.slug','category.category_name')
            ->get();
            return $products;
	}

	public function faqApi()
	{
		$faq = Capsule::table('faq')->get();
		return $faq;
	}


	public function bannerPageApi($name,$id)
	{
		$productPage = Capsule::table('banners')->find($id);
		return $productPage;
	}
	public function productPageApi($name,$id)
	{
		$productPage = Capsule::table('product')->find($id);
		return $productPage;
	}

	public function categoryListApi()
	{
		$categoryList = Capsule::table('category')->get();
		return $categoryList;	
	}

	public function socialIconApi()
	{
		$socialIcon = Capsule::table('social_icon')->get();
		return $socialIcon;	
	}

	private function sanitize_my_email($field) {
    $field = filter_var($field, FILTER_SANITIZE_EMAIL);
    if (filter_var($field, FILTER_VALIDATE_EMAIL)) {
        return true;
    } else {
        return false;
    }
}

	public function contactFormApi($name,$email,$telephone,$subject,$message)
	{
		
		$to_email = '';
		$subject = $email . '  ' . $subject;
		$message = $message;
		$headers = 'From: '.$email;
		//check if the email address is invalid $secure_check
		$secure_check = $this->sanitize_my_email($to_email);
		if ($secure_check == false) {
		    echo "Invalid input";
		} else { //send email 
		    mail($to_email, $subject, $message, $headers);		   
		}
		
	}


	public function aboutPageApi()
	{
		$aboutUs = Capsule::table('aboutUs')->get();
		return $aboutUs;
	}


public function checkSession()
{
	return isset($_SESSION['isAdmin']);
}

	
	public function check1()
		{			
			$allData= $_SESSION['isAdmin'];
			return $allData;
		}
	public function decoded()
		{		
			if($_SESSION['isAdmin'] == '1')
		{
			return true;
		}
		else
		{
			return false;
		}
		}

		
	public function page()
		{
			$page= $_SESSION['pagination'];
			return $page;
		}

	public function logout()
		{
			unset($_SESSION['user']);
			unset($_SESSION['isAdmin']);
			unset($_SESSION['user_Id']);			
		}
	
	public function uploadOne(UploadedFile $uploadedFile, $folder = null, $disk = 'public', $filename = null)
    {
        $name = !is_null($filename) ? $filename : str_random(25);

        $file = $uploadedFile->storeAs($folder, $name.'.'.$uploadedFile->getClientOriginalExtension(), $disk);

        return $file;
    }

public function moveUploadedFile($directory, UploadedFile $uploadedFile)
{
    $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
    $basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
    $filename = sprintf('%s.%0.8s', $basename, $extension);

    $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

    return $filename;
}
}
?>