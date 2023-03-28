@extends('admin.index')

@section('content')
    <h1 class="text-xl font-semibold mb-4">{{ __('Edit Role') }}</h1>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('roles.update', $role) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4 w-1/2">
                            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" name="name" id="name" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" value="{{ $role->name }}">
                        </div>

                        <div class="mb-4 w-1/2">
                            <label for="permissions" class="block text-sm font-medium text-gray-700">Permissions</label>
                            <select name="permissions[]" id="permissions" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" multiple>
                                @foreach($permissions as $permission)
                                    <option value="{{ $permission->id }}" {{ $role->hasPermissionTo($permission) ? 'selected' : '' }}>{{ $permission->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Update Role
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection