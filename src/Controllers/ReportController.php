<?php

namespace App\Controllers;

use App\Controllers\BaseController;

final class ReportController extends BaseController
{

    public function kartu_peserta($request, $response, $args)
   {
        $container = $this->container;
        $pmb_id = $args['pmb_id'];
        $object = $this->get_object('pmb');
        $pmb = $object->find($pmb_id);
        $value = ['pmb' => $pmb];

        $pdfContent = $this->container->get('renderPDF')("reports/kartu_peserta.phtml", $value);
        $pdfContent = ['content' => base64_encode($pdfContent)];

        $response->getBody()->write(json_encode($pdfContent));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        // return $response
        //     ->withHeader('Content-Type', 'application/pdf')
        //     ->withStatus(201);
   }

}
