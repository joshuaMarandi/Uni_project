<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University Project Management System</title>
    <style>
        /* Monochromatic Gray Theme */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            color: #333;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #333;
            color: #fff;
            padding: 15px;
            text-align: center;
        }
        .container {
            width: 80%;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1, h2 {
            color: #444;
        }
        img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .section {
            margin-bottom: 30px;
        }
        .section h2 {
            border-bottom: 2px solid #ccc;
            padding-bottom: 10px;
        }
        .button-container {
            text-align: center;
            margin-top: 20px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            color: #fff;
            background-color: #555;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #333;
        }
        h1{
            color:white;
        }
    </style>
</head>
<body>
    <header>
        <h1>University Project Management System</h1>
    </header>

    <div class="container">
        <section class="section">
            <h2>Welcome to the University Project Management System</h2>
            <p>This application is designed to streamline and enhance the management of student projects in universities. It provides a centralized platform for students, supervisors, and coordinators to manage and track project progress effectively.</p>
            <img src="images/overview.jpg" alt="Overview of the Application">
        </section>

        <section class="section">
            <h2>Features</h2>
            <ul>
                <li><strong>Student Management:</strong> Supervisors can easily manage student details, including registration numbers, names, and project titles.</li>
                <li><strong>Progress Tracking:</strong> Supervisors can track student visits and provide comments on their progress.</li>
                <li><strong>Assessment Monitoring:</strong> Coordinators can view and assess the progress of students, including those who are ready to present or are not eligible.</li>
                <li><strong>Comments and Feedback:</strong> Continuous feedback from supervisors and coordinators ensures students stay on track with their projects.</li>
            </ul>
            <img src="images/features.jpg" alt="Features of the Application">
        </section>

        <section class="section">
            <h2>How It Works</h2>
            <p>Our system is designed to be intuitive and user-friendly. Here’s a brief overview of how it works:</p>
            <ol>
                <li><strong>For Supervisors:</strong> Log in to your account, fill in student details, track their progress, and provide feedback.</li>
                <li><strong>For Coordinators:</strong> Monitor the overall progress, assess students, and manage assessments.</li>
                <li><strong>For Students:</strong> Access your project details, receive feedback, and stay updated on your project status.</li>
            </ol>
            <img src="images/how-it-works.jpg" alt="How It Works">
        </section>

        <div class="button-container">
            <a href="supervisor_dashboard.php" class="btn">Get Started</a>
        </div>
    </div>
</body>
</html>
