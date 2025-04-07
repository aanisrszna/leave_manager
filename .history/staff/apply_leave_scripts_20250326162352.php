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
    const proof = document.getElementById('proof'); // Correct ID
    const selectedOption = leaveTypeSelect.options[leaveTypeSelect.selectedIndex];
    const needProof = selectedOption.getAttribute('data-need-proof');

    if (needProof === 'Yes') {
        proofContainer.style.display = 'block';
    } else {
        proofContainer.style.display = 'none';
        proof.value = ""; // Clear file input when hiding
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

    // Run toggleProofField on page load (fixes issue if a proof-required leave type is pre-selected)
    toggleProofField();
    validateForm();
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
    const proofFile = document.getElementById('proof'); // Corrected from `proof_file`

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

document.getElementById('proof').addEventListener('change', function () {
    if (this.files.length > 0) {
        const message = document.createElement('div');
        message.textContent = "Proof uploaded successfully ✅";
        message.style.color = "green";
        message.style.textAlign = "center";
        message.style.marginTop = "10px";
        document.body.appendChild(message); // Show message anywhere (e.g., below form)

        // Remove message after 3 seconds
        setTimeout(() => {
            message.remove();
        }, 3000);
    }
    validateForm();
});



document.getElementById('date_form').addEventListener('input', calc);
document.getElementById('date_to').addEventListener('input', calc);
document.getElementById('is_half_day').addEventListener('change', calc);


</script>



<script src="../vendors/scripts/core.js"></script>
<script src="../vendors/scripts/script.min.js"></script>
<script src="../vendors/scripts/process.js"></script>
<script src="../vendors/scripts/layout-settings.js"></script>