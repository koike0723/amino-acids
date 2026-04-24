/**
 * キャリコン入力欄の動的追加・削除
 */
document.addEventListener("DOMContentLoaded", () => {
    const addBtn = document.getElementById("add_btn");
    const delBtn = document.getElementById("del_btn");
    const ccBox = document.getElementById("cc_box");
    const insertTarget = document.getElementById("display_parent");

    // 追加処理
    addBtn.addEventListener("click", (e) => {
        e.preventDefault();

        // 1. 現在のカードを取得
        const cards = ccBox.querySelectorAll(".card");
        if (cards.length === 0) return;

        // 2. 最後のカードをコピー
        const lastCard = cards[cards.length - 1];
        const newCard = lastCard.cloneNode(true);

        // 3. 新しいカードの調整
        const nextIndex = cards.length + 1;

        // タイトル（キャリコン○）の書き換え
        const title = newCard.querySelector("dt");
        if (title) title.textContent = `キャリコン${nextIndex}`;

        // 入力欄のクリアとID重複排除
        const inputs = newCard.querySelectorAll("input[type='date']");
        inputs.forEach((input, i) => {
            input.value = "";
            // IDがあると重複するため削除（または連番を振る）
            input.id = `cc${nextIndex}_${i + 1}`;
            // PHPで配列として受け取るためにname属性を調整（重要）
            // もし既存の cc1_1, cc1_2 という形式を維持したい場合は以下
            input.name = `cc${nextIndex}_${i + 1}`;
        });

        // ラベルのfor属性もIDに合わせる
        const labels = newCard.querySelectorAll("label");
        labels.forEach((label, i) => {
            label.setAttribute("for", `cc${nextIndex}_${i + 1}`);
        });

        // 4. 画面に追加（ボタンの直前に挿入）
        ccBox.insertBefore(newCard, insertTarget);
    });

    // 削除処理
    delBtn.addEventListener("click", (e) => {
        e.preventDefault();

        const cards = ccBox.querySelectorAll(".card");
        // 最初の3つ（初期状態）は消さない、などの制限が必要な場合はここを調整
        if (cards.length > 1) {
            cards[cards.length - 1].remove();
        } else {
            alert("これ以上削除できません。");
        }
    });
});