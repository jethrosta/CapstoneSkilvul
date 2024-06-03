document.addEventListener('DOMContentLoaded', function() {
    var dropArea = document.getElementById('dropArea');
    var fileInput = document.getElementById('fileInput');
    var uploadButton = document.getElementById('uploadButton');
    var notification = document.getElementById('notification');
    var notificationText = document.getElementById('notificationText');
    var resultDiv = document.getElementById('result');
    var processedImage = document.getElementById('processedImage');

    if (dropArea && fileInput && uploadButton && notification) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropArea.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, unhighlight, false);
        });

        dropArea.addEventListener('drop', handleDrop, false);

        function highlight() {
            dropArea.classList.add('highlight');
        }

        function unhighlight() {
            dropArea.classList.remove('highlight');
        }

        function handleDrop(e) {
            var dt = e.dataTransfer;
            var files = dt.files;
            handleFiles(files);
        }

        fileInput.addEventListener('change', function() {
            handleFiles(fileInput.files);
        });

        function handleFiles(files) {
            var file = files[0];
            if (file) {
                if (file.size <= 5 * 1024 * 1024) {
                    var statusText = document.getElementById('dropArea');
                    if (statusText) {
                        statusText.textContent = 'Selected file: ' + file.name;
                    }
                } else {
                    alert('File size exceeds the limit. Please choose a file smaller than 5 MB.');
                }
            }
        }

        uploadButton.addEventListener('click', function() {
            if (fileInput.files.length > 0) {
                var formData = new FormData();
                formData.append('file', fileInput.files[0]);

                fetch('/upload', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    notification.style.display = 'block';
                    notificationText.textContent = "Upload successful!";
                    uploadButton.style.display = 'none';
                    ocrText.textContent = data.text;
                    processedImage.src = data.processed_image;
                    resultDiv.style.display = 'block';
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            } else {
                showAlert('Please select a file before uploading.');
            }
        });


        function showAlert(message) {
            var alertPopup = document.createElement('div');
            alertPopup.className = 'alert-popup';
            alertPopup.textContent = message;
            document.body.appendChild(alertPopup);
            setTimeout(function() {
                alertPopup.remove();
            }, 3000);
        }
    }
});
