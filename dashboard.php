<?php
	session_start();
	if(!isset($_SESSION['login']) || $_SESSION['login']==0)
		header("Location: login.php");
	$uid = $_SESSION['uid'];
	require_once("db_connect.php");
	// Fetch user data with direct emission columns
	$query = "SELECT * FROM footprint WHERE uid = '$uid' ORDER BY date ASC";
	$result = mysql_query($query);

	$dates = [];
	$transport = [];
	$energy = [];
	$waste = [];
	$diet = [];
	$total_emissions = [];
	if ($result && mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_assoc($result)) {
			$dates[] = $row['date'];
			$transport[] = round((float)$row['te'], 2);
			$energy[] = round((float)$row['ee'], 2);
			$waste[] = round((float)$row['we'], 2);
			$diet[] = round((float)$row['de'], 2);
			$total_emissions[] = round((float)$row['fe'], 2);
		}
	} else {
		echo "No data found for user.";
	}
	// summary stats same as before, optionally based on total_emissions
	$current = end($total_emissions);
	$average = array_sum($total_emissions) / max(count($total_emissions), 1);
	$first = $total_emissions[0] ?? 0;
	$reduction = $first > 0 ? round((($first - $current) / $first) * 100, 2) : 0;
	$status = $reduction > 25 ? "On Track" : ($reduction > 0 ? "Getting There" : "Just Started");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Green Guide - Carbon Footprint Tracker</title>
	<link rel="stylesheet" type="text/css" href="style.css" />
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
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
			display: flex;
			justify-content: space-around;
			gap: 1rem;
			flex-wrap: wrap; /* wrap on small screens */
			margin-top: 2rem;
		}
     .stat {
			flex: 1 1 20%; /* grow/shrink with minimum width ~20% */
			min-width: 200px; /* prevent too small on narrow screens */
			background-color: white;
			padding: 1.5rem;
			border-radius: 0.5rem;
			box-shadow: 0 2px 5px rgba(0,0,0,0.1);
			text-align: center;
		}

		.stat h2 {
			margin-top: 0;
			color: var(--primary-color);
		}

		.stat strong {
			font-size: 2rem;
			display: block;
			margin: 0.5rem 0;
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
		
    </style>
</head>
<body>
    <header>
        <h1><span style="color: #a5d6a7;">🌱 Green</span> Guide</h1>
        <p>Know and Track your carbon footprint</p>
	</header>
	
    <div class="container">
        <div class="tab-container">
            <div class="tabs">
               <button class="tab" onClick="location.href='calculator.php';">Calculator</button>&nbsp;&nbsp;
               <button class="tab" onClick="location.href='suggestions.php';">Suggestions</button>&nbsp;&nbsp;
               <button class="tab active" onClick="location.href='dashboard.php';">Dashboard</button>&nbsp;&nbsp;
			   <button class="tab" onClick="location.href='logout.php';">Signout</button>
			</div>
            <div class="card">
			<canvas id="carbonChart"></canvas>
		</div>

		<div class="stats-container">
			<div class="stat">
				<h2>Current Footprint</h2>
			   <font face="arial" size="5"> <?= number_format($current, 2) ?> kg CO2e per month</font>
			</div>
			<div class="stat">
				<h2>Average Footprint</h2>
				 <font face="arial" size="5"> <?= number_format($average, 2) ?> kg CO2e per month</font>
			</div>
			<div class="stat">
				<h2>Reduction</h2>
				 <font face="arial" size="5"> <?= $reduction ?>%
				<p>from your first calculation</p></font>
			</div>
			<div class="stat">
				<h2>Status</h2>
				<p>🌍  <font face="arial" size="5"> <?= $status ?></font></p>
			</div>
		</div>
            
		</div>
    </div>
	<script>
		Chart.register(ChartDataLabels);
		const ctx = document.getElementById('carbonChart').getContext('2d');
		const totalEmissions = <?= json_encode($total_emissions) ?>;
		const chart = new Chart(ctx, {
			type: 'bar',
			data: {
				labels: <?= json_encode($dates) ?>,
				datasets: [
					{
						label: 'Transportation',
						data: <?= json_encode($transport) ?>,
						backgroundColor: '#4CAF50'
					},
					{
						label: 'Energy',
						data: <?= json_encode($energy) ?>,
						backgroundColor: '#f8c471'
					},
					{
						label: 'Waste',
						data: <?= json_encode($waste) ?>,
						backgroundColor: '#a2d9ce'
					},
					{
						label: 'Diet',
						data: <?= json_encode($diet) ?>,
						backgroundColor: '#bb8fce'
					}
				]
			},
			options: {
				responsive: true,
				plugins: {
					legend: {
						position: 'top'
					},
					title: {
						display: true,
						text: 'Carbon Footprint History'
					},
					tooltip: {
						mode: 'index',
						intersect: false,
						callbacks: {
							footer: function(tooltipItems) {
								let total = 0;
								tooltipItems.forEach(function(item) {
									total += item.raw;
								});
								return 'Total: ' + total.toFixed(2) + ' kg CO2e';
							}
						}
					},
					datalabels: {
						color: '#000',
						font: {
							weight: 'bold',
							size: 12
						},
						display: function(context) {
							return context.datasetIndex === 3; // Keep label only on 'Diet'
						},
						formatter: function(value, context) {
							return totalEmissions[context.dataIndex]; // Show total on top
						},
						anchor: 'end',
						align: 'end'
					}
				},
				interaction: {
					mode: 'index',
					intersect: false
				},
				scales: {
					x: { stacked: true },
					y: {
						stacked: true,
						title: {
							display: true,
							text: 'CO2e (kg)'
						}
					}
				}
			},
			plugins: [ChartDataLabels]
		});
	</script>
</body>
</html>