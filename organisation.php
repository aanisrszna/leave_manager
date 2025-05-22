<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Organization Chart</title>
    <style>
        .org-chart {
            display: flex;
            justify-content: center;
            flex-direction: column;
            align-items: center;
        }
        .level {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }
        .box {
            border: 2px solid #444;
            padding: 10px 15px;
            border-radius: 10px;
            margin: 0 10px;
            text-align: center;
            background: #f0f0f0;
            font-family: Arial, sans-serif;
        }
        .highlight {
            border: 2px solid green;
        }
        .line {
            height: 20px;
            border-left: 2px solid #444;
            margin: 0 auto;
        }
    </style>
</head>
<body>

<div class="org-chart">
    <!-- Top Directors -->
    <div class="level">
        <div class="box">TEH CHEE HOE<br><small>(Director)</small></div>
        <div class="box">TEH TZE WEY<br><small>(Business Development Director)</small></div>
        <div class="box">TAN YU JIAN<br><small>(Director)</small></div>
    </div>

    <!-- Middle Management -->
    <div class="level">
        <div class="box">CHEONG KOK JENG<br><small>(IT Technical Manager)</small></div>
        <div class="box">NOOR A'KASHAH ABDUL HALIM<br><small>(Finance & Account)</small></div>
        <div class="box highlight">NUR EDRINNA<br><small>(Business Development Executive)</small></div>
        <div class="box">SASHIKALA<br><small>(Admin & Procurement Assistant Manager)</small></div>
    </div>

    <!-- Subordinates -->
    <div class="level">
        <div class="box">MURPHY CHAN<br><small>(IT Technical Engineer)</small></div>
        <div class="box">MUHAMAD FAZREEN<br><small>(IT Technical Engineer)</small></div>
        <div class="box">ANIS RUSZANNA<br><small>(Business Development)</small></div>
    </div>
</div>

</body>
</html>
