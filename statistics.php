<?php
    session_start();
?>
<!DOCTYPE html>
    <head>
        <title>Statistics Page</title>
        <link rel="stylesheet" href="statistics.css?v=<?php echo time();?>"> 
        <script type = "text/javascript" type="module" src="statistics.js"></script>
        <script type="text/javascript" type="module" src="CombinedTime_Final.js"></script>
        <script type = "text/javascript" type="module" src="Window.js"></script>
        <!-- <script src="https://cdn.anychart.com/releases/8.0.1/js/anychart-core.min.js"></script> -->
        <script src="https://cdn.anychart.com/releases/8.10.0/js/anychart-base.min.js?hcode=a0c21fc77e1449cc86299c5faa067dc4"></script>
        <script src="https://cdn.anychart.com/releases/8.0.1/js/anychart-pie.min.js"></script>
        <script src="https://cdn.anychart.com/releases/8.10.0/js/anychart-core.min.js"></script>
        <script src="https://cdn.anychart.com/releases/8.10.0/js/anychart-cartesian.min.js"></script>

        <!-- <script src="https://cdn.anychart.com/releases/8.10.0/js/anychart-exports.min.js?hcode=a0c21fc77e1449cc86299c5faa067dc4"></script>
        <script src="https://cdn.anychart.com/releases/8.10.0/js/anychart-ui.min.js?hcode=a0c21fc77e1449cc86299c5faa067dc4"></script> -->

        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Signika+Negative:wght@600&display=swap" rel="stylesheet">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>

    <body>
        <!--Main division for the background-->
        <div id="background">
            <div class="btn-group">
                <button class="button" onclick="schedule()">Schedule</button>
                <button class="button">Statistics</button>
                <button class="button" onclick="routine()">Routine</button>
            </div>
            <div class="main"></div>
        </div>

    
        <div id="box"></div> 

            <!-- <div id="work"> 
                <h2>Total time spent on Work today:</h2>
            </div>

            <div id="exercise">
                <h2>Total time spent on Exercising today:</h2>
            </div>

            <div id="misc">
                <h2>Total time spent on Miscellaneous today:</h2>
            </div>

            <div id="prodpercent">
                <h2>Percentage of work tasks done during your selected productivity period today:</h2>
            </div> -->

            <div id="sleepDuration">
                <h2>Your estimated sleep duration for today: </h2>
            </div>

            <div id="total">
                <h2>Total time to be spent on tasks today: </h2>
            </div>

            <!-- Newly added for pie chart -->
            <div id="container"></div>
            <div id="barContainer"></div>

        <script>

        window.onload = function() {
            var workDuration = 0; 
            var exerciseDuration=0;
            var miscDuration=0;
            var mealDuration=0;

            let currArr = [];
            let name, cat, year, month, date, startTimeHour, startTimeMin, endTimeHour, endTimeMin, completed, newWin, prodStart, prodEnd, prodPeriod; 
            let totalWork = 0;
            let totalTime = 0;
            let percentage = 0;
            let totalSleep = 480;
            // STEP 1: Obtain all the fixed tasks for the day
            <?php
                //TODO: We need to obtain the year, month and date from the html page that directs us here. So update these variables here accordingly later.
                date_default_timezone_set('Singapore');
                $taskYear = date("Y");
                $taskMonth = date("m") - 1; // For javascript, months span from 0 - 11. This is already accounted for in the main schedule page.
                $taskDate = date("d"); //Just for testing!!
                //$type = 1; //Type for fixed tasks is always 1
                $userid = $_SESSION["userid"];


                $sql = "SELECT * FROM fixedtaskwindow WHERE userid = $userid AND taskYear = $taskYear AND taskMonth = $taskMonth AND taskDate = $taskDate;";

                $fullDate = $taskYear."-".($taskMonth + 1)."-".$taskDate;
                $timestamp = strtotime($fullDate);
                $dayNum = date('w', $timestamp);

                $dailySql = "SELECT * FROM routinetask WHERE userid = $userid AND freq = 0;";

                $weeklySql = "SELECT * FROM routinetask WHERE userid = $userid AND freq = 1 AND taskDay = $dayNum;";

                $biweeklySql = "SELECT * FROM routinetask WHERE userid = $userid AND freq = 2 AND taskDay = $dayNum AND week = 0;";

                $monthlySql = "SELECT * FROM routinetask WHERE userid = $userid AND freq = 3 AND taskDate = $taskDate;";

                $routineChecks = [$dailySql, $weeklySql, $biweeklySql, $monthlySql];

                $user = 'root'; 
                $pass = '';
                $db='orbital247';
                $conn = mysqli_connect('localhost', $user, $pass, $db);
                $result = mysqli_query($conn, $sql);
                
                if ($result) {
                    $resultCheck = mysqli_num_rows($result);
                    $data = array();
                    if ($resultCheck > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $data[] = $row;   
                        }   
                    }
                    foreach($data as $single) {
                        $name = $single['taskName'];
                        echo "name = '$name';";
                        $cat = $single['taskCategory'];
                        echo "cat = parseInt($cat);";
                        $year = $single['taskYear'];
                        echo "year = parseInt($year);";
                        $month = $single['taskMonth'];
                        echo "month = parseInt($month);";
                        $date = $single['taskDate'];
                        echo "date = parseInt($date);";
                        $startTimeHour = $single['startTimeHour'];
                        echo "startTimeHour = parseInt($startTimeHour);";
                        $startTimeMin = $single['startTimeMin'];
                        echo "startTimeMin = parseInt($startTimeMin);";
                        $endTimeHour = $single['endTimeHour'];
                        echo "endTimeHour = parseInt($endTimeHour);";
                        $endTimeMin = $single['endTimeMin'];
                        echo "endTimeMin = parseInt($endTimeMin);";
                        $completed = $single['completed'];
                        echo "completed = parseInt($completed);";
                        
                        
                        echo 'newWin = new Window(name, cat, year, month, date, new Time(startTimeHour, startTimeMin), new Time(endTimeHour, endTimeMin), completed);';

                        echo 'currArr.push(newWin);';
                    }
                }
                echo 'console.log(currArr);';

                foreach($routineChecks as $check) {
                    $result = mysqli_query($conn, $check);
                
                    if ($result) {
                        $resultCheck = mysqli_num_rows($result);
                        $data = array();
                        if ($resultCheck > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $data[] = $row;   
                            }   
                        }
                        foreach($data as $single) {
                            $name = $single['taskName'];
                            echo "name = '$name';";
                            $cat = $single['taskCategory'];
                            echo "cat = parseInt($cat);";
                            echo "year = $taskYear;";
                            echo "month = $taskMonth;";
                            echo "date = $taskDate;";
                            $startTimeHour = $single['startTimeHour'];
                            echo "startTimeHour = parseInt($startTimeHour);";
                            $startTimeMin = $single['startTimeMin'];
                            echo "startTimeMin = parseInt($startTimeMin);";
                            $endTimeHour = $single['endTimeHour'];
                            echo "endTimeHour = parseInt($endTimeHour);";
                            $endTimeMin = $single['endTimeMin'];
                            echo "endTimeMin = parseInt($endTimeMin);";
                            
                            echo 'newWin = new Window(name, cat, year, month, date, new Time(startTimeHour, startTimeMin), new Time(endTimeHour, endTimeMin), false);';

                            echo 'currArr.push(newWin);';
                        }
                    }
                }
                        
                $prodSql = "SELECT * FROM infoproductive WHERE id = $userid";
                $wakeUpSql = "SELECT * FROM infowakeup WHERE id = $userid";
                $currResult = mysqli_query($conn, $prodSql);
                $currResult2 = mysqli_query($conn, $wakeUpSql);
                if ($currResult) {
                    $resultCheck = mysqli_num_rows($currResult);
                    $currData = array();
                    if ($resultCheck > 0) {
                        while ($row = mysqli_fetch_assoc($currResult)) {
                            $currData[] = $row;   
                        }   
                    }
                    foreach($currData as $singleData) {
                        $startHour = $singleData['startTimeHour'];
                        $startMin = $singleData['startTimeMin'];
                        $endHour = $singleData['endTimeHour'];
                        $endMin = $singleData['endTimeMin'];
                        echo "prodStart = new Time($startHour, $startMin);";
                        echo "prodEnd = new Time($endHour, $endMin);";
                    }
                }
                
                if ($currResult2) {
                    $resultCheck2 = mysqli_num_rows($currResult2);
                    $currData2 = array();
                    if ($resultCheck2 > 0) {
                        while ($row2 = mysqli_fetch_assoc($currResult2)) {
                            $currData2[] = $row2;   
                        }   
                    }
                    foreach($currData2 as $singleData2) {
                        $wakeHour = $singleData2['hour'];
                        $wakeMin = $singleData2['minute'];
                        echo "let sleepEnd = new Time($wakeHour, $wakeMin);";
                        echo "let sleepStart = Time.findStartTime(sleepEnd, [8, 0]);";
                    }
                }
                ?>

                console.log("sleepStart " + sleepStart);
                console.log(currArr);

                prodPeriod = new Window("productive", 2, new Date().getFullYear(), new Date().getMonth(), new Date().getDate(), prodStart, prodEnd, false);

                let sleepPeriod = new Window("sleep", 2, new Date().getFullYear(), new Date().getMonth(), new Date().getDate(), sleepStart, sleepEnd, false);

                for (let i = 0; i < currArr.length; i++) {
                    currType = currArr[i].getTaskCat();
                    console.log(currType);
                    console.log("I enter cat block");
                    console.log(currArr[i].getStartTime());
                    console.log(currArr[i].getEndTime());
                    let duration = Time.duration(currArr[i].getStartTime(), currArr[i].getEndTime());
                    //console.log("duration"+duration);
                    if (currType == 0) {
                        console.log("I am 0");
                        workDuration += (duration[0]*60) + (duration[1]);
                    } else if (currType == 1) {
                        console.log("I am 1");
                        exerciseDuration += (duration[0]*60) + (duration[1]);
                    } else if (currType == 2) {
                        console.log("I am 2");
                        miscDuration += (duration[0]*60) + (duration[1]);
                    } 

                    if (currArr[i].isCompletelyDuring(prodPeriod) || currArr[i].partiallyOverlaps(prodPeriod) || prodPeriod.partiallyOverlaps(prodPeriod)) {
                        let currDuration = Time.duration(currArr[i].getStartTime(), currArr[i].getEndTime());

                        totalTime += (currDuration[0] * 60) + currDuration[1];

                        if (currArr.type == 0) {
                            totalWork += (currDuration[0] * 60) + currDuration[1];
                        }
                    }

                    //if (currArr[i].isCompletelyDuring(sleepPeriod) || currArr[i].partiallyOverlaps(sleepPeriod) || sleepPeriod.partiallyOverlaps(currArr[i])) {
                    if (currArr[i].duringSleep(sleepStart, sleepEnd)) {
                        let currDuration = Time.duration(currArr[i].getStartTime(), currArr[i].getEndTime());
                        console.log("I come to sleep block");
                        totalSleep -= (currDuration[0] * 60) + currDuration[1]; //estimated sleep duration
                    }
                }  
                console.log("work duration:"+workDuration);
                console.log("exer duration:"+exerciseDuration);
                console.log("misc duration:"+miscDuration);

                let pieTotal = workDuration + exerciseDuration + miscDuration;
                total = pieTotal;
                total = [(total - (total % 60)) / 60, total % 60];

                workPercent = ((workDuration/pieTotal) * 100).toFixed(1);
                workHoursMins = [((workDuration - (workDuration % 60)) / 60), (workDuration % 60)];
                workDuration = (workDuration / 60).toFixed(2);
                // let workmain = document.getElementById("work");
                // let workele = document.createElement("input");
                // workele.type = "text";
                // workele.setAttribute("readonly", "readonly");
                // var line = "Hours: " + workDuration[0] + " " + "Minutes: " + workDuration[1];
                // workele.value = line;
                // workele.classList.add("work");
                // workele.style.position="absolute";
                // workele.style.zIndex="20";
                // workele.style.fontFamily = "Signika Negative";
                // workele.style.fontSize = "18px";
                // workele.style.textAlign = "center";
                // workele.style.backgroundColor = "#FDFD96";
                // workele.style.color = "#1e5353";
                // workele.style.border = "black";
                // workele.style.borderRadius = "5px";
                // workele.style.marginLeft= "400px";
                // workele.style.marginTop= "-45px";
                // workmain.appendChild(workele);

                exercisePercent = ((exerciseDuration/pieTotal) * 100).toFixed(1);
                exercieHoursMins = [((exerciseDuration - (exerciseDuration % 60)) / 60), (exerciseDuration % 60)];
                exerciseDuration = (exerciseDuration / 60).toFixed(2);
                //exerciseDuration = [((exerciseDuration - (exerciseDuration % 60)) / 60), (exerciseDuration % 60)]; 
                // let exercisemain = document.getElementById("exercise");
                // let exerciseele = document.createElement("input");
                // exerciseele.type = "text";
                // exerciseele.setAttribute("readonly", "readonly");
                // var line = "Hour: " + exerciseDuration[0] + " " + "Minute: " + exerciseDuration[1];
                // exerciseele.value = line;
                // exerciseele.classList.add("exercise");
                // exerciseele.style.position="absolute";
                // exerciseele.style.zIndex="20";
                // exerciseele.style.fontFamily = "Signika Negative";
                // exerciseele.style.fontSize = "18px";
                // exerciseele.style.textAlign = "center";
                // exerciseele.style.backgroundColor = "#FDFD96";
                // exerciseele.style.color = "#1e5353";
                // exerciseele.style.border = "black";
                // exerciseele.style.borderRadius = "5px";
                // exerciseele.style.marginLeft= "400px";
                // exerciseele.style.marginTop= "-45px";
                // exercisemain.appendChild(exerciseele);

                miscPercent = ((miscDuration/pieTotal) * 100).toFixed(1);
                miscHoursMins = [((miscDuration - (miscDuration % 60)) / 60), (miscDuration % 60)];
                miscDuration = (miscDuration / 60).toFixed(2);
                //miscDuration = [((miscDuration - (miscDuration % 60)) / 60), (miscDuration % 60)]; 
                // let miscmain = document.getElementById("misc");
                // let miscele = document.createElement("input");
                // miscele.type = "text";
                // miscele.setAttribute("readonly", "readonly");
                // var line = "Hour: " + miscDuration [0] + " " + "Minute: " + miscDuration[1];
                // miscele.value = line;
                // miscele.classList.add("misc");
                // miscele.style.position="absolute";
                // miscele.style.zIndex="20";
                // miscele.style.fontFamily = "Signika Negative";
                // miscele.style.fontSize = "18px";
                // miscele.style.textAlign = "center";
                // miscele.style.backgroundColor = "#FDFD96";
                // miscele.style.color = "#1e5353";
                // miscele.style.borderRadius = "5px";
                // miscele.style.border = "black";
                // miscele.style.marginTop= "-45px";
                // miscele.style.marginLeft= "450px";
                // miscmain.appendChild(miscele);

                anychart.onDocumentReady(function() {

                    // set the data
                    var data = [
                        {x: "Work", value: workDuration, hours: workHoursMins[0], mins: workHoursMins[1], percent: workPercent},
                        {x: "Exercise", value: exerciseDuration, hours: exercieHoursMins[0], mins: exercieHoursMins[1], percent: exercisePercent},
                        {x: "Miscelleneous", value: miscDuration, hours: miscHoursMins[0], mins: miscHoursMins[1], percent: miscPercent},
                    ];

                    // create the chart
                    var chart = anychart.pie();

                    // set the chart title
                    chart.title("Today's Breakdown");
                    chart.title().fontFamily("Signika Negative");
                    chart.title().fontSize(23);
                    chart.title().fontWeight(700);
                    chart.title().fontColor("black");
                    chart.title().fontDecoration("underline");
                    chart.title().padding("0px");

                    // add the data
                    chart.data(data);

                    // set legend position
                    //chart.legend().position("right");
                    // set items layout
                    //chart.legend().itemsLayout("vertical");

                    // to remove the white box that was initally the chart's background
                    chart.background().enabled(false);
                    //chart.outline(true);
                    // chart.normal().outline().enabled(true);
                    // chart.normal().outline().width("5%");
                    // chart.hovered().outline().width("10%");
                    // chart.selected().outline().width("3");
                    // chart.selected().outline().fill("#455a64");
                    // chart.selected().outline().stroke(null);
                    // chart.selected().outline().offset(2);

                    chart.labels().format("{%x}");
                    chart.labels().fontFamily("Signika Negative");
                    chart.labels().fontSize(16);
                    chart.labels().fontWeight(900);
                    chart.labels().fontColor("black");
                    chart.labels().position("center");

                    chart.legend().enabled(false);
                    chart.legend().fontFamily("Signika Negative");
                    chart.legend().fontSize(20);
                    chart.legend().fontColor("black");

                    chart.radius("40%");

                    chart.palette(["#e3aba1", "white", "#fdfd96"]); // Should be in the order of the data input
                    //anychart.color.darken("#e3aba1", 0.2);

                    chart.tooltip().useHtml(true);

                    //chart.tooltip().displayMode("double");

                    //chart.tooltip().title();
                    //title.fontDecoration("underline");
                    chart.tooltip().title().fontFamily("Signika Negative");
                    chart.tooltip().title().fontSize(17);
                    chart.tooltip().title().fontWeight(400);

                    chart.tooltip().fontFamily("Signika Negative");
                    chart.tooltip().fontSize(15);
                    chart.tooltip().fontWeight(50);

                    //chart.tooltip().format("Hours spent: {%value}\nSales volume: <b>${%value}</b>");
                    chart.tooltip().format("Total time spent: {%hours} hour(s) and {%mins} minute(s)<br><i>{%percent}% of the day was spent on '{%x}'</i>");

                    // display the chart in the container
                    chart.container('container');
                    chart.draw();


                    //BAR CHART
                    // create data
                    let bar1val = 0;
                    let bar2val = 0;
                    let bar3val = 0;
                    let bar4val = 0;

                    var bar1 = sleepEnd + "-" + Time.findEndTime(sleepEnd, [4, 0]);
                    var bar2 = Time.findEndTime(sleepEnd, [4, 0]) + "-" + Time.findEndTime(sleepEnd, [8, 0]);
                    var bar3 = Time.findEndTime(sleepEnd, [8, 0]) + "-" + Time.findEndTime(sleepEnd, [12, 0]);
                    var bar4 = Time.findEndTime(sleepEnd, [12, 0]) + "-" + Time.findEndTime(sleepEnd, [16, 0]);

                    
                    var bar1win = new Window("slot", 2, new Date().getFullYear(), new Date().getMonth(), new Date().getDate(), sleepEnd, Time.findEndTime(sleepEnd, [4, 0]), false);
                    var bar2win = new Window("slot", 2, new Date().getFullYear(), new Date().getMonth(), new Date().getDate(), Time.findEndTime(sleepEnd, [4, 0]), Time.findEndTime(sleepEnd, [8, 0]), false);
                    console.log(bar2win);
                    var bar3win = new Window("slot", 2, new Date().getFullYear(), new Date().getMonth(), new Date().getDate(), Time.findEndTime(sleepEnd, [8, 0]), Time.findEndTime(sleepEnd, [12, 0]), false);
                    var bar4win = new Window("slot", 2, new Date().getFullYear(), new Date().getMonth(), new Date().getDate(), Time.findEndTime(sleepEnd, [12, 0]), Time.findEndTime(sleepEnd, [16, 0]), false);

                    function barUpdate(arr) {
                        if ((arr).isCompletelyDuring(bar1win)) {
                            let dur = Time.duration(arr.getStartTime(), arr.getEndTime());
                            bar1val += (dur[0] * 60) + dur[1];
                        } else if ((arr).isCompletelyDuring(bar2win)) {
                            let dur = Time.duration(arr.getStartTime(), arr.getEndTime());
                            bar2val += (dur[0] * 60) + dur[1];
                        } else if (arr.isCompletelyDuring(bar3win)) {
                            let dur = Time.duration(arr.getStartTime(), arr.getEndTime());
                            bar3val += (dur[0] * 60) + dur[1];
                        } else if (arr.isCompletelyDuring(bar4win)) {
                            console.log("I come to the expected bar");
                            let dur = Time.duration(arr.getStartTime(), arr.getEndTime());
                            bar4val += (dur[0] * 60) + dur[1];
                        } else if ((arr).partiallyOverlaps(bar1win)) {
                            if (bar1win.startsAfter(arr)) {
                                let dur = Time.duration(bar1win.getStartTime(), arr.getEndTime());
                                bar1val += (dur[0] * 60) + dur[1];

                                barUpdate(new Window("slot", 2, new Date().getFullYear(), new Date().getMonth(), new Date().getDate(), arr.getStartTime(), bar1win.getStartTime(), false));
                            } else {
                                let dur = Time.duration(arr.getStartTime(), bar1win.getEndTime());
                                bar1val += (dur[0] * 60) + dur[1];
                                
                                barUpdate(new Window("slot", 2, new Date().getFullYear(), new Date().getMonth(), new Date().getDate(), bar1win.getEndTime(), arr.getEndTime(), false));

                            }
                        } else if ((arr).partiallyOverlaps(bar2win)) {
                            if (bar2win.startsAfter(arr)) {
                                let dur = Time.duration(bar2win.getStartTime(), arr.getEndTime());
                                bar2val += (dur[0] * 60) + dur[1];
                                
                                barUpdate(new Window("slot", 2, new Date().getFullYear(), new Date().getMonth(), new Date().getDate(), arr.getStartTime(), bar2win.getStartTime(), false));
                            } else {
                                let dur = Time.duration(arr.getStartTime(), bar2win.getEndTime());
                                bar2val += (dur[0] * 60) + dur[1];
                                
                                barUpdate(new Window("slot", 2, new Date().getFullYear(), new Date().getMonth(), new Date().getDate(), bar2win.getEndTime(), arr.getEndTime(), false));
                            }
                        } else if ((arr).partiallyOverlaps(bar3win)) {
                            if (bar3win.startsAfter(arr)) {
                                let dur = Time.duration(bar3win.getStartTime(), arr.getEndTime());
                                bar3val += (dur[0] * 60) + dur[1];
                                
                                barUpdate(new Window("slot", 2, new Date().getFullYear(), new Date().getMonth(), new Date().getDate(), arr.getStartTime(), bar3win.getStartTime(), false));
                            } else {
                                let dur = Time.duration(arr.getStartTime(), bar3win.getEndTime());
                                bar3val += (dur[0] * 60) + dur[1];
                                
                                barUpdate(new Window("slot", 2, new Date().getFullYear(), new Date().getMonth(), new Date().getDate(), bar3win.getEndTime(), arr.getEndTime(), false));
                            }
                        } else if ((arr).partiallyOverlaps(bar4win)) {
                            if (bar4win.startsAfter(arr)) {
                                let dur = Time.duration(bar4win.getStartTime(), arr.getEndTime());
                                bar4val += (dur[0] * 60) + dur[1];
                                
                                barUpdate(new Window("slot", 2, new Date().getFullYear(), new Date().getMonth(), new Date().getDate(), arr.getStartTime(), bar4win.getStartTime(), false));
                            } else {
                                let dur = Time.duration(arr.getStartTime(), bar4win.getEndTime());
                                bar4val += (dur[0] * 60) + dur[1];
                                
                                barUpdate(new Window("slot", 2, new Date().getFullYear(), new Date().getMonth(), new Date().getDate(), bar4win.getEndTime(), arr.getEndTime(), false));
                            }
                        }
                    }

                    for (let j = 0; j < currArr.length; j++) {
                        barUpdate(currArr[j]);
                    }

                    // var data = [
                    // [bar1, 10000],
                    // [bar2, 10000],
                    // [bar3, 10000],
                    // [bar4, 10000],
                    // [bar5, 10000],
                    // [bar6, 10000]
                    // ];
                    
                    var data = [
                    {x: bar1, value:(bar1val / 60).toFixed(2), hours:(bar1val - (bar1val % 60)) / 60, mins:bar1val % 60},
                    {x: bar2, value:(bar2val / 60).toFixed(2), hours:(bar2val - (bar2val % 60)) / 60, mins:bar2val % 60},
                    {x: bar3, value:(bar3val / 60).toFixed(2), hours:(bar3val - (bar3val % 60)) / 60, mins:bar3val % 60},
                    {x: bar4, value:(bar4val / 60).toFixed(2), hours:(bar4val - (bar4val % 60)) / 60, mins:bar4val % 60}
                    ];

                    // create a chart
                    var barChart = anychart.line();

                    // create a bar series and set the data
                    var series = barChart.column(data);

                    // set the container id
                    barChart.container("container");

                    // initiate drawing the chart
                    barChart.container('barContainer');
                    barChart.draw();

                    barChart.title("Productivity Distribution");
                    barChart.title().fontFamily("Signika Negative");
                    barChart.title().fontSize(23);
                    barChart.title().fontWeight(700);
                    barChart.title().fontColor("black");
                    barChart.title().fontDecoration("underline");
                    barChart.title().padding("0px");

                    barChart.background().enabled(false);

                    barChart.labels().format("{%x}");
                    barChart.labels().fontFamily("Signika Negative");
                    barChart.labels().fontSize(10);
                    barChart.labels().fontWeight(900);
                    barChart.labels().fontColor("black");
                    barChart.labels().position("center");

                    barChart.legend().enabled(false);
                    barChart.legend().fontFamily("Signika Negative");
                    barChart.legend().fontSize(10);
                    barChart.legend().fontColor("black");

                    barChart.palette(["#f2a294"]); 

                    // var yScale1 = anychart.scales.linear();
                    // yScale1.maximum(4);

                    barChart.tooltip().format("Time spent on tasks: {%hours} hour(s) and {%mins} minute(s)");

                });

                if (totalTime != 0) {
                    percentage = ((totalWork / totalTime) * 100); //percentage of work tasks during selected productive period
                }   
                console.log("percentage " + percentage);

                let percentmain = document.getElementById("prodpercent");
                // let prodPercent = document.createElement("input");
                // prodPercent.type = "text";
                // prodPercent.setAttribute("readonly", "readonly");
                // var line = percentage + "%";
                // prodPercent.value = line;
                // prodPercent.classList.add("prodpercent");
                // prodPercent.style.position="absolute";
                // prodPercent.style.zIndex="20";
                // prodPercent.style.fontFamily = "Signika Negative";
                // prodPercent.style.fontSize = "18px";
                // prodPercent.style.textAlign = "center";
                // prodPercent.style.backgroundColor = "#FDFD96";
                // prodPercent.style.color = "#1e5353";
                // prodPercent.style.borderRadius = "5px";
                // prodPercent.style.border = "black";
                // prodPercent.style.marginTop= "-45px";
                // prodPercent.style.marginLeft= "100px";
                // percentmain.appendChild(prodPercent);

                //To be added in the for loop for the workduration, exercise duration and misc duration thing
                console.log("totalSleep " + totalSleep);

                totalSleep = [((totalSleep - (totalSleep % 60)) / 60), (totalSleep % 60)]; 
                let sleepmain = document.getElementById("sleepDuration");
                let sleep = document.createElement("input");
                sleep.type = "text";
                sleep.setAttribute("readonly", "readonly");
                var line = "Hours: " + totalSleep[0] + "       " + "Minutes: " + totalSleep[1];
                sleep.value = line;
                sleep.classList.add("sleepDuration");
                sleep.style.position="absolute";
                sleep.style.zIndex="20";
                sleep.style.fontFamily = "Signika Negative";
                sleep.style.fontSize = "22px";
                sleep.style.textAlign = "center";
                //sleep.style.backgroundColor = "#FDFD96";
                sleep.style.backgroundColor = "white";
                sleep.style.color = "#1e5353";
                sleep.style.borderRadius = "5px";
                sleep.style.border = "black";
                sleep.style.marginTop= "10px";
                sleep.style.marginLeft= "90px";
                sleep.style.height= "40px";
                sleep.style.width= "230px";
                sleepmain.appendChild(sleep);

                let totalMain = document.getElementById("total");
                let totalDuration = document.createElement("input");
                totalDuration.type = "text";
                totalDuration.setAttribute("readonly", "readonly");
                var line = "Hours: " + total[0] + "       " + "Minutes: " + total[1];
                totalDuration.value = line;
                totalDuration.classList.add("sleepDuration");
                totalDuration.style.position="absolute";
                totalDuration.style.zIndex="20";
                totalDuration.style.fontFamily = "Signika Negative";
                totalDuration.style.fontSize = "22px";
                totalDuration.style.textAlign = "center";
                //sleep.style.backgroundColor = "#FDFD96";
                totalDuration.style.backgroundColor = "white";
                totalDuration.style.color = "#1e5353";
                totalDuration.style.borderRadius = "5px";
                totalDuration.style.border = "black";
                totalDuration.style.marginTop= "10px";
                totalDuration.style.marginLeft= "70px";
                totalDuration.style.height= "40px";
                totalDuration.style.width= "230px";
                totalMain.appendChild(totalDuration);
        }  
        </script>
    </body>
</html>
