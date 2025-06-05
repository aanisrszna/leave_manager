<script>
function calc() {
    const date_from = document.getElementById('date_form');
    const date_to = document.getElementById('date_to');
    const leaveTypeSelect = document.getElementById('leave_type');
    const selectedLeaveType = leaveTypeSelect.options[leaveTypeSelect.selectedIndex].value;
    const isHalfDay = document.getElementById('is_half_day').value;

    const startDate = new Date(date_from.value);
    const endDate = new Date(date_to.value);

    if (endDate < startDate) {
        alert("End date cannot be earlier than start date!");
        resetInputs();
        return;
    }

    let requestedDays = getBusinessDateCount(startDate, endDate);

    if (isHalfDay === '1') {
        if (requestedDays === 1) {
            requestedDays -= 0.5;
        } else {
            requestedDays -= 0.5;
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
    const leaveTypeId = getSelectedLeaveTypeId();

    const includeWeekends = (leaveTypeId === 6 || leaveTypeId === 7);

    while (currentDate <= endDate) {
        const day = currentDate.getDay();
        if (includeWeekends || (day !== 0 && day !== 6)) {
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
    const proof = document.getElementById('proof');
    const selectedOption = leaveTypeSelect.options[leaveTypeSelect.selectedIndex];
    const needProof = selectedOption.getAttribute('data-need-proof');

    if (needProof === 'Yes') {
        proofContainer.style.display = 'block';
    } else {
        proofContainer.style.display = 'none';
        proof.value = "";
    }
}

function getSelectedLeaveTypeId() {
    const leaveTypeSelect = document.getElementById('leave_type');
    const selectedOption = leaveTypeSelect.options[leaveTypeSelect.selectedIndex];
    return parseInt(selectedOption.value);
}

function validateForm() {
    const dateFrom = document.getElementById('date_form').value;
    const dateTo = document.getElementById('date_to').value;
    const requestedDays = parseFloat(document.getElementById('requested_days').value) || 0;
    const outstandingDays = parseFloat(document.getElementById('outstanding_days').value) || 0;
    const applyButton = document.getElementById('apply');

    const leaveTypeSelect = document.getElementById('leave_type');
    const selectedOption = leaveTypeSelect.options[leaveTypeSelect.selectedIndex];
    const needProof = selectedOption.getAttribute('data-need-proof');
    const proofFile = document.getElementById('proof');

    let isFormValid = dateFrom && dateTo && requestedDays > 0 && outstandingDays >= 0;

    if (needProof === 'Yes' && (!proofFile || proofFile.files.length === 0)) {
        isFormValid = false;
    }

    applyButton.disabled = !isFormValid;
}

document.addEventListener("DOMContentLoaded", function () {
    const dateInputs = document.querySelectorAll("#date_form, #date_to");

    dateInputs.forEach(function (input) {
        input.addEventListener("change", function () {
            const selectedDate = new Date(this.value);
            const day = selectedDate.getDay();

            if (day === 0 || day === 6) {
                alert("You selected a weekend (Saturday or Sunday). Please choose a weekday.");
                this.value = "";
            } else {
                calc();
                validateForm();
            }
        });
    });

    document.getElementById('date_form').addEventListener('input', validateForm);
    document.getElementById('date_to').addEventListener('input', validateForm);
    document.getElementById('leave_type').addEventListener('change', function () {
        toggleProofField();
        validateForm();
    });
    document.getElementById('proof').addEventListener('change', function () {
        if (this.files.length > 0) {
            alert("Proof uploaded successfully ✅");
        }
        validateForm();
    });
    document.getElementById('is_half_day').addEventListener('change', calc);

    // Initialize on load
    toggleProofField();
    validateForm();
});
</script>

<!-- External scripts -->
<script src="../vendors/scripts/core.js"></script>
<script src="../vendors/scripts/script.min.js"></script>
<script src="../vendors/scripts/process.js"></script>
<script src="../vendors/scripts/layout-settings.js"></script>
