<?php

namespace App\Command;

use App\Traits\DiscountTrait;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateCartCommand extends  Command
{
    use DiscountTrait;

    protected static $defaultName = 'createCart';

    private $offers ;
    const TAX  = 14;

    public function __construct()
    {
        /**
         * type 1 discount on the same product
         * type 2 discount on another product when buy another 2 product
         */
        $this->offers = [
            ['name' => 'Shoes',     'type'    =>  1,     'offer' =>10                     ],
            ['name' => 'T-shirt',   'type'    =>  2,     'offer' => 50  , 'on' => 'Jacket']
        ];

        parent::__construct();
    }

    protected function configure()
    {
        // set the configurations for the command
        $this
            ->setDescription('program to price a cart of products')
            ->setHelp('This command take a cart of product as arguments, calculate the total and offers')
            ->addOption(
                'bill-currency',
                null,
                InputOption::VALUE_OPTIONAL,
                'If set, the task will yell in uppercase letters'
            )
            ->addArgument(
                'names',
                InputArgument::IS_ARRAY,
                'names'
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

        // extract vales of the product given by the user
        $names = $input->getArgument('names');


        // calculate the offers for the users
        $offers             =       $this->getOffers($names , $this->offers , $products ,$currency);
        // store the total discount totalDiscount variable
        $totalDiscount      =       $offers[0];
        // store the array of discount texts
        $discountTexts      =       $offers[1];


        // Calculate the sub total for the products by passing :
        // the products given by the user
        // the available products
        $subTotal  = $this->getTotal($names , $products);

        // calculate the tax before discount
        $tax=  $subTotal * self::TAX /100 ;
        // calculate total
        $total = $subTotal +  $tax - $totalDiscount;

        // display the result
        $this->outputResult($output , $subTotal , $tax , $discountTexts ,$total , $currency);

        return Command::SUCCESS;
    }


    /**
     * the function used to output text for the user
     * it accepts instance of OutputInterface
     * sub total , tax , array of discount texts and total
     * @param OutputInterface $output
     * @param $subTotal
     * @param $tax
     * @param $disc
     * @param $total
     * @param $currency
     */
    private function outputResult(OutputInterface $output , $subTotal , $tax , $disc , $total , $currency)
    {
        $output->writeln("Subtotal: $currency $subTotal" );
        $output->writeln("Taxes: $currency $tax");

        if(count($disc)){
            $output->writeln('Discounts:');
            foreach ($disc as $item){
                $output->writeln('     ' .$item);
            }
        }

        $output->writeln("Total: $currency $total");
    }


    /**
     * the function calculates the total price
     * for the products without the discount
     *
     * it accepts the user input and the array of products
     * return the total
     * @param $userInput
     * @param $productsArray
     * @return float
     */
    private function getTotal($userInput , $productsArray) : float
    {
        $subTotal = 0;
        // we first array_column the products array
        $products = array_column($productsArray, 'name');
        // loop on the array provided by user
        foreach ($userInput as $name){
            // check if the input exists in the products
            if(!in_array($name , $products))  continue;
            // if its exist add the price
            $subTotal += search_array($name , $productsArray, 'name','price');
        }

        // return total
        return $subTotal;
    }

}