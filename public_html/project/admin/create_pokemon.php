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
    $hasError = false;
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

            //validation
            if (!is_numeric($pokemon["pokedex_id"]) || $pokemon["pokedex_id"] < 0)
            {
               flash("PHP: Pokedex ID must be a number greater than or equal to 0", "warning");
               $hasError = true; 
            }
            $stats = ["hp", "attack", "defense", "sp_attack", "sp_defense", "speed"];
            foreach ($stats as $k)
            {
                if ($pokemon[$k] < 1 || $pokemon[$k] > 255)
                {
                    flash("PHP: Stats must be between 1 and 255", "warning");
                    $hasError = true;
                    break;
                }
            }
            if ($pokemon["type_1"] == "")
            {
                flash("PHP: Your pokemon must have a type", "warning");
                $hasError = true;
            }
        }
    } else {
        flash("PHP: You must provide a pokemon", "warning");
        $hasError = true;
    }
    //insert data - Below should only really need the table name changes
    // the query building should work for all regular inserts
    if (!$hasError)
    {
        try {
            $name = $pokemon["name"];
            $r = insert("IT202-Pokemon", $pokemon, ["update_duplicate"=>true]);
            if ($r["lastInsertId"]) {
                flash("Inserted record " . $r["lastInsertId"], "success");
            } else {
                flash("Error inserting record", "warning");
            }
        } catch (PDOException $e) {
            error_log("Something broke with the query" . var_export($e, true));
            flash("An error occurred", "danger");
        }
        catch(Exception $e) {
            error_log("Something broke with the query" . var_export($e, true));
            flash("An error occurred: " . $e->getMessage(), "danger");
        }
    }
}

//TODO handle manual create stock
?>

<script>
    function validateCreate(form)
    {
        let valid = true;

        if (form.name.value.trim() == "")
        {
            flash("JS: You must enter a pokemon name", "warning");
            valid = false;
        }
        if (isNaN(Number(form.pokedex_id.value)) || Number(form.pokedex_id.value) < 0)
        {
            flash("JS Create: Pokedex ID must be a number greater than or equal to 0", "warning");
            valid = false;
        }
        let stats = [Number(form.hp.value), Number(form.attack.value), Number(form.defense.value), Number(form.sp_attack.value), Number(form.sp_defense.value), Number(form.speed.value)];
        for (const stat of stats)
        {
            if (stat < 1 || stat > 255)
            {
                flash("JS: Stats must be between 1 and 255", "warning");
                valid = false;
                break;
            }
        }
        if (form.type_1.value.trim() == "")
        {
            flash("JS: Pokemon must have a type", "warning");
            valid = false;
        }
        return valid;
    }
</script>

<div class="container-fluid">
    <h3>Create or Fetch Pokemon</h3>
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link bg-primary" href="#" onclick="switchTab('create')">Fetch</a>
        </li>
        <li class="nav-item">
            <a class="nav-link bg-primary" href="#" onclick="switchTab('fetch')">Create</a>
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
        <form method="POST" onsubmit="return validateCreate(this)">
            <div class="mb-3">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" placeholder="Name" required>
            </div>
            <div class="mb-3">
                <label for="pokedex_id">Pokedex ID</label>
                <input type="number" name="pokedex_id" id="pokedex_id" placeholder="Pokedex ID" min="0" value="0" required>
            </div>
            <div class="mb-3">
                <label for="ability_1">Ability 1</label>
                <input type="text" name="ability_1" id="ability_1" placeholder="Ability 1">
            </div>
            <div class="mb-3">
                <label for="ability_2">Ability 2</label>
                <input type="text" name="ability_2" id="ability_2" placeholder="Ability 2">
            </div>
            <div class="mb-3">
                <label for="ability_3">Ability 3</label>
                <input type="text" name="ability_3" id="ability_3" placeholder="Ability 3">
            </div>
            <div class="mb-3">
                <label for="moves">Moves</label>
                <input type="text" name="moves" id="moves" placeholder="Moves">
            </div>
            <div class="mb-3">
                <label for="hp">HP</label>
                <input type="number" name="hp" id="hp" placeholder="HP" min="1" max="255"  value="0" required>
            </div>
            <div class="mb-3">
                <label for="attack">Attack</label>
                <input type="number" name="attack" id="attack" placeholder="Attack" min="1" max="255" value="0" required>
            </div>
            <div class="mb-3">
                <label for="defense">Defense</label>
                <input type="number" name="defense" id="defense" placeholder="Defense" min="1" max="255" value="0" required>
            </div>
            <div class="mb-3">
                <label for="sp_attack">Special Attack</label>
                <input type="number" name="sp_attack" id="sp_attack" placeholder="Special Attack" min="1" max="255" value="0" required>
            </div>
            <div class="mb-3">
                <label for="sp_defense">Special Defense</label>
                <input type="number" name="sp_defense" id="sp_defense" placeholder="Special Defense" min="1" max="255" value="0" required>
            </div>
            <div class="mb-3">
                <label for="speed">Speed</label>
                <input type="number" name="speed" id="speed" placeholder="Speed" min="1" max="255" value="0" required>
            </div>
            <div class="mb-3">
                <label for="type_1">Type 1</label>
                <input type="text" name="type_1" id="type_1" placeholder="Type 1" required>
            </div>
            <div class="mb-3">
                <label for="type_2">Type 2</label>
                <input type="text" name="type_2" id="type_2" placeholder="Type 2">
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