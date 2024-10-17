<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('File Upload') }}
        </h2>
    </x-slot>
    <style>
        /* Basic styles for the file drop area */
        .upload-area {
            border: 2px dashed #ccc;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: border-color 0.3s;
        }

        .upload-area.dragover {
            border-color: #000;
        }

        .upload-area input[type="file"] {
            display: none;
        }

        .file-info {
            margin-top: 10px;
        }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="col-span-full px-8 mt-6">
                    <h1>Upload Your Documents (PDF or TXT)</h1>

                    <!-- File Upload Area -->
                    <div class="upload-area" id="upload-area">
                        <p>Drag & Drop your file here or click to select</p>
                        <input type="file" id="fileInput" name="file" accept=".pdf,.txt">
                    </div>

                    <!-- Show selected file information -->
                    <div class="file-info" id="file-info"></div>
                    <input type="hidden" id="user_id" name="user_id" value="{{ Auth::user()->id }}">
                    <!-- Submit Button -->
                    <button id="submit-btn"
                        class="bg-green-200 text-green-800 rounded-lg text-xs text-center self-center px-3 py-2 my-2 mx-2">Upload</button>

                    <!-- Show success or error message -->
                    <div id="message" style="margin-top: 10px; color: green;"></div>

                    <script>
                        // Get the elements
                        const uploadArea = document.getElementById('upload-area');
                        const fileInput = document.getElementById('fileInput');
                        const fileInfo = document.getElementById('file-info');
                        const submitBtn = document.getElementById('submit-btn');
                        const messageDiv = document.getElementById('message');
                        let selectedFile = null;

                        // Add event listeners for drag-and-drop functionality
                        uploadArea.addEventListener('dragover', (e) => {
                            e.preventDefault();
                            uploadArea.classList.add('dragover');
                        });

                        uploadArea.addEventListener('dragleave', () => {
                            uploadArea.classList.remove('dragover');
                        });

                        uploadArea.addEventListener('drop', (e) => {
                            e.preventDefault();
                            uploadArea.classList.remove('dragover');
                            handleFileUpload(e.dataTransfer.files[0]);
                        });

                        // Handle click to select file
                        uploadArea.addEventListener('click', () => fileInput.click());

                        // Handle file input change
                        fileInput.addEventListener('change', (e) => handleFileUpload(e.target.files[0]));

                        // Function to handle file upload
                        function handleFileUpload(file) {
                            if (validateFile(file)) {
                                selectedFile = file;
                                fileInfo.textContent = `Selected File: ${file.name}`;
                            } else {
                                fileInfo.textContent = `Invalid file type. Only PDF and TXT are allowed.`;
                                selectedFile = null;
                            }
                        }

                        // Function to validate file type
                        function validateFile(file) {
                            const allowedTypes = ['application/pdf', 'text/plain'];
                            return allowedTypes.includes(file.type);
                        }

                        // Handle form submission
                        submitBtn.addEventListener('click', () => {
                            if (!selectedFile) {
                                alert('Please select a valid file before uploading.');
                                messageDiv.textContent = 'Please select a valid file before uploading.';
                                return;
                            }

                            const formData = new FormData();
                            var user_id = document.getElementById('user_id').value;
                            formData.append('file', selectedFile);
                            formData.append('user_id', user_id);

                            // Send file to the server using AJAX
                            fetch('{{ route('file.upload') }}', {
                                    method: 'POST',
                                    body: formData,
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        alert('File uploaded successfully!');
                                        messageDiv.textContent = 'File uploaded successfully!';
                                        window.location.reload();
                                    } else {
                                        messageDiv.textContent = 'Error uploading file.';
                                    }
                                })
                                .catch(error => {
                                    messageDiv.textContent = 'An error occurred while uploading the file.';
                                    console.error('Upload error:', error);
                                });
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-10">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <span class="font-semibold text-lg px-6">
                    <label for="">File Upload List</label>
                </span>
                @if (!empty($file_upload))
                    @php
                        $count = 1;
                    @endphp
                    <table class="min-w-full">
                        <thead class="border-b">
                            <tr>
                                <th scope="col" class="text-sm font-medium text-gray-900 px-6 py-4 text-left">#
                                </th>
                                <th scope="col" class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                                    File Upload
                                </th>
                                <th scope="col" class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (json_decode($file_upload) as $item)
                                <tr class="border-b">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ @$count }}
                                    </td>
                                    <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                        {{ @$item->file_upload_url }}
                                    </td>
                                    <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('document.view', $item->file_url) }}">
                                            <button
                                                class="bg-green-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                                View
                                            </button>
                                        </a>
                                        <button onClick="deletePost({{ @$item->id }})"
                                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                                @php
                                    $count++;
                                @endphp
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
    <script>
        function deletePost(id) {

            event.preventDefault();
            if (confirm('Are you sure you want to delete this item?')) {
                const formData = new FormData();
                formData.append('user_id', id);
                // Send file to the server using AJAX
                fetch('http://localhost:8000/file-upload/' + id, {
                        method: 'DELETE',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            messageDiv.textContent = 'File Deleted successfully!';
                            window.location.reload();
                        } else {
                            messageDiv.textContent = 'Error uploading file.';
                        }
                    })
                    .catch(error => {
                        messageDiv.textContent = 'An error occurred while uploading the file.';
                        console.error('Upload error:', error);
                    });
            }
        }
    </script>
</x-app-layout>
