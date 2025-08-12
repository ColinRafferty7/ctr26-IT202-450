<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: " . get_url("landing.php")));
}
//attempt to apply
if (isset($_POST["users"], $_POST["roles"])) {
    $user_ids = $_POST["users"]; //se() doesn't like arrays so we'll just do this
    $role_ids = $_POST["roles"]; //se() doesn't like arrays so we'll just do this
    if (empty($user_ids) || empty($role_ids)) {
        flash("Both users and roles need to be selected", "warning");
    } else {
        //for sake of simplicity, this will be a tad inefficient (normally bulk operations should fail/pass together)
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO UserRoles (user_id, role_id, is_active) VALUES (:uid, :rid, 1) 
        ON DUPLICATE KEY UPDATE is_active = !is_active");

        // triggers 1 query per pair, that way an exception will only affect that pair rather than the bulk operation
        foreach ($user_ids as $uid) {
            foreach ($role_ids as $rid) {
                try {
                    $stmt->execute([":uid" => $uid, ":rid" => $rid]);
                    if ($stmt->rowCount() > 0) {
                        flash("Toggled role for user $uid and role $rid", "success");
                    } else {
                        flash("No changes made for user $uid and role $rid", "warning");
                    }
                } catch (PDOException $e) {
                    flash("There was an error toggling the role, please try again later", "danger");
                    error_log("Error toggling role for user $uid and role $rid: " . var_export($e->errorInfo, true));
                }
            }
        }
    }
}



//search for user by username
$users = [];
$active_roles = [];
$username = "";
if (isset($_POST["action"])) {
    $username = trim(se($_POST, "username", "", false));
    $pokemon_name = trim(se($_POST, "pokemon", "", false));
    if (true) {
        //get active roles only if a username was submitted
        $active_roles = [];
        $db = getDB();
        $stmt = $db->prepare("SELECT id, name, description FROM Roles WHERE is_active = 1 LIMIT 25");
        try {
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($results) {
                $active_roles = $results;
            }
        } catch (PDOException $e) {
            flash(var_export($e->errorInfo, true), "danger");
        }
        //fetch usernames with a csv of roles and their active status
        // Note: the role status will show inactive only if the role has been assigned at least once
        // We're effectively doing a soft delete by toggling `is_active` to 0.
        // Alternatively, we could simply delete the UserRole entry, but that would lose history.
        $stmt = $db->prepare("SELECT Users.id, username, 
        (SELECT GROUP_CONCAT(name, ' (' , IF(ur.is_active = 1,'active','inactive') , ')') from 
        UserRoles ur 
        JOIN Roles on ur.role_id = Roles.id 
        WHERE ur.user_id = Users.id) as roles
        from Users WHERE username like :username");
        try {
            $stmt->execute([":username" => "%$username%"]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($results) {
                $users = $results;
            }
        } catch (PDOException $e) {
            flash(var_export($e->errorInfo, true), "danger");
        }
    } else {
        flash("Username must not be empty", "warning");
    }
}

$pokemon = [];
$active_roles = [];
$pokemon_name = "";
if (isset($_POST["action"])) {
    $pokemon_name = trim(se($_POST, "pokemon", "", false));
    $username = trim(se($_POST, "username", "", false));
    if (true) {
        //get active roles only if a username was submitted
        $active_roles = [];
        $db = getDB();
        $stmt = $db->prepare("SELECT id, name, description FROM Roles WHERE is_active = 1 LIMIT 25");
        try {
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($results) {
                $active_roles = $results;
            }
        } catch (PDOException $e) {
            flash(var_export($e->errorInfo, true), "danger");
        }
        //fetch usernames with a csv of roles and their active status
        // Note: the role status will show inactive only if the role has been assigned at least once
        // We're effectively doing a soft delete by toggling `is_active` to 0.
        // Alternatively, we could simply delete the UserRole entry, but that would lose history.
        $stmt = $db->prepare("SELECT name FROM `IT202-Pokemon` WHERE name like :name");
        try {
            $stmt->execute([":name" => "%$pokemon_name%"]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($results) {
                $pokemon = $results;
            }
        } catch (PDOException $e) {
            flash(var_export($e->errorInfo, true), "danger");
        }
    } else {
        flash("Username must not be empty", "warning");
    }
}

?>
<h3>Assign Roles</h3>
<!-- search form -->
<div class="container text-center">
    <div class="row">
        <div class="col">
            <form method="POST">
                <?php render_input(["type" => "text", "name" => "username", "id" => "username", "label" => "Username search", "rules" => ["required" => true], "value" => $username]); ?>

                <input type="hidden" name="action" value="fetch_user">
                <?php render_button(["text" => "Search", "type" => "submit"]); ?>
            </form>
            <!-- empty toggle form, inputs will use the form attribute to associate with this form -->
            <form id="toggleForm" method="POST"></form>
            <?php if (isset($username) && !empty($username)) : ?>
                <input form="toggleForm" type="hidden" name="username" value="<?php se($username, false); ?>" />
            <?php endif; ?>
            <table class="table">
                <thead>
                    <th>Users</th>
                </thead>
                <tbody>
                    <td>
                        <!-- nested table for users -->
                        <table class="table">
                            <?php foreach ($users as $user) : ?>
                                <tr>
                                    <td>

                                        <input form="toggleForm" id="user_<?php se($user, 'id'); ?>" type="checkbox" name="users[]" value="<?php se($user, 'id'); ?>" />
                                        <label form="toggleForm" for="user_<?php se($user, 'id'); ?>"><?php se($user, "username"); ?></label>
                                    </td>
                                    <td><?php se($user, "roles", "No Roles"); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </td>
                </tbody>
            </table>
        </div>
        <div class="col">
            <form method="POST">
                <?php render_input(["type" => "text", "name" => "pokemon", "id" => "pokemon", "label" => "Pokemon search", "rules" => ["required" => true], "value" => $pokemon_name]); ?>

                <input type="hidden" name="action" value="fetch_pokemon">
                <?php render_button(["text" => "Search", "type" => "submit"]); ?>
            </form>
            <!-- empty toggle form, inputs will use the form attribute to associate with this form -->
            <form id="toggleForm" method="POST"></form>
            <?php if (isset($username) && !empty($username)) : ?>
                <input form="toggleForm" type="hidden" name="username" value="<?php se($username, false); ?>" />
            <?php endif; ?>
            <table class="table">
                <thead>
                    <th>Pokemon</th>
                </thead>
                <tbody>
                    <td>
                        <!-- nested table for users -->
                        <table class="table">
                            <?php foreach ($pokemon as $poke) : ?>
                                <tr>
                                    <td>

                                        <input form="toggleForm" id="pokemon_<?php se($poke, "name"); ?>" type="checkbox" name="pokemon[]" value="<?php se($poke, 'name'); ?>" />
                                        <label form="toggleForm" for="pokemon_<?php se($poke, "name"); ?>"><?php se($poke, "name"); ?></label>
                                    </td>
                                    <td><img src="https://raw.githubusercontent.com/May8th1995/sprites/master/<?php se($poke, "name", "Unown"); ?>.png"></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </td>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="d-flex flex-column justify-content-center align-items-center">
    <a href="?" class="btn btn-secondary">Reset</a>
    <?php render_button(["text" => "Toggle Roles", "type" => "submit"]); ?>
</div>

<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/flash.php");
?>

<style>
    label
    {
        background-color: transparent;
    }
</style>