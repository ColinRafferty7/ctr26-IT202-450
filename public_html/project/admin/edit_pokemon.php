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
        error_log("Cleaned up POST: " . var_export($pokemon, true));
    }
    // Ideally only the table name should need to change for most queries
    //update data
    $pokemon["id"] = $id; // add id to the stock array for the update
    try {
        $name = $pokemon[0];
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
<div class="container-fluid">
    <h3>Edit Stock</h3>
    <form method="POST">
        <div class="mb-3">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" placeholder="Name" required value="<?php se($pokemon, "name"); ?>">
        </div>
        <div class="mb-3">
                <label for="pokedex_id">Pokedex ID</label>
                <input type="number" name="pokedex_id" id="pokedex_id" placeholder="Pokedex ID" required value="<?php se($pokemon, "pokedex_id"); ?>">
        </div>
        <div class="mb-3">
                <label for="ability_1">Ability 1</label>
                <input type="text" name="ability_1" id="ability_1" placeholder="Ability 1" required value="<?php se($pokemon, "ability_1"); ?>">
        </div>
        <div class="mb-3">
                <label for="ability_2">Ability 2</label>
                <input type="text" name="ability_2" id="ability_2" placeholder="Ability 2" required value="<?php se($pokemon, "ability_2"); ?>">
        </div>
        <div class="mb-3">
                <label for="ability_3">Ability 3</label>
                <input type="text" name="ability_3" id="ability_3" placeholder="Ability 3" required value="<?php se($pokemon, "ability_3"); ?>">
        </div>
        <div class="mb-3">
                <label for="moves">Moves</label>
                <input type="text" name="moves" id="moves" placeholder="Moves" required value="<?php se($pokemon, "moves"); ?>">
        </div>
        <div class="mb-3">
                <label for="hp">HP</label>
                <input type="number" name="hp" id="hp" placeholder="HP" required value="<?php se($pokemon, "hp"); ?>">
        </div>
        <div class="mb-3">
                <label for="attack">Attack</label>
                <input type="number" name="attack" id="attack" placeholder="Attack" required value="<?php se($pokemon, "attack"); ?>">
            </div>
            <div class="mb-3">
                <label for="defense">Defense</label>
                <input type="number" name="defense" id="defense" placeholder="Defense" required value="<?php se($pokemon, "defense"); ?>">
            </div>
            <div class="mb-3">
                <label for="sp_attack">Special Attack</label>
                <input type="number" name="sp_attack" id="sp_attack" placeholder="Special Attack" required value="<?php se($pokemon, "sp_attack"); ?>">
            </div>
            <div class="mb-3">
                <label for="sp_defense">Special Defense</label>
                <input type="number" name="sp_defense" id="sp_defense" placeholder="Special Defense" required value="<?php se($pokemon, "sp_defense"); ?>">
            </div>
            <div class="mb-3">
                <label for="speed">Speed</label>
                <input type="number" name="speed" id="speed" placeholder="Speed" required value="<?php se($pokemon, "speed"); ?>">
            </div>
            <div class="mb-3">
                <label for="type_1">Type 1</label>
                <input type="text" name="type_1" id="type_1" placeholder="Type 1" required value="<?php se($pokemon, "type_1"); ?>">
            </div>
            <div class="mb-3">
                <label for="type_2">Type 2</label>
                <input type="text" name="type_2" id="type_2" placeholder="Type 2" required value="<?php se($pokemon, "type_2"); ?>">
        </div>
        <input type="submit" value="Update" class="btn btn-primary">
    </form>

</div>


<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/flash.php");
?>