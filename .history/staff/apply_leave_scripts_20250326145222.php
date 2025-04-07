<script>

const publicHolidays = [
    "2025-01-01", // Example: New Year
    "2025-05-01", // Example: Labour Day
    "2025-12-25"  // Example: Christmas
    // Add more public holidays here in YYYY-MM-DD format
];

function calc() {
    const date_from = document.getElementById('date_form');
    const date_to = document.getElementById('date_to');
    const leaveTypeSelect = document.getElementById('leave_type');
    const selectedLeaveType = leaveTypeSelect.options[leaveTypeSelect.selectedIndex].value;
    const isHalfDay = document.getElementById('is_half_day').value;
    const applyButton = document.getElementById('apply_leave_btn');

    const startDate = new Date(date_from.value);
    const endDate = new Date(date_to.value);
    const currentDate = new Date();
    
    let validLeave = true; // Track if leave is valid

    // Check if FromDate is at least 5 business days from today (except Medical/Sick Leave)
    const minBusinessDate = getMinBusinessDate(currentDate, 5);
    if (selectedLeaveType !== "Medical/Sick Leave" && startDate < minBusinessDate) {
        alert("Leave application must be submitted at least 5 business days before the FromDate!");
        validLeave = false;
    }

    // Check if date is a weekend
    if (startDate.getDay() === 0 || startDate.getDay() === 6) {
        alert("You cannot select weekends (Saturday & Sunday). Please choose a weekday.");
        validLeave = false;
    }

    // Check if date is a public holiday
    const startDateStr = startDate.toISOString().split('T')[0]; // Format YYYY-MM-DD
    if (publicHolidays.includes(startDateStr)) {
        alert("Selected date falls on a public holiday. Please choose another date.");
        validLeave = false;
    }

    // Ensure End Date is not before Start Date
    if (endDate < startDate) {
        alert("End date cannot be earlier than start date!");
        validLeave = false;
    }

    // Calculate requested leave days (only if valid)
    let requestedDays = 0;
    if (validLeave) {
        requestedDays = getBusinessDateCount(startDate, endDate);

        // Adjust for half-day leave
        if (isHalfDay === '1') {
            if (requestedDays === 1) {
                requestedDays -= 0.5;
            } else {
                requestedDays -= 0.5;
            }
        }
    }

    // Update requested and outstanding leave days
    document.getElementById('requested_days').value = validLeave ? requestedDays : "";
    updateAvailableDays();

    // Enable/Disable apply button
    applyButton.disabled = !validLeave;
}

function updateAvailableDays() {
    const leaveTypeSelect = document.getElementById('leave_type');
    const selectedOption = leaveTypeSelect.options[leaveTypeSelect.selectedIndex];
    const availableDays = parseFloat(selectedOption.getAttribute('data-available-days')) || 0;
    const requestedDays = parseFloat(document.getElementById('requested_days').value) || 0;
    const outstandingDays = availableDays - requestedDays;

    document.getElementById('outstanding_days').value = outstandingDays >= 0 ? outstandingDays : 0;

    if (outstandingDays < 0) {
        alert("Requested days exceed available leave days!");
        document.getElementById('apply_leave_btn').disabled = true;
    }
}

// Disable submit button on invalid input
document.getElementById('date_form').addEventListener('input', calc);
document.getElementById('date_to').addEventListener('input', calc);
document.getElementById('is_half_day').addEventListener('change', calc);
document.getElementById('leave_type').addEventListener('change', calc);

</script>



<script src="../vendors/scripts/core.js"></script>
<script src="../vendors/scripts/script.min.js"></script>
<script src="../vendors/scripts/process.js"></script>
<script src="../vendors/scripts/layout-settings.js"></script>