<?php

namespace Jmoati\Gitolite\FrontBundle\Controller;

use Gitter\Client;
use Jmoati\Gitolite\CoreBundle\Entity\Repository;
use Jmoati\Gitolite\FrontBundle\Form\Type\KeyType;
use Jmoati\Gitolite\FrontBundle\Form\Type\RepositoryType;
use Jmoati\HelperBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class RepositoryController extends Controller
{
    public function indexAction($filter)
    {
        $repositories = $this
            ->getRepository('JmoatiGitoliteCoreBundle:Repository')
            ->findByUserQuery(
                $this->getUser(),
                in_array($filter, array('all', 'owner')),
                in_array($filter, array('all', 'developer')),
                in_array($filter, array('all', 'viewer'))
            );

        $pagination = $this->paginate($repositories);

        return $this->render(
            'JmoatiGitoliteFrontBundle:Repository:index.html.twig',
            array(
                 'pagination' => $pagination,
            )
        );
    }

    public function viewAction(Repository $repository)
    {
        $gitter = new Client();
        $repo   = $gitter->getRepository('/Users/jmoati/Sites/gitoliteadmin');

        return $this->render(
            'JmoatiGitoliteFrontBundle:Repository:view.html.twig',
            array(
                 'repository' => $repository,
                 'repo'       => $repo,

            )
        );
    }

    public function newAction()
    {
        $form = $this->createForm(new RepositoryType());

        if ($this->processForm($form)) {
            $repository = $form->getData();
            $repository->setOwner($this->getUser());

            return $this
                ->flush($repository)
                ->dispatch('jmoati_gitolite.should_be_compute')
                ->redirect(array('jmoati_gitolite_front_repository_index'));
        }

        return $this->render(
            'JmoatiGitoliteFrontBundle:Repository:new.html.twig',
            array(
                 'form' => $form->createView(),
            )
        );
    }

    public function editAction(Repository $repository)
    {
        $form_name = $this->createForm(new RepositoryType(), $repository, array('privacy' => false,'details' => false));
        $form_details = $this->createForm(new RepositoryType(), $repository, array('privacy' => false, 'name'    => false));

        return $this->render('JmoatiGitoliteFrontBundle:Repository:edit_index.html.twig', array(
                 'repository'   => $repository,
                 'form_name'    => $form_name->createView(),
                 'form_details' => $form_details->createView(),
            )
        );
    }

    public function deleteAction(Request $request, Repository $repository)
    {
        if ($request->isMethodSafe()) {
            return $this->render(
                "JmoatiGitoliteFrontBundle:Repository:delete.html.twig",
                array(
                     'repository' => $repository,
                     'form'       => $this->createDeleteForm()->createView(),
                )
            );
        } else {
            return $this
                ->remove($repository)
                ->flush()
                ->dispatch('jmoati_gitolite.should_be_compute')
                ->redirect(array('jmoati_gitolite_front_repository_index'));
        }
    }

    public function newKeyAction(Repository $repository)
    {
        $form = $this->createForm(new KeyType());

        if ($this->processForm($form)) {
            $key = $form->getData();
            $key->setRepository($repository);

            return $this
                ->flush($key)
                ->dispatch('jmoati_gitolite.should_be_compute')
                ->redirect(array('jmoati_gitolite_front_homepage'));
        }

        return $this->render(
            'JmoatiGitoliteFrontBundle:Repository:newKey.html.twig',
            array(
                 'form' => $form->createView(),
            )
        );

    }
}
