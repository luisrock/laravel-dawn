{{-- TODO: show visible products --}}
{{-- {{ dd(get_defined_vars()) }} --}}

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Status') }}
        </h2>

        <p id="user-status-info" class="mt-1 text-sm text-gray-600 dark:text-gray-400">
        <p>{{ __('You are a') }}<span class="text-blue-800 dark:text-dark-400">
                @role('subscriber')
                    {{ __(' subscriber') }}</span>.
                {{ __(' Enjoy your subscription!') }}</p>
        @endrole
        @role('premium')
            {{ __(' premium subscriber') }}</span>.
            {{ __(" Wow, that's big!") }}</p>
        @endrole
        @role('admin')
            {{ __(' admin') }}</span>.
            {{ __('You can do it all!') }}</p>
        @endrole
        @role('superadmin')
            {{ __(' superadmin') }}</span>.
            {{ __('Hi, big boss!') }}</p>
        @endrole
        @role('registered')
            {{ __(' registered user') }}</span>.
            {{ __('Become a subscriber!') }}</p>
        @endrole
        </p>
    </header>

    {{-- if config.services.stripe.enabled -- }}

    {{-- has any roles --}}

    @if (config('services.stripe.enabled'))
        @hasanyrole('subscriber|premium')
            <div id="subscription-info"></div>
        @endhasanyrole

        @role('registered')
            <div id="payment-button-container">
                {{-- <button id="stripe-button" class="inline-flex items-center mt-5 px-4 py-2 bg-blue-800 dark:bg-grey-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
            {{ __('Comprar') }}
        </button> --}}
                <button id="open-modal"
                    class="inline-flex items-center mt-5 px-4 py-2 bg-blue-800 dark:bg-grey-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    {{ __('Choose Plan') }}
                </button>
            </div>
        @endrole


        {{-- modal with cards --}}

        <div class="modal hidden fixed z-10 inset-0 overflow-y-auto bg-black bg-opacity-50" aria-labelledby="modal-title"
            role="dialog" aria-modal="true">
            @include('profile.partials.pricing-tables')
        </div>
        {{-- //todo: js and improve the modal --}}





        @if (session('status'))
            <div
                class="rounded-md p-4 mt-6 {{ session('status_type') === 'payment_successful' ? 'bg-green-50 dark:bg-green-800' : 'bg-red-50 dark:bg-red-800' }}">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 {{ session('status_type') === 'payment_successful' ? 'text-green-400 dark:text-green-200' : 'text-red-400 dark:text-red-200' }}"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                            aria-hidden="true">
                            @if (session('status_type') === 'payment_successful')
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            @else
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l6.342 11.25A1.75 1.75 0 0117.495 18H2.506a1.75 1.75 0 01-1.589-2.651L7.26 3.1zm.993 12.902a1 1 0 11-2 0 1 1 0 012 0zm-.752-5.047l.006-.073V7.003a1 1 0 10-2 0v3.028a1.001 1.001 0 001.994.038z"
                                    clip-rule="evenodd" />
                            @endif
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium {{ session('status_type') === 'payment_successful' ? 'text-green-800 dark:text-green-200' : 'text-red-800 dark:text-red-200' }}"
                            id="payment-status-text">
                            {{ session('status') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif
    @endif

    <div class="mt-6 flex justify-end">
        <a href="{{ route('contact') }}"
            class="text-sm text-blue-800 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300">
            {{ __('Contact') }}
        </a>
    </div>
</section>

@if (config('services.stripe.enabled'))
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        //TODO: na volta do pagamento com cartão bem-sucedido, o usuário está vendo o "Payment successful! You are now a subscriber.", 
        //mas também vê 'You are a registered user. Become a subscriber!' e o botão de comprar. 
        //Só depois de atualizar a página que o botão some e o texto muda para 'You are a subscriber. Enjoy your subscription!'

        //if {{ session('status_type') === 'payment_successful' }}, remove the button and change the text
        if ("{{ session('status_type') }}" === 'payment_successful') {
            if (document.getElementById('payment-button-container')) {
                document.getElementById('payment-button-container').remove();
            }

            if (document.getElementById('payment-status-text')) {
                document.getElementById('payment-status-text').innerHTML +=
                    " {{ __('Reloading the page in 5 seconds...') }}";
            }

            //reload the page after 3 seconds
            setTimeout(function() {
                location.reload();
            }, 5000);
        }


        const stripe = Stripe('{{ config('services.stripe.key') }}');

        async function checkPendingBoleto() {
            const urlWithUserId = '/get-user-payment-info-from-db/{{ Auth::user()->id }}';
            const response = await fetch(urlWithUserId, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
            });

            const data = await response.json();
            console.log(data);

            if (!data.error && data.payment_method === 'boleto' && data.status !== 'paid') {
                const currentDate = new Date();
                const expirationDate = new Date(data.expiration_date);
                //get locale from config
                const locale = "{{ config('app.locale') }}";
                //format date to locale
                const formattedExpirationDate = expirationDate.toLocaleDateString(locale);


                if (expirationDate > currentDate) {
                    document.getElementById('payment-button-container').innerHTML = `
                    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg mt-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Boleto Emitted!') }}</h3> 
                        <span class="block text-sm font-medium text-gray-800 dark:text-gray-500">{{ __('Hey! You have a valid boleto to be paid. Here is the barcode:') }}</span>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-200">${data.barcode}</p>
                        <p class="mt-3 text-sm text-gray-900 dark:text-gray-200">* {{ __("If you already paid it, just wait  a little bit. Soon we'll be informed and let you know.") }}</p>
                        
                        <div class="grid grid-cols-1 mt-10 gap-4 md:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-800 dark:text-gray-500">{{ __('VOUCHER LINK') }}</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-200"><a href="${data.voucher_url}">{{ __('click here') }}</a></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-800 dark:text-gray-500">{{ __('EXPIRATION DATE') }}</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-200">${formattedExpirationDate}</p>
                            </div>
                        </div>
                    </div>`;
                }
            }
        }
        @role('registered')
            checkPendingBoleto();
        @endrole

        document.getElementById('open-modal').addEventListener('click', () => {
            const modal = document.querySelector('.modal');
            modal.classList.remove('hidden');
        });

        document.getElementById('close-modal').addEventListener('click', () => {
            const modal = document.querySelector('.modal');
            modal.classList.add('hidden');
        });

        document.querySelector('.modal').addEventListener('click', (event) => {
            if (event.target === event.currentTarget) {
                const modal = document.querySelector('.modal');
                modal.classList.add('hidden');
            }
        });


        @hasanyrole('subscriber|premium')
            async function checkSubscription() {
                const urlWithUserId = '/get-user-payment-info-from-db/{{ Auth::user()->id }}';
                const response = await fetch(urlWithUserId, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                });

                const data = await response.json();

                if (data.error) {
                    document.getElementById('subscription-info').textContent = data.error;
                } else {
                    const subscriptionEndsAt = "{{ Auth::user()->subscription_ends_at }}";
                    const currentDate = new Date();
                    const subscriptionEndDate = new Date(subscriptionEndsAt);

                    if (subscriptionEndDate > currentDate) {
                        const paidDate = new Date(data.created);
                        const formattedPaidDate =
                            `${paidDate.getDate().toString().padStart(2, '0')}/${(paidDate.getMonth() + 1).toString().padStart(2, '0')}/${paidDate.getFullYear()}`;
                        const formattedExpirationDate =
                            `${subscriptionEndDate.getDate().toString().padStart(2, '0')}/${(subscriptionEndDate.getMonth() + 1).toString().padStart(2, '0')}/${subscriptionEndDate.getFullYear()}`;

                        let paymentMethod = data.payment_method;
                        let cardInfo = '';

                        if (paymentMethod === 'card') {
                            paymentMethod = `
                    <div>
                        <label class="block text-sm font-medium text-gray-800 dark:text-gray-500">{{ __('CARD') }}</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-200">${data.brand} ...${data.last_digits}</p>
                    </div>`;
                        } else {
                            paymentMethod = `
                    <div>
                        <label class="block text-sm font-medium text-gray-800 dark:text-gray-500">{{ __('PAYMENT METHOD') }}</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-200">{{ __('Boleto') }}</p>
                    </div>`;
                        }

                        document.getElementById('subscription-info').innerHTML = `                
                    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg mt-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Subscription Details') }}</h3>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-800 dark:text-gray-500">{{ __('DATE') }}</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-200">${formattedPaidDate}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-800 dark:text-gray-500">{{ __('AMOUNT - RECEIPT') }}</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-200">
                                    ${data.amount} ${data.currency} 
                                    <a href="${data.receipt_url}" target="_blank" title="{{ __('get your receipt') }}" class="underline text-green-600 hover:text-green-500 ml-5 ">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-5 w-5 inline">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                    </a>
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-800 dark:text-gray-500">{{ __('EXPIRATION DATE') }}</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-200">${formattedExpirationDate}</p>
                            </div>
                            ${paymentMethod}
                        </div>
                    </div>`;
                    }
                }
            }
            checkSubscription();
        @endhasanyrole

        @role('registered')
            document.getElementById('stripe-button').addEventListener('click', async () => {
                const response = await fetch('/create-checkout-session', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                });

                const data = await response.json();
                const result = await stripe.redirectToCheckout({
                    sessionId: data.id,
                });

                if (result.error) {
                    console.error(result.error.message);
                }
            });
        @endrole
    </script>
@endif
