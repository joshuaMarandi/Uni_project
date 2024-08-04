<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export Report</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            background-color: #f4f4f4;
        }

        .container {
            padding: 20px;
            max-width: 800px;
            margin: auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
            /* text-align: center; */
            margin-left: 30%;
            margin-bottom: 20px;
        }

        .btn-export {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 4px;
            color: #fff;
            text-decoration: none;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin: 10px;
            text-align: center;
            transition: background-color 0.3s;
            background-color: #17a2b8; /* Teal color */
        }
        .btn-export-1 {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 4px;
            color: yellowgreen;
            text-decoration: none;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin: 10px;
            text-align: center;
            transition: background-color 0.3s;
            background-color: #17a2b8; /* Teal color */
        }
        .btn-export:hover {
            background-color: #138496; /* Darker teal */
        }

        .btn-export i {
            margin-right: 8px;
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }

            .btn-export {
                padding: 8px 16px;
                font-size: 14px;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 5px;
            }

            .btn-export {
                padding: 6px 12px;
                font-size: 12px;
                display: block;
                width: 100%;
                box-sizing: border-box;
                text-align: center;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Export the Report</h1>
    
    <a href="export_report.php?category=verified" class="btn btn-export">
        <i class="fas fa-file-csv"></i> Verified students
    </a>
    <a href="export_report.php?category=not_verified" class="btn btn-export-1">
        <i class="fas fa-file-csv"></i> Not Verified students
    </a>
    <a href="coordinator_dashboard.php" class="btn btn-export"  class="btn btn-back">
    <i class="fas fa-chevron-left"></i>Back</a>
</div>

</body>
</html>
