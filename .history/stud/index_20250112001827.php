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
                <h2 class="h3 mb-0">Employee Leave Calendar</h2>
            </div>

            <!-- Calendar Section -->
            <div class="row pb-10">
                <div class="col-12">
                    <div id="calendar"></div> <!-- Calendar Container -->
                </div>
            </div>

            <!-- Leave Types Section -->
            <div class="title pb-20">
                <h2 class="h3 mb-0">Employee Leave Types</h2>
            </div>

            <div class="row pb-10">
                <?php
                // Fetch Leave Data
                $query = mysqli_query($conn, "
                    SELECT 
                        el.emp_id,
                        lt.LeaveType,
                        el.available_day
                    FROM 
                        employee_leave el
                    INNER JOIN 
                        tblleavetype lt 
                    ON 
                        el.leave_type_id = lt.id
                    WHERE 
                        el.emp_id = '$session_id'
                ") or die(mysqli_error($conn));

                while ($row = mysqli_fetch_array($query)) {
                ?>
                <div class="col-xl-6 col-lg-6 col-md-12 mb-20">
                    <div class="card-box height-100-p widget-style3">
                        <div class="d-flex flex-wrap">
                            <div class="widget-data">
                                <div class="weight-700 font-24 text-dark"><?php echo $row['LeaveType']; ?></div>
                                <div class="font-18 text-secondary weight-500">Available Days: <?php echo $row['available_day']; ?></div>
                            </div>
                            <div class="widget-icon">
                                <div class="icon" data-color="#17a2b8"><i class="icon-copy fa fa-calendar-check-o"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>

            <?php include('includes/footer.php'); ?>
        </div>
    </div>

    <!-- Include FullCalendar Dependencies -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

    <!-- Calendar Initialization Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: [
                    <?php
                    // Fetch Leave Events Data
                    $eventsQuery = mysqli_query($conn, "
                        SELECT 
                            lt.LeaveType,
                            el.start_date,
                            el.end_date
                        FROM 
                            employee_leave el
                        INNER JOIN 
                            tblleavetype lt 
                        ON 
                            el.leave_type_id = lt.id
                        WHERE 
                            el.emp_id = '$session_id'
                    ") or die(mysqli_error($conn));

                    while ($event = mysqli_fetch_array($eventsQuery)) {
                        echo "{
                            title: '{$event['LeaveType']}',
                            start: '{$event['start_date']}',
                            end: '{$event['end_date']}'
                        },";
                    }
                    ?>
                ]
            });

            calendar.render();
        });
    </script>
    
    <!-- js -->
    <?php include('includes/scripts.php') ?>
</body>
</html>
