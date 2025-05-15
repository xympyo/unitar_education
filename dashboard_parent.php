<?php
require_once 'db.php';
session_start();
// SESSION TIMEOUT: 6 hours (21600 seconds)
$timeout = 21600;
if (!isset($_SESSION['parent_email'])) {
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
// For demo: use parent with email from session or fallback
$parent_email = $_SESSION['parent_email'] ?? '';
$parent = $conn->query("SELECT * FROM parents_students WHERE email='$parent_email'")->fetch_assoc();
$student_id = $parent['student_id'] ?? 1;
$student_name = $parent['name'] ?? 'Unknown';
?>
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
    <?php
    $reports = [];
    $result = $conn->query("SELECT * FROM report_history WHERE student_id=$student_id");
    while ($row = $result->fetch_assoc()) {
        $reports[] = $row;
    }
    ?>
    <div class="sidebar">
        <div class="parent-name">Parent: <?php echo htmlspecialchars($student_name); ?></div>
        <nav class="nav">
            <button class="nav-btn active">Student Progress</button>
        </nav>
        <div class="sidebar-bottom">
            <button class="parental-access-btn" onclick="showParentalAccess()">Parental Access</button>
        </div>
    </div>
    <div class="main-content">
        <div class="main-scroll">
            <div class="dashboard-header">Student Progress Overview</div>
            <div class="progress-table-section">
                <style>
                .progress-table-section {
                    margin-bottom: 32px;
                }
                .progress-table {
                    width: 100%;
                    border-collapse: collapse;
                    background: #fff;
                    border-radius: 10px;
                    overflow: hidden;
                    box-shadow: 0 2px 12px rgba(24,119,242,0.06);
                    font-size: 1.01em;
                }
                .progress-table thead th {
                    background: #1877f2;
                    color: #fff;
                    padding: 14px 10px;
                    font-weight: 700;
                    border-bottom: 2px solid #e0eaff;
                }
                .progress-table tbody td {
                    padding: 12px 10px;
                    border-bottom: 1px solid #f2f2f2;
                    text-align: center;
                }
                .progress-table tbody tr:nth-child(even) {
                    background: #f6fafd;
                }
                .progress-table tbody tr:hover {
                    background: #e7f1ff;
                }
                .progress-table tbody td[colspan="6"] {
                    background: #f6fafd;
                    font-style: italic;
                }
                </style>
                <table class="progress-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Participation</th>
                            <th>Understanding</th>
                            <th>Behavior</th>
                            <th>Emotional</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
<?php if (count($reports) === 0): ?>
    <tr>
        <td colspan="6" style="text-align:center; color:#888; padding:32px 0; font-size:1.1em;">
            <i class="fas fa-info-circle" style="color:#1877f2;font-size:1.5em;"></i><br>No progress reports found for this student.
        </td>
    </tr>
<?php else: ?>
    <?php foreach ($reports as $report): ?>
        <tr>
            <td><?php echo htmlspecialchars($report['created_at'] ?? '-'); ?></td>
            <td><?php echo htmlspecialchars($report['participation'] ?? '-'); ?></td>
            <td><?php echo htmlspecialchars($report['understanding'] ?? '-'); ?></td>
            <td><?php echo htmlspecialchars($report['behavior'] ?? '-'); ?></td>
            <td><?php echo htmlspecialchars($report['emotional'] ?? '-'); ?></td>
            <td><?php echo htmlspecialchars($report['notes'] ?? '-'); ?></td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>
</tbody>
                </table>
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
                <div class="youtube-title" id="aiNotesTitle">AI Notes & Feedback</div>
                <div class="youtube-thumb" id="aiNotesSection">
                    <span id="aiNotesLoading">Loading AI recommendation...</span>
                    <span id="aiNotes" style="display:none;"></span>
                </div>
            </div>
            <div class="youtube-section">
                <div class="youtube-title" id="videoImproveTitle">YouTube: Improve Yourself</div>
                <div class="youtube-thumb" id="youtubeImproveSection">
                    <span id="youtubeImproveLoading">Loading video...</span>
                    <a id="youtubeImproveLink" href="#" target="_blank" style="display:none;">
                        <img id="youtubeImproveThumb" src="" alt="YouTube Thumbnail">
                    </a>
                </div>
            </div>
            <div class="youtube-section">
                <div class="youtube-title" id="videoLikeTitle">YouTube: You Might Like</div>
                <div class="youtube-thumb" id="youtubeLikeSection">
                    <span id="youtubeLikeLoading">Loading video...</span>
                    <a id="youtubeLikeLink" href="#" target="_blank" style="display:none;">
                        <img id="youtubeLikeThumb" src="" alt="YouTube Thumbnail">
                    </a>
                </div>
            </div>
            <!-- FontAwesome for icons -->
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
            <div class="bento-insights-grid">
                <div class="bento-card" id="futureCareerCard">
                    <div class="bento-icon"><i class="fas fa-rocket"></i></div>
                    <div class="bento-title">Future Career</div>
                    <div class="bento-content"><span id="futureCareerLoading">Loading...</span><span id="futureCareer" style="display:none;"></span></div>
                </div>
                <div class="bento-card" id="childInterestCard">
                    <div class="bento-icon"><i class="fas fa-heart"></i></div>
                    <div class="bento-title">Child Interest</div>
                    <div class="bento-content"><span id="childInterestLoading">Loading...</span><span id="childInterest" style="display:none;"></span></div>
                </div>
                <div class="bento-card" id="learningStyleCard">
                    <div class="bento-icon"><i class="fas fa-brain"></i></div>
                    <div class="bento-title">Learning Style</div>
                    <div class="bento-content"><span id="learningStyleLoading">Loading...</span><span id="learningStyle" style="display:none;"></span></div>
                </div>
                <div class="bento-card" id="recommendedHobbyCard">
                    <div class="bento-icon"><i class="fas fa-gamepad"></i></div>
                    <div class="bento-title">Recommended Hobby</div>
                    <div class="bento-content"><span id="recommendedHobbyLoading">Loading...</span><span id="recommendedHobby" style="display:none;"></span></div>
                </div>
                <div class="bento-card" id="parentTipsCard">
                    <div class="bento-icon"><i class="fas fa-lightbulb"></i></div>
                    <div class="bento-title">Parent Tips</div>
                    <div class="bento-content"><span id="parentTipsLoading">Loading...</span><span id="parentTips" style="display:none;"></span></div>
                </div>
            </div>
            <style>
            .bento-insights-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
                gap: 18px;
                margin: 32px 0 24px 0;
            }
            .bento-card {
                background: linear-gradient(120deg, #e9f4ff 70%, #c2e9fb 100%);
                border-radius: 18px;
                box-shadow: 0 4px 16px rgba(24,119,242,0.08);
                padding: 26px 18px 20px 18px;
                display: flex;
                flex-direction: column;
                align-items: flex-start;
                transition: transform 0.16s, box-shadow 0.16s;
                position: relative;
                min-height: 140px;
                cursor: pointer;
                border: 2px solid #e0eaff;
            }
            .bento-card:hover {
                transform: translateY(-5px) scale(1.03);
                box-shadow: 0 8px 24px rgba(24,119,242,0.18);
                border-color: #80c6ff;
                background: linear-gradient(120deg, #d6ecff 60%, #b3e0fa 100%);
            }
            .bento-icon {
                font-size: 2.2rem;
                color: #1877f2;
                margin-bottom: 12px;
            }
            .bento-title {
                font-size: 1.13rem;
                font-weight: 700;
                color: #165ecb;
                margin-bottom: 7px;
            }
            .bento-content {
                font-size: 1.01rem;
                color: #222;
                min-height: 32px;
            }
            @media (max-width: 700px) {
                .bento-insights-grid {
                    grid-template-columns: 1fr;
                }
            }
            </style>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                fetch('http://127.0.0.1:5000/ai_recommendation', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ parent_email: '<?php echo $_SESSION['parent_email']; ?>' })
                })
                .then(response => response.json())
                .then(data => {
                        // AI Notes
                        document.getElementById('aiNotesLoading').style.display = 'none';
                        if (data.ai_notes) {
                            document.getElementById('aiNotes').textContent = data.ai_notes;
                            document.getElementById('aiNotes').style.display = '';
                        } else if (data.error) {
                            document.getElementById('aiNotes').textContent = data.error;
                            document.getElementById('aiNotes').style.display = '';
                        }
                        // Video for Improvement
                        document.getElementById('youtubeImproveLoading').style.display = 'none';
                        if (data.youtube_link_to_improve) {
                            let ytId = extractYouTubeId(data.youtube_link_to_improve);
                            let thumbUrl = ytId ? `https://img.youtube.com/vi/${ytId}/hqdefault.jpg` : '';
                            // Check if video exists via oEmbed
                            if (ytId === 'dQw4w9WgXcQ') {
    document.getElementById('youtubeImproveSection').innerHTML = '<span style="color:#b00">Blocked video for your safety. Please try again for a different recommendation.</span>';
} else if (ytId) {
                                fetch(`https://www.youtube.com/oembed?url=https://www.youtube.com/watch?v=${ytId}&format=json`)
                                    .then(resp => {
                                        if (resp.ok) {
                                            // Check if thumbnail exists
                                            const img = new Image();
                                            img.onload = function() {
                                                document.getElementById('youtubeImproveLink').href = data.youtube_link_to_improve;
                                                document.getElementById('youtubeImproveThumb').src = thumbUrl;
                                                document.getElementById('youtubeImproveLink').style.display = '';
                                            };
                                            img.onerror = function() {
                                                document.getElementById('youtubeImproveSection').innerHTML = '<span style="color:#b00">YouTube thumbnail does not exist.</span>';
                                            };
                                            img.src = thumbUrl;
                                        } else {
                                            document.getElementById('youtubeImproveSection').innerHTML = '<span style="color:#b00">YouTube video does not exist.</span>';
                                        }
                                    })
                                    .catch(() => {
                                        document.getElementById('youtubeImproveSection').innerHTML = '<span style="color:#b00">Failed to check YouTube video.</span>';
                                    });
                            } else {
                                document.getElementById('youtubeImproveSection').innerHTML = '<span style="color:#b00">Invalid YouTube link.</span>';
                            }
                        } else {
                            document.getElementById('youtubeImproveSection').innerHTML = '<span style="color:#888">No YouTube video provided.<br>You should explore more interesting content together!</span>';
                        }
                        // Video You Might Like
                        document.getElementById('youtubeLikeLoading').style.display = 'none';
                        if (data.youtube_link_for_improving) {
                            let ytId = extractYouTubeId(data.youtube_link_for_improving);
                            let thumbUrl = ytId ? `https://img.youtube.com/vi/${ytId}/hqdefault.jpg` : '';
                            if (ytId === 'dQw4w9WgXcQ') {
    document.getElementById('youtubeImproveSection').innerHTML = '<span style="color:#b00">Blocked video for your safety. Please try again for a different recommendation.</span>';
} else if (ytId) {
                                fetch(`https://www.youtube.com/oembed?url=https://www.youtube.com/watch?v=${ytId}&format=json`)
                                    .then(resp => {
                                        if (resp.ok) {
                                            const img = new Image();
                                            img.onload = function() {
                                                document.getElementById('youtubeLikeLink').href = data.youtube_link_for_improving;
                                                document.getElementById('youtubeLikeThumb').src = thumbUrl;
                                                document.getElementById('youtubeLikeLink').style.display = '';
                                            };
                                            img.onerror = function() {
                                                document.getElementById('youtubeLikeSection').innerHTML = '<span style="color:#b00">YouTube thumbnail does not exist.</span>';
                                            };
                                            img.src = thumbUrl;
                                        } else {
                                            document.getElementById('youtubeLikeSection').innerHTML = '<span style="color:#b00">YouTube video does not exist.</span>';
                                        }
                                    })
                                    .catch(() => {
                                        document.getElementById('youtubeLikeSection').innerHTML = '<span style="color:#b00">Failed to check YouTube video.</span>';
                                    });
                            } else {
                                document.getElementById('youtubeLikeSection').innerHTML = '<span style="color:#b00">Invalid YouTube link.</span>';
                            }
                        } else {
                            document.getElementById('youtubeLikeSection').innerHTML = '<span style="color:#888">No YouTube video provided.<br>You should explore more interesting content together!</span>';
                        }
                        // AI Features
                        // Future Career
                        document.getElementById('futureCareerLoading').style.display = 'none';
                        if (data.future_career) {
                            document.getElementById('futureCareer').textContent = data.future_career;
                            document.getElementById('futureCareer').style.display = '';
                        }
                        // Child Interest
                        document.getElementById('childInterestLoading').style.display = 'none';
                        if (data.child_interest) {
                            document.getElementById('childInterest').textContent = data.child_interest;
                            document.getElementById('childInterest').style.display = '';
                        }
                        // Learning Style
                        document.getElementById('learningStyleLoading').style.display = 'none';
                        if (data.learning_style) {
                            document.getElementById('learningStyle').textContent = data.learning_style;
                            document.getElementById('learningStyle').style.display = '';
                        }
                        // Recommended Hobby
                        document.getElementById('recommendedHobbyLoading').style.display = 'none';
                        if (data.recommended_hobby) {
                            document.getElementById('recommendedHobby').textContent = data.recommended_hobby;
                            document.getElementById('recommendedHobby').style.display = '';
                        }
                        // Parent Tips
                        document.getElementById('parentTipsLoading').style.display = 'none';
                        if (data.parent_tips) {
                            document.getElementById('parentTips').textContent = data.parent_tips;
                            document.getElementById('parentTips').style.display = '';
                        }
                    })
                    .catch(err => {
                        document.getElementById('aiNotesLoading').style.display = 'none';
                        document.getElementById('aiNotes').textContent = 'Failed to load AI feedback.';
                        document.getElementById('aiNotes').style.display = '';
                        document.getElementById('youtubeImproveLoading').style.display = 'none';
                        document.getElementById('youtubeLikeLoading').style.display = 'none';
                        // AI Features loading fallback
                        document.getElementById('futureCareerLoading').style.display = 'none';
                        document.getElementById('futureCareer').textContent = 'Failed to load.';
                        document.getElementById('futureCareer').style.display = '';
                        document.getElementById('childInterestLoading').style.display = 'none';
                        document.getElementById('childInterest').textContent = 'Failed to load.';
                        document.getElementById('childInterest').style.display = '';
                        document.getElementById('learningStyleLoading').style.display = 'none';
                        document.getElementById('learningStyle').textContent = 'Failed to load.';
                        document.getElementById('learningStyle').style.display = '';
                        document.getElementById('recommendedHobbyLoading').style.display = 'none';
                        document.getElementById('recommendedHobby').textContent = 'Failed to load.';
                        document.getElementById('recommendedHobby').style.display = '';
                        document.getElementById('parentTipsLoading').style.display = 'none';
                        document.getElementById('parentTips').textContent = 'Failed to load.';
                        document.getElementById('parentTips').style.display = '';
                    });
                function extractYouTubeId(url) {
                    let match = url.match(/[?&]v=([^&#]+)/);
                    if (match && match[1]) return match[1];
                    match = url.match(/youtu\.be\/([^?&#]+)/);
                    return match && match[1] ? match[1] : null;
                }
            });
            </script>
        </div>
    </div>
    <!-- PARENTAL ACCESS MODAL -->
    <div class="parental-modal-bg" id="parentalModalBg" style="display:none;">
        <div class="parental-modal">
            <button class="close-parental-modal" onclick="closeParentalAccess()"><i class="fas fa-times"></i></button>
            <div class="parental-modal-title"><i class="fas fa-user-shield"></i> Parental Access</div>
            <div class="parental-modal-section">
                <div class="parental-section-title"><i class="fas fa-book"></i> Student Report History</div>
                <div id="parentalReportTableWrap">
                    <table class="parental-table">
                        <thead><tr><th>Class</th><th>Participation</th><th>Understanding</th><th>Behavior</th><th>Emotional</th><th>Notes</th></tr></thead>
                        <tbody id="parentalReportTableBody"><tr><td colspan="6">Loading...</td></tr></tbody>
                    </table>
                </div>
            </div>
            <div class="parental-modal-section">
                <div class="parental-section-title"><i class="fas fa-chalkboard-teacher"></i> Teacher Requests</div>
                <div id="parentalTeacherTableWrap">
                    <table class="parental-table">
                        <thead><tr><th>Teacher</th><th>Email</th><th>Action</th></tr></thead>
                        <tbody id="parentalTeacherTableBody"><tr><td colspan="3">Loading...</td></tr></tbody>
                    </table>
                </div>
            </div>
            <div class="parental-modal-section">
                <div class="parental-section-title"><i class="fas fa-edit"></i> Update Student Class</div>
                <form id="parentalClassForm" onsubmit="return updateClass();">
                    <input type="number" id="parentalClassInput" class="parental-input" min="1" max="12" required />
                    <button type="submit" class="submit-btn" style="width:auto;">Update</button>
                    <span id="parentalClassMsg"></span>
                </form>
            </div>
        </div>
        <div class="parental-modal-confirm-bg" id="parentalConfirmBg" style="display:none;">
            <div class="parental-modal-confirm">
                <div class="parental-confirm-title">Confirm Approval</div>
                <div class="parental-confirm-msg">Are you sure you want to approve this teacher's request?</div>
                <button class="submit-btn" id="parentalConfirmBtn">Confirm</button>
                <button class="close-btn" onclick="closeParentalConfirm()">Cancel</button>
            </div>
        </div>
    </div>
    <style>
    .parental-modal-bg {
        position: fixed; left: 0; top: 0; right: 0; bottom: 0;
        background: rgba(24,119,242,0.15); z-index: 2000;
        display: flex; justify-content: center; align-items: center;
    }
    .parental-modal {
        background: #fff; border-radius: 18px; box-shadow: 0 8px 36px rgba(24,119,242,0.18);
        padding: 36px 28px 28px 28px; min-width: 370px; max-width: 98vw; position: relative;
        animation: fadeUp 0.4s cubic-bezier(.77, 0, .18, 1);
    }
    .close-parental-modal {
        position: absolute; top: 18px; right: 18px; border: none; background: none; color: #1877f2;
        font-size: 1.3rem; cursor: pointer;
    }
    .parental-modal-title {
        font-size: 1.3rem; font-weight: 800; color: #1877f2; margin-bottom: 18px;
        display: flex; align-items: center; gap: 10px;
    }
    .parental-modal-section { margin-bottom: 28px; }
    .parental-section-title {
        font-size: 1.07rem; font-weight: 700; color: #165ecb; margin-bottom: 8px;
        display: flex; align-items: center; gap: 7px;
    }
    .parental-table {
        width: 100%; border-collapse: collapse; background: #fafdff; border-radius: 10px;
        overflow: hidden; box-shadow: 0 2px 10px rgba(24,119,242,0.06); margin-bottom: 12px;
    }
    .parental-table th, .parental-table td {
        padding: 10px 8px; border-bottom: 1px solid #e3eaf5; text-align: left;
    }
    .parental-table th { background: #e6f0ff; color: #1877f2; font-weight: 700; }
    .parental-table tbody tr:last-child td { border-bottom: none; }
    .parental-table td { color: #222; }
    .parental-input {
        padding: 7px 13px; border-radius: 7px; border: 1.5px solid #d3e4fc; font-size: 1rem;
        margin-right: 10px; width: 80px;
    }
    .parental-modal-confirm-bg {
        position: fixed; left: 0; top: 0; right: 0; bottom: 0;
        background: rgba(24,119,242,0.16); z-index: 2100; display: flex; justify-content: center; align-items: center;
    }
    .parental-modal-confirm {
        background: #fff; border-radius: 16px; box-shadow: 0 4px 18px rgba(24,119,242,0.14);
        padding: 28px 24px 18px 24px; min-width: 250px; text-align: center;
    }
    .parental-confirm-title { font-size: 1.1rem; font-weight: 700; color: #1877f2; margin-bottom: 10px; }
    .parental-confirm-msg { margin-bottom: 18px; color: #222; }
    </style>
    <script>
    function showParentalAccess() {
        document.getElementById('parentalModalBg').style.display = 'flex';
        loadParentalReport();
        loadParentalTeachers();
        loadParentalClass();
    }
    function closeParentalAccess() {
        document.getElementById('parentalModalBg').style.display = 'none';
    }
    function loadParentalReport() {
        fetch('parental_access.php?action=get_report_history')
            .then(res => res.json())
            .then(data => {
                let html = '';
                if (data.data && data.data.length) {
                    data.data.forEach(r => {
                        html += `<tr><td>${r.class||'-'}</td><td>${r.participation}</td><td>${r.understanding}</td><td>${r.behavior}</td><td>${r.emotional}</td><td>${r.notes||''}</td></tr>`;
                    });
                } else {
                    html = '<tr><td colspan="6">No report history found.</td></tr>';
                }
                document.getElementById('parentalReportTableBody').innerHTML = html;
            });
    }
    function loadParentalTeachers() {
        fetch('parental_access.php?action=get_teacher_requests')
            .then(res => res.json())
            .then(data => {
                let html = '';
                if (data.data && data.data.length) {
                    data.data.forEach(r => {
                        html += `<tr><td>${r.name}</td><td>${r.email}</td><td>`;
                        if (r.approved == 1) {
                            html += '<span style="color:#27c96e;font-weight:700;">Approved</span>';
                        } else {
                            html += `<button class='submit-btn' style='padding:6px 18px;border-radius:7px;background:#1877f2;color:#fff;border:none;cursor:pointer;' onclick='showParentalConfirm(${r.id})'>Approve</button>`;
                        }
                        html += '</td></tr>';
                    });
                } else {
                    html = '<tr><td colspan="3">No teacher requests found.</td></tr>';
                }
                document.getElementById('parentalTeacherTableBody').innerHTML = html;
            });
    }
    let approveId = null;
    function showParentalConfirm(id) {
        approveId = id;
        document.getElementById('parentalConfirmBg').style.display = 'flex';
        document.getElementById('parentalConfirmBtn').onclick = approveParentalTeacher;
    }
    function closeParentalConfirm() {
        document.getElementById('parentalConfirmBg').style.display = 'none';
        approveId = null;
    }
    function approveParentalTeacher() {
        if (!approveId) return;
        fetch('parental_access.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=approve_teacher&id=${approveId}`
        })
        .then(res => res.json())
        .then(data => {
            closeParentalConfirm();
            loadParentalTeachers();
        });
    }
    function loadParentalClass() {
        fetch('parental_access.php?action=get_report_history')
            .then(res => res.json())
            .then(data => {
                // Use first row's class if available
                let cls = (data.data && data.data.length) ? data.data[0].class : '';
                document.getElementById('parentalClassInput').value = cls || '';
            });
    }
    function updateClass() {
        let val = document.getElementById('parentalClassInput').value;
        fetch('parental_access.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=update_class&new_class=${val}`
        })
        .then(res => res.json())
        .then(data => {
            let msg = document.getElementById('parentalClassMsg');
            if (data.success) {
                msg.innerHTML = '<span style="color:#27c96e;font-weight:700;">Updated!</span>';
            } else {
                msg.innerHTML = `<span style='color:#e74c3c;'>${data.error||'Failed.'}</span>`;
            }
        });
        return false;
    }
    </script>
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
        // Data from PHP
        const reports = <?php echo json_encode($reports); ?>;
        const studentName = <?php echo json_encode($student_name); ?>;

        // Chart.js setup (real data)
        let barChart, pieChart;

        function renderCharts(period = 'day', className = '') {
            // Aggregate report data for the student
            const recentReports = reports.slice(0, 10); // Most recent 10
            const labels = recentReports.map(r => new Date(r.created_at).toLocaleDateString());
            const participation = recentReports.map(r => Number(r.participation));
            const understanding = recentReports.map(r => Number(r.understanding));
            const behavior = recentReports.map(r => Number(r.behavior));
            const emotional = recentReports.map(r => Number(r.emotional));
            // Bar chart: metrics over time
            if (barChart) barChart.destroy();
            barChart = new Chart(document.getElementById('barChart'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                            label: 'Participation',
                            data: participation,
                            backgroundColor: '#1877f2'
                        },
                        {
                            label: 'Understanding',
                            data: understanding,
                            backgroundColor: '#27c96e'
                        },
                        {
                            label: 'Behavior',
                            data: behavior,
                            backgroundColor: '#ffb300'
                        },
                        {
                            label: 'Emotional',
                            data: emotional,
                            backgroundColor: '#e74c3c'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    }
                }
            });
            // Pie chart: average metrics
            if (pieChart) pieChart.destroy();
            const avg = arr => arr.length ? arr.reduce((a, b) => a + b, 0) / arr.length : 0;
            pieChart = new Chart(document.getElementById('pieChart'), {
                type: 'pie',
                data: {
                    labels: ['Participation', 'Understanding', 'Behavior', 'Emotional'],
                    datasets: [{
                        data: [avg(participation), avg(understanding), avg(behavior), avg(emotional)],
                        backgroundColor: ['#1877f2', '#27c96e', '#ffb300', '#e74c3c']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    }
                }
            });
        }

        window.onload = function() {
            renderCharts();
            renderReportTable();
        }

        function renderReportTable() {
            let html = '';
            reports.forEach(r => {
                html += `<tr><td>${r.class}</td><td>${r.participation}</td><td>${r.understanding}</td><td>${r.behavior}</td><td>${r.emotional}</td><td>${r.notes || ''}</td></tr>`;
            });
            document.getElementById('reportTableBody').innerHTML = html;
        }

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

        // END renderCharts

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