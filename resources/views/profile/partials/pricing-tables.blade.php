{{-- {{ dd(get_defined_vars()) }} --}}

{{-- TODO: gerar pricing baseada nesses dados

Each stripeProducts
"id" => 1
"product_id" => "prod_NeY8bWO6dCkW1G"
"active" => 1
"default_price" => "price_1MtF2SAabNZCbwviNnyTaVIw"
"description" => "30 dias de acesso ao Teses e Súmulas"
"image" => null
"metadata" => "{"role": "subscriber", "visible": "true", "period_number": "30", "period_frequency": "day"}"
"name" => "T&S Acesso Mensal"
"prices" => "[{"active": true, "currency": "brl", "nickname": "Preço Promocional", "price_id": "price_1Mtap4AabNZCbwviAjk9dS2l", "unit_amount": 1900}, {"active": true, "curr ▶"
"deleted_at" => null
"created_at" => "2023-04-05 18:34:12"
"updated_at" => "2023-04-10 13:44:03" --}}

<div class="min-h-screen flex items-center justify-center">
    <div class="relative py-3 sm:max-w-xl sm:mx-auto">
        <div
            class="absolute inset-0 bg-gradient-to-r from-cyan-400 to-light-blue-500 shadow-lg transform -skew-y-6 sm:skew-y-0 sm:-rotate-6 sm:rounded-3xl">
        </div>
        <div class="relative px-4 py-10 bg-white shadow-lg sm:rounded-3xl sm:p-20">
            <div class="max-w-md mx-auto">
                <button id="close-modal" class="absolute top-4 right-4 text-black font-semibold text-lg">X</button>
                <div class="flex space-x-12">

                    @foreach ($stripeProducts as $product)
                        @php
                            if ($product->metadata->visible == 'false') {
                                continue;
                            }
                        @endphp
                        <div class="w-1/2">
                            <h1 class="text-2xl font-semibold">{{ $product->name }}</h1>
                            <p class="mt-2 text-gray-600">{{ $product->description }}</p>
                            <div class="mt-4 flex items-baseline">
                                <span class="text-4xl font-bold">{{ $product->formatted_price }}</span>
                                <span class="text-gray-600 ml-2">/{{ $product->period }}</span>
                            </div>
                            <ul class="mt-6 space-y-4">
                                {{-- <li>1 User Account</li>
                                <li>10GB Storage</li>
                                <li>Email Support</li> --}}
                            </ul>
                            <button class="mt-8 px-6 py-2 text-white bg-blue-500 hover:bg-blue-600 rounded-md">
                                {{ __('Choose Plan') }}
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
