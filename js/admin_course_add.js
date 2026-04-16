document.getElementById('couse-add-form').addEventListener('submit', function () {
    const cousename = document.getElementById('coursename').value;
    const RoomName = document.querySelectorAll('input[name="interest"]:checked');
    const TraningType = document.querySelectorAll('input[name="interest"]:checked');
    const TraningDay = document.getElementById('TraningDay').value;
    const errMsg = document.getElementById('error-message');

    // 必須入力チェック
    if (cousename === "" || TraningDay === "" || RoomName.length === 0 || TraningType.length === 0 || TraningDay === "") {
        errMsg.innerText = "すべての項目を入力してください。";
        e.preventDefault(); // 送信を停止
        return;
    }
});