<?php

namespace Jmoati\Gitolite\ConsoleBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;

class ConfigGenerator
{

    /**
     * @var EntityManager
     */
    protected $em;
    /**
     * @var string
     */
    protected $web_user;
    /**
     * @var string
     */
    protected $web_user_path;

    /**
     * @param Registry $doctrine
     * @param string   $web_user
     */
    public function __construct(Registry $doctrine, $web_user)
    {
        $this->em            = $doctrine->getManager();
        $this->web_user      = $web_user;
        $this->web_user_path = exec("echo ~{$this->web_user}");
    }

    public function generateKeysFiles($path = null, $purge_before = false)
    {
        $users = $this->getKeys();

        if (count($users)) {
            if ($purge_before) {
                passthru("rm -Rf $path/*");
            }

            foreach ($users AS $user => $keys) {
                foreach ($keys AS $key_name => $key_content) {
                    $file = "{$path}/{$key_name}/{$user}.pub";
                    $dir  = dirname($file);

                    if (!file_exists($dir)) {
                        mkdir($dir);
                    }

                    $f = fopen($file, 'wb');
                    fwrite($f, $key_content);
                    fclose($f);
                }
            }
        }

        return true;
    }

    public function getKeys()
    {
        $em     = $this->em;
        $result = array();

        $users = $em
            ->getRepository('JmoatiGitoliteCoreBundle:User')
            ->findAll();

        foreach ($users AS $user) {
            foreach ($user->getKeys() AS $key) {
                $result["user_{$user->getSlug()}"][$key->getSlug()] = $key->getValue();
            }
        }

        $repositories = $em
            ->getRepository('JmoatiGitoliteCoreBundle:Repository')
            ->findAll();

        foreach ($repositories AS $repository) {
            foreach ($repository->getKeys() AS $key) {
                $result["repo_{$repository->getSlug()}"][$key->getSlug()] = $key->getContent();
            }
        }

        $result["system"]["system"] = $this->getSystemKey();

        return $result;
    }

    protected function getSystemKey()
    {
        if (file_exists($this->web_user_path)) {
            return file_get_contents("{$this->web_user_path}/.ssh/id_dsa.pub");
        }
    }

    /**
     * @param string $destination
     *
     * @return boolean|string
     */
    public function generateRepositoryFile($destination = null)
    {
        $file_content = array();
        $repositories = $this->getRepositories();

        foreach ($repositories AS $repository => $values) {
            if (count($file_content)) {
                $file_content[] = "";
            }

            $file_content[] = "repo\t$repository";

            foreach ($values AS $right => $users) {
                $file_content[] = "\t" . strtoupper($right) . " = " . implode(' ', $users);
            }
        }

        $file_content = implode("\n", $file_content);

        if (is_string($destination)) {
            $f = fopen($destination, 'wb');
            fwrite($f, $file_content);
            fclose($f);

            return true;
        } else {
            return $file_content;
        }
    }

    public function getRepositories()
    {
        $result       = array();
        $repositories = $this->em->getRepository('JmoatiGitoliteCoreBundle:Repository')->findAll();

        foreach ($repositories AS $repository) {
            $result[$repository->getSlug()]['RW+'][] = "system";
            $result[$repository->getSlug()]['RW+'][] = 'user_' . $repository->getOwner()->getSlug();
            $result[$repository->getSlug()]['R'][]   = "deploy_{$repository->getSlug()}";

            if ($repository->isPublic()) {
                $result[$repository->getSlug()]['R'][] = "@all";
            }

            foreach ($repository->getDevelopers() AS $developer) {
                $result[$repository->getSlug()]['RW+'][] = 'user_' . $developer->getSlug();;

            }

            foreach ($repository->getViewers() AS $viewer) {
                $result[$repository->getSlug()]['R'][] = 'user_' . $viewer->getSlug();;

            }
        }

        unset($result['gitolite-admin']);
        $result['gitolite-admin']["RW+"] = array("system");

        return $result;
    }

}
