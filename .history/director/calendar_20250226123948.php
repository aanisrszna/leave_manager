<?php

// Count the number of each event type
$leaveCount = count(array_filter($calendarEvents, fn($e) => $e[1] === 'leave'));
$holidayCount = count(array_filter($calendarEvents, fn($e) => $e[1] === 'holiday'));
$birthdayCount = count(array_filter($calendarEvents, fn($e) => $e[1] === 'birthday'));

?>

<!-- Pie Chart Canvas -->
<canvas id="calendarPieChart" width="300" height="300"></canvas>

<!-- Calendar Display -->
<div id="calendarWrapper">
    <?= draw_calendar($currentMonth, $currentYear, $calendarEvents); ?>
</div>

<!-- Chart.js Script -->
<script>
    const ctx = document.getElementById('calendarPieChart').getContext('2d');

    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Leaves', 'Holidays', 'Birthdays'],
            datasets: [{
                data: [<?= $leaveCount ?>, <?= $holidayCount ?>, <?= $birthdayCount ?>],
                backgroundColor: ['#007bff', '#dc3545', '#ffc107'],
            }]
        },
        options: {
            responsive: false
        }
    });

    // Convert Canvas to Image and Apply as Background
    setTimeout(() => {
        const chartImage = document.getElementById('calendarPieChart').toDataURL();
        document.getElementById('calendarWrapper').style.backgroundImage = `url(${chartImage})`;
        document.getElementById('calendarWrapper').style.backgroundSize = 'contain';
        document.getElementById('calendarWrapper').style.backgroundPosition = 'center';
    }, 500);
</script>

<style>
    #calendarWrapper {
        position: relative;
        width: fit-content;
        padding: 20px;
        background-repeat: no-repeat;
    }
</style>
