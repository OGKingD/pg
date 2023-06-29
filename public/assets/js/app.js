function salert(title, text, icon,showConfirmButton = false, allowOutsideClick = false,allowEnterKey = false, allowEscapeKey = false) {
    Swal.fire({
        title: title,
        text: text,
        icon: icon,
        allowOutsideClick: allowOutsideClick,
        allowEnterKey: allowEnterKey,
        allowEscapeKey: allowEscapeKey,
        showConfirmButton: showConfirmButton,

    });
}

function positonedAlert(position,htmlMessage) {
    Swal.fire({
        position: position,
        showConfirmButton: false,
        showCloseButton: true,
        html: htmlMessage,
        width: 300,
        background: "#fb6340",
        allowOutsideClick: false,
        allowEnterKey: false,
        allowEscapeKey: false,

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

function stimer(title,timer = 2000) {
    let e;
    Swal.fire({
        title: title,
        html: "I will redirect in <b></b> milliseconds.",
        timer: timer,
        timerProgressBar: !0,
        didOpen: () => {
            Swal.showLoading(), e = setInterval(() => {
                const e = Swal.getHtmlContainer();
                if (e) {
                    const t = e.querySelector("b");
                    t && (t.textContent = Swal.getTimerLeft())
                }
            }, 100)
        },
        willClose: () => {
            clearInterval(e)
        }
    }).then(e => {
        Swal.dismiss();
        Swal.DismissReason.timer;
    })

}
addEventListener('alertBox',function () {
    let type = event.detail.type;
    let message = event.detail.message;
    if (type === "processing"){
        sprocessing(message ?? "Fetching Transaction")
    }
    if (type === "success"){
        salert("Success", message ?? "Transaction Fetched!",'success',true)
    }
    if (type === "info"){
        salert("Info",message,'success')
    }
})
addEventListener("closeAlert",function () {
    Swal.close();
})
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
        if (msg !== "successful"){
            Swal.fire({
                title: '"Oops, unable to copy"',
            });
        }
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

function setUserField(type,value,id) {
    let userDiv = document.getElementById('userListing');
    userDiv.style.display = "none";

    if (type === "email"){
        let emailField = document.getElementById('customer_email');
        emailField.value = value;
    }
    if (type === "name"){
        let nameField = document.getElementById('username');
        nameField.value = value;
    }
    let userIdField = document.getElementById('userId');
    userIdField.value = id;

    console.log(type,document.getElementById('username').value)

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






