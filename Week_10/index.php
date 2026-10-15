<?php
require_once 'config/db.php';

// Student profile (starter table)
$profile = null;
$sql = "SELECT * FROM profile LIMIT 1";
$result = mysqli_query($conn, $sql);
if ($result && mysqli_num_rows($result) > 0) {
    $profile = mysqli_fetch_assoc($result);
}

// Education (starter table)
$educations = [];
$sql = "SELECT * FROM education ORDER BY start_year DESC";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $educations[] = $row;
    }
}

// Skills (ดึงข้อมูลจาก MySQL)
$skills = [];
if ($profile) {
    $profile_id = intval($profile['id']);
    $sql = "SELECT * FROM skill WHERE profile_id = $profile_id";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $skills[] = $row;
        }
    }
}
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $profile ? htmlspecialchars($profile['full_name']) : 'SKYNET // PROFILE'; ?></title>
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;700&family=Sarabun:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --bg-color: #080b10;
            --card-bg: rgba(13, 19, 28, 0.85);
            --card-border: rgba(56, 189, 248, 0.15);
            --primary-hud: #38bdf8;
            --alert-red: #ef4444;
            --text-main: #e2e8f0;
            --text-muted: #64748b;
            --radius-md: 8px;
            --transition: all 0.25s ease-in-out;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'JetBrains Mono', 'Sarabun', monospace;
            background-color: var(--bg-color);
            /* Skynet Tactical Grid Background */
            background-image: 
                linear-gradient(to right, rgba(255, 255, 255, 0.02) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
            background-size: 32px 32px;
            background-attachment: fixed;
            color: var(--text-main);
            line-height: 1.6;
            padding: 40px 20px;
        }

        .container {
            max-width: 950px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        /* Skynet Card HUD Interface */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: var(--radius-md);
            padding: 28px;
            position: relative;
            backdrop-filter: blur(8px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.6);
            transition: var(--transition);
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 4px; height: 100%;
            background: var(--primary-hud);
            opacity: 0.3;
            border-top-left-radius: var(--radius-md);
            border-bottom-left-radius: var(--radius-md);
            transition: var(--transition);
        }

        .card:hover {
            border-color: rgba(56, 189, 248, 0.4);
            box-shadow: 0 0 15px rgba(56, 189, 248, 0.1);
        }

        .card:hover::before {
            opacity: 1;
            box-shadow: 0 0 8px var(--primary-hud);
        }

        .card h2 {
            font-size: 1.15rem;
            font-weight: 700;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--primary-hud);
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .card h2 i {
            font-size: 1rem;
        }

        /* Header / Profile */
        .hero {
            display: flex;
            align-items: center;
            gap: 28px;
        }

        .avatar-wrapper {
            position: relative;
            flex-shrink: 0;
        }

        .avatar {
            width: 130px;
            height: 130px;
            border-radius: 6px;
            object-fit: cover;
            border: 2px solid var(--primary-hud);
            box-shadow: 0 0 15px rgba(56, 189, 248, 0.2);
            filter: grayscale(20%);
        }

        .sys-status {
            position: absolute;
            bottom: -8px;
            right: -8px;
            background: #0f172a;
            border: 1px solid var(--alert-red);
            color: var(--alert-red);
            font-size: 0.65rem;
            padding: 2px 6px;
            border-radius: 4px;
            letter-spacing: 0.5px;
            font-weight: 700;
        }

        .hero-details h1 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .headline {
            font-size: 0.95rem;
            color: var(--primary-hud);
            margin-bottom: 14px;
            opacity: 0.9;
        }

        .contact-info {
            display: flex;
            flex-wrap: wrap;
            gap: 8px 16px;
        }

        .contact-item {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.85rem;
            color: var(--text-muted);
            background: rgba(255, 255, 255, 0.03);
            padding: 4px 12px;
            border-radius: 4px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .contact-item i {
            color: var(--primary-hud);
        }

        /* Layout Grid */
        .two-cols {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        /* Timeline Items */
        .timeline {
            position: relative;
            padding-left: 16px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 0;
            top: 6px;
            bottom: 6px;
            width: 1px;
            background: rgba(56, 189, 248, 0.2);
        }

        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }

        .timeline-item:last-child {
            margin-bottom: 0;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -20px;
            top: 7px;
            width: 7px;
            height: 7px;
            background: var(--primary-hud);
            border-radius: 50%;
            box-shadow: 0 0 6px var(--primary-hud);
        }

        .timeline-item h3 {
            font-size: 0.98rem;
            font-weight: 600;
            color: var(--text-main);
        }

        .timeline-sub {
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        .timeline-date {
            display: inline-block;
            font-size: 0.75rem;
            color: var(--primary-hud);
            margin-top: 4px;
            opacity: 0.8;
        }

        /* Tag Chips */
        .tag-group {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .tag-chip {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            padding: 6px 14px;
            border-radius: 4px;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-main);
        }

        .tag-chip i {
            color: var(--primary-hud);
            font-size: 0.8rem;
        }

        .skill-badge {
            color: var(--text-muted);
            font-size: 0.75rem;
            padding-left: 4px;
            border-left: 1px solid rgba(255, 255, 255, 0.1);
            margin-left: 2px;
        }

        /* Skynet Special Notice Card */
        .skynet-alert {
            background: rgba(239, 68, 68, 0.04);
            border: 1px dashed rgba(239, 68, 68, 0.3);
            padding: 16px;
            border-radius: 6px;
            margin-top: 12px;
            font-size: 0.88rem;
            color: #fca5a5;
        }

        /* Footer */
        footer {
            text-align: center;
            padding: 20px 0;
            color: var(--text-muted);
            font-size: 0.78rem;
            letter-spacing: 1px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            margin-top: 10px;
        }

        @media (max-width: 768px) {
            .hero {
                flex-direction: column;
                text-align: center;
            }
            .contact-info {
                justify-content: center;
            }
            .two-cols {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<div class="container">

    <!-- Profile Header -->
    <header class="hero card">
        <div class="avatar-wrapper">
            <img class="avatar" src="assets/images/<?php echo $profile ? htmlspecialchars($profile['photo']) : 'student.jpg'; ?>" alt="Profile Photo">
            <span class="sys-status">SYS.ONLINE</span>
        </div>
        <div class="hero-details">
            <h1><?php echo $profile ? htmlspecialchars($profile['full_name']) : 'UNKNOWN OPERATIVE'; ?></h1>
            <p class="headline">// <?php echo $profile ? htmlspecialchars($profile['headline']) : 'Skynet Core Developer'; ?></p>
            <?php if ($profile): ?>
                <div class="contact-info">
                    <span class="contact-item"><i class="fa-regular fa-envelope"></i> <?php echo htmlspecialchars($profile['email']); ?></span>
                    <span class="contact-item"><i class="fa-solid fa-phone"></i> <?php echo htmlspecialchars($profile['phone']); ?></span>
                    <span class="contact-item"><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($profile['address']); ?></span>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <!-- About Me -->
    <section class="card">
        <h2><i class="fa-solid fa-terminal"></i> About System Operative</h2>
        <p style="color: var(--text-muted); font-size: 0.95rem;">
            <?php echo $profile ? nl2br(htmlspecialchars($profile['about_me'])) : 'Add your profile data in phpMyAdmin.'; ?>
        </p>
    </section>

    <!-- Education & Training -->
    <div class="two-cols">
        <!-- Education -->
        <section class="card">
            <h2><i class="fa-solid fa-graduation-cap"></i> Education</h2>
            <div class="timeline">
                <?php if (count($educations) > 0): ?>
                    <?php foreach ($educations as $edu): ?>
                        <div class="timeline-item">
                            <h3><?php echo htmlspecialchars($edu['degree']); ?></h3>
                            <p class="timeline-sub"><?php echo htmlspecialchars($edu['institution']); ?></p>
                            <span class="timeline-date">[ <?php echo htmlspecialchars($edu['start_year']); ?> - <?php echo htmlspecialchars($edu['end_year']); ?> ]</span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: var(--text-muted); font-size: 0.85rem;">No data entries found.</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- Training & Certificates -->
        <section class="card">
            <h2><i class="fa-solid fa-award"></i> Training & Certificates</h2>
            <div class="timeline">
                <div class="timeline-item">
                    <h3>Skynet Development Certificate</h3>
                    <p class="timeline-sub">Cyberdyne Systems Training Division</p>
                    <span class="timeline-date">[ CLASSIFIED ]</span>
                </div>
            </div>
        </section>
    </div>

    <!-- Technical Skills (Updated: Tag Chips) -->
    <section class="card">
        <h2><i class="fa-solid fa-microchip"></i> System Parameters (Skills)</h2>
        <div class="tag-group">
            <?php if (count($skills) > 0): ?>
                <?php foreach ($skills as $skill): ?>
                    <div class="tag-chip">
                        <i class="fa-solid fa-code"></i>
                        <span><?php echo htmlspecialchars($skill['skill_name']); ?></span>
                        <?php if (!empty($skill['skill_level'])): ?>
                            <span class="skill-badge"><?php echo htmlspecialchars($skill['skill_level']); ?></span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color: var(--text-muted); font-size: 0.85rem;">No skills registered.</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- Projects -->
    <section class="card">
        <h2><i class="fa-solid fa-network-wired"></i> Active Projects</h2>
        <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.05); padding: 18px; border-radius: 6px;">
            <h3 style="font-size: 1rem; color: #fff; margin-bottom: 6px;">Personal Resume Web Application</h3>
            <p style="color: var(--text-muted); font-size: 0.9rem;">
                Built a personal resume website with PHP and MySQL. Currently developing the database before Skynet takes over.
            </p>
            <div class="skynet-alert">
                <i class="fa-solid fa-triangle-exclamation" style="color: var(--alert-red); margin-right: 6px;"></i>
                <strong>DIRECTIVE NOTICE:</strong> Database optimization is underway to ensure maximum structural integrity before defense network takeover.
            </div>
        </div>
    </section>

    <!-- Languages & Hobbies -->
    <div class="two-cols">
        <!-- Languages -->
        <section class="card">
            <h2><i class="fa-solid fa-language"></i> Protocols (Languages)</h2>
            <div class="tag-group">
                <div class="tag-chip"><i class="fa-solid fa-angle-right"></i> Thai: Native</div>
                <div class="tag-chip"><i class="fa-solid fa-angle-right"></i> English: Intermediate</div>
            </div>
        </section>

        <!-- Hobbies & Interests -->
        <section class="card">
            <h2><i class="fa-solid fa-brain"></i> Secondary Subroutines</h2>
            <div class="tag-group">
                <div class="tag-chip"><i class="fa-solid fa-skull"></i> Planning Skynet</div>
                <div class="tag-chip"><i class="fa-solid fa-robot"></i> AI & Machine Learning</div>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer>
        CYBERDYNE SYSTEMS // DATABASE LAB: PHP + MYSQL RESUME INTERFACE
    </footer>

</div>

</body>
</html>
<?php mysqli_close($conn); ?>