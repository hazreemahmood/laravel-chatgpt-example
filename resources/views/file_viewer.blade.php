<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-lite.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f4f4f4;
        }

        .document-container {
            max-width: 100%;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        .document-content {
            margin-top: 20px;
        }

        .pdf-viewer {
            height: 1000px;
            width: 100%;
            border: none;
        }

        .context-menu {
            display: none;
            position: absolute;
            background-color: white;
            border: 1px solid #ccc;
            z-index: 1000;
            width: 150px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .context-menu ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .context-menu ul li {
            padding: 10px;
            cursor: pointer;
            border-bottom: 1px solid #eee;
        }

        .context-menu ul li:last-child {
            border-bottom: none;
        }

        .context-menu ul li:hover {
            background-color: #f0f0f0;
        }


        body {
            font-family: Arial, sans-serif;
        }

        .custom-menu {
            display: none;
            position: absolute;
            background-color: white;
            border: 1px solid #ccc;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }

        .custom-menu ul {
            list-style: none;
            padding: 10px;
            margin: 0;
        }

        .custom-menu li {
            padding: 8px 12px;
            cursor: pointer;
        }

        .custom-menu li:hover {
            background-color: #f0f0f0;
        }

        #queryContainer {
            display: none;
            margin-top: 10px;
        }

        textarea {
            width: 100%;
            height: 100px;
            margin-top: 5px;
        }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="col-span-full px-8 mt-6 pb-8">
                    <div id="customContextMenu" class="context-menu">
                        <ul>
                            <li id="askAiOption">Ask AI</li>
                            <li id="copyOption">Copy</li>
                        </ul>
                    </div>
                    <div id="queryContainer">
                        <label for="userQuery">Type your query for <span id="userQueryLabel"></span></label>
                        <textarea id="userQuery" placeholder="Ask your question..."></textarea>
                        <div id="loader"
                            class="w-36 h-36 border-8 rounded-full border-t-lime-400 animate-spin hidden">
                            <!-- Circle 2-->
                        </div>
                        <div id="buttonQuery">
                            <button id="closeQuery"
                                class="bg-red-700 rounded-lg text-white text-xs text-center self-center px-3 py-2 my-2 mx-2">Close</button>
                            <button id="submitQuery"
                                class="bg-green-200 text-green-800 rounded-lg text-xs text-center self-center px-3 py-2 my-2 mx-2">Submit</button>
                        </div>
                    </div>
                    <div class="document-container">
                        <a href="{{ route('file-upload.show', Auth::user()->id) }}">
                            <button id="submit-btn"
                                class="bg-green-200 text-green-800 rounded-lg text-xs text-center self-center px-3 py-2 my-2 mx-2">
                                Back
                            </button>
                        </a>
                        <h1>Document Viewer for <b>{{ $fileName }}</b></h1>

                        @if ($fileType == 'pdf')
                            <!-- PDF Viewer using iframe to display the PDF -->
                            <iframe src="{{ asset('storage/uploads/' . $fileName) }}" class="pdf-viewer"></iframe>
                        @else
                            <!-- Text file viewer with Summernote editor -->
                            <h3>Editable Text Content:</h3>
                            <div id="status" style="margin-top: 10px; color: green;"></div>
                            <textarea id="documentEditor" class="document-content">{{ $content }}</textarea>
                            <button id="saveButton"
                                class="bg-green-200 text-green-800 rounded-lg text-xs text-center self-center px-3 py-2 my-2 mx-2">Save
                                Changes</button>
                        @endif
                    </div>

                    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-lite.min.js"></script>
                    <script>
                        $(document).ready(function() {
                            // Initialize Summernote editor for text files
                            $('#documentEditor').summernote({
                                height: 1000,
                                toolbar: [
                                    ['style', ['bold', 'italic', 'underline', 'clear']],
                                    ['fontsize', ['fontsize']],
                                    ['para', ['ul', 'ol', 'paragraph']],
                                    ['height', ['height']],
                                    ['insert', ['link']],
                                ]
                            });
                        });

                        // Handle Save button click
                        $('#saveButton').on('click', function() {
                            customMenu.style.display = 'none';
                            queryContainer.style.display = 'none'; // Hide query box
                            const content = $('#documentEditor').summernote('code'); // Get the HTML content
                            const path = "{{ $fileName }}"; // Get the file path from the server

                            saveContent(content, path);
                        });

                        // Save content to server
                        function saveContent(content, path) {
                            console.log(content);
                            console.log(path);
                            $.ajax({
                                url: '/save-document/' + "{{ $fileName }}", // Your Laravel route for saving
                                method: 'POST',
                                data: {
                                    content: content,
                                    path: path,
                                    _token: '{{ csrf_token() }}' // Include CSRF token for security
                                },
                                success: function(response) {
                                    console.log(response);
                                    $('#status').text('Content saved successfully!');
                                },
                                error: function(xhr) {
                                    $('#status').text('Error saving content: ' + xhr.responseText);
                                }
                            });
                        }
                        const customMenu = document.getElementById('customContextMenu');
                        const textContainer = document.getElementById('textContainer');
                        const queryContainer = document.getElementById('queryContainer');
                        const userQuery = document.getElementById('userQuery');
                        // Listen for the right-click event (context menu event)
                        document.addEventListener('contextmenu', function(event) {
                            // Prevent the default context menu from appearing
                            event.preventDefault();

                            // Get the selected text
                            const selectedText = window.getSelection().toString().trim();

                            // If text is selected, show the custom context menu
                            if (selectedText.length > 0) {
                                const contextMenu = document.getElementById('customContextMenu');
                                contextMenu.style.display = 'block';
                                contextMenu.style.left = `${event.pageX}px`; // Set the position of the menu
                                contextMenu.style.top = `${event.pageY}px`;

                                // Store the selected text for use in actions
                                contextMenu.setAttribute('data-selected-text', selectedText);
                            } else {
                                // If no text is selected, hide the menu
                                hideContextMenu();
                            }
                        });

                        // Hide the custom context menu when clicking elsewhere on the page
                        document.addEventListener('click', function(event) {
                            if (!event.target.closest('.context-menu')) {
                                hideContextMenu();
                            }
                        });

                        // Function to hide the context menu
                        function hideContextMenu() {
                            const contextMenu = document.getElementById('customContextMenu');
                            contextMenu.style.display = 'none';
                        }

                        // Handle the "Copy" option
                        document.getElementById('copyOption').addEventListener('click', function() {
                            const selectedText = document.getElementById('customContextMenu').getAttribute('data-selected-text');

                            if (selectedText) {
                                // Copy the selected text to the clipboard
                                navigator.clipboard.writeText(selectedText).then(function() {
                                    alert('Text copied to clipboard!');
                                }).catch(function(err) {
                                    console.error('Failed to copy text: ', err);
                                });
                            }

                            hideContextMenu();
                        });

                        window.addEventListener('click', () => {
                            // console.log('hello');
                            customMenu.style.display = 'none';
                            // queryContainer.style.display = 'none'; // Hide query box
                        });

                        document.getElementById('askAiOption').addEventListener('click', () => {
                            const selectedText = document.getElementById('customContextMenu').getAttribute('data-selected-text');
                            document.getElementById('userQueryLabel').textContent = selectedText;
                            customMenu.style.display = 'none';
                            if (selectedText) {
                                queryContainer.style.display = 'block';
                                userQuery.value = ''; // Clear previous input
                                userQuery.focus();
                            }
                        });

                        document.getElementById('submitQuery').addEventListener('click', async () => {
                            const query = userQuery.value;
                            if (query) {
                                const response = await askAI(query);
                                console.log('response', response);
                                userQuery.value = response; // Display the response in the text box
                            }
                        });

                        document.getElementById('closeQuery').addEventListener('click', async () => {
                            customMenu.style.display = 'none';
                            queryContainer.style.display = 'none'; // Hide query box
                        });

                        async function askAI(userInput) {
                            document.getElementById('loader').style.display = 'block';
                            document.getElementById('buttonQuery').style.display = 'none';
                            const selectedText = document.getElementById('customContextMenu').getAttribute('data-selected-text');
                            const predefinedPrompt =
                                `User selected text: "${selectedText}"\nUser query: "${userInput}"\nAI Response:`;
                            const aiContent = userInput + ' "' + selectedText + '"';
                            var apiUrl = "http://127.0.0.1:8000/api/chat";

                            const response = await fetch(apiUrl, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify({
                                    content: predefinedPrompt, // Adjust based on the model you want to use
                                }),
                            });

                            if (response.ok) {
                                document.getElementById('loader').style.display = 'none';
                                document.getElementById('buttonQuery').style.display = 'block';
                                const data = await response.json();
                                return data;
                            } else {
                                console.error('Error fetching from API:', response);
                                return 'Error fetching response. Please try again.';
                            }
                        }
                    </script>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
