function salert(title, text, icon) {
    Swal.fire({
        title: title,
        text: text,
        icon: icon,
    });
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


// Chart Doughnut Consumption by room
var ctx1 = document.getElementById("chart-consumption").getContext("2d");

var gradientStroke1 = ctx1.createLinearGradient(0, 230, 0, 50);

gradientStroke1.addColorStop(1, 'rgba(203,12,159,0.2)');
gradientStroke1.addColorStop(0.2, 'rgba(72,72,176,0.0)');
gradientStroke1.addColorStop(0, 'rgba(203,12,159,0)'); //purple colors

new Chart(ctx1, {
    type: "doughnut",
    data: {
        labels: ['Living Room', 'Kitchen', 'Attic', 'Garage', 'Basement'],
        datasets: [{
            label: "Consumption",
            weight: 9,
            cutout: 90,
            tension: 0.9,
            pointRadius: 2,
            borderWidth: 2,
            backgroundColor: ['#FF0080', '#A8B8D8', '#21d4fd', '#98ec2d', '#ff667c'],
            data: [15, 20, 13, 32, 20],
            fill: false
        }],
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
                }
            },
            x: {
                grid: {
                    drawBorder: false,
                    display: false,
                    drawOnChartArea: false,
                    drawTicks: false,
                },
                ticks: {
                    display: false,
                }
            },
        },
    },
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


