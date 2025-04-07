<?php
// Include the Dompdf library
require_once 'path/to/dompdf/autoload.inc.php'; // Replace with actual path to Dompdf autoload
use Dompdf\Dompdf;
use Dompdf\Options;

// Database connection
include 'db_connection.php'; // Include your database connection file

// Initialize Dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);
$dompdf = new Dompdf($options);

// Check if the button is clicked to generate PDF
if (isset($_POST['generate_pdf'])) {
    // Query to get the employee data (FirstName) without redundancies
    $sql = "SELECT DISTINCT tblemployees.emp_id, tblemployees.FirstName
            FROM employee_leave
            JOIN tblemployees ON employee_leave.emp_id = tblemployees.emp_id";
    $query = $dbh->prepare($sql);
    $query->execute();
    $employees = $query->fetchAll(PDO::FETCH_OBJ);

    // Start output buffering
    ob_start();

    // HTML content for the PDF
    ?>
    <html>
    <head>
        <style>
            body {
                font-family: Arial, sans-serif;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }
            th, td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
            }
            th {
                background-color: #f2f2f2;
            }
            h3 {
                color: rgb(70, 142, 209);
            }
        </style>
    </head>
    <body>
        <h2>Employee Leave Information Report</h2>
        <div class="row">
            <?php
            // Loop through each employee to create a separate table for each
            foreach ($employees as $employee) {
                echo "<h3>Leave Information for " . htmlentities($employee->FirstName) . "</h3>";
                echo "<table>
                        <thead>
                            <tr>
                                <th>Leave Type</th>
                                <th>Available Days</th>
                            </tr>
                        </thead>
                        <tbody>";

                // Query to get leave types and available days for each employee
                $sql_leaves = "SELECT tblleavetype.LeaveType, employee_leave.available_day
                               FROM employee_leave
                               JOIN tblleavetype ON employee_leave.leave_type_id = tblleavetype.id
                               WHERE employee_leave.emp_id = :emp_id";
                $query_leaves = $dbh->prepare($sql_leaves);
                $query_leaves->bindParam(':emp_id', $employee->emp_id, PDO::PARAM_INT);
                $query_leaves->execute();
                $leave_details = $query_leaves->fetchAll(PDO::FETCH_OBJ);

                // Display leave details for each employee
                foreach ($leave_details as $leave) {
                    echo "<tr>
                            <td>" . htmlentities($leave->LeaveType) . "</td>
                            <td>" . htmlentities($leave->available_day) . "</td>
                          </tr>";
                }

                echo "    </tbody>
                        </table>";
            }
            ?>
        </div>
    </body>
    </html>
    <?php

    // Capture the output
    $html = ob_get_clean();

    // Load HTML content
    $dompdf->loadHtml($html);

    // (Optional) Setup the paper size and orientation
    $dompdf->setPaper('A4', 'portrait');

    // Render the HTML as PDF
    $dompdf->render();

    // Output the generated PDF to Browser
    $dompdf->stream('employee_leave_report.pdf', array('Attachment' => 0)); // Set Attachment to 1 for download
}
?>
