<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$host = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "youtube";

$conn = new mysqli($host, $dbusername, $dbpassword, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Emergency</title>
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&callback=initMap" async defer></script>
    
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; text-align: center; padding: 50px; }
        .container { max-width: 600px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0px 0px 10px gray; margin: auto; text-align: left; }
        input, select, textarea { width: 100%; margin: 10px 0; padding: 8px; border: 1px solid #ccc; border-radius: 5px; }
        button { width: 100%; padding: 10px; background-color: red; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background-color: darkred; }
        #map { height: 300px; width: 100%; margin-top: 10px; border-radius: 8px; }
    </style>

    <script>
        let map, marker;

        function initMap() {
            map = new google.maps.Map(document.getElementById("map"), {
                center: { lat: -1.286389, lng: 36.817223 }, // Default: Nairobi, Kenya (Change if needed)
                zoom: 15
            });

            marker = new google.maps.Marker({
                position: map.getCenter(),
                map: map,
                draggable: true
            });

            // Update hidden input when marker is moved
            google.maps.event.addListener(marker, "dragend", function () {
                document.getElementById("location_coords").value = marker.getPosition().lat() + "," + marker.getPosition().lng();
            });
        }

        function useManualInput() {
            document.getElementById("location_coords").value = "";
        }
    </script>
</head>

<body>
    <div class="container">
        <h2>🚨 Report an Emergency</h2>
        <form action="submit_emergency.php" method="POST" enctype="multipart/form-data">
            <label>Select Location:</label>
            <input type="radio" name="location_type" value="map" checked onclick="initMap()"> 📍 Pin Location on Map  
            <input type="radio" name="location_type" value="manual" onclick="useManualInput()"> ⌨️ Enter Manually  

            <div id="map"></div>
            <input type="hidden" name="location_coords" id="location_coords">

            <label>Or Enter Location Manually:</label>
            <input type="text" name="manual_location" placeholder="Enter location (if not using map)">

            <label>Type of Emergency:</label>
            <select name="type" required>
                <option value="Medical">🚑 Medical Emergency</option>
                <option value="Fire">🔥 Fire</option>
                <option value="Crime">🚔 Crime</option>
                <option value="Other">⚠️ Other</option>
            </select>

            <label>Describe the situation:</label>
            <textarea name="details" placeholder="Provide more details" required></textarea>

            <label>Upload an image (optional):</label>
            <input type="file" name="image">

            <button type="submit">Submit Report</button>
        </form>
        <br>
        <a href="home.php">⬅ Back to Dashboard</a>
    </div>
</body>
</html>
