<?php
namespace App\Command;

use App\Service\CustomerService;
use App\Service\RandomUserApiService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsCommand(
    name: 'app:customer:import',
    description: 'Customer import command from randomuser API',
)]
class CustomerImportCommand extends Command
{
    private RandomUserApiService $randomUserApi;
    private CustomerService $customer;
    private mixed $defaultResultsLimit;
    private mixed $defaultNationality;

    public function __construct(RandomUserApiService $randomUserApi, CustomerService $customer, ParameterBagInterface $params)
    {
        $this->randomUserApi = $randomUserApi;
        $this->customer = $customer;

        $randomUserApiParameters = $params->get('randomuserapi');
        $this->defaultResultsLimit = $randomUserApiParameters['default_results_limit'];
        $this->defaultNationality = $randomUserApiParameters['default_filters']['nat'];

        parent::__construct();
    }

    protected function configure(): void
    {
//        $this
//            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
//            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
//        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');

        $resultLimitQuestion = new Question('How many you need to import? [default is '.$this->defaultResultsLimit.']: ');
        $resultLimit = $helper->ask($input, $output, $resultLimitQuestion);

        $filterNationalityQuestion = new Question('Preferred Nationality? (options: AU, BR, CA, CH, DE, DK, ES, FI, FR, GB, IE, IN, IR, MX, NL, NO, NZ, RS, TR, UA, US) [default is '.strtoupper($this->defaultNationality).']: ');
        $filterNationality = $helper->ask($input, $output, $filterNationalityQuestion);

        $importedUsers = $this->randomUserApi->get($resultLimit, $filterNationality);
        $output->writeln("Successfully imported users from API.");

        $count = 0;
        foreach ($importedUsers as $importedUser)
        {
            $this->customer->insert($importedUser);
            $count++;
        }

        $output->writeln("\nA total of ".$count." imported users were successfully saved to the database.");

        return Command::SUCCESS;
    }
}
