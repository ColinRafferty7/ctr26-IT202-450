<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: " . get_url("landing.php")));
}
?>

<?php

//TODO handle stock fetch
if (isset($_POST["action"])) {
    $action = $_POST["action"];
    $name =  se($_POST, "name", "", false);
    $pokemon = [];
    if ($name) {
        if ($action === "fetch") {
            $result = fetch_pokemon($name);

            error_log("Data from API" . var_export($result, true));
            if ($result) {
                $pokemon = $result;
                $pokemon["is_api"] = 1;
            }
        } else if ($action === "create") {
            foreach ($_POST as $k => $v) {
                // remove keys that aren't part of your data
                // this is both for security and for our dynamic DB logic to work correctly
                // the keys must match the column names of your table
                if (!in_array($k, ["name", "pokedex_id", "ability_1", "ability_2", "ability_3", "moves", "hp", "attack", "defense", "sp_attack", "sp_defense", "speed", "type_1", "type_2"])) {
                    unset($_POST[$k]);
                }
            }
            $pokemon = $_POST;
            $pokemon["is_api"] = 0;
            error_log("Cleaned up POST: " . var_export($pokemon, true));
        }
    } else {
        flash("You must provide a pokemon", "warning");
    }
    //insert data - Below should only really need the table name changes
    // the query building should work for all regular inserts
    $db = getDB();
    $query = "INSERT INTO `IT202-Pokemon` ";
    $columns = [];
    $params = [];
    //per record
    foreach ($pokemon as $k => $v) {
        array_push($columns, "`$k`");
        $params[":$k"] = $v;
    }
    $query .= "(" . join(",", $columns) . ")";
    $query .= "VALUES (" . join(",", array_keys($params)) . ")";
    error_log("Query: " . $query);
    error_log("Params: " . var_export($params, true));
    try {
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        flash("Inserted record " . $db->lastInsertId(), "success");
    } catch (PDOException $e) {
        error_log("Something broke with the query" . var_export($e, true));
        flash("An error occurred", "danger");
    }
}

//TODO handle manual create stock
?>
<div class="container-fluid">
    <h3>Create or Fetch Stock</h3>
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link bg-success" href="#" onclick="switchTab('create')">Fetch</a>
        </li>
        <li class="nav-item">
            <a class="nav-link bg-success" href="#" onclick="switchTab('fetch')">Create</a>
        </li>
    </ul>
    <div id="fetch" class="tab-target">
        <form method="POST">
            <div>
                <label for="name">Pokemon</label>
                <input type="search" name="name" id="name" placeholder="Pokemon" required>
            </div>
            <input type="hidden" name="action" value="fetch">
            <input type="submit" value="Fetch" class="btn btn-primary">
        </form>
    </div>
    <div id="create" style="display: none;" class="tab-target">
        <form method="POST">
            <div class="mb-3">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" placeholder="Name" required>
            </div>
            <div class="mb-3">
                <label for="pokedex_id">Pokedex ID</label>
                <input type="number" name="pokedex_id" id="pokedex_id" placeholder="Pokedex ID" required>
            </div>
            <div class="mb-3">
                <label for="ability_1">Ability 1</label>
                <input type="text" name="ability_1" id="ability_1" placeholder="Ability 1" required>
            </div>
            <div class="mb-3">
                <label for="ability_2">Ability 2</label>
                <input type="text" name="ability_2" id="ability_2" placeholder="Ability 2" required>
            </div>
            <div class="mb-3">
                <label for="ability_3">Ability 3</label>
                <input type="text" name="ability_3" id="ability_3" placeholder="Ability 3" required>
            </div>
            <div class="mb-3">
                <label for="moves">Moves</label>
                <input type="text" name="moves" id="moves" placeholder="Moves" required>
            </div>
            <div class="mb-3">
                <label for="hp">HP</label>
                <input type="number" name="hp" id="hp" placeholder="HP" required>
            </div>
            <div class="mb-3">
                <label for="attack">Attack</label>
                <input type="number" name="attack" id="attack" placeholder="Attack" required>
            </div>
            <div class="mb-3">
                <label for="defense">Defense</label>
                <input type="number" name="defense" id="defense" placeholder="Defense" required>
            </div>
            <div class="mb-3">
                <label for="sp_attack">Special Attack</label>
                <input type="number" name="sp_attack" id="sp_attack" placeholder="Special Attack" required>
            </div>
            <div class="mb-3">
                <label for="sp_defense">Special Defense</label>
                <input type="number" name="sp_defense" id="sp_defense" placeholder="Special Defense" required>
            </div>
            <div class="mb-3">
                <label for="speed">Speed</label>
                <input type="number" name="speed" id="speed" placeholder="Speed" required>
            </div>
            <div class="mb-3">
                <label for="type_1">Type 1</label>
                <input type="text" name="type_1" id="type_1" placeholder="Type 1" required>
            </div>
            <div class="mb-3">
                <label for="type_2">Type 2</label>
                <input type="text" name="type_2" id="type_2" placeholder="Type 2" required>
            </div>
            <input type="hidden" name="action" value="create">
            <input type="submit" value="Create" class="btn btn-primary">
        </form>
    </div>
</div>
<script>
    function switchTab(tab) {
        let target = document.getElementById(tab);
        if (target) {
            let eles = document.getElementsByClassName("tab-target");
            for (let ele of eles) {
                ele.style.display = (ele.id === tab) ? "none" : "block";
            }
        }
    }
</script>

<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/flash.php");
?>