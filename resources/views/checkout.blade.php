<x-app-layout>
    <x-slot name="style">
        <link rel="stylesheet" href="{{ asset('css/checkout.css') }}">
    </x-slot>

    <x-slot name="js">
        <script src="{{ asset('js/checkout.js') }}" defer></script>
    </x-slot>
    {{--------------------- 
            $slot 
        --------------------}}
            <main  class="_container">
                <div class="checkout_order">
                    <div class="checkout">
                        <h1>Checkout</h1>
                        @if ($errors->any())
                            @foreach ($errors->all() as $error)
                            <p style="color: red">{{ $error }}</p>
                            @endforeach
                        @else
                            <strong style="cursor: pointer;" onclick="fillForm(this)">Fill My Details -></strong>
                        @endif
                        <form action="{{ route('checkout') }}" method="post">
                            @csrf
                            <h2>Billing Details</h2>
                            <div class="form_group">
                                <label for="first_name">First Name</label>
                                <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" required>
                            </div>
                            <div class="form_group">
                                <label for="last_name">Last Name</label>
                                <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}" required>
                            </div>
                            <div class="form_group">
                                <label for="email">Email Address</label>
                                <input id="email" type="email" name="email" value="{{ old('email') }}" required>
                            </div>
                            <div class="form_group">
                                <label for="address">Address Line</label>
                                <input id="address" type="text" name="address_line" value="{{ old('address_line') }}" required>
                            </div>
                            <div class="two_form">
                                <div class="form_group">
                                    <label for="city">City</label>
                                    <input id="city" type="text" name="city" value="{{ old('city') }}" required>
                                </div>
                                <div class="form_group">
                                    <label for="postal_code">Postal Code</label>
                                    <input id="postal_code" type="text" name="postal_code" value="{{ old('postal_code') }}" required>
                                </div>
                            </div>
                            <div class="two_form">
                                <div class="form_group">
                                    <label for="country">Country</label>
                                    <input id="country" type="text" name="country" value="{{ old('country') }}" required>
                                </div>
                                <div class="form_group">
                                    <label for="mobile">Mobile No.</label>
                                    <input id="mobile" type="text" name="mobile" value="{{ old('mobile') }}" required>
                                </div>
                            </div>
                            <h2>Payment Details</h2>
                            <input type="checkbox" name="cod" value="1" id="cod">
                            <label for="cod">Cash On Delivery</label>

                            <button type="submit">Order Now</button>

                            <!-- paypal button -->
                            <h3>Or Pay with PayPal</h3>
                            <div id="paypal-button-container"></div>

                            <script src="https://www.paypal.com/sdk/js?client-id=AZhh_2j-wWj86ONFGcuekRyDn_KkjhgbWWw5Wq9ZQKACqCZQDdVc_mOsiXOhQin2PkEPE8aftoyL1sqJ&currency=USD"></script>

                        <script>
                        paypal.Buttons({
                            async createOrder() {
                            const response = await fetch("/create-paypal-order", {
                                method: "POST",
                                headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                                }
                            });
                            const order = await response.json();
                            return order.id; // phải return order.id
                            },

                            async onApprove(data) {
                            const response = await fetch("/capture-paypal-order", {
                                method: "POST",
                                headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify({ orderID: data.orderID })
                            });

                            const details = await response.json();
                            if (details.status === "COMPLETED") {
                                alert(`Payment successful by ${details.payer.name.given_name}`);
                            } else {
                                alert("Payment not completed");
                            }
                            },

                            onError(err) {
                            console.error(err);
                            alert("PayPal error occurred");
                            }
                        }).render("#paypal-button-container");
                        </script>
                            <!-- end paypal button -->
                        </form>

                    </div>
                    <div class="order">
                        <div class="card">
                            <h2>Your Order</h2>
                            <div class="flex_align">
                                <strong>Sub Total</strong>
                                <span>${{ $subTotal }}</span>
                            </div>
                            <div class="flex_align">
                                <strong>Discount</strong>
                                <span>$0</span>
                            </div>
                            <div class="flex_align">
                                <strong>Tax</strong>
                                <span>$0</span>
                            </div>
                            <div class="flex_align">
                                <strong>Shipping Cost</strong>
                                <span>$0</span>
                            </div>
                            <div class="flex_align">
                                <h3>Total Cost</h3>
                                <h3>${{ $subTotal }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
    {{--------------------- 
        $slot 
    --------------------}}
</x-app-layout>