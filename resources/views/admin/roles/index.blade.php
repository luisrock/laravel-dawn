@extends('admin.index')

@section('content')
    <h1 class="text-xl font-semibold mb-4">{{ __('Roles') }}</h1>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <a href="{{ route('roles.create') }}" class="inline-block mb-4 px-4 py-2 font-semibold text-white bg-blue-500 rounded hover:bg-blue-600">{{ __('Create Role') }}</a>

                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('ID') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Name') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($roles as $role)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $role->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $role->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('roles.edit', $role) }}" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                            {{ __('Edit') }}
                                        </a>
                                        <form action="{{ route('roles.destroy', $role) }}" method="POST" class="inline" onsubmit="return confirmDelete();">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-600 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                                                {{ __('Delete') }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>
        function confirmDelete() {
            return confirm(' {{ __("Are you sure you want to remove this role?") }} ');
        }
    </script>
@endsection