document.getElementById('create-medication-form').addEventListener('submit', async (event) => {
    event.preventDefault();

    const formData = {
        name: document.getElementById('name').value,
        started_at: document.getElementById('started_at').value,
        dosage: document.getElementById('dosage').value,
        note: document.getElementById('note').value || null
    };

    console.log(formData)
    try {
        const response = await fetch('/api/medications', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        const text = await response.text();
        console.log('Response:', text);

        const result = JSON.parse(text);

        if (response.ok) {
            alert(result.message || 'Medication created successfully!');
        } else {
            alert(result.error || 'An error occurred');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An unexpected error occurred');
    }
});