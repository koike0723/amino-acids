let add_btn = document.getElementById("add_btn");
let input_field = document.getElementById("input_field");

add_btn.addEventListener("click", (e) => {
    e.preventDefault();
    let new_input_field = input_field.cloneNode(true);
    add_btn.before(new_input_field);
});