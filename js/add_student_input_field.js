const add_btn = document.getElementById("add_btn");
let input_field = document.getElementById("input_field");
const display_parent = document.getElementById("display_parent");
let student_number = 1;

add_btn.addEventListener("click", (e) => {
    e.preventDefault();
    student_number++;
    let new_input_field = input_field.cloneNode(true);
    let ele_last_name = new_input_field.querySelector(".last_name");
    let ele_first_name = new_input_field.querySelector(".first_name");
    let ele_student_number = new_input_field.querySelector(".student_number");
    ele_last_name.value = "";
    ele_first_name.value = "";
    ele_student_number.value = student_number;
    display_parent.before(new_input_field);
});