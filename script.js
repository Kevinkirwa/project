document.getElementById('create-table-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const tableName = document.getElementById('table-name').value;
    const columns = document.getElementById('columns').value;

    fetch('api/create_table.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ tableName, columns })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            generateAddRecordsForm(tableName, columns);
        } else {
            alert('Error creating table.');
        }
    });
});

function generateAddRecordsForm(tableName, columns) {
    const formContainer = document.getElementById('add-records-form-container');
    formContainer.innerHTML = '';

    let formHtml = `<h2>Add Records to ${tableName}</h2><form id="add-records-form">`;
    for (let i = 1; i <= columns; i++) {
        formHtml += `<label for="column-${i}">Column ${i}:</label><input type="text" id="column-${i}" name="column-${i}" required><br><br>`;
    }
    formHtml += `<button type="submit">Add Record</button></form>`;
    formContainer.innerHTML = formHtml;

    document.getElementById('add-records-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('tableName', tableName);

        fetch('api/add_records.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Record added successfully.');
            } else {
                alert('Error adding record.');
            }
        });
    });
}
