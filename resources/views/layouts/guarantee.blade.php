<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="MySonar - Finalisation de votre demande de financement">
    <title>MySonar | @yield('title', 'Paiement des frais')</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.kkiapay.me/k.js"></script>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: #f5f7fb;
            color: #1a1f36;
            line-height: 1.5;
        }
        
        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        /* Header */
        .header {
            text-align: center;
            padding: 2rem 0;
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 2rem;
        }
        
        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1a56db;
            letter-spacing: -0.5px;
        }
        
        .logo span {
            color: #0ea5e9;
        }
        
        /* Cards */
        .card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e5e7eb;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            background: #ffffff;
        }
        
        .card-header h2 {
            font-size: 1.125rem;
            font-weight: 600;
            color: #1a1f36;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        /* Grid */
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }
        
        /* Info rows */
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f0f2f5;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            color: #6b7280;
            font-size: 0.875rem;
        }
        
        .info-value {
            font-weight: 500;
            color: #1a1f36;
        }
        
        .info-value.amount {
            font-size: 1.25rem;
            font-weight: 700;
            color: #10b981;
        }
        
        /* Fee box */
        .fee-box {
            background: #fef2f2;
            border-radius: 12px;
            padding: 1.25rem;
            text-align: center;
            margin-top: 1rem;
        }
        
        .fee-label {
            color: #dc2626;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .fee-amount {
            font-size: 2rem;
            font-weight: 700;
            color: #dc2626;
        }
        
        .fee-sub {
            font-size: 0.75rem;
            color: #ef4444;
            margin-top: 0.5rem;
        }
        
        /* Button */
        .btn {
            width: 100%;
            padding: 0.875rem;
            background: #1a56db;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s;
        }
        
        .btn:hover {
            background: #1e40af;
        }
        
        .btn:disabled {
            background: #9ca3af;
            cursor: not-allowed;
        }
        
        /* Testimonials */
        .testimonial-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
        }
        
        .testimonial {
            background: #f9fafb;
            border-radius: 12px;
            padding: 1.25rem;
            border: 1px solid #e5e7eb;
        }
        
        .testimonial-text {
            font-size: 0.875rem;
            color: #4b5563;
            line-height: 1.6;
            margin-bottom: 1rem;
        }
        
        .testimonial-author {
            font-weight: 600;
            font-size: 0.875rem;
            color: #1a1f36;
        }
        
        .testimonial-date {
            font-size: 0.75rem;
            color: #9ca3af;
            margin-top: 0.25rem;
        }
        
        .payment-received {
            color: #10b981;
            font-size: 0.75rem;
            font-weight: 500;
            margin-top: 0.5rem;
        }
        
        /* Stats */
        .stats {
            background: #f9fafb;
            border-radius: 12px;
            padding: 1.5rem;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            text-align: center;
            margin-top: 1.5rem;
        }
        
        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1a56db;
        }
        
        .stat-label {
            font-size: 0.75rem;
            color: #6b7280;
            margin-top: 0.25rem;
        }
        
        /* Contact */
        .contact-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            text-align: center;
        }
        
        .contact-item {
            padding: 1rem;
            background: #f9fafb;
            border-radius: 8px;
        }
        
        .contact-item i {
            font-size: 1.25rem;
            color: #1a56db;
            margin-bottom: 0.5rem;
        }
        
        .contact-item h4 {
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .contact-item p {
            font-size: 0.75rem;
            color: #6b7280;
        }
        
        /* Alert */
        .alert {
            padding: 0.75rem 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-size: 0.875rem;
        }
        
        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
        }
        
        .notice {
            background: #fffbeb;
            border-left: 3px solid #f59e0b;
            padding: 0.75rem 1rem;
            font-size: 0.75rem;
            color: #92400e;
            margin-bottom: 1rem;
        }
        
        /* Footer */
        .footer {
            text-align: center;
            padding: 2rem 0;
            border-top: 1px solid #e5e7eb;
            margin-top: 2rem;
            font-size: 0.75rem;
            color: #9ca3af;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }
            
            .grid-2 {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .testimonial-grid {
                grid-template-columns: 1fr;
            }
            
            .stats {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }
            
            .contact-grid {
                grid-template-columns: 1fr;
            }
        }
        
        .spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.6s linear infinite;
            margin-right: 0.5rem;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .hidden {
            display: none;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">MySonar<span>Assurance</span></div>
        </div>
        
        @yield('content')
        
        <div class="footer">
            MySonar Assurance &copy; 2026 - Paiement sécurisé par KKiaPay
        </div>
    </div>
    
    @stack('scripts')
</body>
</html>