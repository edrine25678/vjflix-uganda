<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::orderBy('sort_order')->get();

        return view('admin.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.plans.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        Plan::create($data);

        return redirect()->route('admin.plans.index')
            ->with('success', 'Plan created successfully');
    }

    public function edit(Plan $plan)
    {
        return view('admin.plans.edit', compact('plan'));
    }

    public function update(Request $request, Plan $plan)
    {
        $data = $this->validateData($request, $plan);

        $plan->update($data);

        return redirect()->route('admin.plans.index')
            ->with('success', 'Plan updated successfully');
    }

    public function destroy(Plan $plan)
    {
        // Check if plan has active subscriptions
        if ($plan->subscriptions()->active()->exists()) {
            return back()->with('error', 'Cannot delete plan with active subscriptions');
        }

        $plan->delete();

        return redirect()->route('admin.plans.index')
            ->with('success', 'Plan deleted successfully');
    }

    /**
     * Validate the plan form and normalise it for persistence.
     *
     * The repeatable "features[]" rows are trimmed and emptied rows dropped so
     * the JSON column does not accumulate blank strings.
     */
    private function validateData(Request $request, ?Plan $plan = null): array
    {
        $slugRule = $plan
            ? Rule::unique('plans')->ignore($plan->id)
            : 'unique:plans,slug';

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', $slugRule],
            'description' => 'nullable|string',
            'price_ugx' => 'required|integer|min:0',
            'interval_unit' => 'required|in:day,week,month,year',
            'interval_count' => 'required|integer|min:1',
            'duration_days' => 'required|integer|min:1',
            'features' => 'nullable|array',
            'features.*' => 'nullable|string|max:255',
            'badge' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        if (array_key_exists('features', $data)) {
            $data['features'] = collect($data['features'] ?? [])
                ->map(fn ($feature) => trim((string) $feature))
                ->filter()
                ->values()
                ->all();
        }

        return $data;
    }
}
