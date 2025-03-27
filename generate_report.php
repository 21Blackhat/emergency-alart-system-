<?php
require 'config.php';
require 'vendor/autoload.php'; // For PDF (mpdf)

use Mpdf\Mpdf;

// Fetch all emergency reports
$query = "SELECT emergencies.id, users.name AS user_name, emergencies.location, emergencies.status, emergencies.created_at 
          FROM emergencies 
          JOIN users ON emergencies.user_id = users.id 
          ORDER BY emergencies.created_at DESC";

$result = $conn->query($query);
$reports = [];

while ($row = $result->fetch_assoc()) {
    $reports[] = $row;
}

// Check the format requested (PDF or CSV)
$format = isset($_GET['format']) ? $_GET['format'] : 'pdf';

if ($format === 'pdf') {
    generatePDF($reports);
} else {
    generateCSV($reports);
}

// Function to generate a PDF report
function generatePDF($reports) {
    $mpdf = new Mpdf();
    $html = '<h2>Emergency Response Report</h2>';
    $html .= '<table border="1" width="100%" cellpadding="5">';
    $html .= '<tr><th>ID</th><th>User</th><th>Location</th><th>Status</th><th>Timestamp</th></tr>';

    foreach ($reports as $report) {
        $html .= "<tr>
                    <td>{$report['id']}</td>
                    <td>{$report['user_name']}</td>
                    <td>{$report['location']}</td>
                    <td>{$report['status']}</td>
                    <td>{$report['created_at']}</td>
                 </tr>";
    }

    $html .= '</table>';
    $mpdf->WriteHTML($html);
    $mpdf->Output("Emergency_Report.pdf", "D");
}

// Function to generate a CSV report
function generateCSV($reports) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="Emergency_Report.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'User', 'Location', 'Status', 'Timestamp']);

    foreach ($reports as $report) {
        fputcsv($output, $report);
    }

    fclose($output);
}
?>
