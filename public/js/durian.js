function openModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
}
function showDurianDetails(name, total) {
    document.getElementById('viewDurianName').textContent = name;
    document.getElementById('viewDurianTotal').textContent = total;
    openModal('viewDurianModal');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

function openEditModal(id, name, total, orchard_id) {
    document.getElementById('editDurianId').value = id;
    document.getElementById('editName').value = name;
    document.getElementById('editTotal').value = total;

    // Select the correct orchard based on orchard_id
    document.getElementById('editOrchard').value = orchard_id;

    // Update the form action URL to include the Durian ID for the update
    document.getElementById('editDurianForm').action = `/durians/${id}`;

    openModal('editDurianModal');
}