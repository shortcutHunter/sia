<?php

namespace App\Models;

use Psr\Container\ContainerInterface;

class BaseModel
{
  private $model;

  protected $container;
  protected $relation;
  protected $table_name;

  public $data;
  public $selection;
  public $search_field = 'nama';
  
  public function __construct(ContainerInterface $container)
  {
    $this->container = $container;
    $this->model = $container->get('db')->table($this->table_name);
    $this->selection();
  }

  public function selection()
  {
    $this->selection = [];
  }

  public function selectionField($data)
  {
    foreach ($this->selection as $key => $value) 
    {
      $data->{$key."_label"} = $value[$data->{$key}] ?: NULL;
    }
    return $data;
  }

  public function relationField($data)
  {
    foreach ($this->relation as $value) {
      $field_name = $value."_id";
      // $class_name = array_map("ucfirst", explode("_", $value));
      // $class_name = "App\\Models\\".join("", $class_name)."Model";
      // $relation_model = new $class_name($this->container);
      $relation_model = $this->container->get('getModel')($value);

      $relation_model->read($data->{$field_name});
      $data->{$value} = $relation_model->data;
    }
    return $data;
  }

  public function updateField(&$data)
  {
    $data = $this->selectionField($data);
    $data = $this->relationField($data);
  }

  public function read($id)
  {
    $selection = $this->selection;
    $model = clone $this->model;

    $data = $model->where('id', $id)->first();
    $this->updateField($data);
    $this->data = $data;
  }

  public function get($query=[], $search=false)
  {
    $selection = $this->selection;
    $model = clone $this->model;

    if ($search) {
      $data = $model->where($this->search_field, 'like', '%'.$search.'%')->get();
    }else{
      if (count($query) == 0) {
        $data = $model->get();
      }else{
        $data = $model->where($query)->get();
      }
    }

    foreach ($data as $k => $v)
    {
      $this->updateField($data[$k]);
    }
    $this->data = $data;
  }

  public function raw($query)
  {
    $model = clone $this->model;
    $data = $model->whereRaw($query)->get();
    foreach ($data as $k => $v)
    {
      $this->updateField($data[$k]);
    }
    $this->data = $data;
  }

  public function removeLabel($value)
  {
    $result = $value;

    // remove selection label
    foreach ($value as $key => $value) {
      if (preg_match("/_label/i", $key)) {
        unset($result[$key]);
      }
    }

    // remove relation value
    foreach ($this->relation as $value) {
      unset($result[$value]);
    }

    return $result;
  }

  public function create($value)
  {
    $model = clone $this->model;
    $value = $this->removeLabel($value);
    $id = $model->insertGetId($value);
    $this->read($id);
  }

  public function update($id, $value)
  {
    $model = clone $this->model;
    $value = $this->removeLabel($value);
    $model->where('id', $id)->update($value);
    $this->read($id);
  }

  public function massUpdate($ids, $value)
  {
    $model = clone $this->model;
    $value = $this->removeLabel($value);
    $this->model->whereIn('id', $ids)->update($value);
  }

  public function delete($id)
  {
    $model = clone $this->model;
    $id = $model->where('id', $id)->delete();
    return true;
  }

}
