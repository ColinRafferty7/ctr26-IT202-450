<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: " . get_url("landing.php")));
}
?>

<?php
$id = se($_GET, "id", -1, false);
//TODO handle stock fetch
if (isset($_POST["name"])) {
    foreach ($_POST as $k => $v) {
        if (!in_array($k, ["name", "pokedex_id", "ability_1", "ability_2", "ability_3", "moves", "hp", "attack", "defense", "sp_attack", "sp_defense", "speed", "type_1", "type_2"])) {
            unset($_POST[$k]);
        }
        $pokemon = $_POST;
        $hasError = false;
        error_log("Cleaned up POST: " . var_export($pokemon, true));
    }

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

    // Ideally only the table name should need to change for most queries
    //update data
    $pokemon["id"] = $id; // add id to the stock array for the update
    if (!$hasError)
    {
        try {
            $name = $pokemon["name"];
            $r = update("IT202-Pokemon", $pokemon);
            if ($r["rowCount"]) {
                flash("Updated " . $r["rowCount"] . " record(s)", "success");
            } else {
                flash("Error updating record (this can occur if no properties changed)", "warning");
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

$pokemon = [];
if ($id > -1) {
    //fetch
    $db = getDB();
    $query = "SELECT id, name, pokedex_id, ability_1, ability_2, ability_3, moves, hp, attack, defense, sp_attack, sp_defense, speed, type_1, type_2, is_api FROM `IT202-Pokemon` WHERE id = :id";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute([":id" => $id]);
        $r = $stmt->fetch();
        if ($r) {
            $pokemon = $r;
        }
    } catch (PDOException $e) {
        error_log("Error fetching record: " . var_export($e, true));
        flash("Error fetching record", "danger");
    }
} else {
    flash("Invalid id passed", "danger");
    die(header("Location:" . get_url("admin/list_pokemon.php")));
}

?>

<script>
    function validateEdit(form)
    {
        let valid = true;

        if (form.name.value.trim() == "")
        {
            flash("JS: You must enter a pokemon name", "warning");
            valid = false;
        }
        if (isNaN(Number(form.pokedex_id.value)) || Number(form.pokedex_id.value) < 0)
        {
            flash("JS Edit: Pokedex ID must be a number greater than or equal to 0", "warning");
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
    <h3>Edit Pokemon</h3>
    <form method="POST" onsubmit="return validateEdit(this)">
        <div class="mb-3">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" placeholder="Name" value="<?php se($pokemon, "name"); ?>" required>
        </div>
        <div class="mb-3">
                <label for="pokedex_id">Pokedex ID</label>
                <input type="number" name="pokedex_id" id="pokedex_id" placeholder="Pokedex ID" value="<?php se($pokemon, "pokedex_id"); ?>" min="0" reqired>
        </div>
        <div class="mb-3">
                <label for="ability_1">Ability 1</label>
                <input type="text" name="ability_1" id="ability_1" placeholder="Ability 1" value="<?php se($pokemon, "ability_1"); ?>">
        </div>
        <div class="mb-3">
                <label for="ability_2">Ability 2</label>
                <input type="text" name="ability_2" id="ability_2" placeholder="Ability 2" value="<?php se($pokemon, "ability_2"); ?>">
        </div>
        <div class="mb-3">
                <label for="ability_3">Ability 3</label>
                <input type="text" name="ability_3" id="ability_3" placeholder="Ability 3" value="<?php se($pokemon, "ability_3"); ?>">
        </div>
        <div class="mb-3">
                <label for="moves">Moves</label>
                <input type="text" name="moves" id="moves" placeholder="Moves" value="<?php se($pokemon, "moves"); ?>">
        </div>
        <div class="mb-3">
                <label for="hp">HP</label>
                <input type="number" name="hp" id="hp" placeholder="HP" value="<?php se($pokemon, "hp"); ?>" min="1" max="255" required>
        </div>
        <div class="mb-3">
                <label for="attack">Attack</label>
                <input type="number" name="attack" id="attack" placeholder="Attack" value="<?php se($pokemon, "attack"); ?>" min="1" max="255" required>
            </div>
            <div class="mb-3">
                <label for="defense">Defense</label>
                <input type="number" name="defense" id="defense" placeholder="Defense" value="<?php se($pokemon, "defense"); ?>" min="1" max="255" required>
            </div>
            <div class="mb-3">
                <label for="sp_attack">Special Attack</label>
                <input type="number" name="sp_attack" id="sp_attack" placeholder="Special Attack" value="<?php se($pokemon, "sp_attack"); ?>" min="1" max="255" required>
            </div>
            <div class="mb-3">
                <label for="sp_defense">Special Defense</label>
                <input type="number" name="sp_defense" id="sp_defense" placeholder="Special Defense" value="<?php se($pokemon, "sp_defense"); ?>" min="1" max="255" required>
            </div>
            <div class="mb-3">
                <label for="speed">Speed</label>
                <input type="number" name="speed" id="speed" placeholder="Speed" value="<?php se($pokemon, "speed"); ?>" min="1" max="255" required>
            </div>
            <div class="mb-3">
                <label for="type_1">Type 1</label>
                <input type="text" name="type_1" id="type_1" placeholder="Type 1" value="<?php se($pokemon, "type_1"); ?>" required>
            </div>
            <div class="mb-3">
                <label for="type_2">Type 2</label>
                <input type="text" name="type_2" id="type_2" placeholder="Type 2" value="<?php se($pokemon, "type_2"); ?>">
        </div>
        <input type="submit" value="Update" class="btn btn-primary">
    </form>

</div>


<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/flash.php");
?>