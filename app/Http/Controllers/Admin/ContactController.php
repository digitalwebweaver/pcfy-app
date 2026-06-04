<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $query = ContactSubmission::latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->active();
        }
        if ($request->filled('q')) {
            $query->where(fn($q) => $q->where('name', 'like', "%{$request->q}%")
                                      ->orWhere('email', 'like', "%{$request->q}%")
                                      ->orWhere('message', 'like', "%{$request->q}%"));
        }

        $contacts = $query->paginate(25)->withQueryString();
        $counts = [
            'unread'   => ContactSubmission::where('status', 'unread')->count(),
            'read'     => ContactSubmission::where('status', 'read')->count(),
            'replied'  => ContactSubmission::where('status', 'replied')->count(),
            'archived' => ContactSubmission::where('status', 'archived')->count(),
        ];

        return view('admin.contacts.index', compact('contacts', 'counts'));
    }

    public function show(ContactSubmission $contact)
    {
        $contact->markRead();
        return view('admin.contacts.show', compact('contact'));
    }

    public function update(Request $request, ContactSubmission $contact)
    {
        $action = $request->input('action');

        if ($action === 'reply') {
            $request->validate(['reply_body' => 'required|string']);

            $fromName  = Setting::get('mail_from_name', 'pcfy.in');
            $fromEmail = Setting::get('mail_from_email', 'noreply@pcfy.in');

            Mail::raw($request->reply_body, function ($msg) use ($contact, $fromName, $fromEmail) {
                $msg->to($contact->email, $contact->name)
                    ->from($fromEmail, $fromName)
                    ->subject('Re: '.($contact->subject ?: 'Your enquiry to pcfy.in'));
            });

            $contact->update(['status' => 'replied', 'replied_at' => now()]);
            return back()->with('success', 'Reply sent to '.$contact->email);
        }

        if ($action === 'archive') {
            $contact->update(['status' => 'archived']);
            return redirect()->route('adminhu.contacts.index')->with('success', 'Archived.');
        }

        if ($action === 'unread') {
            $contact->update(['status' => 'unread', 'read_at' => null]);
            return back()->with('success', 'Marked as unread.');
        }

        return back();
    }

    public function destroy(ContactSubmission $contact)
    {
        $contact->delete();
        return redirect()->route('adminhu.contacts.index')->with('success', 'Deleted.');
    }

    // Unused resource stubs
    public function create()  { return redirect()->route('adminhu.contacts.index'); }
    public function store(Request $request) { return redirect()->route('adminhu.contacts.index'); }
    public function edit(ContactSubmission $contact) { return redirect()->route('adminhu.contacts.show', $contact); }
}
