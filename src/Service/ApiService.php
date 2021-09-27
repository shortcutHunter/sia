<?php

namespace App\Service;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\PhpRenderer;

final class ApiService
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

// =============================================================================================================================
    // Template
    public function tableTemplate(ServerRequestInterface $request, ResponseInterface $response, $args)
   {
       $model = $args['model'];
       $container = $this->container;
       return $container->get('renderer')->render($response, 'models/'.$model.'/table.template.phtml');
   }

   public function detailTemplate(ServerRequestInterface $request, ResponseInterface $response, $args)
   {
       $model = $args['model'];
       $container = $this->container;
       return $container->get('renderer')->render($response, 'models/'.$model.'/detail.template.phtml');
   }

   public function formTemplate(ServerRequestInterface $request, ResponseInterface $response, $args)
   {
       $model = $args['model'];
       $container = $this->container;
       return $container->get('renderer')->render($response, 'models/'.$model.'/form.template.phtml');
   }

// =============================================================================================================================
    // GET
    public function get(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {   
        $getData = $request->getQueryParams();
        $model_name = $args['model'];
        $model = $this->container->get('getModel')($model_name);

        if (isset($getData['keyword'])) {
            $model->get([], $getData['keyword']);
        }else{
            $model->get();
        }
        
        $data = $model->data->toJson();

        $response->getBody()->write($data);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function detail(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $id = $args['id'];
        $model_name = $args['model'];
        $model = $this->container->get('getModel')($model_name);
        $model->read($id);
        $data = $model->data;

        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function selection(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $id = $args['id'];
        $model_name = $args['model'];
        $model = $this->container->get('getModel')($model_name);
        $selection = $model->selection;

        $response->getBody()->write(json_encode($selection));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

// =============================================================================================================================
    // POST

    public function add(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $result = ['success' => true];
        $model_name = $args['model'];
        $model = $this->container->get('getModel')($model_name);
        $postData = $request->getParsedBody();

        try {
            $model->create($postData);
            $result['id'] = $model->data->id;
        } catch (Exception $e) {
            $result['success'] = false;
            $result['msg'] = "Terjadi kesalahan dalam mengubah data ini. Mohon hubungi sistem administrator.";
        }
        
        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function update(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $result = ['success' => true];
        $model_name = $args['model'];
        $model = $this->container->get('getModel')($model_name);
        $id = $args['id'];
        $postData = $request->getParsedBody();

        try {
            $model->update($id, $postData);
            $result['data'] = $model->data;
        } catch (Exception $e) {
            $result['success'] = false;
            $result['msg'] = "Terjadi kesalahan dalam mengubah data ini. Mohon hubungi sistem administrator.";
        }
        
        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function delete(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $result = ['success' => true];
        $model_name = $args['model'];
        $model = $this->container->get('getModel')($model_name);
        $id = $args['id'];

        try {
            $model->delete($id);
        } catch (Exception $e) {
            $result['success'] = false;
            $result['msg'] = "Terjadi kesalahan dalam menghapus data ini. Mohon hubungi sistem administrator.";
        }

        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }


// =============================================================================================================================
    // File
    public function upload(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $result = ['success' => true];
        $model_name = $args['model'];
        $model = $this->container->get('getModel')($model_name);
        $id = $args['id'];
        $postData = $request->getParsedBody();
        $myFiles = $request->getUploadedFiles();
        
        $model->read($id);
        $data = $model->data;

        try {
            if ($postData['model']) {
                $record_id = $data->{$postData['model']}->id;
                $model_name = $postData['model'];
                $model = $this->container->get('getModel')($model_name);
            }else{
                $record_id = $id;
            }
            foreach ($myFiles as $key => $value) {
                if ($value) {
                    $file_dir = null;
                    $file_dir = $model_name."/".$record_id;
                    $filename = $this->container->get('moveUploadedFile')($file_dir, $value, $value->getClientFilename());
                }
            }
        } catch (Exception $e) {
            $result['success'] = false;
            $result['msg'] = "Terjadi kesalahan dalam menghapus data ini. Mohon hubungi sistem administrator.";
        }

        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

// =============================================================================================================================
}
