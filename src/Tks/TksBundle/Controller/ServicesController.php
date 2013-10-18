<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Emiliano
 * Date: 25.06.13
 * Time: 10:10
 * To change this template use File | Settings | File Templates.
 */
namespace Tks\TksBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;
use Tks\TksBundle\Entity\Deployment;


class ServicesController
{
    public function __construct(\Doctrine\ORM\EntityManager $em)
    {
        $this->em = $em;
    }

    public function getLastChanged($deploymentId) {
        $qb = $this->em->createQueryBuilder();
        $qb->select('u.lastchanged')
            ->from('Tks\TksBundle\Entity\TranslationKey', 'u')
            ->where('u.deployment = ?1')
            ->orderBy('u.lastchanged', 'DESC')
            ->setParameter(1, $deploymentId)
            ->setMaxResults(1);

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getTotalKeys($languageId = null, $deploymentId = null) {
        $sql = "SELECT COUNT(DISTINCT u.name) FROM Tks\TksBundle\Entity\TranslationKey u";

        if ($languageId && $deploymentId) {
            $sql .= " WHERE u.language = $languageId AND u.deployment = $deploymentId";
        }

        $query = $this->em->createQuery($sql);
        return (int)$query->getSingleScalarResult();
    }

    public function createBackupOfTks($deployTargetId)
    {
        $idBackupDeployment = $this->createBackupDeployment($deployTargetId);
        // create backup
        $this->bulkQuery(null, $idBackupDeployment, $deployTargetId);
    }

    public function createBackupDeployment($deployTargetId)
    {
        $em            = $this->em;
        $bupDeployment = new Deployment();
        $bupDeployment->setParent($deployTargetId);
        $bupDeployment->setName('backup.deployment');
        $em->persist($bupDeployment);
        $em->flush();
        return $bupDeployment->getId();
    }

    public function bulkQuery(
        $replace,
        $deployTargetId,
        $deploySourceId,
        $langSourceId = null,
        $filter = '',
        $filterOpt = 'contains'
    ) {
        // bulk copy query:
        $q = $this->buildQuery($replace, $langSourceId, $filter);
        /* @var \PDOStatement $stmt */
        $stmt = $this->em->getConnection()->prepare($q);
        $stmt->bindValue("deployTargetId", $deployTargetId);
        $stmt->bindValue("deploySourceId", $deploySourceId);
        if ($langSourceId) {
            $stmt->bindValue("langSourceId", $langSourceId);
        }
        if ($filter && $filter != '') {
            $filterWithOpt = "%" . $filter . "%";
            switch ($filterOpt) {
              case 'startswith':
                $filterWithOpt = $filter . "%";
                break;
              case 'endswith':
                $filterWithOpt = "%" . $filter;
                break;
            }
            $stmt->bindValue("filter", $filterWithOpt);
        }
        $stmt->execute();
        //$stmt->rowCount();
    }

    public function buildQuery($replace, $langSourceId = null, $filter = '')
    {
        $q = "INSERT IGNORE ";
        if ($replace) {
            $q = "REPLACE INTO ";
        }

        $q .= "
                translation_keys (deployment_id, language_id, name, value, lastChanged)
                SELECT
                :deployTargetId, tk.language_id, tk.name, tk.value, NOW()
                FROM translation_keys tk
                WHERE tk.deployment_id = :deploySourceId";

        if ($langSourceId) {
            $q .= " AND tk.language_id = :langSourceId";
        }

        if ($filter && $filter != '') {
            $q .= " AND tk.name LIKE :filter";
        }

        return $q;
    }
}