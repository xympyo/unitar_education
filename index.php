<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spark Education</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:700,400&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Montserrat', Arial, sans-serif;
            background: #f6fafd;
            color: #222;
            box-sizing: border-box;
            max-width: 100vw;
            overflow-x: hidden;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 40px;
            background: #1877f2;
            height: 70px;
            box-shadow: 0 3px 12px rgba(24, 119, 242, 0.07);
        }

        .logo {
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .logo-img {
            width: 44px;
            height: 44px;
            margin-right: 10px;
        }

        .logo-text {
            color: #fff;
            font-size: 1.7rem;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .header-buttons a {
            margin-left: 18px;
            padding: 10px 28px;
            border-radius: 24px;
            background: #fff;
            color: #1877f2;
            font-weight: 600;
            text-decoration: none;
            box-shadow: 0 2px 8px rgba(24, 119, 242, 0.08);
            transition: background 0.2s, color 0.2s, box-shadow 0.2s;
        }

        .header-buttons a:hover {
            background: #e3efff;
            color: #165ecb;
            box-shadow: 0 4px 16px rgba(24, 119, 242, 0.15);
        }

        .hero {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 70vh;
            text-align: center;
            position: relative;
        }

        .hero-text {
            font-size: 2.7rem;
            font-weight: 700;
            color: #1877f2;
            margin-bottom: 32px;
            letter-spacing: 1px;
            opacity: 0;
            transform: translateY(30px);
            animation: fadeUp 1.1s cubic-bezier(.77, 0, .18, 1) 0.3s forwards;
        }

        @keyframes fadeUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero-buttons {
            display: flex;
            gap: 28px;
            margin-bottom: 30px;
        }

        .hero-buttons a {
            padding: 16px 38px;
            border-radius: 30px;
            background: linear-gradient(90deg, #1877f2 70%, #3ab7ff 100%);
            color: #fff;
            font-size: 1.1rem;
            font-weight: 600;
            text-decoration: none;
            box-shadow: 0 4px 14px rgba(24, 119, 242, 0.14);
            transition: background 0.2s, transform 0.2s;
        }

        .hero-buttons a:hover {
            background: linear-gradient(90deg, #165ecb 70%, #1f9fff 100%);
            transform: translateY(-4px) scale(1.04);
        }

        .content-section {
            background: #fff;
            margin: 0 auto;
            max-width: 950px;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(24, 119, 242, 0.09);
            padding: 44px 38px 36px 38px;
            margin-bottom: 60px;
            animation: contentFadeIn 1.3s cubic-bezier(.77, 0, .18, 1) 0.7s both;
        }

        @keyframes contentFadeIn {
            from {
                opacity: 0;
                transform: translateY(40px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .content-section h2 {
            color: #1877f2;
            font-size: 2rem;
            margin-bottom: 16px;
        }

        .content-section ul {
            color: #2d3a4b;
            font-size: 1.1rem;
            line-height: 1.7;
            padding-left: 22px;
        }

        .content-section .card-row {
            display: flex;
            gap: 22px;
            margin-top: 32px;
            flex-wrap: wrap;
        }

        .content-section .card {
            flex: 1 1 220px;
            background: #f6fafd;
            border-radius: 12px;
            padding: 22px 18px;
            box-shadow: 0 2px 8px rgba(24, 119, 242, 0.07);
            min-width: 220px;
            margin-bottom: 18px;
            transition: box-shadow 0.2s, transform 0.2s;
        }

        .content-section .card:hover {
            box-shadow: 0 6px 24px rgba(24, 119, 242, 0.13);
            transform: translateY(-3px) scale(1.03);
        }

        @media (max-width: 1024px) {
            .header {
                padding: 0 18px;
            }

            .content-section {
                max-width: 98vw;
                padding: 28px 10px 22px 10px;
            }
        }

        @media (max-width: 700px) {
            .header {
                flex-direction: column;
                height: auto;
                padding: 14px 4vw 10px 4vw;
                min-width: 0;
            }

            .logo-img {
                width: 36px;
                height: 36px;
            }

            .logo-text {
                font-size: 1.2rem;
            }

            .header-buttons a {
                margin-left: 8px;
                padding: 8px 14px;
                font-size: 0.98rem;
            }

            .hero-text {
                font-size: 1.3rem;
                padding: 0 2vw;
            }

            .hero-buttons {
                flex-direction: column;
                gap: 14px;
                width: 100%;
                align-items: center;
            }

            .hero-buttons a {
                width: 80vw;
                max-width: 320px;
                padding: 13px 0;
                font-size: 1rem;
            }

            .content-section {
                padding: 14px 2vw;
                max-width: 99vw;
            }

            .content-section .card-row {
                flex-direction: column;
                gap: 10px;
            }

            .content-section .card {
                min-width: 0;
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .header {
                padding: 10px 2vw 6px 2vw;
            }

            .logo-img {
                width: 28px;
                height: 28px;
            }

            .logo-text {
                font-size: 0.98rem;
            }

            .header-buttons a {
                padding: 7px 7vw;
                font-size: 0.93rem;
                margin-left: 4px;
            }

            .hero-text {
                font-size: 1.02rem;
                padding: 0 1vw;
            }

            .hero-buttons a {
                width: 90vw;
                max-width: 99vw;
                padding: 11px 0;
                font-size: 0.98rem;
            }

            .content-section {
                border-radius: 9px;
                padding: 7px 1vw;
                max-width: 99vw;
            }

            .content-section h2 {
                font-size: 1.15rem;
            }

            .content-section ul {
                font-size: 0.97rem;
            }

            .content-section .card-row {
                gap: 7px;
            }
        }
    </style>
</head>

<body>
    <header class="header">
        <a href="index.php" class="logo">
            <span class="logo-text">Spark Education</span>
        </a>
        <div class="header-buttons">
            <a href="login.php">Be a part of us</a>
        </div>
    </header>
    <main>
        <section class="hero">
            <div class="hero-text" id="heroText">
                Bridging Students and Teachers for a Brighter Future
            </div>
            <div class="hero-buttons">
                <a href="login.php" style="background: linear-gradient(90deg, #1877f2 70%, #3ab7ff 100%);">I'm a Parent</a>
                <a href="login.php" style="background: linear-gradient(90deg, #3ab7ff 70%, #1877f2 100%);">I'm a Teacher</a>
            </div>
        </section>
        <section class="content-section">
            <h2>Empowering Education, Connecting Communities</h2>
            <ul>
                <li>Teachers can easily report their meetings and student progress.</li>
                <li>Parents stay informed about their child's learning journey.</li>
                <li>Seamless communication between educators and families.</li>
                <li>Track, document, and celebrate every achievement together.</li>
            </ul>
            <div class="card-row">
                <div class="card">
                    <strong>Meeting Reports</strong>
                    <p>Teachers can log every meeting, share notes, and provide feedback on student performance.</p>
                </div>
                <div class="card">
                    <strong>Student Progress</strong>
                    <p>Monitor student growth, assignments, and participation with clear, organized reports.</p>
                </div>
                <div class="card">
                    <strong>Parent-Teacher Collaboration</strong>
                    <p>Encourage active involvement by keeping parents in the loop with regular updates.</p>
                </div>
            </div>
        </section>
    </main>
    <script>
        // Simple hero text animation (typing effect)
        document.addEventListener('DOMContentLoaded', function() {
            const heroText = document.getElementById('heroText');
            const fullText = heroText.textContent;
            heroText.textContent = '';
            let i = 0;

            function typeWriter() {
                if (i < fullText.length) {
                    heroText.textContent += fullText.charAt(i);
                    i++;
                    setTimeout(typeWriter, 35);
                }
            }
            setTimeout(typeWriter, 350);
        });
    </script>
</body>

</html>