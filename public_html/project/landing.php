<?php
require(__DIR__ . "/../../partials/nav.php");
if (is_logged_in(true)) {
    error_log("Session data: " . var_export($_SESSION, true));
}
?>

<?php
    function createPokemonById($name)
    {
        $id = se($_GET, "name", $name, false);
        $pokemon = [];
        $db = getDB();
        $query = "SELECT id, name, pokedex_id, ability_1, ability_2, ability_3, type_1, type_2, is_api FROM `IT202-Pokemon` WHERE name = :name";
        try {
            $stmt = $db->prepare($query);
            $stmt->execute([":name" => $name]);
            $r = $stmt->fetch();
            if ($r) {
                $pokemon = $r;
            }
            return $pokemon;
        } catch (PDOException $e) {
            error_log("Error fetching record: " . var_export($e, true));
            flash("Error fetching record", "danger");
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