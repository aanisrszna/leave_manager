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
    private $fullname;
    private $staff_position;

    public function __construct($fullname, $staff_position) {
        parent::__construct();
        $this->fullname = $fullname;
        $this->staff_position = $staff_position;
    }

    public function Header() {
        $this->Image('../vendors/images/riverraven.png', 160, 10, 30);
        $this->Cell(0, 10, '', 0, 10, 'L');
        
        $this->SetFont('helvetica', 'B', 14);
        $this->Cell(0, 10, 'Leave Application Form', 0, 10, 'L');
        $this->SetFont('helvetica', 'I', 10);
        $this->Cell(0, 5, '(This form must be COMPLETED with all details before submission)', 0, 10, 'L');
        $this->Cell(0, 5, '(All form must be submitted 4 days before going on leave on the said date.)', 0, 10, 'L');
        $this->Cell(0, 5, '(Please attach medical certificates for sick leave.)', 0, 10, 'L');

        $this->Ln(5);
        
        $this->SetFont('helvetica', '', 12);
        $this->Cell(0, 10, "Full Name: " . $this->fullname, 0, 1, 'L');
        $this->Cell(0, 5, "Position: " . $this->staff_position, 0, 1, 'L');
        $this->Ln(5);
    }

    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'C');
    }
}

// Pass the variables when creating the PDF object
$pdf = new MYPDF($fullname, $staff_position);
$pdf->SetMargins(15, 27, 15);
$pdf->SetAutoPageBreak(TRUE, 25);
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);

$html = ''; // Initialize $html

$html .= <<<EOD
<!-- Leave Details -->
<br><br><br><br><br><br><br>
<table cellspacing="0" cellpadding="5" border="1">
    <tr><td><b>Leave Start Date:</b></td><td>$leave_start</td></tr>
    <tr><td><b>Leave End Date:</b></td><td>$leave_end</td></tr>
    <tr><td><b>No. of Days:</b></td><td>$requested_days</td></tr>
    <tr><td><b>Leave Type:</b></td><td>$leave_type</td></tr>
</table>
<br>
<p>In case of emergency, I can be reached at: <b>$phone_number</b></p>

<!-- Approvals Section -->
<table cellspacing="0" cellpadding="5" border="1" style="width:100%;">
    <tr>
        <td colspan="4" style="background-color:#D3D3D3; font-weight:bold; text-align:center;">Approvals</td>
    </tr>
    <tr>
        <td colspan="2"><b>Head of Department :</b></td>
        <td colspan="2"><b>Director :</b></td>
    </tr>
    <tr>
        <td colspan="2" style="height:30px;">__________________________________</td>
        <td colspan="2" style="height:30px;">__________________________________</td>
    </tr>
    <tr>
        <td><b>Date :</b></td>
        <td style="height:25px;">__________</td>
        <td><b>Date :</b></td>
        <td style="height:25px;">__________</td>
    </tr>
    <tr>
        <td></td>
        <td>[ ] Rejected</td>
        <td>[ ] Approved</td>
        <td>[ ] Rejected</td>
    </tr>
</table>
EOD;

$pdf->writeHTML($html, true, false, true, false, '');

// Output the PDF (Make sure there's no output before this line)
$pdf->Output('Leave_Application_Form.pdf', 'I');

?>
