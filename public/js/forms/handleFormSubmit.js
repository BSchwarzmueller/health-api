document.getElementById('create-medication-form')?.addEventListener('submit', async function (event) {
    event.preventDefault();
    await handleFormSubmit(event, '/api/medications', 'POST');
});

document.getElementById('update-medication-form')?.addEventListener('submit', async function (event) {
    event.preventDefault();
    const medicationId = document.getElementById('medication_id').value;
    await handleFormSubmit(event, `/api/medications/${medicationId}`, 'PUT');
});

document.getElementById('delete-medication-form')?.addEventListener('submit', async function (event) {
    event.preventDefault();
    const medicationId = document.getElementById('medication_id').value;
    await handleFormSubmit(event, `/api/medication/${medicationId}`, 'DELETE');
});

document.getElementById('review-medications-form')?.addEventListener('submit', async function (event) {
    event.preventDefault();
    const userID = document.getElementById('user_id').value;
    await handleFormSubmit(event, `/api/medications/${userID}`, 'GET');
})

async function handleFormSubmit(event, url, method) {
    const formData = new FormData(event.target);
    const data = Object.fromEntries(formData.entries());

    try {
        const options = {
            method: method,
            headers: {
                'Content-Type': 'application/json'
            }
        };

        if (method !== 'GET') {
            options.body = JSON.stringify(data);
        }

        const response = await fetch(url, options);
        const result = await response.json();

        if (response.ok) {
            alert('Operation successful');
        } else {
            alert('Error: ' + result.error);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while processing the request');
    }
}