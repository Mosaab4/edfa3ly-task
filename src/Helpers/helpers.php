<?php

if(!function_exists('search_array'))
{
    /**
     * this helper function searches array
     * the mechanism of this function that it accepts
     *
     * 1 . the value you want to search the array for it
     * 2 . the array
     * 3 . the key you want it to search with
     * 4 . the key you want to retrieve its value
     *
     * @param $searchValue
     * @param $searchedArray
     * @param $keyName
     * @param $returnValue
     * @return mixed
     */
    function search_array($searchValue , $searchedArray , $keyName , $returnValue )
    {
        // search the array with the given value by the key name
        $key = array_search($searchValue , array_column($searchedArray , $keyName));
        // extract the index of the value and return the value of the wanted key
        return  $searchedArray[$key][$returnValue];
    }
}