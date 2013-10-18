<?php

use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RequestContext;

/**
 * appProdUrlMatcher
 *
 * This class has been auto-generated
 * by the Symfony Routing Component.
 */
class appProdUrlMatcher extends Symfony\Bundle\FrameworkBundle\Routing\RedirectableUrlMatcher
{
    /**
     * Constructor.
     */
    public function __construct(RequestContext $context)
    {
        $this->context = $context;
    }

    public function match($pathinfo)
    {
        $allow = array();
        $pathinfo = rawurldecode($pathinfo);

        if (0 === strpos($pathinfo, '/api')) {
            if (0 === strpos($pathinfo, '/api/get_')) {
                // tks_tks_tks_getspecial
                if ($pathinfo === '/api/get_special') {
                    return array (  '_controller' => 'Tks\\TksBundle\\Controller\\TksController::getSpecialAction',  '_route' => 'tks_tks_tks_getspecial',);
                }

                // tks_tks_tks_gettranslations
                if ($pathinfo === '/api/get_translations') {
                    return array (  '_controller' => 'Tks\\TksBundle\\Controller\\TksController::getTranslationsAction',  '_route' => 'tks_tks_tks_gettranslations',);
                }

                // tks_tks_tks_getuserinfo
                if ($pathinfo === '/api/get_user_info') {
                    return array (  '_controller' => 'Tks\\TksBundle\\Controller\\TksController::getUserInfoAction',  '_route' => 'tks_tks_tks_getuserinfo',);
                }

            }

            // tks_tks_tks_deployments
            if (0 === strpos($pathinfo, '/api/deployments') && preg_match('#^/api/deployments(?:/(?P<which>readAccess|writeAccess))?$#s', $pathinfo, $matches)) {
                return $this->mergeDefaults(array_replace($matches, array('_route' => 'tks_tks_tks_deployments')), array (  'which' => 'readAccess',  '_controller' => 'Tks\\TksBundle\\Controller\\TksController::deploymentsAction',));
            }

            // tks_tks_tks_languages
            if ($pathinfo === '/api/languages') {
                return array (  '_controller' => 'Tks\\TksBundle\\Controller\\TksController::languagesAction',  '_route' => 'tks_tks_tks_languages',);
            }

            if (0 === strpos($pathinfo, '/api/tks')) {
                // tks_tks_tks_tksupdate
                if (preg_match('#^/api/tks/(?P<id>\\d+)$#s', $pathinfo, $matches)) {
                    if ($this->context->getMethod() != 'PUT') {
                        $allow[] = 'PUT';
                        goto not_tks_tks_tks_tksupdate;
                    }

                    return $this->mergeDefaults(array_replace($matches, array('_route' => 'tks_tks_tks_tksupdate')), array (  '_controller' => 'Tks\\TksBundle\\Controller\\TksController::tksUpdateAction',));
                }
                not_tks_tks_tks_tksupdate:

                // tks_tks_tks_tksadd
                if ($pathinfo === '/api/tks') {
                    if ($this->context->getMethod() != 'POST') {
                        $allow[] = 'POST';
                        goto not_tks_tks_tks_tksadd;
                    }

                    return array (  '_controller' => 'Tks\\TksBundle\\Controller\\TksController::tksAddAction',  '_route' => 'tks_tks_tks_tksadd',);
                }
                not_tks_tks_tks_tksadd:

            }

            // tks_tks_tks_tksstats
            if ($pathinfo === '/api/stats') {
                return array (  '_controller' => 'Tks\\TksBundle\\Controller\\TksController::tksStatsAction',  '_route' => 'tks_tks_tks_tksstats',);
            }

            // tks_tks_csv_csv
            if ($pathinfo === '/api/csv') {
                return array (  '_controller' => 'Tks\\TksBundle\\Controller\\CSVController::csvAction',  '_route' => 'tks_tks_csv_csv',);
            }

            // tks_tks_bulkcopies_bulkcopy
            if ($pathinfo === '/api/bulkcopy') {
                return array (  '_controller' => 'Tks\\TksBundle\\Controller\\BulkCopiesController::bulkCopyAction',  '_route' => 'tks_tks_bulkcopies_bulkcopy',);
            }

        }

        if (0 === strpos($pathinfo, '/s')) {
            // tks_tks_apitoken_stats
            if (0 === strpos($pathinfo, '/stats') && preg_match('#^/stats/(?P<apiToken>[^/]++)$#s', $pathinfo, $matches)) {
                return $this->mergeDefaults(array_replace($matches, array('_route' => 'tks_tks_apitoken_stats')), array (  '_controller' => 'Tks\\TksBundle\\Controller\\ApiTokenController::statsAction',));
            }

            // tks_tks_apitoken_setdeploymenttks
            if (0 === strpos($pathinfo, '/set') && preg_match('#^/set/(?P<apiToken>[^/]++)$#s', $pathinfo, $matches)) {
                return $this->mergeDefaults(array_replace($matches, array('_route' => 'tks_tks_apitoken_setdeploymenttks')), array (  '_controller' => 'Tks\\TksBundle\\Controller\\ApiTokenController::setDeploymentTksAction',));
            }

        }

        // tks_tks_apitoken_getdeploymenttks
        if (0 === strpos($pathinfo, '/get') && preg_match('#^/get/(?P<apiToken>[^/]++)(?:/(?P<start>\\d+)(?:/(?P<offset>\\d+))?)?$#s', $pathinfo, $matches)) {
            return $this->mergeDefaults(array_replace($matches, array('_route' => 'tks_tks_apitoken_getdeploymenttks')), array (  'start' => 0,  'offset' => 0,  '_controller' => 'Tks\\TksBundle\\Controller\\ApiTokenController::getDeploymentTksAction',));
        }

        // _welcome
        if (rtrim($pathinfo, '/') === '') {
            if (substr($pathinfo, -1) !== '/') {
                return $this->redirect($pathinfo.'/', '_welcome');
            }

            return array (  '_controller' => 'Tks\\TksBundle\\Controller\\DefaultController::indexAction',  '_route' => '_welcome',);
        }

        // deployment_access
        if (preg_match('#^/(?P<select>[^/]++)/(?P<deployment>[^/]++)$#s', $pathinfo, $matches)) {
            return $this->mergeDefaults(array_replace($matches, array('_route' => 'deployment_access')), array (  '_controller' => 'Tks\\TksBundle\\Controller\\DefaultController::deploymentAccessAction',));
        }

        // editor
        if ($pathinfo === '/editor') {
            return array (  '_controller' => 'Tks\\TksBundle\\Controller\\DefaultController::editorAction',  '_route' => 'editor',);
        }

        if (0 === strpos($pathinfo, '/log')) {
            if (0 === strpos($pathinfo, '/login')) {
                // login
                if ($pathinfo === '/login') {
                    return array (  '_controller' => 'Tks\\TksBundle\\Controller\\SecurityController::loginAction',  '_route' => 'login',);
                }

                // login_check
                if ($pathinfo === '/login_check') {
                    return array('_route' => 'login_check');
                }

            }

            // logout
            if ($pathinfo === '/logout') {
                return array('_route' => 'logout');
            }

        }

        throw 0 < count($allow) ? new MethodNotAllowedException(array_unique($allow)) : new ResourceNotFoundException();
    }
}
