<?php

namespace App\Controllers;

use App\Controllers\BaseController;

final class TemplateController extends BaseController
{
    public function getKonfiguration()
    {
        $konfigurasi = $this->get_object('konfigurasi')->first();

        return $konfigurasi;
    }

    public function isRegister()
    {
        $pendaftaran_obj = $this->get_object('pendaftaran');

        $konfigurasi = $this->getKonfiguration();
        $register = false;

        if ($konfigurasi->registrasi) {
            $pendaftaran = $pendaftaran_obj->where('status', 'open')->first();

            if (!empty($pendaftaran)) {
                $register = true;
            }
        }

        return $register;
    }

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
            return $response->withHeader('Location', '/home');
        }
        return $container->get('twig')->render($response, 'template/template.twig');
    }

    public function login($request, $response)
    {
        $container = $this->container;

        if ($container->get('session')->get('user')) {
            return $response->withHeader('Location', '/');
        }

        return $container->get('twig')->render($response, 'root/login.twig', ['noMenu' => true]);
    }

    public function lupaPassword($request, $response)
    {
        $container = $this->container;

        if ($container->get('session')->get('user')) {
            return $response->withHeader('Location', '/');
        }

        return $container->get('twig')->render($response, 'root/lupa_password.twig', ['noMenu' => true]);
    }

    public function resetPassword($request, $response)
    {
        $container = $this->container;
        $user_obj = $this->get_object('user');
        $getData = $request->getQueryParams();

        if ($container->get('session')->get('user')) {
            return $response->withHeader('Location', '/');
        }

        $token = $getData['token'];
        $user = $user_obj->where('token', $token)->count();

        if ($user == 0) {
            return $response->withHeader('Location', '/reset/password/gagal');
        }

        return $container->get('twig')->render($response, 'root/reset_password.twig', ['noMenu' => true, 'token' => $token]);
    }

    public function homePage($request, $response)
    {
        $data = [
            'register' => $this->isRegister()
        ];

        $container = $this->container;
        return $container->get('twig')->render($response, 'root/home.twig', $data);
    }

    public function loginGagal($request, $response)
    {
        $container = $this->container;

        return $container->get('twig')->render($response, 'root/login_gagal.twig', ['noMenu' => true]);
    }

    public function lupaPasswordGagal($request, $response)
    {
        $container = $this->container;

        return $container->get('twig')->render($response, 'root/lupa_password_gagal.twig', ['noMenu' => true]);
    }

    public function lupaPasswordBerhasil($request, $response)
    {
        $container = $this->container;

        return $container->get('twig')->render($response, 'root/lupa_password_berhasil.twig', ['noMenu' => true]);
    }

    public function resetPasswordGagal($request, $response)
    {
        $container = $this->container;

        return $container->get('twig')->render($response, 'root/reset_password_gagal.twig', ['noMenu' => true]);
    }

    public function resetPasswordBerhasil($request, $response)
    {
        $container = $this->container;

        return $container->get('twig')->render($response, 'root/reset_password_berhasil.twig', ['noMenu' => true]);
    }
}
