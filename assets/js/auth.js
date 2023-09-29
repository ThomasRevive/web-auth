var $ = jQuery.noConflict();

$(document).ready(function () {
    $("#qr_code_form").submit(function (event) {
        event.preventDefault();

        let formData = $(this).serializeArray();

        $.ajax({
            url: "/ajax/get-qr-code.php",
            method: "POST",
            data: formData,
            dataType: "html",
            success: function (response) {
                $(".qr-code-container").html(response);
            },
            error: function (error) {
                $(".qr-code-container").html("Error: " + error);
            }
        });
    })
});