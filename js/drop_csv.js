const alert_parent = document.querySelector(".content-wrap");
const output_filename = document.getElementById("output_filename");
const drop_area = document.getElementById("csv_drop_area");
const input_file = document.getElementById("csv_file");

function csvCheck(file){
    const is_csv_name = file.name.toLowerCase().endsWith(".csv");
    const is_csv_mime =
        file.type === "text/csv" ||
        file.type === "application/vnd.ms-excel";

    return is_csv_name && is_csv_mime;
}

document.getElementById("csv_file").addEventListener("change", (e) => {
    
    // すでにアラートがある場合の要素削除
    let old_alert = document.querySelector(".alert");
    if (old_alert) {
        old_alert.remove();
    }

    // ファイル通信できているかのチェック＆フォームにアップされたファイル情報の格納
    let file = e.currentTarget.files[0];
    if (!file) return;

    // CSVファイルかどうかの判別
    if (!csvCheck(file)) {
        const not_csv_alert = document.createElement('div');
        not_csv_alert.className = 'alert alert-danger';
        not_csv_alert.textContent = 'CSVファイルではありません。';
        alert_parent.before(not_csv_alert);
        output_filename.textContent = "CSVファイルを選択";
        return;
    }

    // 選択されたファイル名の表示
    output_filename.textContent = file.name;
});




// ファイルドラック中の見た目処理
drop_area.addEventListener("dragover", (e) => {
    e.preventDefault();
    drop_area.style.backgroundColor = "#eef";
});
drop_area.addEventListener("dragleave", () => {
    drop_area.style.backgroundColor = "";
});

// ドロップしたファイルの処理
drop_area.addEventListener("drop", (e) => {
    e.preventDefault();
    drop_area.style.backgroundColor = "";

    // すでにアラートがある場合の要素削除
    let old_alert = document.querySelector(".alert");
    if (old_alert) {
        old_alert.remove();
    }

    // ドロップされたファイル情報の格納
    let drop_file = e.dataTransfer.files[0];
    if (!drop_file) return;

    // CSVファイルかどうかの判別
    if (!csvCheck(drop_file)) {
        const not_csv_alert = document.createElement('div');
        not_csv_alert.className = 'alert alert-danger';
        not_csv_alert.textContent = 'CSVファイルではありません。';
        alert_parent.before(not_csv_alert);
        output_filename.textContent = "CSVファイルを選択";
        return;
    }

    const data_transfer = new DataTransfer();
    data_transfer.items.add(drop_file);
    input_file.files = data_transfer.files;

    // ドロップされたファイル名の表示
    output_filename.textContent = drop_file.name;
});