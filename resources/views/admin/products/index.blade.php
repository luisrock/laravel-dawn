@extends('admin.index')

@section('content')
    <style>
        .notification-enter,
        .notification-leave-to {
            opacity: 0;
            transform: translateY(-1rem);
        }
    </style>


    <h1 class="text-xl font-semibold mb-4">{{ __('Products (active)') }}</h1>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <a href="{{ config('services.stripe.dash_link') }}/products"
                        class="inline-block mb-4 px-4 py-2 font-semibold text-white bg-blue-500 rounded hover:bg-blue-600"
                        target="_blank">{{ __('Create Product') }}</a>

                    {{-- <div id="notification" class="hidden mb-4 px-4 py-2 text-white rounded"></div> --}}
                    <div id="notification"
                        class="fixed top-0 right-0 mt-12 mr-6 px-4 py-2 text-white rounded transition-opacity duration-300 transform transition-transform duration-300 translate-y-full">
                    </div>



                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('ID') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Name') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Price') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Role') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Show') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($products as $product)
                                {{-- {{ dd($product) }} --}}
                                @php
                                    if (!$product->active) {
                                        continue;
                                    }
                                    $product_link = config('services.stripe.dash_link') . "/products/{$product->product_id}";
                                @endphp
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ $product_link }}"
                                            class="text-indigo-600 hover:text-indigo-900">{{ $product->product_id }}</a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $product->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $product->price }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $product->metadata['role'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" name="visible" class="toggle-product-visibility"
                                            data-product-id="{{ $product->product_id }}"
                                            {{ $product->visible ? 'checked' : '' }}>
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
        function showNotification(message, isError = false) {
            const notificationElement = document.getElementById('notification');
            notificationElement.textContent = message;
            notificationElement.classList.remove('bg-red-500', 'bg-green-500', 'opacity-0', '-translate-y-full');
            notificationElement.classList.add(isError ? 'bg-red-500' : 'bg-green-500', 'opacity-100', 'translate-y-0');

            setTimeout(() => {
                notificationElement.classList.remove('opacity-100', 'translate-y-0');
                notificationElement.classList.add('opacity-0', '-translate-y-full');
            }, 3000);
        }


        document.addEventListener('DOMContentLoaded', () => {
            const visibilityToggles = document.querySelectorAll('.toggle-product-visibility');

            visibilityToggles.forEach(toggle => {
                toggle.addEventListener('change', async (e) => {
                    const productId = e.target.dataset.productId;
                    const visibility = e.target.checked;
                    const urlTemplate =
                        `{{ route('products.setVisibility', ['product' => '_PRODUCT_ID_']) }}`;
                    const url = urlTemplate.replace('_PRODUCT_ID_', productId);

                    try {
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify({
                                visible: visibility,
                            }),
                        });

                        if (!response.ok) {
                            throw new Error('Failed to update product visibility');
                        }
                        showNotification(
                            'Product visibility updated successfully and DB sync done!');
                    } catch (error) {
                        console.error(error);
                        e.target.checked = !
                            visibility; // Revert the checkbox to its original state
                        showNotification(
                            'An error occurred while updating product visibility. Please try again.',
                            true);
                    }
                });
            });
        });
    </script>
@endsection
