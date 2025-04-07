<script>

function calc() {
    const date_from = document.getElementById('date_form');
    const date_to = document.getElementById('date_to');
    const leaveTypeSelect = document.getElementById('leave_type');
    const selectedLeaveType = leaveTypeSelect.options[leaveTypeSelect.selectedIndex].value;
    const isHalfDay = document.getElementById('is_half_day').value;

    const startDate = new Date(date_from.value);
    const endDate = new Date(date_to.value);
    const currentDate = new Date();

    // Check if FromDate is at least 5 business days from today (excluding "Medical/Sick Leave")
    const minBusinessDate = getMinBusinessDate(currentDate, 5);
    if (selectedLeaveType !== "Medical/Sick Leave" && startDate < minBusinessDate) {
        alert("Leave application must be submitted at least 5 business days before the FromDate!");
        resetInputs();
        return;
    }
    if (endDate < startDate) {
        alert("End date cannot be earlier than start date!");
        resetInputs();
        return;
    }

    let requestedDays = getBusinessDateCount(startDate, endDate);

    // Adjust for half-day leave if applicable
    if (isHalfDay === '1') {
        if (requestedDays === 1) {
            requestedDays -= 0.5; // Add half-day only if the total duration spans 1 full day or more
        } else {
            requestedDays -= 0.5; // For single-day half-day leave
        }
    }

    document.getElementById('requested_days').value = requestedDays;
    updateAvailableDays();
}

function toggleHalfDayType() {
    const isHalfDay = document.getElementById('is_half_day').value;
    const halfDayTypeContainer = document.getElementById('half_day_type_container');
    halfDayTypeContainer.style.display = isHalfDay === '1' ? 'block' : 'none';
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
    }
}

function getBusinessDateCount(startDate, endDate) {
    let count = 0;
    const currentDate = new Date(startDate);

    while (currentDate <= endDate) {
        const day = currentDate.getDay();
        if (day !== 0 && day !== 6) {
            count++;
        }
        currentDate.setDate(currentDate.getDate() + 1);
    }

    return count;
}

function getMinBusinessDate(currentDate, businessDays) {
    let count = 0;
    const minBusinessDate = new Date(currentDate);

    while (count < businessDays) {
        minBusinessDate.setDate(minBusinessDate.getDate() + 1);
        const day = minBusinessDate.getDay();
        if (day !== 0 && day !== 6) {
            count++;
        }
    }

    return minBusinessDate;
}

function resetInputs() {
    document.getElementById('requested_days').value = '';
    document.getElementById('outstanding_days').value = '';
}
function toggleProofField() {
    const leaveTypeSelect = document.getElementById('leave_type');
    const proofContainer = document.getElementById('proof_container');
    const selectedOption = leaveTypeSelect.options[leaveTypeSelect.selectedIndex];
    const needProof = selectedOption.getAttribute('data-need-proof');

    if (needProof === 'Yes') {
        proofContainer.style.display = 'block';
    } else {
        proofContainer.style.display = 'none';
    }
}

document.addEventListener("DOMContentLoaded", function () {
    let dateInputs = document.querySelectorAll("#date_form, #date_to");

    dateInputs.forEach(function (input) {
        input.addEventListener("input", function () {
            let selectedDate = new Date(this.value);
            let day = selectedDate.getDay();

            if (day === 0 || day === 6) { // 0 = Sunday, 6 = Saturday
                alert("You cannot select weekends (Saturday & Sunday). Please choose a weekday.");
                this.value = ""; // Reset input field
            }
        });
    });
});


function validateForm() {
    const dateFrom = document.getElementById('date_form').value;
    const dateTo = document.getElementById('date_to').value;
    const requestedDays = parseFloat(document.getElementById('requested_days').value) || 0;
    const outstandingDays = parseFloat(document.getElementById('outstanding_days').value) || 0;
    const applyButton = document.getElementById('apply');

    // Check if proof is required
    const leaveTypeSelect = document.getElementById('leave_type');
    const selectedOption = leaveTypeSelect.options[leaveTypeSelect.selectedIndex];
    const needProof = selectedOption.getAttribute('data-need-proof');
    const proofFile = document.getElementById('proof_file'); // Assume this is the file input

    let isFormValid = dateFrom && dateTo && requestedDays > 0 && outstandingDays >= 0;

    if (needProof === 'Yes' && (!proofFile || proofFile.files.length === 0)) {
        isFormValid = false; // Require proof file if needed
    }

    // Enable or disable the button based on validation
    applyButton.disabled = !isFormValid;
}

// Attach event listeners to validate the form dynamically
document.addEventListener("DOMContentLoaded", function () {
    document.getElementById('date_form').addEventListener('input', validateForm);
    document.getElementById('date_to').addEventListener('input', validateForm);
    document.getElementById('leave_type').addEventListener('change', function () {
        toggleProofField();
        validateForm();
    });
    document.getElementById('proof').addEventListener('change', validateForm);
});


document.getElementById('proof').addEventListener('change', function(event) {
    let file = event.target.files[0];
    let messageDiv = document.getElementById('uploadMessage');
    messageDiv.innerHTML = '';

    if (file) {
        let allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
        let fileExtension = file.name.split('.').pop().toLowerCase();
        let fileSize = file.size / 1024 / 1024; // Convert to MB

        if (!allowedExtensions.includes(fileExtension)) {
            messageDiv.innerHTML = '<span style="color: red;">Invalid file type! Please upload a pdf, jpg, jpeg, or png.</span>';
            event.target.value = ''; // Reset input
        } else if (fileSize > 2) {
            messageDiv.innerHTML = '<span style="color: red;">File size exceeds 2MB. Please upload a smaller file.</span>';
            event.target.value = ''; // Reset input
        } else {
            messageDiv.innerHTML = '<span style="color: green;">File uploaded successfully!</span>';
        }
    }
});
document.getElementById('date_form').addEventListener('input', calc);
document.getElementById('date_to').addEventListener('input', calc);
document.getElementById('is_half_day').addEventListener('change', calc);


</script>



<script src="../vendors/scripts/core.js"></script>
<script src="../vendors/scripts/script.min.js"></script>
<script src="../vendors/scripts/process.js"></script>
<script src="../vendors/scripts/layout-settings.js"></script>