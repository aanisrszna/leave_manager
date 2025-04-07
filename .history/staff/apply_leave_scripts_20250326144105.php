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
        disableSubmitButton();
        return;
    }

    // Check if endDate is before startDate
    if (endDate < startDate) {
        alert("End date cannot be earlier than start date!");
        resetInputs();
        disableSubmitButton();
        return;
    }

    // Check for weekends
    if (startDate.getDay() === 0 || startDate.getDay() === 6 || endDate.getDay() === 0 || endDate.getDay() === 6) {
        alert("You cannot select weekends (Saturday & Sunday). Please choose a weekday.");
        resetInputs();
        disableSubmitButton();
        return;
    }

    // Add public holiday check (if you have a list of holidays)
    const publicHolidays = ["2025-03-25", "2025-05-01", "2025-06-01"]; // Example list of public holidays
    if (isPublicHoliday(startDate, publicHolidays) || isPublicHoliday(endDate, publicHolidays)) {
        alert("Selected dates fall on a public holiday. Please choose another date.");
        resetInputs();
        disableSubmitButton();
        return;
    }

    let requestedDays = getBusinessDateCount(startDate, endDate);

    // Adjust for half-day leave if applicable
    if (isHalfDay === '1') {
        if (requestedDays === 1) {
            requestedDays -= 0.5;
        } else {
            requestedDays -= 0.5;
        }
    }

    document.getElementById('requested_days').value = requestedDays;
    updateAvailableDays();
    enableSubmitButton(); // Enable button if validation passes
}

// Disable the submit button
function disableSubmitButton() {
    document.getElementById('apply_button').disabled = true;
}

// Enable the submit button
function enableSubmitButton() {
    document.getElementById('apply_button').disabled = false;
}

// Check if the selected date is a public holiday
function isPublicHoliday(date, publicHolidays) {
    const dateString = date.toISOString().split('T')[0]; // Format date as 'YYYY-MM-DD'
    return publicHolidays.includes(dateString);
}

function resetInputs() {
    document.getElementById('requested_days').value = '';
    document.getElementById('outstanding_days').value = '';
}

// Check weekends on the date inputs
document.addEventListener("DOMContentLoaded", function () {
    let dateInputs = document.querySelectorAll("#date_form, #date_to");

    dateInputs.forEach(function (input) {
        input.addEventListener("input", function () {
            let selectedDate = new Date(this.value);
            let day = selectedDate.getDay();

            if (day === 0 || day === 6) { // 0 = Sunday, 6 = Saturday
                alert("You cannot select weekends (Saturday & Sunday). Please choose a weekday.");
                this.value = ""; // Reset input field
                disableSubmitButton(); // Disable submit button if invalid date is selected
            } else {
                enableSubmitButton(); // Enable button if valid date is selected
            }
        });
    });
});




<script src="../vendors/scripts/core.js"></script>
<script src="../vendors/scripts/script.min.js"></script>
<script src="../vendors/scripts/process.js"></script>
<script src="../vendors/scripts/layout-settings.js"></script>