function salert(title, text, icon) {
    Swal.fire({
        title: title,
        text: text,
        icon: icon,
    });
}

function sprocessing(title,allowEscapeKey=false, showConfirmButton=false, allowOutsideClick= false ) {
    Swal.fire({
        title: title,
        html: '  <span class="spinner-border spinner-border-lg text-primary"></span>\n',
        allowEscapeKey: allowEscapeKey,
        showConfirmButton: showConfirmButton,
        allowOutsideClick: allowOutsideClick,
        allowEnterKey: false,
    });
}
function copyTextToClipboard(elementId) {

    var textArea = document.createElement("textarea");
    document.body.appendChild(textArea);
    console.log(document.getElementById(elementId));
    textArea.value = document.getElementById(elementId).value ? document.getElementById(elementId).value : document.getElementById(elementId).innerText;

    textArea.focus();
    textArea.select();


    try {
        var successful = document.execCommand('copy');
        var msg = successful ? 'successful' : 'unsuccessful';
        Swal.fire({
            title: 'Details Copied',
        });
    } catch (err) {
        console.log(err);

        throw new Error("Oops, unable to copy RRR");

    }

    document.body.removeChild(textArea);


}

function toggleModal(modalId) {
    $(modalId).modal('toggle');
}

function triggerConfigurationOptions() {
    fixedPlugin.classList.contains("show") ? fixedPlugin.classList.remove("show") : fixedPlugin.classList.add("show");
}

function logout() {
    document.getElementById("logOut").submit();
}

// {{--countJs Script--}}

if (document.getElementById('status1')) {
    const countUp = new CountUp('status1', document.getElementById("status1").getAttribute("countTo"));
    if (!countUp.error) {
        countUp.start();
    } else {
        console.error(countUp.error);
    }
}
if (document.getElementById('status2')) {
    const countUp = new CountUp('status2', document.getElementById("status2").getAttribute("countTo"));
    if (!countUp.error) {
        countUp.start();
    } else {
        console.error(countUp.error);
    }
}
if (document.getElementById('status3')) {
    const countUp = new CountUp('status3', document.getElementById("status3").getAttribute("countTo"));
    if (!countUp.error) {
        countUp.start();
    } else {
        console.error(countUp.error);
    }
}
if (document.getElementById('status4')) {
    const countUp = new CountUp('status4', document.getElementById("status4").getAttribute("countTo"));
    if (!countUp.error) {
        countUp.start();
    } else {
        console.error(countUp.error);
    }
}

//scrollbarjs
var win = navigator.platform.indexOf('Win') > -1;
if (win && document.querySelector('#sidenav-scrollbar')) {
    var options = {
        damping: '0.5'
    }
    Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
}



// {{--chartJs--}}

// Rounded slider
const setValue = function(value, active) {
    document.querySelectorAll("round-slider").forEach(function(el) {
        if (el.value === undefined) return;
        el.value = value;
    });
    const span = document.querySelector("#value");
    span.innerHTML = value;
    if (active)
        span.style.color = 'red';
    else
        span.style.color = 'black';
}

document.querySelectorAll("round-slider").forEach(function(el) {
    el.addEventListener('value-changed', function(ev) {
        if (ev.detail.value !== undefined)
            setValue(ev.detail.value, false);
        else if (ev.detail.low !== undefined)
            setLow(ev.detail.low, false);
        else if (ev.detail.high !== undefined)
            setHigh(ev.detail.high, false);
    });

    el.addEventListener('value-changing', function(ev) {
        if (ev.detail.value !== undefined)
            setValue(ev.detail.value, true);
        else if (ev.detail.low !== undefined)
            setLow(ev.detail.low, true);
        else if (ev.detail.high !== undefined)
            setHigh(ev.detail.high, true);
    });
});




// Chart Consumption by day
var ctx = document.getElementById("chart-cons-week").getContext("2d");

new Chart(ctx, {
    type: "bar",
    data: {
        labels: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
        datasets: [{
            label: "Watts",
            tension: 0.4,
            borderWidth: 0,
            borderRadius: 4,
            borderSkipped: false,
            backgroundColor: "#3A416F",
            data: [150, 230, 380, 220, 420, 200, 70],
            maxBarThickness: 6
        }, ],
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false,
            }
        },
        interaction: {
            intersect: false,
            mode: 'index',
        },
        scales: {
            y: {
                grid: {
                    drawBorder: false,
                    display: false,
                    drawOnChartArea: false,
                    drawTicks: false,
                },
                ticks: {
                    display: false
                },
            },
            x: {
                grid: {
                    drawBorder: false,
                    display: false,
                    drawOnChartArea: false,
                    drawTicks: false
                },
                ticks: {
                    beginAtZero: true,
                    font: {
                        size: 12,
                        family: "Open Sans",
                        style: 'normal',
                    },
                    color: "#9ca2b7"
                },
            },
            y: {
                grid: {
                    drawBorder: false,
                    display: false,
                    drawOnChartArea: true,
                    drawTicks: false,
                    borderDash: [5, 5]
                },
                ticks: {
                    display: true,
                    padding: 10,
                    color: '#9ca2b7'
                }
            },
            x: {
                grid: {
                    drawBorder: false,
                    display: true,
                    drawOnChartArea: true,
                    drawTicks: false,
                    borderDash: [5, 5]
                },
                ticks: {
                    display: true,
                    padding: 10,
                    color: '#9ca2b7'
                }
            },
        },
    },
});


