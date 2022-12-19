var FlashToast = function () {
    var message = localStorage.getItem('message') ?? "Operação realizada com sucesso!"
    var classes = localStorage.getItem('classes') ?? "green accent-4"

    let Show = function () {
        M.toast({ html: message, classes: classes, displayLength: 5000 })
        localStorage.removeItem("flash-toast");
    }

    let Success = function (message = "Operação realizada com sucesso!") {
        localStorage.setItem("message", message);
        localStorage.setItem("classes", 'green accent-4');
        localStorage.setItem("flash-toast", true);
    }

    let Error = function (message = "Ocorreu um erro!") {
        localStorage.setItem("message", message);
        localStorage.setItem("classes", 'red accent-4');
        localStorage.setItem("flash-toast", true);
    }

    return {
        Success: function (message, seconds) {
            return Success(message, seconds);
        },
        Error: function (message, seconds) {
            return Error(message, seconds);
        },
        Show: function () {
            return Show();
        }
    }
}();

var Toast = function () {
    let Success = function (message = "Operação realizada com sucesso!") {
        M.toast({ html: message, classes: 'green accent-4', displayLength: 5000 })
    }

    let Error = function (message = "Ocorreu um erro!") {
        M.toast({ html: message, classes: 'red accent-4', displayLength: 5000 })
    }

    return {
        Success: function (message, seconds) {
            return Success(message, seconds);
        },
        Error: function (message, seconds) {
            return Error(message, seconds);
        }
    }
}();

$(document).ready(function () {
    if (localStorage.getItem("flash-toast")) {
        FlashToast.Show();
    }
});