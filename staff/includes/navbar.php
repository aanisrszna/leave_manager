<div class="header">
    <div class="header-left">
        <div class="menu-icon dw dw-menu"></div>
        <div class="search-toggle-icon dw dw-search2" data-toggle="header_search"></div>
    </div>
    <div class="header-right">
        <div class="dashboard-setting user-notification">
            <!-- Notification Bell Icon -->

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
