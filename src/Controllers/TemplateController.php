<?php

namespace App\Controllers;

use App\Controllers\BaseController;

final class TemplateController extends BaseController
{

    public function table($request, $response, $args)
    {
        $object    = $args['object'];
        $container = $this->container;
        return $container->get('twig')->render($response, 'pages/'.$object.'/table.twig');
    }

    public function detail($request, $response, $args)
    {
        $object    = $args['object'];
        $container = $this->container;
        return $container->get('twig')->render($response, 'pages/'.$object.'/detail.twig');
    }

    public function form($request, $response, $args)
    {
        $object    = $args['object'];
        $container = $this->container;
        return $container->get('twig')->render($response, 'pages/'.$object.'/form.twig');
    }

    public function dynamicTemplate($request, $response, $args)
    {
        $folder_name = $args['folder_name'];
        $file        = $args['file'];
        $container = $this->container;
        return $container->get('twig')->render($response, $folder_name.'/'.$file.'.twig');
    }

    public function component($request, $response, $args)
    {
        $component_name = $args['component_name'];
        $container      = $this->container;
        return $container->get('twig')->render($response, 'pages/components/'.$component_name.'.twig');
    }

    public function menu($request, $response, $args)
    {
        $container = $this->container;
        return $container->get('twig')->render($response, 'pages/components/menu.twig');
    }

    public function home($request, $response)
    {
        $container = $this->container;
        if (!$container->get('session')->get('user')) {
            return $response->withHeader('Location', '/login');
        }
        // return $container->get('twig')->render($response, 'root/admin.twig');
        return $container->get('twig')->render($response, 'template/template.twig');
    }

    public function login($request, $response)
    {
        $container = $this->container;

        if ($container->get('session')->get('user')) {
            return $response->withHeader('Location', '/');
        }

        return $container->get('twig')->render($response, 'root/login.twig');
    }

    public function register($request, $response)
    {
        $container = $this->container;
        $object = $this->get_object('orang');

        $data = [
            'option'    => [],
            'selection' => [],
        ];

        foreach ($object->selection_fields as $value) {
            $data['option'][$value] = $object->{$value."_enum"};
        }

        foreach ($object::$relation as $model) {
            $model_name = $model['name'];
            $object_relation = $this->get_object($model['name']);
            $data[$model_name] = [
                'option'    => [],
                'selection' => [],
            ];

            if ($model['is_selection']) {
                $data['selection'][$model['name']] = $object_relation->all();
            }else{
                foreach ($object_relation->selection_fields as $value) {
                    $data[$model_name]['option'][$value] = $object_relation->{$value."_enum"};
                }
                
                foreach ($object_relation::$relation as $value) {
                    if ($value['is_selection']) {
                        $object_relation_selection = $this->get_object($value['name']);
                        $data[$model_name]['selection'][$value['name']] = $object_relation_selection->all();
                    }
                }
            }
        }

        return $container->get('twig')->render($response, 'root/register.twig', $data);
    }

    public function registerSubmit($request, $response)
    {
        $container = $this->container;
        $postData = $request->getParsedBody();
        $postFile = $request->getUploadedFiles();
        $orang_obj = $this->get_object('orang');
        $pmb_obj = $this->get_object('pmb');

        $orang_value = [
            'nik' => $postData['nik'],
            'nama' => $postData['nama'],
            'tempat_lahir' => $postData['tempat_lahir'],
            'tanggal_lahir' => $postData['tanggal_lahir'],
            'jenis_kelamin' => $postData['jenis_kelamin'],
            'alamat' => $postData['alamat'],
            'agama_id' => $postData['agama_id'],
            'email' => $postData['email'],
            'no_hp' => $postData['no_hp'],
            'nama_ayah' => $postData['nama_ayah'],
            'pekerjaan_ayah' => $postData['pekerjaan_ayah'],
            'nama_ibu' => $postData['nama_ibu'],
            'pekerjaan_ibu' => $postData['pekerjaan_ibu'],
            'penghasilan_ortu' => $postData['penghasilan_ortu'],
            'asal_sekolah' => $postData['asal_sekolah'],
            'jurusan' => $postData['jurusan'],
            'tinggi_badan' => $postData['tinggi_badan'],
            'berat_badan' => $postData['berat_badan'],

            'pasfoto' => [
                'filename' => $postFile['pasfoto']->getClientFilename(),
                'filetype' => $postFile['pasfoto']->getClientMediaType(),
                'base64' => base64_encode(file_get_contents($postFile['pasfoto']->getFilePath())),
            ],
            'ijazah' => [
                'filename' => $postFile['ijazah']->getClientFilename(),
                'filetype' => $postFile['ijazah']->getClientMediaType(),
                'base64' => base64_encode(file_get_contents($postFile['ijazah']->getFilePath())),
            ],
            'ktp' => [
                'filename' => $postFile['ktp']->getClientFilename(),
                'filetype' => $postFile['ktp']->getClientMediaType(),
                'base64' => base64_encode(file_get_contents($postFile['ktp']->getFilePath())),
            ],
            'surket_menikah' => [
                'filename' => $postFile['surket_menikah']->getClientFilename(),
                'filetype' => $postFile['surket_menikah']->getClientMediaType(),
                'base64' => base64_encode(file_get_contents($postFile['surket_menikah']->getFilePath())),
            ],
        ];
        $orang = $orang_obj->create($orang_value);

        $pmb_value = [
            'orang_id' => $orang->id,
            'bukti_pembayaran' => [
                'filename' => $postFile['bukti_pembayaran']->getClientFilename(),
                'filetype' => $postFile['bukti_pembayaran']->getClientMediaType(),
                'base64' => base64_encode(file_get_contents($postFile['bukti_pembayaran']->getFilePath())),
            ],
            'pernyataan' => true
        ];
        $pmb = $pmb_obj->create($pmb_value);

        return $response->withHeader('Location', '/register/sukses');
    }


    public function sukses($request, $response)
    {
        $container = $this->container;
        return $container->get('twig')->render($response, 'root/register_sukses.twig');
    }

    public function newTemplate($request, $response)
    {
        $container = $this->container;
        return $container->get('twig')->render($response, 'root/template.twig');
    }

}
