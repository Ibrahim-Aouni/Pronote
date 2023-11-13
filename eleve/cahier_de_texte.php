<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

try {
    $bdd = new PDO('mysql:host=localhost;dbname=pronote;charset=utf8', 'root', '');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Erreur de connexion à la base de données : ' . $e->getMessage());
}
include('./navbar_eleve.php');


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style.css">
    <title>Document</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }

        .container {
            margin-top: 50px;
        }

        .table {
            width: 100%;
            margin-top: 20px;
        }

        th, td {
            padding: 15px;
            text-align: center;
        }

        th {
            background-color: #007bff;
            color: #fff;
        }

        .highlight {
            background-color: #79c0ff;
        }

        #monthYear {
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
            color: #007bff;
        }

        .prev-next-btn {
            cursor: pointer;
            margin: 0 10px;
        }
    </style>
</head>

<body>
    
<div class="container">
    <div id="monthYear"></div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Sun</th>
                <th>Mon</th>
                <th>Tue</th>
                <th>Wed</th>
                <th>Thu</th>
                <th>Fri</th>
                <th>Sat</th>
            </tr>
        </thead>
        <tbody id="calendarBody"></tbody>
    </table>
</div>
    <?php
    $requete = $bdd->prepare('SELECT dates, titres, messages FROM devoir ');
        $requete->execute();
        while ($row = $requete->fetch()) {
           
         
                echo '
                
                   
 <div class="col-lg-4">
 <div class="card card-margin">
     <div class="card-header no-border">
         <h5 class="card-title">'.$nom.'</h5>
     </div>
     <div class="card-body pt-0">
         <div class="widget-49">
             <div class="widget-49-title-wrapper">
                 <div class="widget-49-date-warning">
                     <span class="widget-49-date-day">13</span>
                     <span class="widget-49-date-month">apr</span>
                 </div>
                 <div class="widget-49-meeting-info">
                     <span class="widget-49-pro-title">'.htmlspecialchars($row['titres']).'</span>
                     <span class="widget-49-meeting-time">'.htmlspecialchars($row['dates']).'</span>
                 </div>
             </div>
             <ol class="widget-49-meeting-points">
                 <li class="widget-49-meeting-item"><span>'.htmlspecialchars($row['messages']).'</span></li>
                 
             </ol>
             <div class="widget-49-meeting-action">
                 <a href="#" class="btn btn-sm btn-flash-border-warning">View All</a>
             </div>
         </div>
     </div>
 </div>
</div>
                    
                </div>';
            }
        


?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const calendarBody = document.getElementById("calendarBody");
    const monthYearDisplay = document.getElementById("monthYear");
    let currentMonth = new Date().getMonth();
    let currentYear = new Date().getFullYear();
    const highlightedDays = [5, 10, 15, 20, 25];

    function generateCalendar(month, year) {
        calendarBody.innerHTML = "";
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        const firstDay = new Date(year, month, 1).getDay();

        let date = 1;

        for (let i = 0; i < 6; i++) {
            const row = document.createElement("tr");

            for (let j = 0; j < 7; j++) {
                const cell = document.createElement("td");
                if ((i === 0 && j < firstDay) || date > daysInMonth) {
                    cell.textContent = "";
                } else {
                    cell.textContent = date;

                    if (highlightedDays.includes(date)) {
                        cell.classList.add("highlight");
                    }

                    date++;
                }
                row.appendChild(cell);
            }

            calendarBody.appendChild(row);

            if (date > daysInMonth) {
                break;
            }
        }

        monthYearDisplay.textContent = `${getMonthName(month)} ${year}`;
    }

    function getMonthName(month) {
        const options = { month: "long" };
        return new Date(2020, month, 1).toLocaleDateString("en-US", options);
    }

    function updateCalendar() {
        generateCalendar(currentMonth, currentYear);
    }

    function showPrevMonth() {
        currentMonth--;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        }
        updateCalendar();
    }

    function showNextMonth() {
        currentMonth++;
        if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        }
        updateCalendar();
    }

    updateCalendar();
</script>

    
</body>
</html>