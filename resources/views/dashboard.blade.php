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
                    There's nothing here.
                </div>
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
