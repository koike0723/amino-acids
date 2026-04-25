document.querySelectorAll('.cc_slot').forEach(slot => {
    const slotId = slot.dataset.slotId;
    const roomSel = slot.querySelector('select[name^="room_id"]');
    const consultantSel = slot.querySelector('select[name^="consultant_id"]');

    const save = async () => {
        const body = new FormData();
        body.append('slot_id', slotId);
        body.append('room_id', roomSel.value);
        body.append('consultant_id', consultantSel.value);
        try {
            const res  = await fetch('php_do/cc_slot_update_do.php', { method: 'POST', body });
            const data = await res.json();
            if (!data.success) {
                alert('保存に失敗しました: ' + (data.message ?? ''));
            }
        } catch {
            alert('通信エラーが発生しました');
        }
    };

    roomSel.addEventListener('change', save);
    consultantSel.addEventListener('change', save);
});
