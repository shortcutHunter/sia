<?php

namespace App\Service;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\PhpRenderer;

use App\Models\UserModel;
use App\Models\SessionModel;
use App\Models\OrangModel;
use App\Models\AgamaModel;
use App\Models\PmbModel;

final class RootService
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function root(ServerRequestInterface $request, ResponseInterface $response)
    {
        $container = $this->container;
        $session = new SessionModel();

        if (!$session->get('user')) {
            return $response->withHeader('Location', '/login');
        }

        return $container->get('renderer')->render($response, 'admin.phtml');
    }

    public function home(ServerRequestInterface $request, ResponseInterface $response)
    {
        $container = $this->container;
        // return $container->get('renderer')->render($response, 'base/home.template.phtml');
        return $container->get('renderer')->render($response, 'base/maintenance.phtml');
    }

    public function components(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $view_name = $args['view_name'];
        $container = $this->container;
        return $container->get('renderer')->render($response, 'base/components/'.$view_name.'.template.phtml');
    }

    public function login(ServerRequestInterface $request, ResponseInterface $response)
    {
        $container = $this->container;
        $session = new SessionModel($container);

        if ($session->get('user')) {
            return $response->withHeader('Location', '/');
        }

        return $container->get('renderer')->render($response, 'login.phtml');
    }

    public function register(ServerRequestInterface $request, ResponseInterface $response)
    {
        $container = $this->container;
        $session = new SessionModel($container);
        $orang = new OrangModel($container);
        $agama = new AgamaModel($container);

        if ($session->get('user')) {
            return $response->withHeader('Location', '/');
        }

        $selection = $orang->selection;
        $agama->get();
        $value = [
            "selection" => $selection,
            "agama" => $agama->data
        ];

        return $container->get('renderer')->render($response, 'register.phtml', $value);
    }

    public function registerSukses(ServerRequestInterface $request, ResponseInterface $response)
    {
        $container = $this->container;
        return $container->get('renderer')->render($response, 'registerSukses.phtml');
    }

    public function loginSubmit(ServerRequestInterface $request, ResponseInterface $response)
    {
        $container = $this->container;
        $user = new UserModel($container);
        $postData = $request->getParsedBody();

        $sucess = $user->authenticate($postData['username'], $postData['password']);

        if ($sucess) {
            return $response->withHeader('Location', '/');
        }else{
            return $response->withHeader('Location', '/login');
        }
    }

    public function replaceNull($val) {
        foreach ($val as $key => $value) {
            if ($value == 'null' || $value == null) {
                $val[$key] = null;
            }
        }
        return $val;
    }

    public function registerSubmit(ServerRequestInterface $request, ResponseInterface $response)
    {
        $container = $this->container;
        $pmb = new PmbModel($container);
        $orang = new OrangModel($container);

        $postData = $request->getParsedBody();
        $postData = $this->replaceNull($postData);
        $myFiles = $request->getUploadedFiles();

        $data = [];
        $data['bukti_pembayaran'] = $postData['bukti_pembayaran'];
        $data['pernyataan'] = true;
        unset($postData['bukti_pembayaran']);
        unset($postData['pernyataan']);
        $data['orang'] = $postData;

        $pmb->create($data);
        $orang_id = $pmb->data->orang_id;
        $pmb_id = $pmb->data->id;

        $file_name_value = [];

        foreach ($myFiles as $key => $value) {
            if ($value) {
                if ($key == 'bukti_pembayaran') {
                    $file_dir = "pmb/".$pmb_id;
                    $filename = $this->container->get('moveUploadedFile')($file_dir, $value, $value->getClientFilename());
                    $pmb->update($pmb_id, ['bukti_pembayaran' => $filename]);
                }else{
                    $file_dir = "orang/".$orang_id;
                    $filename = $this->container->get('moveUploadedFile')($file_dir, $value, $value->getClientFilename());
                    $file_name_value[$key] = $filename;
                }
            }
        }

        if (count($file_name_value) > 0) {
            $orang->update($orang_id, $file_name_value);
        }

        return $response->withHeader('Location', '/register/sukses');
    }

    public function logout(ServerRequestInterface $request, ResponseInterface $response)
    {
        $container = $this->container;
        $session = new SessionModel();
        
        $session->killAll();
        return $response->withHeader('Location', '/login');
    }

    public function injectAdmin(ServerRequestInterface $request, ResponseInterface $response)
    {
        $container = $this->container;
        $user = new UserModel($container);
        $orang = new OrangModel($container);

        $orang->create(['nama' => 'Admin', 'no_create_user' => true]);
        $user->create([
            'username' => 'admin',
            'password' => 'admin',
            'role' => 'admin',
            'orang_id' => $orang->data->id
        ]);

    }

    public function currentSession(ServerRequestInterface $request, ResponseInterface $response)
    {
        $container = $this->container;
        $session = new SessionModel();
        $user = $session->get('user');

        $response->getBody()->write(json_encode($user));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

}
