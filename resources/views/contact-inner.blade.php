<div class="container mx-auto px-4 py-12">
    <!-- Display success message if it exists -->
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
            <strong class="font-semibold">Success!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    <!-- Display error message if it exists -->
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <strong class="font-semibold">Error!</strong>
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <h1 class="text-3xl font-semibold mb-6">Contact Us</h1>
    <div class="bg-white shadow-md rounded-lg p-8">
        <form action="{{ route('contact.submit') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="name" class="block text-gray-700 font-semibold mb-2">Name:</label>
                <input  type="text" name="name" id="name" class="w-full border-2 border-gray-200 p-3 rounded outline-none focus:border-indigo-500" 
                        placeholder="Enter your name" 
                        value="{{ auth()->check() ? auth()->user()->name : '' }}"
                        {{ auth()->check() ? 'readonly' : '' }}
                        required>
            </div>
            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-semibold mb-2">Email:</label>
                <input  type="email" name="email" id="email" class="w-full border-2 border-gray-200 p-3 rounded outline-none focus:border-indigo-500" 
                        placeholder="Enter your email" 
                        value="{{ auth()->check() ? auth()->user()->email : '' }}"
                        {{ auth()->check() ? 'readonly' : '' }}
                        required>
            </div>
            <div class="mb-4">
                <label for="subject" class="block text-gray-700 font-semibold mb-2">Subject:</label>
                <input type="text" name="subject" id="subject" class="w-full border-2 border-gray-200 p-3 rounded outline-none focus:border-indigo-500" placeholder="Enter the subject" required>
            </div>
            <div class="mb-4">
                <label for="message" class="block text-gray-700 font-semibold mb-2">Message:</label>
                <textarea name="message" id="message" rows="5" class="w-full border-2 border-gray-200 p-3 rounded outline-none focus:border-indigo-500" placeholder="Enter your message" required></textarea>
            </div>
            <button type="submit" class="bg-indigo-500 hover:bg-indigo-600 text-white font-semibold py-2 px-4 rounded">Submit</button>
        </form>
    </div>
</div>