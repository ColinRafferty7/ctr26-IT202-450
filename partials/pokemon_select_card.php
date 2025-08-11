<?php
if (!isset($data)) {
    error_log("Using Pokemon card partial without data");
    flash("Dev Alert: Pokemon card called without data", "danger");
}
?>

<?php if (isset($data)) : ?>
    <div class="card mx-auto my-3 bg-dark" style="width: 20rem;">
        <div class="ratio ratio-1x1 d-flex justify-content-center  " style="height:64px">
            <img src="https://raw.githubusercontent.com/May8th1995/sprites/master/<?php se($data, "name", "Unown"); ?>.png"
            class="img-fluid object-fit-contain">
        </div>
        <div class="card-body bg-dark">
            <h5 class="card-title text-secondary"><?php se($data, "name", "Unknown"); ?></h5>
            <div class="card-text">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">Pokedex ID: <?php se($data, "pokedex_id", "N/A"); ?></li>
                    <li class="list-group-item">Type 1: <?php se($data, "type_1", "N/A"); ?></li>
                    <li class="list-group-item">Type 2: <?php se($data, "type_2", "N/A"); ?></li>
                    <form method="POST">
                        <input type="hidden" name="selection" value="<?php se($data, "name") ?>">
                        <?php render_button(["text" => "Select", "type" => "submit"]); ?>
                    </form>
                </ul>
            </div>
        </div>
    </div>
<?php endif; ?>

<style>
    .list-group-item
    {
        background-color: var(--bs-secondary);
        border-color: var(--bs-dark);
    }
</style>

<?php
require_once(__DIR__ . "/flash.php");
?>