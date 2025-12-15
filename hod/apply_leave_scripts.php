<script>
function isLateOrBackdated() {
    const dateFromValue = document.getElementById('date_form').value;
    if (!dateFromValue) return false;

    const startDate = new Date(dateFromValue);
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    const minBusinessDate = getMinBusinessDate(today, 5);
    return startDate < minBusinessDate;
}

function autofillReasonForEmergencyAL() {
    const reasonInput = document.querySelector('input[name="reason"]');
    if (!reasonInput) return;

    if (reasonInput.value.trim() === '') {
        reasonInput.value = 'Emergency Leave - AL';
    }
}

function updateLateLeaveNotice() {
    const notice = document.getElementById('lateLeaveNotice');
    const leaveType = document.getElementById('leave_type').value;
    const reasonInput = document.querySelector('input[name="reason"]');

    if (!notice || !reasonInput) return;

    if (leaveType.startsWith('Annual') && isLateOrBackdated()) {
        notice.style.display = 'block';
        autofillReasonForEmergencyAL();
    } else {
        notice.style.display = 'none';

        if (reasonInput.value === 'Emergency Leave - AL') {
            reasonInput.value = '';
        }
    }
}

function autofillReasonForEmergencyAL() {
    const reasonInput = document.querySelector('input[name="reason"]');
    if (!reasonInput) return;

    // Only auto-fill if empty (do not overwrite user input)
    if (reasonInput.value.trim() === '') {
        reasonInput.value = 'Emergency Leave - AL';
    }
}

function calc() {
    const date_from = document.getElementById('date_form');
    const date_to = document.getElementById('date_to');
    const leaveTypeSelect = document.getElementById('leave_type');

    if (!date_from.value || !date_to.value || !leaveTypeSelect.value) {
        return;
    }

    const selectedLeaveType = leaveTypeSelect.value;
    const isHalfDay = document.getElementById('is_half_day').value;

    const startDate = new Date(date_from.value);
    const endDate = new Date(date_to.value);

    if (endDate < startDate) {
        alert("End date cannot be earlier than start date!");
        resetInputs();
        return;
    }

    // Show warning ONCE for late Annual Leave
    if (
        selectedLeaveType.startsWith('Annual') &&
        isLateOrBackdated() &&
        !window._lateLeaveAlertShown
    ) {
        alert(
            "This Annual Leave is submitted late or less than 5 business days.\n"
        );
        window._lateLeaveAlertShown = true;
    }

    let requestedDays;
    if (selectedLeaveType === "Maternity Leave" || selectedLeaveType === "Paternity Leave") {
        requestedDays = getCalendarDayCount(startDate, endDate);
    } else {
        requestedDays = getBusinessDateCount(startDate, endDate);
    }

    if (isHalfDay === '1') {
        requestedDays -= 0.5;
    }

    document.getElementById('requested_days').value = requestedDays;
    updateAvailableDays();
    validateForm();
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
        if (day !== 0 && day !== 6) count++;
        currentDate.setDate(currentDate.getDate() + 1);
    }
    return count;
}

function getCalendarDayCount(startDate, endDate) {
    let count = 0;
    const currentDate = new Date(startDate);

    while (currentDate <= endDate) {
        count++;
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
        if (day !== 0 && day !== 6) count++;
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
        proof.value = '';
    }
}

function validateForm() {
    const dateFrom = document.getElementById('date_form').value;
    const dateTo = document.getElementById('date_to').value;
    const requestedDays = parseFloat(document.getElementById('requested_days').value) || 0;
    const outstandingDays = parseFloat(document.getElementById('outstanding_days').value) || 0;

    document.getElementById('apply').disabled =
        !(dateFrom && dateTo && requestedDays > 0 && outstandingDays >= 0);
}

function enableLeaveTypeIfDatesSelected() {
    const dateFrom = document.getElementById('date_form').value;
    const dateTo = document.getElementById('date_to').value;
    const leaveType = document.getElementById('leave_type');

    if (dateFrom && dateTo) {
        leaveType.disabled = false;
    } else {
        leaveType.disabled = true;
        leaveType.value = '';
        resetInputs();
    }
}

document.addEventListener("DOMContentLoaded", function () {
    const leaveTypeSelect = document.getElementById('leave_type');
    leaveTypeSelect.disabled = true;

    let dateInputs = document.querySelectorAll("#date_form, #date_to");

    dateInputs.forEach(function (input) {
        input.addEventListener("change", function () {
            setTimeout(() => {
                let selectedDate = new Date(this.value);
                let day = selectedDate.getDay();

                if (day === 0 || day === 6) {
                    alert("You cannot select weekends (Saturday & Sunday). Please choose a weekday.");
                    this.value = "";
                }
            }, 100);
        });
    });

    // Attach dynamic validators
    document.getElementById('date_form').addEventListener('change', () => {
        enableLeaveTypeIfDatesSelected();
        updateLateLeaveNotice();

        // ✅ Only calculate if leave type already chosen
        if (document.getElementById('leave_type').value) {
            calc();
        }

        validateForm();
    });

    document.getElementById('date_to').addEventListener('change', () => {
        enableLeaveTypeIfDatesSelected();

        // ✅ Only calculate if leave type already chosen
        if (document.getElementById('leave_type').value) {
            calc();
        }

        validateForm();
    });



    document.getElementById('leave_type').addEventListener('change', function () {
        if (!this.value) return;
        updateLateLeaveNotice();
        calc();
        toggleProofField();
        validateForm();
    });

    
    leaveTypeSelect.addEventListener('mousedown', function (e) {
        if (this.disabled) {
            e.preventDefault();
            alert("Please select Start Date and End Date first.");
        }
    });


    document.getElementById('proof').addEventListener('change', function () {
        if (this.files.length > 0) {
            alert("Proof uploaded successfully ✅");
        }
        validateForm();
    });

    document.getElementById('is_half_day').addEventListener('change', calc);

    toggleProofField();
    validateForm();
});
</script>

<!-- Vendor Scripts -->
<script src="../vendors/scripts/core.js"></script>
<script src="../vendors/scripts/script.min.js"></script>
<script src="../vendors/scripts/process.js"></script>
<script src="../vendors/scripts/layout-settings.js"></script>
