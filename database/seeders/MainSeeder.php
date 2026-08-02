<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\System\Company;
use App\Models\Core\User;
use App\Models\Business\Grain;
use App\Models\Business\Godown;
use App\Models\Business\PartyType;
use App\Models\Business\BrokerCommissionRate;
use App\Models\Business\Purchase;
use App\Models\Business\PurchaseItem;
use App\Models\Business\Lot;
use App\Models\Business\Sale;
use App\Models\Business\SaleLotAllocation;
use App\Models\Business\SaleCharge;
use App\Models\Business\SaleCollection;
use App\Models\Business\SalePayment;
use App\Models\Business\LedgerEntry;
use App\Models\Business\BrokerCommissionEntry;
use App\Models\Business\GrainStock;
use App\Models\Business\Payment;
use App\Models\Business\InventoryLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MainSeeder extends Seeder
{
    public function run()
    {
        $faker = \Faker\Factory::create('en_IN');

        // 1. Create Company
        $company = Company::create([
            'name' => 'Garg Grain Trading Co.',
            'phone' => '9876543210',
            'email' => 'contact@garggrain.com',
            'address' => 'Grain Market, Delhi',
            'display_unit' => 'Quintal',
            'bag_weight_kg' => 50,
        ]);

        // 2. Create Admin
        $admin = User::create([
            'company_id' => $company->id,
            'name' => 'System Admin',
            'email' => 'admin@grain.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // 3. Create Grains
        $grains = [];
        $grainNames = ['Wheat', 'Rice (Basmati)', 'Corn (Maize)', 'Soybean', 'Mustard Seed'];
        foreach ($grainNames as $name) {
            $grains[] = Grain::create([
                'company_id' => $company->id,
                'name' => $name,
            ]);
        }

        // 4. Create Godowns
        $godowns = [];
        for ($i = 1; $i <= 3; $i++) {
            $godowns[] = Godown::create([
                'company_id' => $company->id,
                'name' => 'Godown ' . $i,
                'location' => $faker->streetName,
                'capacity_in_quintals' => 10000,
                'current_stock_in_quintals' => 0,
            ]);
        }

        $farmerType = PartyType::create(['company_id' => $company->id, 'name' => 'Farmer', 'slug' => 'farmer']);
        $traderType = PartyType::create(['company_id' => $company->id, 'name' => 'Trader', 'slug' => 'trader']);

        // 6. Create Parties (20)
        $parties = [];
        for ($i = 0; $i < 20; $i++) {
            $parties[] = User::create([
                'company_id' => $company->id,
                'name' => $faker->name,
                'phone' => $faker->numerify('9#########'),
                'role' => 'party',
                'party_type_id' => $faker->randomElement([$farmerType->id, $traderType->id]),
                'opening_balance' => $faker->randomFloat(2, 0, 50000),
                'opening_balance_type' => $faker->randomElement(['credit', 'debit']),
            ]);
        }

        // 7. Create Brokers (5)
        $brokers = [];
        for ($i = 0; $i < 5; $i++) {
            $broker = User::create([
                'company_id' => $company->id,
                'name' => $faker->name . ' (Broker)',
                'phone' => $faker->numerify('8#########'),
                'role' => 'broker',
            ]);
            $brokers[] = $broker;

            // Give them random commission rules
            BrokerCommissionRate::create([
                'company_id' => $company->id,
                'broker_id' => $broker->id,
                'commission_type' => $faker->randomElement(['per_quintal', 'percentage', 'per_kg']),
                'rate' => $faker->randomElement([1, 2, 5, 10, 50, 100]),
                'applies_to' => 'both',
            ]);
        }

        // 8. Generate Purchases (approx 150)
        $lots = []; // Keep track of open lots for sales

        for ($i = 1; $i <= 300; $i++) {
            if ($i % 10 === 0) {
                $date = Carbon::today();
            } else {
                $date = Carbon::today()->subDays(rand(1, 100)); // Spread over last 100 days
            }
            $party = $faker->randomElement($parties);
            $broker = $faker->randomElement($brokers);
            $grain = $faker->randomElement($grains);
            $godown = $faker->randomElement($godowns);
            
            $unit = $faker->randomElement(['Quintal', 'Kg', 'Ton', 'Bags']);
            $quantity = $faker->numberBetween(50, 500);
            if ($unit === 'Kg') $quantity *= 100;
            if ($unit === 'Bags') $quantity *= 2;
            
            $rate = $faker->randomFloat(2, 2000, 3000); // per quintal typically
            if ($unit === 'Kg') $rate /= 100;
            if ($unit === 'Ton') $rate *= 10;
            if ($unit === 'Bags') $rate /= 2;

            $totalAmount = $quantity * $rate;

            $purchase = Purchase::create([
                'company_id' => $company->id,
                'purchase_no' => 'PUR-' . date('y') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'sequence_no' => $i,
                'date' => $date->format('Y-m-d'),
                'purchase_time' => $date->format('H:i'),
                'party_id' => $party->id,
                'broker_id' => $broker->id,
                'total_amount' => $totalAmount,
                'created_by' => $admin->id,
            ]);

            // Purchase Item (Simulate single item for simplicity)
            PurchaseItem::create([
                'purchase_id' => $purchase->id,
                'grain_id' => $grain->id,
                'quantity' => $quantity,
                'unit' => $unit,
                'rate' => $rate,
                'total_amount' => $totalAmount,
            ]);

            // Calculate Qtl
            $qtyInQtl = \App\Helpers\UnitHelper::toQtl($quantity, $unit, $company->bag_weight_kg);
            
            // Create Lot
            $lot = Lot::create([
                'company_id' => $company->id,
                'purchase_id' => $purchase->id,
                'godown_id' => $godown->id,
                'grain_id' => $grain->id,
                'lot_no' => 'LOT-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'initial_quantity' => $qtyInQtl,
                'remaining_quantity' => $qtyInQtl,
                'rate' => \App\Helpers\UnitHelper::rateToQtl($rate, $unit, $company->bag_weight_kg ?? 50),
                'status' => 'open',
            ]);
            $lots[] = $lot;

            // Add Godown stock
            $godown->current_stock_in_quintals += $qtyInQtl;
            $godown->save();

            // Ledger Entry
            LedgerEntry::create([
                'company_id' => $company->id,
                'party_id' => $party->id,
                'entry_type' => 'purchase',
                'reference_type' => Purchase::class,
                'reference_id' => $purchase->id,
                'entry_date' => $date->format('Y-m-d'),
                'credit' => $totalAmount, // Purchase increases what we owe to party
            ]);

            // Grain Stock
            $grainStock = GrainStock::firstOrCreate(
                ['company_id' => $company->id, 'grain_id' => $grain->id]
            );
            $grainStock->quantity += $qtyInQtl;
            $grainStock->save();

            // Inventory Log
            InventoryLog::create([
                'company_id' => $company->id,
                'grain_id' => $grain->id,
                'godown_id' => $godown->id,
                'lot_id' => $lot->id,
                'transaction_type' => 'purchase',
                'quantity_changed' => $qtyInQtl,
                'balance_after' => $lot->remaining_quantity,
                'date' => $date->format('Y-m-d'),
                'created_by' => $admin->id,
            ]);

            // Broker Commission
            $commissionRule = BrokerCommissionRate::where('broker_id', $broker->id)->first();
            if ($commissionRule) {
                $commAmt = 0;
                $rateInQtl = \App\Helpers\UnitHelper::rateToQtl($rate, $unit, $company->bag_weight_kg);
                if ($commissionRule->commission_type === 'per_quintal') {
                    $commAmt = $qtyInQtl * $commissionRule->rate;
                } elseif ($commissionRule->commission_type === 'per_kg') {
                    $commAmt = ($qtyInQtl * 100) * $commissionRule->rate;
                } elseif ($commissionRule->commission_type === 'percentage') {
                    $commAmt = ($totalAmount * $commissionRule->rate) / 100;
                } else {
                    $commAmt = $commissionRule->rate;
                }

                BrokerCommissionEntry::create([
                    'company_id' => $company->id,
                    'broker_id' => $broker->id,
                    'reference_type' => Purchase::class,
                    'reference_id' => $purchase->id,
                    'date' => $date->format('Y-m-d'),
                    'quantity' => $qtyInQtl,
                    'rate' => $rateInQtl,
                    'commission_type' => $commissionRule->commission_type,
                    'commission_rate' => $commissionRule->rate,
                    'commission_amount' => $commAmt,
                ]);
            }
            
            // Recalculate Ledger balance
            LedgerEntry::recalculateForParty($company->id, $party->id);
        }

        // 9. Generate Sales (approx 300)
        // With special focus on Cash Discounts

        for ($i = 1; $i <= 300; $i++) {
            // Find a random open lot
            $openLots = array_filter($lots, fn($l) => $l->remaining_quantity > 0);
            if (empty($openLots)) break;
            
            $lotIndex = array_rand($openLots);
            $lot = $openLots[$lotIndex];
            
            if ($i % 10 === 0) {
                $date = Carbon::today();
            } else {
                $date = Carbon::today()->subDays(rand(0, 100)); // Spread over last 100 days
            }
            $party = $faker->randomElement($parties);
            $broker = $faker->randomElement($brokers);
            
            // Take up to 50% of remaining, minimum 1 Qtl
            $takeQtl = $faker->randomFloat(2, 1, max(1, $lot->remaining_quantity * 0.5));
            if ($takeQtl > $lot->remaining_quantity) $takeQtl = $lot->remaining_quantity;
            
            $unit = 'Quintal'; // Keep it simple for sales
            $quantity = $takeQtl;
            $rate = $lot->rate + $faker->randomFloat(2, 100, 500); // Add markup
            $totalAmount = $quantity * $rate;
            
            // EDGE CASE: Cash Discount!
            $hasDiscount = $faker->boolean(30); // 30% chance of a discount
            $discountPercent = 0;
            $discountAmount = 0;
            if ($hasDiscount) {
                // Either flat amount or percent
                if ($faker->boolean(50)) {
                    $discountPercent = $faker->randomFloat(2, 1, 5); // 1-5%
                    $discountAmount = ($totalAmount * $discountPercent) / 100;
                } else {
                    $discountAmount = $faker->randomFloat(2, 50, 500);
                }
            }
            
            $netAmount = $totalAmount - $discountAmount;
            
            // Simulate Payment (Cash, Bank, Credit)
            $paymentMode = $faker->randomElement(['Cash', 'Bank Transfer', 'Credit']);
            $amountPaid = 0;
            if ($paymentMode === 'Cash' || $paymentMode === 'Bank Transfer') {
                $amountPaid = $netAmount; // Paid fully
            } elseif ($hasDiscount) {
                // If there's a cash discount, it implies Cash payment mostly!
                $paymentMode = 'Cash';
                $amountPaid = $netAmount;
            } else {
                $amountPaid = $faker->randomElement([0, $netAmount * 0.5]); // Partial or unpaid
            }
            
            $outstanding = max(0, $netAmount - $amountPaid);

            $sale = Sale::create([
                'company_id' => $company->id,
                'sale_no' => 'SAL-' . date('y') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'sequence_no' => $i,
                'date' => $date->format('Y-m-d'),
                'sale_time' => $date->format('H:i'),
                'party_id' => $party->id,
                'broker_id' => $broker->id,
                'grain_id' => $lot->grain_id,
                'quantity' => $quantity,
                'unit' => $unit,
                'rate' => $rate,
                'total_amount' => $totalAmount,
                'discount_percent' => $discountPercent,
                'discount_amount' => $discountAmount,
                'net_amount' => $netAmount,
                'amount_paid' => $amountPaid,
                'outstanding_amount' => $outstanding,
                'payment_mode' => $paymentMode,
                'created_by' => $admin->id,
                'notes' => $hasDiscount ? 'Included special cash discount' : '',
            ]);

            // Deduct from Lot
            $lot->remaining_quantity -= $takeQtl;
            if ($lot->remaining_quantity <= 0.01) {
                $lot->remaining_quantity = 0;
                $lot->status = 'closed';
            }
            $lot->save();

            SaleLotAllocation::create([
                'sale_id' => $sale->id,
                'lot_id' => $lot->id,
                'quantity_taken' => $takeQtl,
                'cost_rate' => $lot->rate,
            ]);
            
            // Deduct Godown Stock
            $godown = Godown::find($lot->godown_id);
            $godown->current_stock_in_quintals -= $takeQtl;
            $godown->save();

            // Deduct Grain Stock
            $grainStock = GrainStock::where(['company_id' => $company->id, 'grain_id' => $lot->grain_id])->first();
            if ($grainStock) {
                $grainStock->quantity -= $takeQtl;
                $grainStock->save();
            }

            // Inventory Log
            InventoryLog::create([
                'company_id' => $company->id,
                'grain_id' => $lot->grain_id,
                'godown_id' => $lot->godown_id,
                'lot_id' => $lot->id,
                'transaction_type' => 'sale',
                'quantity_changed' => -$takeQtl,
                'balance_after' => $lot->remaining_quantity,
                'date' => $date->format('Y-m-d'),
                'created_by' => $admin->id,
            ]);

            // Add Sale Collection if paid
            if ($amountPaid > 0) {
                SaleCollection::create([
                    'company_id' => $company->id,
                    'sale_id' => $sale->id,
                    'collected_at' => $date->format('Y-m-d'),
                    'mode' => $paymentMode,
                    'amount' => $amountPaid,
                    'reference_no' => 'Immediate Collection',
                    'created_by' => $admin->id,
                ]);
            }

            // Add Sale Payment log
            if ($amountPaid > 0) {
                SalePayment::create([
                    'sale_id' => $sale->id,
                    'mode' => $paymentMode,
                    'amount' => $amountPaid,
                ]);
            }

            // Ledger Entry (Debit party for Sale)
            LedgerEntry::create([
                'company_id' => $company->id,
                'party_id' => $party->id,
                'entry_type' => 'sale',
                'reference_type' => Sale::class,
                'reference_id' => $sale->id,
                'entry_date' => $date->format('Y-m-d'),
                'debit' => $netAmount, // Party owes us net amount
            ]);
            
            // If they paid immediately, record a Payment ledger entry as well to settle it!
            if ($amountPaid > 0) {
                // Wait, typically payments are managed via Payment table or Sale Collections. 
                // A true ledger should have the payment entry.
                $payment = Payment::create([
                    'company_id' => $company->id,
                    'party_id' => $party->id,
                    'direction' => 'receipt', // Money received from party
                    'date' => $date->format('Y-m-d'),
                    'amount' => $amountPaid,
                    'mode' => $paymentMode,
                    'created_by' => $admin->id,
                ]);

                LedgerEntry::create([
                    'company_id' => $company->id,
                    'party_id' => $party->id,
                    'entry_type' => 'payment',
                    'reference_type' => Payment::class,
                    'reference_id' => $payment->id,
                    'entry_date' => $date->format('Y-m-d'),
                    'credit' => $amountPaid, // Party pays us, decreasing debit
                ]);
            }

            // Broker Commission
            $commissionRule = BrokerCommissionRate::where('broker_id', $broker->id)->first();
            if ($commissionRule) {
                $commAmt = 0;
                $rateInQtl = \App\Helpers\UnitHelper::rateToQtl($rate, $unit, $company->bag_weight_kg);
                if ($commissionRule->commission_type === 'per_quintal') {
                    $commAmt = $takeQtl * $commissionRule->rate;
                } elseif ($commissionRule->commission_type === 'per_kg') {
                    $commAmt = ($takeQtl * 100) * $commissionRule->rate;
                } elseif ($commissionRule->commission_type === 'percentage') {
                    $commAmt = ($netAmount * $commissionRule->rate) / 100;
                } else {
                    $commAmt = $commissionRule->rate;
                }

                BrokerCommissionEntry::create([
                    'company_id' => $company->id,
                    'broker_id' => $broker->id,
                    'reference_type' => Sale::class,
                    'reference_id' => $sale->id,
                    'date' => $date->format('Y-m-d'),
                    'quantity' => $takeQtl,
                    'rate' => $rateInQtl,
                    'commission_type' => $commissionRule->commission_type,
                    'commission_rate' => $commissionRule->rate,
                    'commission_amount' => $commAmt,
                ]);
            }
            
            // Recalculate Ledger balance
            LedgerEntry::recalculateForParty($company->id, $party->id);
        }
    }
}
