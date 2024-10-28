<div id="timer-widget" class="fixed bottom-4 right-4 bg-white rounded-lg shadow-lg border p-4" style="display: none;">
    <div class="flex items-center space-x-4">
        <div>
            <div class="text-sm text-gray-600">Current Task:</div>
            <div id="current-task" class="font-medium"></div>
        </div>
        <div>
            <div class="text-sm text-gray-600">Time:</div>
            <div id="timer-display" class="font-medium">00:00:00</div>
        </div>
        <button id="stop-timer"
            class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600"
            onclick="stopTimer()">
            Stop
        </button>
    </div>
</div>

<script>
    let timerInterval;
    let startTime;
    let timerId;

    function updateTimer() {
        fetch('/timetracker-php/timer/current')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.timer) {
                    showTimer(data.timer);
                } else {
                    hideTimer();
                }
            });
    }

    function showTimer(timer) {
        document.getElementById('timer-widget').style.display = 'block';
        document.getElementById('current-task').textContent = timer.task_name;
        startTime = new Date(timer.start_time);
        timerId = timer.id;

        if (!timerInterval) {
            timerInterval = setInterval(updateDisplay, 1000);
        }
    }

    function hideTimer() {
        document.getElementById('timer-widget').style.display = 'none';
        if (timerInterval) {
            clearInterval(timerInterval);
            timerInterval = null;
        }
    }

    function updateDisplay() {
        const now = new Date();
        const diff = Math.floor((now - startTime) / 1000);

        const hours = Math.floor(diff / 3600);
        const minutes = Math.floor((diff % 3600) / 60);
        const seconds = diff % 60;

        document.getElementById('timer-display').textContent =
            `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
    }

    function stopTimer() {
        fetch(`/timetracker-php/timer/${timerId}/stop`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    hideTimer();
                }
            });
    }

    // Check timer status every 30 seconds
    updateTimer();
    setInterval(updateTimer, 30000);
</script>