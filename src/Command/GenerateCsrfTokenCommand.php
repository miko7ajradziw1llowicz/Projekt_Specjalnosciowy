<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfToken;

class GenerateCsrfTokenCommand extends Command
{
    protected static $defaultName = 'app:generate-csrf-token';

    private $tokenGenerator;

    public function __construct(TokenGeneratorInterface $tokenGenerator)
    {
        $this->tokenGenerator = $tokenGenerator;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Generates a new CSRF token')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $csrfToken = $this->generateCsrfToken('my_intention');
        $csrfTokenValue = $csrfToken->getValue();

        $output->writeln(sprintf('CSRF token value: %s', $csrfTokenValue));

        return Command::SUCCESS;
    }

    private function generateCsrfToken(string $intention): CsrfToken
    {
        $token = $this->tokenGenerator->generateToken();
        $csrfToken = new CsrfToken($intention, $token);
        return $csrfToken;
    }
}
