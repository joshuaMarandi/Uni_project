<?php include 'includes/header.php'; ?>

<div class="container">
    <h1>Create a New Project</h1>
    <form action="submit_project.php" method="post">
        <div class="form-group">
            <label for="title">Project Title:</label>
            <input type="text" id="title" name="title" required>
        </div>
        <div class="form-group">
            <label for="student">Student Name:</label>
            <input type="text" id="student" name="student" required>
        </div>
        <div class="form-group">
            <label for="supervisor">Supervisor Name:</label>
            <input type="text" id="supervisor" name="supervisor" required>
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea id="description" name="description" required></textarea>
        </div>
        <button type="submit" class="btn">Create Project</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
