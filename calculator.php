<?php
	session_start();
	if(!isset($_SESSION['login']) || $_SESSION['login']==0)
	header("Location: login.php");
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
		
    </style>
</head>
<body>
    <header>
        <h1><span style="color: #a5d6a7;">🌱 Green Guide</span></h1>
        <p>Know and Track your carbon footprint</p>
	 </header>
	<?php
	require_once("db_connect.php");
	$uid=$_SESSION['uid'];
	$date = date_default_timezone_set('Asia/Kolkata');
	// $cdate = date("D, F j, Y, g:i A T");
	// $cdate = date('d.m.Y', strtotime($cdate));
    $cdate = date('d.m.Y');
	?>
    <div class="container">
        <div class="tab-container">
            <div class="tabs">
               <button class="tab active" onClick="location.href='calculator.php';">Calculator</button>&nbsp;&nbsp;
               <button class="tab" onClick="location.href='suggestions.php';">Suggestions</button>&nbsp;&nbsp;
               <button class="tab" onClick="location.href='dashboard.php';">Dashboard</button>&nbsp;&nbsp;
			   <button class="tab" onClick="location.href='logout.php';">Signout</button>
			</div>
            
            <div class="tab-content active" id="calculator">
				<h2>Carbon Footprint Calculator</h2>
				<form id="carbon-form" name="calculate" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
					<div class="form-group">
                        <label for="transportation">Transportation (km per week)</label>
                        <input type="number" name="trans" id="transportation" min="0" step="0.1" required>
                    </div>
                   
                    <div class="form-group">
                        <label for="transportation-type">Transportation Type</label>
                        <select id="transportation-type" name="tt" required>
                            <option value="">-- Select type --</option>
                            <option value="car">Car (Petrol/Diesel)</option>
                            <option value="electric-car">Electric Car</option>
                            <option value="bus">Bus</option>
                            <option value="train">Train</option>
                            <option value="bike">Bicycle/Walking</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="energy">Energy Consumption (kWh per month)</label>
                        <input type="number" name="ec" id="energy" min="0" step="0.1" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="energy-type">Energy Source</label>
                        <select id="energy-type" name="es" required>
                            <option value="">-- Select source --</option>
                            <option value="renewable">Renewable Energy</option>
                            <option value="natural-gas">Natural Gas</option>
                            <option value="coal">Coal</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="waste">Waste Generated (kg per week)</label>
                        <input type="number" id="waste" name="wg" min="0" step="0.1" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="recycling">Percentage of Waste Recycled (%)</label>
                        <input type="number" id="recycling" name="wr" min="0" max="100" step="1" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="diet">Diet Type</label>
                        <select id="diet" name="dt" required>
                            <option value="">-- Select diet --</option>
                            <option value="meat-heavy">Meat Heavy</option>
                            <option value="average">Average Omnivore</option>
                            <option value="pescatarian">Pescatarian</option>
                            <option value="vegetarian">Vegetarian</option>
                            <option value="vegan">Vegan</option>
                        </select>
                    </div>
					<button type="submit">Calculate Carbon Footprint</button>
                </form>
				<?php
					if(isset($_REQUEST['trans']))	
					{
						$trans=$_REQUEST['trans'];
						$tt=$_REQUEST['tt'];
						$ec=$_REQUEST['ec'];
						$es=$_REQUEST['es'];
						$wg=$_REQUEST['wg'];
						$wr=$_REQUEST['wr'];
						$dt=$_REQUEST['dt'];
						
						$_SESSION['trans']=$trans;
						$_SESSION['tt']=$tt;
						$_SESSION['ec']=$ec;
						$_SESSION['es']=$es;
						$_SESSION['wg']=$wg;
						$_SESSION['wr']=$wr;
						$_SESSION['dt']=$dt;
						
						if ($tt=="car")
								$tef="0.17";
						else if ($tt=="electric-car")
								$tef="0.13";
						else if ($tt=="bus")
								$tef="0.103";
						else if ($tt=="train")
								$tef="0.041";
						else if ($tt=="bike")
								$tef="0";
						$te=$trans*$tef*4.33;
						if ($es=="standard")
								$eef="0.233";
						else if ($es=="renewable")
								$eef="0.025";
						else if ($es=="natural-gas")
								$eef="0.185";
						else if ($es=="coal")
								$eef="0.340";
						$ee=$ec*$eef;
						$we=$wg*0.95*4.33 * (1 - ($wr/100));
						if ($dt=="meat-heavy")
								$def="7.19";
						else if ($dt=="average")
								$def="5.63";
						else if ($dt=="pescatarian")
								$def="3.91";
						else if ($dt=="vegetarian")
								$def="3.81";
						else if ($dt=="vegan")
								$def="2.89";
						$de=$def * 30;
						$fe=$te+$ee+$we+$de;
						
							$q_maxrec="select max(rec) from footprint ";
							$r_maxrec=mysql_query($q_maxrec);
							$row_maxrec=mysql_fetch_array($r_maxrec);
							$rec=$row_maxrec[0] + 1;
							
							$query="insert into footprint values 
									(	'$rec',
										'$uid',
										'$trans',
										'$tt',
										'$ec',
										'$es',
										'$wg',
										'$wr',
										'$dt',
										'$cdate',								
										'$te',
										'$ee',
										'$we',
										'$de',
										'$fe'
									)";
							$a=mysql_query($query);	
						
						?>
						<div class="results" id="results">
							<h3>Your Carbon Footprint Results per month</h3>
							<div class="result-item">
								<span>Transportation:</span>
								<span ><?php echo $te ?> kg CO2e</span>
							</div>
							<div class="result-item">
								<span>Energy Consumption:</span>
								<span id="energy-result"><?php echo $ee ?> kg CO2e</span>
							</div>
							<div class="result-item">
								<span>Waste:</span>
								<span id="waste-result"><?php echo $we ?> kg CO2e</span>
							</div>
							<div class="result-item">
								<span>Diet:</span>
								<span id="diet-result"><?php echo $de ?> kg CO2e</span>
							</div>
							<div class="total">
								<span>Total Carbon Footprint:</span>
								<span id="total-result"><?php echo $fe ?> kg CO2e per month</span>
							</div>
						</div>
						<div style="margin-top: 1rem;">
							<button class="btn" onClick="location.href='calculator.php';">Try again with another Set of Data</button>
						</div>
						<?php
					}
				?>
            </div>
		</div>
    </div>
	<?php if(isset($_REQUEST['trans'])) 
	{ 
	?>
		<script>
			window.onload = function () {
				const resultsSection = document.getElementById("results");
				if (resultsSection) {
					resultsSection.scrollIntoView({ behavior: 'smooth' });
				}
			};
		</script>
		<?php 
	} 
	?>
	</body>
</html>