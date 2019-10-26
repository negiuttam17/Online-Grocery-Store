<?php
use Slim\Views\Twig as View;


class HomeController extends Controller
{

public function index($request ,$response)
	{
		// $homefeedlist =$this->authUser->homefeedlist();
		// $userlist =$this->auth->userlist1();
		// $merchant=$this->auth->merchant1();
		// $feeds =$this->auth->feedlist1();
		// $merchant1=$this->auth->reportfeedback();
		// $events =$this->auth->events1();
		// $allTopUpLog=$this->auth->allTopUplog1();

			return $this->view->render($response,'admin/index.html',["homefeedlist"=>$service_url,"userlist"=>$userlist,"merchant"=>$merchant,"feeds"=>$feeds,"merchant1"=>$merchant1,"events"=>$events,"allTopUpLog"=>$allTopUpLog]);

	}




}