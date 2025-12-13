<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Incident API Request</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f7fa;
            padding: 20px;
        }
        h2 {
            color: #2c3e50;
        }
        .card {
            background: #ffffff;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 6px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        .severity-high { color: red; font-weight: bold; }
        .severity-medium { color: orange; font-weight: bold; }
        .severity-low { color: green; font-weight: bold; }
    </style>
</head>
<body>

<h2>Incident Reports</h2>
<div id="incidentContainer">Loading...</div>

<script>
(async function () {
    try {
        // API Endpoint
        const apiUrl = 'https://crime.alertara.com/api/endpoint/sample.php';

        // Request API
        const response = await fetch(apiUrl);

        if (!response.ok) {
            throw new Error('Failed to fetch API');
        }

        const result = await response.json();

        const container = document.getElementById('incidentContainer');
        container.innerHTML = '';

        // Display data
        result.data.forEach(item => {
            const div = document.createElement('div');
            div.className = 'card';

            let severityClass = '';
            if (item.severity === 'High') severityClass = 'severity-high';
            if (item.severity === 'Medium') severityClass = 'severity-medium';
            if (item.severity === 'Low') severityClass = 'severity-low';

            div.innerHTML = `
                <strong>Type:</strong> ${item.incident_type}<br>
                <strong>Location:</strong> ${item.location}<br>
                <strong>Severity:</strong>
                <span class="${severityClass}">${item.severity}</span><br>
                <small>Reported at: ${item.reported_at}</small>
            `;

            container.appendChild(div);
        });

    } catch (error) {
        document.getElementById('incidentContainer').innerHTML =
            '<p style="color:red;">Error loading data</p>';
        console.error(error);
    }
})();
</script>

</body>
</html>
