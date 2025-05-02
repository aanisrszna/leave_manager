<?php
include('../includes/session.php');
include('../includes/config.php');
require_once('../TCPDF-main/tcpdf.php');

$did = intval($_GET['leave_id']);
$sql = "SELECT tblemployees.FirstName, tblemployees.Staff_ID, tblemployees.Position_Staff, tblemployees.Phonenumber, tblemployees.EmailId, 
               tblleave.LeaveType, tblleave.RequestedDays, tblleave.DaysOutstand, tblleave.PostingDate, tblleave.FromDate, tblleave.ToDate, 
               tblleave.HodRemarks, tblleave.RegRemarks, tblleave.HodSign, tblleave.RegSign, tblleave.HodDate, tblleave.RegDate, tblleave.proof,
               tblleave.HalfDayType, tblleave.reason, tblemployees.Emergency_Contact, tblemployees.Emergency_Name
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
$leave_type = $row['LeaveType'];
$requested_days = $row['RequestedDays'];
$applied_date = date('d F Y', strtotime($row['PostingDate']));
$leave_start = date('d-M-Y', strtotime($row['FromDate']));
$leave_end = date('d-M-Y', strtotime($row['ToDate']));
$hod_remarks = ($row['HodRemarks'] == 1) ? 'Approved' : (($row['HodRemarks'] == 2) ? 'Rejected' : 'Pending');
$reg_remarks = ($row['RegRemarks'] == 1) ? 'Approved' : (($row['RegRemarks'] == 2) ? 'Rejected' : 'Pending');
$hod_sign = !empty($row['HodSign']) ? '../signature/' . $row['HodSign'] : 'No Signature';
$reg_sign = !empty($row['RegSign']) ? '../signature/' . $row['RegSign'] : 'No Signature';

$hod_date = !empty($row['HodDate']) ? date('d-M-Y', strtotime($row['HodDate'])) : '';
$reg_date = !empty($row['RegDate']) ? date('d-M-Y', strtotime($row['RegDate'])) : '';

$proof_path = '../' . $row['proof'];
$halfday = $row['HalfDayType'];
$reason =$row['reason'];
$emergencyname = $row['Emergency_Name'];
$emergencycontact =$row['Emergency_Contact'];
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
<br><br><br><br><br><br><br><br>
<table cellspacing="0" cellpadding="5" border="1">
    <thead>
        <tr>
            <th colspan="6" style="text-align: center;"><b>Leave Application</b></th>
        </tr>
        <tr>
            <th>Start</th>
            <th>End</th>
            <th>No of day(s)</th>
            <th>AM / PM<br><small>(halfday only)</small></th>
            <th>Leave type</th>
            <th>Reason</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>$leave_start</td>
            <td>$leave_end</td>
            <td>$requested_days</td>
            <td>$halfday</td>
            <td>$leave_type</td>
            <td>$reason</td>
        </tr>

    </tbody>
</table>


<p>In case of emergency, I can be reached at: <b>$emergencycontact ($emergencyname)</b></p>
<br>
<!-- Approvals Section -->
<table cellspacing="0" cellpadding="5" border="1" style="width:100%;">
    <tr>
        <td colspan="4" style="background-color:#D3D3D3; font-weight:bold; text-align:center;">Approvals</td>
    </tr>
    <tr>
        <td colspan="2"><b>Head of Department </b></td>
        <td colspan="2"><b>Director </b></td>
    </tr>
    <tr>
        <td colspan="2" style="height:50px; text-align:center;">
            <img src="$hod_sign" width="100" height="30" alt="No Signature">
        </td>
        <td colspan="2" style="height:50px; text-align:center;">
            <img src="$reg_sign" width="100" height="30" alt="No Signature">
        </td>
    </tr>
    <tr>
        <td><b>Date</b></td>
        <td style="height:25px;">$hod_date</td>
        <td><b>Date</b></td>
        <td style="height:25px;">$reg_date</td>
    </tr>

    <tr>
        <td colspan="2">$hod_remarks</td>

        <td colspan="2">$reg_remarks</td>

    </tr>
</table>
<br><br>
<h3>Proof Picture</h3>
EOD;

$pdf->writeHTML($html, true, false, true, false, '');

$extension = strtolower(pathinfo($proof_path, PATHINFO_EXTENSION));

if (!empty($row['proof']) && file_exists($proof_path)) {
    if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
        $pdf->Image($proof_path, 15, $pdf->GetY(), 100, 60, '', '', 'T', true, 300);
        $pdf->Ln(70); // Add space after image
    } elseif ($extension === 'pdf') {
        $pdf->SetFont('helvetica', '', 11);
        $pdf->Write(0, 'Proof is a PDF file. You can view it at the following link:', '', 0, 'L', true, 0, false, false, 0);

        $baseUrl = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/rr_leave_portal/';
        $pdfLink = $baseUrl . $row['proof'];

        $pdf->SetTextColor(0, 0, 255);
        $pdf->Write(0, $pdfLink, $pdfLink, 0, 'L', true, 0, false, true, 0);
        $pdf->SetTextColor(0, 0, 0); // Reset text color
    } else {
        $pdf->Write(0, 'Unsupported proof file type.', '', 0, 'L', true, 0, false, false, 0);
    }
} else {
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Write(0, 'No proof file uploaded.', '', 0, 'L', true, 0, false, false, 0);
}

// Output the PDF (Make sure there's no output before this line)
$pdf->Output('Leave_Application_Form.pdf', 'I');

?>
