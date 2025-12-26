<?php
require_once 'config.php';

$student_info = null;
$schedule = [];
$absences = [];
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $academic_number = trim($_POST['academic_number'] ?? '');

    if ($academic_number !== '') {
        $stmt = $conn->prepare("SELECT * FROM student_form WHERE academic_number = ?");
        $stmt->bind_param("s", $academic_number);
        $stmt->execute();
        $result = $stmt->get_result();
        $student_info = $result->fetch_assoc();
        $stmt->close();

        if ($student_info) {
            $student_id = $student_info['id'];

            $stmt_schedule = $conn->prepare("
                SELECT c.classes_id, c.class_name, cs.day_of_week, cs.time_start, cs.time_end
                FROM class_enrollments ce
                JOIN classes c ON ce.class_id = c.classes_id
                LEFT JOIN class_schedule cs ON c.classes_id = cs.class_id
                WHERE ce.student_id = ?
            ");
            $stmt_schedule->bind_param("i", $student_id);
            $stmt_schedule->execute();
            $result_schedule = $stmt_schedule->get_result();
            $schedule = $result_schedule->fetch_all(MYSQLI_ASSOC);
            $stmt_schedule->close();

            // عدد الغيابات
            $stmt_absences = $conn->prepare("
                SELECT class_id, COUNT(*) AS absence_count
                FROM student_attendance
                WHERE student_id = ? AND status = 'غائب'
                GROUP BY class_id
            ");
            $stmt_absences->bind_param("i", $student_id);
            $stmt_absences->execute();
            $result_absences = $stmt_absences->get_result();
            while ($row = $result_absences->fetch_assoc()) {
                $absences[$row['class_id']] = $row['absence_count'];
            }
            $stmt_absences->close();
        } else {
            $message = "لم يتم العثور على طالب بهذا الرقم الأكاديمي.";
        }
    } else {
        $message = "الرجاء إدخال الرقم الأكاديمي.";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>سجل حضور وغياب الطلاب </title>
<link rel="stylesheet" href="style.css">

</head>
<body>
<div class="header">
    <h1>نظام حضور وغياب الطلاب - مركز</h1>
</div>
<div class="bar"></div>

<div class="container">
    <form method="POST">
        <input type="text" name="academic_number" placeholder="أدخل الرقم الأكاديمي" value="<?= htmlspecialchars($_POST['academic_number'] ?? '') ?>">
        <button type="submit">بحث</button>
    </form>

    <?php if($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if($student_info): ?>
    <div class="student-info">
        <div class="info-item"><strong>اسم الطالب:</strong> <?= htmlspecialchars($student_info['first_name'].' '.$student_info['father_name'].' '.$student_info['grandfather_name'].' '.$student_info['family_name']) ?></div>
        <div class="info-item"><strong>الرقم الأكاديمي:</strong> <?= htmlspecialchars($student_info['academic_number']) ?></div>
        <div class="info-item"><strong>العنوان:</strong> <?= htmlspecialchars($student_info['address']) ?></div>
        <div class="info-item"><strong>رقم الهاتف:</strong> <?= htmlspecialchars($student_info['mobile_number']) ?></div>
        
       
    </div>

    <div class="section-title">الشعب المسجل بها الطالب وعدد الغيابات</div>

    <?php if(count($schedule) > 0): ?>
    <div class="table-responsive">
    <table>
        <thead>
            <tr>
                <th>اسم الشعبة</th>
                <th>اليوم</th>
                <th>وقت البداية</th>
                <th>وقت النهاية</th>
                <th>عدد الغيابات</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $displayed_classes = []; 
            foreach($schedule as $row): 
                if (in_array($row['classes_id'], $displayed_classes)) continue; 
                $displayed_classes[] = $row['classes_id'];
            ?>
            <tr>
                <td><?= htmlspecialchars($row['class_name'] ?? '-') ?></td>
                <td>
                    <?php 
                    $days = [];
                    foreach ($schedule as $sched) {
                        if ($sched['classes_id'] == $row['classes_id'] && !empty($sched['day_of_week'])) {
                            $days[] = htmlspecialchars($sched['day_of_week']);
                        }
                    }
                    echo !empty($days) ? implode('<br>', array_unique($days)) : '-';
                    ?>
                </td>
                <td>
                    <?php 
                    $starts = [];
                    foreach ($schedule as $sched) {
                        if ($sched['classes_id'] == $row['classes_id'] && !empty($sched['time_start'])) {
                            $starts[] = htmlspecialchars(substr($sched['time_start'], 0, 5));
                        }
                    }
                    echo !empty($starts) ? implode('<br>', array_unique($starts)) : '-';
                    ?>
                </td>
                <td>
                    <?php 
                    $ends = [];
                    foreach ($schedule as $sched) {
                        if ($sched['classes_id'] == $row['classes_id'] && !empty($sched['time_end'])) {
                            $ends[] = htmlspecialchars(substr($sched['time_end'], 0, 5));
                        }
                    }
                    echo !empty($ends) ? implode('<br>', array_unique($ends)) : '-';
                    ?>
                </td>
                <td><?= $absences[$row['classes_id']] ?? 0 ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php else: ?>
        <p style="text-align:center;">لا توجد شعب مسجل بها هذا الطالب.</p>
    <?php endif; ?>
    <?php endif; ?>
</div>
<div class="footer">
    <div>العنوان: نابلس - الدوار - عمارة قنازع وزريق - الطابق السابع</div>
    
</div>

</body>
</html>
