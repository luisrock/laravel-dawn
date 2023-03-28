<x-app-layout>
    <div class="flex flex-col md:flex-row min-h-screen">
        <!-- Hamburger Button -->
        <button id="hamburger-btn" class="md:hidden p-4 focus:outline-none focus:bg-gray-700">
            <svg class="w-6 h-6 text-white" viewBox="0 0 24 24">
                <path d="M4 6h16c1.1 0 2-.9 2-2s-.9-2-2-2H4C2.9 2 2 2.9 2 4s.9 2 2 2zm16 4H4c-1.1 0-2 .9-2 2s.9 2 2 2h16c1.1 0 2-.9 2-2s-.9-2-2-2zm0 8H4c-1.1 0-2 .9-2 2s.9 2 2 2h16c1.1 0 2-.9 2-2s-.9-2-2-2z"/>
            </svg>
        </button>

        <div id="sidebar-menu" class="hidden md:block w-full md:w-64 bg-gray-800 dark:bg-gray-900 p-4 flex-shrink-0">
            <nav class="mt-5">
                <div class="mb-4">
                    <h2 class="text-lg font-semibold text-white">Users</h2>
                    <ul>
                        <li class="py-1">
                            <a href="{{ route('users.index') }}" class="text-gray-300 hover:text-white">List Users</a>
                        </li>
                        <li class="py-1">
                            <a href="{{ route('users.create') }}" class="text-gray-300 hover:text-white">Create User</a>
                        </li>
                    </ul>
                </div>
                <div class="mb-4">
                    <h2 class="text-lg font-semibold text-white">Roles</h2>
                    <ul>
                        <li class="py-1">
                            <a href="{{ route('roles.index') }}" class="text-gray-300 hover:text-white">List Roles</a>
                        </li>
                        <li class="py-1">
                            <a href="{{ route('roles.create') }}" class="text-gray-300 hover:text-white">Create Role</a>
                        </li>
                    </ul>
                </div>
                <div class="mb-4">
                    <h2 class="text-lg font-semibold text-white">Permissions</h2>
                    <ul>
                        <li class="py-1">
                            <a href="{{ route('permissions.index') }}" class="text-gray-300 hover:text-white">List Permissions</a>
                        </li>
                        <li class="py-1">
                            <a href="{{ route('permissions.create') }}" class="text-gray-300 hover:text-white">Create Permission</a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
        <div class="w-full md:flex-1 p-4">
            @yield('content')
        </div>
    </div>

    <script>
        document.getElementById('hamburger-btn').addEventListener('click', function() {
            document.getElementById('sidebar-menu').classList.toggle('hidden');
        });
    </script>
</x-app-layout>