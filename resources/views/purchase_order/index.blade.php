@extends('layouts.app')

@section('title', 'Purchase Orders')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">Purchase Orders</h1>

    <!-- Sorting Options -->
    <div class="mb-3">
        <form method="GET" action="{{ route('purchase_order.index') }}">
            <div class="form-group">
                <label for="sortOrder">Sort by Date:</label>
                <select name="sort" id="sortOrder" class="form-control" onchange="this.form.submit()">
                    <option value="desc" {{ $sortOrder === 'desc' ? 'selected' : '' }}>Latest First</option>
                    <option value="asc" {{ $sortOrder === 'asc' ? 'selected' : '' }}>Oldest First</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-header">
            Purchase Orders
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>PO Number</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchaseOrders as $order)
                        <tr onclick="window.location='{{ route('purchase_order.show', $order->purchase_order_id) }}'" style="cursor: pointer;">
                            <td>{{ $order->po_number }}</td>
                            <td>{{ \Carbon\Carbon::parse($order->date)->format('Y-m-d') }}</td>
                            <td>{{ $order->status }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">No purchase orders found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination Links -->
            <div class="pagination-wrapper">
                {{ $purchaseOrders->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>
@endsection
