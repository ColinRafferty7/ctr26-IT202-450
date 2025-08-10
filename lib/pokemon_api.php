<?php

/**
 * This file is a wrapper for our API calls.
 * Here, each endpoint needed will be exposes as a function.
 * The function will take the parameters needed for the API call and return the result.
 * The function will also handle the API key and endpoint.
 * Requires the api_helper.php file and load_api_keys.php file.
 */

/**
 * Fetches the stock quote for a given symbol.
 */
function fetch_pokemon($pokemon)
{
    $data = ["name" => $pokemon];
    $endpoint = "https://pokemon-data-api.p.rapidapi.com/api/pokemon_data";
    $isRapidAPI = true;
    $rapidAPIHost = "pokemon-data-api.p.rapidapi.com";
    $result = get($endpoint, "POKEMON_API_KEY", $data, $isRapidAPI, $rapidAPIHost);
    //example of cached data to save the quotas, don't forget to comment out the get() if using the cached data for testing
    /* $result = ["status" => 200, "response" => '{
        name:"nidoqueen"
        ability_1:"poison-point"
        ability_2:"rivalry"
        ability_3:"sheer-force"
        pokedex_id:31
        moves:"mega punch, pay day, fire punch, ice punch, thunder punch..."
        stats:
            hp:90
            attack:92
            defense:87
            sp_attack:75
            sp_defense:85
            speed:76
        type_1:"poison"
        type_2:"ground"
    }'];*/
    error_log("API Response: " . var_export($result, true));
    if (se($result, "status", 400, false) == 200 && isset($result["response"])) {
        $result = json_decode($result["response"], true);
    } else {
        $result = [];
    }
    $transformedResult = [];
    foreach ($result as $k => $v) {
        // remove the numbers from the keys and fix spaces to underscores
        // "01. symbol"
        //["01.", "symbol"]
        if (!is_array($v))
        {
            $transformedResult[$k] = $v;
        }
        else 
        {
            foreach ($v as $k2 => $v2)
            {
                $transformedResult[$k2] = $v2;
            }
        }
    }
    return $transformedResult;
}