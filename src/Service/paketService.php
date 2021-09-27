<?php

namespace App\Service;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\PhpRenderer;
use App\Models\PaketModel;
use App\Models\ItemModel;
use App\Models\KonfigurasiModel;

final class paketService
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->paket = new PaketModel($container);
        $this->item = new ItemModel($container);
        $this->konfigurasi = new KonfigurasiModel($container);
    }

    public function paketItem(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {   
        $id = $args['id'];
        $item = $this->item;

        $item->get([['paket_id', $id]]);
        $data = $item->data->toJson();

        $response->getBody()->write($data);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function paketTambah(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {   
        $result = ['success' => true];
        $paket = $this->paket;
        $item = $this->item;
        $postData = $request->getParsedBody();

        try {
            $items = $postData['items'];
            unset($postData['items']);
            $paket->create($postData);
            $paket_id = $paket->data->id;

            foreach ($items as $itm) {
                $item_value = [
                    "nama" => $itm['nama'],
                    "kode" => $itm['kode'],
                    "nominal" => $itm['nominal'],
                    "paket_id" => $paket_id,
                ];
                $item->create($item_value);
            }
            $result['id'] = $paket_id;
        } catch (Exception $e) {
            $result['success'] = false;
            $result['msg'] = "Terjadi kesalahan dalam mengubah data ini. Mohon hubungi sistem administrator.";
        }
        
        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function paketEdit(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {   
        $result = ['success' => true];
        $paket = $this->paket;
        $item = $this->item;
        $id = $args['id'];
        $postData = $request->getParsedBody();

        try {
            $items = $postData['items'];
            $deleted = $postData['deleted'];

            unset($postData['items']);
            unset($postData['deleted']);
            
            $paket->update($id, $postData);

            foreach ($items as $itm) {
                $item_value = [
                    "nama" => $itm['nama'],
                    "kode" => $itm['kode'],
                    "nominal" => $itm['nominal'],
                    "paket_id" => $id,
                ];
                if (array_key_exists('id', $itm)) {
                    $item->update($itm['id'], $item_value);
                }else{
                    $item->create($item_value);
                }
            }

            foreach ($deleted as $value) {
                $item->delete($value);
            }

            $result['id'] = $paket_id;
        } catch (Exception $e) {
            $result['success'] = false;
            $result['msg'] = "Terjadi kesalahan dalam mengubah data ini. Mohon hubungi sistem administrator.";
        }
        
        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function paketKonfigurasi(ServerRequestInterface $request, ResponseInterface $response)
    {   
        $konfigurasi = $this->konfigurasi;
        $konfigurasi->get();

        if ($konfigurasi->data->isEmpty()) {
            $konfigurasi->create([]);
            $data = $konfigurasi->data;
        }else{
            $data = $konfigurasi->data[0];
        }

        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function updatePaketKonfigurasi(ServerRequestInterface $request, ResponseInterface $response)
    {   
        $result = ['success' => true];
        $konfigurasi = $this->konfigurasi;
        $postData = $request->getParsedBody();

        try {

            $konfigurasi->update(1, $postData);

            foreach ($deleted as $value) {
                $item->delete($value);
            }

            $result['id'] = $paket_id;
        } catch (Exception $e) {
            $result['success'] = false;
            $result['msg'] = "Terjadi kesalahan dalam mengubah data ini. Mohon hubungi sistem administrator.";
        }
        
        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

}
