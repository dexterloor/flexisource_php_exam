<?php

namespace App\Command;

// Command
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

// Parameters from config files
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

// Services
use App\Service\RandomUserApiService;
use App\Service\CustomerService;

class ImportUsers extends Command
{
    private RandomUserApiService $randomUserApi;
    private CustomerService $customer;
    private $defaultResultsLimit;
    private $defaultNationality;

    /**
     * @param RandomUserApiService $randomUserApi
     * @param CustomerService $customer
     */
    public function __construct(RandomUserApiService $randomUserApi, CustomerService $customer, ParameterBagInterface $params)
    {
        $this->randomUserApi = $randomUserApi;
        $this->customer = $customer;

        $randomUserApiParameters = $params->get('randomuser_api');
        $this->defaultResultsLimit = $randomUserApiParameters['default_results_limit'];
        $this->defaultNationality = $randomUserApiParameters['default_filters']['nat'];

        parent::__construct();
        $this->setName('app:customers:import');
    }

    protected function configure(): void
    {
        $this->setDescription('Import users from randomuser.me');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $io = new SymfonyStyle($input, $output);
        $helper = $this->getHelper('question');

        // Ask Result Limit
        $resultLimitQuestion = new Question('No. of users to import [default is '.$this->defaultResultsLimit.']: ');
        $resultLimit = $helper->ask($input, $output, $resultLimitQuestion);

        // Ask Nationality Filter
        $filterNationalityQuestion = new Question('Nationality to pull (choose from AU, BR, CA, CH, DE, DK, ES, FI, FR, GB, IE, IN, IR, MX, NL, NO, NZ, RS, TR, UA, US) [default is '.strtoupper($this->defaultNationality).']: ');
        $filterNationality = $helper->ask($input, $output, $filterNationalityQuestion);

        $importedUsers = $this->randomUserApi->get($resultLimit, $filterNationality);
        $output->writeln("Successfully imported users from API.");
        $output->writeln("Saving imported users to the database...");

        $count = 0;
        foreach ($importedUsers as $importedUser)
        {
            $this->customer->insert($importedUser);
            $count++;

            $savingMessage = $count > 1 ? 'Saved '.$count.' users.' : 'Saved '.$count.' user.';
            $output->writeln($savingMessage);
        }

        $output->writeln("\nA total of ".$count." imported users were successfully saved to the database.");

        return Command::SUCCESS;
    }

}