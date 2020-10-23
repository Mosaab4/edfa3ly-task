# Take Home Challenge

the application is a CLI program to calculate the price of a cart provided by the user.

#### Table of contents
- [Requirements](#requirements)
- [Installation](#installation)
- [Usage](#usage)
    - [Available Commands](#available-commands)
        - [showProducts](#showproducts)
        - [createCart](#createcart)
- [Structure](#structure)
- [Todolist](#todolist)


### Requirements
To implement the Task I used:  

1. [symfony's](https://symfony.com/doc/current/console.html) console component.
2. php 7.4.3 .
3. composer as dependency manager.  

### Installation

You will need to have `php` to run the commands and `composer` to install the required dependencies.

You need to have a copy of the repository on your local machine.

Then `cd ` to the directory of the project.

run `composer install` to install required dependencies.


Once the installation completed, you can run any command from the available commands by hitting :

```bash
php console.php <command_name>
```
 
### Usage

The program uses json file to store the products.

Available products in this time :
* T-shirt 10.99
* Pants 14.99
* Jacket 19.99
* Shoes 24.99

Available Offers are :
* Shoes are on 10% off.
* Buy two t-shirts and get a jacket half its price.

Available currencies are :
* USD
* EGP

you can pass `--bill-currency` argument to any command to change the currency with one of available values.

if it's not provided the program use USD as Default.

##### Available Commands:

###### showProducts

The command lists all available products.

To execute the command run : 
```
php console.php showProducts
``` 

###### createCart

The command accepts multiple arguments as products 
```bash
php console.php createCart <porduct1> <product2> ....
```

Then it calculates the sub total, discounts, tax and total price of the products.

### Structure

```bash
├── composer.json
├── console.php                                 // run the application and reigster commands
├── products.json                               // json file for the products
├── README.md
├── src
     ├── Command
         ├── CreateCartCommand.php              // create cart command class
         └── ShowProductCommand.php             // show products command class
     ├── Helpers
         └── helpers.php                        // helper functions
     └── Traits
         └── DiscountTrait.php                  // responsible for handling discounts

```

### Todolist
- [ ] Create a command for creating new products by appending the product and price to the json files
- [ ] Create a command for displaying and adding new offers
- [ ] use json files for offers
- [ ] add a new feature to enable the tool to access json api for more products and offers 

