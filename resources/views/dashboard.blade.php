<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="col-span-full px-8 mt-6">
                    <h1>Upload File</h1>
                    @if (session('success'))
                        <p>{{ session('success') }}</p>
                        <img src="{{ asset('storage/uploads/' . session('file')) }}" alt="Uploaded File">
                    @endif
                    @if (session('error'))
                        <p>{{ session('error') }}</p>
                    @endif

                    <form action="{{ route('file.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <label for="file">Choose a file:</label>
                        <input type="file" name="file" id="file">
                        <button type="submit">Upload</button>
                    </form>
                    <label for="ask_me_anything" class="block text-sm font-medium leading-6 text-gray-900">Ask me
                        anything</label>
                    <div class="mt-2">
                        <textarea id="ask_me_anything" name="ask_me_anything" rows="3"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"></textarea>
                    </div>
                    <div class="mt-6 mb-6 flex items-center justify-end gap-x-6">
                        <button onClick="askAI()"
                            class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Ask
                            AI</button>
                    </div>
                    <label for="ai_response" class="block text-sm font-medium leading-6 text-gray-900">AI
                        Response</label>
                    <div class="mt-2">
                        <textarea readonly id="ai_response" name="ai_response" rows="3"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"></textarea>
                    </div>
                    <div class="mt-6 mb-6 flex items-center justify-end gap-x-6">
                        <input type="hidden" id="user_id" name="user_id" value="{{ Auth::user()->id }}">
                        <button onClick="save()"
                            class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Save
                            Response</button>
                    </div>
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
