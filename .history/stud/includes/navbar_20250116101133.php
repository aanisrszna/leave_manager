<?php
// Start the session and make sure session_id is available
session_start();
$session_id = $_SESSION['emp_id']; // Assuming the session variable for emp_id is stored as 'emp_id'

// Check if the session variable exists
if (!isset($session_id)) {
    // Redirect to login page if the session is not set
    header('Location: login.php');
    exit();
}

// Database connection
include('db_connection.php'); // Replace with your actual database connection file

// Fetch the latest leave application status to trigger a new notification if necessary
$latestLeaveQuery = mysqli_query($conn, "
    SELECT RegRemarks, leave_date 
    FROM tblleave 
    WHERE empid = '$session_id' 
    ORDER BY id DESC 
    LIMIT 1
") or die(mysqli_error($conn));

if (mysqli_num_rows($latestLeaveQuery) > 0) {
    $latestLeave = mysqli_fetch_array($latestLeaveQuery);
    $leaveStatus = $latestLeave['RegRemarks'];
    $leaveDate = $latestLeave['leave_date']; // Assuming there's a 'leave_date' field in tblleave

    // Check if the notification already exists for this employee and leave status
    $notificationCheckQuery = mysqli_query($conn, "
        SELECT * FROM tblnotifications 
        WHERE empid = '$session_id' AND message LIKE '%Leave Has Been Approved%' 
        OR message LIKE '%Leave Has Been Rejected%'
    ") or die(mysqli_error($conn));

    // Insert a new notification if it doesn't exist
    if (mysqli_num_rows($notificationCheckQuery) == 0) {
        if ($leaveStatus == 1) {
            $message = "🎉 Your Leave for $leaveDate Has Been Approved!";
            $queryInsert = "INSERT INTO tblnotifications (empid, message, is_read) 
                            VALUES ('$session_id', '$message', 0)";
            mysqli_query($conn, $queryInsert) or die(mysqli_error($conn));
        } elseif ($leaveStatus == 2) {
            $message = "😔 Sorry, Your Leave for $leaveDate Has Been Rejected.";
            $queryInsert = "INSERT INTO tblnotifications (empid, message, is_read) 
                            VALUES ('$session_id', '$message', 0)";
            mysqli_query($conn, $queryInsert) or die(mysqli_error($conn));
        }
    }
}

// Fetch notifications for the logged-in employee
$notificationsQuery = mysqli_query($conn, "
    SELECT * FROM tblnotifications 
    WHERE empid = '$session_id' 
    ORDER BY created_at DESC
") or die(mysqli_error($conn));
?>

<div class="header">
    <div class="header-left">
        <div class="menu-icon dw dw-menu"></div>
        <div class="search-toggle-icon dw dw-search2" data-toggle="header_search"></div>
    </div>
    <div class="header-right">
        <div class="dashboard-setting user-notification">
            <!-- Notification Bell Icon -->
            <div class="dropdown">
                <a class="dropdown-toggle no-arrow" href="javascript:;" data-toggle="dropdown">
                    <i class="dw dw-notification"></i> <!-- Notification bell icon -->
                </a>
                <div class="dropdown-menu">
                    <?php while ($notification = mysqli_fetch_array($notificationsQuery)): ?>
                        <a class="dropdown-item" href="javascript:;" 
                           style="background-color: <?php echo $notification['is_read'] == 0 ? '#f0f0f0' : '#fff'; ?>;">
                            <i class="dw dw-notification"></i> 
                            <?php echo $notification['message']; ?>
                        </a>
                    <?php endwhile; ?>
                </div>
            </div>
            <!-- Settings Icon -->
            <div class="dropdown">
                <a class="dropdown-toggle no-arrow" href="javascript:;" data-toggle="right-sidebar">
                    <i class="dw dw-settings2"></i> <!-- Settings icon -->
                </a>
            </div>
        </div>

        <div class="user-info-dropdown">
            <div class="dropdown">
                <?php 
                $query = mysqli_query($conn, "SELECT * FROM tblemployees WHERE emp_id = '$session_id'") or die(mysqli_error());
                $row = mysqli_fetch_array($query);
                ?>

                <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                    <span class="user-icon">
                        <img src="<?php echo (!empty($row['location'])) ? '../uploads/'.$row['location'] : '../uploads/NO-IMAGE-AVAILABLE.jpg'; ?>" alt="">
                    </span>
                    <span class="user-name"><?php echo $row['FirstName']; ?></span>
                </a>
                <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                    <a class="dropdown-item" href="my_profile.php"><i class="dw dw-user1"></i> Profile</a>
                    <a class="dropdown-item" href="../logout.php"><i class="dw dw-logout"></i> Log Out</a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .dashboard-setting {
        display: flex;
        align-items: center;
        gap: 15px; /* Space between icons */
    }

    .dw-notification, .dw-settings2 {
        font-size: 20px; /* Adjust icon size */
        color: #555; /* Default icon color */
        cursor: pointer; /* Pointer cursor for interactivity */
    }

    .dw-notification:hover, .dw-settings2:hover {
        color: #000; /* Change color on hover */
    }

    .user-icon img {
        width: 40px; /* Adjust size of profile image */
        height: 40px;
        border-radius: 50%; /* Make image circular */
        object-fit: cover; /* Ensure image fits well */
    }
</style>
