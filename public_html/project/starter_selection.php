<?php
require(__DIR__ . "/../../partials/nav.php");
if (is_logged_in(true)) {
    error_log("Session data: " . var_export($_SESSION, true));
}
?>

<?php
$pokemon = [];
if (isset($_POST["selection"])) {
    //fetch
    $db = getDB();
    $query = "SELECT id, name, pokedex_id, ability_1, ability_2, ability_3, moves, hp, attack, defense, sp_attack, sp_defense, speed, type_1, type_2, is_api FROM `IT202-Pokemon` WHERE name = :name";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute([":name" => $_POST["selection"]]);
        $r = $stmt->fetch();
        if ($r) {
            $pokemon = $r;
        }
    } catch (PDOException $e) {
        error_log("Error fetching record: " . var_export($e, true));
        flash("Error fetching record", "danger");
    }
}

if (!empty($pokemon))
{
    $insert = "INSERT INTO `IT202-User-Pokemon` (user_id, species_name, hp, attack, defense, sp_attack, sp_defense, speed, type_1, type_2) 
        VALUES (:user_id, :species_name, :hp, :attack, :defense, :sp_attack, :sp_defense, :speed, :type_1, :type_2)";
    try {
        $stmt = $db->prepare($insert);
        $stmt->execute([
            ":user_id" => get_user_id(),
            ":species_name" => $pokemon["name"],
            ":hp" => $pokemon["hp"],
            ":attack" => $pokemon["attack"],
            ":defense" => $pokemon["defense"],
            ":sp_attack" => $pokemon["sp_attack"],
            ":sp_defense" => $pokemon["sp_defense"],
            ":speed" => $pokemon["speed"],
            ":type_1" => $pokemon["type_1"],
            ":type_2" => $pokemon["type_2"]
        ]);
        flash("Pokémon added to your team!", "success");
        die(header("Location: " . get_url("pokemon_box.php")));
    } catch (PDOException $e) {
        error_log("Error inserting into MyTeam: " . var_export($e, true));
        flash("Could not add Pokémon to team", "danger");
    }
}
?>

<h1 style="text-align: center; color: var(--bs-warning);">Start Your Adventure</h1>
<h3 style="text-align: center; color: var(--bs-light);">Which starter would you like?</h3>


<div class="container text-center">
  <div class="row align-items-center">
    <div class="col">
        <?php render_pokemon_select_card(createPokemonById("Bulbasaur")) ?>
    </div>
    <div class="col">
        <?php render_pokemon_select_card(createPokemonById("Charmander")) ?>
    </div>
    <div class="col">
        <?php render_pokemon_select_card(createPokemonById("Squirtle")) ?>
    </div>
  </div>
</div>