<div class="header">
    <div class="header-left">
        <div class="menu-icon dw dw-menu"></div>
        <div class="search-toggle-icon dw dw-search2" data-toggle="header_search"></div>
    </div>
    <div class="header-right">
        <div class="dashboard-setting user-notification">
            <!-- Notification Bell Icon -->
            <div class="dropdown">
                <?php
                // Fetch notifications
                $notificationQuery = mysqli_query($conn, "
                    SELECT id, RegRemarks, is_read 
                    FROM tblleave 
                    WHERE empid = '$session_id' 
                    ORDER BY id DESC 
                    LIMIT 5;
                ") or die(mysqli_error($conn));

                // Count unread notifications
                $unreadCountQuery = mysqli_query($conn, "
                    SELECT COUNT(*) AS unread_count 
                    FROM tblleave 
                    WHERE empid = '$session_id' AND is_read = 0;
                ") or die(mysqli_error($conn));
                $unreadCount = mysqli_fetch_assoc($unreadCountQuery)['unread_count'];
                ?>
                <a class="dropdown-toggle no-arrow" href="javascript:;" role="button" data-toggle="dropdown">
                    <i class="dw dw-notification"></i>
                    <?php if ($unreadCount > 0): ?>
                        <span class="notification-alert"></span> <!-- Red badge -->
                    <?php endif; ?>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <h6 class="dropdown-header">Notifications</h6>
                    <?php
                    if (mysqli_num_rows($notificationQuery) > 0) {
                        while ($notification = mysqli_fetch_assoc($notificationQuery)) {
                            $bgColor = $notification['is_read'] == 0 ? 'background-color: #f5f5f5;' : 'background-color: #fff;';
                            $message = $notification['RegRemarks'] == 1 
                                ? '🎉 Your Leave Has Been Approved!' 
                                : ($notification['RegRemarks'] == 2 ? '😔 Sorry, Your Leave Has Been Rejected.' : 'Leave status pending.');
                            echo "
                                <div class='notification-item' style='{$bgColor} padding: 10px; border-bottom: 1px solid #ddd;'>
                                    {$message}
                                </div>
                            ";
                        }
                    } else {
                        echo "<div class='notification-item' style='padding: 10px;'>No new notifications.</div>";
                    }
                    ?>
                </div>
            </div>
            <!-- Settings Icon -->
            <div class="dropdown">
                <a class="dropdown-toggle no-arrow" href="javascript:;" data-toggle="right-sidebar">
                    <i class="dw dw-settings2"></i>
                </a>
            </div>
        </div>

        <div class="user-info-dropdown">
            <div class="dropdown">
                <?php 
                $query = mysqli_query($conn, "SELECT * FROM tblemployees WHERE empid = '$session_id'") or die(mysqli_error($conn));
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
    .notification-alert {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 8px;
        height: 8px;
        background-color: red;
        border-radius: 50%;
    }

    .dropdown-menu {
        width: 300px;
    }

    .dropdown-header {
        font-weight: bold;
        padding: 10px;
        border-bottom: 1px solid #ddd;
    }

    .notification-item {
        padding: 10px;
        font-size: 14px;
        cursor: pointer;
    }

    .notification-item:hover {
        background-color: #e9e9e9;
    }
</style>
