//日程はデフォで今日の日付
//かつ、クリックした際にカレンダーが下に出てきて、クリックして日程を選べる
document.addEventListener('DOMContentLoaded', function () {
    const now = new Date();
    document.getElementById('course-date').value = now.toISOString().slice(0,10);
});

//教室もドロップボックスで選ぶ形にするSS
