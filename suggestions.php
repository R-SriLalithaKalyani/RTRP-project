<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] == 0) {
    header("Location: login.php");
    exit();
}

require_once("db_connect.php");

$uid = $_SESSION['uid'];
date_default_timezone_set('Asia/Kolkata');
$cdate = date('d.m.Y');

function addSuggestion(&$suggestions, $text) {
    $suggestions[] = $text;
}

$suggestions = [];

if (isset($_SESSION['trans'], $_SESSION['tt'], $_SESSION['ec'], $_SESSION['es'], $_SESSION['wg'], $_SESSION['wr'], $_SESSION['dt'])) {
    $trans = $_SESSION['trans'];
    $tt = $_SESSION['tt']; // transportation type
    $ec = $_SESSION['ec']; // energy consumption
    $es = $_SESSION['es']; // energy source
    $wg = $_SESSION['wg']; // waste generated
    $wr = $_SESSION['wr']; // waste recycled
    $dt = $_SESSION['dt']; // diet type

    // Transportation
    if ($tt === 'car') {
        addSuggestion($suggestions, "🚗 Consider carpooling or using public transportation to reduce your driving emissions.");
        addSuggestion($suggestions, "🚶 For short trips, try walking or cycling instead of driving.");
    }

    if ($trans > 100 && $tt === 'car') {
        addSuggestion($suggestions, "🛻 Your car usage is relatively high. Consider working from home or combining errands to reduce driving.");
    }

    if ($tt === 'electric-car') {
        addSuggestion($suggestions, "🔌 Use renewable energy to charge your electric vehicle for a cleaner footprint.");
    }

    // Energy
    if ($ec > 250) {
        addSuggestion($suggestions, "💡 Your energy use is above average. Switch to LED bulbs and energy-efficient appliances.");
        addSuggestion($suggestions, "🔌 Unplug electronics when not in use to avoid phantom energy loss.");
    }

    if ($es === 'coal' || $es === 'standard') {
        addSuggestion($suggestions, "🌞 Consider switching to renewable sources like solar or wind energy.");
    }

    // Waste
    if ($wr < 50) {
        addSuggestion($suggestions, "♻️ Increase recycling by sorting waste properly.");
        addSuggestion($suggestions, "🌿 Try composting organic waste to reduce landfill contributions.");
    }

    if ($wg > 10) {
        addSuggestion($suggestions, "📦 Reduce packaging waste by buying in bulk or using reusable containers.");
        addSuggestion($suggestions, "🛍️ Try zero-waste shopping practices.");
    }

    // Diet
    if ($dt === 'meat-heavy') {
        addSuggestion($suggestions, "🥦 Try 'Meatless Mondays' or reducing meat meals each week.");
        addSuggestion($suggestions, "🐄 Choose locally sourced meat to reduce transportation emissions.");
    } elseif ($dt === 'average') {
        addSuggestion($suggestions, "🌱 Replace some red meat meals with plant-based alternatives.");
    }

    // General
    addSuggestion($suggestions, "🌳 Plant trees or support reforestation to offset emissions.");
    addSuggestion($suggestions, "🛒 Buy local products to reduce transportation impact.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Green Guide - Carbon Footprint Tracker</title>
	<link rel="stylesheet" type="text/css" href="style.css" />
    <style>
        :root {
            --primary-color: #2e7d32;
            --secondary-color: #81c784;
            --background-color: #f1f8e9;
            --text-color: #212121;
            --chart-height: 300px;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--background-color);
            color: var(--text-color);
        }
        
        header {
            background-color: var(--primary-color);
            color: white;
            padding: 1rem;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1rem;
        }
        
        .tab-container {
            margin-top: 1rem;
        }
        
        .tabs {
            display: flex;
            background-color: white;
            border-radius: 0.5rem 0.5rem 0 0;
            overflow: hidden;
        }
        
        .tab {
            padding: 1rem 2rem;
            cursor: pointer;
            flex-grow: 1;
            text-align: center;
            background-color: #148f77 ;
            transition: background-color 0.3s;
        }
        
        .tab.active {
            background-color: var(--secondary-color);
            color: white;
            font-weight: bold;
        }
        
        .tab-content {
            display: none;
            background-color: white;
            padding: 1.5rem;
            border-radius: 0 0 0.5rem 0.5rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .tab-content.active {
            display: block;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        
        input, select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ccc;
            border-radius: 0.25rem;
            font-size: 1rem;
        }
        
        button {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 0.25rem;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s;
        }
        
        button:hover {
            background-color: #1b5e20;
        }
        
        .results {
            margin-top: 2rem;
            background-color: white;
            padding: 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .result-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #eee;
        }
        
        .result-item:last-child {
            border-bottom: none;
        }
        
        .total {
            font-weight: bold;
            font-size: 1.2rem;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 2px solid #eee;
        }
        
        .suggestions {
            margin-top: 2rem;
        }
        
        .suggestion-item {
            background-color: #e8f5e9;
            padding: 1rem;
            margin-bottom: 0.5rem;
            border-radius: 0.25rem;
            border-left: 4px solid var(--primary-color);
        }
        
        .dashboard {
            margin-top: 2rem;
        }
        
        .chart-container {
            background-color: white;
            padding: 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            height: var(--chart-height);
            margin-bottom: 2rem;
        }
        
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }
        
        .stat-card {
            background-color: white;
            padding: 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        
        .stat-card h3 {
            margin-top: 0;
            color: var(--primary-color);
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            margin: 0.5rem 0;
        }
        
        .badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            background-color: var(--secondary-color);
            color: white;
            border-radius: 1rem;
            font-size: 0.8rem;
        }
        
        @media (max-width: 768px) {
            .tabs {
                flex-direction: column;
            }
            
            .tab {
                border-radius: 0;
            }
        }
		.suggestion-item {
            background-color: #e8f5e9;
            padding: 1rem;
            margin-bottom: 0.75rem;
            border-left: 5px solid #2e7d32;
            border-radius: 0.25rem;
            font-size: 1rem;
        }
    </style>
</head>
<body>
    <header>
        <h1><span style="color: #a5d6a7;">🌱 Green</span> Guide</h1>
        <p>Know and Track your carbon footprint</p>
	 </header>
	<?php
	require_once("db_connect.php");
	$uid=$_SESSION['uid'];
	$date = date_default_timezone_set('Asia/Kolkata');
	$cdate = date("D, F j, Y, g:i A T");
	$cdate = date('d.m.Y', strtotime($cdate));
	?>
    <div class="container">
        <div class="tab-container">
            <div class="tabs">
               <button class="tab" onClick="location.href='calculator.php';">Calculator</button>&nbsp;&nbsp;
               <button class="tab active" onClick="location.href='suggestions.php';">Suggestions</button>&nbsp;&nbsp;
               <button class="tab" onClick="location.href='dashboard.php';">Dashboard</button>&nbsp;&nbsp;
			   <button class="tab" onClick="location.href='logout.php';">Signout</button>
			</div>
            <div class="tab-content active" id="suggestions">
				<h2>Suggestions to Reduce Your Carbon Footprint</h2>
				<div class="suggestions" id="suggestions-container">
					<?php if (count($suggestions) > 0): ?>
						<?php foreach ($suggestions as $item): ?>
							<div class="suggestion-item"><?php echo $item; ?></div>
						<?php endforeach; ?>
					<?php else: ?>
						<p>Please calculate your carbon footprint first to see personalized suggestions.</p>
					<?php endif; ?>
				</div>
			</div>
			
		</div>
    </div>
</body>
</html>