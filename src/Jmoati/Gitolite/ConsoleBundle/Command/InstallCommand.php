<?php

namespace Jmoati\Gitolite\ConsoleBundle\Command;

use Jmoati\HelperBundle\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class InstallCommand extends Command
{

    protected function configure()
    {
        parent::configure();

        $this
            ->setName('gitolite:install')
            ->setDescription('Install gitolite on your server');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fs = new Filesystem();

        parent::execute($input, $output);

        $this->write(
            "\n\n<info>   ____ _ _        _ _ _         ___           _        _ _
  / ___(_) |_ ___ | (_) |_ ___  |_ _|_ __  ___| |_ __ _| | |
 | |  _| | __/ _ \| | | __/ _ \  | || '_ \/ __| __/ _` | | |
 | |_| | | || (_) | | | ||  __/  | || | | \__ \ || (_| | | |
  \____|_|\__\___/|_|_|\__\___| |___|_| |_|___/\__\__,_|_|_|
                                                                        </info>\n\n\n"
        );

        $process = $this->runProcess(
            'whoami',
            '<info>*</info> Are you root ? '
        );

        $username = trim($process->getOutput());

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        $web_user          = $this->getParameter('web_user');
        $gitolite_user     = $this->getParameter('gitolite_user');
        $repositories_path = $this->getParameter("repositories_path");

        if ('root' != $username) {
            $this->write(" <error>[NO]</error>\n");
            $this->write(
                "\n<comment>Your are <info>$username</info>.\nPlease, use sudo to be <info>root</info></comment>.\n\n"
            );
        } else {
            $this->write(" <info>[YES]</info>\n");

            $process = $this->runProcess(
                "ls ~{$gitolite_user}",
                "<info>*</info> Create user <comment>$gitolite_user</comment> for gitolite "
            );

            if (0 == $process->getExitCode()) {
                $this->write(" <info>[ALREADY EXIST]</info>\n");
            } else {
                $this->runProcess(
                    "adduser --system --shell /bin/sh --gecos 'git version control' --group -disabled-password --home /home/{$gitolite_user} {$gitolite_user}",
                    null,
                    true
                );
            }

            $this->runProcess(
                "usermod -a -G $gitolite_user $web_user",
                "<info>*</info> Add group <comment>{$gitolite_user}</comment> to web user <comment>{$web_user}</comment> ",
                true
            );

            $process           = $this->runProcess(
                "echo ~{$web_user}",
                "<info>*</info> Generate id_dsa key for web user <comment>$web_user</comment>  "
            );
            $web_user_path     = trim($process->getOutput());
            $web_user_key_path = "{$web_user_path}/.ssh/id_dsa";

            $this->runProcess(
                "chown $web_user $web_user_path"
            );

            if ($fs->exists($web_user_key_path)) {
                $this->write(" <info>[ALREADY EXIST]</info>\n");
            } else {
                passthru("sudo -H -u $web_user ssh-keygen -q -N '' -t dsa -f $web_user_key_path");

                if ($fs->exists($web_user_key_path)) {
                    $this->write(" <info>[DONE]</info>\n");
                } else {
                    $this->write(" <error>[ERROR]</error>\n");
                }
            }

            $this->runProcess(
                "chmod a+r {$web_user_key_path}.pub && chmod a+x $web_user_path/.ssh",
                "<info>*</info> Check chmod for key <comment>{$web_user_key_path}.pub</comment> ",
                true
            );

            $process            = $this->runProcess(
                "echo ~{$gitolite_user}",
                '<info>*</info> Download gitolite 3 '
            );
            $gitolite_user_path = trim($process->getOutput());

            if ($fs->exists("{$gitolite_user_path}/gitolite")) {
                $this->write(" <info>[ALREADY EXIST]</info>\n");
            } else {

                $this->runProcess(
                    "sudo -H -u $gitolite_user git clone git://github.com/sitaramc/gitolite ~{$gitolite_user}/gitolite",
                    null,
                    true
                );
            }

            $this->runProcess(
                "sudo -H -u $gitolite_user mkdir {$gitolite_user_path}/bin"
            );

            $this->runProcess(
                "sudo -H -u $gitolite_user {$gitolite_user_path}/gitolite/install -ln",
                "<info>*</info> Install gitolite 3",
                true
            );

            $this->runProcess(
                "sudo -H -u $gitolite_user {$gitolite_user_path}/bin/gitolite setup -pk $web_user_path/.ssh/id_dsa.pub",
                "<info>*</info> Setup gitolite 3",
                true
            );

            $this->runProcess(
                "sudo -H -u $web_user echo \"Host localhost\nStrictHostKeyChecking no\" > $web_user_path/.ssh/config",
                "<info>*</info> Setup Gitolite Admin"
            );

            if ($fs->exists("{$repositories_path}/gitolite-admin")) {
                $this->write(" <info>[ALREADY EXIST]</info>\n");
            } else {

                $this->runProcess(
                    "sudo -H -u $web_user git clone {$gitolite_user}@localhost:gitolite-admin.git {$repositories_path}/gitolite-admin",
                    null,
                    true
                );

                $this->write("\n\nFinish");
            }
        }
    }

}
