<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
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
                    <button id="submit-btn" class="bg-green-200 text-green-800 rounded-lg text-xs text-center self-center px-3 py-2 my-2 mx-2">Upload</button>

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
                                messageDiv.textContent = 'Please select a valid file before uploading.';
                                return;
                            }

                            const formData = new FormData();
                            var user_id = document.getElementById('user_id').value;
                            formData.append('file', selectedFile);
                            formData.append('user_id', user_id);
                            console.log(selectedFile);

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
                                        messageDiv.textContent = 'File uploaded successfully!';
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
                    <label for="">Post List</label>
                </span>
                @if (!empty($_COOKIE['post']))
                    @foreach (json_decode($_COOKIE['post']) as $item)
                        @php
                            @$post = json_decode($item->post);
                            @$ask_me_anything = $post->ask_me_anything;
                            @$ai_response = $post->ai_response;
                        @endphp
                        <ul class="bg-white rounded-lg shadow divide-y divide-gray-200">
                            <li class="px-6 py-4">
                                <div class="flex justify-between">
                                    <span class="font-semibold text-lg">
                                        {{ $ask_me_anything }}
                                    </span>
                                    <span class="text-gray-500 text-xs">1 day ago
                                        <button onClick="deletePost({{ @$item->id }})"
                                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                            Delete
                                        </button>
                                    </span>
                                </div>
                                <p class="text-gray-700">
                                    {!! nl2br(e($ai_response)) !!}
                                </p>
                            </li>
                        </ul>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        var tid = setInterval(function() {
            if (document.readyState !== 'complete') return;
            clearInterval(tid);
            // do your work
            getPost();
        }, 100);

        function getPost() {
            var url = "http://127.0.0.1:8000/api/getPost";
            var user_id = document.getElementById('user_id').value;
            try {
                axios.post(url, {
                        title: 'Axios POST request',
                        body: 'This is a POST request using Axios',
                        user_id: user_id
                    })
                    .then(response => {
                        console.log(response.data);
                        document.cookie = "post=" + JSON.stringify(response.data);
                        // localStorage.setItem('post', JSON.stringify(response.data));
                    })
                    .catch(error => {
                        console.error(error); // Handle error
                    });
            } catch (error) {
                console.error(error);
            };
        }

        function deletePost(id) {
            var url = "http://127.0.0.1:8000/api/deletePost";
            // var user_id = document.getElementById('user_id').value;
            try {
                axios.post(url, {
                        title: 'Axios POST request',
                        body: 'This is a POST request using Axios',
                        id: id
                    })
                    .then(response => {
                        console.log(response.data);
                        getPost();
                        setInterval(function() {
                            window.location.reload();
                        }, 500);
                    })
                    .catch(error => {
                        console.error(error); // Handle error
                    });
            } catch (error) {
                console.error(error);
            };
        }

        function askAI() {
            var url = "http://127.0.0.1:8000/api/chat";
            var ask_me_anything = document.getElementById('ask_me_anything').value;
            var content = ask_me_anything;
            try {
                axios.post(url, {
                        title: 'Axios POST request',
                        body: 'This is a POST request using Axios',
                        content: content
                    })
                    .then(response => {
                        document.getElementById('ai_response').value = response.data;
                    })
                    .catch(error => {
                        console.error(error); // Handle error
                    });
            } catch (error) {
                console.error(error);
            };
        }

        function save() {
            var url = "http://127.0.0.1:8000/api/saveResponse";
            var ask_me_anything = document.getElementById('ask_me_anything').value;
            var ai_response = document.getElementById('ai_response').value;
            var user_id = document.getElementById('user_id').value;
            try {
                axios.post(url, {
                        title: 'Axios POST request',
                        body: 'This is a POST request using Axios',
                        ask_me_anything: ask_me_anything,
                        ai_response: ai_response,
                        user_id: user_id
                    })
                    .then(response => {
                        console.log(response.data); // Handle success
                        getPost();
                        setInterval(function() {
                            window.location.reload();
                        }, 500);
                    })
                    .catch(error => {
                        console.error(error); // Handle error
                    });
            } catch (error) {
                console.error(error);
            };
        }
    </script>
</x-app-layout>
