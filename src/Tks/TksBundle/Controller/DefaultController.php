<?php

namespace Tks\TksBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->redirect($this->generateUrl('editor'));
    }

    public function deploymentAccessAction($select, $deployment)
    {
        return $this->redirect($this->generateUrl('editor', array(
            "select" => $select,
            "deployment" => $deployment
        )));
    }

    public function editorAction() {
        return $this->render('TksBundle:Default:editor.html.twig');
    }
}
