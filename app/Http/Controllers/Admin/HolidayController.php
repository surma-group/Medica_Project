<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HolidayController extends Controller
{
    // Display a listing of holidays
    public function index(Request $request)
    {
        $query = Holiday::query();

        // Filter by From date
        if ($request->filled('from')) {
            $query->whereDate('from', '>=', $request->from);
        }

        // Filter by To date
        if ($request->filled('to')) {
            $query->whereDate('to', '<=', $request->to);
        }

        $holidays = $query->orderBy('from', 'desc')->paginate(10);

        return view('admin.holiday.index', compact('holidays'));
    }

    // Show the form for creating a new holiday
    public function create()
    {
        return view('admin.holiday.form');
    }

    // Store a newly created holiday in storage
    public function store(Request $request)
    {
        // Convert datetime-local format to proper datetime string before validation
        $request->merge([
            'from' => str_replace('T', ' ', $request->from),
            'to'   => str_replace('T', ' ', $request->to),
        ]);

        // Validate
        $request->validate([
            'title' => 'required|string|max:255',
            'from'  => 'required|date',
            'to'    => [
                'required',
                'date',
                function ($attribute, $value, $fail) use ($request) {
                    try {
                        $from = Carbon::parse($request->from);
                        $to   = Carbon::parse($request->to);

                        if ($to->lt($from)) {
                            $fail('The "To" field must be a date after or equal to "From".');
                        }
                    } catch (\Exception $e) {
                        $fail('Invalid date format.');
                    }
                }
            ],
            'status' => 'required|boolean',
        ]);

        // Save to database in proper format
        Holiday::create([
            'title'  => $request->title,
            'from'   => Carbon::parse($request->from)->format('Y-m-d H:i:s'),
            'to'     => Carbon::parse($request->to)->format('Y-m-d H:i:s'),
            'status' => $request->status,
        ]);

        return redirect()->route('holiday.index')->with('success', 'Holiday created successfully.');
    }



    // Show the form for editing the specified holiday
    public function edit(Holiday $holiday)
    {
        return view('admin.holiday.form', compact('holiday'));
    }

    // Update the specified holiday in storage

    public function update(Request $request, Holiday $holiday)
    {
        // Convert datetime-local inputs to proper datetime format
        $from = str_replace('T', ' ', $request->from);
        $to   = str_replace('T', ' ', $request->to);

        // Validate
        $request->validate([
            'title' => 'required|string|max:255',
            'from'  => 'required|date',
            'to'    => ['required', 'date', function ($attribute, $value, $fail) use ($from, $to) {
                try {
                    $fromDate = Carbon::parse($from);
                    $toDate   = Carbon::parse($to);

                    if ($toDate->lt($fromDate)) {
                        $fail('The "To" field must be a date after or equal to "From".');
                    }
                } catch (\Exception $e) {
                    $fail('Invalid date format.');
                }
            }],
            'status' => 'required|boolean',
        ]);

        // Update the holiday
        $holiday->update([
            'title'  => $request->title,
            'from'   => Carbon::parse($from)->format('Y-m-d H:i:s'),
            'to'     => Carbon::parse($to)->format('Y-m-d H:i:s'),
            'status' => $request->status,
        ]);

        return redirect()->route('holiday.index')->with('success', 'Holiday updated successfully.');
    }


    // Remove the specified holiday from storage
    public function destroy(Holiday $holiday)
    {
        $holiday->delete();
        return redirect()->route('holiday.index')->with('success', 'Holiday deleted successfully.');
    }

    public function calendar()
    {
        return view('admin.holiday.calendar');
    }

    public function calendarEvents()
    {
        $holidays = Holiday::where('status', 1)->get();

        $events = [];

        foreach ($holidays as $holiday) {
            $events[] = [
                'title' => $holiday->title,
                'start' => $holiday->from,
                'end'   => $holiday->to,
                'backgroundColor' => '#00bcd4',
                'borderColor' => '#00bcd4',
            ];
        }

        return response()->json($events);
    }
}
