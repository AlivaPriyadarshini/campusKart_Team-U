document.addEventListener("DOMContentLoaded", function () {

    console.log("CampusKart loaded successfully");


    // Add small animation to buttons

    const buttons = document.querySelectorAll(".btn");

    buttons.forEach(function (button) {

        button.addEventListener("click", function () {

            button.style.transform = "scale(0.97)";

            setTimeout(function () {
                button.style.transform = "scale(1)";
            }, 100);

        });

    });


    // Quantity validation

    const quantityInputs =
        document.querySelectorAll(".quantity");

    quantityInputs.forEach(function (input) {

        input.addEventListener("change", function () {

            if (input.value < 1) {
                input.value = 1;
            }

        });

    });

});