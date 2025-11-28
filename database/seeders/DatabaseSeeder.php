<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '+1234567890',
            'is_active' => true,
        ]);

        // Create Customer Service Users
        $cs1 = User::create([
            'name' => 'Customer Service 1',
            'email' => 'cs1@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer_service',
            'phone' => '+1234567891',
            'is_active' => true,
        ]);

        $cs2 = User::create([
            'name' => 'Customer Service 2',
            'email' => 'cs2@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer_service',
            'phone' => '+1234567892',
            'is_active' => true,
        ]);

        // Create Tailor Users
        $tailor1 = User::create([
            'name' => 'Tailor 1',
            'email' => 'tailor1@example.com',
            'password' => Hash::make('password'),
            'role' => 'tailor',
            'phone' => '+1234567893',
            'is_active' => true,
        ]);

        $tailor2 = User::create([
            'name' => 'Tailor 2',
            'email' => 'tailor2@example.com',
            'password' => Hash::make('password'),
            'role' => 'tailor',
            'phone' => '+1234567894',
            'is_active' => true,
        ]);

        $tailor3 = User::create([
            'name' => 'Tailor 3',
            'email' => 'tailor3@example.com',
            'password' => Hash::make('password'),
            'role' => 'tailor',
            'phone' => '+1234567895',
            'is_active' => true,
        ]);

        // Create Products
        $products = [
            [
                'name' => 'Men\'s Suit',
                'description' => 'Classic men\'s suit with jacket and trousers',
                'price' => 299.99,
                'is_active' => true,
            ],
            [
                'name' => 'Women\'s Dress',
                'description' => 'Elegant women\'s dress for special occasions',
                'price' => 199.99,
                'is_active' => true,
            ],
            [
                'name' => 'Shirt',
                'description' => 'Custom tailored shirt',
                'price' => 79.99,
                'is_active' => true,
            ],
            [
                'name' => 'Trousers',
                'description' => 'Custom tailored trousers',
                'price' => 89.99,
                'is_active' => true,
            ],
            [
                'name' => 'Blazer',
                'description' => 'Classic blazer jacket',
                'price' => 249.99,
                'is_active' => true,
            ],
        ];

        $productModels = [];
        foreach ($products as $product) {
            $productModels[] = Product::create($product);
        }

        // Create Customers
        $customers = [
            [
                'name' => 'Ahmed Ali',
                'phone' => '+201234567890',
                'email' => 'ahmed@example.com',
                'address' => '123 Main St, Cairo, Egypt',
                'notes' => 'Prefers traditional styles',
            ],
            [
                'name' => 'Fatima Hassan',
                'phone' => '+201234567891',
                'email' => 'fatima@example.com',
                'address' => '456 Oak Ave, Alexandria, Egypt',
                'notes' => 'Regular customer',
            ],
            [
                'name' => 'Mohamed Ibrahim',
                'phone' => '+201234567892',
                'email' => 'mohamed@example.com',
                'address' => '789 Pine Rd, Giza, Egypt',
                'notes' => null,
            ],
            [
                'name' => 'Sara Mahmoud',
                'phone' => '+201234567893',
                'email' => 'sara@example.com',
                'address' => '321 Elm St, Luxor, Egypt',
                'notes' => 'VIP customer',
            ],
            [
                'name' => 'Omar Khaled',
                'phone' => '+201234567894',
                'email' => 'omar@example.com',
                'address' => '654 Maple Dr, Aswan, Egypt',
                'notes' => null,
            ],
            [
                'name' => 'Layla Nour',
                'phone' => '+201234567895',
                'email' => 'layla@example.com',
                'address' => '987 Cedar Ln, Sharm El Sheikh, Egypt',
                'notes' => 'Prefers modern designs',
            ],
            [
                'name' => 'Youssef Tarek',
                'phone' => '+201234567896',
                'email' => 'youssef@example.com',
                'address' => '147 Birch Way, Hurghada, Egypt',
                'notes' => null,
            ],
            [
                'name' => 'Nour Samir',
                'phone' => '+201234567897',
                'email' => 'nour@example.com',
                'address' => '258 Spruce St, Dahab, Egypt',
                'notes' => 'First-time customer',
            ],
            [
                'name' => 'Karim Fadi',
                'phone' => '+201234567898',
                'email' => 'karim@example.com',
                'address' => '369 Willow Ave, Marsa Alam, Egypt',
                'notes' => null,
            ],
            [
                'name' => 'Mariam Rami',
                'phone' => '+201234567899',
                'email' => 'mariam@example.com',
                'address' => '741 Cherry Rd, Siwa, Egypt',
                'notes' => 'Prefers eco-friendly materials',
            ],
        ];

        $customerModels = [];
        foreach ($customers as $customer) {
            $customerModels[] = Customer::create($customer);
        }

        // Create Sample Orders
        $orders = [
            [
                'customer_id' => $customerModels[0]->id,
                'customer_service_id' => $cs1->id,
                'tailor_id' => $tailor1->id,
                'product_id' => $productModels[0]->id,
                'status' => 'in_progress',
                'total_price' => 299.99,
                'delivery_date' => now()->addDays(7),
                'length' => 180.5,
                'shoulder' => 45.0,
                'chest' => 100.0,
                'waist' => 85.0,
                'hip' => 95.0,
                'sleeve' => 60.0,
                'measurement_notes' => 'Customer prefers slightly loose fit',
                'notes' => 'Urgent order',
            ],
            [
                'customer_id' => $customerModels[1]->id,
                'customer_service_id' => $cs1->id,
                'tailor_id' => $tailor2->id,
                'product_id' => $productModels[1]->id,
                'status' => 'pending',
                'total_price' => 199.99,
                'delivery_date' => now()->addDays(10),
                'length' => 165.0,
                'shoulder' => 38.0,
                'chest' => 90.0,
                'waist' => 70.0,
                'hip' => 95.0,
                'sleeve' => 55.0,
                'measurement_notes' => null,
                'notes' => null,
            ],
            [
                'customer_id' => $customerModels[2]->id,
                'customer_service_id' => $cs2->id,
                'tailor_id' => $tailor3->id,
                'product_id' => $productModels[2]->id,
                'status' => 'completed',
                'total_price' => 79.99,
                'delivery_date' => now()->addDays(5),
                'length' => 175.0,
                'shoulder' => 42.0,
                'chest' => 95.0,
                'waist' => 80.0,
                'hip' => 90.0,
                'sleeve' => 58.0,
                'measurement_notes' => null,
                'notes' => null,
            ],
            [
                'customer_id' => $customerModels[3]->id,
                'customer_service_id' => $cs2->id,
                'tailor_id' => null,
                'product_id' => $productModels[3]->id,
                'status' => 'pending',
                'total_price' => 89.99,
                'delivery_date' => now()->addDays(14),
                'length' => 170.0,
                'shoulder' => 40.0,
                'chest' => 92.0,
                'waist' => 78.0,
                'hip' => 88.0,
                'sleeve' => 57.0,
                'measurement_notes' => null,
                'notes' => 'Waiting for fabric selection',
            ],
            [
                'customer_id' => $customerModels[4]->id,
                'customer_service_id' => $cs1->id,
                'tailor_id' => $tailor1->id,
                'product_id' => $productModels[4]->id,
                'status' => 'in_progress',
                'total_price' => 249.99,
                'delivery_date' => now()->addDays(8),
                'length' => 185.0,
                'shoulder' => 46.0,
                'chest' => 105.0,
                'waist' => 90.0,
                'hip' => 100.0,
                'sleeve' => 62.0,
                'measurement_notes' => null,
                'notes' => null,
            ],
            [
                'customer_id' => $customerModels[5]->id,
                'customer_service_id' => $cs1->id,
                'tailor_id' => $tailor2->id,
                'product_id' => $productModels[0]->id,
                'status' => 'delivered',
                'total_price' => 299.99,
                'delivery_date' => now()->subDays(2),
                'length' => 160.0,
                'shoulder' => 36.0,
                'chest' => 88.0,
                'waist' => 72.0,
                'hip' => 92.0,
                'sleeve' => 54.0,
                'measurement_notes' => null,
                'notes' => 'Delivered successfully',
            ],
            [
                'customer_id' => $customerModels[6]->id,
                'customer_service_id' => $cs2->id,
                'tailor_id' => $tailor3->id,
                'product_id' => $productModels[1]->id,
                'status' => 'completed',
                'total_price' => 199.99,
                'delivery_date' => now()->addDays(3),
                'length' => 168.0,
                'shoulder' => 39.0,
                'chest' => 91.0,
                'waist' => 75.0,
                'hip' => 94.0,
                'sleeve' => 56.0,
                'measurement_notes' => null,
                'notes' => null,
            ],
            [
                'customer_id' => $customerModels[7]->id,
                'customer_service_id' => $cs1->id,
                'tailor_id' => null,
                'product_id' => $productModels[2]->id,
                'status' => 'pending',
                'total_price' => 79.99,
                'delivery_date' => now()->addDays(12),
                'length' => 172.0,
                'shoulder' => 41.0,
                'chest' => 93.0,
                'waist' => 82.0,
                'hip' => 89.0,
                'sleeve' => 59.0,
                'measurement_notes' => null,
                'notes' => null,
            ],
            [
                'customer_id' => $customerModels[8]->id,
                'customer_service_id' => $cs2->id,
                'tailor_id' => $tailor1->id,
                'product_id' => $productModels[3]->id,
                'status' => 'in_progress',
                'total_price' => 89.99,
                'delivery_date' => now()->addDays(6),
                'length' => 178.0,
                'shoulder' => 44.0,
                'chest' => 98.0,
                'waist' => 87.0,
                'hip' => 96.0,
                'sleeve' => 61.0,
                'measurement_notes' => null,
                'notes' => null,
            ],
            [
                'customer_id' => $customerModels[9]->id,
                'customer_service_id' => $cs1->id,
                'tailor_id' => $tailor2->id,
                'product_id' => $productModels[4]->id,
                'status' => 'cancelled',
                'total_price' => 249.99,
                'delivery_date' => null,
                'length' => 162.0,
                'shoulder' => 37.0,
                'chest' => 86.0,
                'waist' => 68.0,
                'hip' => 90.0,
                'sleeve' => 53.0,
                'measurement_notes' => null,
                'notes' => 'Customer cancelled order',
            ],
        ];

        foreach ($orders as $order) {
            Order::create($order);
        }
    }
}
