<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/roles.php';
require_once __DIR__ . '/../includes/functions.php';
require_login();

$user = current_user();
$mysqli = db_connect();

// Set fixed category order and icons
$category_icon_map = [
    'Manuals' => 'bi-book',
    'Procedures' => 'bi-file-earmark-text',
    'Work Instructions' => 'bi-list-check',
    'Policies' => 'bi-layers',
    'Others' => 'bi-three-dots',
];

// Initialize category counts (fixed order)
$category_summary = [
    'Manuals' => 0,
    'Procedures' => 0,
    'Work Instructions' => 0,
    'Policies' => 0,
    'Others' => 0
];

// Read document counts
$sql1 = "SELECT category, COUNT(*) as total FROM documents GROUP BY category";
$res1 = $mysqli->query($sql1);
while ($row = $res1->fetch_assoc()) {
    $db_cat = ucwords(str_replace('_', ' ', $row['category']));
    if (array_key_exists($db_cat, $category_summary)) {
        $category_summary[$db_cat] = (int)$row['total'];
    } else {
        $category_summary['Others'] += (int)$row['total'];
    }
}
$total_docs = array_sum($category_summary);

// For charts
$categories = array_keys($category_summary);
$totals1 = array_values($category_summary);

$sql2 = "SELECT department, COUNT(*) as total FROM documents GROUP BY department";
$res2 = $mysqli->query($sql2);
$departments = [];
$totals2 = [];
while ($row = $res2->fetch_assoc()) {
    $departments[] = $row['department'];
    $totals2[] = (int)$row['total'];
}

$sql3 = "SELECT pic, COUNT(*) as total FROM documents GROUP BY pic";
$res3 = $mysqli->query($sql3);
$pics = [];
$totals3 = [];
while ($row = $res3->fetch_assoc()) {
    $pics[] = $row['pic'];
    $totals3[] = (int)$row['total'];
}

include_once __DIR__ . '/../templates/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document Analysis Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f4f9fd 0%, #dde8f9 100%);
            font-family: 'Inter', Arial, sans-serif;
            margin: 0;
            min-height: 100vh;
        }
        .app-bar {
            width: 100%;
            background: #274690;
            color: #fff;
            text-align: center;
            padding: 18px 0 8px 0;
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: .2px;
            border-bottom-left-radius: 1.5rem;
            border-bottom-right-radius: 1.5rem;
            box-shadow: 0 2px 10px rgba(60,80,160,0.05);
        }
        .dashboard-container {
			width: 90%;
			max-width: none;
            margin: 22px auto 0 auto;
            padding: 8px;
        }
        .summary-cards {
            display: flex;
            flex-wrap: wrap;
            gap: 18px;
            margin: 0 0 26px 0;
            justify-content: center;
        }
        .summary-card {
            flex: 1 1 145px;
            min-width: 140px;
            max-width: 220px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 3px 12px rgba(40,80,180,0.07);
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 22px 10px 14px 10px;
            transition: box-shadow .2s;
            position: relative;
        }
        .summary-card .bi {
            font-size: 2.2em;
            color: #4461c9;
            margin-bottom: 7px;
        }
        .summary-card .cat-label {
            color: #22336b;
            font-weight: 600;
            font-size: 1em;
            margin-bottom: 2px;
            margin-top: 2px;
        }
        .summary-card .cat-total {
            color: #237a67;
            font-weight: 700;
            font-size: 2em;
            margin-bottom: 0;
            letter-spacing: 1px;
        }
        .summary-card .cat-total.all {
            color: #2468ac;
        }
        .summary-card:hover, .summary-card:focus {
            box-shadow: 0 8px 22px rgba(64,100,230,0.10);
        }
        .welcome-row {
            margin-bottom: 18px;
			font-size: 1.2rem;
            text-align: center;
        }
        .role-badge {
            display: inline-block;
            margin-left: 10px;
            padding: 3px 12px;
            border-radius: 11px;
            background: #e2edfa;
            color: #23567a;
            font-size: .95em;
            border: 1px solid #bfd9f2;
            vertical-align: middle;
        }
        .charts-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 24px;
        }
        .chart-card {
            background: #f4f7fa;
            padding: 19px 7px 12px 7px;
            border-radius: 17px;
            box-shadow: 0 1px 8px rgba(30,60,160,0.04);
            min-height: 270px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .chart-card h2 {
            text-align: center;
            color: #234e89;
            font-size: 1.1rem;
            margin-bottom: 10px;
            font-weight: 700;
        }
        .chart-card canvas {
            width: 100% !important;
            height: 190px !important;
            max-width: 380px;
        }
        @media (max-width: 900px) {
            .charts-grid { grid-template-columns: 1fr; gap: 18px; }
            .dashboard-container { padding: 6px; }
        }
        @media (max-width: 660px) {
            .summary-cards { flex-direction: column; gap: 14px;}
            .summary-card { min-width: 90vw; max-width: 95vw;}
            .app-bar { font-size: 1.08rem; padding: 14px 0 6px 0;}
            .charts-grid { gap: 11px; }
            .dashboard-container { padding: 1.5vw; }
        }
		.mb-4 {
			margin-bottom: 0rem !important;
		}


		.dashboard-title-container {
			width: 100%;
			max-width: 1200px;
			margin: 38px auto 0 auto;
			text-align: center;
			padding: 0 18px;
		}

		.dashboard-title-main {
			font-size: 1.4em;
			color: #22336b;
			font-weight: 700;
			letter-spacing: .5px;
			margin-bottom: 0.2em;
			margin-top: 0.1em;
		}
		.dashboard-title-main i.bi {
			color: #4461c9;
			font-size: 1.2em;
			vertical-align: -.05em;
			margin-right: 0.18em;
		}

    </style>
</head>
<body>
    <div class="app-bar">
        <div class="welcome-row">
            Welcome, <b><?= htmlspecialchars($user['username'] ?? '-') ?></b>
            <span class="role-badge"><?= htmlspecialchars($user['role'] ?? '-') ?></span>
        </div>
    </div>
    <div class="dashboard-title-container">
        <div class="dashboard-title-main">
            <i class="bi bi-bar-chart-line"></i> Document Analysis Dashboard
        </div>
    </div>
    <div class="dashboard-container">
	
        <!-- Category summary as app cards (fixed order) -->
        <div class="summary-cards">
            <?php foreach ($category_summary as $cat => $cnt): ?>
                <div class="summary-card" title="<?= htmlspecialchars($cat) ?>">
                    <i class="bi <?= $category_icon_map[$cat] ?>"></i>
                    <div class="cat-label"><?= htmlspecialchars($cat) ?></div>
                    <div class="cat-total"><?= $cnt ?></div>
                </div>
            <?php endforeach; ?>
            <div class="summary-card" title="Total Documents">
                <i class="bi bi-bar-chart"></i>
                <div class="cat-label" style="color:#2468ac;">All</div>
                <div class="cat-total all"><?= $total_docs ?></div>
            </div>
        </div>

        <!-- Charts -->
        <div class="charts-grid">
            <div class="chart-card">
                <h2>By Category</h2>
                <canvas id="categoryChart"></canvas>
            </div>
            <div class="chart-card">
                <h2>By Department</h2>
                <canvas id="departmentChart"></canvas>
            </div>
            <div class="chart-card">
                <h2>By PIC</h2>
                <canvas id="picChart"></canvas>
            </div>
        </div>
    </div>
<script>
const categories = <?= json_encode($categories); ?>;
const totals1 = <?= json_encode($totals1); ?>;
const departments = <?= json_encode($departments); ?>;
const totals2 = <?= json_encode($totals2); ?>;
const pics = <?= json_encode($pics); ?>;
const totals3 = <?= json_encode($totals3); ?>;

function emptyMsg(chartId, arr) {
    if(arr.length === 0) {
        let ctx = document.getElementById(chartId).getContext('2d');
        ctx.font = "16px Inter, Arial";
        ctx.fillStyle = "#bbb";
        ctx.textAlign = "center";
        ctx.fillText("No data available", 120, 55);
    }
}

if (categories.length > 0) {
    new Chart(document.getElementById('categoryChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: categories,
            datasets: [{
                label: 'No. of Documents',
                data: totals1,
                backgroundColor: 'rgba(54,162,235,0.8)',
                borderRadius: 8
            }]
        },
        options: {
            plugins: { legend: { display: false }, title: { display: false } },
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });
} else { emptyMsg('categoryChart', categories); }

if (departments.length > 0) {
    new Chart(document.getElementById('departmentChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: departments,
            datasets: [{
                label: 'No. of Documents',
                data: totals2,
                backgroundColor: 'rgba(52,195,143,0.8)',
                borderRadius: 8
            }]
        },
        options: {
            plugins: { legend: { display: false }, title: { display: false } },
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });
} else { emptyMsg('departmentChart', departments); }

if (pics.length > 0) {
    new Chart(document.getElementById('picChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: pics,
            datasets: [{
                label: 'No. of Documents',
                data: totals3,
                backgroundColor: 'rgba(236,120,54,0.8)',
                borderRadius: 8
            }]
        },
        options: {
            plugins: { legend: { display: false }, title: { display: false } },
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });
} else { emptyMsg('picChart', pics); }
</script>
</body>
</html>
<?php include_once __DIR__ . '/../templates/footer.php'; ?>
