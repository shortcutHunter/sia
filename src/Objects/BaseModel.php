<?php

namespace App\Objects;

use Illuminate\Database\Eloquent\Model;
use App\Objects\Session;
use Spipu\Html2Pdf\Html2Pdf;
use \Slim\Views\PhpRenderer;

class BaseModel extends Model
{
	public $timestamps = false;
	public $like_fields = [];
	protected $guarded = [];

	public $selection_fields = [];
	public static $relation = [];

	public static $file_fields = [];
	public static $date_fields = [];

	public $session;


	public function __construct(array $attributes = array())
	{
		parent::__construct($attributes);
		$this->session = new Session();
	}

	public static function nextCode($kode_name) {
		$sequance_obj = self::getModelByName('sequance');
		$kode = $sequance_obj->getnextCode($kode_name);
		return $kode;
	}

	public static function getModelByName($name)
	{
		$class_name = join("", array_map("ucfirst", explode("_", $name)));
        $class_name = "App\\Objects\\".$class_name;
        $model = new $class_name();
        return $model;
	}

	public static function renderHtml($template_name, $value) {
		$settings = require __DIR__ . '/../../config/settings.php';
		$renderer = new PhpRenderer($settings['template_path']);
		$rendered_template = $renderer->fetch($template_name, $value);
		return $rendered_template;
	}

	public function renderPdf($template_name, $value) {
		$html2pdf = new Html2Pdf();
		$rendered_template = self::renderHtml($template_name, $value);
        $html2pdf->writeHTML($rendered_template);
        $pdfContent = $html2pdf->output('my_doc.pdf', 'S');
        return $pdfContent;
	}

	public static function relationName()
	{
		$relation_name = [];
		foreach (static::$relation as $key => $value) {
			if (!$value['is_selection']) {
				array_push($relation_name, $value['name']);
			}			
		}
		return $relation_name;
	}

	public static function create(array $attributes = [])
	{
		$relation_name = static::$relation;

		foreach ($relation_name as $key => $value) {
			$relation_model_name = $value['name'];

			if ($value['is_selection'] || $value['skip']) {
				unset($attributes[$relation_model_name]);
			}else{
				if (array_key_exists($relation_model_name, $attributes)) {
					$relation_value = $attributes[$relation_model_name];
					$object_relation = self::getModelByName($relation_model_name);

					$object_relation = $object_relation->create($relation_value);
					$attributes[$relation_model_name."_id"] = $object_relation->id;
					unset($attributes[$relation_model_name]);
				}
			}
		}

		foreach (static::$file_fields as $value) {
			if (array_key_exists($value, $attributes)) {
				$file_data_att = $attributes[$value];
				$object_relation = self::getModelByName('file');
				$file_data_value = [
					'name' => $value,
					'filename' => $file_data_att['filename'],
					'filetype' => $file_data_att['filetype'],
					'base64' => $file_data_att['base64']
				];
				$object_relation_created = $object_relation->create($file_data_value);
				$attributes[$value."_id"] = $object_relation_created->id;
				unset($attributes[$value]);
			}
		}

		foreach (static::$date_fields as $value) {
            if (array_key_exists($value, $attributes)) {
                $attributes[$value] = \DateTime::createFromFormat('d/m/Y', $attributes[$value])->format('Y-m-d');
            }
        }

	    $model = static::query()->create($attributes);
	    return $model;
	}

	public function update(array $attributes = [], array $options = [])
	{
		$relation_name = static::$relation;
		$file_fields = static::$file_fields;

		foreach ($relation_name as $key => $value) {
			$relation_model_name = $value['name'];

			if ($value['is_selection'] || $value['skip']) {
				unset($attributes[$relation_model_name]);
			}else{

				if (array_key_exists($relation_model_name, $attributes)) {
					$relation_value = $attributes[$relation_model_name];
					$object_relation = self::getModelByName($relation_model_name);
					$object_relation_id = $relation_value['id'];
					unset($relation_value['id']);

					$object_relation = $object_relation->find($object_relation_id)->update($relation_value);
					unset($attributes[$relation_model_name]);
				}

			}
		}

		foreach ($file_fields as $value) {
			if (array_key_exists($value, $attributes)) {
				$file_data_att = $attributes[$value];

				if (!$file_data_att) {
					unset($attributes[$value]);
					continue;
				}

				$file_data_value = [
					'name' => $value,
					'filename' => $file_data_att['filename'],
					'filetype' => $file_data_att['filetype'],
					'base64' => $file_data_att['base64']
				];

				$file_data = $this->{$value};

				if ($file_data) {
					$file_data->update($file_data_value);
				}else{
					$object_relation = self::getModelByName('file');
					$object_relation_created = $object_relation->create($file_data_value);
					$attributes[$value."_id"] = $object_relation_created->id;
				}
				unset($attributes[$value]);
			}
		}

		foreach (static::$date_fields as $value) {
            if (array_key_exists($value, $attributes)) {
            	if ($attributes[$value]) {
            		$attributes[$value] = \DateTime::createFromFormat('d/m/Y', $attributes[$value])->format('Y-m-d');
            	}
            }
        }

	    $model = parent::update($attributes, $options);
	    return $model;
	}
}