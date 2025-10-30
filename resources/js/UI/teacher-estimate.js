document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('teacher-estimate-form');
    
    if (!form) {
        return;
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const estimated = formData.get('estimated');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Validate the input
        const estimatedNum = parseInt(estimated);
        if (isNaN(estimatedNum) || estimatedNum < 0 || estimatedNum > 100) {
            alert('Veuillez entrer un nombre entre 0 et 100');
            return;
        }
        
        // Send POST request
        fetch('/user/update-estimate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                estimated: estimatedNum
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Hide the banner
                document.getElementById('teacher-estimate-banner').style.display = 'none';
                // Show success message
                document.getElementById('teacher-estimate-success').style.display = 'block';
            } else {
                alert('Une erreur est survenue. Veuillez réessayer.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Une erreur est survenue. Veuillez réessayer.');
        });
    });
});
