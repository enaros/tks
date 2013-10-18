<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Emiliano
 * Date: 19.06.13
 * Time: 17:03
 * To change this template use File | Settings | File Templates.
 */
namespace Tks\TksBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;

use Tks\TksBundle\Entity\Deployment;
use Tks\TksBundle\Entity\TranslationKey;
use Tks\TksBundle\Entity\UsersAccess;

class BulkCopiesController extends Controller
{
    /**
     * @Route("/bulkcopy")
     */
    public function bulkCopyAction()
    {
        ini_set("memory_limit", "512M");
        ini_set("max_execution_time", 0);
        // parameters
        $langSourceId   = (int)@$_GET['source-language'];
        $deploySourceId = (int)@$_GET['source-deployment'];
        $deployTargetId = (int)@$_GET['target-deployment'];
        $filter         = $_GET['filter'];
        $filterOpt      = $_GET['filter-opt'];
        $copyType       = @$_GET['opt'];
        $replace        = $copyType == 'create-replace';

        if (!($langSourceId && $deployTargetId && $deploySourceId)) {
            $response = new Response('missing/invalid parameters');
            $response->setStatusCode(500);
            return $response;
        }

        $this->get("db.service")->createBackupOfTks($deployTargetId);

        // bulk copy
        $this->get("db.service")->bulkQuery(
            $replace,
            $deployTargetId,
            $deploySourceId,
            $langSourceId,
            $filter,
            $filterOpt
        );

        $response = new Response();
        $response->setStatusCode(200);
        return $response;
    }
}