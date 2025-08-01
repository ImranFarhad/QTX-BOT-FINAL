<?php
session_start();

// --- Login Logic ---
if (isset($_POST['password'])) {
    if ($_POST['password'] === '017222') {
        $_SESSION['logged_in'] = true;
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $error = "Incorrect password!";
    }
}

// --- Logout Logic ---
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// --- Check if logged in ---
$logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;

// --- Handle AJAX request for signal ---
if ($logged_in && isset($_GET['action']) && $_GET['action'] === 'get_signal') {
    $pair = $_GET['pair'] ?? 'EURUSD_otc';
    $tf = $_GET['tf'] ?? '60';

    // TODO: এখানে তোমার রিয়েল API যুক্ত করো, এখন ফেক সিগন্যাল রিটার্ন করছি
    $signals = ['Green', 'Red'];
    $prediction = $signals[array_rand($signals)];
    $confidence = rand(85, 98);

    header('Content-Type: application/json');
    echo json_encode([
        'prediction' => $prediction,
        'confidence' => $confidence,
        'pair' => $pair,
        'timeframe' => $tf
    ]);
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>QTX SIGNAL BOT (Single File)</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            color: white;
            margin: 0; padding: 20px;
            display: flex; justify-content: center; align-items: center; height: 100vh;
        }
        .container {
            background: rgba(0,0,0,0.7);
            padding: 30px;
            border-radius: 12px;
            width: 360px;
            text-align: center;
            box-shadow: 0 0 15px #00ffcc99;
        }
        input, select, button {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 8px;
            border: none;
            font-size: 16px;
        }
        button {
            background: #f0b90b;
            font-weight: bold;
            cursor: pointer;
            color: black;
        }
        .error {
            color: #ff4444;
            margin-bottom: 10px;
        }
        #signal-output {
            margin-top: 20px;
            font-size: 20px;
            min-height: 50px;
        }
        a.logout {
            color: #ff5555;
            display: block;
            margin-top: 15px;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container">
<?php if (!$logged_in): ?>
    <h2>QTX Signal Bot Login 🔐</h2>
    <form method="post">
        <input type="password" name="password" placeholder="Enter Password" required>
        <button type="submit">Login</button>
        <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>
    </form>

<?php else: ?>

    <h2>📊 Signal Dashboard</h2>

    <select id="pair">
        <option value="EURUSD_otc">EURUSD_otc</option>
        <option value="AUDCAD_otc">AUDCAD_otc</option>
        <option value="GBPJPY_otc">GBPJPY_otc</option>
        <option value="USDJPY">USDJPY</option>
        <option value="BTCUSD">BTCUSD</option>
        <!-- Add more pairs as you want -->
    </select>

    <select id="timeframe">
        <option value="60">1 Minute</option>
        <option value="30">30 Seconds</option>
        <option value="15">15 Seconds</option>
        <option value="10">10 Seconds</option>
        <option value="5">5 Seconds</option>
    </select>

    <button onclick="getSignal()">Get Signal 🚀</button>

    <div id="signal-output">Select pair & timeframe, then click 'Get Signal'</div>

    <a class="logout" href="?logout=1">Logout</a>

    <script>
        function getSignal() {
            const pair = document.getElementById('pair').value;
            const tf = document.getElementById('timeframe').value;
            const output = document.getElementById('signal-output');
            output.textContent = '⏳ Fetching signal...';

            fetch(`?action=get_signal&pair=${pair}&tf=${tf}`)
                .then(res => res.json())
                .then(data => {
                    output.innerHTML = `<strong>Pair:</strong> ${data.pair}<br>
                                        <strong>Timeframe:</strong> ${data.timeframe}s<br>
                                        <strong>Prediction:</strong> ${data.prediction === 'Green' ? '🟢 BUY' : '🔴 SELL'}<br>
                                        <strong>Confidence:</strong> ${data.confidence}%`;
                })
                .catch(() => {
                    output.textContent = '❌ Error fetching signal';
                });
        }
    </script>

<?php endif; ?>
</div>

</body>
</html>
