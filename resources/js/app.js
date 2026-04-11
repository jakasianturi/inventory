require("./bootstrap");

$(".dropdown-menu").on("click", function (e) {
    e.stopPropagation();
    if ($(e.target).is("[data-toggle=modal]")) {
        $($(e.target).data("target")).modal();
    }
});
