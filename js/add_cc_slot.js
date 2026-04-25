const ccDate = document.querySelector('input[name="cc_date"]').value;

document.getElementById('add_btn').addEventListener('click', async (e) => {
    e.preventDefault();
    const body = new FormData();
    body.append('cc_date', ccDate);
    try {
        const res  = await fetch('php_do/cc_slot_add_do.php', { method: 'POST', body });
        const data = await res.json();
        if (data.success) {
            location.reload();
        } else {
            alert('スロットの追加に失敗しました: ' + (data.message ?? ''));
        }
    } catch {
        alert('通信エラーが発生しました');
    }
});

document.querySelectorAll('.cc-slot-delete-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
        if (!confirm('このスロットを削除しますか？\n予約がある場合も削除されます。')) return;
        const body = new FormData();
        body.append('slot_id', btn.dataset.slotId);
        try {
            const res  = await fetch('php_do/cc_slot_delete_do.php', { method: 'POST', body });
            const data = await res.json();
            if (data.success) {
                location.reload();
            } else {
                alert('スロットの削除に失敗しました: ' + (data.message ?? ''));
            }
        } catch {
            alert('通信エラーが発生しました');
        }
    });
});
