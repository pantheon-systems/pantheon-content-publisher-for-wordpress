var conn = new WebSocket('ws://localhost:8080/websocket');

conn.onopen = function(e) {
    console.log("Preview connection established!");
};

conn.onmessage = function(e) {
    let articleId = e.data;
    // Get the current URL
    const url = new URL(window.location.href);
    const params = new URLSearchParams(url.search);
    const pccGrant = params.get('pcc_grant');

    // Create data object to send
    const data = {
        document_id: articleId,
        pcc_grant: pccGrant
    };

	`${window.PCCAdmin.rest_url}/disconnect`
    // Send data to WordPress REST API endpoint via AJAX (fetch API)
    fetch(`${window.PCCFront.rest_url}/preview-content`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-WP-Nonce': window.PCCFront.nonce // Assuming wpApiSettings is enqueued and available
        },
        body: JSON.stringify(data)
    })
        .then(response => response.json())
        .then(result => {
            console.log('Success:', result);
            var entryContents = document.getElementsByClassName('entry-content');

            // Iterate through each element with class 'entry-content'
            entryContents[0].innerHTML = result.content
            // entryContents[0].innerHTML = result.title
        })
        .catch(error => {
            console.error('Error:', error);
        });
    };

conn.onerror = function(error) {
    console.error('WebSocket error: ' + error.message);
};

conn.onclose = function(e) {
    console.log('Connection closed');
};
