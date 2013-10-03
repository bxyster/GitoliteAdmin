<?php

namespace Jmoati\Gitolite\FrontBundle\Controller;

use Jmoati\HelperBundle\Controller\Controller;

class IndexController extends Controller
{

    public function indexAction()
    {
        return $this->render('JmoatiGitoliteFrontBundle:Index:index.html.twig', array());
    }

}
