<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Emiliano
 * Date: 25.06.13
 * Time: 17:50
 * To change this template use File | Settings | File Templates.
 */

namespace Tks\TksBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CSVController extends Controller {
    /**
     * @Route("/csv")
     */
    public function csvAction() {

        $response = new Response();
        // Set headers
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', 'text/csv');
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $response->headers->set('Content-Disposition', 'attachment; filename="file.csv"');
        $response->setContent(utf8_decode(urldecode(base64_decode($_POST['data']))));

        return $response;
    }
}