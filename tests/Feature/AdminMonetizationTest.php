<?php

use App\Models\Episode;
use App\Models\Movie;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Season;
use App\Models\Series;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Vj;
use App\Models\WatchProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function admin(): User
{
    return User::factory()->create(['role' => 'super_admin']);
}

function plan(array $attributes = []): Plan
{
    return Plan::factory()->create($attributes);
}

function subscriptionFor(User $user, ?Plan $plan = null, array $attributes = []): Subscription
{
    return Subscription::create(array_merge([
        'user_id' => $user->id,
        'plan_id' => ($plan ?? plan())->id,
        'status' => 'active',
        'starts_at' => now(),
        'expires_at' => now()->addDays(30),
        'auto_renew' => true,
        'payment_method' => 'mobile_money',
    ], $attributes));
}

function paymentFor(Subscription $subscription, array $attributes = []): Payment
{
    return Payment::create(array_merge([
        'user_id' => $subscription->user_id,
        'plan_id' => $subscription->plan_id,
        'subscription_id' => $subscription->id,
        'transaction_reference' => 'VJF-'.strtoupper(uniqid()),
        'network' => 'mtn',
        'phone_number' => $subscription->user->phone_number,
        'amount' => $subscription->plan->price_ugx,
        'currency' => 'UGX',
        'status' => 'completed',
        'paid_at' => now(),
    ], $attributes));
}

/*
|--------------------------------------------------------------------------
| Plans
|--------------------------------------------------------------------------
*/

test('admin can open the plan list, create form and edit form', function () {
    $admin = admin();
    $plan = plan(['name' => 'Monthly Premium', 'badge' => 'Most Popular']);

    $this->actingAs($admin)->get('/admin/plans')
        ->assertOk()
        ->assertSee('Subscription Plans')
        ->assertSee('Monthly Premium')
        ->assertSee('Most Popular')
        ->assertSee($plan->formattedPrice());

    $this->actingAs($admin)->get('/admin/plans/create')->assertOk()->assertSee('Create a Subscription Plan');

    $this->actingAs($admin)->get("/admin/plans/{$plan->id}/edit")
        ->assertOk()
        ->assertSee('Edit Monthly Premium');
});

test('admin can create a plan and blank feature rows are discarded', function () {
    $admin = admin();

    $response = $this->actingAs($admin)->post('/admin/plans', [
        'name' => 'Weekly Lite',
        'slug' => 'weekly-lite',
        'description' => 'Cheap weekly access',
        'price_ugx' => 5000,
        'interval_unit' => 'week',
        'interval_count' => 1,
        'duration_days' => 7,
        'features' => ['SD streaming', '   ', 'One screen'],
        'badge' => 'Starter',
        'is_active' => '1',
        'sort_order' => 1,
    ]);

    $response->assertRedirect('/admin/plans')->assertSessionHas('success');

    $plan = Plan::where('slug', 'weekly-lite')->firstOrFail();

    $this->assertSame(5000, $plan->price_ugx);
    $this->assertSame(7, $plan->duration_days);
    $this->assertTrue($plan->is_active);
    // The padded row is trimmed away and the whitespace-only row is dropped.
    $this->assertSame(['SD streaming', 'One screen'], $plan->features);
});

test('plan slug is derived from the name when omitted', function () {
    $this->actingAs(admin())->post('/admin/plans', [
        'name' => 'Yearly Family',
        'price_ugx' => 250000,
        'interval_unit' => 'year',
        'interval_count' => 1,
        'duration_days' => 365,
    ])->assertRedirect('/admin/plans');

    $this->assertDatabaseHas('plans', ['slug' => 'yearly-family']);
});

test('creating a plan validates its input', function () {
    $this->actingAs(admin())->post('/admin/plans', [
        'name' => '',
        'price_ugx' => -100,
        'interval_unit' => 'fortnight',
        'interval_count' => 0,
        'duration_days' => 0,
    ])->assertSessionHasErrors(['name', 'price_ugx', 'interval_unit', 'interval_count', 'duration_days']);

    $this->assertSame(0, Plan::count());
});

test('admin can update a plan and unchecking active persists false', function () {
    $admin = admin();
    $plan = plan(['is_active' => true, 'sort_order' => 5]);

    $this->actingAs($admin)->put("/admin/plans/{$plan->id}", [
        'name' => 'Renamed Plan',
        'slug' => $plan->slug,
        'price_ugx' => 30000,
        'interval_unit' => 'month',
        'interval_count' => 1,
        'duration_days' => 30,
        'features' => ['HD', ''],
        'is_active' => '0',
        'sort_order' => 3,
    ])->assertRedirect('/admin/plans')->assertSessionHas('success');

    $plan->refresh();

    $this->assertSame('Renamed Plan', $plan->name);
    $this->assertSame(30000, $plan->price_ugx);
    $this->assertFalse($plan->is_active, 'unchecked active must persist as false, not fall back to the column default');
    $this->assertSame(['HD'], $plan->features);
});

test('a plan with active subscriptions cannot be deleted', function () {
    $admin = admin();
    $plan = plan();
    subscriptionFor(User::factory()->create(), $plan, ['status' => 'active', 'expires_at' => now()->addDays(10)]);

    $this->actingAs($admin)
        ->from('/admin/plans')
        ->delete("/admin/plans/{$plan->id}")
        ->assertRedirect('/admin/plans')
        ->assertSessionHas('error');

    $this->assertDatabaseHas('plans', ['id' => $plan->id]);
});

test('a plan without active subscriptions can be deleted', function () {
    $admin = admin();
    $plan = plan();
    subscriptionFor(User::factory()->create(), $plan, ['status' => 'cancelled']);

    $this->actingAs($admin)->delete("/admin/plans/{$plan->id}")
        ->assertRedirect('/admin/plans')
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('plans', ['id' => $plan->id]);
});

/*
|--------------------------------------------------------------------------
| Subscriptions
|--------------------------------------------------------------------------
*/

test('admin can browse and inspect subscriptions', function () {
    $admin = admin();
    $user = User::factory()->create(['name' => 'Grace Nakato']);
    $plan = plan(['name' => 'Monthly Premium']);
    $subscription = subscriptionFor($user, $plan);
    paymentFor($subscription);

    $this->actingAs($admin)->get('/admin/subscriptions')
        ->assertOk()
        ->assertSee('Subscriptions')
        ->assertSee('Grace Nakato')
        ->assertSee('Monthly Premium');

    $this->actingAs($admin)->get("/admin/subscriptions/{$subscription->id}")
        ->assertOk()
        ->assertSee('Access Settings')
        ->assertSee($plan->name);
});

test('admin can change a subscription status, expiry and auto renew', function () {
    $admin = admin();
    $subscription = subscriptionFor(User::factory()->create(), plan(), [
        'status' => 'pending',
        'auto_renew' => true,
    ]);

    $expiry = now()->addDays(90)->startOfDay();

    $this->actingAs($admin)->put("/admin/subscriptions/{$subscription->id}", [
        'status' => 'active',
        'expires_at' => $expiry->toDateString(),
        'auto_renew' => '0',
    ])->assertRedirect()->assertSessionHas('success');

    $subscription->refresh();

    $this->assertSame('active', $subscription->status);
    $this->assertTrue($subscription->expires_at->isSameDay($expiry));
    $this->assertFalse($subscription->auto_renew);
});

test('subscription status is validated', function () {
    $subscription = subscriptionFor(User::factory()->create());

    $this->actingAs(admin())
        ->put("/admin/subscriptions/{$subscription->id}", ['status' => 'teleported'])
        ->assertSessionHasErrors('status');
});

test('admin can delete a subscription', function () {
    $subscription = subscriptionFor(User::factory()->create());

    $this->actingAs(admin())->delete("/admin/subscriptions/{$subscription->id}")
        ->assertRedirect('/admin/subscriptions');

    $this->assertDatabaseMissing('subscriptions', ['id' => $subscription->id]);
});

/*
|--------------------------------------------------------------------------
| Payments
|--------------------------------------------------------------------------
*/

test('admin can browse payments and inspect a completed one', function () {
    $admin = admin();
    $user = User::factory()->create(['name' => 'Brian Kizza']);
    $plan = plan(['name' => 'Monthly Premium']);
    $subscription = subscriptionFor($user, $plan);
    $payment = paymentFor($subscription, ['external_reference' => 'MPESA-XYZ-123']);

    $this->actingAs($admin)->get('/admin/payments')
        ->assertOk()
        ->assertSee('Mobile Money Payments')
        ->assertSee($payment->transaction_reference)
        ->assertSee('Brian Kizza');

    $this->actingAs($admin)->get("/admin/payments/{$payment->id}")
        ->assertOk()
        ->assertSee('Payment Details')
        ->assertSee('MPESA-XYZ-123')
        ->assertSee('MTN Mobile Money');
});

test('a failed payment shows its failure reason', function () {
    $admin = admin();
    $subscription = subscriptionFor(User::factory()->create());
    $payment = paymentFor($subscription, [
        'status' => 'failed',
        'failure_reason' => 'Subscriber cancelled the USSD prompt',
        'paid_at' => null,
    ]);

    $this->actingAs($admin)->get("/admin/payments/{$payment->id}")
        ->assertOk()
        ->assertSee('Failure reason')
        ->assertSee('Subscriber cancelled the USSD prompt');
});

test('payments are listed newest first', function () {
    $admin = admin();
    $user = User::factory()->create();

    $old = paymentFor(subscriptionFor($user), ['transaction_reference' => 'VJF-OLD']);
    $old->forceFill(['created_at' => now()->subDays(10)])->save();

    $new = paymentFor(subscriptionFor($user), ['transaction_reference' => 'VJF-NEW']);
    $new->forceFill(['created_at' => now()])->save();

    $html = $this->actingAs($admin)->get('/admin/payments')->assertOk()->getContent();

    $this->assertLessThan(strpos($html, 'VJF-OLD'), strpos($html, 'VJF-NEW'));
});

/*
|--------------------------------------------------------------------------
| Analytics
|--------------------------------------------------------------------------
*/

test('admin can load the analytics dashboard for every period', function () {
    $admin = admin();
    plan();
    Movie::factory()->count(2)->create(['status' => 'published']);

    foreach ([7, 30, 90, 365] as $period) {
        $this->actingAs($admin)
            ->get("/admin/analytics?period={$period}")
            ->assertOk()
            ->assertSee('Analytics')
            ->assertSee('Audience')
            ->assertSee('Monetisation');
    }

    $this->actingAs($admin)->get('/admin/analytics')->assertOk();
});

test('analytics renders with no content, no subscribers and no watch history', function () {
    // The empty-database path is where the hand-rolled SQL is most likely to break.
    $this->actingAs(admin())->get('/admin/analytics')
        ->assertOk()
        ->assertSee('No watch activity in this window.');
});

test('analytics daily series are derived from watch progress', function () {
    $admin = admin();
    $user = User::factory()->create();
    $movie = Movie::factory()->create(['status' => 'published']);

    WatchProgress::create([
        'user_id' => $user->id,
        'watchable_type' => Movie::class,
        'watchable_id' => $movie->id,
        'progress_seconds' => 3600,
        'duration_seconds' => 7200,
        'completed' => true,
    ]);

    $this->actingAs($admin)->get('/admin/analytics')
        ->assertOk()
        ->assertSee('Daily watch time');
});

test('top series by watch time resolves through seasons', function () {
    // watch_progress rows for an Episode must reach series via episodes -> seasons -> series.
    $admin = admin();
    $series = Series::factory()->create(['status' => 'published']);
    $season = Season::factory()->create(['series_id' => $series->id]);
    $episode = Episode::factory()->create(['season_id' => $season->id]);

    WatchProgress::create([
        'user_id' => User::factory()->create()->id,
        'watchable_type' => Episode::class,
        'watchable_id' => $episode->id,
        'progress_seconds' => 1800,
        'duration_seconds' => 2400,
        'completed' => false,
    ]);

    $this->actingAs($admin)->get('/admin/analytics')
        ->assertOk()
        ->assertSee($series->title);
});

test('analytics data endpoint returns json for every type', function () {
    $admin = admin();
    plan();
    Movie::factory()->count(2)->create(['status' => 'published']);
    Vj::factory()->count(2)->create();

    $types = [
        'platform', 'movies', 'vjs', 'genres', 'users',
        'subscriptions', 'daily_active_users', 'daily_watch_time',
        'completion_rate', 'top_content',
    ];

    foreach ($types as $type) {
        $this->actingAs($admin)
            ->getJson("/admin/analytics/data?type={$type}")
            ->assertOk()
            ->assertJsonPath('success', true);
    }
});

/*
|--------------------------------------------------------------------------
| Authorisation
|--------------------------------------------------------------------------
*/

test('non-admins are rejected from the new admin screens', function () {
    $user = User::factory()->create(['role' => 'user']);

    foreach ([
        '/admin/plans',
        '/admin/plans/create',
        '/admin/subscriptions',
        '/admin/payments',
        '/admin/analytics',
    ] as $url) {
        $this->actingAs($user)->get($url)->assertStatus(403);
    }
});
