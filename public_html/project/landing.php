<?php
require(__DIR__ . "/../../partials/nav.php");
if (is_logged_in(true)) {
    error_log("Session data: " . var_export($_SESSION, true));
}
?>

<?php
$team = [];
$db = getDB();
$query = "SELECT species_name FROM `IT202-User-Pokemon` WHERE user_id = :user_id";
try {
    $stmt = $db->prepare($query);
    $stmt->execute([":user_id" => get_user_id()]);
    $r = $stmt->fetchAll();
    if ($r) {
        $team = $r;
    }
} catch (PDOException $e) {
    error_log("Error fetching record: " . var_export($e, true));
    flash("Error fetching record", "danger");
}
?>

<h1 style="text-align: center; color: var(--bs-warning);">Your Team</h1>

<div class="container text-center">
  <div class="row align-items-center">
    <?php foreach($team as $member) : ?>
        <div class="col">
            <?php render_pokemon_select_card(createPokemonById(se($member, "species_name"))) ?>
        </div>
    <?php endforeach; ?>
  </div>
</div>