<?php

namespace App\Service;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\PhpRenderer;
use App\Models\PmbModel;

final class PmbService
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->pmb = new PmbModel($container);
    }

    public function kartuPeserta(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {   
        $id = $args['id'];
        $pmb = $this->pmb;
        $pmb->read($id);

        if ($pmb->data->orang->tanggal_lahir) {
            $tgl_lahir = strtotime($pmb->data->orang->tanggal_lahir);
            $tgl_lahir = date('d-m-Y', $tgl_lahir);
        }else{
            $tgl_lahir = '';
        }

        $value = ['pmb' => $pmb->data, 'selection' => $pmb->selection, 'tgl_lahir' => $tgl_lahir];
        $pdfContent = $this->container->get('renderPDF')("html_templates/kartu_peserta.phtml", $value);

        $response->getBody()->write($pdfContent);
        return $response
            ->withHeader('Content-Type', 'application/pdf')
            // ->withHeader('Content-Disposition', 'attachment; filename="Kartu Peserta '.$orang->nik.' '.$orang->nama.'.pdf"') // Force Download
            ->withStatus(201);
    }

    public function pmbGetBaru(ServerRequestInterface $request, ResponseInterface $response)
    {
        $pmb = $this->pmb;
        $pmb->get([['status', 'baru']]);
        $response->getBody()->write($pmb->data->toJson());
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

}
