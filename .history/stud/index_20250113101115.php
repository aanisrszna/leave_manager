<?php include('includes/header.php') ?>
<?php include('../includes/session.php') ?>
<body>

    <?php include('includes/navbar.php') ?>
    <?php include('includes/right_sidebar.php') ?>
    <?php include('includes/left_sidebar.php') ?>

    <div class="mobile-menu-overlay"></div>

    <div class="main-container">
        <div class="pd-ltr-20">
            <div class="title pb-20">
                <h2 class="h3 mb-0">Employee Leave Types</h2>
            </div>

            <!-- Month Selector -->
            <form method="GET" class="mb-4">
                <label for="month">Select Month:</label>
                <select name="month" id="month" class="form-control" style="width: auto; display: inline-block;">
                    <?php
                    $months = [
                        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                        5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                        9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
                    ];
                    foreach ($months as $num => $name) {
                        $selected = (isset($_GET['month']) && $_GET['month'] == $num) ? 'selected' : '';
                        echo "<option value='$num' $selected>$name</option>";
                    }
                    ?>
                </select>
                <button type="submit" class="btn btn-primary">Show Calendar</button>
            </form>

            <!-- Calendar Display -->
            <div class="calendar-container">
                <?php
                function draw_calendar($month, $year) {
                    $daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                    $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
                    $numberOfDays = date('t', $firstDayOfMonth);
                    $dateComponents = getdate($firstDayOfMonth);
                    $monthName = $dateComponents['month'];
                    $dayOfWeek = $dateComponents['wday'];
                    $calendar = "<table class='table table-bordered'>";
                    $calendar .= "<thead><tr><th colspan='7'>$monthName $year</th></tr></thead>";
                    $calendar .= "<tr>";
                    foreach ($daysOfWeek as $day) {
                        $calendar .= "<th class='text-center'>$day</th>";
                    }
                    $calendar .= "</tr><tr>";
                    if ($dayOfWeek > 0) {
                        $calendar .= str_repeat("<td></td>", $dayOfWeek);
                    }
                    $currentDay = 1;
                    while ($currentDay <= $numberOfDays) {
                        if ($dayOfWeek == 7) {
                            $dayOfWeek = 0;
                            $calendar .= "</tr><tr>";
                        }
                        $calendar .= "<td class='text-center'>$currentDay</td>";
                        $currentDay++;
                        $dayOfWeek++;
                    }
                    if ($dayOfWeek != 7) {
                        $remainingDays = 7 - $dayOfWeek;
                        $calendar .= str_repeat("<td></td>", $remainingDays);
                    }
                    $calendar .= "</tr></table>";
                    return $calendar;
                }

                $selectedMonth = isset($_GET['month']) ? intval($_GET['month']) : date('n');
                echo draw_calendar($selectedMonth, 2025);
                ?>
            </div>

            <?php include('includes/footer.php'); ?>
        </div>
    </div>

    <!-- js -->
    <?php include('includes/scripts.php') ?>
</body>
</html>
