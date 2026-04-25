const toggle_ele = document.querySelector("main");
const open_btn = document.getElementById("open_btn");
const close_btn = document.getElementById("close_btn");
const drawerArea = document.getElementById("drawer_area");
const tableArea = document.querySelector(".cc-detail-table-area");

open_btn.addEventListener("click", () => {
    toggle_ele.classList.add("open");
    tableArea.style.marginBottom = drawerArea.offsetHeight + "px";
});

close_btn.addEventListener("click", () => {
    toggle_ele.classList.remove("open");
    tableArea.style.marginBottom = "";
});
