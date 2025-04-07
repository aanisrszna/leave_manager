<?php
include('../includes/session.php');
include('../includes/config.php');
require_once('../TCPDF-main/tcpdf.php');

$did = intval($_GET['leave_id']);
$sql = "SELECT tblemployees.FirstName, tblemployees.Staff_ID, tblemployees.Position_Staff, tblemployees.Phonenumber, tblemployees.EmailId, 
               tblleave.LeaveType, tblleave.RequestedDays, tblleave.DaysOutstand, tblleave.PostingDate, tblleave.FromDate, tblleave.ToDate, tblleave.HodRemarks, tblleave.RegRemarks,
               tblleave.HodSign, tblleave.RegSign, tblleave.proof
        FROM tblleave 
        JOIN tblemployees ON tblleave.empid = tblemployees.emp_id 
        WHERE tblleave.id = '$did'";

$query = mysqli_query($conn, $sql) or die(mysqli_error());
$row = mysqli_fetch_array($query);

// Extract values from the query
$fullname = $row['FirstName'];
$staff_position = $row['Position_Staff'];
$staff_id = $row['Staff_ID'];
$phone_number = $row['Phonenumber'];
$email = $row['EmailId'];
$leave_type = $row['LeaveType'];
$requested_days = $row['RequestedDays'];
$days_outstanding = $row['DaysOutstand'];
$applied_date = $row['PostingDate'];
$leave_period = $row['FromDate'] . ' to ' . $row['ToDate'];
$hod_remarks = $row['HodRemarks'] ? ($row['HodRemarks'] == 1 ? 'Approved' : ($row['HodRemarks'] == 2 ? 'Rejected' : 'Pending')) : 'NA';
$reg_remarks = $row['RegRemarks'] ? ($row['RegRemarks'] == 1 ? 'Approved' : ($row['RegRemarks'] == 2 ? 'Rejected' : 'Pending')) : 'NA';
$hod_sign = $row['HodSign'];  // Manager's Signature (path to image)
$reg_sign = $row['RegSign'];  // Director's Signature (path to image)
$proof_picture = '../proof/' . $row['proof']; // Proof Picture (path to image)

// Define custom PDF class
class MYPDF extends TCPDF {
    // Page header
    public function Header() {
        $this->SetFont('helvetica', 'B', 16);
        $this->Cell(0, 10, 'Leave Application Report', 0, 1, 'C');
    }

    // Page footer
    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'C');
    }
}

// Create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('Leave Application Report');

// Set margins and auto page breaks
$pdf->SetMargins(15, 27, 15);
$pdf->SetAutoPageBreak(TRUE, 25);

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', '', 12);

// Add content
$html = <<<EOD
<h2>Leave Application Details</h2>
<table cellspacing="0" cellpadding="5" border="1">
    <tr><td><b>Full Name:</b></td><td>$fullname</td></tr>
    <tr><td><b>Staff ID:</b></td><td>$staff_id</td></tr>
    <tr><td><b>Position:</b></td><td>$staff_position</td></tr>
    <tr><td><b>Phone Number:</b></td><td>$phone_number</td></tr>
    <tr><td><b>Email:</b></td><td>$email</td></tr>
    <tr><td><b>Leave Type:</b></td><td>$leave_type</td></tr>
    <tr><td><b>Requested Days:</b></td><td>$requested_days</td></tr>
    <tr><td><b>Days Outstanding:</b></td><td>$days_outstanding</td></tr>
    <tr><td><b>Application Date:</b></td><td>$applied_date</td></tr>
    <tr><td><b>Leave Period:</b></td><td>$leave_period</td></tr>
    <tr><td><b>HOD Remarks:</b></td><td>$hod_remarks</td></tr>
    <tr><td><b>Registrar Remarks:</b></td><td>$reg_remarks</td></tr>
</table>
<br><br>
<h3>Proof Picture</h3>
EOD;

// Add HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// Add Proof Picture if it exists
if (!empty($proof_picture) && file_exists($proof_picture)) {
    $pdf->Image($proof_picture, 15, $pdf->GetY(), 100, 60, '', '', 'T', true, 300, '', false, false, 1, false, false, false);
} else {
    $pdf->Write(0, 'No proof picture uploaded.', '', 0, 'L', true, 0, false, false, 0);
}

// Add signatures if they exist
if (!empty($hod_sign) && file_exists($hod_sign)) {
    $pdf->Image($hod_sign, 15, $pdf->GetY() + 70, 50, 20, '', '', 'T', true, 300, '', false, false, 1, false, false, false);
    $pdf->SetY($pdf->GetY() + 25);
    $pdf->Write(0, 'HOD Signature', '', 0, 'L', true, 0, false, false, 0);
}
if (!empty($reg_sign) && file_exists($reg_sign)) {
    $pdf->Image($reg_sign, 15, $pdf->GetY() + 10, 50, 20, '', '', 'T', true, 300, '', false, false, 1, false, false, false);
    $pdf->Write(0, 'Registrar Signature', '', 0, 'L', true, 0, false, false, 0);
}

// Output the PDF
$pdf->Output('leave_application_report.pdf', 'I');
?>
