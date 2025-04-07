<?php
include('../includes/session.php');
include('../includes/config.php');
require_once('../TCPDF-main/tcpdf.php');

$did = intval($_GET['leave_id']);
$sql = "SELECT tblemployees.FirstName, tblemployees.LastName, tblemployees.Staff_ID, tblemployees.Position_Staff, tblemployees.Phonenumber, tblemployees.EmailId, 
               tblleave.LeaveType, tblleave.RequestedDays, tblleave.DaysOutstand, tblleave.PostingDate, tblleave.FromDate, tblleave.ToDate, 
               tblleave.HodRemarks, tblleave.RegRemarks, tblleave.HodSign, tblleave.RegSign, tblleave.proof
        FROM tblleave 
        JOIN tblemployees ON tblleave.empid = tblemployees.emp_id 
        WHERE tblleave.id = '$did'";

$query = mysqli_query($conn, $sql) or die(mysqli_error());
$row = mysqli_fetch_array($query);

// Extract values from the query
$fullname = $row['FirstName'] . ' ' . $row['LastName'];
$staff_position = $row['Position_Staff'];
$staff_id = $row['Staff_ID'];
$phone_number = $row['Phonenumber'];
$leave_type = $row['LeaveType'];
$requested_days = $row['RequestedDays'];
$applied_date = date('d F Y', strtotime($row['PostingDate']));
$leave_start = date('d-M-Y', strtotime($row['FromDate']));
$leave_end = date('d-M-Y', strtotime($row['ToDate']));
$hod_remarks = ($row['HodRemarks'] == 1) ? 'Approved' : (($row['HodRemarks'] == 2) ? 'Rejected' : 'Pending');
$reg_remarks = ($row['RegRemarks'] == 1) ? 'Approved' : (($row['RegRemarks'] == 2) ? 'Rejected' : 'Pending');
$hod_sign = $row['HodSign'];
$reg_sign = $row['RegSign'];
$proof_picture = '../proof/' . $row['proof'];

// Define custom PDF class
class MYPDF extends TCPDF {
    public function Header() {
        $this->Image('../vendors/images/riverraven.png', 160, 10, 30);
        $this->SetFont('helvetica', 'B', 14);
        $this->Cell(0, 10, 'Leave Application Form', 0, 1, 'C');
        $this->SetFont('helvetica', 'I', 10);
        $this->Cell(0, 5, '(This form must be COMPLETED with all details before submission)', 0, 1, 'C');
        $this->Ln(5);
    }

    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'C');
    }
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetMargins(15, 27, 15);
$pdf->SetAutoPageBreak(TRUE, 25);
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);

$html = <<<EOD
<table cellspacing="0" cellpadding="5" border="1">
    <tr><td><b>Full Name:</b></td><td>$fullname</td></tr>
    <tr><td><b>Position:</b></td><td>$staff_position</td></tr>
    <tr><td><b>Phone Number:</b></td><td>$phone_number</td></tr>
    <tr><td><b>Leave Type:</b></td><td>$leave_type</td></tr>
    <tr><td><b>No. of Days:</b></td><td>$requested_days</td></tr>
    <tr><td><b>Leave Start Date:</b></td><td>$leave_start</td></tr>
    <tr><td><b>Leave End Date:</b></td><td>$leave_end</td></tr>
</table>
<br>
<h3>Approvals</h3>
<table cellspacing="0" cellpadding="5" border="1">
    <tr><td><b>HOD Approval:</b></td><td>$hod_remarks</td></tr>
    <tr><td><b>Director Approval:</b></td><td>$reg_remarks</td></tr>
</table>
EOD;

$pdf->writeHTML($html, true, false, true, false, '');

$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);
$pdf->Write(0, 'Proof Picture:', '', 0, 'L', true, 0, false, false, 0);

if (!empty($proof_picture) && file_exists($proof_picture)) {
    $pdf->Image($proof_picture, 15, $pdf->GetY() + 10, 100, 60, '', '', 'T', true, 300, '', false, false, 1, false, false, false);
} else {
    $pdf->Write(0, 'No proof picture uploaded.', '', 0, 'L', true, 0, false, false, 0);
}

$pdf->Output('Leave_Application_Form.pdf', 'I');
?>
