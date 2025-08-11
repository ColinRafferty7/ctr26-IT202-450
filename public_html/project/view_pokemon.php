<?php 
    require(__DIR__ . "/../../partials/nav.php");
    $id = se($_GET, "id", -1, false);
    $pokemon = [];
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
    render_pokemon_card($pokemon); 
?>