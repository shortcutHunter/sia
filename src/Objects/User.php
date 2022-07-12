<?php

namespace App\Objects;

use App\Objects\BaseModel;

class User extends BaseModel
{
	protected $table = 'user';
	protected $with  = ['role', 'orang'];

	// public $selection_fields = ['role'];

	public static $relation = [
		['name' => 'role', 'is_selection' => true, 'skip' => true],
		['name' => 'orang', 'is_selection' => false, 'skip' => true],
	];

	// public $role_enum = [
	// 	"mahasiswa" => "Mahasiswa",
	// 	"dosen" => "Dosen",
	// 	"keuangan" => "Keuangan",
	// 	"akademik" => "Akademik",
	// 	"admin" => "Admin",
	// 	"panitia" => "Panitia"
	// ];

	public function generateToken()
	{
		$token = bin2hex(openssl_random_pseudo_bytes(8));
		$this->update(['token' => $token]);
		return $token;
	}

	public function orang()
	{
		return $this->hasOne(Orang::class, 'id', 'orang_id');
	}

	public function role()
	{
		return $this->belongsToMany(Role::class, 'user_role', 'user_id', 'role_id');
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

	public function createRole($roles)
	{
		$created_user_role_ids = [];

		foreach ($roles as $key => $value) {
			$user_role_obj = self::getModelByName('user_role');
			$user_role_value = [
				'role_id' => $value['id'],
				'user_id' => $this->id
			];

			$user_role = $user_role_obj->where([
				['role_id', $value['id']],
				['user_id', $this->id]
			]);

			if ($user_role->count() == 0) {
				$user_role = $user_role_obj->create($user_role_value);
			} else {
				$user_role = $user_role->first();
			}

			array_push($created_user_role_ids, $user_role->id);
		}


		$user_role_obj = self::getModelByName('user_role');
		$user_role = $user_role_obj->where('user_id', $this->id)->whereNotIn('id', $created_user_role_ids);
		$user_role->delete();
	}

	public static function create(array $attributes = [])
	{
		$role = false;

		if (array_key_exists('role', $attributes)) {
			$role = $attributes['role'];
			unset($attributes['role']);
		}

		if (array_key_exists('orang_id', $attributes) && $attributes['orang_id'] != '') {
			$object_orang = self::getModelByName('user');
			$orang = $object_orang->find($attributes['orang_id']);

			if (!empty($orang)) {
				$attributes['username'] = $orang->nik || '';

				if (!empty($orang->tanggal_lahir)) {
					$attributes['password'] = date('dmY', strtotime($orang->tanggal_lahir));
				}
			}
		}

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



		if ($role) {
			$model->createRole($role);
		}

		return $model;
	}

	public function update(array $attributes = [], array $options = [])
	{		
		if (array_key_exists('role', $attributes)) {
			$role = $attributes['role'];
			$this->createRole($role);

			unset($attributes['role']);
		}

		if (array_key_exists('pass', $attributes)) {
			$attributes['password'] = self::encrypt($attributes['pass']);
			unset($attributes['pass']);
		}

		return parent::update($attributes, $options);
	}
}