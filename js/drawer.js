const toggle_ele = document.querySelector("main");
const open_btn = document.getElementById("open_btn");
const close_btn = document.getElementById("close_btn");
open_btn.addEventListener("click", () => {
    toggle_ele.classList.add("open");
});

close_btn.addEventListener("click", () => {
    toggle_ele.classList.remove("open");
});