<?php

namespace Tks\TksBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;

use Tks\TksBundle\Entity\Deployment;
use Tks\TksBundle\Entity\TranslationKey;
use Tks\TksBundle\Entity\UsersAccess;

class TksController extends Controller
{
    /**
     * @Route("/get_special")
     */
    // get translation keys with special conditions
    // like show all keys or show only empty keys
    public function getSpecialAction()
    {
        // GET params
        $language   = (int)@$_GET['language'];
        $deployment = (int)@$_GET['deployment'];
        $filter     = @$_GET['filter'];
        $show       = @$_GET['show'];

        // get repository
        $r = $this->getDoctrine()->getRepository('TksBundle:TranslationKey');

        // This query is gonna get ALL keys that matches custom filter and store it in $allKeys
        $q       = $r->createQueryBuilder('p')
            ->select('p.name')
            ->where('p.name LIKE :f')
            ->setParameter('f', "%$filter%")
            ->orderBy('p.name')
            ->groupBy('p.name')
            ->getQuery();
        $allKeys = $q->getResult();

        // This query is gonna get only existing keys for target language and deployment
        $q   = $r->createQueryBuilder('p')
            ->select('p.name, p.id, p.value')
            ->where('p.language = :l AND p.deployment = :d AND p.name LIKE :f')
            ->setParameter('l', $language)
            ->setParameter('d', $deployment)
            ->setParameter('f', "%$filter%")
            ->orderBy('p.name')
            ->getQuery();
        $all = $q->getResult();

        // then we do the following:
        //  1. add to every key in allKeys the proper value [show==all]
        //  2. remove from every key in allKeys that has a value [show==empty]
        $temp = array();
        // first we create a dictionary to get direct access by name
        foreach ($all as $k => $v) {
            $temp[$v['name']] = $v;
        }
        // then we loop through allKeys and remove/replace depending on $show
        foreach ($allKeys as $k => $v) {
            if (isset($temp[$v['name']])) {
                if ($show == 'all') {
                    $allKeys[$k] = $temp[$v['name']];
                } else {
                    if ($show == 'empty') {
                        unset($allKeys[$k]);
                    }
                }
            }
        }
        // normalize array, needed when we unset keys
        $allKeys = array_values($allKeys);
        return $this->jsonResponse($allKeys);
    }

    private function jsonResponse($result)
    {
        $response = new Response(json_encode($result));
        $response->headers->set('Content-Type', 'application/json');
        $response->setStatusCode(200);
        return $response;
    }

    /**
     * @Route("/get_translations")
     */
    public function getTranslationsAction()
    {
        // GET params
        $language   = (int)@$_GET['language'];
        $deployment = (int)@$_GET['deployment'];
        $filter     = @$_GET['filter'];
        $filterVal  = @$_GET['filterValues'];
        $show       = @$_GET['show'];

        if ($show == 'all' || $show == 'empty') {
            return $response = $this->forward("TksBundle:Tks:getSpecial");
        }

        $r = $this->getDoctrine()->getRepository('TksBundle:TranslationKey');

        /* @var \Tks\TksBundle\Entity\TranslationKey[] $all */
        if ($language && $deployment) {
            $sqlWhere = 'p.language = :l AND p.deployment = :d AND p.name LIKE :f';
            if ($filterVal) {
                $sqlWhere .= ' AND p.value LIKE :fv';
            }
            if ($show == 'today') {
                $sqlWhere .= ' AND p.lastchanged >= :when';
            }
            $q   = $r->createQueryBuilder('p')
                ->where($sqlWhere)
                ->setParameter('l', $language)
                ->setParameter('d', $deployment)
                ->setParameter('f', "%$filter%")
                ->orderBy('p.name');
                //->setMaxResults(25)
            if ($filterVal) {
                $q->setParameter('fv', "%$filterVal%");
            }
            if ($show == 'today') {
                $q->setParameter(
                    'when',
                    date('Y-m-d 00:00:00')
                );
            }

            $all = $q->getQuery()->getResult();
        } else {
            $all = $r->findAll();
        }

        return $this->jsonResponse($all);
    }

    // returns array
    // array[
    //  readAccess => array(depID1, depID2, ...)
    //  writeAccess => array(depID1, ...)
    // ]

    /**
     * @Route("/get_user_info")
     */
    public function getUserInfoAction()
    {
        return $this->jsonResponse($this->getUserAccess());
    }

    public function getUserAccess()
    {
        $user           = $this->get('security.context')->getToken()->getUser();
        $userId         = $user->getId();
        $userAccessType = $user->getAccessType();

        $permisions = array();

        switch ($userAccessType) {
            case 'WHITELIST':
                // here I get an array of permisions
                // array(
                //   deploymentId -> canWrite (TRUE|FALSE)
                // )
                $access = $this->getDoctrine()->getRepository(
                    'TksBundle:UsersAccess'
                )->findByUser($userId);
                foreach ($access as $a) {
                    $permisions[$a->getDeployment()] = $a->getCanWrite();
                }
                break;
            case 'ALL':
                // find all non backups deployments
                $access = $this->getDoctrine()->getRepository(
                    'TksBundle:Deployment'
                )->findByParent(0);
                foreach ($access as $a) {
                    $permisions[$a->getId()] = 1;
                }
                break;
        }

        $writeAccess = array_keys(
            array_filter($permisions)
        ); // get only the key with value 1
        $readAccess  = array_keys(
            $permisions
        ); // get all de keys from permisions array
        if (!count($writeAccess)) {
            $writeAccess = array(0);
        }
        if (!count($readAccess)) {
            $readAccess = array(0);
        }
        return array(
            'readAccess'  => $readAccess,
            'writeAccess' => $writeAccess
        );
    }

    /**
     * @Route("/deployments/{which}", requirements={"which" = "readAccess|writeAccess"})
     */
    public function deploymentsAction($which = 'readAccess')
    {
        $deployments = $this->getDeployments($which);
        return $this->jsonResponse($deployments);
    }

    public function getDeployments($accessRights = 'readAccess')
    {
        $access = $this->getUserAccess();
        $em     = $this->getDoctrine()->getManager();
        $query  = $em->createQuery(
            "SELECT u FROM Tks\TksBundle\Entity\Deployment u WHERE u.id IN (?1)"
        )->setParameter(1, $access[$accessRights]);
        return $query->getResult();
    }

    /**
     * @Route("/languages")
     */
    public function languagesAction()
    {
        $all = $this->getLanguages();
        return $this->jsonResponse($all);
    }

    public function getLanguages()
    {
        return $this->getDoctrine()->getRepository(
            'TksBundle:Language'
        )->findAll();
    }

    /**
     * @Route("/tks/{id}", requirements={"id" = "\d+"})
     * @Method({"PUT"})
     */
    public function tksUpdateAction($id)
    {
        $data = json_decode($this->get("request")->getContent());

        $em = $this->getDoctrine()->getManager();
        $tk = $em->getRepository('TksBundle:TranslationKey')->find($id);

        $tk->setValue($data->value);
        $tk->setLastChanged(new \DateTime('now'));
        $em->flush();

        // you have to return the changed model otherwise Bakbone is gonna call the error callback
        return $this->jsonResponse($tk);
    }

    /**
     * @Route("/tks")
     * @Method({"POST"})
     */
    public function tksAddAction()
    {
        $data = json_decode($this->get("request")->getContent());
        //$data->id = '10000';

        $em               = $this->getDoctrine()->getManager();
        $data->language   = $this->getDoctrine()->getRepository(
            'TksBundle:Language'
        )->find((int)$data->language);
        $data->deployment = $this->getDoctrine()->getRepository(
            'TksBundle:Deployment'
        )->find((int)$data->deployment);

        $tk = new TranslationKey();
        $tk->save($data);
        $em->persist($tk);
        $em->flush();

        return $this->jsonResponse($tk);
    }

    /**
     * @Route("/stats")
     */
    public function tksStatsAction()
    {
        $service     = $this->get("db.service");
        $deployments = $this->getDeployments();
        $languages   = $this->getLanguages();
        $totalKeys   = $service->getTotalKeys();

        $stats = array(
            'categories' => array(),
            'series'     => array()
        );

        foreach ($deployments as $d) {
            array_push($stats['categories'], $d->name);
        }

        foreach ($languages as $l) {

            $serie = array(
                "name"  => 'Completed',
                "data"  => array(),
                "stack" => $l->getShortname()
            );

            foreach ($deployments as $d) {
                $total = $service->getTotalKeys($l->getId(), $d->getId());
                array_push($serie['data'], (int)$total);
            }
            $serieRemaining          = $serie; // copy array
            $serieRemaining['color'] = '#999999';
            array_walk(
                $serieRemaining['data'],
                function (&$value, $key) use ($totalKeys) {
                    $value = $totalKeys - $value;
                }
            );
            $serieRemaining['name'] = 'Not completed';
            array_push($stats['series'], $serieRemaining);
            array_push($stats['series'], $serie);
        }

        return $this->jsonResponse($stats);
    }

    private function errorResponse($result, $msg)
    {
        $result['error'] = $msg;
        $response        = new Response(json_encode($result));
        $response->headers->set('Content-Type', 'application/json');
        $response->setStatusCode(200);
        return $response;
    }
}
