const sessionInactivityLimit = 10 * 60 * 1000; // 1 minute in milliseconds
let sessionInactivityTimer;

// Function to reset the inactivity timer
function resetSessionInactivityTimer() {
    clearTimeout(sessionInactivityTimer);

    // Start a new inactivity timer
    sessionInactivityTimer = setTimeout(() => {
        // Show the modal and set the localStorage item after inactivity
        $('#timeout').modal('show');
        localStorage.setItem('timeoutAcknowledged', 'true');
    }, sessionInactivityLimit);
}

// Function to detect user activity and reset the timer
function detectActivity() {
    window.onload = resetSessionInactivityTimer;
    window.onmousemove = resetSessionInactivityTimer;
    window.onmousedown = resetSessionInactivityTimer; // Mouse click
    window.ontouchstart = resetSessionInactivityTimer; // Touchscreen
    window.onclick = resetSessionInactivityTimer; // Mouse click
    window.onkeydown = resetSessionInactivityTimer; // Keyboard press
    window.addEventListener('scroll', resetSessionInactivityTimer); // Scrolling
}

detectActivity();

// Ensure the modal shows after inactivity even on page refresh
document.addEventListener("DOMContentLoaded", function() {
    if (localStorage.getItem('timeoutAcknowledged')) {
        $('#timeout').modal('show');
        resetSessionInactivityTimer();
    }
});

document.getElementById("session_timeout").addEventListener("click", function() {
    localStorage.removeItem('timeoutAcknowledged');
    $('#timeout').modal('hide');

    resetSessionInactivityTimer();
});
