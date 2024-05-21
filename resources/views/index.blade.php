<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-commerce</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="container">
        <h1>Products</h1>
        @if(session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
        @endif
        <div class="products">
            @foreach($products as $product)
            <div class="product">
                <h2>{{ $product->name }}</h2>
                <p>{{ $product->description }}</p>
                <p>${{ $product->price }}</p>

                <!-- Size Selection -->
                <label for="size-{{ $product->id }}">Size:</label>
                <select id="size-{{ $product->id }}" class="size">
                    <option value="M">M</option>
                    <option value="L">L</option>
                    <option value="XL">XL</option>
                </select>

                <!-- Color Selection -->
                <label for="color-{{ $product->id }}">Color:</label>
                <select id="color-{{ $product->id }}" class="color">
                    <option value="Red">Red</option>
                    <option value="White">White</option>
                </select>

                <button class="add-to-cart" data-id="{{ $product->id }}">Add to Cart</button>
            </div>
            @endforeach
        </div>
        <h1>Cart</h1>
        <div class="cart">
            @foreach($cart as $id => $item)
            <div class="cart-item" data-id="{{ $id }}">
                <h2>{{ $item['name'] }}</h2>
                <p>Size: {{ $item['size'] }}</p>
                <p>Color: {{ $item['color'] }}</p>
                <p>Quantity: {{ $item['quantity'] }}</p>
                <p>Price: ${{ $item['price'] }}</p>
                <button class="remove-from-cart" data-id="{{ $id }}">Delete</button>
            </div>
            @endforeach
        </div>

        <!-- Checkout Form -->
        <h2>Checkout</h2>
        <form method="POST" action="{{ route('checkout') }}">
            @csrf
            <div class="form-group">
                <label for="customer_name">Name:</label>
                <input type="text" name="customer_name" id="customer_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="customer_address">Address:</label>
                <textarea name="customer_address" id="customer_address" class="form-control" required></textarea>
            </div>
            <div class="form-group">
                <label for="phone_number">Phone Number:</label>
                <input type="text" name="phone_number" id="phone_number" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Place Order</button>
        </form>
        <br>
        <br>
        <br>
    </div>
    <script>
        $(document).ready(function () {
            $('.add-to-cart').click(function (e) {
                e.preventDefault();

                var productId = $(this).data('id');
                var size = $('#size-' + productId).val();
                var color = $('#color-' + productId).val();

                $.ajax({
                    url: '{{ route('cart.add') }}',
                    method: "POST",
                    data: {
                        _token: '{{ csrf_token() }}',
                        product_id: productId,
                        size: size,
                        color: color
                    },
                    success: function (response) {
                        alert(response.success);
                        updateCart();
                    }
                });
            });

            function updateCart() {
                $.ajax({
                    url: '{{ route('cart.get') }}',
                    method: "GET",
                    success: function (cart) {
                        console.log('cart', cart);
                        $('.cart').empty();
                        $.each(cart, function (id, item) {
                            $('.cart').append(
                                '<div class="cart-item" data-id="' + id + '">' +
                                '<h2>' + item.name + '</h2>' +
                                '<p>Size: ' + item.size + '</p>' +
                                '<p>Color: ' + item.color + '</p>' +
                                '<p>Quantity: ' + item.quantity + '</p>' +
                                '<p>Price: $' + item.price + '</p>' +
                                '</div>'
                            );
                        });
                    }
                });
            }

            // Remove from cart functionality
        $(document).on('click', '.remove-from-cart', function (e) {
            e.preventDefault();

            var cartKey = $(this).data('cart-key');

            $.ajax({
                url: '{{ route('cart.remove') }}',
                method: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    cart_key: cartKey
                },
                success: function (response) {
                    alert(response.success);
                    updateCart();
                }
            });
        });

        // Function to update the cart
        function updateCart() {
            $.ajax({
                url: '{{ route('cart.get') }}',
                method: "GET",
                success: function (cart) {
                    $('.cart').empty();
                    $.each(cart, function (cartKey, item) {
                        $('.cart').append(
                            '<div class="cart-item" data-cart-key="' + cartKey + '">' +
                            '<h2>' + item.name + '</h2>' +
                            '<p>Size: ' + item.size + '</p>' +
                            '<p>Color: ' + item.color + '</p>' +
                            '<p>Quantity: ' + item.quantity + '</p>' +
                            '<p>Price: $' + item.price + '</p>' +
                            '<button class="remove-from-cart" data-cart-key="' + cartKey + '">Remove</button>' +
                            '</div>'
                        );
                    });
                }
            });
        }

        });
    </script>
</body>

</html>