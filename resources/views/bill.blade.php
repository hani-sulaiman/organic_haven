<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Bill</title>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f4f4f9;
    }
    .bill-container {
      max-width: 800px;
      margin: 20px auto;
      background-color: #ffffff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .bill-header {
      text-align: center;
      margin-bottom: 20px;
    }
    .bill-header h1 {
      margin: 0;
      font-size: 2em;
      color: #333;
    }
    .bill-details {
      margin-bottom: 20px;
    }
    .bill-details p {
      margin: 5px 0;
      font-size: 1em;
      color: #555;
    }
    .bill-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }
    .bill-table th, .bill-table td {
      border: 1px solid #ddd;
      padding: 12px;
      text-align: left;
    }
    .bill-table th {
      background-color: #f8f9fa;
      font-weight: bold;
    }
    .bill-table tr:nth-child(even) {
      background-color: #f9f9f9;
    }
    .bill-total {
      text-align: right;
      font-size: 1.2em;
      color: #333;
    }
    .bill-total strong {
      font-size: 1.5em;
    }
  </style>
</head>
<body>
  <div class="bill-container">
    <div class="bill-header">
      <h1>Order Bill</h1>
      <p>Order #{{ $order->id }}</p>
    </div>
    <div class="bill-details">
      <p><strong>User:</strong> {{ $order->user->name }}</p>
      <p><strong>Email:</strong> {{ $order->user->email }}</p>
      <p><strong>Status:</strong> {{ $order->status }}</p>
      <p><strong>Total:</strong> ${{ number_format($order->total, 2) }}</p>
      <p><strong>Created At:</strong> {{ $order->created_at }}</p>
    </div>
    <table class="bill-table">
      <thead>
        <tr>
          <th>Product</th>
          <th>Quantity</th>
          <th>Price</th>
          <th>Total</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($order->order_items as $item)
          <tr>
            <td>{{ $item->product->title }}</td>
            <td>{{ $item->quantity }}</td>
            <td>${{ number_format($item->price, 2) }}</td>
            <td>${{ number_format($item->total, 2) }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
    <div class="bill-total">
      <p><strong>Grand Total:</strong> ${{ number_format($order->total, 2) }}</p>
    </div>
  </div>
</body>
</html>