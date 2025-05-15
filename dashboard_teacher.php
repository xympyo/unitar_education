<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard - EduBridge</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:700,400&display=swap" rel="stylesheet">
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
        <div class="teacher-name">Teacher: Jane Smith</div>
        <nav class="nav">
            <button class="nav-btn active" id="navClass" onclick="showSection('class')">Class</button>
            <button class="nav-btn" id="navStudent" onclick="showSection('student')">Student</button>
            <button class="nav-btn" id="navAddReport" onclick="showSection('addReport')">Add Report</button>
        </nav>
    </div>
    <div class="main-content">
        <div class="main-scroll" id="mainScroll">
            <!-- Content will be injected here -->
        </div>
    </div>
    <script>
        // Dummy data
        const classes = [{
                name: 'Math',
                students: [{
                        name: 'Alice',
                        participation: 2,
                        understanding: 3,
                        behavior: 2,
                        emotional: 4
                    }, {
                        name: 'Alice',
                        participation: 2,
                        understanding: 3,
                        behavior: 2,
                        emotional: 4
                    }, {
                        name: 'Alice',
                        participation: 2,
                        understanding: 3,
                        behavior: 2,
                        emotional: 4
                    }, {
                        name: 'Alice',
                        participation: 2,
                        understanding: 3,
                        behavior: 2,
                        emotional: 4
                    }, {
                        name: 'Alice',
                        participation: 2,
                        understanding: 3,
                        behavior: 2,
                        emotional: 4
                    }, {
                        name: 'Alice',
                        participation: 2,
                        understanding: 3,
                        behavior: 2,
                        emotional: 4
                    }, {
                        name: 'Alice',
                        participation: 2,
                        understanding: 3,
                        behavior: 2,
                        emotional: 4
                    }, {
                        name: 'Alice',
                        participation: 2,
                        understanding: 3,
                        behavior: 2,
                        emotional: 4
                    },
                    {
                        name: 'Bob',
                        participation: 4,
                        understanding: 2,
                        behavior: 1,
                        emotional: 3
                    },
                    {
                        name: 'Charlie',
                        participation: 3,
                        understanding: 2,
                        behavior: 4,
                        emotional: 2
                    }
                ]
            },
            {
                name: 'Biology',
                students: [{
                        name: 'David',
                        participation: 2,
                        understanding: 2,
                        behavior: 3,
                        emotional: 2
                    },
                    {
                        name: 'Eva',
                        participation: 4,
                        understanding: 4,
                        behavior: 4,
                        emotional: 4
                    }
                ]
            },
            {
                name: 'Physics',
                students: [{
                        name: 'David',
                        participation: 2,
                        understanding: 2,
                        behavior: 3,
                        emotional: 2
                    },
                    {
                        name: 'Eva',
                        participation: 4,
                        understanding: 4,
                        behavior: 4,
                        emotional: 4
                    }
                ]
            }
        ];
        let allStudents = [];
        classes.forEach(cls => {
            cls.students.forEach(stu => allStudents.push({
                ...stu,
                class: cls.name
            }));
        });

        function avg(stu) {
            return (stu.participation + stu.understanding + stu.behavior + stu.emotional) / 4;
        }

        function showSection(section) {
            document.querySelectorAll('.nav-btn').forEach(btn => btn.classList.remove('active'));
            if (section === 'class') document.getElementById('navClass').classList.add('active');
            if (section === 'student') document.getElementById('navStudent').classList.add('active');
            if (section === 'addReport') document.getElementById('navAddReport').classList.add('active');
            if (section === 'class') renderClassSection();
            if (section === 'student') renderStudentSection();
            if (section === 'addReport') renderAddReportSection();
        }

        function renderClassSection() {
            let html = `<div class='dashboard-header'>Your Classes</div>`;
            classes.forEach(cls => {
                html += `<div class='table-section'><div class='form-label' style='margin-bottom:8px;'>${cls.name}</div><table><thead><tr><th>Student</th><th>Avg</th></tr></thead><tbody>`;
                let sorted = [...cls.students].sort((a, b) => avg(a) - avg(b));
                sorted.forEach(stu => {
                    html += `<tr><td>${stu.name}</td><td>${avg(stu).toFixed(2)}</td></tr>`;
                });
                html += `</tbody></table></div>`;
            });
            document.getElementById('mainScroll').innerHTML = html;
        }

        function renderStudentSection() {
            let html = `<div class='dashboard-header'>All Students</div>`;
            html += `<div class='search-bar'><input type='text' id='studentSearch' placeholder='Search student by name...' oninput='filterStudentList()'></div>`;
            html += `<div class='table-section'><table><thead><tr><th>Student</th><th>Class</th><th>Participation</th><th>Understanding</th><th>Behavior</th><th>Emotional</th><th>Avg</th></tr></thead><tbody id='studentTableBody'></tbody></table></div>`;
            document.getElementById('mainScroll').innerHTML = html;
            renderStudentTable(allStudents);
        }

        function renderStudentTable(list) {
            let sorted = [...list].sort((a, b) => avg(a) - avg(b));
            let rows = sorted.map(stu => `<tr><td>${stu.name}</td><td>${stu.class}</td><td>${stu.participation}</td><td>${stu.understanding}</td><td>${stu.behavior}</td><td>${stu.emotional}</td><td>${avg(stu).toFixed(2)}</td></tr>`).join('');
            document.getElementById('studentTableBody').innerHTML = rows;
        }

        function filterStudentList() {
            const val = document.getElementById('studentSearch').value.toLowerCase();
            const filtered = allStudents.filter(stu => stu.name.toLowerCase().includes(val));
            renderStudentTable(filtered);
        }

        function renderAddReportSection() {
            let html = `<div class='dashboard-header'>Add Report</div>`;
            html += `<div class='table-section'><form id='reportForm' onsubmit='return submitReportForm()'>`;
            html += `<div class='form-group'><label class='form-label'>Class</label>${dropdown('class', classes.map(c=>c.name), '', 'selectClass(this)')}</div>`;
            html += `<div class='form-group'><label class='form-label'>Student Name</label>${dropdown('student', allStudents.map(s=>s.name), '', '', true)}</div>`;
            ['Participation', 'Understanding', 'Behavior', 'Emotional'].forEach(metric => {
                html += `<div class='form-group'><label class='form-label'>${metric}</label>${dropdown(metric.toLowerCase(), [1,2,3,4], '', '', false, true)}</div>`;
            });
            html += `<div class='form-group'><label class='form-label'>Notes</label><textarea class='notes-input' maxlength='300' id='notesInput' placeholder='Type up to 300 characters...'></textarea></div>`;
            html += `<div class='form-actions'><button type='button' class='cancel-btn' onclick='showSection("class")'>Cancel</button><button type='submit' class='submit-btn'>Submit</button></div>`;
            html += `</form></div>`;
            document.getElementById('mainScroll').innerHTML = html;
            window.selectedClass = '';
            window.selectedStudent = '';
            window.dropdownData = {
                class: classes.map(c => c.name),
                student: allStudents.map(s => s.name)
            };
            setupDropdowns();
        }

        function dropdown(id, list, selected, onChange, searchable, numbersOnly) {
            let btnVal = selected || `Select ${id.charAt(0).toUpperCase()+id.slice(1)}`;
            return `<div class='dropdown' id='dropdown_${id}'>
        <button type='button' class='dropdown-btn' onclick='toggleDropdown("${id}")' id='btn_${id}'>${btnVal}</button>
        <div class='dropdown-list' id='list_${id}' style='display:none;'>
            ${searchable ? `<div style='padding:6px;'><input type='text' style='width:95%;padding:7px 8px;border:1px solid #d3e4fc;border-radius:7px;' placeholder='Search...' oninput='searchDropdown("${id}",this.value)'></div>` : ''}
            ${list.map(item=>`<div class='dropdown-item' onclick='selectDropdown("${id}","${item}")'>${item}</div>`).join('')}
        </div>
    </div>`;
        }

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
    </script>
</body>

</html>