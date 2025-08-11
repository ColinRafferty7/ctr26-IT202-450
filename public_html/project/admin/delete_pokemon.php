<?php 
    require(__DIR__ . "/../../../partials/nav.php");

    if (!has_role("Admin")) 
        {
        flash("You don't have permission to view this page", "warning");
        die(header("Location: " . get_url("landing.php")));
    }
?>

<?php
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

    if ($_SERVER['REQUEST_METHOD'] === 'POST') 
    {
        if (isset($_POST['action']) && $_POST['action'] === 'delete') 
        {
            try 
            {
                $delete = $db->prepare("DELETE FROM `IT202-Pokemon` WHERE id = :id");
                if ($delete->execute([":id" => $id]))
                {
                    header("Location: " . get_url("landing.php"));
                }
            }
            catch (Exception $e)
            {

            }
        }
    }

    render_pokemon_card($pokemon); 
?>

<form method="POST">
  <button type="submit" name="action" value="delete">Delete</button>
</form>

<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/flash.php");
?>