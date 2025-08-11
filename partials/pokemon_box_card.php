<?php
if (!isset($data)) {
    error_log("Using Pokemon card partial without data");
    flash("Dev Alert: Pokemon card called without data", "danger");
}
?>
<?php if (isset($data)) : ?>
    <div class="card mx-auto my-3 bg-dark" style="width: 20rem;">
        <div class="ratio ratio-1x1 d-flex justify-content-center  " style="height:64px">
            <img src="https://raw.githubusercontent.com/May8th1995/sprites/master/<?php se($data, "species_name", "Unown"); ?>.png"
            class="img-fluid object-fit-contain">
        </div>
        <div class="card-body bg-dark">
            <h5 class="card-title text-secondary"><?php se($data, "species_name", "Unknown"); ?></h5>
            <div class="card-text">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Nickname: <?php se($data, "nickname", "N/A"); ?>
                        <span>
                            <button class="btn btn-sm btn-outline-primary bg-primary text-light" onclick="toggleNicknameEdit()">Edit</button>
                        </span>
                    </li>
                    <li class="list-group-item">Type 1: <?php se($data, "type_1", "N/A"); ?></li>
                    <li class="list-group-item">Type 2: <?php se($data, "type_2", "N/A"); ?></li>
                    <li class="list-group-item">HP: <?php se($data, "hp", "N/A"); ?></li>
                    <li class="list-group-item">Attack: <?php se($data, "attack", "N/A"); ?></li>
                    <li class="list-group-item">Defense: <?php se($data, "defense", "N/A"); ?></li>
                    <li class="list-group-item">Special Attack: <?php se($data, "sp_attack", "N/A"); ?></li>
                    <li class="list-group-item">Special Defense: <?php se($data, "sp_defense", "N/A"); ?></li>
                    <li class="list-group-item">Speed: <?php se($data, "speed", "N/A"); ?></li>
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