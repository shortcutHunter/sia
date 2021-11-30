<?php

namespace App\Objects;

use App\Objects\BaseModel;

class User extends BaseModel
{
	protected $table = 'user';

	public $role_enum = [
		"mahasiswa" => "Mahasiswa",
		"dosen" => "Dosen",
		"keuangan" => "Keuangan",
		"akademik" => "Akademik",
		"admin" => "Admin",
	];

	public function orang()
	{
		return $this->hasOne(Orang::class, 'id', 'orang_id');
	}

	// Custom function

	public static function encrypt($value)
	{
		$value = password_hash($value, PASSWORD_DEFAULT);
		return $value;
	}

	public function verify($value, $encrypted)
	{
		return password_verify($value, $encrypted);
	}

	public function authenticate($username, $password)
	{
		$user = self::where('username', $username);
		if (!$user) {
			return false;
		}else{
			$user = $user->first();
			$encrypted = $user->password;
			$verify = $this->verify($password, $encrypted);

			if ($verify) {
				// remove password from collection
				$user->offsetUnset('password');
				$user->offsetUnset('unenpass');
				$this->session->set('user', $user);
				$this->session->set('orang', $user->orang);
			}
			return $verify;
		}
	}

	public function logout()
	{
		$this->session->killAll();
	}

	public static function create(array $attributes = [])
	{
		if (!array_key_exists('password', $attributes)) {
			$created_pass = bin2hex(random_bytes(4));
			$attributes['password'] = $created_pass;
		}
		$attributes['unenpass'] = $attributes['password'];
		$attributes['password'] = self::encrypt($attributes['password']);

		if (!array_key_exists('username', $attributes)) {
			$username = bin2hex(random_bytes(3));
			$attributes['username'] = $username;
		}
		$model = parent::create($attributes);
		return $model;
	}

	// public static function boot()
	// {
	// 	parent::boot();

	// 	self::created(function(User $user){
	// 		if (!$user->password) {
	// 			$created_pass = bin2hex(random_bytes(8));
	// 			$user->update(['password' => $this->encrypt($created_pass), 'unenpass' => $created_pass]);
	// 		}
	// 		if (!$user->username) {
	// 			$username = bin2hex(random_bytes(6));
	// 			$user->update(['username' => $username]);
	// 		}
	// 	});
	// }

}