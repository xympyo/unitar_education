<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Dashboard - EduBridge</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:700,400&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        .parent-name {
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

        .sidebar-bottom {
            position: absolute;
            bottom: 36px;
            width: 100%;
            display: flex;
            justify-content: center;
        }

        .parental-access-btn {
            background: linear-gradient(90deg, rgb(79, 131, 170) 70%, #3ab7ff 100%);
            color: #fff;
            border: none;
            border-radius: 18px;
            font-size: 1.05rem;
            font-weight: 700;
            padding: 13px 32px;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(24, 119, 242, 0.11);
            transition: background 0.18s, transform 0.18s;
        }

        .parental-access-btn:hover {
            background: linear-gradient(90deg, #165ecb 70%, #1f9fff 100%);
            transform: translateY(-2px) scale(1.03);
        }

        .main-content {
            flex: 1 1 0%;
            height: 100vh;
            overflow-y: auto;
            background: #f6fafd;
            padding: 0 0 0 0;
            display: flex;
            flex-direction: column;
        }

        .main-scroll {
            padding: 38px 5vw 38px 5vw;
            max-width: 950px;
            margin: 0 auto;
        }

        .dashboard-header {
            font-size: 1.7rem;
            font-weight: 700;
            color: #1877f2;
            margin-bottom: 18px;
        }

        .dashboard-controls {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }

        .dashboard-controls select,
        .dashboard-controls button {
            padding: 8px 18px;
            border-radius: 11px;
            border: 1.5px solid #d3e4fc;
            background: #fff;
            color: #1877f2;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: border 0.16s, background 0.16s;
        }

        .dashboard-controls button.active,
        .dashboard-controls button:focus {
            background: #1877f2;
            color: #fff;
            border: 1.5px solid #1877f2;
        }

        .charts-row {
            display: flex;
            gap: 32px;
            margin-bottom: 28px;
            flex-wrap: wrap;
        }

        .chart-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(24, 119, 242, 0.09);
            padding: 24px 18px 18px 18px;
            flex: 1 1 320px;
            min-width: 290px;
            max-width: 420px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .chart-title {
            font-size: 1.12rem;
            font-weight: 600;
            color: #1877f2;
            margin-bottom: 10px;
        }

        .youtube-section {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(24, 119, 242, 0.09);
            padding: 18px 18px 16px 18px;
            margin-bottom: 24px;
        }

        .youtube-title {
            font-size: 1.07rem;
            font-weight: 600;
            color: #1877f2;
            margin-bottom: 10px;
        }

        .youtube-thumb {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .youtube-thumb img {
            width: 130px;
            height: 80px;
            border-radius: 8px;
            object-fit: cover;
            box-shadow: 0 2px 8px rgba(24, 119, 242, 0.09);
        }

        .youtube-link {
            color: #1877f2;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.16s;
        }

        .youtube-link:hover {
            color: #0e5bb5;
        }

        @media (max-width: 900px) {
            .charts-row {
                flex-direction: column;
                gap: 18px;
            }

            .main-scroll {
                padding: 22px 2vw 22px 2vw;
            }
        }

        @media (max-width: 700px) {
            .sidebar {
                width: 100px;
                min-width: 0;
            }

            .parent-name {
                font-size: 1rem;
                padding-left: 12px;
            }

            .nav-btn {
                font-size: 0.97rem;
                padding: 10px 8px 10px 6px;
            }

            .sidebar-bottom {
                bottom: 16px;
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

        /* PIN Popup */
        .pin-popup-bg {
            position: fixed;
            left: 0;
            top: 0;
            right: 0;
            bottom: 0;
            background: rgba(24, 119, 242, 0.12);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 100;
        }

        .pin-popup {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(24, 119, 242, 0.13);
            padding: 36px 32px 28px 32px;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-width: 270px;
            animation: fadeUp 0.4s cubic-bezier(.77, 0, .18, 1);
        }

        .pin-title {
            color: #1877f2;
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 18px;
        }

        .pin-input-row {
            display: flex;
            gap: 9px;
            margin-bottom: 16px;
        }

        .pin-block {
            width: 32px;
            height: 42px;
            background: #e7f1ff;
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 700;
            color: #1877f2;
            letter-spacing: 2px;
            border: 2px solid #d3e4fc;
        }

        .pin-popup input[type="number"] {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .pin-error {
            color: #ff3a3a;
            font-size: 0.98rem;
            margin-bottom: 10px;
            min-height: 20px;
        }

        .pin-popup .close-btn {
            margin-top: 6px;
            background: none;
            border: none;
            color: #1877f2;
            font-size: 1.1rem;
            cursor: pointer;
        }

        .submit-btn {
            width: 100%;
            padding: 12px 0;
            background: linear-gradient(90deg, #1877f2 70%, #3ab7ff 100%);
            color: #fff;
            font-size: 1.04rem;
            font-weight: 700;
            border: none;
            border-radius: 13px;
            box-shadow: 0 2px 10px rgba(24, 119, 242, 0.11);
            cursor: pointer;
            transition: background 0.18s, transform 0.18s;
            margin-top: 8px;
        }

        .submit-btn:hover {
            background: linear-gradient(90deg, #165ecb 70%, #1f9fff 100%);
            transform: translateY(-2px) scale(1.03);
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <div class="parent-name">Parent: John Doe</div>
        <nav class="nav">
            <button class="nav-btn active">Student Progress</button>
            <!-- More nav items can be added here -->
        </nav>
        <div class="sidebar-bottom">
            <button class="parental-access-btn" onclick="openPinPopup()">Parental Access</button>
        </div>
    </div>
    <div class="main-content">
        <div class="main-scroll">
            <div class="dashboard-header">Student Progress Overview</div>
            <div class="dashboard-controls">
                <button class="active" onclick="setPeriod('day', this)">Day</button>
                <button onclick="setPeriod('week', this)">Week</button>
                <button onclick="setPeriod('month', this)">Month</button>
                <select id="classSelect" onchange="changeClass()">
                    <option value="classA">Math</option>
                    <option value="classB">Biology</option>
                    <option value="classC">Physics</option>
                </select>
            </div>
            <div class="charts-row">
                <div class="chart-card">
                    <div class="chart-title">Participation, Understanding, Behavior, Emotional</div>
                    <canvas id="barChart" width="320" height="220"></canvas>
                </div>
                <div class="chart-card">
                    <div class="chart-title">Student Strengths (Pie Chart)</div>
                    <canvas id="pieChart" width="320" height="220"></canvas>
                </div>
            </div>
            <div class="youtube-section">
                <div class="youtube-title">Notes to Intake</div>
                <div class="youtube-thumb">
                    <span>Placeholder here</span>
                </div>
            </div>
            <div class="youtube-section">
                <div class="youtube-title">Recommended Video for Improvement</div>
                <div class="youtube-thumb">
                    <a href="https://www.youtube.com/watch?v=dQw4w9WgXcQ" class="youtube-link" target="_blank">
                        <img src="https://img.youtube.com/vi/dQw4w9WgXcQ/hqdefault.jpg" alt="YouTube Thumbnail">
                    </a>
                    <span>How to Improve Participation</span>
                </div>
            </div>
            <div class="youtube-section">
                <div class="youtube-title">Video for Student's Excellence</div>
                <div class="youtube-thumb">
                    <a href="https://www.youtube.com/watch?v=9bZkp7q19f0" class="youtube-link" target="_blank">
                        <img src="https://img.youtube.com/vi/9bZkp7q19f0/hqdefault.jpg" alt="YouTube Thumbnail">
                    </a>
                    <span>Celebrating Student Success</span>
                </div>
            </div>
        </div>
    </div>
    <!-- PIN POPUP -->
    <div class="pin-popup-bg" id="pinPopupBg">
        <div class="pin-popup">
            <div class="pin-title">Input your parental PIN</div>
            <div class="pin-input-row" onclick="focusPinInput()">
                <div class="pin-block" id="pinBlock0"></div>
                <div class="pin-block" id="pinBlock1"></div>
                <div class="pin-block" id="pinBlock2"></div>
                <div class="pin-block" id="pinBlock3"></div>
                <div class="pin-block" id="pinBlock4"></div>
                <div class="pin-block" id="pinBlock5"></div>
                <input type="number" id="pinInput" maxlength="6" oninput="handlePinInput()" />
            </div>
            <div class="pin-error" id="pinError"></div>
            <button class="submit-btn" onclick="submitPin()">Submit</button>
            <button class="close-btn" onclick="closePinPopup()">Cancel</button>
        </div>
    </div>
    <script>
        // Chart.js setup (beginner-friendly, dummy data)
        let barChart, pieChart;

        function renderCharts(period = 'day', className = 'classA') {
            const ctxBar = document.getElementById('barChart').getContext('2d');
            const ctxPie = document.getElementById('pieChart').getContext('2d');
            // Dummy data
            const dataMap = {
                day: [2, 3, 2, 4],
                week: [3, 3, 3, 2],
                month: [4, 2, 3, 3]
            };
            const pieMap = {
                classA: [6, 8, 4, 7],
                classB: [3, 5, 7, 6],
                classC: [8, 4, 5, 8]
            };
            if (barChart) barChart.destroy();
            if (pieChart) pieChart.destroy();
            barChart = new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: ['Participation', 'Understanding', 'Behavior', 'Emotional'],
                    datasets: [{
                        label: 'Score (1-4)',
                        data: dataMap[period],
                        backgroundColor: [
                            '#1877f2', '#3ab7ff', '#4dd0e1', '#b388ff'
                        ],
                        borderRadius: 7
                    }]
                },
                options: {
                    responsive: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 4,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
            pieChart = new Chart(ctxPie, {
                type: 'pie',
                data: {
                    labels: ['Participation', 'Understanding', 'Behavior', 'Emotional'],
                    datasets: [{
                        data: pieMap[className],
                        backgroundColor: [
                            '#1877f2', '#3ab7ff', '#4dd0e1', '#b388ff'
                        ]
                    }]
                },
                options: {
                    responsive: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }

        function setPeriod(period, btn) {
            document.querySelectorAll('.dashboard-controls button').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            renderCharts(period, document.getElementById('classSelect').value);
        }

        function changeClass() {
            renderCharts(document.querySelector('.dashboard-controls button.active').textContent.toLowerCase(), document.getElementById('classSelect').value);
        }
        window.onload = function() {
            renderCharts();
        };
        // PIN Popup logic
        function openPinPopup() {
            document.getElementById('pinPopupBg').style.display = 'flex';
            focusPinInput();
            clearPinBlocks();
        }

        function closePinPopup() {
            document.getElementById('pinPopupBg').style.display = 'none';
            document.getElementById('pinInput').value = '';
            clearPinBlocks();
            document.getElementById('pinError').textContent = '';
        }

        function focusPinInput() {
            document.getElementById('pinInput').focus();
        }

        function clearPinBlocks() {
            for (let i = 0; i < 6; i++) document.getElementById('pinBlock' + i).textContent = '';
        }

        function handlePinInput() {
            const val = document.getElementById('pinInput').value.replace(/\D/g, '').slice(0, 6);
            document.getElementById('pinInput').value = val;
            for (let i = 0; i < 6; i++) {
                document.getElementById('pinBlock' + i).textContent = val[i] ? '•' : '';
            }
            document.getElementById('pinError').textContent = '';
        }

        function submitPin() {
            const pin = document.getElementById('pinInput').value;
            // Dummy PIN: 123456 (change logic as needed)
            if (pin.length !== 6) {
                document.getElementById('pinError').textContent = 'PIN must be 6 digits.';
                return;
            }
            if (pin === '123456') {
                closePinPopup();
                // Replace main content with full report (dummy table)
                const mainScroll = document.querySelector('.main-scroll');
                mainScroll.innerHTML = `
            <div class="dashboard-header">Full Student Report</div>
            <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;background:#fff;border-radius:12px;box-shadow:0 2px 10px rgba(24,119,242,0.09);margin-bottom:30px;">
                <thead style="background:#1877f2;color:#fff;">
                    <tr>
                        <th style='padding:12px 8px;'>Teacher</th>
                        <th style='padding:12px 8px;'>Class</th>
                        <th style='padding:12px 8px;'>Participation</th>
                        <th style='padding:12px 8px;'>Understanding</th>
                        <th style='padding:12px 8px;'>Behavior</th>
                        <th style='padding:12px 8px;'>Emotional</th>
                        <th style='padding:12px 8px;'>Teacher Note</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style='text-align:center;'>
                        <td style='padding:10px 8px;'>Mr. Smith</td>
                        <td style='padding:10px 8px;'>Class A</td>
                        <td style='padding:10px 8px;'>3</td>
                        <td style='padding:10px 8px;'>4</td>
                        <td style='padding:10px 8px;'>3</td>
                        <td style='padding:10px 8px;'>2</td>
                        <td style='padding:10px 8px;'>2</td>
                    </tr>
                    <tr style='text-align:center;background:#f6fafd;'>
                        <td style='padding:10px 8px;'>Ms. Lee</td>
                        <td style='padding:10px 8px;'>Class B</td>
                        <td style='padding:10px 8px;'>4</td>
                        <td style='padding:10px 8px;'>2</td>
                        <td style='padding:10px 8px;'>4</td>
                        <td style='padding:10px 8px;'>4</td>
                        <td style='padding:10px 8px;'>4</td>
                    </tr>
                </tbody>
            </table>
            </div>
            <button class="parental-access-btn" onclick="window.location.reload()">Back to Dashboard</button>
        `;
            } else {
                document.getElementById('pinError').textContent = 'Incorrect PIN.';
            }
        }
    </script>
</body>

</html>