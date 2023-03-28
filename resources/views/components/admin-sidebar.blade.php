<!-- Admin Sidebar -->
<div class="w-1/4 pr-4">
    <div class="mb-4">
        <h3 class="font-semibold text-lg">Users</h3>
        <ul>
            <li><a href="{{ route('users.index') }}" class="text-blue-500">List Users</a></li>
            <li><a href="{{ route('users.create') }}" class="text-blue-500">Create User</a></li>
        </ul>
    </div>
    <div class="mb-4">
        <h3 class="font-semibold text-lg">Roles</h3>
        <ul>
            <li><a href="{{ route('roles.index') }}" class="text-blue-500">List Roles</a></li>
            <li><a href="{{ route('roles.create') }}" class="text-blue-500">Create Role</a></li>
        </ul>
    </div>
    <div class="mb-4">
        <h3 class="font-semibold text-lg">Permissions</h3>
        <ul>
            <li><a href="{{ route('permissions.index') }}" class="text-blue-500">List Permissions</a></li>
            <li><a href="{{ route('permissions.create') }}" class="text-blue-500">Create Permission</a></li>
        </ul>
    </div>
</div>
