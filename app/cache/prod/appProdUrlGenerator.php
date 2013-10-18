<?php

use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Psr\Log\LoggerInterface;

/**
 * appProdUrlGenerator
 *
 * This class has been auto-generated
 * by the Symfony Routing Component.
 */
class appProdUrlGenerator extends Symfony\Component\Routing\Generator\UrlGenerator
{
    static private $declaredRoutes = array(
        'tks_tks_tks_getspecial' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Tks\\TksBundle\\Controller\\TksController::getSpecialAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/api/get_special',    ),  ),  4 =>   array (  ),),
        'tks_tks_tks_gettranslations' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Tks\\TksBundle\\Controller\\TksController::getTranslationsAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/api/get_translations',    ),  ),  4 =>   array (  ),),
        'tks_tks_tks_getuserinfo' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Tks\\TksBundle\\Controller\\TksController::getUserInfoAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/api/get_user_info',    ),  ),  4 =>   array (  ),),
        'tks_tks_tks_deployments' => array (  0 =>   array (    0 => 'which',  ),  1 =>   array (    'which' => 'readAccess',    '_controller' => 'Tks\\TksBundle\\Controller\\TksController::deploymentsAction',  ),  2 =>   array (    'which' => 'readAccess|writeAccess',  ),  3 =>   array (    0 =>     array (      0 => 'variable',      1 => '/',      2 => 'readAccess|writeAccess',      3 => 'which',    ),    1 =>     array (      0 => 'text',      1 => '/api/deployments',    ),  ),  4 =>   array (  ),),
        'tks_tks_tks_languages' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Tks\\TksBundle\\Controller\\TksController::languagesAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/api/languages',    ),  ),  4 =>   array (  ),),
        'tks_tks_tks_tksupdate' => array (  0 =>   array (    0 => 'id',  ),  1 =>   array (    '_controller' => 'Tks\\TksBundle\\Controller\\TksController::tksUpdateAction',  ),  2 =>   array (    'id' => '\\d+',    '_method' => 'PUT',  ),  3 =>   array (    0 =>     array (      0 => 'variable',      1 => '/',      2 => '\\d+',      3 => 'id',    ),    1 =>     array (      0 => 'text',      1 => '/api/tks',    ),  ),  4 =>   array (  ),),
        'tks_tks_tks_tksadd' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Tks\\TksBundle\\Controller\\TksController::tksAddAction',  ),  2 =>   array (    '_method' => 'POST',  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/api/tks',    ),  ),  4 =>   array (  ),),
        'tks_tks_tks_tksstats' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Tks\\TksBundle\\Controller\\TksController::tksStatsAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/api/stats',    ),  ),  4 =>   array (  ),),
        'tks_tks_csv_csv' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Tks\\TksBundle\\Controller\\CSVController::csvAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/api/csv',    ),  ),  4 =>   array (  ),),
        'tks_tks_bulkcopies_bulkcopy' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Tks\\TksBundle\\Controller\\BulkCopiesController::bulkCopyAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/api/bulkcopy',    ),  ),  4 =>   array (  ),),
        'tks_tks_apitoken_stats' => array (  0 =>   array (    0 => 'apiToken',  ),  1 =>   array (    '_controller' => 'Tks\\TksBundle\\Controller\\ApiTokenController::statsAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'variable',      1 => '/',      2 => '[^/]++',      3 => 'apiToken',    ),    1 =>     array (      0 => 'text',      1 => '/stats',    ),  ),  4 =>   array (  ),),
        'tks_tks_apitoken_setdeploymenttks' => array (  0 =>   array (    0 => 'apiToken',  ),  1 =>   array (    '_controller' => 'Tks\\TksBundle\\Controller\\ApiTokenController::setDeploymentTksAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'variable',      1 => '/',      2 => '[^/]++',      3 => 'apiToken',    ),    1 =>     array (      0 => 'text',      1 => '/set',    ),  ),  4 =>   array (  ),),
        'tks_tks_apitoken_getdeploymenttks' => array (  0 =>   array (    0 => 'apiToken',    1 => 'start',    2 => 'offset',  ),  1 =>   array (    'start' => 0,    'offset' => 0,    '_controller' => 'Tks\\TksBundle\\Controller\\ApiTokenController::getDeploymentTksAction',  ),  2 =>   array (    'start' => '\\d+',    'offset' => '\\d+',  ),  3 =>   array (    0 =>     array (      0 => 'variable',      1 => '/',      2 => '\\d+',      3 => 'offset',    ),    1 =>     array (      0 => 'variable',      1 => '/',      2 => '\\d+',      3 => 'start',    ),    2 =>     array (      0 => 'variable',      1 => '/',      2 => '[^/]++',      3 => 'apiToken',    ),    3 =>     array (      0 => 'text',      1 => '/get',    ),  ),  4 =>   array (  ),),
        '_welcome' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Tks\\TksBundle\\Controller\\DefaultController::indexAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/',    ),  ),  4 =>   array (  ),),
        'deployment_access' => array (  0 =>   array (    0 => 'select',    1 => 'deployment',  ),  1 =>   array (    '_controller' => 'Tks\\TksBundle\\Controller\\DefaultController::deploymentAccessAction',  ),  2 =>   array (    'only' => 'onlyone|all',  ),  3 =>   array (    0 =>     array (      0 => 'variable',      1 => '/',      2 => '[^/]++',      3 => 'deployment',    ),    1 =>     array (      0 => 'variable',      1 => '/',      2 => '[^/]++',      3 => 'select',    ),  ),  4 =>   array (  ),),
        'editor' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Tks\\TksBundle\\Controller\\DefaultController::editorAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/editor',    ),  ),  4 =>   array (  ),),
        'login' => array (  0 =>   array (  ),  1 =>   array (    '_controller' => 'Tks\\TksBundle\\Controller\\SecurityController::loginAction',  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/login',    ),  ),  4 =>   array (  ),),
        'login_check' => array (  0 =>   array (  ),  1 =>   array (  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/login_check',    ),  ),  4 =>   array (  ),),
        'logout' => array (  0 =>   array (  ),  1 =>   array (  ),  2 =>   array (  ),  3 =>   array (    0 =>     array (      0 => 'text',      1 => '/logout',    ),  ),  4 =>   array (  ),),
    );

    /**
     * Constructor.
     */
    public function __construct(RequestContext $context, LoggerInterface $logger = null)
    {
        $this->context = $context;
        $this->logger = $logger;
    }

    public function generate($name, $parameters = array(), $referenceType = self::ABSOLUTE_PATH)
    {
        if (!isset(self::$declaredRoutes[$name])) {
            throw new RouteNotFoundException(sprintf('Unable to generate a URL for the named route "%s" as such route does not exist.', $name));
        }

        list($variables, $defaults, $requirements, $tokens, $hostTokens) = self::$declaredRoutes[$name];

        return $this->doGenerate($variables, $defaults, $requirements, $tokens, $parameters, $name, $referenceType, $hostTokens);
    }
}
