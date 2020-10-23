<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ShowProductCommand extends Command
{
    protected static $defaultName = 'showProducts';

    protected function configure()
    {
        $this
            ->setDescription('command to list all available products')
            ->setHelp('list all available products you can buy')
            ->addOption(
               'bill-currency',
               null,
               InputOption::VALUE_OPTIONAL,
               'If set, the task will yell in uppercase letters'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            // get the content of the files
            $productsFile = @file_get_contents(__DIR__ . '/../../products.json');
            // decode content of the file
            $products = json_decode($productsFile, true);

            // handle errors in json files
            if( ! is_array(json_decode($productsFile, true))) {
                $output->writeln("Error In json file for the products");
                return Command::FAILURE;
            }
        }catch (\Exception $e) {
            $output->writeln("Error In json file for the products");
            return Command::FAILURE;
        }

        // get the bill-currency option ent
        $inputCurrency = strtoupper($input->getOption('bill-currency'));

        // check the currency
        $currency = $inputCurrency && $inputCurrency == 'EGP' ? "e£" :  '$';

        foreach ($products as $product ){
            $output->writeln( $this->formatOutput($product , $currency) );
        }

        return Command::SUCCESS;
    }

    /**
     * @param $product
     * @param $currency
     * @return string
     */
    private function formatOutput($product , $currency) : string
    {
        return $product['name'] . " $currency" . $product['price'];
    }
}