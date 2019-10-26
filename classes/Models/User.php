<?php


use Illuminate\Database\Eloquent\Model;

class User extends Model
{
	// protected $table = 'notification';
	protected $user = 'users';

		protected $fillable =[
			'User_Id',
			'Name',
			'Email',
			'Password',
			'Mobile',
			'Address',
			'created_at',
			'updated_at'
			
		];



}