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

class BulkCreateController extends Controller
{
    /**
     * @Route("/bulkcreate")
     */
    public function bulkCreateAction()
    {
        $deploymentId = (int) $_POST['deploymentId'];
        $keylist = $this->filter($_POST['keylist']);
        $keylist = $this->prepareData($keylist);

        $this->insertUpdate($deploymentId, $keylist);

        $a = $this->getDoctrine()->getRepository(
            'TksBundle:Language'
        )->findAll();

        $result = array(
            'status' => 'ok',
            'did' => $deploymentId,
            'a' => $a,
            'keylist' => $keylist
        );

        $response = new Response(json_encode($result));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    private function prepareData($keylist)
    {
        $result = array();
        $mysqldate = date('Y-m-d H:i:s', time());

        $languages = $this->getDoctrine()->getRepository(
            'TksBundle:Language'
        )->findAll();

        foreach ($keylist as $i => $key) {
            foreach ($languages as $lang) {
                $result[] = array(
                    'name' => $key,
                    'value' => $key,
                    'language' => $lang->id,
                    'lastChanged' => $mysqldate
                );
            }
        }
        return $result;
    }

    private function insertUpdate($deploymentId, $data)
    {
        $con    = $this->getDoctrine()->getManager()->getConnection();
        $values = array();
        foreach ($data as $k => $item) {
            $values[] = "($deploymentId, :language$k, :name$k, :value$k, :lastChanged$k)";
        }

        $values = implode(",", $values);
        $table  = "`translation_keys` (`deployment_id`, `language_id`, `name`, `value`, `lastChanged`)";

        $sqlInsertDuplicateKeys = "
			INSERT INTO $table VALUES $values
			ON DUPLICATE KEY UPDATE
			  value=VALUES(value), lastChanged=VALUES(lastChanged)";

        /* @var \Doctrine\DBAL\Driver\Statement $stmt */
        $stmt = $con->prepare($sqlInsertDuplicateKeys);

        foreach ($data as $k => $item) {
            $stmt->bindValue("name$k", $item['name']);
            $stmt->bindValue("value$k", $item['value']);
            $stmt->bindValue("language$k", $item['language']);
            $stmt->bindValue("lastChanged$k", $item['lastChanged']);
        }

        $stmt->execute();
//        unset($stmt);
    }

    private function filter($keylist)
    {
        $keylist = explode("\n", $keylist);

        foreach ($keylist as $i => &$key) {
            $key = trim($key);
            $space = strpos($key, ' ');
            if ($space !== false) {
                $key = substr($key, 0, $space);
            }
            if (empty($key)) {
                unset($keylist[$i]);
            }
        }
        return $keylist;
    }
}