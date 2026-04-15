document.addEventListener('', () => {
    const submitBtn = document.getElementById('').value;
    submitBtn.addEventListener("click", () => {
        const username = document.getElementById("username").value;
        const nameError = document.getElementById("nameError");
        let isValid = true;

        // --- バリデーション処理 ---
        // 必須チェック
        if (username.trim() === "") {
            nameError.textContent = "未入力の項目があります";
            isValid = false;
        } else {
            nameError.textContent = ""; // エラーなし
        }
        // --- 結果判定 ---
        if (isValid) {
            form.submit();
        }
    });
});
