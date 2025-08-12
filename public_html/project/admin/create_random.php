<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: " . get_url("landing.php")));
}
?>

<?php
if (isset($_POST["action"])) 
{
    $action = $_POST["action"];
    $pokemon = [];
    $name = "random";
    $hasError = false;
    if ($name) 
    {
        if ($action === "fetch") {
            $result = fetch_random();

            error_log("Data from API" . var_export($result, true));
            if ($result) {
                $pokemon = $result;
                $pokemon["is_api"] = 1;
            }

            try {
                $name = $pokemon["name"];
                $r = insert("IT202-Pokemon", $pokemon, ["update_duplicate"=>true]);
                if ($r["lastInsertId"]) {
                    flash("Inserted record " . $r["lastInsertId"], "success");
                } else {
                    flash("Error inserting record", "warning");
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
}
?>

<div class="container-fluid">
    <h3>Create random pokemon</h3>
    <div id="fetch" class="tab-target">
        <form method="POST">
            <div>
            </div>
            <input type="hidden" name="action" value="fetch">
            <input type="submit" value="Fetch" class="btn btn-primary">
        </form>
    </div>
</div>

<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/flash.php");
?>