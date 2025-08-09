<?php
require(__DIR__ . "/base.php");
$a1 = [
    ["id" => 1, "make" => "Toyota", "model" => "Camry", "year" => 2010],
    ["id" => 2, "make" => "Honda", "model" => "Civic", "year" => 2005]
];

$a2 = [
    ["id" => 3, "make" => "Ford", "model" => "Mustang", "year" => 1995],
    ["id" => 4, "make" => "Chevrolet", "model" => "Impala", "year" => 2000]
];

$a3 = [
    ["id" => 5, "make" => "Nissan", "model" => "Altima", "year" => 2015],
    ["id" => 6, "make" => "BMW", "model" => "3 Series", "year" => 2018]
];

$a4 = [
    ["id" => 7, "make" => "Mercedes", "model" => "C Class", "year" => 2011],
    ["id" => 8, "make" => "Audi", "model" => "A4", "year" => 1990]
];

function processCars($cars) {
    printProblemData($cars);
    echo "<br>New Properties Output:<br>";
    
    // Note: use the $cars variable to iterate over, don't directly touch $a1-$a4
    // TODO Objective: Add logic to create a new array ($processedCars) with original properties plus age and isClassic. isClassic is a boolean based on today\'s year and the $classic_age variable.
    $currentYear = null; // determine current year
    $processedCars = []; // result array
    $classic_age = 25; // don't change this value
    // Start edits
    // ctr26 07/13/2025
    // Set the current year to 2025
    // For each member of cars, add a new array into processed cars
    // Use an indexed for each loop to add each key value pair into the new array
    // Use the newly added data to get the current age of the car and set the classic variable

    $currentYear = 2025;

    foreach ($cars as $index => $car)
    {
        $processedCars[$index] = [];
        foreach ($car as $key => $value)
        {
            $processedCars[$index][$key] = $value;
        }
        $processedCars[$index]["age"] = ($currentYear - $processedCars[$index]["year"]);
        $processedCars[$index]["isClassic"] = ($processedCars[$index]["age"] >= 25);
    }
   
    // End edits
    echo "<pre>" . var_export($processedCars, true) . "</pre>";
    
}
$ucid = "ctr26"; // replace with your UCID
printHeader($ucid, 2); 
?>
<table>
    <thead>
        <tr>
            <th>A1</th>
            <th>A2</th>
            <th>A3</th>
            <th>A4</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                <?php processCars($a1); ?>
            </td>
            <td>
                <?php processCars($a2); ?>
            </td>
            <td>
                <?php processCars($a3); ?>
            </td>
            <td>
                <?php processCars($a4); ?>
            </td>
        </tr>
    </tbody>
</table>
<?php printFooter($ucid,2); ?>
<style>
    table {
        border-spacing: 1em 3em;
        border-collapse: separate;
    }

    td {
        border-right: solid 1px black;
        border-left: solid 1px black;
    }
</style>