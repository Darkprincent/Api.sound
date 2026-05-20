const API_URL = 'http://s1/api.gen-sound.ru/tracks';

let currentEditId = null;

async function loadTracks() {
    try {
        const response = await fetch(API_URL);
        const tracks = await response.json();
        const tbody = document.getElementById('track-list');
        tbody.innerHTML = '';

        tracks.forEach(track => {
            tbody.innerHTML += `
                <tr>
                    <td>${track.id}</td>
                    <td>${escapeHtml(track.title)}</td>
                    <td>${escapeHtml(track.author_name)}</td>
                    <td>
                        <button class="btn btn-sm btn-info" onclick="editTrack(${track.id})">Изменить</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteTrack(${track.id})">Удалить</button>
                    </td>
                </tr>
            `;
        });
    } catch (error) {
        console.error('Ошибка загрузки:', error);
        alert('Не удалось загрузить треки.');
    }
}

async function deleteTrack(id) {
    if (!confirm('Удалить трек?')) return;
    await fetch(`${API_URL}/${id}`, { method: 'DELETE' });
    loadTracks();
}

async function editTrack(id) {
    const response = await fetch(`${API_URL}/${id}`);
    const track = await response.json();

    currentEditId = track.id;

    document.getElementById('editId').value = track.id;
    document.getElementById('title').value = track.title;
    document.getElementById('author_id').value = track.author_id;

    document.getElementById('formTitle').innerHTML = '✏️ Редактировать трек';
    document.getElementById('submitBtn').innerHTML = 'Обновить';
    document.getElementById('cancelBtn').style.display = 'inline-block';
}

function cancelEdit() {
    currentEditId = null;
    document.getElementById('trackForm').reset();

    document.getElementById('formTitle').innerHTML = '➕ Добавить трек';
    document.getElementById('submitBtn').innerHTML = 'Создать';
    document.getElementById('cancelBtn').style.display = 'none';
}

document.getElementById('trackForm').onsubmit = async (event) => {
    event.preventDefault();

    const title = document.getElementById('title').value;
    const author_id = document.getElementById('author_id').value;

    let url = API_URL;
    let options = {};

    if (currentEditId) {
        url = `${API_URL}/${currentEditId}`;
        options = {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ title, author_id })
        };
    } else {
        const formData = new URLSearchParams();
        formData.append('title', title);
        formData.append('author_id', author_id);

        options = {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: formData
        };
    }

    const response = await fetch(url, options);
    const result = await response.json();

    if (result.status) {
        cancelEdit();
        loadTracks();
    } else {
        alert('Произошла ошибка при сохранении.');
    }
};

document.getElementById('cancelBtn').onclick = cancelEdit;

function escapeHtml(str) {
    if (!