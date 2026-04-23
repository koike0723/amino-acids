const addBtn = document.getElementById("add_btn");
const delBtn = document.getElementById("del_btn");
let studentNumber = document.querySelectorAll(".input_student").length;

// 入力欄1行を全部取得
function getStudentRows() {
    return document.querySelectorAll(".input_student");
}

// 入力欄の親要素を取得
function getParentContainer() {
    const firstRow = document.querySelector(".input_student");
    return firstRow ? firstRow.parentElement : null;
}

// 入力値を空にする
function clearRow(row) {
    const lastName = row.querySelector(".last_name");
    const firstName = row.querySelector(".first_name");
    const number = row.querySelector(".student_number");

    if (lastName) lastName.value = "";
    if (firstName) firstName.value = "";
    if (number) {
        studentNumber++;
        number.value = studentNumber;
    }
}

// 追加
addBtn.addEventListener("click", function (e) {
    e.preventDefault();

    const rows = getStudentRows();
    const parent = getParentContainer();

    if (!rows.length || !parent) return;

    const lastRow = rows[rows.length - 1];
    const newRow = lastRow.cloneNode(true);

    clearRow(newRow);

    parent.insertBefore(newRow, document.getElementById("display_parent"));
});

// 削除
delBtn.addEventListener("click", function (e) {
    e.preventDefault();

    const rows = getStudentRows();

    if (rows.length > 1) {
        rows[rows.length - 1].remove();
        studentNumber--;
    }
});