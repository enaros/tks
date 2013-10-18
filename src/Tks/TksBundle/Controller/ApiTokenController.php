<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Emiliano
 * Date: 20.06.13
 * Time: 10:03
 * To change this template use File | Settings | File Templates.
 */
namespace Tks\TksBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;

class ApiTokenController extends Controller
{
	/**
	 * @Route("/stats/{apiToken}")
	 */
	public function statsAction($apiToken)
	{
		$doc = $this->getDoctrine();
		$service = $this->get("db.service");
		$d = $doc->getRepository('TksBundle:Deployment')->findOneByApiToken($apiToken);

		if (!$d) {
			return $this->errorResponse(null, 'invalid api token');
		}

		$result["deployment"] = $d->getName();
		$result["totalKeys"] = $service->getTotalKeys();
		$result["lastChanged"] = $service->getLastChanged($d->getId());

		$languages = $doc->getRepository('TksBundle:Language')->findAll();

		foreach ($languages as $l) {
			$result[$l->getName()] = $service->getTotalKeys($l->getId(), $d->getId());
		}

		return $this->jsonResponse($result);
	}

	public function getPostFileData()
	{
		$rawPost = file_get_contents('php://input');
		$json = json_decode($rawPost, true);
		if (!$json) {
			$file = tempnam('/tmp', 'rawjson');
			file_put_contents($file, $rawPost);
			$json = json_decode(join('', gzfile($file)), true);
			@unlink($file);
		}
		return $json;
	}

	/**
	 * @Route("/set/{apiToken}")
	 */
	public function setDeploymentTksAction($apiToken)
	{
		ini_set("memory_limit", "512M");
		ini_set("max_execution_time", 0);
		$logger = $this->get('logger');
		$doc = $this->getDoctrine();
		// --------------------------------------------------------------------

		$d = $doc->getRepository('TksBundle:Deployment')->findOneByApiToken($apiToken);

		$data = $this->getPostFileData(); // get data from json file (gzipped)
		if (!$d) { return $this->errorResponse(null, 'invalid api token'); }
		if (!$data) { return $this->errorResponse(null, 'invalid json data'); }

		$result = array('deployment' => $d->getName());
		$this->get('db.service')->createBackupOfTks($d->getId()); // service backup

		try {
			$result = $this->insertSegmentedByBuckets($d->getId(), $data);
		} catch (\Doctrine\DBAL\DBALException $e) {
			// REMEMBER: \Doctrine\ORM\NoResultException useful for no-results
			$logger->err($e->getMessage());
			return $this->errorResponse(
				null,
				'ApiTokenController:insertUpdate error in sql query (probably a key constraint)'
			);
		} catch (\Exception $e) {
			$logger->err($e->getMessage());
			return $this->errorResponse(
				null,
				'invalid json parameters'
			);
		}

		return $this->jsonResponse($result);
	}

	/* OLD: not used right now, replaced using INSERT ON DUCPLICATE KEY */
  // but now we use it again since we need to check for lastChanged
  // TODO: it is showing the updates count but it isnt updating the table: WHY?
	private function update($deploymentId, $data)
	{
		$con = $this->getDoctrine()->getManager()->getConnection();

    $sql = "UPDATE `translation_keys`
				SET value = :value, lastChanged = :lastChanged
				WHERE deployment_id = $deploymentId
				AND language_id = :language
				AND name = :name
				AND lastChanged < :lastChanged";

    $n = 0;
		/* @var \Doctrine\DBAL\Driver\Statement $stmt */
		foreach ($data as $k => $item) {
			$stmt = $con->prepare($sql);
			$stmt->bindValue("name", $item['name']);
			$stmt->bindValue("value", $item['value']);
			$stmt->bindValue("language", $item['language']);
			$stmt->bindValue("lastChanged", $item['lastChanged']);

      $stmt->execute();

			$n += (int) $stmt->rowCount();
		}
		return $n;
	}

	private function insertSegmentedByBuckets($deploymentId, $data)
	{
		if (!isset($data[0])) {
			throw new \Exception('json parameter is not an array');
		}

		$bucketSize = 1000;
		$buckets    = array_chunk($data, $bucketSize);
		$result     = array('inserts' => 0, 'updates' => 0);

		foreach ($buckets as $bucketData) {
			$r = $this->insertUpdate($deploymentId, $bucketData);
			$result['updates'] += $r['updates'];
			$result['inserts'] += $r['inserts'];
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

		$sqlInsertIgnore        = "INSERT IGNORE INTO $table VALUES $values";
		$sqlInsertDuplicateKeys = "
			INSERT INTO $table VALUES $values
			ON DUPLICATE KEY UPDATE
			  value=VALUES(value), lastChanged=VALUES(lastChanged)";

		/* @var \Doctrine\DBAL\Driver\Statement $stmt[] */
		$stmt[1] = $con->prepare($sqlInsertIgnore);
		// $stmt[2] = $con->prepare($sqlInsertDuplicateKeys);

		foreach ($data as $k => $item) {
			foreach ($stmt as $s) {
				$s->bindValue("name$k", $item['name']);
				$s->bindValue("value$k", $item['value']);
				$s->bindValue("language$k", $item['language']);
				$s->bindValue("lastChanged$k", $item['lastChanged']);
			}
		}

		$stmt[1]->execute();
		$result['inserts'] = (int)$stmt[1]->rowCount();
		unset($stmt[1]);

    // I have commented the second statment because we cannot do bulk updates
    // checking if the lastChanged data is equals or less than the lastChanged
    // attribute in $data

		// $stmt[2]->execute();
    // $result['updates'] = (int)$stmt[2]->rowCount() / 2;
    // unset($stmt[2]);
    // unset($con);

        $result['updates'] = $this->update($deploymentId, $data);

		return $result;
	}

	/**
	 * @Route("/get/{apiToken}/{start}/{offset}",
	 *  requirements={"start" = "\d+", "offset" = "\d+"})
	 */
	public function getDeploymentTksAction($apiToken, $start = 0, $offset = 0)
	{
		$doc = $this->getDoctrine();
		$d = $doc->getRepository('TksBundle:Deployment')->findOneByApiToken($apiToken);

		if (!$d) {
			return $this->errorResponse(null, 'invalid api token');
		}
		$result = array('deployment' => $d->getName());

		$r = $doc->getRepository('TksBundle:TranslationKey');
		$q = $r->createQueryBuilder('p')
			->select('p')
			->where('p.deployment = :d')
			->setParameter('d', $d->getId())
			->orderBy('p.name');

		if ($offset) {
			$q->setMaxResults($offset)->setFirstResult($start);
		}

		$tks              = $q->getQuery()->getResult();
		$tksFormatedArray = array();

		foreach ($tks as $t) {
			$tksFormatedArray[] = $t->exportForJson();
		}

		$result['metadata'] = array(
			'offset' => (int)$offset,
			'start'  => (int)$start,
			'total'  => count($tks)
		);
		$result['tks']      = $tksFormatedArray;

		return $this->jsonResponse($result);
	}

	private function jsonResponse($result)
	{
		$response = new Response(json_encode($result));
		$response->headers->set('Content-Type', 'application/json');
		return $response;
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