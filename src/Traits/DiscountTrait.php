<?php


namespace App\Traits;


trait DiscountTrait
{

    /**
     * getOffers
     * getOffers used to calculate users discounts, return text for the discount output
     * the function accepts 3 parameters:
     * 1 .  user input
     * 2 .  available offers
     * 3 .  available products
     * and it returns array contains:
     * 1 . the net discount of all products
     * 2 . array of formatted text
     * @param $userInput
     * @param $availableOffers
     * @param $products
     * @param $currency
     * @return array
     */
    public function getOffers($userInput ,  $availableOffers , $products , $currency) : array
    {
        $totalDiscount = 0 ;
        $doubleOffers = [];
        $discountText = [];

        // convert names of available offers to one array
        $offers = array_column( $availableOffers,'name');

        // loop on the products given by the user to check if the inputs have any offers to give
        foreach ($userInput as $name) {
            // check if the product has an offer with type 1
            // offers with type 1 have a direct discount on it with a specific percent
            if(
                in_array($name  ,$offers) &&
                search_array($name ,  $availableOffers , 'name' , 'type') == 1
            ) {
                // if the offer of type 1 we pass the name of the product to getNormalDiscount() function
                // the function return the discount and formatted text
                $result =  $this->getNormalDiscount($name , $availableOffers , $products , $currency);
                // then we add the discount to total discounts
                $totalDiscount += $result[0];
                // push the formatted text of discount to the array of texts
                array_push($discountText , $result[1]);
            }

            // if the product has no offer of type 1
            // we check if it has an offer of type 2
            // offers with type 2 give discount with percent on specific product if the user buy 2 pieces of another product
            if(
                in_array($name  ,$offers) &&
                search_array($name ,  $availableOffers , 'name', 'type') == 2
            ){
                // if the product has an offer of type 2
                // we push the product to the doubleOffers array to perform calculations on it
                array_push($doubleOffers ,  $name );
            }
        }

        // check if the user have any offers of type 2
        if(count($doubleOffers)){
            // if  the user deserves offers of type 2
            // we pass the deserved offers, user input, available offers and products to getDoubleOffer function
            // the function make calculations on the user input and return total discounts and array of text for the discounts
            $result = $this->getDoubleOffer($doubleOffers , $userInput, $availableOffers , $products , $currency);

            // then we add the discount to total discounts
            $totalDiscount += $result[0];
            // then we loop on the array of texts and append each text to the array of discounts Text
            foreach ($result[1] as $item){
                array_push($discountText , $item);
            }
        }

        // finally we return the total discount and array of texts for deserved offers
        return array(
            $totalDiscount,
            $discountText
        );
    }

    /**
     * getNormalDiscount
     * the function calculate offers of type 1
     * it accepts  3 parameters :
     * 1 . name of the product
     * 2 . available offers
     * 3 . available products
     * @param $name
     * @param $availableOffers
     * @param $products
     * @param $currency
     * @return array
     */
    public function getNormalDiscount($name , $availableOffers , $products , $currency) : array
    {
        // first we get the offer percent
        $discountPercent = search_array($name , $availableOffers , 'name' , 'offer');
        // then we get the price of original product
        $price = search_array($name , $products ,'name', 'price');

        // we calculate the discount amount of the original price
        $discountAmount = $price * $discountPercent / 100;

        // then add text for the discount
        $logMessage = $this->formatText($discountPercent , $name ,  $discountAmount , $currency);

        // finally we return the price of discount and log message
        return array(
            $discountAmount ,
            $logMessage
        );
    }


    /**
     * getDoubleOffer
     * the function calculate the offers of type 2
     *
     * the offers of type 2 give the user discount with percent on a specific product
     * when he buys 2 pieces of another product
     *
     * the function make sure that the user buy 1 piece of the product with discount for every 2 pieces of the original product
     * before he can have the discount
     *
     * it accepts 4 parameters:
     * 1 . the offers of type 2 the gained by the user
     * 2 . the user input
     * 3 . the available offers
     * 4 . the available products
     *
     * it returns an array of two values
     * 1 . total discount
     * 2 . array of discount texts
     * @param $userOffers
     * @param $userInput
     * @param $offers
     * @param $products
     * @param $currency
     * @return array
     */
    public function getDoubleOffer($userOffers , $userInput , $offers , $products ,$currency) : array
    {
        $discountText = [];
        $totalDiscount = 0 ;

        // first we loop on the offers the user deserve
        foreach (array_count_values($userOffers) as $offer => $key){
            // then we retrieve the product with the discount
            $prize = search_array(  $offer , $offers , 'name', 'on');

            // check if the user already purchase the product with discount
            if(in_array($prize , $userInput)){
                // we calculate the number of the product
                $count = array_count_values($userInput)[$prize];

                // we retrieve the percent and the price of the product
                $percent = search_array($offer , $offers , 'name', 'offer');
                $price = search_array($prize , $products,  'name','price');

                // we calculate the discount
                $after = $price * $percent / 100;

                // then for every 2 pieces of the original product
                // we give the user discount if he buy 1 piece of the product with discount
                for($i = 2 ; $i <= $count * 2; $i += 2){
                    // add the discount to total discounts
                    $totalDiscount += $after;
                    // push the text to the array of texts
                    array_push($discountText , $this->formatText($percent , $prize , $after , $currency));
                }
            }
        }

        // finally we return total discount and array of texts
        return array(
            $totalDiscount ,
            $discountText
        );
    }

    /**
     * format text for discount lines
     * @param $percent
     * @param $productName
     * @param $discountAmount
     * @param $currency
     * @return string
     */
    public function formatText($percent , $productName , $discountAmount , $currency) : string
    {
        return "$percent % off $productName: - $currency $discountAmount";
    }


}