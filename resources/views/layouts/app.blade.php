<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Perpustakaan - @yield('title')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    @yield('additional_css')
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f6fa;
        }

        /* Sidebar styles dari sebelumnya tetap sama */
        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #2c3e50;
            color: white;
            position: fixed;
            left: 0;
            top: 0;
            padding: 20px 0;
            z-index: 1000;
        }

        /* Content wrapper */
        .content-wrapper {
            margin-left: 250px; /* Sesuai dengan lebar sidebar */
            padding: 30px;
            min-height: 100vh;
        }

        /* Header content */
        .content-header {
            margin-bottom: 30px;
        }

        .content-header h1 {
            color: #2c3e50;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        /* Card style */
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }

        /* Table styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }

        thead {
            background-color: #2c3e50;
            color: white;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        tbody tr:hover {
            background-color: #f8f9fa;
        }

        /* Button styles */
        .btn {
            padding: 8px 15px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: #3498db;
            color: white;
        }

        .btn-success {
            background-color: #2ecc71;
            color: white;
        }

        .btn-danger {
            background-color: #e74c3c;
            color: white;
        }

        .btn-warning {
            background-color: #f1c40f;
            color: white;
        }

        .btn:hover {
            opacity: 0.9;
        }

        /* Form styles */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #2c3e50;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .form-control:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 5px rgba(52,152,219,0.3);
        }

        /* Search container */
        .search-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .search-group {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .search-input {
            flex: 1;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1050;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 20px;
            border-radius: 8px;
            width: 500px;
            max-width: 90%;
            position: relative;
        }

        .modal-header {
            margin-bottom: 20px;
        }

        .modal-title {
            color: #2c3e50;
            font-size: 20px;
            font-weight: 600;
        }

        .close-modal {
            position: absolute;
            right: 20px;
            top: 20px;
            font-size: 20px;
            cursor: pointer;
            color: #666;
        }

        /* Alert styles */
        .alert {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .sidebar {
                width: 0;
                transform: translateX(-100%);
                transition: all 0.3s ease;
            }

            .content-wrapper {
                margin-left: 0;
            }

            .sidebar.active {
                width: 250px;
                transform: translateX(0);
            }

            .search-group {
                flex-direction: column;
            }

            .search-input {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    @include('layouts.sidebar')
    
    <div class="content-wrapper">
        @yield('content')
    </div>

    @yield('scripts')
</body>
</html> 