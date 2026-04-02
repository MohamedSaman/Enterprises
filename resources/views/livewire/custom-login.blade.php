    <div class="login-container">
        @php
        $shopPhoneForWa = preg_replace('/\D+/', '', config('shop.whatsapp'));
        @endphp

        <!-- Full-screen background -->
        <div class="background-image"></div>

        <!-- Split card -->
        <div class="login-card">

            <!-- ===== RIGHT: Form panel ===== -->
            <div class="form-panel">
                <div class="module-tabs" role="tablist" aria-label="System modules">
                    <button type="button" wire:click="setModule('invontery')" class="module-tab {{ $selectedModule === 'invontery' ? 'active' : '' }}" role="tab" aria-selected="{{ $selectedModule === 'invontery' ? 'true' : 'false' }}">Invontery</button>
                    <button type="button" wire:click="setModule('production')" class="module-tab {{ $selectedModule === 'production' ? 'active' : '' }}" role="tab" aria-selected="{{ $selectedModule === 'production' ? 'true' : 'false' }}">Production</button>
                </div>

                <div class="panel-heading">
                    <div class="user-ring" style="background:var(--sys-primary-soft);border:2px solid var(--sys-primary);">
                        <i class="bi bi-person-fill" style="color:var(--sys-primary);"></i>
                    </div>
                    <h2 style="color:var(--sys-primary);">Welcome Back</h2>
                    <p style="color:#6b7280;">Sign in to your account to continue</p>
                </div>

                <form wire:submit.prevent="login">

                    <!-- Email field -->
                    <div class="input-wrap">
                        <i class="bi bi-envelope input-icon"></i>
                        <input type="email"
                            class="form-control {{ $errors->has('email') ? 'is-invalid shake' : '' }}"
                            wire:model="email"
                            placeholder="Enter Email"
                            required
                            aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}">
                        @error('email')
                        <div class="invalid-feedback d-block" style="padding-left:4px;font-size:0.82rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password field -->
                    <div class="input-wrap">
                        <i class="bi bi-lock input-icon"></i>
                        <input type="password"
                            class="form-control {{ $errors->has('password') ? 'is-invalid shake' : '' }}"
                            wire:model="password"
                            placeholder="Enter Password"
                            required
                            aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}">
                        @error('password')
                        <div class="invalid-feedback d-block" style="padding-left:4px;font-size:0.82rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Remember & Forgot options -->
                    <div class="form-options">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" wire:model="remember" id="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                        @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-link">Forgot Password?</a>
                        @endif
                    </div>

                    <!-- Login button -->
                    <button type="submit" class="login-btn">Login</button>

                    <div class="login-contact-block">
                        <p class="contact-address">
                            <i class="bi bi-geo-alt-fill"></i>
                            {{ config('shop.address') }}
                        </p>
                        <div class="contact-icons">
                            <a href="tel:{{ config('shop.phone') }}" class="contact-icon phone" title="Call us">
                                <i class="bi bi-telephone-fill"></i>
                            </a>
                            <a href="mailto:{{ config('shop.email') }}" class="contact-icon email" title="Email us">
                                <i class="bi bi-envelope-fill"></i>
                            </a>
                            <a href="https://api.whatsapp.com/send/?phone={{ $shopPhoneForWa }}&text=Hi%2C+I%27m+interested+in+your+bathware+products.&type=phone_number&app_absent=0"
                                target="_blank" rel="noopener noreferrer"
                                class="contact-icon whatsapp" title="WhatsApp us">
                                <i class="bi bi-whatsapp"></i>
                            </a>
                        </div>
                    </div>

                    <style>
                        html,
                        body {
                            height: 100%;
                            margin: 0;
                            overflow: hidden;
                        }

                        .module-tab {
                            background: none;
                            border: none;
                            padding: 0;
                            cursor: pointer;
                        }

                        .login-container {
                            --sys-primary: #0b6e79;
                            --sys-primary-soft: #d9eef1;
                            --sys-accent: #155e75;
                            --sys-muted: #5f6f80;
                            --sys-border: #d2e1e6;
                            height: 100vh;
                            width: 100%;
                            overflow: hidden;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            padding: 10px;
                            box-sizing: border-box;
                        }

                        .login-container * {
                            box-sizing: border-box;
                        }

                        .background-image {
                            position: fixed;
                            inset: 0;
                            width: 100%;
                            height: 100%;
                            overflow: hidden;
                        }

                        .login-card {
                            grid-template-columns: 1fr !important;
                            width: min(100%, 430px);
                            max-width: 430px;
                            max-height: calc(100vh - 20px);
                            margin: 0;
                            border-radius: 22px;
                            overflow: hidden;
                        }

                        .login-card .form-panel {
                            width: 100%;
                            max-width: 100%;
                            padding: 16px;
                        }

                        .form-panel form {
                            max-width: 100%;
                            margin: 0 auto;
                        }

                        .panel-heading {
                            text-align: center;
                            margin-bottom: 14px;
                        }

                        .panel-heading .user-ring {
                            margin: 0 auto 10px;
                        }

                        .panel-heading h2 {
                            margin-bottom: 4px;
                        }

                        .panel-heading p {
                            margin-bottom: 12px;
                        }

                        .module-tabs {
                            display: flex;
                            gap: 10px;
                            margin: 0 auto 18px;
                            max-width: 100%;
                            padding: 6px;
                            border-radius: 14px;
                            background: #edf4f6;
                            border: 1px solid var(--sys-border);
                        }

                        .module-tab {
                            position: relative;
                            overflow: hidden;
                            flex: 1;
                            text-align: center;
                            padding: 10px 12px;
                            border-radius: 10px;
                            font-weight: 700;
                            text-decoration: none;
                            color: #4f6072;
                            border: 1px solid transparent;
                            transition: transform .22s ease, color .22s ease, border-color .22s ease, box-shadow .22s ease;
                            transform: translateY(0);
                        }

                        .module-tab::before {
                            content: '';
                            position: absolute;
                            top: 0;
                            left: 0;
                            width: 100%;
                            height: 100%;
                            background: rgba(255, 255, 255, .25);
                            opacity: 0;
                            transform: scaleX(.7);
                            transition: opacity .3s ease, transform .3s ease;
                        }

                        .module-tab.active {
                            background: #fff;
                            color: var(--sys-primary);
                            border-color: rgba(18, 150, 165, 0.25);
                            box-shadow: 0 6px 16px rgba(18, 150, 165, 0.16);
                            animation: activeTabPulse .45s ease;
                        }

                        .module-tab.active::before {
                            opacity: 1;
                            transform: scaleX(1);
                        }

                        .module-tab:not(.active):hover {
                            color: var(--sys-primary);
                            border-color: var(--sys-border);
                            transform: translateY(-1px);
                        }

                        @keyframes activeTabPulse {
                            0% {
                                transform: scale(0.96);
                            }

                            100% {
                                transform: scale(1);
                            }
                        }

                        .login-btn {
                            background: var(--sys-primary);
                            border: none;
                            color: #fff;
                            font-weight: 700;
                            box-shadow: 0 8px 20px rgba(11, 110, 121, 0.28);
                            width: 100%;
                            margin-top: 8px;
                        }

                        .login-btn:hover {
                            background: #095b64;
                            filter: none;
                        }

                        @media (max-width: 768px) {
                            .login-card {
                                width: min(100%, 430px);
                                max-height: calc(100vh - 16px);
                            }

                            .login-card .form-panel {
                                padding: 14px 12px;
                            }

                            .module-tabs,
                            .form-panel form {
                                max-width: 100%;
                            }
                        }

                        .forgot-link {
                            color: var(--sys-primary);
                            font-weight: 600;
                        }

                        .input-wrap .form-control:focus {
                            border-color: var(--sys-primary);
                            box-shadow: 0 0 0 3px rgba(18, 150, 165, 0.18);
                        }

                        .login-contact-block {
                            margin: 14px auto 0;
                            max-width: 100%;
                            padding-top: 12px;
                            border-top: 1px dashed #d7e1e4;
                            text-align: center;
                        }

                        .contact-address {
                            margin: 0 0 10px;
                            color: var(--sys-muted);
                            font-size: .9rem;
                        }

                        .contact-address i {
                            color: var(--sys-primary);
                            margin-right: 6px;
                        }

                        .contact-icons {
                            display: flex;
                            justify-content: center;
                            gap: 8px;
                        }

                        .contact-icon {
                            width: 34px;
                            height: 34px;
                            border-radius: 50%;
                            display: inline-flex;
                            align-items: center;
                            justify-content: center;
                            color: #fff;
                            text-decoration: none;
                            transition: transform .2s ease, filter .2s ease;
                        }

                        .contact-icon:hover {
                            transform: translateY(-1px) scale(1.04);
                            filter: brightness(1.05);
                        }

                        .contact-icon.phone {
                            background: var(--sys-primary);
                        }

                        .contact-icon.email {
                            background: var(--sys-accent);
                        }

                        .contact-icon.whatsapp {
                            background: #25d366;
                        }

                        /* Visual override layer to match the provided login style */
                        html,
                        body {
                            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
                            background:
                                radial-gradient(circle at top left, rgba(14, 165, 233, 0.14), transparent 34%),
                                radial-gradient(circle at bottom right, rgba(212, 166, 61, 0.10), transparent 30%),
                                linear-gradient(180deg, #f7fbfe 0%, #eef4f8 100%);
                            color: #1f2937;
                        }

                        body::before {
                            content: '';
                            position: fixed;
                            inset: 0;
                            pointer-events: none;
                            background: linear-gradient(135deg, rgba(255, 255, 255, 0.72), rgba(255, 255, 255, 0.24));
                            opacity: 0.05;
                        }

                        body::after {
                            content: '';
                            position: fixed;
                            inset: 0;
                            pointer-events: none;
                            background: linear-gradient(120deg, rgba(255, 255, 255, 0.28), transparent 35%, rgba(255, 255, 255, 0.12));
                        }

                        .login-container {
                            --sys-primary: #0f7bb3;
                            --sys-primary-soft: #dceff9;
                            --sys-accent: #0b5f8a;
                            --sys-muted: #5f6f80;
                            --sys-border: #dde8f0;
                            --sys-surface: rgba(255, 255, 255, 0.88);
                            --sys-surface-strong: #ffffff;
                            padding: 18px;
                        }

                        .background-image {
                            background:
                                radial-gradient(circle at 15% 20%, rgba(14, 165, 233, 0.15), transparent 20%),
                                radial-gradient(circle at 84% 18%, rgba(212, 166, 61, 0.14), transparent 18%),
                                radial-gradient(circle at 74% 80%, rgba(15, 118, 110, 0.10), transparent 20%),
                                linear-gradient(180deg, #f9fcfe 0%, #eef4f8 100%);
                        }

                        .login-card {
                            position: relative;
                            z-index: 1;
                            border-radius: 26px;
                            overflow: hidden;
                            background: var(--sys-surface);
                            border: 1px solid rgba(255, 255, 255, 0.72);
                            box-shadow: 0 24px 70px rgba(15, 23, 42, 0.14), 0 4px 14px rgba(15, 23, 42, 0.08);
                            backdrop-filter: blur(16px);
                            -webkit-backdrop-filter: blur(16px);
                            animation: cardFloatIn 0.7s ease both;
                        }

                        .login-card .form-panel {
                            padding: 26px 24px 22px;
                        }

                        .panel-heading {
                            margin-bottom: 16px;
                        }

                        .panel-heading .user-ring {
                            width: 62px;
                            height: 62px;
                            border-radius: 50%;
                            box-shadow: 0 10px 24px rgba(15, 123, 179, 0.18);
                        }

                        .panel-heading h2 {
                            font-size: 1.95rem;
                            font-weight: 800;
                            letter-spacing: -0.04em;
                            color: #1f2937 !important;
                        }

                        .panel-heading p {
                            color: #64748b !important;
                            font-size: 0.96rem;
                        }

                        .module-tabs {
                            gap: 8px;
                            padding: 6px;
                            border-radius: 16px;
                            background: rgba(241, 247, 251, 0.95);
                            border: 1px solid var(--sys-border);
                        }

                        .module-tab {
                            border-radius: 12px;
                            font-weight: 700;
                            color: #4f6072;
                            transition: transform .22s ease, color .22s ease, border-color .22s ease, box-shadow .22s ease, background-color .22s ease;
                        }

                        .module-tab.active {
                            background: var(--sys-surface-strong);
                            color: var(--sys-primary);
                            border-color: rgba(15, 123, 179, 0.22);
                            box-shadow: 0 8px 20px rgba(15, 123, 179, 0.16);
                        }

                        .module-tab:not(.active):hover {
                            color: var(--sys-primary);
                            border-color: var(--sys-border);
                            transform: translateY(-1px);
                        }

                        .input-wrap {
                            position: relative;
                            margin-bottom: 14px;
                        }

                        .input-wrap .input-icon {
                            position: absolute;
                            left: 14px;
                            top: 50%;
                            transform: translateY(-50%);
                            color: #94a3b8;
                            z-index: 2;
                            pointer-events: none;
                        }

                        .input-wrap .form-control {
                            height: 50px;
                            border-radius: 12px;
                            border: 1px solid #d9e4ec;
                            background: #f7fafe;
                            padding-left: 42px;
                            padding-right: 14px;
                            color: #1f2937;
                            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.8);
                            transition: border-color .2s ease, box-shadow .2s ease, background-color .2s ease;
                        }

                        .input-wrap .form-control::placeholder {
                            color: #9aa7b6;
                        }

                        .input-wrap .form-control:focus {
                            background: #ffffff;
                            border-color: rgba(15, 123, 179, 0.55);
                            box-shadow: 0 0 0 4px rgba(15, 123, 179, 0.14);
                        }

                        .input-wrap .form-control[type='password'] {
                            letter-spacing: 0.24em;
                        }

                        .form-options {
                            display: flex;
                            align-items: center;
                            justify-content: space-between;
                            gap: 12px;
                            margin-top: 4px;
                            margin-bottom: 6px;
                            font-size: 0.9rem;
                        }

                        .form-check-label {
                            color: #4b5563;
                        }

                        .forgot-link {
                            color: #b58900;
                            font-weight: 700;
                            text-decoration: none;
                            white-space: nowrap;
                        }

                        .forgot-link:hover {
                            color: #8c6800;
                            text-decoration: underline;
                        }

                        .login-btn {
                            background: linear-gradient(90deg, #0f7bb3 0%, #0b8dcf 100%);
                            border: none;
                            color: #fff;
                            font-weight: 700;
                            box-shadow: 0 14px 28px rgba(15, 123, 179, 0.28);
                            width: 100%;
                            height: 50px;
                            border-radius: 14px;
                            margin-top: 14px;
                            letter-spacing: 0.01em;
                            transition: transform .2s ease, box-shadow .2s ease, filter .2s ease;
                        }

                        .login-btn:hover {
                            background: linear-gradient(90deg, #0d6fa3 0%, #0a80bc 100%);
                            box-shadow: 0 16px 30px rgba(15, 123, 179, 0.3);
                            transform: translateY(-1px);
                        }

                        .login-contact-block {
                            margin: 18px auto 0;
                            padding-top: 14px;
                            border-top: 1px solid rgba(148, 163, 184, 0.22);
                        }

                        .contact-address {
                            margin: 0 0 12px;
                            color: var(--sys-muted);
                            font-size: .9rem;
                            line-height: 1.45;
                        }

                        .contact-icons {
                            gap: 10px;
                        }

                        .contact-icon {
                            width: 38px;
                            height: 38px;
                            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.1);
                        }

                        .contact-icon:hover {
                            transform: translateY(-2px) scale(1.04);
                        }

                        .contact-icon.phone {
                            background: linear-gradient(180deg, #0f7bb3, #0b5f8a);
                        }

                        .contact-icon.email {
                            background: linear-gradient(180deg, #1d6d8c, #155e75);
                        }

                        .contact-icon.whatsapp {
                            background: linear-gradient(180deg, #2bd66f, #1fa955);
                        }

                        .invalid-feedback {
                            margin-top: 6px;
                            color: #dc2626;
                        }

                        @keyframes cardFloatIn {
                            from {
                                opacity: 0;
                                transform: translateY(18px) scale(0.98);
                            }

                            to {
                                opacity: 1;
                                transform: translateY(0) scale(1);
                            }
                        }

                        @media (max-width: 768px) {
                            .login-container {
                                padding: 10px;
                            }

                            .login-card {
                                max-height: calc(100vh - 20px);
                                border-radius: 22px;
                            }

                            .login-card .form-panel {
                                padding: 22px 16px 18px;
                            }

                            .panel-heading h2 {
                                font-size: 1.7rem;
                            }

                            .form-options {
                                align-items: flex-start;
                                flex-direction: column;
                                gap: 8px;
                            }

                            .forgot-link {
                                align-self: flex-end;
                            }
                        }
                    </style>

                </form>
            </div>

        </div>
    </div>