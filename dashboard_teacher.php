<?php
require_once 'db.php';
session_start();
// SESSION TIMEOUT: 6 hours (21600 seconds)
$timeout = 21600;
if (!isset($_SESSION['teacher_email']) || !isset($_SESSION['teacher_id'])) {
    header('Location: login.php');
    exit();
}
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $timeout)) {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time();
// For demo: use teacher with user_id=1, email from session or fallback
// Ensure session teacher_id and teacher_email are always in sync and valid
$teacher_email = $_SESSION['teacher_email'] ?? '';
$teacher = $conn->query("SELECT * FROM teachers WHERE email='$teacher_email'")->fetch_assoc();
$teacher_id = $teacher['user_id'] ?? null;
if (!$teacher_id) {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit();
}
$_SESSION['teacher_id'] = $teacher_id;
$classes = [];
$students = [];
$result = $conn->query("SELECT DISTINCT class FROM report_history WHERE teacher_id=$teacher_id");
while ($row = $result->fetch_assoc()) {
    $classes[] = $row['class'];
}
// Only fetch students assigned to this teacher (approved requests)
$students_result = $conn->query("SELECT ps.* FROM teacher_student_requests tsr JOIN parents_students ps ON tsr.student_id = ps.student_id WHERE tsr.teacher_id = $teacher_id AND tsr.approved = 1");
while ($row = $students_result->fetch_assoc()) {
    $students[] = $row;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard - EduBridge</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:700,400&display=swap" rel="stylesheet">
    <style>
        /* Responsive enhancements - DO NOT REMOVE ANYTHING, ONLY ADD */
        html,
        body {
            max-width: 100vw;
            overflow-x: hidden;
        }

        .sidebar {
            min-width: 220px;
            width: 220px;
            background: #f6fafd;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            height: 100vh;
            z-index: 10;
            transition: left 0.2s;
        }

        .main-content {
            transition: margin 0.2s;
        }

        .main-scroll {
            padding: 24px 36px;
            min-height: 100vh;
        }

        @media (max-width: 900px) {
            .main-scroll {
                padding: 18px 8px;
            }

            .main-content {
                margin-left: 0;
            }

            .sidebar {
                position: relative;
                width: 100vw;
                min-width: 0;
                height: auto;
                display: flex;
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
                padding: 0 8px;
            }

            .main-content {
                margin-left: 0;
            }
        }

        @media (max-width: 600px) {
            .sidebar {
                flex-direction: column;
                align-items: flex-start;
                padding: 8px 4px;
                min-width: 0;
                width: 100vw;
                height: auto;
                position: relative;
            }

            .main-content {
                margin-left: 0;
            }

            .main-scroll {
                padding: 10px 2vw;
            }

            table,
            thead,
            tbody,
            th,
            td,
            tr {
                display: block;
                width: 100%;
                box-sizing: border-box;
            }

            table {
                border: none;
                box-shadow: none !important;
            }

            th,
            td {
                padding: 10px 6px !important;
                font-size: 1rem;
                border: none;
            }

            tr {
                margin-bottom: 12px;
                border-radius: 8px;
                background: #fff;
                box-shadow: 0 2px 10px rgba(24, 119, 242, 0.04);
            }

            .dashboard-header {
                font-size: 1.25rem;
                padding-bottom: 8px;
            }

            .dropdown,
            .dropdown-btn,
            .dropdown-list {
                width: 100% !important;
                min-width: 0 !important;
            }

            form,
            .submit-btn {
                width: 100% !important;
                min-width: 0 !important;
            }

            .search-bar input {
                font-size: 1rem;
                padding: 10px 8px;
                width: 100% !important;
            }

            /* Modal popup responsiveness */
            #assignConfirmModal>div {
                min-width: 80vw !important;
                padding: 18px 8vw 12px 8vw !important;
            }
        }
    </style>
    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
            background: #f6fafd;
            font-family: 'Montserrat', Arial, sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: row;
            overflow: hidden;
        }

        .sidebar {
            width: 230px;
            background: #1877f2;
            color: #fff;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            position: relative;
            min-height: 100vh;
            padding: 0 0 24px 0;
            z-index: 2;
        }

        .teacher-name {
            font-size: 1.25rem;
            font-weight: 700;
            padding: 32px 0 18px 32px;
            letter-spacing: 1px;
        }

        .nav {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 6px;
            padding-left: 18px;
        }

        .nav-btn {
            background: none;
            border: none;
            color: #fff;
            font-size: 1.08rem;
            font-weight: 600;
            text-align: left;
            padding: 13px 20px 13px 10px;
            border-radius: 12px 0 0 12px;
            cursor: pointer;
            transition: background 0.16s, color 0.16s;
        }

        .nav-btn.active {
            background: #fff;
            color: #1877f2;
        }

        .main-content {
            flex: 1 1 0%;
            height: 100vh;
            overflow-y: auto;
            background: #f6fafd;
            padding: 0;
            display: flex;
            flex-direction: column;
        }

        .main-scroll {
            padding: 38px 5vw 38px 5vw;
            max-width: 1050px;
        }

        .dashboard-header {
            font-size: 1.7rem;
            font-weight: 700;
            color: #1877f2;
            margin-bottom: 18px;
            overflow-x: hidden;
        }

        .search-bar {
            margin-bottom: 18px;
            width: 100%;
            display: flex;
            align-items: center;
        }

        .search-bar input {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid #d3e4fc;
            border-radius: 9px;
            font-size: 1rem;
            font-family: inherit;
            outline: none;
            transition: border 0.18s;
        }

        .search-bar input:focus {
            border-color: #1877f2;
        }

        .table-section {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(24, 119, 242, 0.09);
            padding: 18px 28px 18px 28px;
            margin-bottom: 30px;
            overflow-x: auto;
            width: 100%;
            box-sizing: border-box;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 1rem;
        }

        th,
        td {
            padding: 12px 8px;
            text-align: center;
        }

        thead {
            background: #1877f2;
            color: #fff;
        }

        tr:nth-child(even) {
            background: #f6fafd;
        }

        .dropdown {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .dropdown-btn {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid #d3e4fc;
            border-radius: 9px;
            background: #fff;
            color: #1877f2;
            font-size: 1rem;
            font-weight: 600;
            font-family: inherit;
            text-align: left;
            cursor: pointer;
            outline: none;
            transition: border 0.18s;
        }

        .dropdown-btn:focus,
        .dropdown-btn.active {
            border-color: #1877f2;
        }

        .dropdown-list {
            position: absolute;
            left: 0;
            right: 0;
            background: #fff;
            border: 1.5px solid #d3e4fc;
            border-radius: 0 0 9px 9px;
            max-height: 170px;
            overflow-y: auto;
            z-index: 10;
            box-shadow: 0 2px 10px rgba(24, 119, 242, 0.09);
        }

        .dropdown-item {
            padding: 10px 14px;
            cursor: pointer;
            color: #1877f2;
            transition: background 0.13s;
        }

        .dropdown-item:hover,
        .dropdown-item.active {
            background: #e7f1ff;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            font-size: 1rem;
            margin-bottom: 6px;
            color: #1877f2;
            font-weight: 600;
        }

        .notes-input {
            min-height: 60px;
            max-height: 120px;
            padding: 10px 14px;
            border: 1.5px solid #d3e4fc;
            border-radius: 9px;
            font-size: 1rem;
            font-family: inherit;
            resize: vertical;
            outline: none;
            transition: border 0.18s;
        }

        .notes-input:focus {
            border-color: #1877f2;
        }

        .form-actions {
            display: flex;
            justify-content: space-between;
            gap: 12px;
        }

        .submit-btn {
            background: linear-gradient(90deg, #27c96e 70%, #5af598 100%);
            color: #fff;
            border: none;
            border-radius: 13px;
            font-size: 1.04rem;
            font-weight: 700;
            padding: 13px 32px;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(24, 119, 242, 0.11);
            transition: background 0.18s, transform 0.18s;
        }

        .submit-btn:hover {
            background: linear-gradient(90deg, #1fae5b 70%, #5af598 100%);
            transform: translateY(-2px) scale(1.03);
        }

        .cancel-btn {
            background: linear-gradient(90deg, #ff3a3a 70%, #ff7e7e 100%);
            color: #fff;
            border: none;
            border-radius: 13px;
            font-size: 1.04rem;
            font-weight: 700;
            padding: 13px 32px;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(24, 119, 242, 0.11);
            transition: background 0.18s, transform 0.18s;
        }

        .cancel-btn:hover {
            background: linear-gradient(90deg, #d22c2c 70%, #ff7e7e 100%);
            transform: translateY(-2px) scale(1.03);
        }

        @media (max-width: 900px) {
            .main-scroll {
                padding: 22px 2vw 22px 2vw;
            }
        }

        @media (max-width: 700px) {
            .sidebar {
                width: 100px;
                min-width: 0;
            }

            .teacher-name {
                font-size: 1rem;
                padding-left: 12px;
            }

            .nav-btn {
                font-size: 0.97rem;
                padding: 10px 8px 10px 6px;
            }
        }

        @media (max-width: 480px) {
            .sidebar {
                width: 60px;
            }

            .main-scroll {
                padding: 8px 1vw 8px 1vw;
            }

            .dashboard-header {
                font-size: 1.1rem;
            }
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <div class="teacher-name">Teacher: <?php echo htmlspecialchars($teacher['name'] ?? 'Unknown'); ?></div>
        <nav class="nav">
            <button class="nav-btn active" id="navClass" onclick="showSection('class')">Class</button>
            <button class="nav-btn" id="navStudent" onclick="showSection('student')">Student</button>
            <button class="nav-btn" id="navAddReport" onclick="showSection('addReport')">Add Report</button>
            <button class="nav-btn" id="navAssignStudent" onclick="showSection('assignStudent')">Assign Student</button>
        </nav>
    </div>
    <div class="main-content">
        <div class="main-scroll" id="mainScroll"></div>
    </div>
    <script>
        // Section switching logic
        function showSection(section) {
            document.querySelectorAll('.nav-btn').forEach(btn => btn.classList.remove('active'));
            if (section === 'class') {
                document.getElementById('navClass').classList.add('active');
                renderClassSection();
            }
            if (section === 'student') {
                document.getElementById('navStudent').classList.add('active');
                renderStudentSection();
            }
            if (section === 'addReport') {
                document.getElementById('navAddReport').classList.add('active');
                renderAddReportSection();
            }
            if (section === 'assignStudent') {
                document.getElementById('navAssignStudent').classList.add('active');
                renderAssignStudentSection();
            }
        }
        // Minimal section rendering placeholders
        function renderClassSection() {
            let el = document.getElementById('mainScroll');
            if (!el) {
                let fallback = document.getElementById('mainContentFallback');
                if (!fallback) {
                    fallback = document.createElement('div');
                    fallback.id = 'mainContentFallback';
                    fallback.style.padding = '32px';
                    fallback.style.color = '#b00';
                    fallback.style.fontWeight = 'bold';
                    document.body.appendChild(fallback);
                }
                fallback.innerHTML = 'You have no class yet!';
                return;
            }
            // Fetch students assigned to this teacher, grouped by class
            fetch('fetch_teacher_classes.php', {
                    credentials: 'include'
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.classes || data.classes.length === 0) {
                        el.innerHTML = '<div class="dashboard-header">No students assigned yet.</div>';
                        return;
                    }
                    let html = '';
                    data.classes.forEach(cls => {
                        html += `<div style='margin-bottom:22px;'>`;
                        html += `<div style='font-size:1.7rem;font-weight:700;margin-bottom:18px;color:#1877f2;'>Class ${cls.class}</div>`;
                        html += `<table style='width:100%;border-collapse:collapse;background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 2px 10px rgba(24,119,242,0.08);margin-bottom:18px;'>`;
                        html += `<thead><tr style='background:#f6fafd;'><th style="padding:10px 8px; color:black;">Name</th><th style="padding:10px 8px; color:black;">Email</th><th style="padding:10px 8px; color:black;">Average Point</th></tr></thead><tbody>`;
                        cls.students.forEach(stu => {
                            html += `<tr><td style="padding:10px 8px;">${stu.name}</td><td style="padding:10px 8px;">${stu.email}</td><td style="padding:10px 8px;">${stu.avg !== null ? stu.avg : '-'}</td></tr>`;
                        });
                        html += `</tbody></table></div>`;
                    });
                    el.innerHTML = html;
                })
                .catch(() => {
                    el.innerHTML = '<div class="dashboard-header">Failed to load class data.</div>';
                });
        }

        function renderStudentSection() {
            let el = document.getElementById('mainScroll');
            if (!el) {
                let fallback = document.getElementById('mainContentFallback');
                if (!fallback) {
                    fallback = document.createElement('div');
                    fallback.id = 'mainContentFallback';
                    fallback.style.padding = '32px';
                    fallback.style.color = '#b00';
                    fallback.style.fontWeight = 'bold';
                    document.body.appendChild(fallback);
                }
                fallback.innerHTML = 'You have no student yet!';
                return;
            }
            el.innerHTML = '<div class="dashboard-header">Loading students...</div>';
            fetch('fetch_teacher_students.php', {
                    credentials: 'include'
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.students || data.students.length === 0) {
                        el.innerHTML = '<div class="dashboard-header">No students assigned yet.</div>';
                        return;
                    }
                    let html = `<div class='dashboard-header'>Student List</div>`;
                    html += `<table style='width:100%;border-collapse:collapse;background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 2px 10px rgba(24,119,242,0.08);margin-bottom:24px;'>`;
                    html += `<thead><tr style='background:#f6fafd;'><th style='padding:10px 8px; color:black;'>ID</th><th style='padding:10px 8px; color:black;'>Name</th><th style='padding:10px 8px; color:black;'>Classes</th><th style='padding:10px 8px; color:black;'>Average Point</th><th style='padding:10px 8px; color:black;'>Detail</th></tr></thead><tbody>`;
                    data.students.forEach((stu, idx) => {
                        html += `<tr style='text-align:center;'>`;
                        html += `<td style='padding:10px 8px;'>${stu.id}</td>`;
                        html += `<td style='padding:10px 8px;'>${stu.name}</td>`;
                        html += `<td style='padding:10px 8px;'>${stu.classes ?? '-'}</td>`;
                        html += `<td style='padding:10px 8px;'>${stu.avg !== null ? stu.avg : '-'}</td>`;
                        html += `<td style='padding:10px 8px;'><button class='detail-btn' data-idx='${idx}' style='padding:6px 18px;border-radius:7px;background:#1877f2;color:#fff;border:none;cursor:pointer;'>Detail</button></td>`;
                        html += `</tr>`;
                        html += `<tr class='report-row' id='report-row-${idx}' style='display:none;background:#f6fafd;'><td colspan='5'><div class='report-detail' style='padding:12px 8px;'></div></td></tr>`;
                    });
                    html += `</tbody></table>`;
                    el.innerHTML = html;

                    // Attach event listeners for detail buttons
                    document.querySelectorAll('.detail-btn').forEach(btn => {
                        btn.onclick = function() {
                            const idx = this.getAttribute('data-idx');
                            const row = document.getElementById('report-row-' + idx);
                            const detailDiv = row.querySelector('.report-detail');
                            if (row.style.display === 'none') {
                                // Fill report table
                                let reports = data.students[idx].reports;
                                if (!reports || reports.length === 0) {
                                    detailDiv.innerHTML = `<div style='color:#b00;padding:10px;'>No reports available for this student.</div>`;
                                } else {
                                    let rhtml = `<table style='width:100%;border-collapse:collapse;background:#fff;border-radius:7px;box-shadow:0 2px 10px rgba(24,119,242,0.06);'>`;
                                    rhtml += `<thead><tr style='background:gray;'><th style='padding:8px;'>Participation</th><th style='padding:8px;'>Understanding</th><th style='padding:8px;'>Behavior</th><th style='padding:8px;'>Emotional</th><th style='padding:8px;'>Notes</th><th style='padding:8px;'>Teacher</th></tr></thead><tbody>`;
                                    reports.forEach(rep => {
                                        rhtml += `<tr style='text-align:center;'>`;
                                        rhtml += `<td style='padding:8px;'>${rep.participation}</td>`;
                                        rhtml += `<td style='padding:8px;'>${rep.understanding}</td>`;
                                        rhtml += `<td style='padding:8px;'>${rep.behavior}</td>`;
                                        rhtml += `<td style='padding:8px;'>${rep.emotional}</td>`;
                                        rhtml += `<td style='padding:8px;'>${rep.notes ?? ''}</td>`;
                                        rhtml += `<td style='padding:8px;'>${rep.teacher_name ?? '-'}</td>`;
                                        rhtml += `</tr>`;
                                    });
                                    rhtml += `</tbody></table>`;
                                    detailDiv.innerHTML = rhtml;
                                }
                                // Slide down
                                row.style.display = '';
                                setTimeout(() => {
                                    row.style.transition = 'all 0.4s';
                                    row.style.height = row.scrollHeight + 'px';
                                }, 10);
                            } else {
                                // Slide up
                                row.style.transition = 'all 0.4s';
                                row.style.height = '0px';
                                setTimeout(() => {
                                    row.style.display = 'none';
                                }, 400);
                            }
                        };
                    });
                })
                .catch(() => {
                    el.innerHTML = '<div class="dashboard-header">Failed to load student data.</div>';
                });
        }

        function renderAssignStudentSection() {
            let el = document.getElementById('mainScroll');
            if (!el) {
                // Only show fallback if mainScroll is truly missing (should never happen)
                let fallback = document.getElementById('mainContentFallback');
                if (!fallback) {
                    fallback = document.createElement('div');
                    fallback.id = 'mainContentFallback';
                    fallback.style.padding = '32px';
                    fallback.style.color = '#b00';
                    fallback.style.fontWeight = 'bold';
                    document.body.appendChild(fallback);
                }
                fallback.innerHTML = 'Could not load assign student section.';
                return;
            } else {
                // Remove fallback if present
                let fallback = document.getElementById('mainContentFallback');
                if (fallback) fallback.remove();
            }
            el.innerHTML = `
        <div class="dashboard-header">Assign Student</div>
        <div class='search-bar'><input type='text' id='assignStudentSearch' placeholder='Search student by name...' style='width: 100%; padding: 10px; font-size: 1rem;'></div>
        <div id='assignStudentList' style='margin: 18px 0; max-height: 200px; overflow-y: auto; border: 1px solid #d3e4fc; border-radius: 8px; padding: 8px;'></div>
        <div id='assignStudentEmail' style='margin: 12px 0; color: #1877f2;'></div>
        <button id='assignStudentBtn' class='submit-btn' style='margin-top: 12px;' disabled>Add Student</button>
        <div id='assignStudentMsg' style='margin-top: 18px;'></div>
    `;

            // Fetch assignable students from backend
            fetch('fetch_assignable_classes_and_students.php', {
                    credentials: 'include'
                })
                .then(res => res.json())
                .then(data => {
                    let students = data.students || [];
                    let assignStudentList = document.getElementById('assignStudentList');
                    let assignStudentSearch = document.getElementById('assignStudentSearch');
                    let assignStudentBtn = document.getElementById('assignStudentBtn');
                    let assignStudentEmail = document.getElementById('assignStudentEmail');
                    let assignStudentMsg = document.getElementById('assignStudentMsg');
                    let selectedStudent = null;

                    function renderList(filter = '') {
                        let filtered = students.filter(s => s.name.toLowerCase().includes(filter.toLowerCase()));
                        let html = '';
                        if (filtered.length === 0) {
                            html = "<div style='color:#888;padding:12px;'>No students available for assignment.</div>";
                            assignStudentBtn.disabled = true;
                            assignStudentEmail.textContent = '';
                            selectedStudent = null;
                        } else {
                            filtered.forEach(stu => {
                                html += `<div class='dropdown-item' style='padding:10px 8px;cursor:pointer;' data-id='${stu.id}' data-email='${stu.email || ''}'>${stu.name} ${stu.classes ? '('+stu.classes+')' : ''}</div>`;
                            });
                        }
                        assignStudentList.innerHTML = html;
                    }
                    renderList();

                    assignStudentSearch.addEventListener('input', function() {
                        renderList(this.value);
                    });

                    assignStudentList.addEventListener('click', function(e) {
                        if (e.target.classList.contains('dropdown-item')) {
                            let id = e.target.getAttribute('data-id');
                            let stu = students.find(s => s.id == id);
                            selectedStudent = stu;
                            assignStudentEmail.textContent = stu.email ? stu.email : '';
                            assignStudentBtn.disabled = false;
                            // Highlight selected
                            document.querySelectorAll('#assignStudentList .dropdown-item').forEach(i => i.style.background = '');
                            e.target.style.background = '#e7f1ff';
                        }
                    });

                    assignStudentBtn.onclick = function() {
                        if (!selectedStudent) return;
                        showAssignConfirmPopup(function(confirmed) {
                            if (confirmed) doAssignStudent();
                        });
                        return;
                    };

                    // Modal popup implementation
                    function showAssignConfirmPopup(callback) {
                        // Remove any existing modal
                        let oldModal = document.getElementById('assignConfirmModal');
                        if (oldModal) oldModal.remove();
                        // Overlay
                        let overlay = document.createElement('div');
                        overlay.id = 'assignConfirmModal';
                        overlay.style.position = 'fixed';
                        overlay.style.top = '0';
                        overlay.style.left = '0';
                        overlay.style.width = '100vw';
                        overlay.style.height = '100vh';
                        overlay.style.background = 'rgba(0,0,0,0.25)';
                        overlay.style.display = 'flex';
                        overlay.style.alignItems = 'center';
                        overlay.style.justifyContent = 'center';
                        overlay.style.zIndex = '9999';
                        // Card
                        let card = document.createElement('div');
                        card.style.background = '#fff';
                        card.style.padding = '26px 32px 20px 32px';
                        card.style.borderRadius = '14px';
                        card.style.boxShadow = '0 4px 24px rgba(24,119,242,0.14)';
                        card.style.maxWidth = '90vw';
                        card.style.minWidth = '300px';
                        card.style.textAlign = 'center';
                        card.innerHTML = `<div style='font-size:1.15rem;font-weight:600;margin-bottom:18px;'>Are you sure you want to assign this student?</div>
                        <div style='display:flex;gap:18px;justify-content:center;'>
                            <button id='assignConfirmBtn' style='padding:9px 22px;background:#1877f2;color:#fff;border:none;border-radius:8px;font-weight:600;font-size:1rem;cursor:pointer;'>Confirm</button>
                            <button id='assignCancelBtn' style='padding:9px 22px;background:#eee;color:#333;border:none;border-radius:8px;font-weight:600;font-size:1rem;cursor:pointer;'>Cancel</button>
                        </div>`;
                        overlay.appendChild(card);
                        document.body.appendChild(overlay);
                        document.getElementById('assignConfirmBtn').onclick = function() {
                            overlay.remove();
                            callback(true);
                        };
                        document.getElementById('assignCancelBtn').onclick = function() {
                            overlay.remove();
                            callback(false);
                        };
                    }

                    // Assignment logic
                    function doAssignStudent() {
                        let teacherId = window.teacher_id || 1;
                        fetch('assign_student.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    teacher_id: teacherId,
                                    student_id: selectedStudent.id
                                })
                            })
                            .then(res => res.json())
                            .then(data => {
                                assignStudentMsg.textContent = data.status === 'success' ? 'Student assigned successfully!' : (data.message || 'Failed to assign student.');
                                assignStudentMsg.style.color = data.status === 'success' ? '#27c96e' : '#b00';
                                if (data.status === 'success') {
                                    // Remove assigned student from list
                                    students = students.filter(s => s.id != selectedStudent.id);
                                    renderList(assignStudentSearch.value);
                                    assignStudentEmail.textContent = '';
                                    assignStudentBtn.disabled = true;
                                }
                            })
                            .catch(() => {
                                assignStudentMsg.textContent = 'Network error.';
                                assignStudentMsg.style.color = '#b00';
                            });
                    }

                })
                .catch(() => {
                    document.getElementById('assignStudentList').innerHTML = "<div style='color:#b00;padding:12px;'>Failed to load students.</div>";
                });
        }

        function renderAddReportSection() {
            let el = document.getElementById('mainScroll');
            if (!el) {
                let fallback = document.getElementById('mainContentFallback');
                if (!fallback) {
                    fallback = document.createElement('div');
                    fallback.id = 'mainContentFallback';
                    fallback.style.padding = '32px';
                    fallback.style.color = '#b00';
                    fallback.style.fontWeight = 'bold';
                    document.body.appendChild(fallback);
                }
                fallback.innerHTML = 'Could not load add report section.';
                return;
            }
            el.innerHTML = '<div class="dashboard-header">Loading report form...</div>';
            fetch('fetch_teacher_students.php', {
                    credentials: 'include'
                })
                .then(res => res.json())
                .then(data => {
                    // Debug: log teacher_id and students
                    console.log('Teacher Students Response:', data);
                    let students = data.students || [];
                    // Extract unique classes from assigned students
                    let classes = [...new Set(students.map(s => s.classes).filter(Boolean))];
                    let classInput = '';
                    let studentInput = '';
                    let scoreDropdown = (id, label) => `
                        <div style='margin-bottom:14px;'>
                            <label for='${id}' style='display:block;font-weight:600;margin-bottom:5px;'>${label}</label>
                            <select id='${id}' style='width:100%;padding:9px 12px;border-radius:7px;border:1.5px solid #d3e4fc;font-size:1rem;'>
                                <option value=''>Select</option>
                                <option value='1'>1</option>
                                <option value='2'>2</option>
                                <option value='3'>3</option>
                                <option value='4'>4</option>
                            </select>
                        </div>`;
                    // Custom class dropdown
                    classInput = `
                        <div style='margin-bottom:14px;position:relative;'>
                            <label for='reportClassInput' style='display:block;font-weight:600;margin-bottom:5px;'>Class</label>
                            <div class='dropdown' style='width:100%;'>
                                <button type='button' id='btn_reportClassInput' class='dropdown-btn' style='width:100%;padding:9px 12px;border-radius:7px;border:1.5px solid #d3e4fc;font-size:1rem;text-align:left;background:#fff;'>Select Class</button>
                                <div id='list_reportClassInput' class='dropdown-list' style='display:none;position:absolute;width:100%;max-height:180px;overflow-y:auto;background:#fff;border:1.5px solid #d3e4fc;border-radius:7px;z-index:10;box-shadow:0 2px 10px rgba(24,119,242,0.06);'></div>
                            </div>
                        </div>`;
                    // Custom student dropdown
                    studentInput = `
                        <div style='margin-bottom:14px;position:relative;'>
                            <label for='reportStudentInput' style='display:block;font-weight:600;margin-bottom:5px;'>Student Name</label>
                            <div class='dropdown' style='width:100%;'>
                                <button type='button' id='btn_reportStudentInput' class='dropdown-btn' style='width:100%;padding:9px 12px;border-radius:7px;border:1.5px solid #d3e4fc;font-size:1rem;text-align:left;background:#fff;'>Select Student</button>
                                <div id='list_reportStudentInput' class='dropdown-list' style='display:none;position:absolute;width:100%;max-height:180px;overflow-y:auto;background:#fff;border:1.5px solid #d3e4fc;border-radius:7px;z-index:10;box-shadow:0 2px 10px rgba(24,119,242,0.06);'></div>
                            </div>
                        </div>`;
                    // Notes input
                    let notesInput = `
                        <div style='margin-bottom:16px;'>
                            <label for='reportNotes' style='display:block;font-weight:600;margin-bottom:5px;'>Notes</label>
                            <textarea id='reportNotes' maxlength='500' style='width:70%;min-height:60px;max-height:120px;padding:10px 14px;border:1.5px solid #d3e4fc;border-radius:7px;font-size:1rem;resize:vertical;'></textarea>
                            <div id='notesCounter' style='text-align:right;font-size:0.95rem;color:#888;margin-top:2px;'>0 / 500</div>
                        </div>`;
                    let formHtml = `
                        <div class='dashboard-header'>Add Report</div>
                        <form id='addReportForm' style='background:#fff;padding:24px 22px 18px 22px;border-radius:13px;box-shadow:0 2px 10px rgba(24,119,242,0.06);'>
                            ${classInput}
                            ${studentInput}
                            ${scoreDropdown('reportParticipation','Participation')}
                            ${scoreDropdown('reportUnderstanding','Understanding')}
                            ${scoreDropdown('reportBehavior','Behavior')}
                            ${scoreDropdown('reportEmotional','Emotional')}
                            ${notesInput}
                            <button type='submit' class='submit-btn' style='margin-top:10px;width:100%;'>Submit Report</button>
                            <div id='addReportMsg' style='margin-top:14px;'></div>
                        </form>`;
                    el.innerHTML = formHtml;

                    // Notes counter
                    const notesInputEl = document.getElementById('reportNotes');
                    const notesCounter = document.getElementById('notesCounter');
                    notesInputEl.addEventListener('input', function() {
                        notesCounter.textContent = `${this.value.length} / 500`;
                    });

                    // --- Custom Dropdown Logic ---
                    let selectedClass = null;
                    let selectedStudent = null;
                    // Class dropdown
                    const btnClass = document.getElementById('btn_reportClassInput');
                    const listClass = document.getElementById('list_reportClassInput');
                    btnClass.onclick = function(e) {
                        listClass.style.display = listClass.style.display === 'block' ? 'none' : 'block';
                        renderClassDropdown('');
                    };
                    btnClass.addEventListener('dblclick', function() {
                        // Reset filter: show all classes and students
                        selectedClass = null;
                        btnClass.textContent = 'Select Class';
                        btnClass.removeAttribute('data-value');
                        selectedStudent = null;
                        btnStudent.textContent = 'Select Student';
                        btnStudent.removeAttribute('data-value');
                        btnStudent.removeAttribute('data-id');
                        renderStudentDropdown('');
                    });

                    function renderClassDropdown(filter) {
                        let filtered;
                        // If a student is selected, only show that student's class
                        if (selectedStudent) {
                            let stu = students.find(s => s.id == selectedStudent);
                            filtered = stu ? [stu.classes] : classes;
                        } else {
                            filtered = classes.filter(c => !filter || c.toLowerCase().includes(filter.toLowerCase()));
                        }
                        let html = '';
                        [...new Set(filtered)].forEach(c => {
                            html += `<div class='dropdown-item' style='padding:10px 8px;cursor:pointer;' data-value='${c}'>${c}</div>`;
                        });
                        if (!html) html = '<div style="padding:12px;color:#888;">No classes found.</div>';
                        listClass.innerHTML = html;
                        listClass.querySelectorAll('.dropdown-item').forEach(item => {
                            item.onclick = function() {
                                btnClass.textContent = this.textContent;
                                listClass.style.display = 'none';
                                btnClass.setAttribute('data-value', this.getAttribute('data-value'));
                                selectedClass = this.getAttribute('data-value');
                                // Reset student selection when class changes
                                selectedStudent = null;
                                btnStudent.textContent = 'Select Student';
                                btnStudent.removeAttribute('data-value');
                                btnStudent.removeAttribute('data-id');
                                renderStudentDropdown('');
                            };
                        });
                    }
                    document.addEventListener('click', function(e) {
                        if (!btnClass.contains(e.target) && !listClass.contains(e.target)) listClass.style.display = 'none';
                    });

                    // Student dropdown
                    const btnStudent = document.getElementById('btn_reportStudentInput');
                    const listStudent = document.getElementById('list_reportStudentInput');
                    btnStudent.onclick = function(e) {
                        listStudent.style.display = listStudent.style.display === 'block' ? 'none' : 'block';
                        renderStudentDropdown('');
                    };

                    function renderStudentDropdown(filter) {
                        let filtered = students.filter(s => {
                            if (selectedClass) return s.classes == selectedClass && (!filter || s.name.toLowerCase().includes(filter.toLowerCase()));
                            return !filter || s.name.toLowerCase().includes(filter.toLowerCase());
                        });
                        let html = '';
                        filtered.forEach(s => {
                            html += `<div class='dropdown-item' style='padding:10px 8px;cursor:pointer;' data-id='${s.id}' data-value='${s.name}' data-class='${s.classes}'>${s.name}</div>`;
                        });
                        if (!html) html = '<div style="padding:12px;color:#888;">No students found.</div>';
                        listStudent.innerHTML = html;
                        listStudent.querySelectorAll('.dropdown-item').forEach(item => {
                            item.onclick = function() {
                                btnStudent.textContent = this.textContent;
                                listStudent.style.display = 'none';
                                btnStudent.setAttribute('data-value', this.getAttribute('data-value'));
                                btnStudent.setAttribute('data-id', this.getAttribute('data-id'));
                                selectedStudent = this.getAttribute('data-id');
                                // When student is selected, restrict class dropdown to that class only
                                selectedClass = this.getAttribute('data-class');
                                btnClass.textContent = selectedClass;
                                btnClass.setAttribute('data-value', selectedClass);
                            };
                        });
                    }
                    document.addEventListener('click', function(e) {
                        if (!btnStudent.contains(e.target) && !listStudent.contains(e.target)) listStudent.style.display = 'none';
                    });
                    // Handle form submission
                    document.getElementById('addReportForm').onsubmit = function(e) {
                        e.preventDefault();
                        // Get class and student from custom dropdowns
                        let classVal = document.getElementById('btn_reportClassInput').getAttribute('data-value');
                        let student_id = document.getElementById('btn_reportStudentInput').getAttribute('data-id');
                        let participation = document.getElementById('reportParticipation').value;
                        let understanding = document.getElementById('reportUnderstanding').value;
                        let behavior = document.getElementById('reportBehavior').value;
                        let emotional = document.getElementById('reportEmotional').value;
                        let notes = document.getElementById('reportNotes').value.trim();
                        let msgEl = document.getElementById('addReportMsg');
                        if (!classVal || !student_id || !participation || !understanding || !behavior || !emotional) {
                            msgEl.textContent = 'Please fill all required fields.';
                            msgEl.style.color = '#b00';
                            return;
                        }
                        fetch('add_report.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                credentials: 'include',
                                body: JSON.stringify({
                                    cls: classVal,
                                    student_id,
                                    participation,
                                    understanding,
                                    behavior,
                                    emotional,
                                    notes
                                })
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.status === 'success') {
                                    msgEl.textContent = 'Report successfully added!';
                                    msgEl.style.color = '#27c96e';
                                    document.getElementById('addReportForm').reset();
                                    notesCounter.textContent = '0 / 500';
                                } else {
                                    msgEl.textContent = data.message || 'Failed to add report.';
                                    msgEl.style.color = '#b00';
                                }
                            })
                            .catch(() => {
                                msgEl.textContent = 'Network error.';
                                msgEl.style.color = '#b00';
                            });
                    };
                })
                .catch(() => {
                    el.innerHTML = '<div class="dashboard-header">Failed to load add report form.</div>';
                });
        }
        document.getElementById('assignStudentBtn').disabled = false;
        // Highlight selected
        document.querySelectorAll('#assignStudentList .dropdown-item').forEach(i => i.style.background = '');
        target.style.background = '#e7f1ff';
        document.getElementById('assignStudentBtn').onclick = function() {
            if (!currentSelected) return;
            if (!confirm('Are you sure?')) return;
            let teacherId = window.teacher_id || 1;
            fetch('assign_student.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        teacher_id: teacherId,
                        student_id: currentSelected.id
                    })
                })
                .then(res => res.json())
                .then(data => {
                    document.getElementById('assignStudentMsg').textContent = data.status === 'success' ? 'Student assigned successfully!' : (data.message || 'Failed to assign student.');
                    document.getElementById('assignStudentMsg').style.color = data.status === 'success' ? '#27c96e' : '#b00';
                })
                .catch(() => {
                    document.getElementById('assignStudentMsg').textContent = 'Network error.';
                    document.getElementById('assignStudentMsg').style.color = '#b00';
                });
        };




        function setupDropdowns() {
            window.openDropdown = null;
            document.addEventListener('click', function(e) {
                if (window.openDropdown && !e.target.closest('.dropdown')) {
                    document.getElementById('list_' + window.openDropdown).style.display = 'none';
                    window.openDropdown = null;
                }
            });
        }

        function toggleDropdown(id) {
            const list = document.getElementById('list_' + id);
            if (list.style.display === 'block') {
                list.style.display = 'none';
                window.openDropdown = null;
            } else {
                document.querySelectorAll('.dropdown-list').forEach(dl => dl.style.display = 'none');
                list.style.display = 'block';
                window.openDropdown = id;
            }
        }

        function selectDropdown(id, value) {
            document.getElementById('btn_' + id).textContent = value;
            document.getElementById('list_' + id).style.display = 'none';
            window.openDropdown = null;
            if (id === 'class') {
                window.selectedClass = value;
                // Filter students by class
                const students = classes.find(c => c.name === value).students.map(s => s.name);
                document.getElementById('dropdown_student').outerHTML = dropdown('student', students, '', '', true);
            }
            if (id === 'student') {
                window.selectedStudent = value;
            }
            if (["participation", "understanding", "behavior", "emotional"].includes(id)) {
                // For numbers
                document.getElementById('btn_' + id).textContent = value;
            }
        }

        function searchDropdown(id, val) {
            val = val.toLowerCase();
            let items = [];
            if (id === 'student') {
                let pool = window.selectedClass ? classes.find(c => c.name === window.selectedClass).students : allStudents;
                items = pool.filter(s => s.name.toLowerCase().includes(val)).map(s => s.name);
            }
            let html = items.map(item => `<div class='dropdown-item' onclick='selectDropdown("${id}","${item}")'>${item}</div>`).join('');
            document.getElementById('list_' + id).innerHTML = `<div style='padding:6px;'><input type='text' style='width:95%;padding:7px 8px;border:1px solid #d3e4fc;border-radius:7px;' placeholder='Search...' value='${val}' oninput='searchDropdown("${id}",this.value)'></div>` + html;
        }

        function submitReportForm() {
            // Gather form values
            const cls = document.getElementById('btn_class').textContent;
            const stu = document.getElementById('btn_student').textContent;
            const participation = document.getElementById('btn_participation').textContent;
            const understanding = document.getElementById('btn_understanding').textContent;
            const behavior = document.getElementById('btn_behavior').textContent;
            const emotional = document.getElementById('btn_emotional').textContent;
            const notes = document.getElementById('notesInput').value;
            // Algorithm: ready for backend
            if ([cls, stu, participation, understanding, behavior, emotional].some(x => x.startsWith('Select'))) {
                alert('Please fill all fields.');
                return false;
            }
            // Here, you would send this data to the backend via AJAX/fetch
            // Example (pseudo):
            // fetch('/submit_report.php', { method: 'POST', body: JSON.stringify({cls,stu,participation,understanding,behavior,emotional,notes}) })
            alert('Report submitted!\nClass: ' + cls + '\nStudent: ' + stu + '\nParticipation: ' + participation + '\nUnderstanding: ' + understanding + '\nBehavior: ' + behavior + '\nEmotional: ' + emotional + '\nNotes: ' + notes);
            showSection('class');
            return false;
        }
        // Initial load
        showSection('class');

        // Ensure Class section is rendered by default if no section is selected
        if (!window.sectionInitialized) {
            showSection('class');
            window.sectionInitialized = true;
        }
    </script>
</body>