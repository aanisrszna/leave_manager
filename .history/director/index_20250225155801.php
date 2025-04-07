<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Management</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        /* Container to hold both sections */
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
            width: 90%;
            margin: auto;
        }

        /* Calendar Section */
        .calendar-container {
            width: 100%;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        /* Calendar Table */
        .calendar table {
            width: 100%;
            border-collapse: collapse;
        }

        .calendar td {
            padding: 15px;
            text-align: center;
            border: 1px solid #ddd;
        }

        /* Highlighted Dates */
        .highlight {
            background-color: orange;
            color: white;
            font-weight: bold;
        }

        /* Table Section */
        .table-container {
            width: 100%;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        /* Table Styles */
        .leave-table {
            width: 100%;
            border-collapse: collapse;
        }

        .leave-table th, .leave-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .leave-table th {
            background-color: #f2f2f2;
        }

        /* No Data Row */
        .no-data {
            text-align: center;
            color: gray;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                width: 100%;
                padding: 10px;
            }

            .calendar-container, .table-container {
                width: 100%;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <!-- Calendar Section -->
        <div class="calendar-container">
            <div class="calendar">
                <table>
                    <tr>
                        <td>3</td>
                        <td>4</td>
                        <td>5</td>
                        <td class="highlight">8</td>
                    </tr>
                    <tr>
                        <td>10</td>
                        <td>11</td>
                        <td class="highlight">12</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Table Section -->
        <div class="table-container">
            <h2>All Applications</h2>
            <table class="leave-table">
                <thead>
                    <tr>
                        <th>Staff Name</th>
                        <th>Leave Type</th>
                        <th>Applied Date</th>
                        <th>Manager Status</th>
                        <th>Director Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="6" class="no-data">No data available in table</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
