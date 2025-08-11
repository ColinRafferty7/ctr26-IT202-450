<?php
require(__DIR__ . "/../../partials/nav.php");
if (is_logged_in(true)) {
    error_log("Session data: " . var_export($_SESSION, true));
}
$allowed_columns = ["name", "pokedex_id", "ability_1", "ability_2", "ability_3", "type_1", "type_2"];
$sort = ["asc", "desc"];

$params = [];
$query = "SELECT id, name, pokedex_id, ability_1, ability_2, ability_3, type_1, type_2, is_api FROM `IT202-Pokemon`
WHERE 1=1";// used for easy append of other clauses

if (!isset($_GET['column']) && !isset($_GET['order'])) {
    header("Location: ?column=pokedex_id&order=asc");
    exit;
}


if(count($_GET)> 0){
    $name = se($_GET, "name", "", false);
    if(!empty($symbol)){
        $query .= " AND name like :name";
        $params[":name"] = "%$name%";
    }
    $type_1 = se($_GET, "type_1", "", false);
    if(!empty($type_1)){
        $query .= " AND type_1 >= :type_1";
        $params[":type_1"] = $type_1;
    }
    $column = se($_GET, "column", "", false);
    if(empty($column) || !in_array($column, $allowed_columns)){
        $column = "pokedex_id";
    }
    $order = se($_GET, "order", "", false);
    if(empty($order) || !in_array($order, $sort)){
        $order = "asc";
    }
    // make sure values are trusted
    $query .= " ORDER BY $column $order";
    $limit = se($_GET, "limit", 10, false);
    if(!empty($limit) && is_numeric($limit)){
        if($limit < 1 || $limit > 100){
            $limit = 10;
        }   
        $query .= " LIMIT :limit";
        $params[":limit"] = $limit;
    }
}
$db = getDB();
$stmt = $db->prepare($query);
error_log("Query: " . $query);
error_log("Params: " . var_export($params, true));
foreach($params as $key=>$v){
    // determine PDOPAram type
    $type = match (true) {
        is_numeric($v)   => PDO::PARAM_INT,
        is_bool($v)  => PDO::PARAM_BOOL,
        is_null($v)  => PDO::PARAM_NULL,
        default          => PDO::PARAM_STR,
    };
    $stmt->bindValue("$key", $v,$type);
}
$results = [];
try {
    $stmt->execute();
    $r = $stmt->fetchAll();
    if ($r) {
        $results = $r;
    }
} catch (PDOException $e) {
    error_log("Error fetching stocks " . var_export($e, true));
    flash("Unhandled error occurred", "danger");
}
// TODO filter/sort (last resort if brokers aren't added in this lesson)

// form field for symbol
// form field date range for type_1
// form field for is_api
// form field for column names
// form field for asc/desc

$cols = array_map(function ($col) {
    return [$col => $col];
}, $allowed_columns);
array_unshift($cols, [""=>"Select Column"]);
$order = array_map(function ($col) {
    return [$col => $col];
}, $sort);
array_unshift($order, [""=>"Select Order"]);
$form = [
    [
        "type" => "text",
        "id" => "name",
        "name" => "name",
        "label" => "Pokemon name",
        "value"=> se($_GET, "name", "", false),
    ],
    [
        "type" => "number",
        "id" => "pokedex_id",
        "name" => "pokedex_id",
        "label" => "Pokedex ID",
        "value"=> se($_GET, "pokedex_id", "", false),
    ],
    [
        "type" => "select",
        "id" => "column",
        "name" => "column",
        "label" => "Column",
        "options" => $cols,
        "value" => se($_GET, "column", "", false),
    ],
    [
        "type" => "select",
        "id" => "order",
        "name" => "order",
        "label" => "Order",
        "options" => $order,
        "value" => se($_GET, "order", "", false),
    ],
    [
        "type"=>"number",
        "id"=>"limit",
        "name"=>"limit",
        "label"=>"Limit",
        "value"=>se($_GET, "limit", "10", false),
        "rules"=>["min"=>1, "max"=>100]
    ]
]
?>
<div class="container-fluid">
    <h1>Pokedex</h1>
    <div>
        <form>
            <div class="row">
            <?php foreach ($form as $field): ?>
                <div class="col">
                    <?php render_input($field); ?>
                </div>
            <?php endforeach; ?>
            </div>
            <?php render_button(["text" => "Search", "type" => "submit"]); ?>
            <!-- Uses `?` to remove all query params (normal reset doesn't work here
             because a regular reset "resets" back to the values the form loaded in with.
             Sticky forms will "reset" to what was last applied) -->
            <a href="?" class="btn btn-secondary">Reset</a>
        </form>
    </div>  
    <?php if (count($results) == 0) : ?>
        <p>No results to show</p>
    <?php else : ?>
        <div class="row">
            <?php foreach ($results as $stock): ?>
                <div class="col">
                    <?php render_pokemon_card($stock); ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php
require(__DIR__ . "/../../partials/flash.php");
?>