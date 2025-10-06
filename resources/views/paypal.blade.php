<!DOCTYPE html>
<html>
  <head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
  </head>
  <body>
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
  </body>
</html>
