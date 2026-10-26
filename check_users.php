<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $count = \App\Models\User::count();
    echo "User Count: " . $count . "\n";
    
    if ($count == 0) {
        echo "No users found. Running seeder...\n";
        \Artisan::call('db:seed');
        echo "Seeder finished.\n";
    } else {
        $admin = \App\Models\User::where('email', 'admin@lms.com')->first();
        if ($admin) {
             echo "Admin exists. Password reset might be needed if login fails.\n";
             // Optional: Reset password just in case
             $admin->password = \Illuminate\Support\Facades\Hash::make('password');
             $admin->save();
             echo "Admin password reset to 'password'.\n";
        }
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
