<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Models\SessionModel;
use Psr\Container\ContainerInterface;

final class UserModel extends BaseModel
{
  protected $table_name = "user";
  protected $relation = ['orang'];

  public function __construct(ContainerInterface $container)
  {
    parent::__construct($container);
    $this->session = new SessionModel();
  }

  public function encrypt($value)
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
    $this->get([['username', $username]]);
    if ($this->data->isEmpty()) {
      return false;
    }else{
      $user_data = $this->data[0];
      $encrypted = $user_data->password;
      $verify = $this->verify($password, $encrypted);

      if ($verify) {
        $this->session->set('user', $user_data);
      }

      return $verify;
    }
  }

  public function create($value)
  {
    if (!array_key_exists("password", $value)) {
      $value['password'] = bin2hex(random_bytes(8)); // 10 characters, only 0-9a-f;
    }
    if (!array_key_exists("username", $value)) {
      $date = new \DateTime();
      $timespan = $date->getTimestamp();
      $value['username'] = substr($timespan, (count($timespan)-3)).bin2hex(random_bytes(3));
    }
    $value['unenpass'] = $value['password'];
    $value['password'] = $this->encrypt($value['password']);
    parent::create($value);
  }

  public function update($id, $value)
  {
    $value['password'] = $this->encrypt($value['password']);
    $this->model->where('id', $id)->update($value);
    $this->read($id);
  }
}