// Define constants
let pirates = [];
let currentIndex = 0;

// List of possible pirate positions
const positions = [
    "Captain",
    "Right-hand-man",
    "Shipwright",
    "Doctor",
    "Cook",
    "Sniper",
    "Navigator",
    "Archaeologist",
    "Musician",
    "Helmsman",
    "Admiral",
    "Vice Admiral",
    "Fodder",
    "None"
];

// Load the pirates data initially
function loadPirates() {
    const httpRequest = new XMLHttpRequest();
    httpRequest.onreadystatechange = function() {
        if (httpRequest.readyState === XMLHttpRequest.DONE) {
            if (httpRequest.status === 200) {
                pirates = JSON.parse(httpRequest.responseText);

                // Ensure each pirate has an 'id' if it doesn't already exist
                pirates.forEach((pirate, index) => {
                    if (!pirate.hasOwnProperty('id')) {
                        pirate.id = index + 1; // Assign a unique ID based on the index
                    }
                });

                // Populate the table with the first pirate
                displayPirate(currentIndex);  // Display the first pirate's details
                populatePositionsDropdown();  // Populate the position dropdown
            } else {
                alert('There was a problem with the request.');
            }
        }
    };
    httpRequest.open('GET', 'read_json.php');
    httpRequest.send();
}


// Populate the pirate position dropdown with options
function populatePositionsDropdown() {
    const positionSelect = document.getElementById('piratePosition');
    positionSelect.innerHTML = ''; // Clear existing options
    positions.forEach(position => {
        const option = document.createElement('option');
        option.value = position;
        option.textContent = position;
        positionSelect.appendChild(option);
    });
}

// Call the function to load pirate data
loadPirates();
    
// Function to display a pirate by index
function displayPirate(index) {
    const pirate = pirates[index];
    document.getElementById('pirateName').value = pirate.Name;
    document.getElementById('affiliation').value = pirate.Affiliation;
    document.getElementById('pirateBounty').value = pirate.Bounty.toLocaleString() + " Berries";
    document.getElementById('pirateDevilFruit').checked = pirate['Devil fruit'];
    document.getElementById('pirateImage').src = pirate.img || 'img/default.png'; // Fallback image

    // Select the pirate's current position in the dropdown
    document.getElementById('piratePosition').value = pirate.Position;

    // Update the current position display
    document.getElementById('currentPosition').textContent = `${currentIndex + 1} / ${pirates.length}`;

    // Update the table to show only the current pirate (You can use this function for the table)
    updateQueryTable(pirate);
}



function getPirateIndex(index){
    const httpRequest = new XMLHttpRequest();
    httpRequest.onreadystatechange = function() {
        if (httpRequest.readyState === XMLHttpRequest.DONE) {
            if (httpRequest.status === 200) {
                const pirate = JSON.parse(httpRequest.responseText);
                displayPirate(pirate);
            } else {
                alert('Failed to retrieve pirate.');
            }
        }
    };
    httpRequest.open('POST', 'getPirateIndex.php');
    httpRequest.setRequestHeader('Content-Type', 'application/json');
    httpRequest.send(JSON.stringify({ index: index }));
}

// Navigation buttons
document.getElementById('nextButton').addEventListener('click', function() {
    currentIndex = (currentIndex + 1) % pirates.length;
    displayPirate(currentIndex);
});

document.getElementById('prevButton').addEventListener('click', function() {
    currentIndex = (currentIndex - 1 + pirates.length) % pirates.length;
    displayPirate(currentIndex);
});

document.getElementById('firstButton').addEventListener('click', function() {
    currentIndex = 0;
    displayPirate(currentIndex);
});

document.getElementById('lastButton').addEventListener('click', function() {
    currentIndex = pirates.length - 1;
    displayPirate(currentIndex);
});


// Edit mode
document.addEventListener('DOMContentLoaded', function() {
    // Hide all buttons except Edit and Sort
    const actionButtons = [
        document.getElementById('Insert'),
        document.getElementById('Delete'),
        document.getElementById('Save'),
        document.getElementById('SaveAll')
    ];
        
    actionButtons.forEach(button => button.style.display = 'none');
        
    // Disable input fields initially
    const inputs = [
        document.getElementById('pirateName'),
        document.getElementById('affiliation'),
        document.getElementById('pirateBounty'),
        document.getElementById('pirateDevilFruit'),
        document.getElementById('piratePosition')
    ];
    inputs.forEach(input => (input.disabled = true));
});

let isEditing = false;

// Edit button
document.getElementById('editButton').addEventListener('click', function() {
    const actionButtons = [
        document.getElementById('Insert'),
        document.getElementById('Delete'),
        document.getElementById('Save'),
        document.getElementById('SaveAll')
    ];

    // Toggle editing mode
    isEditing = !isEditing;
        
    // Show or hide action buttons
    actionButtons.forEach(button => button.style.display = isEditing ? 'inline-block' : 'none');
        
    // Enable or disable input fields
    const inputs = [
        document.getElementById('pirateName'),
        document.getElementById('affiliation'),
        document.getElementById('pirateBounty'),
        document.getElementById('pirateDevilFruit'),
        document.getElementById('piratePosition')
    ];
    inputs.forEach(input => (input.disabled = !isEditing));

    // Update the Edit button's text
    document.getElementById('editButton').textContent = isEditing ? 'Cancel Edit' : 'Edit';
});

// Insert button
document.getElementById('Insert').addEventListener('click', function () {
    const newPirate = {
        Name: '',
        Position: '', // placeholder for dropdown
        Affiliation: '',
        Bounty: 0,
        'Devil fruit': false,
        img: 'img/OP.png' // Placeholder image or default
    };
        
    const httpRequest = new XMLHttpRequest();
    httpRequest.onreadystatechange = function () {
        if (httpRequest.readyState === XMLHttpRequest.DONE) {
            if (httpRequest.status === 200) {
                // Parse server response to ensure successful creation
                const createdPirate = JSON.parse(httpRequest.responseText);
                pirates.push(createdPirate); // Add to local array
                currentIndex = pirates.length - 1; // Set current index to the new pirate
                displayPirate(currentIndex); // Show the new pirate
            } else {
                alert('There was a problem inserting the new pirate.');
            }
        }
    };
        
    httpRequest.open('POST', 'insert.php');
    httpRequest.setRequestHeader('Content-Type', 'application/json');
    httpRequest.send(JSON.stringify({ newItem: newPirate }));
});
        
        
        
// Delete button
document.getElementById('Delete').addEventListener('click', function () {
    const itemId = pirates[currentIndex].id;  // Get the current pirate ID to delete
    console.log('Sending request to delete pirate with ID:', itemId);  // Log the ID

    const httpRequest = new XMLHttpRequest();
    
    httpRequest.onreadystatechange = function () {
        if (httpRequest.readyState === XMLHttpRequest.DONE) {
            if (httpRequest.status === 200) {
                // Parse the response to check for success
                const response = JSON.parse(httpRequest.responseText);
                if (response.success) {
                    alert('Pirate Deleted successfully.');

                    // Remove the pirate from the local array
                    pirates.splice(currentIndex, 1);

                    // Adjust the currentIndex if necessary
                    if (pirates.length === 0) {
                        // If no more pirates, reset the form and display no pirate
                        document.getElementById('pirateName').value = '';
                        document.getElementById('affiliation').value = '';
                        document.getElementById('pirateBounty').value = '';
                        document.getElementById('pirateDevilFruit').checked = false;
                        document.getElementById('pirateImage').src = '';
                        document.getElementById('piratePosition').value = '';
                        document.getElementById('currentPosition').textContent = '0 / 0';
                    } else {
                        // If there are remaining pirates, update currentIndex and show the next pirate
                        if (currentIndex >= pirates.length) {
                            currentIndex = pirates.length - 1;  // Go back to the last pirate if at the end
                        }
                        displayPirate(currentIndex);
                    }
                } else {
                    alert(response.message);
                }
            } else {
                alert('There was a problem deleting the pirate.');
            }
        }
    };

    // Send the deletion request
    httpRequest.open('POST', 'delete.php');
    httpRequest.setRequestHeader('Content-Type', 'application/json');
    httpRequest.send(JSON.stringify({ id: itemId }));
});

document.getElementById('Save').addEventListener('click', function() {
    const pirate = pirates[currentIndex];  // Get the pirate currently being edited        

    // Update the pirate object with values from the form
    pirate.Name = document.getElementById('pirateName').value;
    pirate.Position = document.getElementById('piratePosition').value;
    pirate.Affiliation = document.getElementById('affiliation').value;
    pirate.Bounty = parseInt(document.getElementById('pirateBounty').value.replace(/[^0-9]/g, ''), 10) || 0;
    pirate['Devil fruit'] = document.getElementById('pirateDevilFruit').checked;
    
    // Only update the image if a new image has been uploaded
    const uploadedImage = document.getElementById('pirateImagePath').value;
    if (uploadedImage) {
        pirate.img = uploadedImage;  // Set to new uploaded image
    } else {
        // If no new image uploaded, keep the current image (don't change)
        pirate.img = pirate.img || pirate.img;  // Ensure it keeps the current image if no update
    }

    console.log(JSON.stringify(pirates));  // Log the updated pirates array for debugging

    // Save this array to localStorage or sessionStorage if needed for persistence across page reloads
    localStorage.setItem('piratesData', JSON.stringify(pirates));

    updateQueryTable(pirate);  // Update the table with the modified pirate
});

// Sort button
document.getElementById('Sort').addEventListener('click', function() {
    pirates.sort((a, b) => 
        a.Name.trim().toLowerCase().localeCompare(b.Name.trim().toLowerCase())
    );

    // Log the sorted array to verify the order
    console.log('Sorted Pirates:', pirates);

    // Refresh the display to show the updated order
    currentIndex = 0; // Reset to the first pirate after sorting
    displayPirate(currentIndex);
});

        

// Save all button
document.getElementById('SaveAll').addEventListener('click', function() {
    const dataToSend = { data: pirates };  // Send the entire pirates array

    const httpRequest = new XMLHttpRequest();
    httpRequest.onreadystatechange = function() {
        if (httpRequest.readyState === XMLHttpRequest.DONE) {
            if (httpRequest.status === 200) {
                console.log(httpRequest.responseText); // Log the response to see the raw output
                try {
                    const response = JSON.parse(httpRequest.responseText);
                    alert('Data saved successfully.');
                } catch (e) {
                    alert('Failed to parse JSON: ' + e.message);
                }
            } else {
                alert('There was a problem saving the data.');
            }
        }
    };

    httpRequest.open('POST', 'saveAll.php');
    httpRequest.setRequestHeader('Content-Type', 'application/json');
    httpRequest.send(JSON.stringify(dataToSend));
});


// Upload file function
function uploadFile() {
    const fileInput = document.getElementById('fileInput');
    const file = fileInput.files[0];

    if (file) {
        const formData = new FormData();
        formData.append('fileup', file);

        const xhr = new XMLHttpRequest();

        xhr.open('POST', 'uploadfile.php', true);

        // Handle the response
        xhr.onload = function () {
            if (xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        const imagePath = response.imagePath;

                        // Update the image on the page
                        document.getElementById('pirateImage').src = imagePath;

                        // Optionally save the image path in a hidden field
                        document.getElementById('pirateImagePath').value = imagePath;
                    } else {
                        alert(response.message || 'An error occurred during file upload.');
                    }
                } catch (error) {
                    console.error('Invalid JSON response:', xhr.responseText);
                    alert('Server returned an invalid response. Check the console for details.');
                }
            } else {
                alert('An error occurred while uploading the file.');
            }
        };

        // Send the file
        xhr.send(formData);
    } else {
        alert('Please select a file to upload.');
    }
}

function updateQueryTable(pirate) {
    const tableBody = document.getElementById('query-table-body');
    tableBody.innerHTML = ''; // Clear existing rows

    // Create a row for the current pirate
    const row = document.createElement('tr');
    row.innerHTML = `
        <td>${pirate.id}</td>
        <td>${pirate.Name}</td>
        <td>${pirate.Position}</td>
        <td>${pirate.Affiliation}</td>
        <td>${pirate.Bounty.toLocaleString()}</td>
        <td>${pirate['Devil fruit'] === '0' ? 'True' : 'False'}</td>
        <td>${pirate.img}</td>
    `;
    tableBody.appendChild(row);
}


