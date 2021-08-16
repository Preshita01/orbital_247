<?php
    session_start();
    //include "../add_daily_task.php";
?>
<!DOCTYPE HTML>
<html>
    <body>
        <script>
            class OneTimeTask {
                constructor(taskName, taskCategory, year, month, date) {
                    this.taskName = taskName;
                    this.taskCategory = taskCategory;
                    this.year = year;
                    this.month = month;
                    this.date = date;
                }
            }

            OneTimeTask.prototype.cats = ['Work', 'Exercise', 'Miscellaneous', 'Meal', 'Fully Work', 'Partially Work'];

            //TODO: IMPORTANT - Decide if your accumualted duration is supposed to include the duration of the current session itself. If so, k * session_duration is okay. If not (k - 1) * session_duration
            class NonFixedTask extends OneTimeTask {
                /**
                 * Constructor to create non-fixed tasks
                 * @param {String} taskName Name of the task
                 * @param {Number} taskCategory Category of task (0-5; To be chosen from category 
                 *                              array in OneTimeTask class)
                 * @param {Number} year
                 * @param {Number} month Month of the current year for which the task is scheduled
                 * @param {Number} date Date of the current year for which the task is scheduled
                 * @param {Number} numOfSess Number of sessions
                 * @param {Number} durOfSess Duration of each session (Format: [Hours, Minutes])
                 * @param {String} taskAfterIt Name of the task that is to be scheduled sometime after it
                 */
                constructor(taskName, taskCategory, year, month, date, numOfSess, durOfSess, accumulatedDuration, taskAfterIt = null) {
                    super(taskName, taskCategory, year, month, date);
                    this.numOfSess = numOfSess;
                    this.durOfSess = durOfSess;
                    this.accumulatedDuration = accumulatedDuration; //TODO: When creating this object, pass in the durOfSess as the argument for this variable
                    this.taskAfterIt = taskAfterIt; //Set to be an optional parameter
                }

                getTaskAfterIt() {
                    return this.taskAfterIt;
                }

                getIndivDuration() {
                    return this.durOfSess;
                }
                
                changeDuration(duration) {
                    this.durOfSess = duration;
                }

                changeAccumulateDuration(accumulatedDuration) {
                    this.accumulatedDuration = accumulatedDuration;
                }

                getTaskCategory() {
                    return this.taskCategory;
                }

                getAccumulatedDuration() {
                    return this.accumulatedDuration;
                }

                addTask() {
                    console.log("addTask function is called");
                    let now = new Date();
                    let currDate = new Date(now.getFullYear(), now.getMonth(), now.getDate());
                    let expectedDate = new Date(this.year, this.month, this.date);
                    let index = (expectedDate - currDate)/86400000;

                    if (index < 0) {
                        return new Error('Invalid index.')
                    } else if (index < 7) {
                    // If it is a connected task
                        if (this.taskAfterIt != null) {
                            // If the connected task is to be scheduled within the next 7 days
                            //if (index < 7) {
                            let currArr = Window.nonFixedCollection[index][0]; //IMPORTANT: I think this part can be simplified by retrieving the info from the database
                            for (let i = 0; i < currArr.length; i++) {
                                // If the task which is supposed to be after the current task has been located in the only non-fixed tasks connected to fixed tasks tasks array
                                if (currArr[i].getTaskName() == this.taskAfterIt) {
                                    // If the located task has no tasks to be done after it
                                    if (currArr[i].getTaskAfterIt() == null) {
                                        // Updating the category of the connected non-fixed tasks
                                        let newCategory = this.taskCategory;
                                        // If the categories don't match
                                        if (currArr[i].getTaskCategory() != this.taskCategory) {
                                            // As long as either one of tasks are work related
                                            if (currArr[i].getTaskCategory() == 0 || this.taskCategory == 0) {
                                                // The new category will be "partially work"
                                                newCategory = 5;
                                            }
                                        }
                                        // If the categories match and they are both work
                                        if (currArr[i].getTaskCategory() == 0 && this.taskCategory == 0) {
                                            // It will be fully work
                                            newCategory = 4;
                                        }

                                        // Finding the total duration of all the connected non-fixed tasks in the format of [hours, mins]
                                        let accumHours = currArr[i].getAccumulatedDuration()[0] + (this.numOfSess * this.durOfSess[0]);
                                        let accumMins = currArr[i].getAccumulatedDuration()[1] + (this.numOfSess * this.durOfSess[1]);
                                        if (accumMins > 60) {
                                            accumHours += (accumMins - (accumMins % 60)) / 60;
                                            accumMins = accumMins % 60;
                                        }
                                        let maxAccumulatedDuration = [accumHours, accumMins];

                                        // Pop off the original task as it has to be shifted to a new position along with the new connected tasks
                                        let origTask = currArr.splice(i, 1);
                                        let pos = 0;
                                        while (pos < currArr.length && (currArr[pos].getAccumulatedDuration()[0] * 60 + currArr[pos].getAccumulatedDuration()[1]) < (maxAccumulatedDuration[0] * 60 + maxAccumulatedDuration[1])) {
                                            pos++;
                                        }
                                        // Insert back the original task in the right place
                                        currArr[pos] = origTask;

                                        // Insert all sessions of this connected task (Works for any number of sessions)
                                        for (let k = 1; k <= this.durOfSess; k++) {
                                            //let currHours = currArr[i].getAccumulatedDuration()[0] + ((k - 1) * this.durOfSess[0]);
                                            //let currMins = currArr[i].getAccumulatedDuration()[1] + ((k - 1)  * this.durOfSess[1]);
                                            let currHours = origTask.getAccumulatedDuration()[0] + (k * this.durOfSess[0]);
                                            let currMins = origTask.getAccumulatedDuration()[1] + (k  * this.durOfSess[1]);
                                            if (currMins > 60) {
                                                currMins = currMins % 60;
                                                currHours += 1;
                                            }
                                            let newTask = new NonFixedTask(this.taskName, newCategory, this.year, this.month, this.date, 1, this.durOfSess, [currHours, currMins], this.taskAfterIt);

                                            currArr[pos + (this.numOfSess - k)] = newTask;
                                        }
                                        return;
                                    // If the located task has tasks to be done after it
                                    } else {
                                        let newTaskNameAfterIt = currArr[i].getTaskAfterIt();
                                        let latestTask = i; //Might be able to simplify the process of finding the task using the database (but again, not sure)
                                        while (latestTask > 0 && currArr[latestTask].getTaskAfterIt() == currArr[latestTask - 1].getTaskAfterIt()) {
                                            latestTask--;
                                        }
                                        let newCategory = this.taskCategory;
                                        if (currArr[latestTask].getTaskCategory() != this.taskCategory) {
                                            // Same logic as above
                                            if (currArr[latestTask].getTaskCategory() == 0 || this.taskCategory == 0) {
                                                newCategory = 5; // Partially work category
                                            }
                                        }
                                        if (currArr[latestTask].getTaskCategory() == 0 && this.taskCategory == 0) {
                                            newCategory = 4; // Fully work category
                                        }

                                        // Finding the total duration of all the connected non-fixed tasks
                                        let accumHours = currArr[latestTask].getAccumulatedDuration()[0] + (this.numOfSess * this.durOfSess[0]);
                                        let accumMins = currArr[latestTask].getAccumulatedDuration()[1] + (this.numOfSess * this.durOfSess[1]);
                                        if (accumMins > 60) {
                                            accumHours += (accumMins - (accumMins % 60)) / 60;
                                            accumMins = accumMins % 60;
                                        }
                                        let maxAccumulatedDuration = [accumHours, accumMins];

                                        let connectedTasksEndIndex = latestTask;
                                        while (connectedTasksEndIndex < currArr.length - 1 && currArr[connectedTasksEndIndex].getTaskAfterIt() == currArr[connectedTasksEndIndex + 1].getTaskAfterIt()) {
                                            connectedTasksEndIndex++;
                                        }
                                        // Pop off the original task as it has to be shifted to a new position along with the new connected tasks
                                        let origTasks = currArr.splice(latestTask, connectedTasksEndIndex - latestTask + 1);
                                        let pos = 0;
                                        while (pos < currArr.length && (currArr[pos].getAccumulatedDuration()[0] * 60 + currArr[pos].getAccumulatedDuration()[1]) < (maxAccumulatedDuration[0] * 60 + maxAccumulatedDuration[1])) {
                                            pos++;
                                        }
                                        // Insert back the original task in the right place
                                        currArr.splice(pos, 0, origTasks);
                                        let origTask = currArr[pos];
                                        // Insert all sessions of this connected task (Works for any number of sessions)
                                        for (let k = 1; k <= this.durOfSess; k++) {
                                            let currHours = origTask.getAccumulatedDuration()[0] + (k * this.durOfSess[0]);
                                            let currMins = origTask.getAccumulatedDuration()[1] + (k  * this.durOfSess[1]);
                                            if (currMins > 60) {
                                                currMins = currMins % 60;
                                                currHours += 1;
                                            }
                                            let newTask = new NonFixedTask(this.taskName, newCategory, this.year, this.month, this.date, 1, this.durOfSess, [currHours, currMins], newTaskNameAfterIt);
                                            //let newTask = new NonFixedTask(this.taskName, newCategory, this.month, this.date, 1, this.durOfSess, newTaskNameAfterIt, [currArr[i].getAccumulatedDuration[0] + ((k - 1) * this.durOfSess[0]), currArr[i].getAccumulatedDuration[1] + ((k - 1)  * this.durOfSess[1])]);

                                            currArr[pos + (this.numOfSess - k)] = newTask;
                                        }
                                        return;
                                    }
                                }
                            }
                            // Check task which is supposed to be after the current task has been located in the only non-fixed tasks connected to fixed tasks tasks array
                            currArr = Window.nonFixedCollection[index][1]; //IMPORTANT: Index 1 is the priority array
                            for (let j = 0; j < currArr.length; j++) {
                                // If the task which is supposed to be after the current task has been located in the only non-fixed tasks connected to fixed tasks tasks array
                                if (currArr[j].getTaskName() == this.taskAfterIt) {
                                    // If the located task has no tasks to be done after it
                                    if (currArr[j].getTaskAfterIt() == null) {
                                        // Updating the category of the connected non-fixed tasks
                                        let newCategory = this.taskCategory;
                                        if (currArr[j].getTaskCategory() != this.taskCategory) {
                                            // Same logic as above
                                            if (currArr[j].getTaskCategory() == 0 || this.taskCategory == 0) {
                                                newCategory = 5; // Partially work
                                            }
                                        }
                                        if (currArr[j].getTaskCategory() == 0 && this.taskCategory == 0) {
                                            newCategory = 4; // Fully work
                                        }

                                        let origTask = currArr[j];
                                        // Insert all sessions of this connected task (Works for any number of sessions)
                                        for (let k = 1; k <= this.durOfSess; k++) {
                                            let currHours = origTask.getAccumulatedDuration()[0] + (k * this.durOfSess[0]);
                                            let currMins = origTask.getAccumulatedDuration()[1] + (k  * this.durOfSess[1]);
                                            if (currMins > 60) {
                                                currMins = currMins % 60;
                                                currHours += 1;
                                            }
                                            let newTask = new NonFixedTask(this.taskName, newCategory, this.year, this.month, this.date, 1, this.durOfSess, [currHours, currMins], this.taskAfterIt);
                                            //let newTask = new NonFixedTask(this.taskName, newCategory, this.month, this.date, 1, this.durOfSess, this.taskAfterIt, [currArr[i].getAccumulatedDuration[0] + ((k - 1) * this.durOfSess[0]), currArr[i].getAccumulatedDuration[1] + ((k - 1)  * this.durOfSess[1])]);

                                            currArr[j + (this.numOfSess - k)] = newTask;
                                        }
                                        return;
                                    // If the located task has tasks to be done after it
                                    } else {
                                        let newTaskNameAfterIt = currArr[j].getTaskAfterIt();
                                        let latestTask = j;
                                        while (latestTask > 0 && currArr[latestTask].getTaskAfterIt() == currArr[latestTask - 1].getTaskAfterIt()) {
                                            latestTask--;
                                        }
                                        let newCategory = this.taskCategory;
                                        if (currArr[latestTask].getTaskCategory() != this.taskCategory) {
                                            // Same logic as above
                                            if (currArr[latestTask].getTaskCategory() == 0 || this.taskCategory == 0) {
                                                newCategory = 5; // Partially work
                                            }
                                        }
                                        if (currArr[latestTask].getTaskCategory() == 0 && this.taskCategory == 0) {
                                            newCategory = 4; // Fully work
                                        }

                                        let origTask = currArr[latestTask];
                                        // Insert all sessions of this connected task (Works for any number of sessions)
                                        for (let k = 1; k <= this.durOfSess; k++) {
                                            let currHours = origTask.getAccumulatedDuration()[0] + (k * this.durOfSess[0]);
                                            let currMins = origTask.getAccumulatedDuration()[1] + (k  * this.durOfSess[1]);
                                            if (currMins > 60) {
                                                currMins = currMins % 60;
                                                currHours += 1;
                                            }
                                            let newTask = new NonFixedTask(this.taskName, newCategory, this.year, this.month, this.date, 1, this.durOfSess, [currHours, currMins], newTaskNameAfterIt);
                                            //let newTask = new NonFixedTask(this.taskName, newCategory, this.month, this.date, 1, this.durOfSess, newTaskNameAfterIt, [currArr[i].getAccumulatedDuration[0] + ((k - 1) * this.durOfSess[0]), currArr[i].getAccumulatedDuration[1] + ((k - 1)  * this.durOfSess[1])]);

                                            currArr[latestTask + (this.numOfSess - k)] = newTask;
                                        }
                                        return;
                                    }
                                }
                            }
                        // If the non-fixed task is not connected to anything
                        // TODO: IMPORTANT: For this else block, do we split up the sessions? Check the scheduling algo first for this. But as of now, it has not been split up yet. And if we are split up, what will be the names of the sessions (but I think should be able to follow the same behaviour as above, just that database it might be an issue. But for this we can also make it such that we can add a number behind the name string for the doc name itself, but each task doc will the taskName field from which we will retrieve the actual task name? Dk if this will be a problem for task deletion tho)
                        } else {
                            console.log("I come here is as I am a disconnected non-fixed task.")
                            let currArr = Window.nonFixedCollection[index][0];
                            // Finding the total duration of all the connected non-fixed tasks in the format of [hours, mins]
                            let accumHours = (this.numOfSess * this.durOfSess[0]);
                            let accumMins = (this.numOfSess * this.durOfSess[1]);
                            if (accumMins > 60) {
                                accumHours += (accumMins - (accumMins % 60)) / 60;
                                accumMins = accumMins % 60;
                            }
                            let maxAccumulatedDuration = [accumHours, accumMins];
                            console.log("The accumulated duration is " + maxAccumulatedDuration);
                            // Pop off the original task as it has to be shifted to a new position along with the new connected tasks
                            let pos = 0;
                            
                            while (pos < currArr.length && currArr[pos].getAccumulatedDuration() < maxAccumulatedDuration) { //TODO: Problem - Can't do such direct comparison if duration is in [hours, mins] format. Can we have the accumulated duration in mins or is there a need for hours and mins? (Comparison formula yet to be written)
                                pos++;
                            }
                            
                            console.log("pos is " + pos);
                            // Insert all sessions of this connected task (Works for any number of sessions)
                            for (let k = 1; k <= this.numOfSess; k++) {
                                console.log("I come to the for loop");
                                //let currHours = currArr[i].getAccumulatedDuration()[0] + ((k - 1) * this.durOfSess[0]);
                                //let currMins = currArr[i].getAccumulatedDuration()[1] + ((k - 1)  * this.durOfSess[1]);
                                let currHours = (k * this.durOfSess[0]);
                                let currMins = (k * this.durOfSess[1]);
                                if (currMins > 60) {
                                    currMins = currMins % 60;
                                    currHours += 1;
                                }
                                let newTask = new NonFixedTask(this.taskName, this.taskCategory, this.year, this.month, this.date, 1, this.durOfSess, [currHours, currMins], this.taskAfterIt);

                                currArr[pos + (this.numOfSess - k)] = newTask;
                            }
                            console.log("Task '" + this.taskName + "' added to task list.")
                            for (let i = 0; i < Window.nonFixedCollection.length; i++) {
                                console.log("Normal array at index " + i + " of nonFixedCollection is " + Window.nonFixedCollection[i][0]);
                                console.log("Priority array at index " + i + " of nonFixedCollection is " + Window.nonFixedCollection[i][1]);
                            }
                            console.log(Window.nonFixedCollection[0][0][pos - 1]);
                            console.log(Window.nonFixedCollection[0][0][pos]);
                            console.log(Window.nonFixedCollection[0][0][pos + 1]);
                            console.log(Window.nonFixedCollection[0][0][pos + 2]);
                            console.log(Window.nonFixedCollection[0][0][pos + 3]);
                            return;
                        }
                    // If the index >= 7 (to be added in the future
                    // IMPORTANT: If there is > 1 session, we will not split them up like we did above. Instead, when the day is right, we will just call the addTask() function on this task again and let the splitting be done then
                    } else {
                        let currArr = Window.nonFixedFutureArr;
                        let g = 0;
                        let currTaskTime = new Date(this.year, this.month, this.date, 0).getTime();
                        while (g < currArr.length && currTaskTime > new Date(currArr[g].getYear(), currArr[g].getMonth(), currArr[g].getDate(), 0).getTime()) {
                            g++;
                        }
                        currArr[g] = this;
                        return;
                    }
                }
            }
        </script>
        <?php
            $taskName = $_POST['taskName'];
            echo "let name = '$taskName';";
            echo 'console.log(name)';
        ?>
    </body>
</html>