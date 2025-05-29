<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/roles.php';
require_once __DIR__ . '/../includes/functions.php';
require_login();

$mysqli = db_connect();

include_once __DIR__ . '/../templates/header.php';
?>

<div class="container mt-4">

    <!-- Manuals Table -->
    <h4>Manuals</h4>
    <table class="table table-bordered table-striped mb-4">
        <thead>
            <tr>
                <th>No.</th>
                <th>Title</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $manuals = $mysqli->query("SELECT * FROM documents WHERE category='manuals' ORDER BY id");
        $no = 1;
        while ($manual = $manuals->fetch_assoc()) {
            echo "<tr>
                    <td>{$no}</td>
                    <td>" . htmlspecialchars($manual['title']) . "</td>
                  </tr>";
            $no++;
        }
        ?>
        </tbody>
    </table>

    <!-- Procedures Table -->
    <h4>Procedures</h4>
    <table class="table table-bordered table-striped mb-4">
        <thead>
            <tr>
                <th>No.</th>
                <th>Title</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $procedures = $mysqli->query("SELECT * FROM documents WHERE category='procedures' ORDER BY id");
        $no = 1;
        while ($procedure = $procedures->fetch_assoc()) {
            echo "<tr>
                    <td>{$no}</td>
                    <td>" . htmlspecialchars($procedure['title']) . "</td>
                  </tr>";
            $no++;
        }
        ?>
        </tbody>
    </table>

    <!-- Work Instructions Table -->
    <h4>Work Instructions</h4>
    <table class="table table-bordered table-striped mb-4">
        <thead>
            <tr>
                <th>No.</th>
                <th>Title</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $wis = $mysqli->query("SELECT * FROM documents WHERE category='work_instructions' ORDER BY id");
        $no = 1;
        while ($wi = $wis->fetch_assoc()) {
            echo "<tr>
                    <td>{$no}</td>
                    <td>" . htmlspecialchars($wi['title']) . "</td>
                  </tr>";
            $no++;
        }
        ?>
        </tbody>
    </table>
</div>

<?php include_once __DIR__ . '/../templates/footer.php'; ?>
