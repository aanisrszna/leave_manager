<script>

document.addEventListener("DOMContentLoaded", function () {
    const dateFromInput = document.getElementById('date_form');
    const dateToInput = document.getElementById('date_to');
    const leaveTypeSelect = document.getElementById('leave_type');
    const isHalfDaySelect = document.getElementById('is_half_day');
    const applyButton = document.getElementById('apply');

    function validateForm() {
        const startDate = new Date(dateFromInput.value);
        const endDate = new Date(dateToInput.value);
        const leaveType = leaveTypeSelect.value;
        const availableDays = parseFloat(leaveTypeSelect.options[leaveTypeSelect.selectedIndex]?.getAttribute('data-available-days')) || 0;
        const requestedDays = parseFloat(document.getElementById('requested_days').value) || 0;
        const currentDate = new Date();

        let isValid = true;

        // Ensure leave type is selected
        if (!leaveType) {
            isValid = false;
        }

        // Ensure dates are selected
        if (!dateFromInput.value || !dateToInput.value) {
            isValid = false;
        }

        // Ensure start date is at least 5 business days ahead (excluding "Medical/Sick Leave")
        const minBusinessDate = getMinBusinessDate(currentDate, 5);
        if (leaveType !== "Medical/Sick Leave" && startDate < minBusinessDate) {
            isValid = false;
        }

        // Ensure start date is before end date
        if (endDate < startDate) {
            isValid = false;
        }

        // Ensure selected dates are not weekends
        if (startDate.getDay() === 0 || startDate.getDay() === 6 || endDate.getDay() === 0 || endDate.getDay() === 6) {
            isValid = false;
        }

        // Ensure sufficient leave balance
        if (requestedDays > availableDays) {
            isValid = false;
        }

        // Enable/Disable the Apply button based on validation
        applyButton.disabled = !isValid;
    }

    // Function to get the minimum business date (5 days ahead)
    function getMinBusinessDate(currentDate, businessDays) {
        let count = 0;
        const minBusinessDate = new Date(currentDate);

        while (count < businessDays) {
            minBusinessDate.setDate(minBusinessDate.getDate() + 1);
            const day = minBusinessDate.getDay();
            if (day !== 0 && day !== 6) { // Exclude weekends
                count++;
            }
        }

        return minBusinessDate;
    }

    // Event listeners for form fields
    dateFromInput.addEventListener("input", validateForm);
    dateToInput.addEventListener("input", validateForm);
    leaveTypeSelect.addEventListener("change", validateForm);
    isHalfDaySelect.addEventListener("change", validateForm);

    // Initially disable the Apply button
    applyButton.disabled = true;
});

</script>



<script src="../vendors/scripts/core.js"></script>
<script src="../vendors/scripts/script.min.js"></script>
<script src="../vendors/scripts/process.js"></script>
<script src="../vendors/scripts/layout-settings.js"></script>