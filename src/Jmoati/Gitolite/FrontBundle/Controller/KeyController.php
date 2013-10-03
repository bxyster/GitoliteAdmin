<?php

namespace Jmoati\Gitolite\FrontBundle\Controller;

use Jmoati\Gitolite\CoreBundle\Entity\Key;
use Jmoati\Gitolite\FrontBundle\Form\Type\KeyType;
use Jmoati\HelperBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class KeyController extends Controller
{
    public function indexAction()
    {
        $keys = $this
            ->getRepository('JmoatiGitoliteCoreBundle:Key')
            ->findByUserQuery($this->getUser());

        $pagination = $this->paginate($keys);

        return $this->render(
            'JmoatiGitoliteFrontBundle:Key:index.html.twig',
            array(
                'pagination' => $pagination,
            )
        );
    }

    public function newAction()
    {
        $form = $this->createForm(new KeyType());

        if ($this->processForm($form)) {
            $key = $form->getData();
            $key->setUser($this->getUser());

            return $this
                ->flush($key)
                ->dispatch('jmoati_gitolite.should_be_compute')
                ->redirect(array('jmoati_gitolite_front_homepage'));
        }

        return $this->render(
            'JmoatiGitoliteFrontBundle:Key:new.html.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }

    public function deleteAction(Request $request, Key $key)
    {
        if ($request->isMethodSafe()) {
            return $this->render(
                "JmoatiGitoliteFrontBundle:Key:delete.html.twig",
                array(
                    'key'  => $key,
                    'form' => $this->createDeleteForm()->createView(),
                )
            );
        } else {
            return $this
                ->remove($key)
                ->flush()
                ->dispatch('jmoati_gitolite.should_be_compute')
                ->redirect(array('jmoati_gitolite_front_key_index'));
        }
    }

}
