<?php

$loginHtml = file_get_contents('public/Duralux-admin-1.0.0/auth-login-cover.html');

// Helper to replace asset paths
function fixAssets($html) {
    $html = str_replace('href="assets/', 'href="{{ asset(\'Duralux-admin-1.0.0/assets/', $html);
    $html = str_replace('src="assets/', 'src="{{ asset(\'Duralux-admin-1.0.0/assets/', $html);
    // add closing quotes and brackets for blade
    $html = preg_replace('/(href="\{\{\s*asset\(\'Duralux-admin-1.0.0\/assets\/[^\"]+)(\")/', '$1\') }}"', $html);
    $html = preg_replace('/(src="\{\{\s*asset\(\'Duralux-admin-1.0.0\/assets\/[^\"]+)(\")/', '$1\') }}"', $html);
    return $html;
}

$loginHtml = fixAssets($loginHtml);

// Add form action, method, CSRF, and input names
$loginHtml = preg_replace('/<form action="index.html" class="(.*?)"(.*?)>/', '<form method="POST" action="{{ route(\'login\') }}" class="$1"$2>' . "\n" . '                        @csrf', $loginHtml);

// Add name="email"
$loginHtml = preg_replace('/<input type="email" class="form-control" placeholder="Email or Username"(.*?)>/', '<input type="email" name="email" class="form-control" placeholder="Email" value="{{ old(\'email\') }}" required>', $loginHtml);

// Add name="password"
$loginHtml = preg_replace('/<input type="password" class="form-control" placeholder="Password"(.*?)>/', '<input type="password" name="password" class="form-control" placeholder="Password" required>', $loginHtml);

// Add name="remember"
$loginHtml = str_replace('id="rememberMe"', 'id="rememberMe" name="remember"', $loginHtml);

// Display validation errors if any
$errorHtml = <<<BLADE
@if (\$errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach (\$errors->all() as \$error)
                                        <li>{{ \$error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
BLADE;
$loginHtml = str_replace('<form method="POST" action="{{ route(\'login\') }}"', $errorHtml . "\n" . '                    <form method="POST" action="{{ route(\'login\') }}"', $loginHtml);

// Remove the social login part and "don't have an account"
$loginHtml = preg_replace('/<div class="w-100 mt-5 text-center mx-auto">.*?<\/div>\s*<\/div>\s*<div class="mt-5 text-muted">/s', '<div class="mt-5 text-muted">', $loginHtml);


if (!is_dir('resources/views/auth')) {
    mkdir('resources/views/auth', 0777, true);
}

file_put_contents('resources/views/auth/login.blade.php', $loginHtml);

echo "Login Blade view created successfully!\n";
